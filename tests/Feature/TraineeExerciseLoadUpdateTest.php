<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TraineeExerciseLoadUpdateTest extends TestCase
{
    use DatabaseTransactions;

    protected ?User $adminCoach = null;
    protected ?User $traineeUserA = null;
    protected ?Trainee $traineeRecordA = null;
    protected ?User $traineeUserB = null;
    protected ?Trainee $traineeRecordB = null;
    protected ?WorkoutPlan $planA = null;
    protected ?Exercise $exerciseA1 = null;
    protected ?Exercise $exerciseA2 = null;
    protected ?WorkoutPlan $planB = null;
    protected ?Exercise $exerciseB1 = null;

    protected function setUp(): void
    {
        parent::setUp();

        // فحص المتطلب الأساسي: التأكد من وجود الجدول المدار يدوياً
        if (!Schema::hasTable('trainee_load_adjustments')) {
            $this->markTestSkipped(
                'Table trainee_load_adjustments does not exist in the database. ' .
                'As per strict safety rules, the table must be created manually by the user before running tests.'
            );
        }

        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // إنشاء المدرب الإداري
        $this->adminCoach = User::create([
            'name'                 => 'Coach Mahmoud',
            'email'                => 'coach_' . uniqid() . '@fitness.local',
            'password'             => Hash::make('CoachPass!'),
            'status'               => '1',
            'permission_group_id'  => 119,
            'user_type'            => 1,
            'default_language'     => 'ar',
            'force_reset_password' => false,
        ]);

        // المتدربة الأولى Alice (user_type = 2)
        $this->traineeUserA = User::create([
            'name'                 => 'Trainee Alice',
            'email'                => 'alice_' . uniqid() . '@fitness.local',
            'password'             => Hash::make('TraineePass!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'ar',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'user_id'          => $this->traineeUserA->id,
            'name'             => 'Trainee Alice',
            'email'            => $this->traineeUserA->email,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(3)->toDateString(),
            'status'           => 'active',
        ]);

        // المتدرب الثاني Bob
        $this->traineeUserB = User::create([
            'name'                 => 'Trainee Bob',
            'email'                => 'bob_' . uniqid() . '@fitness.local',
            'password'             => Hash::make('TraineePass!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'ar',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'user_id'          => $this->traineeUserB->id,
            'name'             => 'Trainee Bob',
            'email'            => $this->traineeUserB->email,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(3)->toDateString(),
            'status'           => 'active',
        ]);

        // خطة وتمارين Alice
        $this->planA = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecordA->id,
            'day_name'   => 'Day 1 - Chest & Arms',
            'status'     => 'active',
        ]);

        $this->exerciseA1 = Exercise::create([
            'workout_plan_id' => $this->planA->id,
            'name'            => 'Barbell Bench Press',
            'sets'            => 4,
            'reps'            => '8-10',
            'rest'            => '90',
            'internal_weight' => ['20kg', '25kg'],
            'type'            => 'main',
        ]);

        $this->exerciseA2 = Exercise::create([
            'workout_plan_id' => $this->planA->id,
            'name'            => 'Incline Dumbbell Press',
            'sets'            => 3,
            'reps'            => '10',
            'rest'            => '60',
            'internal_weight' => '15kg',
            'type'            => 'main',
        ]);

        // خطة وتمارين Bob
        $this->planB = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecordB->id,
            'day_name'   => 'Day 1 - Legs',
            'status'     => 'active',
        ]);

        $this->exerciseB1 = Exercise::create([
            'workout_plan_id' => $this->planB->id,
            'name'            => 'Barbell Squat',
            'sets'            => 4,
            'reps'            => '8',
            'internal_weight' => '80kg',
            'type'            => 'main',
        ]);
    }

    /**
     * 1. التحقق من قدرة المتدرب المصرح على تقديم وزن جديد لتمرينه
     */
    public function test_authorized_trainee_can_submit_new_weight(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '30kg',
            'notes'         => 'Feeling strong today',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trainee_load_adjustments', [
            'trainee_id'    => $this->traineeRecordA->id,
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '30kg',
            'status'        => 'pending',
        ]);
    }

    /**
     * 2. التحقق من قدرة المتدرب على إرسال ملاحظة بدون تحديد وزن جديد
     */
    public function test_authorized_trainee_can_submit_comment_without_weight(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => null,
            'notes'         => 'Right shoulder feeling tight during last set.',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trainee_load_adjustments', [
            'trainee_id'    => $this->traineeRecordA->id,
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => null,
            'notes'         => 'Right shoulder feeling tight during last set.',
            'status'        => 'pending',
        ]);
    }

    /**
     * 3. التحقق من رفض التقديم الفارغ (بدون وزن وبدون ملاحظة)
     */
    public function test_empty_submission_is_rejected(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '',
            'notes'         => '',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reported_load', 'notes']);
    }

    /**
     * 4. التحقق من منع المتدرب من تقديم تقرير لتمرين يخص متدرباً آخر
     */
    public function test_trainee_cannot_submit_update_for_another_trainees_exercise(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseB1->id, // تمرين Bob
            'reported_load' => '90kg',
            'notes'         => 'Hacked submission',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('trainee_load_adjustments', [
            'exercise_id' => $this->exerciseB1->id,
            'notes'       => 'Hacked submission',
        ]);
    }

    /**
     * 5. التحقق من منع المتدرب من التلاعب بحقول المراجعة عبر الطلب
     */
    public function test_trainee_cannot_manipulate_review_fields(): void
    {
        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '35kg',
            'notes'         => 'Normal note',
            'status'        => 'reviewed',
            'coach_notes'   => 'Forged approval',
            'reviewed_by'   => 999,
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        $report = TraineeLoadAdjustment::where('reported_load', '35kg')->first();
        $this->assertNotNull($report);
        $this->assertSame('pending', $report->status);
        $this->assertNull($report->coach_notes);
        $this->assertNull($report->reviewed_by);
        $this->assertNull($report->reviewed_at);
    }

    /**
     * 6. القاعدة الإلزامية: التحقق من أن تقديم التقرير لا يغير نهائياً الأوزان المقررة في exercises.internal_weight
     */
    public function test_successful_new_submission_does_not_change_exercises_internal_weight(): void
    {
        $originalInternalWeight = $this->exerciseA1->internal_weight;
        $originalDisplayWeight = $this->exerciseA1->internal_weight_display;

        $payload = [
            'exercise_id'   => $this->exerciseA1->id,
            'reported_load' => '100kg',
            'notes'         => 'Testing weight isolation',
        ];

        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $this->exerciseA1->refresh();

        $this->assertSame($originalInternalWeight, $this->exerciseA1->internal_weight);
        $this->assertSame($originalDisplayWeight, $this->exerciseA1->internal_weight_display);
    }

    /**
     * 7. التحقق من بقاء التقديمات المتعددة محفوظة تاريخياً بشكل تراكمي دون استبدال
     */
    public function test_multiple_submissions_remain_separately_available_in_history(): void
    {
        // التقديم الأول
        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '22.5kg',
                'notes'         => 'First submission',
            ]);

        // التقديم الثاني
        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA1->id,
                'reported_load' => '25kg',
                'notes'         => 'Second submission',
            ]);

        $reports = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(2, $reports);
        $this->assertSame('22.5kg', $reports[0]->reported_load);
        $this->assertSame('First submission', $reports[0]->notes);
        $this->assertSame('25kg', $reports[1]->reported_load);
        $this->assertSame('Second submission', $reports[1]->notes);
    }

    /**
     * 8. التحقق من قدرة المدرب المصرح على مراجعة التقرير أو إلغائه وتدوين الملاحظات
     */
    public function test_authorized_coach_can_review_or_dismiss_submission(): void
    {
        $report = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_plan_id'  => $this->planA->id,
            'workout_day_name' => 'Day 1 - Chest & Arms',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg / 25kg',
            'reported_load'    => '27.5kg',
            'notes'            => 'Trainee note',
            'status'           => 'pending',
        ]);

        $reviewPayload = [
            'status'      => 'reviewed',
            'coach_notes' => 'Good progress, keep form tight.',
        ];

        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.review', $report->id), $reviewPayload);

        $response->assertStatus(200);

        $report->refresh();
        $this->assertSame('reviewed', $report->status);
        $this->assertSame('Good progress, keep form tight.', $report->coach_notes);
        $this->assertSame($this->adminCoach->id, $report->reviewed_by);
        $this->assertNotNull($report->reviewed_at);
    }

    /**
     * 9. التحقق من حظر المستخدمين غير المصرح لهم والمتدربين من مراجعة التقارير
     */
    public function test_unauthorized_user_or_coach_cannot_access_or_review_record(): void
    {
        $report = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_plan_id'  => $this->planA->id,
            'workout_day_name' => 'Day 1 - Chest & Arms',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg / 25kg',
            'reported_load'    => '27.5kg',
            'status'           => 'pending',
        ]);

        // محاولة المتدربة Alice مراجعة التقرير بنفسها
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.review', $report->id), [
                'status' => 'reviewed',
            ]);

        // المتدرب مرفوض (302 redirect من وسيط الصلاحيات أو 403)
        $this->assertTrue(in_array($response->status(), [302, 403]));

        $report->refresh();
        $this->assertSame('pending', $report->status);
    }

    /**
     * 10. التحقق من بقاء السجلات التاريخية مقروءة ومحفوظة حتى لو حذف التمرين لاحقاً
     */
    public function test_historical_records_remain_readable_if_referenced_exercise_is_deleted(): void
    {
        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id'   => $this->exerciseA2->id,
                'reported_load' => '17.5kg',
                'notes'         => 'Snapshot verification test',
            ]);

        $report = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA2->id)->first();
        $this->assertNotNull($report);

        // حذف التمرين فيزيائياً
        $this->exerciseA2->forceDelete();

        $report->refresh();

        // اللقطات الثابتة تظل محفوظة بالكامل
        $this->assertSame('Incline Dumbbell Press', $report->exercise_name);
        $this->assertSame('Day 1 - Chest & Arms', $report->workout_day_name);
        $this->assertSame('15kg', $report->prescribed_load);
        $this->assertSame('17.5kg', $report->reported_load);
        $this->assertSame('Snapshot verification test', $report->notes);
    }
}
