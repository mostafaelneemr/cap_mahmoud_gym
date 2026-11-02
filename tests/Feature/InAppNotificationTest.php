<?php

namespace Tests\Feature;

use App\Models\InAppNotification;
use App\Models\PermissionGroup;
use App\Models\User;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $coachUser;
    protected User $traineeUser;
    protected InAppNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(InAppNotificationService::class);

        $group = PermissionGroup::first();
        if (!$group) {
            $group = new PermissionGroup();
            $group->name = 'Admin Group';
            $group->is_active = '1';
            $group->save();
        }

        // Dedicated active coach
        $this->coachUser = new User();
        $this->coachUser->name = 'Test Coach Admin';
        $this->coachUser->email = 'coach_notif_' . bin2hex(random_bytes(4)) . '@gym.com';
        $this->coachUser->password = bcrypt('secret123');
        $this->coachUser->status = 'active';
        $this->coachUser->user_type = 1;
        $this->coachUser->permission_group_id = $group->id;
        $this->coachUser->force_reset_password = 0;
        $this->coachUser->save();

        // Dedicated trainee user
        $this->traineeUser = new User();
        $this->traineeUser->name = 'Test Trainee';
        $this->traineeUser->email = 'trainee_notif_' . bin2hex(random_bytes(4)) . '@gym.com';
        $this->traineeUser->password = bcrypt('secret123');
        $this->traineeUser->status = 'active';
        $this->traineeUser->user_type = 2;
        $this->traineeUser->force_reset_password = 0;
        $this->traineeUser->save();
    }

    public function test_can_create_and_fetch_unread_notification(): void
    {
        $notification = $this->service->send(
            $this->coachUser->id,
            InAppNotification::TYPE_COACH_NEW_LOAD_ADJUSTMENT,
            'New Load Adjustment Test',
            'Trainee has submitted a weight update request.',
            route('system.dashboard')
        );

        $this->assertNotNull($notification);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $this->coachUser->id,
            'title' => 'New Load Adjustment Test',
            'read_at' => null,
        ]);

        $unreadCount = $this->service->getUnreadCount($this->coachUser->id);
        $this->assertGreaterThanOrEqual(1, $unreadCount);
    }

    public function test_duplicate_notification_within_one_hour_is_prevented(): void
    {
        $uniqueTitle = 'Duplicate Prevention Test ' . uniqid();
        $first = $this->service->send(
            $this->traineeUser->id,
            InAppNotification::TYPE_TRAINEE_WORKOUT_UPDATED,
            $uniqueTitle,
            'Your workout program has been updated.',
            route('system.dashboard.trainer')
        );

        $this->assertNotNull($first);

        // Second identical send should return existing duplicate without creating new record
        $second = $this->service->send(
            $this->traineeUser->id,
            InAppNotification::TYPE_TRAINEE_WORKOUT_UPDATED,
            $uniqueTitle,
            'Your workout program has been updated.',
            route('system.dashboard.trainer')
        );

        $this->assertNotNull($second);
        $this->assertEquals($first->id, $second->id);
    }

    public function test_mark_single_notification_as_read(): void
    {
        $notification = $this->service->send(
            $this->coachUser->id,
            InAppNotification::TYPE_COACH_NEW_PROGRESS,
            'Check-in Submitted',
            'Trainee uploaded weekly photos.'
        );

        $this->assertFalse($notification->isRead());

        $this->actingAs($this->coachUser, 'user')
            ->post(route('system.notifications.read-post', $notification->id))
            ->assertStatus(302);

        $notification->refresh();
        $this->assertTrue($notification->isRead());
        $this->assertNotNull($notification->read_at);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $n1 = $this->service->send($this->coachUser->id, InAppNotification::TYPE_GENERAL, 'Alert 1 ' . uniqid(), 'Message 1');
        $n2 = $this->service->send($this->coachUser->id, InAppNotification::TYPE_GENERAL, 'Alert 2 ' . uniqid(), 'Message 2');

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.notifications.read-all'))
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'unread_count' => 0,
            ]);

        $this->assertEquals(0, $this->service->getUnreadCount($this->coachUser->id));
    }

    public function test_dropdown_endpoint_returns_json_payload(): void
    {
        $n = $this->service->send($this->coachUser->id, InAppNotification::TYPE_GENERAL, 'Dropdown Test ' . uniqid(), 'Message text');

        $response = $this->actingAs($this->coachUser, 'user')
            ->getJson(route('system.notifications.dropdown'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'unread_count',
                'notifications' => [
                    '*' => ['id', 'type', 'title', 'message', 'action_url', 'icon', 'is_read', 'created_at_human', 'read_url']
                ]
            ]);
    }

    public function test_user_cannot_access_or_mark_other_users_notification(): void
    {
        $coachNotification = $this->service->send(
            $this->coachUser->id,
            InAppNotification::TYPE_GENERAL,
            'Private Coach Notification',
            'Restricted'
        );

        // Trainee attempts to mark coach's notification as read
        $response = $this->actingAs($this->traineeUser, 'user')
            ->post(route('system.notifications.read-post', $coachNotification->id));

        // 404 because query scopes to where user_id == auth()->id()
        $response->assertStatus(404);
    }
}
