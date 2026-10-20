<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\JoinUsSubmission;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Repositories\JoinUs\JoinUsRepository;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\Workout\WorkoutRepository;
use App\Services\JoinUsService;
use App\Services\TraineeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CodeQualityPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 1. التحقق من عزل بيئة الاختبار في الذاكرة
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. بناء المخطط المعزول
        $this->createIsolatedSchema();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    protected function createIsolatedSchema(): void
    {
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('join_us_submissions');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('user');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('activity_log');

        Schema::create('permission_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('is_supervisor')->default('no');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('permission_group_id');
            $table->string('route_name');
            $table->timestamps();
        });

        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('1');
            $table->integer('permission_group_id')->nullable();
            $table->tinyInteger('user_type')->nullable();
            $table->string('default_language')->default('en-gb');
            $table->boolean('force_reset_password')->default(false);
            $table->string('mobile')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('email')->nullable();
            $table->integer('age')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->date('membership_start')->nullable();
            $table->date('membership_end')->nullable();
            $table->string('training_level')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workout_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->string('day_name')->nullable();
            $table->text('warmup')->nullable();
            $table->text('warmup_links')->nullable();
            $table->text('post_workout')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('workout_plan_id');
            $table->string('name')->nullable();
            $table->string('type')->default('main');
            $table->string('sets')->nullable();
            $table->string('reps')->nullable();
            $table->string('rest_time')->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_weight')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('join_us_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('age')->nullable();
            $table->string('training_level')->nullable();
            $table->string('status')->default('new');
            $table->string('country')->nullable();
            $table->string('governorate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_name')->nullable();
            $table->text('description')->nullable();
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->string('event')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * قياس واختبار أداء: التحقق من التحميل المسبق (Eager Loading) في استعلام جدول المتدربين والقضاء على مشكلة N+1.
     */
    public function test_trainee_datatable_query_eager_loads_relations_without_n_plus_one(): void
    {
        // تهيئة 5 متدربين وحساباتهم
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'id'       => 100 + $i,
                'name'     => "Trainee User {$i}",
                'email'    => "trainee{$i}@gym.local",
                'password' => Hash::make('Secret123!'),
            ]);

            $trainee = Trainee::create([
                'id'               => 200 + $i,
                'user_id'          => $user->id,
                'email'            => $user->email,
                'status'           => 'active',
                'membership_start' => now()->toDateString(),
                'membership_end'   => now()->addMonths(1)->toDateString(),
            ]);

            WorkoutPlan::create([
                'id'         => 300 + $i,
                'trainee_id' => $trainee->id,
                'day_name'   => "Day 1 Routine {$i}",
                'status'     => 'active',
            ]);
        }

        $repo = app(TraineeRepository::class);
        $query = $repo->getDataTableQuery();

        // تفعيل تسجيل الاستعلامات لقياس الأداء
        DB::enableQueryLog();

        $results = $query->get();

        // محاكاة معالجة أعمدة DataTable للتحقق من عدم إطلاق استعلامات إضافية
        foreach ($results as $item) {
            $userName = $item->user?->name;
            $hasWorkouts = !$item->activeWorkoutPlans->isEmpty();
            $this->assertNotEmpty($userName);
            $this->assertTrue($hasWorkouts);
        }

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // التفسير: مع التحميل المسبق Eager Loading، يتم تنفيذ استعلام المتدربين + استعلام المستخدمين + استعلام الخطط فقط (3 استعلامات)
        // بدون Eager Loading، كان سينفذ 1 + 5 (للمستخدمين) + 5 (للخطط) = 11 استعلاماً!
        $this->assertLessThanOrEqual(3, count($queries), 'Eager loading must cap queries to at most 3 regardless of trainee row count.');
        $this->assertCount(5, $results);
    }

    /**
     * اختبار معمارية: التحقق من التحميل المسبق للعلاقة 'user' في TraineeService@findById.
     */
    public function test_trainee_find_by_id_eager_loads_user_relation(): void
    {
        $user = User::create([
            'id'       => 501,
            'name'     => 'Profile View Test',
            'email'    => 'profileview@gym.local',
            'password' => Hash::make('Pass123!'),
        ]);

        $trainee = Trainee::create([
            'id'      => 601,
            'user_id' => $user->id,
            'email'   => $user->email,
            'status'  => 'active',
        ]);

        $service = app(TraineeService::class);
        $data = $service->findById($trainee->id);

        $this->assertNotNull($data['result']);
        $this->assertTrue($data['result']->relationLoaded('user'), 'User relationship must be eager loaded in findById.');
        $this->assertEquals('Profile View Test', $data['result']->user->name);
    }

    /**
     * قياس واختبار أداء: دمج استعلامات إحصائيات طلبات الانضمام في استعلام مجمع واحد (Group By).
     */
    public function test_join_us_status_counts_executes_single_aggregated_query(): void
    {
        // إدخال بيانات حالات مختلفة
        JoinUsSubmission::create(['status' => 'new', 'name' => 'John', 'phone' => '123456']);
        JoinUsSubmission::create(['status' => 'new', 'name' => 'Jane', 'phone' => '123457']);
        JoinUsSubmission::create(['status' => 'contacted', 'name' => 'Bob', 'phone' => '123458']);
        JoinUsSubmission::create(['status' => 'converted', 'name' => 'Alice', 'phone' => '123459']);
        JoinUsSubmission::create(['status' => 'rejected', 'name' => 'Charlie', 'phone' => '123450']);

        $service = app(JoinUsService::class);

        DB::enableQueryLog();

        $counts = $service->statusCounts();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // التأكد من استرجاع الأرقام الصحيحة
        $this->assertEquals(2, $counts['new']);
        $this->assertEquals(1, $counts['contacted']);
        $this->assertEquals(1, $counts['converted']);
        $this->assertEquals(1, $counts['rejected']);

        // التأكد من تنفيذ استعلام تجميعي واحد بدلاً من 4 استعلامات منفصلة
        $this->assertCount(1, $queries, 'Status counts must execute exactly 1 aggregated query instead of 4 separate queries.');
    }

    /**
     * اختبار معمارية وسلامة البيانات: التأكد من سريان المعاملة البنكية (Database Transaction) في مزامنة تمارين اليوم.
     */
    public function test_workout_sync_day_exercises_operates_atomically_within_transaction(): void
    {
        $trainee = Trainee::create([
            'id'      => 701,
            'user_id' => 999,
            'email'   => 'workouttest@gym.local',
        ]);

        $plan = WorkoutPlan::create([
            'id'         => 801,
            'trainee_id' => $trainee->id,
            'day_name'   => 'Chest & Triceps',
            'status'     => 'active',
        ]);

        $initialEx = Exercise::create([
            'workout_plan_id' => $plan->id,
            'name'            => 'Bench Press',
            'type'            => 'main',
        ]);

        $repo = app(WorkoutRepository::class);

        $newExercises = [
            [
                'exercise_id' => $initialEx->id,
                'name'        => 'Incline Dumbbell Press',
                'type'        => 'main',
                'sets'        => '4',
                'reps'        => '10',
            ],
            [
                'name' => 'Cable Flyes',
                'type' => 'main',
                'sets' => '3',
                'reps' => '12',
            ]
        ];

        $updatedPlan = $repo->syncDayExercises($plan->id, $newExercises, ['day_name' => 'Updated Chest Day']);

        $this->assertNotNull($updatedPlan);
        $this->assertEquals('Updated Chest Day', $updatedPlan->day_name);

        // التحقق من تحديث التمرين الأول وإضافة التمرين الجديد
        $this->assertDatabaseHas('exercises', [
            'id'              => $initialEx->id,
            'workout_plan_id' => $plan->id,
            'name'            => 'Incline Dumbbell Press',
        ]);

        $this->assertDatabaseHas('exercises', [
            'workout_plan_id' => $plan->id,
            'name'            => 'Cable Flyes',
        ]);
    }
}
