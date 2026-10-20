<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoadAdjustmentTest extends TestCase
{
    protected User $adminCoach;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;
    protected WorkoutPlan $planA;
    protected Exercise $exerciseA1;
    protected Exercise $exerciseA2;
    protected WorkoutPlan $planB;
    protected Exercise $exerciseB1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. التحقق من عزل بيئة الاختبار في SQLite in-memory
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. تشغيل الترحيلات ومسح الذاكرة المؤقتة
        Artisan::call('migrate');
        Cache::flush();

        // 3. بذر بيانات الاختبار
        $this->seedTestData();
    }

    protected function tearDown(): void
    {
        Artisan::call('migrate:rollback');
        Cache::flush();
        parent::tearDown();
    }

    protected function seedTestData(): void
    {
        // إنشاء مجموعة صلاحيات المشرفين
        $adminGroupId = DB::table('permission_groups')->insertGetId([
            'name'          => 'Admin Coaches',
            'is_supervisor' => 'yes',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('permissions')->insert([
            ['permission_group_id' => $adminGroupId, 'route_name' => 'system.trainee.show', 'created_at' => now(), 'updated_at' => now()],
            ['permission_group_id' => $adminGroupId, 'route_name' => 'system.workout.updateDay', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->adminCoach = User::create([
            'name'                 => 'Coach Mahmoud',
            'email'                => 'coach@fitness.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => $adminGroupId,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // المتدرب الأول A
        $this->traineeUserA = User::create([
            'name'                 => 'Trainee Alice',
            'email'                => 'alice@fitness.local',
            'password'             => Hash::make('TraineePass!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'user_id'          => $this->traineeUserA->id,
            'name'             => 'Trainee Alice',
            'email'            => 'alice@fitness.local',
            'age'              => 25,
            'weight'           => 65.0,
            'height'           => 170.0,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(6)->toDateString(),
            'training_level'   => 'intermediate',
            'status'           => 'active',
        ]);

        // المتدرب الثاني B
        $this->traineeUserB = User::create([
            'name'                 => 'Trainee Bob',
            'email'                => 'bob@fitness.local',
            'password'             => Hash::make('TraineePass!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'user_id'          => $this->traineeUserB->id,
            'name'             => 'Trainee Bob',
            'email'            => 'bob@fitness.local',
            'age'              => 30,
            'weight'           => 82.0,
            'height'           => 180.0,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(3)->toDateString(),
            'training_level'   => 'advanced',
            'status'           => 'active',
        ]);

        // إنشاء خطة تدريبية وتمارين للمتدربة Alice
        $this->planA = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecordA->id,
            'day_name'   => 'Day 1 - Chest & Arms',
            'status'     => 'active',
        ]);

        $this->exerciseA1 = Exercise::create([
            'workout_plan_id' => $this->planA->id,
            'type'            => 'main',
            'name'            => 'Barbell Bench Press',
            'sets'            => 4,
            'reps'            => '8-10',
            'rest'            => '90',
            'internal_weight' => ['20kg', '25kg'],
            'tempo'           => '2-0-2',
        ]);

        $this->exerciseA2 = Exercise::create([
            'workout_plan_id' => $this->planA->id,
            'type'            => 'main',
            'name'            => 'Incline Dumbbell Press',
            'sets'            => 3,
            'reps'            => '10',
            'rest'            => '60',
            'internal_weight' => '15kg',
        ]);

        // خطة وتمارين للمتدرب Bob
        $this->planB = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecordB->id,
            'day_name'   => 'Day 1 - Leg Day',
            'status'     => 'active',
        ]);

        $this->exerciseB1 = Exercise::create([
            'workout_plan_id' => $this->planB->id,
            'type'            => 'main',
            'name'            => 'Barbell Squat',
            'sets'            => 4,
            'reps'            => '6-8',
            'internal_weight' => '80kg',
        ]);
    }

    /**
     * 1. التحقق من قدرة المتدرب المصادق على تقديم تقرير تعديل حمل صالح لتمرين في خطته.
     */
    public function test_authenticated_trainee_can_submit_valid_load_adjustment_report(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '30kg',
            'notes'         => 'Felt easy on last 2 sets, increased from 25kg.',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Load adjustment report submitted successfully to your coach!',
            ]);

        $this->assertDatabaseHas('trainee_load_adjustments', [
            'trainee_id'    => $this->traineeRecordA->id,
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '30kg',
            'status'        => 'pending',
        ]);
    }

    /**
     * 2. التحقق من حفظ سياق التمرين واللقطات التاريخية بدقة (Snapshots).
     */
    public function test_report_stores_correct_context_snapshots_and_reported_load(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '27.5kg',
            'notes'         => 'Test notes for snapshots',
        ];

        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $payload);

        $report = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)->latest()->first();
        $this->assertNotNull($report);
        $this->assertSame('Barbell Bench Press', $report->exercise_name);
        $this->assertSame('Day 1 - Chest & Arms', $report->workout_day_name);
        $this->assertSame('20kg / 25kg', $report->prescribed_load);
        $this->assertSame('27.5kg', $report->reported_load);
        $this->assertSame('Trainee Alice', $report->trainee_name_snapshot);
        $this->assertSame('Test notes for snapshots', $report->notes);
        $this->assertSame('pending', $report->status);
        $this->assertNull($report->coach_notes);
        $this->assertNull($report->reviewed_by);
        $this->assertNull($report->reviewed_at);
    }

    /**
     * 3. القاعدة الجوهرية الإلزامية: التحقق الصارم من أن إرسال التقرير لا يغير إطلاقاً الأوزان المقررة في الخطة.
     */
    public function test_submitting_report_never_modifies_prescribed_load_or_workout_plan(): void
    {
        $originalPrescribedWeight = $this->exerciseA1->internal_weight;
        $originalDisplayWeight = $this->exerciseA1->internal_weight_display;
        $originalUpdatedAt = $this->exerciseA1->updated_at;

        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '50kg',
            'notes'         => 'Trying heavy weight.',
        ];

        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $payload);

        // إعادة قراءة التمرين من قاعدة البيانات والتأكد من عدم تغييره
        $this->exerciseA1->refresh();

        $this->assertSame($originalPrescribedWeight, $this->exerciseA1->internal_weight);
        $this->assertSame($originalDisplayWeight, $this->exerciseA1->internal_weight_display);
        $this->assertSame(4, $this->exerciseA1->sets);
        $this->assertSame('8-10', $this->exerciseA1->reps);

        // التأكد أيضاً من عدم تعديل اليوم التدريبي
        $this->planA->refresh();
        $this->assertSame('active', $this->planA->status);
        $this->assertSame('Day 1 - Chest & Arms', $this->planA->day_name);
    }

    /**
     * 4. التحقق من منع ثغرة IDOR: المتدرب لا يمكنه التقديم نيابة عن متدرب آخر حتى مع تمرير trainee_id.
     */
    public function test_trainee_cannot_submit_report_for_another_trainee_via_idor(): void
    {
        // المتدربة Alice تحاول تمرير معرف المتدرب Bob
        $maliciousPayload = [
            'trainee_id'    => $this->traineeRecordB->id,
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '100kg',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $maliciousPayload);

        // يُرفض من قبل FormRequest برمز 403
        $response->assertStatus(403);

        $this->assertDatabaseMissing('trainee_load_adjustments', [
            'reported_load' => '100kg',
        ]);
    }

    /**
     * 5. التحقق من منع المتدرب من تقديم تقرير لتمرين لا ينتمي لخطته التدريبية.
     */
    public function test_trainee_cannot_submit_report_for_unrelated_or_foreign_exercise(): void
    {
        // المتدربة Alice تحاول تقديم تقرير لتمرين السكوات التابع للمتدرب Bob
        $payload = [
            'exercise_id'   => $this->exerciseB1->id,
            'reported_load' => '90kg',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('trainee_load_adjustments', [
            'exercise_id'   => $this->exerciseB1->id,
            'reported_load' => '90kg',
        ]);
    }

    /**
     * 6. التحقق من رفض تقديم تقرير لتمارين في خطة تدريبية مؤرشفة.
     */
    public function test_trainee_cannot_submit_report_for_archived_workout_plan(): void
    {
        // أرشفة الخطة A
        $this->planA->status = 'archived';
        $this->planA->save();

        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '30kg',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(403);
    }

    /**
     * 7. التحقق من منع الضيوف غير المسجلين من الوصول لمسارات التقارير أو المراجعة.
     */
    public function test_unauthorized_guests_blocked_from_submitting_or_reviewing_reports(): void
    {
        // محاولة ضيف إرسال تقرير
        $responseSubmit = $this->post(route('system.load-adjustment.store'), [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '20kg',
        ]);
        $responseSubmit->assertRedirect('/system/login');

        // محاولة ضيف مراجعة تقرير
        $responseReview = $this->post(route('system.load-adjustment.review', 1), [
            'status' => 'reviewed',
        ]);
        $responseReview->assertRedirect('/system/login');
    }

    /**
     * 8. التحقق من قدرة المدرب المصرح على استعراض تقارير المتدرب وتاريخها.
     */
    public function test_authorized_coach_can_view_trainee_load_adjustments_and_history(): void
    {
        // إنشاء تقرير للمتدربة Alice
        TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_plan_id'  => $this->planA->id,
            'workout_day_name' => 'Day 1',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg',
            'reported_load'    => '25kg',
            'status'           => 'pending',
        ]);

        // المدرب يعرض ملف المتدربة
        $response = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $response->assertStatus(200);
        $response->assertSee('Barbell Bench Press');
        $response->assertSee('25kg');
        $response->assertSee('Load Adjustments');

        // استعلام مسار التاريخ
        $responseHistory = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.load-adjustment.trainee-history', ['trainee_id' => $this->traineeRecordA->id]));

        $responseHistory->assertStatus(200);
        $responseHistory->assertJsonFragment(['reported_load' => '25kg']);
    }

    /**
     * 9. التحقق من قدرة المدرب على مراجعة التقرير وتسجيل ملاحظاته.
     */
    public function test_authorized_coach_can_review_report_and_add_coach_notes(): void
    {
        $report = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_plan_id'  => $this->planA->id,
            'workout_day_name' => 'Day 1',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg',
            'reported_load'    => '25kg',
            'status'           => 'pending',
        ]);

        $reviewPayload = [
            'status'      => 'reviewed',
            'coach_notes' => 'Good form observed, approved for progression.',
        ];

        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.review', $report->id), $reviewPayload);

        $response->assertStatus(200);

        $report->refresh();
        $this->assertSame('reviewed', $report->status);
        $this->assertSame('Good form observed, approved for progression.', $report->coach_notes);
        $this->assertSame($this->adminCoach->id, $report->reviewed_by);
        $this->assertNotNull($report->reviewed_at);
    }

    /**
     * 10. التحقق من حظر المتدرب من استدعاء مسار مراجعة المدرب.
     */
    public function test_trainee_cannot_access_coach_review_endpoint(): void
    {
        $report = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_plan_id'  => $this->planA->id,
            'workout_day_name' => 'Day 1',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg',
            'reported_load'    => '25kg',
            'status'           => 'pending',
        ]);

        // المتدربة Alice تحاول اعتماد تقريرها بنفسها
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.review', $report->id), [
                'status' => 'reviewed',
            ]);

        // وسيط الحماية يمنع المتدرب من مسارات المدرب ويعيد توجيهه للوحة المتدرب
        $response->assertRedirect(route('system.dashboard.trainer'));

        $report->refresh();
        $this->assertSame('pending', $report->status);
    }

    /**
     * 11. التحقق من الحفظ التاريخي التراكمي: تقديم تقارير متعددة لنفس التمرين لا يمسح التقارير السابقة.
     */
    public function test_multiple_submissions_preserve_complete_history_without_overwriting(): void
    {
        // التقرير الأول
        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '22.5kg',
                'notes'         => 'First week report',
            ]);

        // التقرير الثاني لنفس التمرين بعد أسبوع
        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '25kg',
                'notes'         => 'Second week report',
            ]);

        // التقرير الثالث
        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '27.5kg',
                'notes'         => 'Third week report',
            ]);

        $reports = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(3, $reports);
        $this->assertSame('22.5kg', $reports[0]->reported_load);
        $this->assertSame('First week report', $reports[0]->notes);

        $this->assertSame('25kg', $reports[1]->reported_load);
        $this->assertSame('Second week report', $reports[1]->notes);

        $this->assertSame('27.5kg', $reports[2]->reported_load);
        $this->assertSame('Third week report', $reports[2]->notes);
    }

    /**
     * 12. التحقق من بقاء السجل التاريخي مفهوماً ومحفوظاً عند حذف التمرين عبر اللقطات الثابتة.
     */
    public function test_history_remains_readable_when_exercise_is_deleted_or_plan_changed_via_snapshots(): void
    {
        // إنشاء التقرير
        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA2->id,
                'reported_load' => '17.5kg',
                'notes'         => 'Snapshot test',
            ]);

        $report = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA2->id)->first();
        $this->assertNotNull($report);

        // حذف التمرين فيزيائياً
        $this->exerciseA2->forceDelete();

        $report->refresh();

        // المفتاح الأجنبي يتحول إلى null
        $this->assertNull($report->exercise_id);

        // ولكن لقطة الاسم واليوم والحمل المعتمد تظل محفوظة بالكامل
        $this->assertSame('Incline Dumbbell Press', $report->exercise_name);
        $this->assertSame('Day 1 - Chest & Arms', $report->workout_day_name);
        $this->assertSame('15kg', $report->prescribed_load);
        $this->assertSame('17.5kg', $report->reported_load);
    }

    /**
     * 13. التحقق من رفض المدخلات غير الصالحة والفارغة وتجاوز الحد الأقصى.
     */
    public function test_validation_rejects_missing_empty_or_excessive_loads_and_notes(): void
    {
        // 1. تمرين غير موجود أو غير مصرح: يُرفض بالتصريح (403 Forbidden)
        $responseBadEx = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => 999999,
                'reported_load' => '20kg',
            ]);
        $responseBadEx->assertStatus(403);

        // 2. حمل فارغ: يُرفض بقواعد التحقق
        $responseEmptyLoad = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '',
            ]);
        $responseEmptyLoad->assertSessionHasErrors(['reported_load']);

        // 3. حمل يتجاوز 100 حرف
        $responseLongLoad = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => str_repeat('A', 101),
            ]);
        $responseLongLoad->assertSessionHasErrors(['reported_load']);

        // 4. ملاحظات تتجاوز 1000 حرف
        $responseLongNotes = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '25kg',
                'notes'         => str_repeat('N', 1001),
            ]);
        $responseLongNotes->assertSessionHasErrors(['notes']);
    }

    /**
     * 14. التحقق من منع تزوير حالة المراجعة أو ملاحظات المدرب من خلال طلبات المتدرب.
     */
    public function test_review_status_and_coach_notes_cannot_be_forged_via_trainee_submission(): void
    {
        // المتدربة ترسل في مدخلاتها status = reviewed و coach_notes = Hacked
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '35kg',
            'status'        => 'reviewed',
            'coach_notes'   => 'Fake coach approval',
        ];

        $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.load-adjustment.store'), $payload);

        $report = TraineeLoadAdjustment::where('reported_load', '35kg')->first();
        $this->assertNotNull($report);

        // الحالة يجب أن تظل pending وملاحظات المدرب null
        $this->assertSame('pending', $report->status);
        $this->assertNull($report->coach_notes);
        $this->assertNull($report->reviewed_by);
    }

    /**
     * 15. التحقق من استمرار عمل سير عمل تعديل الخطط التدريبية المعتمدة للمدرب بشكل طبيعي ومستقل.
     */
    public function test_existing_coach_workout_editing_workflow_remains_intact_and_separate(): void
    {
        $updatePayload = [
            'day_name'  => 'Day 1 - Chest & Arms Updated',
            'exercises' => [
                [
                    'exercise_id'     => $this->exerciseA1->id,
                    'name'            => 'Barbell Bench Press',
                    'sets'            => '5',
                    'reps'            => '10',
                    'internal_weight' => ['30kg', '35kg'],
                    'type'            => 'main',
                ],
            ],
        ];

        $response = $this->actingAs($this->adminCoach, 'user')
            ->post(route('system.workout.updateDay', $this->planA->id), $updatePayload);

        $this->exerciseA1->refresh();
        $this->assertSame(['30kg', '35kg'], $this->exerciseA1->internal_weight);
        $this->assertSame(5, (int) $this->exerciseA1->sets);
    }
}
