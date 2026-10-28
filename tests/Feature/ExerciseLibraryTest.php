<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\ExerciseLibrary;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Repositories\Workout\WorkoutRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature tests for the Central Exercise Video Library (Phase 1).
 */
class ExerciseLibraryTest extends TestCase
{
    use DatabaseTransactions;

    protected ?User $coachUser = null;
    protected ?User $traineeUser = null;
    protected ?Trainee $trainee = null;
    protected ?PermissionGroup $permGroup = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create dedicated permission group with exercise library and workout permissions
        $this->permGroup = PermissionGroup::create([
            'name' => 'Coach Test Group ' . uniqid(),
            'system' => 'gym',
            'is_supervisor' => 'no',
        ]);

        $routes = [
            'system.workout.index',
            'system.workout.create',
            'system.workout.store',
            'system.workout.edit',
            'system.workout.update',
            'system.workout.updateDay',
            'system.workout.storeInlineDay',
            'system.workout.toggleDayStatus',
            'system.exercise-library.index',
            'system.exercise-library.create',
            'system.exercise-library.store',
            'system.exercise-library.edit',
            'system.exercise-library.update',
            'system.exercise-library.destroy',
            'system.exercise-library.toggle-status',
            'system.exercise-library.search',
            'system.trainee.index',
            'system.trainee.show',
        ];

        foreach ($routes as $route) {
            Permission::create([
                'permission_group_id' => $this->permGroup->id,
                'route_name' => $route,
            ]);
        }

        // Coach user
        $this->coachUser = User::create([
            'name' => 'Coach Library Tester',
            'email' => 'coach_lib_' . uniqid() . '@gymtest.local',
            'password' => Hash::make('Secret123!'),
            'status' => '1',
            'permission_group_id' => $this->permGroup->id,
            'user_type' => 1,
            'default_language' => 'ar',
            'force_reset_password' => false,
        ]);

        // Trainee user & trainee model
        $this->traineeUser = User::create([
            'name' => 'Trainee Library Tester',
            'email' => 'trainee_lib_' . uniqid() . '@gymtest.local',
            'password' => Hash::make('Secret123!'),
            'status' => '1',
            'permission_group_id' => null,
            'user_type' => 2,
            'default_language' => 'ar',
            'force_reset_password' => false,
        ]);

        $this->trainee = Trainee::create([
            'user_id' => $this->traineeUser->id,
            'name' => 'Trainee Library Tester',
            'email' => $this->traineeUser->email,
            'membership_start' => now()->subDays(10)->toDateString(),
            'membership_end' => now()->addDays(80)->toDateString(),
            'training_level' => 'beginner',
            'status' => 'active',
        ]);
    }

    /**
     * 1. Updating central library video URL updates all linked plans immediately.
     */
    public function test_updating_central_video_url_updates_all_linked_plans(): void
    {
        // 1. Create a central library item
        $library = ExerciseLibrary::create([
            'name' => 'Incline Dumbbell Press',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'active',
        ]);

        // 2. Create Plan 1 for Trainee 1
        $plan1 = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Chest Day A',
            'status' => 'active',
        ]);

        $ex1 = Exercise::create([
            'workout_plan_id' => $plan1->id,
            'exercise_library_id' => $library->id,
            'name' => 'Incline Dumbbell Press',
            'sets' => 4,
            'reps' => '10-12',
            'rest' => '90s',
            'internal_weight' => ['20kg', '22.5kg'],
            'link' => null,
        ]);

        // 3. Create another trainee and Plan 2
        $trainee2User = User::create([
            'name' => 'Trainee Two',
            'email' => 'trainee2_' . uniqid() . '@gymtest.local',
            'password' => Hash::make('Secret123!'),
            'status' => '1',
            'user_type' => 2,
            'force_reset_password' => false,
        ]);
        $trainee2 = Trainee::create([
            'user_id' => $trainee2User->id,
            'name' => 'Trainee Two',
            'email' => $trainee2User->email,
            'membership_start' => now()->toDateString(),
            'membership_end' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ]);
        $plan2 = WorkoutPlan::create([
            'trainee_id' => $trainee2->id,
            'day_name' => 'Upper Body B',
            'status' => 'active',
        ]);

        $ex2 = Exercise::create([
            'workout_plan_id' => $plan2->id,
            'exercise_library_id' => $library->id,
            'name' => 'Incline Dumbbell Press',
            'sets' => 3,
            'reps' => '15',
            'rest' => '60s',
            'internal_weight' => ['15kg'],
            'link' => null,
        ]);

        // Assert initial resolved links
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $ex1->fresh()->link);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $ex2->fresh()->link);

        // Update central video URL via service / controller
        $newUrl = 'https://www.youtube.com/watch?v=9bZkp7q19f0';
        $response = $this->actingAs($this->coachUser, 'user')
            ->patch(route('system.exercise-library.update', $library->id), [
                'name' => 'Incline Dumbbell Press',
                'video_url' => $newUrl,
                'status' => 'active',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('exercise_libraries', [
            'id' => $library->id,
            'video_url' => $newUrl,
        ]);

        // Verify both exercises resolve new video URL immediately without re-saving workout plans
        $this->assertEquals($newUrl, $ex1->fresh()->link);
        $this->assertEquals($newUrl, $ex2->fresh()->link);
        $this->assertEquals('https://www.youtube.com/embed/9bZkp7q19f0', $ex1->fresh()->formatted_link);
    }

    /**
     * 2. Updating central video URL preserves each trainee's independent settings (weights, sets, reps, rest, tempo).
     */
    public function test_updating_central_url_preserves_independent_trainee_settings(): void
    {
        $library = ExerciseLibrary::create([
            'name' => 'Barbell Squat',
            'video_url' => 'https://www.youtube.com/watch?v=squatV1',
            'status' => 'active',
        ]);

        $plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Leg Day',
            'status' => 'active',
        ]);

        $exercise = Exercise::create([
            'workout_plan_id' => $plan->id,
            'exercise_library_id' => $library->id,
            'name' => 'Barbell Squat',
            'sets' => 5,
            'reps' => '5',
            'rest' => '120s',
            'tempo' => '3-1-1',
            'notes_en' => 'Drive through the midfoot and keep core braced',
            'internal_weight' => ['100kg', '110kg', '120kg', '125kg'],
            'link' => null,
        ]);

        // Update central URL
        $library->update(['video_url' => 'https://www.youtube.com/watch?v=squatV2']);

        $refreshed = $exercise->fresh();

        // Video link is updated
        $this->assertEquals('https://www.youtube.com/watch?v=squatV2', $refreshed->link);

        // Trainee-specific settings remain 100% untouched
        $this->assertEquals(5, $refreshed->sets);
        $this->assertEquals('5', $refreshed->reps);
        $this->assertEquals('120s', $refreshed->rest);
        $this->assertEquals('3-1-1', $refreshed->tempo);
        $this->assertEquals('Drive through the midfoot and keep core braced', $refreshed->notes_en);
        $this->assertEquals(['100kg', '110kg', '120kg', '125kg'], $refreshed->internal_weight);
    }

    /**
     * 3. Legacy unlinked exercises preserve their local links.
     */
    public function test_unlinked_legacy_exercises_preserve_local_links(): void
    {
        $plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Custom Legacy Day',
            'status' => 'active',
        ]);

        $legacyUrl = 'https://drive.google.com/file/d/1a2b3c4d5e/view?usp=sharing';
        $exercise = Exercise::create([
            'workout_plan_id' => $plan->id,
            'exercise_library_id' => null, // Unlinked
            'name' => 'Custom Special Exercise',
            'sets' => 3,
            'reps' => '12',
            'rest' => '45s',
            'link' => $legacyUrl,
        ]);

        $this->assertFalse($exercise->is_linked_to_library);
        $this->assertEquals($legacyUrl, $exercise->link);
        $this->assertEquals('https://drive.google.com/file/d/1a2b3c4d5e/preview', $exercise->formatted_link);
    }

    /**
     * 4. Deactivation prevents selection for new assignments but does not break existing plans.
     */
    public function test_deactivation_prevents_new_selection_without_breaking_existing_plans(): void
    {
        $library = ExerciseLibrary::create([
            'name' => 'Romanian Deadlift',
            'video_url' => 'https://www.youtube.com/watch?v=rdlVideo111',
            'status' => 'active',
        ]);

        $plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Posterior Chain',
            'status' => 'active',
        ]);

        $exercise = Exercise::create([
            'workout_plan_id' => $plan->id,
            'exercise_library_id' => $library->id,
            'name' => 'Romanian Deadlift',
            'sets' => 3,
            'reps' => '10',
            'rest' => '90s',
            'link' => null,
        ]);

        // Toggle status to inactive via controller
        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.exercise-library.toggle-status', $library->id));

        $response->assertOk();
        $this->assertEquals('inactive', $library->fresh()->status);

        // 1. Search for new assignments: Inactive exercise should NOT appear in search results
        $searchRes = $this->actingAs($this->coachUser, 'user')
            ->get(route('system.exercise-library.search', ['q' => 'Romanian']));

        $searchRes->assertOk();
        $results = $searchRes->json('results');
        $this->assertEmpty($results, 'Inactive exercise must not appear in search results for new workout plan assignments.');

        // 2. Existing plan: Exercise continues to resolve video URL seamlessly without breaking
        $refreshed = $exercise->fresh();
        $this->assertEquals('https://www.youtube.com/watch?v=rdlVideo111', $refreshed->link);
        $this->assertEquals('https://www.youtube.com/embed/rdlVideo111', $refreshed->formatted_link);

        // Trainee dashboard still loads the plan without exceptions
        $traineeDash = $this->actingAs($this->traineeUser, 'user')
            ->get(route('system.dashboard.trainer'));

        $traineeDash->assertOk();
        $traineeDash->assertSee('Romanian Deadlift');
    }

    /**
     * 5. Authorization: Trainees cannot access or modify central exercise library.
     */
    public function test_trainee_cannot_access_or_modify_exercise_library(): void
    {
        $library = ExerciseLibrary::create([
            'name' => 'Overhead Press',
            'video_url' => 'https://www.youtube.com/watch?v=ohp123',
            'status' => 'active',
        ]);

        // Trainee visiting index is redirected to trainee dashboard by permission middleware
        $response = $this->actingAs($this->traineeUser, 'user')
            ->get(route('system.exercise-library.index'));

        $response->assertRedirect(route('system.dashboard.trainer'));

        // Trainee attempting to create is blocked
        $postRes = $this->actingAs($this->traineeUser, 'user')
            ->post(route('system.exercise-library.store'), [
                'name' => 'Unauthorized Exercise',
                'video_url' => 'https://www.youtube.com/watch?v=hack',
                'status' => 'active',
            ]);

        $postRes->assertRedirect(route('system.dashboard.trainer'));
        $this->assertDatabaseMissing('exercise_libraries', ['name' => 'Unauthorized Exercise']);
    }

    /**
     * 6. Query count and eager loading verification (prevents N+1 database queries).
     */
    public function test_eager_loading_prevents_n_plus_one_queries_on_workout_exercises(): void
    {
        $lib1 = ExerciseLibrary::create(['name' => 'Exercise 1', 'video_url' => 'https://youtube.com/watch?v=111']);
        $lib2 = ExerciseLibrary::create(['name' => 'Exercise 2', 'video_url' => 'https://youtube.com/watch?v=222']);
        $lib3 = ExerciseLibrary::create(['name' => 'Exercise 3', 'video_url' => 'https://youtube.com/watch?v=333']);

        $plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Day 1',
            'status' => 'active',
        ]);

        Exercise::create(['workout_plan_id' => $plan->id, 'exercise_library_id' => $lib1->id, 'name' => 'Ex 1', 'sets' => 3]);
        Exercise::create(['workout_plan_id' => $plan->id, 'exercise_library_id' => $lib2->id, 'name' => 'Ex 2', 'sets' => 3]);
        Exercise::create(['workout_plan_id' => $plan->id, 'exercise_library_id' => $lib3->id, 'name' => 'Ex 3', 'sets' => 3]);

        $repo = app(WorkoutRepository::class);

        // Count queries during getExcercisesByTrainee
        DB::enableQueryLog();
        DB::flushQueryLog();

        $plans = $repo->getExcercisesByTrainee($this->trainee->id);
        $this->assertNotEmpty($plans);

        // Access resolved video URLs for all exercises
        foreach ($plans as $p) {
            foreach ($p->exercises as $ex) {
                $this->assertNotNull($ex->link);
                $this->assertTrue($ex->relationLoaded('exerciseLibrary'), 'exerciseLibrary must be eager loaded to avoid N+1 queries.');
            }
        }

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // The query count should be constant regardless of the number of exercises
        $this->assertLessThanOrEqual(10, $queryCount, 'Eager loading must keep query count low and constant.');
    }

    /**
     * 7. Workout day synchronization supports adding and updating exercise_library_id.
     */
    public function test_sync_day_exercises_associates_exercise_library_id(): void
    {
        $library = ExerciseLibrary::create([
            'name' => 'Lat Pulldown',
            'video_url' => 'https://www.youtube.com/watch?v=latPulldown',
            'status' => 'active',
        ]);

        $plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name' => 'Back Day',
            'status' => 'active',
        ]);

        $repo = app(WorkoutRepository::class);

        $exercisesData = [
            [
                'name' => 'Lat Pulldown',
                'exercise_library_id' => $library->id,
                'sets' => 4,
                'reps' => '10-12',
                'rest' => '60s',
                'type' => 'main',
            ]
        ];

        $repo->syncDayExercises($plan->id, $exercisesData);

        $savedExercise = Exercise::where('workout_plan_id', $plan->id)->first();
        $this->assertNotNull($savedExercise);
        $this->assertEquals($library->id, $savedExercise->exercise_library_id);
        $this->assertEquals('https://www.youtube.com/watch?v=latPulldown', $savedExercise->link);
    }
}
