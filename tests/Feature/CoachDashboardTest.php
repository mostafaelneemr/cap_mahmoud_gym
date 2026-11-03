<?php

namespace Tests\Feature;

use App\Enums\TraineesStatusEnum;
use App\Models\PermissionGroup;
use App\Models\Trainee;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CoachDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $coachUser;
    protected DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dashboardService = app(DashboardService::class);

        // Fetch or create a permission group
        $group = PermissionGroup::first();
        if (!$group) {
            $group = new PermissionGroup();
            $group->name = 'Admin Group';
            $group->is_active = '1';
            $group->save();
        }

        // Create a coach / admin user (user_type = 1)
        $this->coachUser = new User();
        $this->coachUser->name = 'Test Coach Admin';
        $this->coachUser->email = 'coach_test_' . bin2hex(random_bytes(4)) . '@gym.com';
        $this->coachUser->password = bcrypt('secret123');
        $this->coachUser->status = 'active';
        $this->coachUser->user_type = 1;
        $this->coachUser->permission_group_id = $group->id;
        $this->coachUser->save();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('system.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_trainee_user_type_is_redirected_to_trainee_dashboard(): void
    {
        $traineeUser = new User();
        $traineeUser->name = 'Trainee User';
        $traineeUser->email = 'trainee_test_' . bin2hex(random_bytes(4)) . '@gym.com';
        $traineeUser->password = bcrypt('secret123');
        $traineeUser->status = '1';
        $traineeUser->user_type = 2; // Trainee
        $traineeUser->save();

        $response = $this->actingAs($traineeUser, 'user')
            ->get(route('system.dashboard'));

        $response->assertRedirect(route('system.dashboard.trainer'));
    }

    public function test_coach_can_access_dashboard_and_receives_all_sections(): void
    {
        $response = $this->actingAs($this->coachUser, 'user')
            ->get(route('system.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('system.dashboard');

        // Check required view variables
        $response->assertViewHas('mainStats');
        $response->assertViewHas('trainingStats');
        $response->assertViewHas('subscriptionBreakdown');
        $response->assertViewHas('monthlyGrowth');
        $response->assertViewHas('pendingActions');
        $response->assertViewHas('recentActivities');

        // Verify HTML contents
        $response->assertSeeText(__('Coach Dashboard'));
        $response->assertSeeText(__('Total Trainees'));
        $response->assertSeeText(__('Active Trainees'));
        $response->assertSeeText(__('Training & Nutrition Program Indicators'));
        $response->assertSeeText(__('Subscription & Financial Overview'));
    }

    public function test_dashboard_service_handles_empty_database_without_errors(): void
    {
        // Test calculations when zero records match
        $mainStats = $this->dashboardService->getMainStats();
        $this->assertIsArray($mainStats);
        $this->assertArrayHasKey('total_trainees', $mainStats);
        $this->assertArrayHasKey('active_ratio_percentage', $mainStats);
        $this->assertArrayHasKey('growth_percentage', $mainStats);

        $trainingStats = $this->dashboardService->getTrainingNutritionStats();
        $this->assertIsArray($trainingStats);
        $this->assertArrayHasKey('workout_coverage_pct', $trainingStats);
        $this->assertArrayHasKey('nutrition_coverage_pct', $trainingStats);

        $subscriptionStats = $this->dashboardService->getSubscriptionBreakdown();
        $this->assertIsArray($subscriptionStats);
        $this->assertArrayHasKey('active_pct', $subscriptionStats);
    }

    public function test_dashboard_service_calculates_monthly_growth_chart_data(): void
    {
        $growthData = $this->dashboardService->getMonthlyGrowthChartData();

        $this->assertIsArray($growthData);
        $this->assertArrayHasKey('categories', $growthData);
        $this->assertArrayHasKey('series', $growthData);
        $this->assertCount(6, $growthData['categories']);
        $this->assertCount(6, $growthData['series']);
    }

    public function test_dashboard_service_action_items_structure(): void
    {
        $actionItems = $this->dashboardService->getPendingActionItems(5);

        $this->assertIsArray($actionItems);
        $this->assertArrayHasKey('missing_workouts', $actionItems);
        $this->assertArrayHasKey('pending_adjustments', $actionItems);
        $this->assertArrayHasKey('expiring_subscriptions', $actionItems);

        // Test newly added recent trainees and program activity structures
        $recentTrainees = $this->dashboardService->getRecentTrainees(5);
        $this->assertIsArray($recentTrainees);
        $this->assertArrayHasKey('recently_added', $recentTrainees);
        $this->assertArrayHasKey('recently_updated', $recentTrainees);

        $programActivity = $this->dashboardService->getRecentProgramActivity(5);
        $this->assertIsArray($programActivity);
        $this->assertArrayHasKey('recent_workouts', $programActivity);
        $this->assertArrayHasKey('recent_nutritions', $programActivity);
        $this->assertArrayHasKey('recent_checkins', $programActivity);

        $recentlyExpired = $this->dashboardService->getRecentlyExpiredSubscriptions(5);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $recentlyExpired);
    }

    public function test_main_stats_correctly_counts_trainees_and_subscriptions(): void
    {
        $user1 = User::create([
            'name' => 'Active Member 1',
            'email' => 'active1_' . bin2hex(random_bytes(3)) . '@test.com',
            'password' => bcrypt('123456'),
            'status' => '1',
            'user_type' => 2,
        ]);

        $trainee1 = Trainee::create([
            'user_id' => $user1->id,
            'email' => $user1->email,
            'status' => TraineesStatusEnum::Active->value,
            'membership_start' => Carbon::now()->subDays(10),
            'membership_end' => Carbon::now()->addDays(3), // Expiring in 3 days
        ]);

        $stats = $this->dashboardService->getMainStats();
        $this->assertGreaterThanOrEqual(1, $stats['total_trainees']);
        $this->assertGreaterThanOrEqual(1, $stats['active_trainees']);
        $this->assertGreaterThanOrEqual(1, $stats['expiring_soon_trainees']);
    }
}
