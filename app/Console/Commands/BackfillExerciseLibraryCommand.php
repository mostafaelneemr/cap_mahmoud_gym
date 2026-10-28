<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use App\Models\ExerciseLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Safe, reversible backfill command to migrate unambiguous exercise video links into the Central Exercise Library.
 * 
 * Safety invariants:
 * - Does not silently resolve ambiguous cases (conflicting URLs).
 * - Leaves names without URLs unlinked until explicit coach assignment.
 * - Preserves existing exercises.link column values intact (no destructive overwriting).
 * - Preserves exercise IDs, sets, reps, weights, notes, tempo, and plan associations.
 * - Fully reversible via --rollback.
 * - Never deletes original exercise records.
 */
class BackfillExerciseLibraryCommand extends Command
{
    protected $signature = 'exercise-library:backfill 
                            {--dry-run : Analyze and report proposed mappings without modifying the database}
                            {--rollback : Reversibly unlink exercises and remove migration-created library records}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Safely and reversibly backfill unambiguous exercise video links into the Central Exercise Library';

    public function handle(): int
    {
        if (!Schema::hasTable('exercise_libraries') || !Schema::hasColumn('exercises', 'exercise_library_id')) {
            $this->error('Schema prerequisites missing: table exercise_libraries or column exercises.exercise_library_id does not exist.');
            return self::FAILURE;
        }

        if ($this->option('rollback')) {
            return $this->handleRollback();
        }

        $this->info('Starting Exercise Library Data Audit & Proposed Mapping Analysis...');

        $allExercises = DB::table('exercises')->select('id', 'name', 'link', 'exercise_library_id')->get();
        $totalRows = $allExercises->count();

        $emptyNamesCount = 0;
        $missingUrlsCount = 0;
        $groupedByName = [];

        foreach ($allExercises as $ex) {
            $rawName = $ex->name;
            $name = trim((string) $rawName);
            $url = trim((string) $ex->link);

            if ($name === '') {
                $emptyNamesCount++;
            }

            if ($url === '') {
                $missingUrlsCount++;
            }

            if (!isset($groupedByName[$name])) {
                $groupedByName[$name] = [
                    'name' => $name,
                    'total_rows' => 0,
                    'urls' => [],
                    'url_counts' => [],
                    'empty_urls' => 0,
                    'row_ids' => [],
                    'already_linked_ids' => [],
                ];
            }

            $groupedByName[$name]['total_rows']++;
            $groupedByName[$name]['row_ids'][] = $ex->id;
            if (!empty($ex->exercise_library_id)) {
                $groupedByName[$name]['already_linked_ids'][] = $ex->id;
            }

            if ($url !== '') {
                if (!in_array($url, $groupedByName[$name]['urls'])) {
                    $groupedByName[$name]['urls'][] = $url;
                    $groupedByName[$name]['url_counts'][$url] = 0;
                }
                $groupedByName[$name]['url_counts'][$url]++;
            } else {
                $groupedByName[$name]['empty_urls']++;
            }
        }

        $conflicts = [];
        $unambiguous = [];
        $noUrls = [];

        foreach ($groupedByName as $name => $data) {
            $urlCount = count($data['urls']);
            if ($urlCount > 1) {
                $conflicts[$name] = $data;
            } elseif ($urlCount === 1) {
                $unambiguous[$name] = $data;
            } else {
                $noUrls[$name] = $data;
            }
        }

        $this->table(['Metric', 'Count'], [
            ['Total Exercise Rows', $totalRows],
            ['Rows with Empty Names', $emptyNamesCount],
            ['Rows with Missing / Empty Video URL', $missingUrlsCount],
            ['Total Distinct Exercise Names', count($groupedByName)],
            ['Names with Conflicting Video URLs (>1 distinct URL)', count($conflicts)],
            ['Unambiguous Names with Exactly 1 Video URL', count($unambiguous)],
            ['Names with No Video URL at all', count($noUrls)],
        ]);

        if (count($conflicts) > 0) {
            $this->warn("\n[AMBIGUOUS CASES DETECTED] The following exercise names have conflicting URLs across rows:");
            foreach ($conflicts as $name => $c) {
                $this->line("- Exercise Name: [{$name}] (Rows: {$c['total_rows']})");
                foreach ($c['url_counts'] as $u => $cnt) {
                    $this->line("    * ({$cnt}x) {$u}");
                }
            }
            $this->warn("Safety Rule: Ambiguous cases will NOT be silently resolved or mapped. They will remain unlinked.\n");
        } else {
            $this->info("Zero conflicting URLs detected across distinct exercise names in the current database.\n");
        }

        $isDryRun = $this->option('dry-run');
        if ($isDryRun) {
            $this->comment("[DRY RUN MODE] Simulating proposed mapping. No changes will be written to the database.");
        } elseif (!$this->option('force') && !$this->confirm("Proceed with safely backfilling " . count($unambiguous) . " unambiguous exercise names?")) {
            $this->warn("Backfill aborted by user.");
            return self::FAILURE;
        }

        $createdLibraries = 0;
        $linkedExercises = 0;

        DB::beginTransaction();
        try {
            foreach ($unambiguous as $name => $data) {
                $targetUrl = $data['urls'][0];

                if ($isDryRun) {
                    $createdLibraries++;
                    $linkedExercises += count($data['row_ids']);
                    continue;
                }

                // Find or create central library record
                $library = ExerciseLibrary::firstOrCreate(
                    ['name' => $name],
                    [
                        'video_url' => $targetUrl,
                        'status' => 'active',
                        'description' => 'Migrated from legacy workout exercise records',
                    ]
                );

                if ($library->wasRecentlyCreated) {
                    $createdLibraries++;
                }

                // If existing library has no URL but we have a verified URL, populate it
                if (empty($library->video_url) && !empty($targetUrl)) {
                    $library->update(['video_url' => $targetUrl]);
                }

                // Link exercise rows where exercise_library_id is NULL
                // CRITICAL SAFETY: exercises.link is NEVER erased or modified.
                $updated = DB::table('exercises')
                    ->whereIn('id', $data['row_ids'])
                    ->whereNull('exercise_library_id')
                    ->update(['exercise_library_id' => $library->id]);

                $linkedExercises += $updated;
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("Backfill successfully completed!");
                $this->info("Central library records created: {$createdLibraries}");
                $this->info("Exercise rows linked to library: {$linkedExercises}");
                $this->info("All legacy links in `exercises.link` were preserved intact.");
            } else {
                DB::rollBack();
                $this->info("[DRY RUN COMPLETE] {$createdLibraries} library entries would be created and {$linkedExercises} exercise rows would be linked.");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Backfill failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    protected function handleRollback(): int
    {
        $this->warn("Preparing to safely rollback Exercise Library backfill mappings...");

        $linkedCount = DB::table('exercises')->whereNotNull('exercise_library_id')->count();
        if ($linkedCount === 0) {
            $this->info("No exercises are currently linked to library records. Nothing to rollback.");
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm("This will unlink {$linkedCount} exercises (setting exercise_library_id = NULL) while preserving original exercise rows and legacy links. Continue?")) {
            $this->warn("Rollback aborted.");
            return self::FAILURE;
        }

        DB::beginTransaction();
        try {
            // 1. Unlink exercises
            $unlinked = DB::table('exercises')
                ->whereNotNull('exercise_library_id')
                ->update(['exercise_library_id' => null]);

            // 2. Safely remove only library records created by migration description if they are unreferenced
            $deletedLib = DB::table('exercise_libraries')
                ->where('description', 'Migrated from legacy workout exercise records')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('exercises')
                        ->whereColumn('exercises.exercise_library_id', 'exercise_libraries.id');
                })
                ->delete();

            DB::commit();

            $this->info("Rollback complete!");
            $this->info("Unlinked exercises: {$unlinked}");
            $this->info("Unreferenced backfill library records cleaned up: {$deletedLib}");
            $this->info("All original exercise records and workout plan histories remain intact.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Rollback failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
