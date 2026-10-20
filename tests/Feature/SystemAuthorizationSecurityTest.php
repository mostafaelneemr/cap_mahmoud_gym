<?php

namespace Tests\Feature;

use App\Models\NutritionPlan;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemAuthorizationSecurityTest extends TestCase
{
    protected User $adminUser;
    protected User $staffUserNoPerms;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;
    protected User $onlineTraineeUser;
    protected Trainee $onlineTraineeRecord;
    protected int $adminGroupId;
    protected int $noPermsGroupId;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. التحقق من سلامة بيئة الاختبار المعزولة في الذاكرة SQLite
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. بناء المخطط المعزول
        $this->createIsolatedSchema();

        // 3. عزل التخزين
        Storage::fake('public');

        // 4. تهيئة البيانات الاصطناعية
        $this->seedSyntheticSecurityData();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    protected function createIsolatedSchema(): void
    {
        Schema::dropIfExists('nutrition_plans');
        Schema::dropIfExists('trainee_progress_photos');
        Schema::dropIfExists('trainee_progress_checkins');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('user');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('setting');

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
            $table->tinyInteger('user_type')->nullable(); // 1: staff/moderator, 2: trainee, 3: online trainee
            $table->string('default_language')->default('en-gb');
            $table->boolean('force_reset_password')->default(false);
            $table->string('mobile')->nullable();
            $table->string('two_fa_secret')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('email')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
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

        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('value')->nullable();
            $table->string('group_name')->nullable();
            $table->string('shown_name_ar')->nullable();
            $table->string('shown_name_en')->nullable();
            $table->string('input_type')->default('text');
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    protected function seedSyntheticSecurityData(): void
    {
        // 1. مجموعة المشرف العام ذات الصلاحيات الكاملة
        $this->adminGroupId = DB::table('permission_groups')->insertGetId([
            'name'          => 'Super Admin',
            'is_supervisor' => 'yes',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $fullPermissions = [
            'system.dashboard',
            'system.user.index',
            'system.user.create',
            'system.user.store',
            'system.user.show',
            'system.user.edit',
            'system.user.update',
            'system.trainee.index',
            'system.trainee.create',
            'system.trainee.store',
            'system.trainee.show',
            'system.trainee.edit',
            'system.trainee.update',
            'system.trainee.destroy',
            'system.workout.index',
            'system.workout.create',
            'system.workout.store',
            'system.nutrition.index',
            'system.nutrition.create',
            'system.nutrition.store',
            'system.nutrition.show',
            'system.nutrition.my-plan',
            'system.setting.index',
            'system.setting.update',
        ];

        foreach ($fullPermissions as $perm) {
            DB::table('permissions')->insert([
                'permission_group_id' => $this->adminGroupId,
                'route_name'          => $perm,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // 2. مجموعة بدون صلاحيات
        $this->noPermsGroupId = DB::table('permission_groups')->insertGetId([
            'name'          => 'Restricted Staff',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // فقط لوحة التحكم الأساسية
        DB::table('permissions')->insert([
            'permission_group_id' => $this->noPermsGroupId,
            'route_name'          => 'system.dashboard',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // 3. مستخدم مشرف عام
        $this->adminUser = User::create([
            'id'                   => 1,
            'name'                 => 'Captain Mahmoud',
            'email'                => 'admin@gym.local',
            'password'             => Hash::make('AdminSecret123!'),
            'status'               => '1',
            'permission_group_id'  => $this->adminGroupId,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // 4. موظف محدود الصلاحيات
        $this->staffUserNoPerms = User::create([
            'id'                   => 2,
            'name'                 => 'Staff Member',
            'email'                => 'staff@gym.local',
            'password'             => Hash::make('StaffSecret123!'),
            'status'               => '1',
            'permission_group_id'  => $this->noPermsGroupId,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // 5. متدرب A (user_type = 2)
        $this->traineeUserA = User::create([
            'id'                   => 10,
            'name'                 => 'Trainee Alpha',
            'email'                => 'alpha@gym.local',
            'password'             => Hash::make('AlphaSecret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'id'         => 100,
            'user_id'    => $this->traineeUserA->id,
            'email'      => $this->traineeUserA->email,
            'status'     => 'active',
        ]);

        NutritionPlan::create([
            'id'          => 501,
            'trainee_id'  => $this->traineeRecordA->id,
            'name'        => 'Alpha Muscle Gain Diet',
            'description' => 'High protein 2800 kcal',
            'status'      => 'active',
        ]);

        // 6. متدرب B (user_type = 2)
        $this->traineeUserB = User::create([
            'id'                   => 20,
            'name'                 => 'Trainee Beta',
            'email'                => 'beta@gym.local',
            'password'             => Hash::make('BetaSecret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'id'         => 200,
            'user_id'    => $this->traineeUserB->id,
            'email'      => $this->traineeUserB->email,
            'status'     => 'active',
        ]);

        NutritionPlan::create([
            'id'          => 502,
            'trainee_id'  => $this->traineeRecordB->id,
            'name'        => 'Beta Fat Loss Diet',
            'description' => 'Low carb 1800 kcal',
            'status'      => 'active',
        ]);

        // 7. متدرب أونلاين (user_type = 3)
        $this->onlineTraineeUser = User::create([
            'id'                   => 30,
            'name'                 => 'Trainee Gamma Online',
            'email'                => 'gamma@gym.local',
            'password'             => Hash::make('GammaSecret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->onlineTraineeRecord = Trainee::create([
            'id'         => 300,
            'user_id'    => $this->onlineTraineeUser->id,
            'email'      => $this->onlineTraineeUser->email,
            'status'     => 'active',
        ]);
    }

    /**
     * اختبار: رفض الطلبات غير المصادق عليها وإعادة التوجيه لصفحة الدخول.
     */
    public function test_unauthenticated_requests_to_system_routes_are_redirected_to_login(): void
    {
        $protectedRoutes = [
            '/system',
            '/system/user',
            '/system/workout',
            '/system/nutrition',
            '/system/trainee',
            '/system/setting',
            '/system/permission-group',
        ];

        foreach ($protectedRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertRedirect('/system/login');
        }
    }

    /**
     * اختبار: منع الوصول إلى صفحة إنشاء التمارين لمن لا يملك الصلاحية (علاج ثغرة ignoredRoutes).
     */
    public function test_unauthorized_staff_user_cannot_access_workout_creation_page(): void
    {
        $response = $this->actingAs($this->staffUserNoPerms, 'user')
            ->get('/system/workout/create');

        // يجب رفض الوصول بكود 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * اختبار: منع الموظف غير المصرح من الوصول إلى إدارة المستخدمين والإعدادات.
     */
    public function test_unauthorized_user_cannot_access_administrative_modules_without_permissions(): void
    {
        $response = $this->actingAs($this->staffUserNoPerms, 'user')
            ->get('/system/user');
        $response->assertStatus(401);

        $responseSetting = $this->actingAs($this->staffUserNoPerms, 'user')
            ->get('/system/setting');
        $responseSetting->assertStatus(401);
    }

    /**
     * اختبار: المتدرب يعاد توجيهه تلقائياً إلى لوحة تحكم المتدربين عند محاولة فتح روابط الإدارة.
     */
    public function test_trainee_is_redirected_away_from_administrative_routes(): void
    {
        // تجربة المتدرب العادي (user_type = 2)
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get('/system/user');
        $response->assertRedirect(route('system.dashboard.trainer'));

        $responseWorkout = $this->actingAs($this->traineeUserA, 'user')
            ->get('/system/workout');
        $responseWorkout->assertRedirect(route('system.dashboard.trainer'));

        // تجربة المتدرب الأونلاين (user_type = 3)
        $responseOnline = $this->actingAs($this->onlineTraineeUser, 'user')
            ->get('/system/user');
        $responseOnline->assertRedirect(route('system.dashboard.trainer'));
    }

    /**
     * اختبار: المتدرب يستطيع عرض خطته الغذائية الخاصة.
     */
    public function test_trainee_can_view_own_nutrition_plan(): void
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.nutrition.my-plan'));

        $response->assertStatus(200);
        $this->assertEquals($this->traineeRecordA->id, $response->viewData('trainee')->id);
        $this->assertEquals('Alpha Muscle Gain Diet', $response->viewData('plan')->name);
    }

    /**
     * اختبار أمني حرج: منع ثغرة IDOR على الخطة الغذائية باستخدام المعرف الرقمي الخام (trainee_id).
     */
    public function test_trainee_cannot_view_another_trainees_nutrition_plan_via_raw_id_idor(): void
    {
        // المتدرب A يحاول طلب خطة المتدرب B عبر trainee_id
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.nutrition.my-plan', ['trainee_id' => $this->traineeRecordB->id]));

        // يجب رفض الطلب بكود 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * اختبار أمني حرج: منع ثغرة IDOR على الخطة الغذائية باستخدام المعرف المشفر (trainee).
     */
    public function test_trainee_cannot_view_another_trainees_nutrition_plan_via_encrypted_id_idor(): void
    {
        $encryptedTargetId = Crypt::encrypt($this->traineeRecordB->id);

        // المتدرب A يحاول طلب خطة المتدرب B عبر المعرف المشفر
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.nutrition.my-plan', ['trainee' => $encryptedTargetId]));

        // يجب رفض الطلب بكود 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * اختبار: المدرب المصرح يستطيع استعراض الخطة الغذائية لأي متدرب.
     */
    public function test_coach_can_view_any_trainees_nutrition_plan(): void
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.nutrition.my-plan', ['trainee_id' => $this->traineeRecordB->id]));

        $response->assertStatus(200);
        $this->assertEquals($this->traineeRecordB->id, $response->viewData('trainee')->id);
        $this->assertEquals('Beta Fat Loss Diet', $response->viewData('plan')->name);
    }

    /**
     * اختبار أمني: منع تصعيد الصلاحيات (Privilege Escalation) عبر تعديل الملف الشخصي (Mass Assignment).
     */
    public function test_profile_update_prevents_privilege_escalation_via_mass_assignment(): void
    {
        $targetUser = $this->staffUserNoPerms;
        $originalGroupId = $targetUser->permission_group_id;
        $originalUserType = $targetUser->user_type;

        // محاولة تصعيد الصلاحيات بإرسال permission_group_id المشرف و user_type
        $response = $this->actingAs($targetUser, 'user')
            ->patch('/system/user/update-profile', [
                'email'                => 'updated-staff@gym.local',
                'default_language'     => 'ar',
                'permission_group_id'  => $this->adminGroupId, // محاولة خبيثة
                'user_type'            => 1,
                'status'               => '1',
            ]);

        $response->assertStatus(200);

        // التحقق من أن مجموعة الصلاحيات لم تتغير ولم يتم استغلال الحقل
        $refreshed = User::find($targetUser->id);
        $this->assertSame($originalGroupId, $refreshed->permission_group_id);
        $this->assertSame($originalUserType, (int) $refreshed->user_type);
        $this->assertSame('updated-staff@gym.local', $refreshed->email);
        $this->assertSame('ar', $refreshed->default_language);
    }

    /**
     * اختبار أمني: منع المتدربين من تنفيذ عمليات الحذف المدمرة للمستخدمين أو المتدربين.
     */
    public function test_trainee_cannot_delete_trainees_or_users(): void
    {
        // المتدرب A يحاول حذف المتدرب B
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->delete('/system/trainee/' . $this->traineeRecordB->id);

        // يعاد توجيهه إلى لوحة التحكم أو يُمنع 403
        $this->assertTrue(in_array($response->status(), [302, 403], true));
        $this->assertDatabaseHas('trainees', [
            'id'         => $this->traineeRecordB->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * اختبار أمني: منع المتدربين من تصفير أو استعادة البرامج التدريبية.
     */
    public function test_trainee_cannot_reset_or_restore_workout_plans(): void
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post('/system/trainee/' . $this->traineeRecordB->id . '/reset-plan');

        $this->assertTrue(in_array($response->status(), [302, 403], true));
    }

    /**
     * اختبار: دالة userCan تدعم كلا النوعين من المتدربين وتعزل ذاكرة التخزين المؤقت لكل مستخدم.
     */
    public function test_user_can_helper_respects_trainee_roles_and_cache_isolation(): void
    {
        // للمتدرب العادي (user_type = 2)
        $this->assertTrue(userCan('system.nutrition.my-plan', $this->traineeUserA->id));
        $this->assertTrue(userCan('system.trainee-progress.index', $this->traineeUserA->id));
        $this->assertTrue(userCan('system.impersonate.leave', $this->traineeUserA->id));
        $this->assertFalse(userCan('system.workout.create', $this->traineeUserA->id));
        $this->assertFalse(userCan('system.user.index', $this->traineeUserA->id));

        // للمتدرب الأونلاين (user_type = 3)
        $this->assertTrue(userCan('system.nutrition.my-plan', $this->onlineTraineeUser->id));
        $this->assertTrue(userCan('system.trainee-progress.index', $this->onlineTraineeUser->id));
        $this->assertTrue(userCan('system.impersonate.leave', $this->onlineTraineeUser->id));
        $this->assertFalse(userCan('system.workout.create', $this->onlineTraineeUser->id));
        $this->assertFalse(userCan('system.user.index', $this->onlineTraineeUser->id));

        // للموظف محدود الصلاحيات: مسار إنشاء التمارين لم يعد مستثنى
        $this->assertFalse(userCan('system.workout.create', $this->staffUserNoPerms->id));

        // للمشرف العام: يملك الصلاحية
        $this->assertTrue(userCan('system.workout.create', $this->adminUser->id));
    }
}
