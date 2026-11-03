<?php

namespace App\Services;

use App\Models\InAppNotification;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\TraineeLoadAdjustmentItem;
use App\Models\TraineeProgressCheckin;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class InAppNotificationService extends BaseService
{
    /**
     * Dispatch an in-app notification safely without breaking core operations.
     */
    public function send($user, string $type, string $title, string $message, ?string $actionUrl = null, ?string $icon = null): ?InAppNotification
    {
        try {
            $userId = $user instanceof User ? $user->id : (int) $user;

            // Avoid duplicate identical unread notification sent within the last hour
            $recentDuplicate = InAppNotification::where('user_id', $userId)
                ->where('type', $type)
                ->where('title', $title)
                ->where('created_at', '>=', now()->subHour())
                ->unread()
                ->first();

            if ($recentDuplicate) {
                return $recentDuplicate;
            }

            return InAppNotification::create([
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $title,
                'message'    => $message,
                'action_url' => $actionUrl,
                'icon'       => $icon ?? 'ki-notification-status',
                'read_at'    => null,
            ]);
        } catch (Throwable $e) {
            Log::warning("Failed to create in-app notification: " . $e->getMessage(), [
                'type'  => $type,
                'title' => $title,
            ]);
            return null;
        }
    }

    /**
     * Count unread notifications for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return InAppNotification::forUser($userId)->unread()->count();
    }

    /**
     * Fetch recent notifications for header dropdown.
     */
    public function getRecentNotifications(int $userId, int $limit = 5): Collection
    {
        return InAppNotification::forUser($userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get paginated notifications for the dedicated page.
     */
    public function getPaginatedNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return InAppNotification::forUser($userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mark a single notification as read if it belongs to the user.
     */
    public function markAsRead(int|InAppNotification $notification, ?int $userId = null): bool
    {
        if ($notification instanceof InAppNotification) {
            return $notification->markAsRead();
        }

        $query = InAppNotification::where('id', $notification);
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }
        $model = $query->first();

        return $model ? $model->markAsRead() : false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return InAppNotification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Helper to retrieve active coaches and staff eligible to receive gym management notifications.
     */
    public function getEligibleCoaches(): Collection
    {
        return User::where(function ($q) {
                $q->where('user_type', 1)
                  ->orWhere('permission_group_id', 119);
            })
            ->where(function ($q) {
                $q->where('status', 1)
                  ->orWhere('status', '1')
                  ->orWhere('status', 'active');
            })
            ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Domain Notification Event Triggers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Trainee submits a load adjustment request → Notify Coach / Admin.
     */
    public function notifyCoachLoadAdjustmentSubmitted(TraineeLoadAdjustment $adjustment): void
    {
        $traineeName = $adjustment->trainee?->user?->name 
            ?? $adjustment->trainee?->name 
            ?? __('Trainee');
        $exerciseName = $adjustment->exercise_name ?? $adjustment->exercise?->name ?? __('Exercise');
        $workoutDay = $adjustment->workout_day_name ?? __('Workout Day');
        $setsCount = $adjustment->items()->count();

        $coaches = $this->getEligibleCoaches();
        if ($coaches->isEmpty()) {
            return;
        }

        $title = __('طلب تعديل أوزان جديد: :trainee', ['trainee' => $traineeName]);
        if ($setsCount > 0) {
            $message = __('أرسل المتدرب :trainee طلباً لتعديل أحمال :count مجموعات لتمرين (:exercise) في يوم (:day).', [
                'trainee'  => $traineeName,
                'count'    => $setsCount,
                'exercise' => $exerciseName,
                'day'      => $workoutDay,
            ]);
        } else {
            $message = __('أرسل المتدرب :trainee طلباً لتعديل أحمال تمرين (:exercise) في يوم (:day).', [
                'trainee'  => $traineeName,
                'exercise' => $exerciseName,
                'day'      => $workoutDay,
            ]);
        }

        $actionUrl = route('system.load-adjustment.index', ['request_id' => $adjustment->id]);

        foreach ($coaches as $coach) {
            $this->send(
                $coach,
                'load_adjustment_submitted',
                $title,
                $message,
                $actionUrl,
                'ki-weight'
            );
        }
    }

    /**
     * Coach reviews an individual set item → Notify Trainee.
     */
    public function notifyTraineeLoadAdjustmentItemReviewed(TraineeLoadAdjustmentItem $item): ?InAppNotification
    {
        $adjustment = $item->loadAdjustment;
        $traineeUser = $adjustment?->trainee?->user;
        if (!$traineeUser) {
            return null;
        }

        $exerciseName = $adjustment->exercise_name ?? $adjustment->exercise?->name ?? __('Exercise');
        $setNumber = $item->set_number;

        if ($item->status === TraineeLoadAdjustmentItem::STATUS_APPROVED) {
            $title = __('اعتماد تعديل الحمل: :exercise', ['exercise' => $exerciseName]);
            $isAlternative = ($item->approved_weight !== null && $item->approved_weight !== $item->suggested_weight);

            if ($isAlternative) {
                $message = __('وافق المدرب على تعديل المجموعة :set بوزن بديل (:weight) بدلاً من المقترح (:suggested).', [
                    'set'       => $setNumber,
                    'weight'    => $item->approved_weight,
                    'suggested' => $item->suggested_weight,
                ]);
            } else {
                $message = __('تم اعتماد الوزن المقترح (:weight) للمجموعة :set.', [
                    'set'    => $setNumber,
                    'weight' => $item->approved_weight,
                ]);
            }

            if (!empty($item->coach_notes)) {
                $message .= ' ' . __('ملاحظة المدرب: :notes', ['notes' => $item->coach_notes]);
            }

            return $this->send(
                $traineeUser,
                'load_adjustment_item_approved',
                $title,
                $message,
                route('system.dashboard.trainer'),
                'ki-check-circle'
            );
        } elseif ($item->status === TraineeLoadAdjustmentItem::STATUS_REJECTED) {
            $title = __('رفض تعديل الحمل: :exercise', ['exercise' => $exerciseName]);
            $message = __('تم رفض طلب تعديل وزن المجموعة :set وتثبيت الوزن الحالي.', [
                'set' => $setNumber,
            ]);

            if (!empty($item->coach_notes)) {
                $message .= ' ' . __('ملاحظة المدرب: :notes', ['notes' => $item->coach_notes]);
            }

            return $this->send(
                $traineeUser,
                'load_adjustment_item_rejected',
                $title,
                $message,
                route('system.dashboard.trainer'),
                'ki-cross-circle'
            );
        }

        return null;
    }

    /**
     * Coach reviews a load adjustment request (legacy bulk report) → Notify Trainee.
     */
    public function notifyTraineeLoadAdjustmentReviewed(TraineeLoadAdjustment $adjustment): void
    {
        $traineeUser = $adjustment->trainee?->user;
        if (!$traineeUser) {
            return;
        }

        $exerciseName = $adjustment->exercise_name ?? $adjustment->exercise?->name ?? __('Exercise');
        $statusLang = $adjustment->status === 'reviewed' ? __('approved') : __('updated');

        $this->send(
            $traineeUser,
            'load_adjustment_reviewed',
            __('Exercise Load Reviewed'),
            __('Your coach has reviewed your load adjustment request for :exercise (:status)', [
                'exercise' => $exerciseName,
                'status'   => $statusLang,
            ]),
            route('system.dashboard.trainer'),
            'ki-weight'
        );
    }

    /**
     * Coach updates workout plan → Notify Trainee.
     */
    public function notifyTraineeWorkoutUpdated(Trainee $trainee, string $workoutName = ''): void
    {
        $user = $trainee->user;
        if (!$user) {
            return;
        }

        $this->send(
            $user,
            'workout_plan_updated',
            __('Workout Program Updated'),
            __('Your coach has updated your training routine. Check your updated workout plan!'),
            route('system.dashboard.trainer'),
            'ki-calendar'
        );
    }

    /**
     * Coach updates nutrition plan → Notify Trainee.
     */
    public function notifyTraineeNutritionUpdated(Trainee $trainee, string $planName = ''): void
    {
        $user = $trainee->user;
        if (!$user) {
            return;
        }

        $this->send(
            $user,
            'nutrition_plan_updated',
            __('Nutrition Plan Updated'),
            __('Your coach has updated your nutrition plan and daily meals.'),
            route('system.nutrition.my-plan'),
            'ki-cup'
        );
    }

    /**
     * Trainee submits progress check-in → Notify Coach.
     */
    public function notifyCoachProgressSubmitted(TraineeProgressCheckin $checkin): void
    {
        $traineeName = $checkin->trainee?->user?->name ?? __('Trainee');
        $coaches = $this->getEligibleCoaches();

        foreach ($coaches as $coach) {
            $this->send(
                $coach,
                'progress_checkin_submitted',
                __('New Progress Check-in'),
                __(':trainee has submitted a new weekly progress check-in with measurements and photos.', [
                    'trainee' => $traineeName,
                ]),
                route('system.trainee-progress.index') . '?trainee_id=' . $checkin->trainee_id,
                'ki-camera'
            );
        }
    }

    /**
     * Coach updates feedback notes on progress → Notify Trainee.
     */
    public function notifyTraineeCoachNotesUpdated(TraineeProgressCheckin $checkin): void
    {
        $user = $checkin->trainee?->user;
        if (!$user) {
            return;
        }

        $this->send(
            $user,
            'progress_notes_updated',
            __('Coach Notes on Your Progress'),
            __('Your coach has added guidance notes to your latest progress check-in.'),
            route('system.trainee-progress.index'),
            'ki-message-text-2'
        );
    }

    /**
     * Subscription expiring in 7 days alert → Notify Trainee.
     */
    public function notifyTraineeSubscriptionExpiring(Trainee $trainee, int $daysLeft = 7): void
    {
        $user = $trainee->user;
        if (!$user) {
            return;
        }

        $this->send(
            $user,
            'subscription_expiring_soon',
            __('Membership Expiring Soon'),
            __('Your gym membership will expire in :days days. Please renew to keep your workout routines active!', [
                'days' => $daysLeft,
            ]),
            route('system.dashboard.trainer'),
            'ki-calendar'
        );
    }
}
