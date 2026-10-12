<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Clears Arabic content (name_ar, notes_ar) from the exercises table.
 * Data-only operation: no schema change, no rows deleted.
 */
class ClearExerciseArabicData extends Command
{
    protected $signature = 'exercises:clear-arabic {--force : Skip the confirmation prompt}';

    protected $description = 'Set name_ar and notes_ar to NULL for all exercises (table structure untouched)';

    public function handle(): int
    {
        $hasNameAr = \Illuminate\Support\Facades\Schema::hasColumn('exercises', 'name_ar');
        $hasNotesAr = \Illuminate\Support\Facades\Schema::hasColumn('exercises', 'notes_ar');

        if (!$hasNameAr && !$hasNotesAr) {
            $this->info('Arabic columns (name_ar, notes_ar) do not exist in the exercises table.');
            return self::SUCCESS;
        }

        $affected = DB::table('exercises')
            ->where(fn ($q) => $q->when($hasNameAr, fn ($q) => $q->whereNotNull('name_ar'))
                ->when($hasNotesAr, fn ($q) => $q->orWhereNotNull('notes_ar')))
            ->count();

        if ($affected === 0) {
            $this->info('No Arabic data found. Nothing to do.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm("This will clear Arabic fields on {$affected} exercise(s). Continue?")) {
            $this->warn('Aborted.');
            return self::FAILURE;
        }

        // Equivalent to: UPDATE `exercises` SET `name_ar` = NULL, `notes_ar` = NULL;
        $updated = DB::table('exercises')->update([
            'name_ar' => null,
            'notes_ar' => null,
        ]);

        $this->info("Done. {$updated} row(s) updated.");
        return self::SUCCESS;
    }
}
