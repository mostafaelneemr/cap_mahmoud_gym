<?php

namespace App\Services;

use App\Enums\TraineesStatusEnum;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WhatsAppBroadcastService extends BaseService
{
    /**
     * Normalize and validate phone numbers supporting Egyptian and international E.164 formats.
     *
     * @param string|null $phone
     * @return string|null Normalized phone with country prefix (digits only) or null if invalid
     */
    public function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // 1. Remove all non-digit characters (spaces, dashes, parentheses, +, etc.)
        $digits = preg_replace('/\D+/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // 2. Strip international exit code prefix (00)
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // 3. Handle Egyptian mobile numbers:
        // Local 11-digit numbers starting with 010, 011, 012, 015
        if (strlen($digits) === 11 && preg_match('/^01[0125]\d{8}$/', $digits)) {
            return '20' . substr($digits, 1);
        }

        // 10-digit Egyptian numbers missing leading 0 (10..., 11..., 12..., 15...)
        if (strlen($digits) === 10 && preg_match('/^1[0125]\d{8}$/', $digits)) {
            return '20' . $digits;
        }

        // 12-digit Egyptian numbers already carrying 20 country code
        if (strlen($digits) === 12 && preg_match('/^201[0125]\d{8}$/', $digits)) {
            return $digits;
        }

        // 4. Handle other International numbers (E.164 length between 10 and 15 digits)
        // Ensure it doesn't start with 0
        $digits = ltrim($digits, '0');
        $len = strlen($digits);

        if ($len >= 10 && $len <= 15) {
            return $digits;
        }

        return null;
    }

    /**
     * Render message template with approved safe personalization placeholders.
     */
    public function renderPersonalizedMessage(string $template, array $recipientData): string
    {
        $name = trim($recipientData['name'] ?? __('Trainee'));
        $traineeId = (string) ($recipientData['id'] ?? $recipientData['trainee_id'] ?? '');

        $rendered = str_replace(
            ['{{name}}', '{{id}}'],
            [$name, $traineeId],
            $template
        );

        return trim($rendered);
    }

    /**
     * Alias for renderPersonalizedMessage.
     */
    public function renderMessage(string $template, array $recipientData): string
    {
        return $this->renderPersonalizedMessage($template, $recipientData);
    }

    /**
     * Evaluate trainee audience and split into eligible and excluded recipients with reasons.
     *
     * @param array $filters
     * @return array ['eligible' => Collection, 'excluded' => Collection]
     */
    public function evaluateRecipients(array $filters = []): array
    {
        $query = Trainee::with('user');

        $filterType = $filters['filter_type'] ?? 'all_active';

        if (!empty($filters['trainee_ids']) && is_array($filters['trainee_ids'])) {
            $query->whereIn('id', $filters['trainee_ids']);
        } else {
            switch ($filterType) {
                case 'expiring_soon':
                    $now = Carbon::now();
                    $query->where('status', TraineesStatusEnum::Active->value)
                          ->whereNotNull('membership_end')
                          ->whereBetween('membership_end', [
                              $now->copy()->startOfDay(),
                              $now->copy()->addDays(7)->endOfDay(),
                          ]);
                    break;

                case 'missing_workout':
                    $query->where('status', TraineesStatusEnum::Active->value)
                          ->whereDoesntHave('activeWorkoutPlans');
                    break;

                case 'all_active':
                default:
                    $query->where('status', TraineesStatusEnum::Active->value);
                    break;
            }
        }

        $trainees = $query->get();

        $eligible = collect();
        $excluded = collect();

        foreach ($trainees as $trainee) {
            $name = $trainee->user?->name ?? $trainee->name ?? __('Trainee #' . $trainee->id);
            $rawPhone = $trainee->mobile ?: ($trainee->user?->mobile ?: null);

            // 1. Consent / Opt-out check
            if (!empty($trainee->whatsapp_opt_out)) {
                $excluded->push([
                    'trainee'   => $trainee,
                    'name'      => $name,
                    'phone_raw' => $rawPhone,
                    'reason'    => __('Recipient opted out of WhatsApp communication'),
                ]);
                continue;
            }

            // 2. Active status check
            if ($trainee->status !== TraineesStatusEnum::Active->value) {
                $excluded->push([
                    'trainee'   => $trainee,
                    'name'      => $name,
                    'phone_raw' => $rawPhone,
                    'reason'    => __('Trainee membership is not active (:status)', ['status' => __($trainee->status)]),
                ]);
                continue;
            }

            // 3. Phone presence check
            if (empty($rawPhone)) {
                $excluded->push([
                    'trainee'   => $trainee,
                    'name'      => $name,
                    'phone_raw' => null,
                    'reason'    => __('Phone number is missing'),
                ]);
                continue;
            }

            // 4. Phone normalization and validity check
            $normalizedPhone = $this->normalizePhoneNumber($rawPhone);
            if (empty($normalizedPhone)) {
                $excluded->push([
                    'trainee'   => $trainee,
                    'name'      => $name,
                    'phone_raw' => $rawPhone,
                    'reason'    => __('Invalid phone number format'),
                ]);
                continue;
            }

            // Successfully passed all eligibility checks
            $eligible->push([
                'trainee'          => $trainee,
                'id'               => $trainee->id,
                'name'             => $name,
                'phone_raw'        => $rawPhone,
                'phone_normalized' => $normalizedPhone,
            ]);
        }

        return [
            'eligible' => $eligible,
            'excluded' => $excluded,
        ];
    }

    /**
     * Create a new broadcast campaign and populate recipient records.
     */
    public function createCampaign(User $coach, array|string $dataOrTitle, ?string $template = null, array $filters = []): WhatsAppCampaign
    {
        if (is_string($dataOrTitle)) {
            $data = array_merge($filters, [
                'title' => $dataOrTitle,
                'message_template' => $template,
            ]);
        } else {
            $data = $dataOrTitle;
        }

        $title = trim($data['title'] ?? '');
        $template = trim($data['message_template'] ?? '');

        if (empty($title)) {
            throw new InvalidArgumentException(__('Campaign title is required.'));
        }
        if (empty($template)) {
            throw new InvalidArgumentException(__('Message template is required.'));
        }

        return DB::transaction(function () use ($coach, $data, $title, $template) {
            $evaluation = $this->evaluateRecipients($data, $coach);
            $eligible = collect($evaluation['eligible']);

            if ($eligible->isEmpty()) {
                throw new InvalidArgumentException(__('No eligible recipients found for this campaign.'));
            }

            $campaign = WhatsAppCampaign::create([
                'coach_id'         => $coach->id,
                'title'            => $title,
                'message_template' => $template,
                'total_recipients' => $eligible->count(),
                'opened_count'     => 0,
                'confirmed_count'  => 0,
                'skipped_count'    => 0,
                'status'           => WhatsAppCampaign::STATUS_IN_PROGRESS,
                'started_at'       => now(),
                'completed_at'     => null,
            ]);

            foreach ($eligible as $item) {
                $personalizedMessage = $this->renderPersonalizedMessage($template, [
                    'id'   => $item['id'],
                    'name' => $item['name'],
                ]);

                WhatsAppCampaignRecipient::create([
                    'campaign_id'      => $campaign->id,
                    'trainee_id'       => $item['id'],
                    'recipient_name'   => $item['name'],
                    'phone_raw'        => $item['phone_raw'],
                    'phone_normalized' => $item['phone_normalized'],
                    'message_rendered' => $personalizedMessage,
                    'status'           => WhatsAppCampaignRecipient::STATUS_PENDING,
                ]);
            }

            return $campaign;
        });
    }

    /**
     * Get the next pending recipient in the sequential campaign workflow.
     */
    public function getNextPendingRecipient(WhatsAppCampaign $campaign): ?WhatsAppCampaignRecipient
    {
        return $campaign->recipients()
            ->whereIn('status', [
                WhatsAppCampaignRecipient::STATUS_PENDING,
                WhatsAppCampaignRecipient::STATUS_OPENED
            ])
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Mark a recipient conversation as opened via wa.me link.
     */
    public function markRecipientOpened(WhatsAppCampaignRecipient $recipient): void
    {
        $recipient->opened_at = now();
        if ($recipient->status === WhatsAppCampaignRecipient::STATUS_PENDING) {
            $recipient->status = WhatsAppCampaignRecipient::STATUS_OPENED;
        }
        $recipient->save();

        $campaign = $recipient->campaign;
        if ($campaign) {
            $campaign->syncCounters();
        }
    }

    /**
     * Record coach's manual confirmation that the message was sent inside WhatsApp.
     */
    public function markRecipientConfirmed(WhatsAppCampaignRecipient $recipient, ?string $notes = null): void
    {
        $recipient->status = WhatsAppCampaignRecipient::STATUS_CONFIRMED;
        $recipient->confirmed_at = now();
        if ($notes !== null) {
            $recipient->notes = $notes;
        }
        $recipient->save();

        $campaign = $recipient->campaign;
        if ($campaign) {
            $campaign->syncCounters();
        }
    }

    /**
     * Mark recipient as skipped by the coach.
     */
    public function markRecipientSkipped(WhatsAppCampaignRecipient $recipient, ?string $notes = null): void
    {
        $recipient->status = WhatsAppCampaignRecipient::STATUS_SKIPPED;
        if ($notes !== null) {
            $recipient->notes = $notes;
        }
        $recipient->save();

        $campaign = $recipient->campaign;
        if ($campaign) {
            $campaign->syncCounters();
        }
    }

    /**
     * Cancel / stop an active campaign.
     */
    public function cancelCampaign(WhatsAppCampaign $campaign): void
    {
        $campaign->status = WhatsAppCampaign::STATUS_CANCELLED;
        $campaign->completed_at = now();
        $campaign->save();

        $campaign->recipients()
            ->whereIn('status', [
                WhatsAppCampaignRecipient::STATUS_PENDING,
                WhatsAppCampaignRecipient::STATUS_OPENED,
            ])
            ->update([
                'status' => WhatsAppCampaignRecipient::STATUS_SKIPPED,
                'notes'  => __('Cancelled with campaign'),
            ]);

        $campaign->syncCounters();
    }
}