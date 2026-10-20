<?php

namespace Tests\Feature;

use App\Models\AuthSession;
use App\Models\Exercise;
use App\Models\NutritionPlan;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TraineeWorkflowIntegrationTest extends TestCase
{
    protected User $adminUser;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;
    protected int $adminGroupId;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. التحقق الصارم من عزل البيئة في SQLite in-memory
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. تشغيل كافة الترحيلات في بيئة الاختبار النظيفة
        Artisan::call('migrate');
        Cache::flush();

        // 3. بذر البيانات الاصطناعية للاختبار
        $this->seedIntegrationData();
    }

    protected function tearDown(): void
    {
        Artisan::call('migrate:rollback');
        Cache::flush();
        parent::tearDown();
    }

    protected function seedIntegrationData(): void
    {
        // 1. مجموعة صلاحيات المشرفين والإدارة
        $this->adminGroupId = DB::table('permission_groups')->insertGetId([
            'name'          => 'Admin Supervisor',
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
            'system.trainee-progress.index',
            'system.trainee-progress.store',
            'system.trainee-progress.update-notes',
            'system.trainee-progress.destroy',
            'system.trainee-progress.destroy-photo',
            'system.trainee-progress.compare',
        ];

        foreach ($adminRoutes as $route) {
            DB::table('permissions')->insert([
                'permission_group_id' => $this->adminGroupId,
                'route_name'          => $route,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // 2. مجموعة صلاحيات المتدربين (125)
        DB::table('permission_groups')->insert([
            'id'            => 125,
            'name'          => 'Trainees Group',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 3. إنشاء حساب المسؤول / المدرب
        $this->adminUser = User::create([
            'id'                   => 101,
            'name'                 => 'Coach Mahmoud',
            'email'                => 'coach@workflow-test.local',
            'password'             => Hash::make('AdminPass123!'),
            'status'               => '1',
            'permission_group_id'  => $this->adminGroupId,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // 4. إنشاء المتدرب A (user_type = 2)
        $this->traineeUserA = User::create([
            'id'                   => 201,
            'name'                 => 'Trainee Alice',
            'email'                => 'alice@workflow-test.local',
            'password'             => Hash::make('AlicePass123!'),
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
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'weight'           => 65.0,
            'height'           => 168.0,
            'age'              => 25,
            'training_level'   => 'intermediate',
        ]);

        // 5. إنشاء المتدرب B (user_type = 3 للتأكد من تغطية كلا النوعين)
        $this->traineeUserB = User::create([
            'id'                   => 202,
            'name'                 => 'Trainee Bob',
            'email'                => 'bob@workflow-test.local',
            'password'             => Hash::make('BobPass123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'id'               => 302,
            'user_id'          => $this->traineeUserB->id,
            'name'             => $this->traineeUserB->name,
            'email'            => $this->traineeUserB->email,
            'status'           => 'active',
            'membership_start' => '2026-02-01',
            'membership_end'   => '2026-11-30',
            'weight'           => 85.0,
            'height'           => 180.0,
            'age'              => 30,
            'training_level'   => 'advanced',
        ]);
    }

    /**
     * 1. التحقق من نجاح إنشاء المتدرب وإنشاء وربط حساب المستخدم المقابل له بدقة.
     */
    public function test_trainee_creation_creates_and_maps_user_account_correctly(): void
    {
        $payload = [
            'name'             => 'New Synthetic Client',
            'email'            => 'client.new@workflow-test.local',
            'status'           => 'active',
            'membership_start' => '2026-03-01',
            'membership_end'   => '2026-09-01',
            'training_level'   => 'beginner',
            'weight'           => 72.5,
            'height'           => 175.0,
            'age'              => 24,
            'telephone'        => '01012345678',
            'telephone_code'   => '+20',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee.store'), $payload);

        $response->assertStatus(200);

        // التحقق من وجود حساب المستخدم
        $createdUser = User::where('email', 'client.new@workflow-test.local')->first();
        $this->assertNotNull($createdUser);
        $this->assertSame(2, (int) $createdUser->user_type);
        $this->assertSame(125, (int) $createdUser->permission_group_id);
        $this->assertTrue(Hash::check('123456', $createdUser->password));

        // التحقق من وجود سجل المتدرب وربطه بالمستخدم
        $createdTrainee = Trainee::where('email', 'client.new@workflow-test.local')->first();
        $this->assertNotNull($createdTrainee);
        $this->assertSame((int) $createdUser->id, (int) $createdTrainee->user_id);
        $this->assertSame('New Synthetic Client', $createdTrainee->name);
        $this->assertEquals(72.5, (float) $createdTrainee->weight);

        // التأكد من أن معرف المتدرب يختلف عن معرف المستخدم
        $this->assertTrue($createdUser->trainer->is($createdTrainee));
        $this->assertTrue($createdTrainee->user->is($createdUser));
    }

    /**
     * 2. التحقق من أن فشل التحقق في إنشاء المتدرب لا يترك أي سجلات جزئية يتيمة.
     */
    public function test_trainee_creation_validation_prevents_partial_record_creation(): void
    {
        $invalidPayload = [
            'name'             => 'Failing Client',
            'email'            => 'failing.client@workflow-test.local',
            'status'           => 'active',
            // تاريخ انتهاء قبل تاريخ البدء لإفشال التحقق
            'membership_start' => '2026-06-01',
            'membership_end'   => '2026-01-01',
            'training_level'   => 'beginner',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee.store'), $invalidPayload);

        $response->assertSessionHasErrors(['membership_end']);

        // التأكد من عدم إنشاء أي مستخدم أو متدرب
        $this->assertDatabaseMissing('user', ['email' => 'failing.client@workflow-test.local']);
        $this->assertDatabaseMissing('trainees', ['email' => 'failing.client@workflow-test.local']);
    }

    /**
     * 3. التحقق من التراجع عن المعاملة (Transaction Rollback) عند حدوث خطأ غير متوقع في طبقة الحفظ.
     */
    public function test_transaction_rollback_cleans_up_on_service_failure(): void
    {
        // محاكاة إلقاء استثناء أثناء إنشاء سجل المتدرب داخل المعاملة
        Trainee::saving(function () {
            throw new \RuntimeException('Simulated catastrophic database crash');
        });

        $payload = [
            'name'             => 'Crash Test User',
            'email'            => 'crashtest@workflow-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee.store'), $payload);

        // يجب أن يرجع المتحكم استجابة فشل fail()
        $this->assertSame(400, $response->getStatusCode());

        // التأكد من أن التراجع Rollback منع بقاء حساب المستخدم المنشأ في الخطوة الأولى
        $this->assertDatabaseMissing('user', ['email' => 'crashtest@workflow-test.local']);
        $this->assertDatabaseMissing('trainees', ['email' => 'crashtest@workflow-test.local']);
    }

    /**
     * 4. التحقق من الحفاظ على تجزئة كلمة المرور الحالية عند تحديث بيانات المتدرب بدون كلمة مرور.
     */
    public function test_trainee_update_preserves_existing_password_when_omitted(): void
    {
        $originalHash = $this->traineeUserA->password;

        $payload = [
            'name'             => 'Alice Name Modified',
            'email'            => $this->traineeUserA->email,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
            'weight'           => 64.0,
            // حقل password فارغ أو غير موجود
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $payload);

        $response->assertStatus(200);

        $this->traineeUserA->refresh();
        $this->assertSame($originalHash, $this->traineeUserA->password);
        $this->assertSame('Alice Name Modified', $this->traineeUserA->name);
    }

    /**
     * 5. التحقق من تشفير وتجزئة كلمة المرور الجديدة عند تزويدها في التحديث.
     */
    public function test_trainee_update_hashes_new_password_correctly(): void
    {
        $newPlainPassword = 'BrandNewPassword2026!';

        $payload = [
            'name'             => $this->traineeUserA->name,
            'email'            => $this->traineeUserA->email,
            'password'         => $newPlainPassword,
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
        ];

        $response = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $payload);

        $response->assertStatus(200);

        $this->traineeUserA->refresh();
        $this->assertNotSame($newPlainPassword, $this->traineeUserA->password);
        $this->assertTrue(Hash::check($newPlainPassword, $this->traineeUserA->password));
    }

    /**
     * 6. التحقق من قاعدة فرادة البريد الإلكتروني (السماح بنفس البريد للمستخدم الحالي ورفض التكرار).
     */
    public function test_trainee_email_uniqueness_allows_self_update_but_rejects_collision(): void
    {
        // أ. تحديث المتدرب A بنفس بريده الحالي (يجب أن ينجح بفضل قاعدة ignore)
        $selfPayload = [
            'name'             => 'Alice Same Email',
            'email'            => 'alice@workflow-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
        ];

        $responseSelf = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $selfPayload);
        $responseSelf->assertStatus(200);

        // ب. محاولة تحديث المتدرب A ببريد المتدرب B (يجب أن يفشل في التحقق)
        $collisionPayload = [
            'name'             => 'Alice Conflict Email',
            'email'            => 'bob@workflow-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'intermediate',
        ];

        $responseCollision = $this->actingAs($this->adminUser, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $collisionPayload);
        $responseCollision->assertSessionHasErrors(['email']);
    }

    /**
     * 7. التحقق من منع المتدربين من تعديل حسابات متدربين آخرين (لكلا النوعين user_type 2 و 3).
     */
    public function test_unauthorized_cross_trainee_update_rejected_for_both_trainee_types(): void
    {
        $maliciousPayload = [
            'name'             => 'Hacked Bob By Alice',
            'email'            => 'hacked.bob@workflow-test.local',
            'status'           => 'active',
            'membership_start' => '2026-01-01',
            'membership_end'   => '2026-12-31',
            'training_level'   => 'beginner',
        ];

        // المتدربة Alice (user_type = 2) تحاول تعديل المتدرب Bob
        $responseA = $this->actingAs($this->traineeUserA, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordB->id), $maliciousPayload);

        // يتم توجيهها للوحة تحكم المتدرب أو رفضها (403/Redirect)
        $this->traineeUserB->refresh();
        $this->assertSame('Trainee Bob', $this->traineeUserB->name);

        // المتدرب Bob (user_type = 3) يحاول تعديل المتدربة Alice
        $responseB = $this->actingAs($this->traineeUserB, 'user')
            ->put(route('system.trainee.update', $this->traineeRecordA->id), $maliciousPayload);

        $this->traineeUserA->refresh();
        $this->assertNotSame('Hacked Bob By Alice', $this->traineeUserA->name);
    }

    /**
     * 8. التحقق من منع المتدربين والضيوف من حذف أي متدرب.
     */
    public function test_unauthorized_trainee_deletion_rejected_for_trainees_and_guests(): void
    {
        // محاولة حذف من ضيف غير مسجل (Guest)
        $responseGuest = $this->delete(route('system.trainee.destroy', $this->traineeRecordB->id));
        $responseGuest->assertRedirect('/system/login');

        // محاولة حذف من متدرب
        $responseTrainee = $this->actingAs($this->traineeUserA, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordB->id));

        $this->assertDatabaseHas('trainees', ['id' => $this->traineeRecordB->id, 'deleted_at' => null]);
    }

    /**
     * 9. التحقق من الحذف المصرح به: حذف ناعم للمتدرب والمستخدم والخطط وإلغاء الجلسات النشطة.
     */
    public function test_authorized_deletion_soft_deletes_trainee_and_user_and_revokes_sessions(): void
    {
        // إنشاء خطة تدريبية وجلسة مصادقة للمتدرب A
        $workoutPlan = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecordA->id,
            'day_name'   => 'Upper Body',
            'status'     => 'active',
        ]);

        $exercise = Exercise::create([
            'workout_plan_id' => $workoutPlan->id,
            'name'            => 'Pull-up',
            'type'            => 'main',
        ]);

        $authSession = AuthSession::create([
            'user_id'      => $this->traineeUserA->id,
            'access_token' => 'active_session_token_alice_999',
            'guard_name'   => 'user',
            'ip'           => '127.0.0.1',
            'user_agent'   => 'Test Browser',
        ]);

        // حذف المتدرب بواسطة المسؤول
        $response = $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordA->id));

        $response->assertRedirect(route('system.trainee.index'));

        // التحقق من الحذف الناعم
        $this->assertSoftDeleted('trainees', ['id' => $this->traineeRecordA->id]);
        $this->assertSoftDeleted('user', ['id' => $this->traineeUserA->id]);
        $this->assertSoftDeleted('workout_plans', ['id' => $workoutPlan->id]);
        $this->assertSoftDeleted('exercises', ['id' => $exercise->id]);

        // التحقق من إلغاء وحذف جلسة الدخول النشطة لضمان عدم استمرار تسجيل الدخول
        $this->assertDatabaseMissing('auth_session', ['id' => $authSession->id]);
    }

    /**
     * 10. التحقق من الاحتفاظ بسجلات المتابعة التاريخية (Check-ins) عند الحذف الناعم للمتدرب.
     */
    public function test_soft_delete_retains_historical_workout_logs_and_checkins(): void
    {
        // إنشاء سجل متابعة للمتدرب B
        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-02-15',
            'weight'       => 84.5,
            'notes'        => 'Historical checkin data to preserve',
        ]);

        $photo = TraineeProgressPhoto::create([
            'checkin_id' => $checkin->id,
            'photo_path' => 'photos/b_checkin_history.jpg',
            'caption'    => 'Progress 1',
        ]);

        // حذف المتدرب B
        $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordB->id));

        // التحقق من بقاء السجل التاريخي دون حذف فيزيائي
        $this->assertDatabaseHas('trainee_progress_checkins', ['id' => $checkin->id]);
        $this->assertDatabaseHas('trainee_progress_photos', ['id' => $photo->id]);
    }

    /**
     * 11. التحقق من منع الوصول غير المصرح به لتسجيلات التقدم والصور للمتدربين الآخرين.
     */
    public function test_progress_tracking_authorization_prevents_cross_trainee_access(): void
    {
        // محاولة المتدربة Alice عرض صور وقياسات المتدرب Bob عبر تمرير trainee_id
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.index', ['trainee_id' => $this->traineeRecordB->id]));

        $response->assertStatus(403);

        // محاولة المتدربة Alice تمرير المعرف المشفر للمتدرب Bob
        $encryptedBob = Crypt::encrypt($this->traineeRecordB->id);
        $responseEncrypted = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.index', ['trainee' => $encryptedBob]));

        $responseEncrypted->assertStatus(403);

        // محاولة المتدربة Alice مقارنة صور المتدرب Bob
        $responseCompare = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.compare', [
                'trainee_id' => $this->traineeRecordB->id,
                'date1'      => '2026-01-01',
                'date2'      => '2026-02-01',
            ]));

        $responseCompare->assertStatus(403);
    }

    /**
     * 12. التحقق من سلوك وسيط الصلاحيات (Middleware) والمسارات ومطابقة الصلاحيات.
     */
    public function test_permission_middleware_and_route_mapping_integrity(): void
    {
        // إنشاء مجموعة مدربين تفتقر لصلاحية delete-trainee
        $restrictedGroupId = DB::table('permission_groups')->insertGetId([
            'name'          => 'Restricted Coaches',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // إعطاء صلاحيات العرض فقط بدون الحذف
        DB::table('permissions')->insert([
            'permission_group_id' => $restrictedGroupId,
            'route_name'          => 'system.trainee.index',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        $restrictedCoach = User::create([
            'name'                 => 'Restricted Coach',
            'email'                => 'restricted.coach@workflow-test.local',
            'password'             => Hash::make('Pass123!'),
            'status'               => '1',
            'permission_group_id'  => $restrictedGroupId,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // المدرب المقيد يستطيع عرض القائمة
        $responseIndex = $this->actingAs($restrictedCoach, 'user')
            ->get(route('system.trainee.index'));
        $responseIndex->assertStatus(200);

        // ولكن عند محاولة الحذف يتم رفضه (401 Unauthorized)
        $responseDelete = $this->actingAs($restrictedCoach, 'user')
            ->delete(route('system.trainee.destroy', $this->traineeRecordA->id));

        $responseDelete->assertStatus(401);
    }
}
