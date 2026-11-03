<?php

namespace App\Services;

use App\Enums\TraineesStatusEnum;
use App\Models\NutritionPlan;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\TraineeProgressCheckin;
use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardService extends BaseService
{
    /**
     * Gather all aggregated data for the Coach Dashboard.
     *
     * @return array
     */
    public function getAllDashboardData(): array
    {
        $mainStats = $this->getMainStats();
        $trainingStats = $this->getTrainingNutritionStats();
        $subscriptionBreakdown = $this->getSubscriptionBreakdown();
        $monthlyGrowth = $this->getMonthlyGrowthChartData();
        $pendingActions = $this->getPendingActionItems(5);
        $recentActivities = $this->getRecentActivities(6);
        $recentTrainees = $this->getRecentTrainees(5);
        $programActivity = $this->getRecentProgramActivity(5);
        $recentlyExpired = $this->getRecentlyExpiredSubscriptions(5);

        return [
            'mainStats'             => $mainStats,
            'trainingStats'         => $trainingStats,
            'subscriptionBreakdown' => $subscriptionBreakdown,
            'monthlyGrowth'         => $monthlyGrowth,
            'pendingActions'        => $pendingActions,
            'recentActivities'      => $recentActivities,
            'recentTrainees'        => $recentTrainees,
            'programActivity'       => $programActivity,
            'recentlyExpired'       => $recentlyExpired,
            'websiteUrl'            => env('APP_URL', url('/')),
        ];
    }

    /**
     * Calculate core trainee metrics and growth rates.
     */
    public function getMainStats(): array
    {
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

        $totalTrainees = Trainee::count();
        $activeTrainees = Trainee::where('status', TraineesStatusEnum::Active->value)->count();
        $inactiveTrainees = Trainee::where('status', TraineesStatusEnum::Inactive->value)->count();

        // Expired memberships
        $expiredTrainees = Trainee::where(function ($q) use ($now) {
            $q->where('status', TraineesStatusEnum::Expired->value)
              ->orWhere(function ($sub) use ($now) {
                  $sub->whereNotNull('membership_end')
                      ->where('membership_end', '<', $now->toDateTimeString());
              });
        })->count();

        // Expiring in next 7 days
        $expiringSoon = Trainee::where('status', TraineesStatusEnum::Active->value)
            ->whereNotNull('membership_end')
            ->whereBetween('membership_end', [
                $now->copy()->startOfDay()->toDateTimeString(),
                $now->copy()->addDays(7)->endOfDay()->toDateTimeString(),
            ])->count();

        // Growth metrics
        $newThisMonth = Trainee::where('created_at', '>=', $startOfThisMonth)->count();
        $newLastMonth = Trainee::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $growthPercentage = 0;
        if ($newLastMonth > 0) {
            $growthPercentage = round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1);
        } elseif ($newThisMonth > 0) {
            $growthPercentage = 100;
        }

        return [
            'total_trainees'           => $totalTrainees,
            'active_trainees'          => $activeTrainees,
            'inactive_trainees'        => $inactiveTrainees,
            'expired_trainees'         => $expiredTrainees,
            'expiring_soon_trainees'   => $expiringSoon,
            'new_this_month'           => $newThisMonth,
            'new_last_month'           => $newLastMonth,
            'growth_percentage'        => $growthPercentage,
            'active_ratio_percentage'  => $totalTrainees > 0 ? round(($activeTrainees / $totalTrainees) * 100, 1) : 0,
        ];
    }

    /**
     * Compute training, load adjustments, and nutrition program indicators.
     */
    public function getTrainingNutritionStats(): array
    {
        $activeTraineesCount = Trainee::where('status', TraineesStatusEnum::Active->value)->count();

        // Trainees with active workout plans
        $withActiveWorkout = Trainee::where('status', TraineesStatusEnum::Active->value)
            ->whereHas('activeWorkoutPlans')
            ->count();

        $missingWorkout = $activeTraineesCount - $withActiveWorkout;
        if ($missingWorkout < 0) {
            $missingWorkout = 0;
        }

        $workoutCoveragePct = $activeTraineesCount > 0
            ? round(($withActiveWorkout / $activeTraineesCount) * 100, 1)
            : 0;

        // Trainees with active nutrition plans
        $withActiveNutrition = Trainee::where('status', TraineesStatusEnum::Active->value)
            ->whereHas('activeNutritionPlans')
            ->count();

        $nutritionCoveragePct = $activeTraineesCount > 0
            ? round(($withActiveNutrition / $activeTraineesCount) * 100, 1)
            : 0;

        // Pending exercise load adjustments submitted by trainees
        $pendingLoadAdjustmentsCount = 0;
        if (class_exists(TraineeLoadAdjustment::class)) {
            $pendingLoadAdjustmentsCount = TraineeLoadAdjustment::pending()->count();
        }

        // Active trainees needing check-in (no check-in in the last 14 days)
        $fourteenDaysAgo = Carbon::now()->subDays(14)->toDateString();
        $traineesNeedingCheckin = Trainee::where('status', TraineesStatusEnum::Active->value)
            ->where(function ($q) use ($fourteenDaysAgo) {
                $q->whereDoesntHave('progressCheckins')
                  ->orWhereDoesntHave('progressCheckins', function ($sub) use ($fourteenDaysAgo) {
                      $sub->where('checkin_date', '>=', $fourteenDaysAgo);
                  });
            })->count();

        return [
            'active_trainees'              => $activeTraineesCount,
            'with_active_workout'          => $withActiveWorkout,
            'missing_workout'              => $missingWorkout,
            'workout_coverage_pct'         => $workoutCoveragePct,
            'with_active_nutrition'        => $withActiveNutrition,
            'nutrition_coverage_pct'       => $nutritionCoveragePct,
            'pending_load_adjustments'     => $pendingLoadAdjustmentsCount,
            'trainees_needing_checkin'     => $traineesNeedingCheckin,
        ];
    }

    /**
     * Subscription status distribution.
     */
    public function getSubscriptionBreakdown(): array
    {
        $now = Carbon::now();

        $active = Trainee::where('status', TraineesStatusEnum::Active->value)->count();
        $inactive = Trainee::where('status', TraineesStatusEnum::Inactive->value)->count();
        $expired = Trainee::where('status', TraineesStatusEnum::Expired->value)
            ->orWhere(function ($q) use ($now) {
                $q->whereNotNull('membership_end')
                  ->where('membership_end', '<', $now);
            })->count();

        $expiring7Days = Trainee::where('status', TraineesStatusEnum::Active->value)
            ->whereNotNull('membership_end')
            ->whereBetween('membership_end', [
                $now->copy()->startOfDay(),
                $now->copy()->addDays(7)->endOfDay(),
            ])->count();

        $total = $active + $inactive + $expired;

        return [
            'active'         => $active,
            'expiring_soon'  => $expiring7Days,
            'expired'        => $expired,
            'inactive'       => $inactive,
            'total'          => $total,
            'active_pct'     => $total > 0 ? round(($active / $total) * 100, 1) : 0,
            'expired_pct'    => $total > 0 ? round(($expired / $total) * 100, 1) : 0,
            'expiring_pct'   => $total > 0 ? round(($expiring7Days / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Trainee registration trend over the last 6 calendar months.
     */
    public function getMonthlyGrowthChartData(): array
    {
        $months = [];
        $categories = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $yearMonth = $monthDate->format('Y-m');
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();

            $monthName = $monthDate->translatedFormat('M Y');
            $categories[] = $monthName;

            $count = Trainee::whereBetween('created_at', [$start, $end])->count();
            $counts[] = $count;
        }

        return [
            'categories' => $categories,
            'series'     => $counts,
        ];
    }

    /**
     * Retrieve actionable items requiring immediate coach review.
     */
    public function getPendingActionItems(int $limit = 5): array
    {
        // 1. Trainees with missing workout plans
        $missingWorkouts = Trainee::with('user')
            ->where('status', TraineesStatusEnum::Active->value)
            ->whereDoesntHave('activeWorkoutPlans')
            ->latest()
            ->take($limit)
            ->get();

        // 2. Pending load adjustment requests from trainees
        $pendingAdjustments = collect();
        if (class_exists(TraineeLoadAdjustment::class)) {
            $pendingAdjustments = TraineeLoadAdjustment::with(['trainee.user', 'exercise'])
                ->pending()
                ->latest()
                ->take($limit)
                ->get();
        }

        // 3. Subscriptions expiring soon (next 7 days)
        $now = Carbon::now();
        $expiringSubscriptions = Trainee::with('user')
            ->where('status', TraineesStatusEnum::Active->value)
            ->whereNotNull('membership_end')
            ->whereBetween('membership_end', [
                $now->copy()->startOfDay(),
                $now->copy()->addDays(7)->endOfDay(),
            ])
            ->orderBy('membership_end', 'asc')
            ->take($limit)
            ->get();

        return [
            'missing_workouts'       => $missingWorkouts,
            'pending_adjustments'    => $pendingAdjustments,
            'expiring_subscriptions' => $expiringSubscriptions,
        ];
    }

    /**
     * Fetch recent activity logs.
     */
    public function getRecentActivities(int $limit = 6)
    {
        try {
            return Activity::with('causer')
                ->latest()
                ->take($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Retrieve recently added and recently updated trainees.
     */
    public function getRecentTrainees(int $limit = 5): array
    {
        $recentlyAdded = Trainee::with('user')
            ->latest('id')
            ->take($limit)
            ->get();

        $recentlyUpdated = Trainee::with('user')
            ->latest('updated_at')
            ->take($limit)
            ->get();

        return [
            'recently_added'   => $recentlyAdded,
            'recently_updated' => $recentlyUpdated,
        ];
    }

    /**
     * Retrieve recent workout, nutrition, and progress activities.
     */
    public function getRecentProgramActivity(int $limit = 5): array
    {
        $recentWorkouts = collect();
        if (class_exists(WorkoutPlan::class)) {
            $recentWorkouts = WorkoutPlan::with('trainee.user')
                ->latest()
                ->take($limit)
                ->get();
        }

        $recentNutritions = collect();
        if (class_exists(NutritionPlan::class)) {
            $recentNutritions = NutritionPlan::with('trainee.user')
                ->latest()
                ->take($limit)
                ->get();
        }

        $recentCheckins = collect();
        if (class_exists(TraineeProgressCheckin::class)) {
            $recentCheckins = TraineeProgressCheckin::with(['trainee.user', 'photos'])
                ->latest()
                ->take($limit)
                ->get();
        }

        return [
            'recent_workouts'   => $recentWorkouts,
            'recent_nutritions' => $recentNutritions,
            'recent_checkins'   => $recentCheckins,
        ];
    }

    /**
     * Retrieve recently expired subscriptions for renewal follow-up.
     */
    public function getRecentlyExpiredSubscriptions(int $limit = 5)
    {
        $now = Carbon::now();

        return Trainee::with('user')
            ->where(function ($q) use ($now) {
                $q->where('status', TraineesStatusEnum::Expired->value)
                  ->orWhere(function ($sub) use ($now) {
                      $sub->whereNotNull('membership_end')
                          ->where('membership_end', '<', $now);
                  });
            })
            ->orderBy('membership_end', 'desc')
            ->take($limit)
            ->get();
    }
}

