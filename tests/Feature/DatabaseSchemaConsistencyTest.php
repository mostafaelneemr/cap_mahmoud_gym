<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\NutritionPlan;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaConsistencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // التأكد من العزل الصارم لقاعدة البيانات قبل تنفيذ أي عمليات
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // تشغيل كافة الترحيلات (الأساسية والأمامية) في بيئة الذاكرة النظيفة
        Artisan::call('migrate');
    }

    protected function tearDown(): void
    {
        Artisan::call('migrate:rollback');
        parent::tearDown();
    }

    /**
     * 1. التحقق من أن تشغيل الترحيلات في بيئة جديدة ينشئ كافة جداول التطبيق بدون أخطاء.
     */
    public function test_fresh_migration_creates_all_expected_application_tables_in_isolated_sqlite(): void
    {
        $expectedTables = [
            'permission_groups',
            'user',
            'trainees',
            'workout_plans',
            'exercises',
            'workout_logs',
            'nutrition_plans',
            'posts',
            'post_items',
            'join_us_submissions',
            'message',
            'settings',
            'permissions',
            'auth_session',
            'activity_log',
            'activate_section',
            'language',
            'social_links',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
            'trainee_progress_checkins',
            'trainee_progress_photos',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table [{$table}] must exist after running migrations."
            );
        }
    }

    /**
     * 2. التحقق من وجود الأعمدة الحيوية والحديثة في الجداول المزامنة.
     */
    public function test_migrated_schema_has_all_required_columns_and_attributes(): void
    {
        // جدول خطط التمرين: وجود حقل روابط الإحماء
        $this->assertTrue(Schema::hasColumn('workout_plans', 'warmup_links'));

        // جدول التمارين: وجود النوع وملاحظات الإنجليزية وصورة الجهاز
        $this->assertTrue(Schema::hasColumn('exercises', 'type'));
        $this->assertTrue(Schema::hasColumn('exercises', 'notes_en'));
        $this->assertTrue(Schema::hasColumn('exercises', 'machine_image'));

        // جدول تسجيلات التقدم: وجود الحقول الرياضية والملاحظات
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'trainee_id'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'coach_id'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'checkin_date'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'weight'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'notes'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_checkins', 'coach_notes'));

        // جدول صور التقدم: وجود المفتاح الأجنبي ومسار الصورة
        $this->assertTrue(Schema::hasColumn('trainee_progress_photos', 'checkin_id'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_photos', 'photo_path'));
        $this->assertTrue(Schema::hasColumn('trainee_progress_photos', 'caption'));
    }

    /**
     * 3. التحقق من صحة علاقات Eloquent المتبادلة بين النماذج الأساسية.
     */
    public function test_eloquent_relationships_resolve_correctly_between_models(): void
    {
        // أ. مجموعة الصلاحيات والصلاحيات (PermissionGroup <-> Permission)
        $group = PermissionGroup::create([
            'name'          => 'Test Role Group',
            'is_supervisor' => 'no',
        ]);

        $permission = Permission::create([
            'permission_group_id' => $group->id,
            'route_name'          => 'system.test.route',
        ]);

        $this->assertTrue($permission->permission_group->is($group));
        $this->assertTrue($group->permission->first()->is($permission));

        // ب. المستخدم والمتدرب (User <-> Trainee)
        $user = User::create([
            'name'                 => 'Synthetic Trainee User',
            'email'                => 'synthetic.user@schema-test.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => 1,
            'permission_group_id'  => $group->id,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $trainee = Trainee::create([
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
        ]);

        $this->assertTrue($user->trainer->is($trainee));
        $this->assertTrue($trainee->user->is($user));

        // ج. المتدرب وخطة التمرين (Trainee <-> WorkoutPlan)
        $workoutPlan = WorkoutPlan::create([
            'trainee_id'   => $trainee->id,
            'day_name'     => 'Chest & Triceps',
            'warmup'       => '10 min treadmill',
            'warmup_links' => ['https://youtube.com/watch?v=sample1'],
            'status'       => 'active',
        ]);

        $this->assertTrue($trainee->workoutPlans->first()->is($workoutPlan));
        $this->assertTrue($workoutPlan->trainee->is($trainee));

        // د. خطة التمرين والتمارين ونطاقات الأنواع (WorkoutPlan <-> Exercise)
        $warmupExercise = Exercise::create([
            'workout_plan_id' => $workoutPlan->id,
            'name'            => 'Arm Circles',
            'type'            => 'warmup',
            'sets'            => 2,
            'reps'            => '15',
        ]);

        $mainExercise = Exercise::create([
            'workout_plan_id' => $workoutPlan->id,
            'name'            => 'Bench Press',
            'type'            => 'main',
            'sets'            => 4,
            'reps'            => '10',
            'machine_image'   => 'exercises/machines/bench.jpg',
        ]);

        $postExercise = Exercise::create([
            'workout_plan_id' => $workoutPlan->id,
            'name'            => 'Chest Stretch',
            'type'            => 'post_workout',
            'sets'            => 1,
            'reps'            => '30s',
        ]);

        $this->assertTrue($mainExercise->workoutPlan->is($workoutPlan));
        $this->assertCount(3, $workoutPlan->exercises);
        $this->assertCount(1, $workoutPlan->warmupExercises);
        $this->assertCount(1, $workoutPlan->mainExercises);
        $this->assertCount(1, $workoutPlan->postWorkoutExercises);
        $this->assertSame('exercises/machines/bench.jpg', $mainExercise->machine_image);

        // هـ. المتدرب وخطة التغذية (Trainee <-> NutritionPlan)
        $nutritionPlan = NutritionPlan::create([
            'trainee_id'  => $trainee->id,
            'name'        => 'High Protein Cutting',
            'description' => 'Detailed nutrition guidelines',
            'status'      => 'active',
        ]);

        $this->assertTrue($trainee->nutritionPlans->first()->is($nutritionPlan));
        $this->assertTrue($nutritionPlan->trainee->is($trainee));

        // و. المتدرب وتسجيلات التقدم والصور (Trainee <-> TraineeProgressCheckin <-> TraineeProgressPhoto)
        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $trainee->id,
            'coach_id'     => null,
            'checkin_date' => '2026-03-01',
            'weight'       => 78.50,
            'notes'        => 'Felt great this week',
            'coach_notes'  => 'Good consistency',
        ]);

        $photo = TraineeProgressPhoto::create([
            'checkin_id' => $checkin->id,
            'photo_path' => 'trainee_photos/checkin_front.jpg',
            'caption'    => 'Front View',
        ]);

        $this->assertTrue($trainee->progressCheckins->first()->is($checkin));
        $this->assertTrue($checkin->trainee->is($trainee));
        $this->assertTrue($checkin->photos->first()->is($photo));
        $this->assertTrue($photo->checkin->is($checkin));
    }

    /**
     * 4. التحقق من التمييز الصارم بين معرف المتدرب (trainees.id) ومعرف المستخدم (user.id).
     */
    public function test_trainee_and_user_identifiers_are_strictly_distinguished(): void
    {
        $user = User::create([
            'id'                   => 8801,
            'name'                 => 'Distinct Trainee User',
            'email'                => 'distinct@schema-test.local',
            'password'             => Hash::make('Password8801!'),
            'status'               => 1,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $trainee = Trainee::create([
            'id'               => 9901,
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
        ]);

        $this->assertNotEquals($trainee->id, $user->id);
        $this->assertSame(9901, (int) $trainee->id);
        $this->assertSame(8801, (int) $user->id);
        $this->assertSame(8801, (int) $trainee->user_id);
        $this->assertSame($user->id, $trainee->user->id);
        $this->assertSame($trainee->id, $user->trainer->id);
    }

    /**
     * 5. التحقق من سلوك الحذف الآمن المتسلسل للمتدرب وخططه التدريبية.
     */
    public function test_soft_delete_cascades_from_trainee_to_workout_plans(): void
    {
        $user = User::create([
            'name'                 => 'Cascade Trainee User',
            'email'                => 'cascade@schema-test.local',
            'password'             => Hash::make('Pass123!'),
            'status'               => 1,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $trainee = Trainee::create([
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
        ]);

        $workoutPlan = WorkoutPlan::create([
            'trainee_id' => $trainee->id,
            'day_name'   => 'Back Day',
            'status'     => 'active',
        ]);

        $exercise = Exercise::create([
            'workout_plan_id' => $workoutPlan->id,
            'name'            => 'Deadlift',
            'type'            => 'main',
            'sets'            => 3,
            'reps'            => '5',
        ]);

        // حذف المتدرب ناعماً
        $trainee->delete();

        // التحقق من الحذف الناعم للمتدرب وخطة التمرين
        $this->assertSoftDeleted('trainees', ['id' => $trainee->id]);
        $this->assertSoftDeleted('workout_plans', ['id' => $workoutPlan->id]);
        $this->assertSoftDeleted('exercises', ['id' => $exercise->id]);
    }

    /**
     * 6. التحقق من حذف صور التقدم عند حذف سجل المتابعة التابع لها.
     */
    public function test_progress_checkin_and_photos_maintain_valid_ownership_and_cascade_delete(): void
    {
        $user = User::create([
            'name'                 => 'Photo Test User',
            'email'                => 'photos@schema-test.local',
            'password'             => Hash::make('Pass123!'),
            'status'               => 1,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $trainee = Trainee::create([
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
        ]);

        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $trainee->id,
            'checkin_date' => '2026-04-01',
            'weight'       => 75.0,
        ]);

        $photo = TraineeProgressPhoto::create([
            'checkin_id' => $checkin->id,
            'photo_path' => 'photos/test_checkin.png',
            'caption'    => 'Side View',
        ]);

        $this->assertDatabaseHas('trainee_progress_photos', ['id' => $photo->id]);

        // حذف سجل المتابعة
        $checkin->delete();

        // التحقق من حذف الصورة المرتبطة تلقائياً عبر حدث deleting في TraineeProgressCheckin
        $this->assertDatabaseMissing('trainee_progress_checkins', ['id' => $checkin->id]);
        $this->assertDatabaseMissing('trainee_progress_photos', ['id' => $photo->id]);
    }

    /**
     * 7. التحقق الصارم من أن بيئة الاختبار معزولة تماماً في ذاكرة SQLite ولا تتصل بقاعدة الإنتاج.
     */
    public function test_isolated_test_environment_is_strictly_sqlite_in_memory(): void
    {
        $connection = DB::connection();
        $this->assertInstanceOf(SQLiteConnection::class, $connection);
        $this->assertSame('sqlite', $connection->getName());
        $this->assertSame('sqlite', $connection->getDriverName());
        $this->assertSame(':memory:', $connection->getDatabaseName());

        // التحقق من أن بيئة التطبيق هي 'testing'
        $this->assertSame('testing', app()->environment());
    }
}
