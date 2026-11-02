<?php

namespace Tests\Feature;

use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Services\WhatsAppBroadcastService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WhatsAppBroadcastTest extends TestCase
{
    use DatabaseTransactions;

    protected User $coachUser;
    protected User $traineeUser;
    protected WhatsAppBroadcastService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WhatsAppBroadcastService::class);

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
        $this->coachUser->email = 'coach_wa_' . bin2hex(random_bytes(4)) . '@gym.com';
        $this->coachUser->password = bcrypt('secret123');
        $this->coachUser->status = 'active';
        $this->coachUser->user_type = 1;
        $this->coachUser->permission_group_id = $group->id;
        $this->coachUser->force_reset_password = 0;
        $this->coachUser->save();

        // Dedicated trainee user
        $this->traineeUser = new User();
        $this->traineeUser->name = 'Test Trainee';
        $this->traineeUser->email = 'trainee_wa_' . bin2hex(random_bytes(4)) . '@gym.com';
        $this->traineeUser->password = bcrypt('secret123');
        $this->traineeUser->status = 'active';
        $this->traineeUser->user_type = 2;
        $this->traineeUser->force_reset_password = 0;
        $this->traineeUser->save();
    }

    public function test_phone_normalization_formats_egyptian_and_international_numbers(): void
    {
        // Standard Egyptian 11 digits with 0
        $this->assertEquals('201012345678', $this->service->normalizePhoneNumber('01012345678'));
        $this->assertEquals('201123456789', $this->service->normalizePhoneNumber('01123456789'));
        $this->assertEquals('201234567890', $this->service->normalizePhoneNumber('01234567890'));
        $this->assertEquals('201555555555', $this->service->normalizePhoneNumber('01555555555'));

        // Egyptian 10 digits without leading 0
        $this->assertEquals('201012345678', $this->service->normalizePhoneNumber('1012345678'));

        // Egyptian with +20 or 0020
        $this->assertEquals('201012345678', $this->service->normalizePhoneNumber('+201012345678'));
        $this->assertEquals('201012345678', $this->service->normalizePhoneNumber('00201012345678'));

        // International Saudi number
        $this->assertEquals('966501234567', $this->service->normalizePhoneNumber('+966501234567'));
        $this->assertEquals('966501234567', $this->service->normalizePhoneNumber('00966501234567'));

        // Invalid numbers
        $this->assertNull($this->service->normalizePhoneNumber(''));
        $this->assertNull($this->service->normalizePhoneNumber(null));
        $this->assertNull($this->service->normalizePhoneNumber('12345')); // too short
        $this->assertNull($this->service->normalizePhoneNumber('abcdefghij'));
    }

    public function test_message_personalization_replaces_placeholders(): void
    {
        $template = "Hello {{name}}, your Trainee ID is {{id}}! Please contact Coach Mahmoud.";
        $recipient = [
            'name' => 'Mohamed Ali',
            'trainee_id' => 999,
        ];

        $rendered = $this->service->renderMessage($template, $recipient);

        $this->assertEquals("Hello Mohamed Ali, your Trainee ID is 999! Please contact Coach Mahmoud.", $rendered);
    }

    public function test_recipient_wa_me_url_is_rfc3986_compliant(): void
    {
        $recipient = new WhatsAppCampaignRecipient();
        $recipient->phone_normalized = '201012345678';
        $recipient->message_rendered = "مرحبا أحمد! موعد تمرينك اليوم الساعة 5.";

        $url = $recipient->wa_me_url;

        $this->assertStringStartsWith('https://wa.me/201012345678?text=', $url);
        // Ensure no unencoded spaces
        $this->assertStringNotContainsString(' ', $url);
        // rawurlencode encodes space as %20
        $this->assertStringContainsString('%20', $url);
    }

    public function test_campaign_creation_and_sequential_workflow(): void
    {
        $campaign = $this->service->createCampaign(
            $this->coachUser,
            'Test Sequential Campaign ' . uniqid(),
            'Hello {{name}}, this is a test.',
            ['subscription_status' => 'all']
        );

        $this->assertNotNull($campaign->id);
        $this->assertEquals(WhatsAppCampaign::STATUS_IN_PROGRESS, $campaign->status);

        // Fetch next recipient
        $first = $this->service->getNextPendingRecipient($campaign);
        if ($first) {
            $this->assertEquals(WhatsAppCampaignRecipient::STATUS_PENDING, $first->status);

            // Step 1: Mark Opened
            $this->service->markRecipientOpened($first);
            $first->refresh();
            $this->assertEquals(WhatsAppCampaignRecipient::STATUS_OPENED, $first->status);
            $this->assertNotNull($first->opened_at);

            // Step 2: Confirm Sent
            $this->service->markRecipientConfirmed($first, 'Sent verified by coach');
            $first->refresh();
            $this->assertEquals(WhatsAppCampaignRecipient::STATUS_CONFIRMED, $first->status);
            $this->assertNotNull($first->confirmed_at);
            $this->assertEquals('Sent verified by coach', $first->notes);

            $campaign->refresh();
            $this->assertGreaterThanOrEqual(1, $campaign->confirmed_count);

            // Test Skip on second recipient if available
            $second = $this->service->getNextPendingRecipient($campaign);
            if ($second) {
                $this->service->markRecipientSkipped($second, 'Trainee requested skip');
                $second->refresh();
                $this->assertEquals(WhatsAppCampaignRecipient::STATUS_SKIPPED, $second->status);
                $this->assertEquals('Trainee requested skip', $second->notes);
            }
        }

        // Test cancel
        $this->service->cancelCampaign($campaign);
        $campaign->refresh();
        $this->assertEquals(WhatsAppCampaign::STATUS_CANCELLED, $campaign->status);
    }

    public function test_coach_can_view_create_campaign_page_without_errors(): void
    {
        $response = $this->actingAs($this->coachUser, 'user')
            ->get(route('system.whatsapp-broadcast.create'));

        $response->assertStatus(200)
            ->assertSee(__('Create WhatsApp Broadcast Campaign'))
            ->assertSee(__('Eligible Trainees'));
    }

    public function test_coach_can_preview_recipients_via_ajax(): void
    {
        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.whatsapp-broadcast.preview-recipients'), [
                'subscription_status' => 'all',
                'coach_assigned' => 'all',
                'exclude_opt_out' => true,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'eligible_count',
                'excluded_count',
                'eligible',
                'excluded',
            ]);
    }

    public function test_trainee_cannot_access_coach_whatsapp_broadcast(): void
    {
        // Trainee user should be redirected or forbidden from whatsapp broadcast routes
        $response = $this->actingAs($this->traineeUser, 'user')
            ->get(route('system.whatsapp-broadcast.index'));

        // Permission middleware redirects trainee to trainer dashboard or aborts
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401, 403]));
    }
}
