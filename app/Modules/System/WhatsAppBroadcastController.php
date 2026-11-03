<?php

namespace App\Modules\System;

use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Services\WhatsAppBroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppBroadcastController extends SystemController
{
    protected WhatsAppBroadcastService $whatsappService;

    public function __construct(WhatsAppBroadcastService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Ensure only Coach / Admin (user_type == 1) can access.
     */
    protected function authorizeCoach(): \App\Models\User
    {
        $user = Auth::guard('user')->user();
        if (!$user || ($user->user_type != 1 && $user->user_type !== null)) {
            abort(403, __('Unauthorized. Coach access only.'));
        }
        return $user;
    }

    /**
     * List all broadcast campaigns.
     */
    public function index(Request $request)
    {
        $this->authorizeCoach();

        $campaigns = WhatsAppCampaign::with('coach')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $this->viewData['breadcrumb'][] = ['text' => __('Communications')];
        $this->viewData['breadcrumb'][] = ['text' => __('WhatsApp Broadcast')];
        $this->viewData['pageTitle'] = __('WhatsApp Broadcast Campaigns');
        $this->viewData['campaigns'] = $campaigns;

        return $this->view('whatsapp-broadcast.index', $this->viewData);
    }

    /**
     * Show form to create a new broadcast campaign.
     */
    public function create(Request $request)
    {
        $user = $this->authorizeCoach();

        // Default evaluation
        $evaluation = $this->whatsappService->evaluateRecipients([
            'subscription_status' => 'all',
            'coach_assigned' => 'all',
            'exclude_opt_out' => true,
        ], $user);

        $this->viewData['breadcrumb'][] = ['text' => __('WhatsApp Broadcast'), 'url' => route('system.whatsapp-broadcast.index')];
        $this->viewData['breadcrumb'][] = ['text' => __('New Campaign')];
        $this->viewData['pageTitle'] = __('Create WhatsApp Broadcast Campaign');
        $this->viewData['evaluation'] = $evaluation;

        return $this->view('whatsapp-broadcast.create', $this->viewData);
    }

    /**
     * AJAX endpoint to preview recipients based on filter selection.
     */
    public function previewRecipients(Request $request)
    {
        $user = $this->authorizeCoach();

        $filters = [
            'subscription_status' => $request->get('subscription_status', 'all'),
            'coach_assigned' => $request->get('coach_assigned', 'all'),
            'exclude_opt_out' => $request->boolean('exclude_opt_out', true),
        ];

        $evaluation = $this->whatsappService->evaluateRecipients($filters, $user);

        return response()->json([
            'status' => true,
            'eligible_count' => collect($evaluation['eligible'])->count(),
            'excluded_count' => collect($evaluation['excluded'])->count(),
            'eligible' => collect($evaluation['eligible'])->take(50)->values(),
            'excluded' => collect($evaluation['excluded'])->take(50)->values(),
        ]);
    }

    /**
     * Store and start a new campaign.
     */
    public function store(Request $request)
    {
        $user = $this->authorizeCoach();

        $request->validate([
            'title' => 'required|string|max:255',
            'message_template' => 'required|string|min:3',
            'subscription_status' => 'nullable|in:all,active,expired,none',
            'coach_assigned' => 'nullable|in:all,only_mine',
            'exclude_opt_out' => 'nullable|boolean',
        ]);

        $filters = [
            'subscription_status' => $request->get('subscription_status', 'all'),
            'coach_assigned' => $request->get('coach_assigned', 'all'),
            'exclude_opt_out' => $request->boolean('exclude_opt_out', true),
        ];

        $evaluation = $this->whatsappService->evaluateRecipients($filters, $user);

        if (collect($evaluation['eligible'])->isEmpty()) {
            return redirect()->back()
                ->withInput()
                ->with('msg', __('No eligible recipients found matching the chosen criteria.'))
                ->with('type', 'danger');
        }

        $campaign = $this->whatsappService->createCampaign(
            $user,
            $request->input('title'),
            $request->input('message_template'),
            $filters
        );

        return redirect()->route('system.whatsapp-broadcast.process', $campaign->id)
            ->with('msg', __('Campaign created. You can now begin sending messages sequentially.'))
            ->with('type', 'success');
    }

    /**
     * Show interactive sequential processing screen.
     */
    public function process(Request $request, $id)
    {
        $this->authorizeCoach();

        $campaign = WhatsAppCampaign::with('recipients')->findOrFail($id);

        $nextRecipient = $this->whatsappService->getNextPendingRecipient($campaign);

        // If no more pending recipients, complete campaign and show summary
        if (!$nextRecipient) {
            if ($campaign->status === WhatsAppCampaign::STATUS_IN_PROGRESS || $campaign->status === WhatsAppCampaign::STATUS_DRAFT) {
                $campaign->status = WhatsAppCampaign::STATUS_COMPLETED;
                $campaign->completed_at = now();
                $campaign->save();
            }

            return redirect()->route('system.whatsapp-broadcast.show', $campaign->id)
                ->with('msg', __('All recipients in this campaign have been processed.'))
                ->with('type', 'success');
        }

        // Calculate progress
        $processedCount = $campaign->recipients()
            ->whereIn('status', [
                WhatsAppCampaignRecipient::STATUS_CONFIRMED,
                WhatsAppCampaignRecipient::STATUS_SKIPPED,
            ])->count();
        $currentIndex = $processedCount + 1;
        $totalCount = $campaign->total_recipients > 0 ? $campaign->total_recipients : 1;
        $progressPercent = min(100, (int) round(($processedCount / $totalCount) * 100));

        $this->viewData['breadcrumb'][] = ['text' => __('WhatsApp Broadcast'), 'url' => route('system.whatsapp-broadcast.index')];
        $this->viewData['breadcrumb'][] = ['text' => $campaign->title, 'url' => route('system.whatsapp-broadcast.show', $campaign->id)];
        $this->viewData['breadcrumb'][] = ['text' => __('Sending Process')];
        $this->viewData['pageTitle'] = __('Send WhatsApp Messages: :title', ['title' => $campaign->title]);
        $this->viewData['campaign'] = $campaign;
        $this->viewData['recipient'] = $nextRecipient;
        $this->viewData['currentIndex'] = $currentIndex;
        $this->viewData['totalCount'] = $totalCount;
        $this->viewData['progressPercent'] = $progressPercent;

        return $this->view('whatsapp-broadcast.process', $this->viewData);
    }

    /**
     * Action on recipient: mark opened, confirm sent, or skip.
     */
    public function action(Request $request, $id)
    {
        $this->authorizeCoach();

        $campaign = WhatsAppCampaign::findOrFail($id);
        $recipientId = $request->input('recipient_id');
        $action = $request->input('action'); // 'open', 'confirm', 'skip'
        $notes = $request->input('notes');

        $recipient = $campaign->recipients()->where('id', $recipientId)->firstOrFail();

        switch ($action) {
            case 'open':
                $this->whatsappService->markRecipientOpened($recipient);
                break;
            case 'confirm':
                $this->whatsappService->markRecipientConfirmed($recipient, $notes);
                break;
            case 'skip':
                $this->whatsappService->markRecipientSkipped($recipient, $notes);
                break;
            default:
                abort(400, 'Invalid action');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'action' => $action,
                'recipient_id' => $recipient->id,
                'campaign_id' => $campaign->id,
            ]);
        }

        return redirect()->route('system.whatsapp-broadcast.process', $campaign->id);
    }

    /**
     * View completed or in-progress campaign details and recipient audit list.
     */
    public function show(Request $request, $id)
    {
        $this->authorizeCoach();

        $campaign = WhatsAppCampaign::with(['coach', 'recipients' => function ($q) {
            $q->orderBy('id', 'asc');
        }])->findOrFail($id);

        $this->viewData['breadcrumb'][] = ['text' => __('WhatsApp Broadcast'), 'url' => route('system.whatsapp-broadcast.index')];
        $this->viewData['breadcrumb'][] = ['text' => $campaign->title];
        $this->viewData['pageTitle'] = $campaign->title;
        $this->viewData['campaign'] = $campaign;

        return $this->view('whatsapp-broadcast.show', $this->viewData);
    }

    /**
     * Cancel campaign.
     */
    public function cancel(Request $request, $id)
    {
        $this->authorizeCoach();

        $campaign = WhatsAppCampaign::findOrFail($id);
        $this->whatsappService->cancelCampaign($campaign);

        return redirect()->route('system.whatsapp-broadcast.show', $campaign->id)
            ->with('msg', __('Campaign was cancelled. Remaining pending recipients were marked skipped.'))
            ->with('type', 'warning');
    }
}

