<?php

namespace Tests\Feature;

use App\Models\AuthSession;
use App\Models\NutritionPlan;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TraineeManagementTest extends TestCase
{
    protected User $adminUser;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Verify strict SQLite in-memory test database isolation
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. Build isolated test schema
        $this->createIsolatedSchema();

        // 3. Clear permission and application cache
        \Illuminate\Support\Facades\Cache::flush();

        // 4. Seed synthetic test data
        $this->seedSyntheticData();
    }

    protected function createIsolatedSchema(): void
    {
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('nutrition_plans');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('permission_groups');
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
            $table->softDeletes();
        });

        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('1');
            $table->integer('permission_group_id')->nullable();
            $table->tinyInteger('user_type')->nullable();
            $table->string('mobile')->nullable();
            $table->string('default_language')->default('en-gb');
            $table->boolean('force_reset_password')->default(false);
            $table->string('two_fa_secret')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->string('password')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->date('membership_start')->nullable();
            $table->date('membership_end')->nullable();
            $table->string('training_level')->default('beginner');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workout_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->string('day_name')->default('Day 1');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('workout_plan_id');
            $table->string('name')->nullable();
            $table->string('type')->default('main');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->string('name')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('auth_session', function (Blueprint $table) {
            $table->increments('id');
            $table->string('guard_name')->default('user');
            $table->string('access_token')->unique();
            $table->unsignedInteger('user_id');
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('trainee_progress_checkins', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->unsignedInteger('coach_id')->nullable();
            $table->date('checkin_date');
            $table->decimal('weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('coach_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('trainee_progress_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checkin_id');
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->timestamps();
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

    protected function seedSyntheticData(): void
    {
        // 1. Admin permission group
        $adminGroup = DB::table('permission_groups')->insertGetId([
            'name'          => 'Super Admin',
            'is_supervisor' => 'yes',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $adminRoutes = [
            'system.trainee.index',
            'system.trainee.create',
            'system.trainee.store',
            'system.trainee.show',
            'system.trainee.edit',
            'system.trainee.update',
            'system.trainee.destroy',
            'system.trainee.get-activity-log',
            'system.trainee.get-auth-session',
            'system.trainee.get-workout',
            'system.trainee.reset-plan',
            'system.trainee.restore-plan',
            'system.trainee.impersonate',
            'system.impersonate.leave',
            'system.dashboard.trainer',
        ];

        foreach ($adminRoutes as $route) {
            DB::table('permissions')->insert([
                'permission_group_id' => $adminGroup,
                'route_name'          => $route,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // 2. Trainees permission group
        DB::table('permission_groups')->insert([
            'id'            => 125,
            'name'          => 'Trainees',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Admin User (Coach/Admin) - ID 101
        $this->adminUser = User::create([
            'id'                   => 101,
            'name'                 => 'Coach Mahmoud Admin',
            'email'                => 'admin@synthetic-gym-test.local',
            'password'             => Hash::make('AdminSecret123!'),
            'status'               => '1',
            'permission_group_id'  => $adminGroup,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // Trainee A User & Record - User ID 201, Trainee ID 301
        $this->traineeUserA = User::create([
            'id'                   => 201,
            'name'                 => 'Trainee Alice',
            'email'                => 'alice@synthetic-gym-test.local',
            'password'             => Hash::make('PasswordA123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'id'               => 301,
            'user_id'          => $this->traineeUserA->id,
            'name'             => $this->traineeUserA->name,
            'email'            => $this->traineeUserA->email,
            'status'           => 'active',
            'age'              => 25,
            'weight'           => 65.5,
            'height'           => 170.0,
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ]);

        // Trainee B User & Record - User ID 202, Trainee ID 302
        $this->traineeUserB = User::create([
            'id'                   => 202,
            'name'                 => 'Trainee Bob',
            'email'                => 'bob@synthetic-gym-test.local',
            'password'             => Hash::make('PasswordB123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'id'               => 302,
            'user_id'          => $this->traineeUserB->id,
            'name'             => $this->traineeUserB->name,
            'email'            => $this->traineeUserB->email,
            'status'           => 'active',
            'age'              => 30,
            'weight'           => 82.0,
            'height'           => 180.0,
            'membership_start' => '2026-02-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
        ]);
    }

    /**
     * السيناريو 1: عرض ملف المتدرب باستخدام معرف صالح (Trainee ID و User ID والمعرف المشفر).
     */
    public function test_view_trainee_using_valid_identifiers(): void
    {
        // 1. استخدام trainee.id
        $response1 = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));
        $response1->assertStatus(200);
        $response1->assertViewHas('result', function ($result) {
            return $result->id === $this->traineeRecordA->id;
        });

        // 2. استخدام user.id
        $response2 = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.show', $this->traineeUserA->id));
        $response2->assertStatus(200);
        $response2->assertViewHas('result', function ($result) {
            return $result->id === $this->traineeRecordA->id;
        });

        // 3. استخدام المعرف المشفر
        $encryptedId = Crypt::encrypt($this->traineeRecordA->id);
        $response3 = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.show', $encryptedId));
        $response3->assertStatus(200);
    }

    /**
     * السيناريو 2: فتح صفحة تعديل المتدرب الصحيح عبر معرف المستخدم أو معرف المتدرب.
     */
    public function test_open_correct_trainee_edit_page(): void
    {
        // فتح التعديل باستخدام user_id (الرابط التقليدي في datatable)
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.edit', $this->traineeUserA->id));

        $response->assertStatus(200);
        $response->assertViewHas('result', function ($result) {
            return $result->id === $this->traineeUserA->id;
        });
        $response->assertViewHas('trainee', function ($trainee) {
            return $trainee->id === $this->traineeRecordA->id;
        });

        // فتح التعديل باستخدام trainee_id أيضاً للتأكد من التوافقية
        $responseTraineeId = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.edit', $this->traineeRecordA->id));
        $responseTraineeId->assertStatus(200);
        $responseTraineeId->assertViewHas('result', function ($result) {
            return $result->id === $this->traineeUserA->id;
        });
    }

    /**
     * السيناريو 3: تحديث المتدرب المقصود دون التأثير على أي سجل آخر.
     */
    public function test_update_intended_trainee_without_affecting_another_record(): void
    {
        $payload = [
            'name'             => 'Alice Updated Name',
            'email'            => 'alice.updated@synthetic-gym-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'weight'           => 67.0,
            'height'           => 170.0,
            'age'              => 26,
            'training_level'   => 'intermediate',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $payload);

        $response->assertStatus(200);

        // التحقق من تحديث المتدرب A
        $this->traineeUserA->refresh();
        $this->traineeRecordA->refresh();
        $this->assertSame('Alice Updated Name', $this->traineeUserA->name);
        $this->assertSame('alice.updated@synthetic-gym-test.local', $this->traineeUserA->email);
        $this->assertSame(26, $this->traineeRecordA->age);
        $this->assertEquals(67.0, (float) $this->traineeRecordA->weight);

        // التحقق من عدم المساس بسجل المتدرب B
        $this->traineeUserB->refresh();
        $this->traineeRecordB->refresh();
        $this->assertSame('Trainee Bob', $this->traineeUserB->name);
        $this->assertSame('bob@synthetic-gym-test.local', $this->traineeUserB->email);
        $this->assertSame(30, $this->traineeRecordB->age);
    }

    /**
     * السيناريو 4: معالجة المعرفات غير الصالحة أو السجلات غير الموجودة (404).
     */
    public function test_handle_invalid_or_nonexistent_identifiers(): void
    {
        // معرف غير موجود لصفحة العرض -> 404
        $responseShow = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.show', 999999));
        $responseShow->assertStatus(404);

        // معرف غير موجود لصفحة التعديل -> 404
        $responseEdit = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.edit', 999999));
        $responseEdit->assertStatus(404);

        // معرف نصي غير صالح
        $responseInvalid = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.show', 'invalid-non-numeric-id'));
        $responseInvalid->assertStatus(404);
    }

    /**
     * السيناريو 5: رفض تعديلات الملف الشخصي غير المصرح بها (المتدرب لا يستطيع تعديل متدرب آخر).
     */
    public function test_reject_unauthorized_profile_updates(): void
    {
        $payload = [
            'name'             => 'Hacked Bob By Alice',
            'email'            => 'hacked.bob@synthetic-gym-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ];

        // المتدربة Alice تحاول تحديث سجل Bob
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordB->id), $payload);

        // إما يتم اعتراضه عبر وسيط الصلاحيات أو فحص الصلاحية الداخلي في الكنترولر (403 أو إعادة التوجيه للمدرب)
        $this->assertTrue(in_array($response->status(), [302, 401, 403], true));

        // التأكد من عدم تعديل بيانات Bob في قاعدة البيانات
        $this->traineeUserB->refresh();
        $this->assertNotSame('Hacked Bob By Alice', $this->traineeUserB->name);
    }

    /**
     * السيناريو 6: السماح بحذف المتدرب بواسطة المستخدم المصرح له (المدرب / المسؤول).
     */
    public function test_allow_authorized_trainee_deletion(): void
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordA->id));

        $this->assertTrue(in_array($response->status(), [200, 302], true));

        // التحقق من الحذف الآمن (Soft Delete) لكل من Trainee و User
        $this->assertSoftDeleted('trainees', ['id' => $this->traineeRecordA->id]);
        $this->assertSoftDeleted('user', ['id' => $this->traineeUserA->id]);
    }

    /**
     * السيناريو 7: رفض الحذف غير المصرح به (المتدرب لا يمكنه حذف نفسه أو متدرب آخر).
     */
    public function test_reject_unauthorized_trainee_deletion(): void
    {
        // المتدرب Bob يحاول حذف Alice
        $response = $this->actingAs($this->traineeUserB, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordA->id));

        $this->assertTrue(in_array($response->status(), [302, 401, 403], true));

        // التأكد من بقاء Alice نشطة وغير محذوفة
        $this->assertDatabaseHas('trainees', [
            'id'         => $this->traineeRecordA->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * السيناريو 8: معالجة حذف سجل غير موجود (Nonexistent Trainee).
     */
    public function test_handle_deletion_of_nonexistent_records(): void
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee.destroy', 888888));

        // يجب ألا يحدث 500 BadMethodCallException أو خطأ غير معالج
        $this->assertTrue(in_array($response->status(), [302, 404, 400], true));
    }

    /**
     * السيناريو 9: الحفاظ على كلمة المرور الحالية عند تحديث الحقول الأخرى بدون إرسال كلمة مرور.
     */
    public function test_preserve_current_password_when_updating_profile_without_password(): void
    {
        $originalHash = $this->traineeUserA->password;

        $payload = [
            'name'             => 'Alice Name Changed',
            'email'            => $this->traineeUserA->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'weight'           => 66.0,
            'training_level'   => 'beginner',
            // password omitted/blank
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $payload);

        $response->assertStatus(200);

        $this->traineeUserA->refresh();
        $this->assertSame($originalHash, $this->traineeUserA->password);
        $this->assertTrue(Hash::check('PasswordA123!', $this->traineeUserA->password));
    }

    /**
     * السيناريو 10: تشفير كلمة المرور الجديدة وتحديثها بشكل سليم عند تقديمها.
     */
    public function test_correctly_hashes_newly_supplied_password(): void
    {
        $newPlainPassword = 'NewSecretPassword2026!';

        $payload = [
            'name'             => $this->traineeUserA->name,
            'email'            => $this->traineeUserA->email,
            'status'           => 'active',
            'password'         => $newPlainPassword,
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $payload);

        $response->assertStatus(200);

        $this->traineeUserA->refresh();
        $this->assertNotSame($newPlainPassword, $this->traineeUserA->password);
        $this->assertTrue(Hash::check($newPlainPassword, $this->traineeUserA->password));
    }

    /**
     * السيناريو 11: الحفاظ على سلوك الحذف الآمن (Soft Delete) وعلاقات النماذج المتسلسلة.
     */
    public function test_preserve_soft_delete_cascades_for_trainee_relations(): void
    {
        $traineeUserC = User::create([
            'id'                   => 203,
            'name'                 => 'Trainee Charlie',
            'email'                => 'charlie@synthetic-gym-test.local',
            'password'             => Hash::make('PasswordC123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $traineeRecordC = Trainee::create([
            'id'               => 303,
            'user_id'          => $traineeUserC->id,
            'name'             => $traineeUserC->name,
            'email'            => $traineeUserC->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ]);

        // إنشاء خطة تدريبية وخطة غذائية وجلسة مصادقة للمتدرب C
        $workoutPlan = WorkoutPlan::create([
            'trainee_id' => $traineeRecordC->id,
            'day_name'   => 'Leg Day',
            'status'     => 'active',
        ]);

        $nutritionPlan = NutritionPlan::create([
            'trainee_id' => $traineeRecordC->id,
            'name'       => 'High Protein Meal Plan',
            'status'     => 'active',
        ]);

        $authSession = AuthSession::create([
            'user_id'      => $traineeUserC->id,
            'access_token' => 'synthetic_token_charlie_123',
            'guard_name'   => 'user',
            'ip'           => '127.0.0.1',
            'user_agent'   => 'PHPUnit Synthetic Browser',
        ]);

        // حذف المتدرب عبر الكنترولر
        $response = $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee.destroy', $traineeRecordC->id));

        $response->assertRedirect(route('system.trainee.index'));

        // التحقق من الحذف الآمن للسجلات التابعة
        $this->assertSoftDeleted('trainees', ['id' => $traineeRecordC->id]);
        $this->assertSoftDeleted('user', ['id' => $traineeUserC->id]);
        $this->assertSoftDeleted('workout_plans', ['id' => $workoutPlan->id]);
        $this->assertDatabaseMissing('nutrition_plans', ['id' => $nutritionPlan->id]);
    }

    /**
     * السيناريو 12: الحفاظ على تدفقات عمل المدربين والمسؤولين المشروعة.
     */
    public function test_maintain_legitimate_coach_and_admin_workflows(): void
    {
        // 1. عرض قائمة المتدربين
        $responseIndex = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee.index'));
        $responseIndex->assertStatus(200);

        // 2. إنشاء متدرب جديد بواسطة المدرب
        $newTraineePayload = [
            'name'             => 'New Synthetic Trainee',
            'email'            => 'new.trainee@synthetic-gym-test.local',
            'status'           => 'active',
            'membership_start' => '2026-03-01',
            'membership_end'   => '2026-09-01',
            'training_level'   => 'beginner',
            'age'              => 22,
            'weight'           => 70.0,
            'height'           => 175.0,
        ];

        $responseStore = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee.store'), $newTraineePayload);
        $responseStore->assertStatus(200);

        $this->assertDatabaseHas('user', [
            'email'     => 'new.trainee@synthetic-gym-test.local',
            'user_type' => 2,
        ]);
        $this->assertDatabaseHas('trainees', [
            'email' => 'new.trainee@synthetic-gym-test.local',
        ]);
    }

    /**
     * السيناريو 13: الحفاظ على حماية الصلاحيات التي تم تثبيتها في المهمة الثانية Task 2.
     */
    public function test_preserves_task2_authorization_protections(): void
    {
        // التأكد من أن المتدرب لا يستطيع الوصول المباشر لمعرفات غير تابعة له في مسارات النظام
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordB->id));

        // يمنع وصول المتدرب إلى ملف متدرب آخر
        $this->assertTrue(in_array($response->status(), [302, 401, 403], true));
    }
}
