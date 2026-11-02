<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\InAppNotification;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\TraineeLoadAdjustmentItem;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Services\InAppNotificationService;
use App\Services\LoadAdjustmentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoadAdjustmentNotificationAndCenterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $coachUser;
    protected User $traineeUser;
    protected Trainee $traineeRecord;
    protected WorkoutPlan $workoutPlan;
    protected Exercise $exercise;
    protected InAppNotificationService $notificationService;
    protected LoadAdjustmentService $loadAdjustmentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = app(InAppNotificationService::class);
        $this->loadAdjustmentService = app(LoadAdjustmentService::class);

        // Find or create permission group
        $group = PermissionGroup::first();
        if (!$group) {
            $group = new PermissionGroup();
            $group->name = 'Super Admin Group';
            $group->is_active = '1';
            $group->save();
        }

        // Active Coach User
        $this->coachUser = User::create([
            'name'                 => 'Coach Mahmoud',
            'email'                => 'coach_' . uniqid() . '@gym.local',
            'password'             => Hash::make('Password123!'),
            'status'               => '1',
            'user_type'            => 1,
            'permission_group_id'  => $group->id,
            'default_language'     => 'ar',
            'force_reset_password' => 0,
        ]);

        // Active Trainee User
        $this->traineeUser = User::create([
            'name'                 => 'Trainee Omar',
            'email'                => 'omar_' . uniqid() . '@gym.local',
            'password'             => Hash::make('Password123!'),
            'status'               => '1',
            'user_type'            => 2,
            'permission_group_id'  => null,
            'default_language'     => 'ar',
            'force_reset_password' => 0,
        ]);

        // Trainee profile
        $this->traineeRecord = Trainee::create([
            'user_id'          => $this->traineeUser->id,
            'name'             => 'Trainee Omar',
            'email'            => $this->traineeUser->email,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(3)->toDateString(),
            'status'           => 'active',
        ]);

        // Active Workout Plan
        $this->workoutPlan = WorkoutPlan::create([
            'trainee_id' => $this->traineeRecord->id,
            'day_name'   => 'Day 1 - Chest & Shoulders',
            'status'     => 'active',
        ]);

        // Exercise with 4 sets
        $this->exercise = Exercise::create([
            'workout_plan_id' => $this->workoutPlan->id,
            'name'            => 'Barbell Incline Press',
            'sets'            => 4,
            'reps'            => '10',
            'rest'            => '60',
            'internal_weight' => ['30kg', '35kg', '40kg', '45kg'],
            'type'            => 'main',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Notification Tests
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * 1. A new request creates the correct coach notification with deep link to review center.
     */
    public function test_new_request_creates_correct_coach_notification(): void
    {
        $payload = [
            'exercise_id' => $this->exercise->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '32.5kg'],
                ['set_number' => 2, 'suggested_weight' => '37.5kg'],
            ],
            'notes'       => 'Pushed heavier today',
        ];

        $response = $this->actingAs($this->traineeUser, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        // Verify coach received notification
        $notification = InAppNotification::where('user_id', $this->coachUser->id)
            ->where('type', 'load_adjustment_submitted')
            ->latest('id')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Trainee Omar', $notification->title);
        $this->assertStringContainsString('Barbell Incline Press', $notification->message);
        $this->assertStringContainsString('Day 1 - Chest & Shoulders', $notification->message);
        $this->assertStringContainsString(route('system.load-adjustment.index'), $notification->action_url);
        $this->assertSame('ki-weight', $notification->icon);
    }

    /**
     * 2. An individual approved set creates a trainee notification.
     */
    public function test_individual_approved_set_creates_trainee_notification(): void
    {
        $report = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '32.5kg'],
            ],
        ], $this->traineeUser);

        $item = $report->items->first();

        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'      => 'approve',
                'coach_notes' => 'Good progress, form looks solid.',
            ]);

        $response->assertStatus(200);

        // Verify trainee received approval notification
        $notification = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_approved')
            ->latest('id')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Barbell Incline Press', $notification->title);
        $this->assertStringContainsString('32.5kg', $notification->message);
        $this->assertStringContainsString('1', $notification->message);
        $this->assertStringContainsString('Good progress', $notification->message);
        $this->assertSame(route('system.dashboard.trainer'), $notification->action_url);
        $this->assertSame('ki-check-circle', $notification->icon);
    }

    /**
     * 3. An individually rejected set creates a trainee notification without changing workout weight.
     */
    public function test_individual_rejected_set_creates_trainee_notification(): void
    {
        $originalWeights = $this->exercise->internal_weight;

        $report = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [
                ['set_number' => 2, 'suggested_weight' => '50kg'],
            ],
        ], $this->traineeUser);

        $item = $report->items->first();

        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'      => 'reject',
                'coach_notes' => 'Too heavy for now, stay at 35kg.',
            ]);

        $response->assertStatus(200);

        // Verify exercise weight remained unchanged
        $this->exercise->refresh();
        $this->assertSame($originalWeights, $this->exercise->internal_weight);

        // Verify trainee received rejection notification
        $notification = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_rejected')
            ->latest('id')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('رفض', $notification->title);
        $this->assertStringContainsString('المجموعة 2', $notification->message);
        $this->assertStringContainsString('Too heavy for now', $notification->message);
        $this->assertSame('ki-cross-circle', $notification->icon);
    }

    /**
     * 4. Alternative approved weight is accurately represented in notification and plan.
     */
    public function test_alternative_approved_weight_accurately_represented(): void
    {
        $report = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [
                ['set_number' => 3, 'suggested_weight' => '50kg'],
            ],
        ], $this->traineeUser);

        $item = $report->items->first();

        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'          => 'approve_alternative',
                'approved_weight' => '42.5kg',
                'coach_notes'     => 'Let us step up to 42.5kg first.',
            ]);

        $response->assertStatus(200);

        $this->exercise->refresh();
        $this->assertSame('42.5kg', $this->exercise->getWeightForSet(3));

        $notification = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_approved')
            ->latest('id')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('42.5kg', $notification->message);
        $this->assertStringContainsString('50kg', $notification->message);
        $this->assertStringContainsString('وزن بديل', $notification->message);
    }

    /**
     * 5. Conflict resolution creates the correct notification upon explicit force approval.
     */
    public function test_conflict_resolution_creates_correct_notification(): void
    {
        // 1. Initial request proposing 35kg for set 1 (base is 30kg)
        $reportA = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '32.5kg']],
        ], $this->traineeUser);

        // 2. Second request proposing 35kg for set 1 (base is 30kg)
        $reportB = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '35kg']],
        ], $this->traineeUser);

        $itemA = $reportA->items->first();
        $itemB = $reportB->items->first();

        // Approve A -> set 1 becomes 32.5kg
        $this->loadAdjustmentService->reviewItem($itemA->id, ['action' => 'approve'], $this->coachUser);

        // Attempt approve B without force -> conflict detected
        $this->loadAdjustmentService->reviewItem($itemB->id, ['action' => 'approve'], $this->coachUser);
        $itemB->refresh();
        $this->assertSame('conflict', $itemB->status);

        // Explicit force conflict resolution
        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $itemB->id), [
                'action'      => 'resolve_approve',
                'force'       => true,
                'coach_notes' => 'Conflict resolved by advancing to 35kg.',
            ]);

        $response->assertStatus(200);

        $this->exercise->refresh();
        $this->assertSame('35kg', $this->exercise->getWeightForSet(1));

        $notification = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_approved')
            ->where('message', 'like', '%35kg%')
            ->first();

        $this->assertNotNull($notification);
    }

    /**
     * 6. Failed transactions do not create success notifications.
     */
    public function test_failed_transactions_do_not_create_success_notifications(): void
    {
        $notificationsBefore = InAppNotification::count();

        // Intentionally invalid exercise ID in store request
        $response = $this->actingAs($this->traineeUser, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id' => 99999999,
                'sets'        => [['set_number' => 1, 'suggested_weight' => '35kg']],
            ]);

        $this->assertTrue(in_array($response->status(), [403, 422]));

        $notificationsAfter = InAppNotification::count();
        $this->assertSame($notificationsBefore, $notificationsAfter);
    }

    /**
     * 7. Duplicate review submissions do not send duplicate notifications.
     */
    public function test_duplicate_review_submissions_do_not_send_duplicate_notifications(): void
    {
        $report = $this->loadAdjustmentService->storeReport([
            'exercise_id' => $this->exercise->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '32.5kg']],
        ], $this->traineeUser);

        $item = $report->items->first();

        // First review
        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action' => 'approve',
            ])->assertStatus(200);

        $countAfterFirst = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_approved')
            ->count();

        $this->assertSame(1, $countAfterFirst);

        // Second duplicate review call on already approved item
        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action' => 'approve',
            ])->assertStatus(200);

        $countAfterSecond = InAppNotification::where('user_id', $this->traineeUser->id)
            ->where('type', 'load_adjustment_item_approved')
            ->count();

        // Count should not duplicate
        $this->assertSame(1, $countAfterSecond);
    }

    /**
     * 8. Notification unread count updates correctly.
     */
    public function test_notification_unread_count_updates_correctly(): void
    {
        $initialUnread = $this->notificationService->getUnreadCount($this->traineeUser->id);

        $this->notificationService->send(
            $this->traineeUser,
            'test_unread',
            'Unread Alert ' . uniqid(),
            'Sample body message'
        );

        $afterSend = $this->notificationService->getUnreadCount($this->traineeUser->id);
        $this->assertSame($initialUnread + 1, $afterSend);

        // Mark all as read
        $this->notificationService->markAllAsRead($this->traineeUser->id);
        $this->assertSame(0, $this->notificationService->getUnreadCount($this->traineeUser->id));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Authorization Tests
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * 9. Coach can access the full notifications page.
     */
    public function test_coach_can_access_full_notifications_page(): void
    {
        $response = $this->actingAs($this->coachUser, 'user')
            ->get(route('system.notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    /**
     * 10. Trainee receives HTTP 403 when accessing the full notifications page directly.
     */
    public function test_trainee_receives_http_403_when_accessing_full_notifications_page(): void
    {
        $response = $this->actingAs($this->traineeUser, 'user')
            ->get(route('system.notifications.index'));

        $response->assertStatus(403);
    }

    /**
     * 11. Trainee can access the header dropdown notification feed.
     */
    public function test_trainee_can_access_header_dropdown(): void
    {
        $response = $this->actingAs($this->traineeUser, 'user')
            ->getJson(route('system.notifications.dropdown'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'unread_count',
                'notifications',
            ]);
    }

    /**
     * 12. Trainee can mark their own notifications as read.
     */
    public function test_trainee_can_mark_own_notification_as_read(): void
    {
        $notif = $this->notificationService->send(
            $this->traineeUser,
            'general',
            'Personal Alert ' . uniqid(),
            'Message'
        );

        $this->assertFalse($notif->isRead());

        $response = $this->actingAs($this->traineeUser, 'user')
            ->postJson(route('system.notifications.read-post', $notif->id));

        $response->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertTrue($notif->fresh()->isRead());
    }

    /**
     * 13. Trainee cannot read or modify another user's notification (IDOR protection).
     */
    public function test_trainee_cannot_read_or_modify_another_users_notification(): void
    {
        $coachNotif = $this->notificationService->send(
            $this->coachUser,
            'general',
            'Coach Confidential Alert',
            'Confidential message'
        );

        // Trainee attempts to mark coach's notification as read
        $response = $this->actingAs($this->traineeUser, 'user')
            ->post(route('system.notifications.read-post', $coachNotif->id));

        $response->assertStatus(404);
        $this->assertFalse($coachNotif->fresh()->isRead());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Load Adjustment Center & Flow Tests
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * 14. Coach can access the centralized load adjustment review center.
     */
    public function test_coach_can_access_centralized_load_adjustment_page(): void
    {
        $response = $this->actingAs($this->coachUser, 'user')
            ->get(route('system.load-adjustment.index'));

        $response->assertStatus(200);
        $response->assertSee('Load Adjustment Center');
        $response->assertSee('مجموعات تنتظر القرار');
    }

    /**
     * 15. Trainee receives HTTP 403 on the centralized load adjustment review page.
     */
    public function test_trainee_receives_http_403_on_centralized_load_adjustment_page(): void
    {
        $response = $this->actingAs($this->traineeUser, 'user')
            ->get(route('system.load-adjustment.index'));

        $response->assertStatus(403);
    }

    /**
     * 16. Existing trainee submission flow beneath exercise still functions seamlessly.
     */
    public function test_existing_trainee_submission_flow_beneath_exercise_still_functions(): void
    {
        $payload = [
            'exercise_id' => $this->exercise->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '32.5kg'],
            ],
            'notes'       => 'Warmup set was easy',
        ];

        $response = $this->actingAs($this->traineeUser, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('trainee_load_adjustments', [
            'trainee_id'  => $this->traineeRecord->id,
            'exercise_id' => $this->exercise->id,
            'status'      => 'pending',
        ]);

        $this->assertDatabaseHas('trainee_load_adjustment_items', [
            'set_number'       => 1,
            'base_weight'      => '30kg',
            'suggested_weight' => '32.5kg',
            'status'           => 'pending',
        ]);
    }
}
