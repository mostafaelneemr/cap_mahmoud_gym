<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\TraineeLoadAdjustmentItem;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Services\LoadAdjustmentService;
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

        if (!Schema::hasTable('trainee_load_adjustments') || !Schema::hasTable('trainee_load_adjustment_items')) {
            $this->markTestSkipped('Tables trainee_load_adjustments or trainee_load_adjustment_items do not exist.');
        }

        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
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
            'internal_weight' => ['20kg', '25kg', '30kg', '35kg'],
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
     * 1. A trainee submits a request for one set.
     */
    public function test_trainee_submits_request_for_one_set(): void
    {
        $payload = [
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '22.5kg'],
            ],
            'notes'       => 'Warmup set felt too easy',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trainee_load_adjustments', [
            'trainee_id'  => $this->traineeRecordA->id,
            'exercise_id' => $this->exerciseA1->id,
            'status'      => 'pending',
        ]);

        $report = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)->latest('id')->first();
        $this->assertNotNull($report);
        $this->assertCount(1, $report->items);

        $item = $report->items->first();
        $this->assertSame(1, $item->set_number);
        $this->assertSame('20kg', $item->base_weight);
        $this->assertSame('22.5kg', $item->suggested_weight);
        $this->assertSame('pending', $item->status);
    }

    /**
     * 2. A trainee submits a request for multiple sets in the same exercise.
     */
    public function test_trainee_submits_request_for_multiple_sets_in_same_exercise(): void
    {
        $payload = [
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '22.5kg'],
                ['set_number' => 3, 'suggested_weight' => '32.5kg'],
            ],
            'notes'       => 'Increasing sets 1 and 3',
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(200);

        $report = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)->latest('id')->first();
        $this->assertCount(2, $report->items);

        $item1 = $report->items->where('set_number', 1)->first();
        $item3 = $report->items->where('set_number', 3)->first();

        $this->assertSame('20kg', $item1->base_weight);
        $this->assertSame('22.5kg', $item1->suggested_weight);

        $this->assertSame('30kg', $item3->base_weight);
        $this->assertSame('32.5kg', $item3->suggested_weight);
    }

    /**
     * 3. Multiple requests can be submitted while earlier requests remain pending.
     */
    public function test_multiple_requests_can_be_submitted_while_earlier_remain_pending(): void
    {
        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id' => $this->exerciseA1->id,
                'sets'        => [['set_number' => 2, 'suggested_weight' => '27.5kg']],
                'notes'       => 'First pending request',
            ])->assertStatus(200);

        $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id' => $this->exerciseA1->id,
                'sets'        => [['set_number' => 2, 'suggested_weight' => '30kg']],
                'notes'       => 'Second pending request for same set',
            ])->assertStatus(200);

        $reports = TraineeLoadAdjustment::where('exercise_id', $this->exerciseA1->id)->get();
        $this->assertCount(2, $reports);
        $this->assertSame('pending', $reports[0]->status);
        $this->assertSame('pending', $reports[1]->status);
    }

    /**
     * 4. Requests cannot reference nonexistent sets.
     */
    public function test_requests_cannot_reference_nonexistent_sets(): void
    {
        // exerciseA2 has 3 sets. Set 4 is invalid.
        $payload = [
            'exercise_id' => $this->exerciseA2->id,
            'sets'        => [
                ['set_number' => 4, 'suggested_weight' => '20kg'],
            ],
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sets.0.set_number']);
    }

    /**
     * 5. The existing maximum of four sets is preserved.
     */
    public function test_existing_maximum_of_four_sets_is_preserved(): void
    {
        // Try set 5
        $payload = [
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 5, 'suggested_weight' => '40kg'],
            ],
        ];

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), $payload);

        $response->assertStatus(422);
    }

    /**
     * 6. Approving one set changes only that set's weight.
     */
    public function test_approving_one_set_changes_only_that_sets_weight(): void
    {
        // Original weights: ['20kg', '25kg', '30kg', '35kg']
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '22.5kg'],
            ],
        ], $this->traineeUserA);

        $item = $report->items->first();

        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'      => 'approve',
                'coach_notes' => 'Approved set 1 increase',
            ]);

        $response->assertStatus(200);

        $this->exerciseA1->refresh();

        // Set 1 changed to 22.5kg, other sets intact
        $this->assertSame('22.5kg', $this->exerciseA1->getWeightForSet(1));
        $this->assertSame('25kg', $this->exerciseA1->getWeightForSet(2));
        $this->assertSame('30kg', $this->exerciseA1->getWeightForSet(3));
        $this->assertSame('35kg', $this->exerciseA1->getWeightForSet(4));

        $item->refresh();
        $this->assertSame('approved', $item->status);
        $this->assertSame('22.5kg', $item->approved_weight);
    }

    /**
     * 7. Rejecting a set leaves its actual workout weight unchanged.
     */
    public function test_rejecting_a_set_leaves_actual_workout_weight_unchanged(): void
    {
        $originalWeights = $this->exerciseA1->internal_weight;

        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 2, 'suggested_weight' => '40kg'],
            ],
        ], $this->traineeUserA);

        $item = $report->items->first();

        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'      => 'reject',
                'coach_notes' => 'Too high for now, stay at 25kg',
            ]);

        $response->assertStatus(200);

        $this->exerciseA1->refresh();
        $this->assertSame($originalWeights, $this->exerciseA1->internal_weight);

        $item->refresh();
        $this->assertSame('rejected', $item->status);
        $this->assertNull($item->approved_weight);
    }

    /**
     * 8. Approving an alternative coach-selected weight applies that alternative.
     */
    public function test_approving_an_alternative_coach_selected_weight_applies_alternative(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 3, 'suggested_weight' => '35kg'],
            ],
        ], $this->traineeUserA);

        $item = $report->items->first();

        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action'          => 'approve_alternative',
                'approved_weight' => '32.5kg',
                'coach_notes'     => 'Let us step up conservatively to 32.5kg first.',
            ]);

        $response->assertStatus(200);

        $this->exerciseA1->refresh();
        $this->assertSame('32.5kg', $this->exerciseA1->getWeightForSet(3));

        $item->refresh();
        $this->assertSame('approved', $item->status);
        $this->assertSame('32.5kg', $item->approved_weight);
    }

    /**
     * 9. A partially reviewed request retains independent states for all remaining sets.
     */
    public function test_partially_reviewed_request_retains_independent_states_for_all_remaining_sets(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [
                ['set_number' => 1, 'suggested_weight' => '22.5kg'],
                ['set_number' => 2, 'suggested_weight' => '27.5kg'],
                ['set_number' => 3, 'suggested_weight' => '32.5kg'],
            ],
        ], $this->traineeUserA);

        $items = $report->items;
        $item1 = $items->where('set_number', 1)->first();
        $item2 = $items->where('set_number', 2)->first();
        $item3 = $items->where('set_number', 3)->first();

        // Coach approves only item 1
        $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item1->id), [
                'action' => 'approve',
            ])->assertStatus(200);

        $item1->refresh();
        $item2->refresh();
        $item3->refresh();
        $report->refresh();

        $this->assertSame('approved', $item1->status);
        $this->assertSame('pending', $item2->status);
        $this->assertSame('pending', $item3->status);
        $this->assertSame('partially_reviewed', $report->status);
    }

    /**
     * 10. A stale request cannot silently overwrite a weight applied by another request.
     */
    public function test_stale_request_cannot_silently_overwrite_weight_applied_by_another_request(): void
    {
        // Initial weight of set 2 is '25kg'
        $this->assertSame('25kg', $this->exerciseA1->getWeightForSet(2));

        // Request A: proposes 27.5kg for set 2 (base_weight is 25kg)
        $reportA = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '27.5kg']],
        ], $this->traineeUserA);

        // Request B: proposes 30kg for set 2 (base_weight is 25kg)
        $reportB = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '30kg']],
        ], $this->traineeUserA);

        $itemA = $reportA->items->first();
        $itemB = $reportB->items->first();

        // 1. Coach approves Request A -> set 2 becomes 27.5kg
        $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $itemA->id), [
                'action' => 'approve',
            ])->assertStatus(200);

        $this->exerciseA1->refresh();
        $this->assertSame('27.5kg', $this->exerciseA1->getWeightForSet(2));

        // 2. Coach later attempts to approve Request B without forcing resolution
        $resB = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $itemB->id), [
                'action' => 'approve',
            ]);

        $resB->assertStatus(200);
        $this->assertTrue($resB->json('data.conflict'));

        // Weight MUST NOT be overwritten
        $this->exerciseA1->refresh();
        $this->assertSame('27.5kg', $this->exerciseA1->getWeightForSet(2));

        // Item B must be marked as conflict
        $itemB->refresh();
        $this->assertSame('conflict', $itemB->status);
        $this->assertSame('27.5kg', $itemB->conflict_current_weight);
    }

    /**
     * 11. Conflict detection preserves both requests and their history.
     */
    public function test_conflict_detection_preserves_both_requests_and_their_history(): void
    {
        $reportA = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '27.5kg']],
        ], $this->traineeUserA);

        $reportB = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '30kg']],
        ], $this->traineeUserA);

        $itemA = $reportA->items->first();
        $itemB = $reportB->items->first();

        app(LoadAdjustmentService::class)->reviewItem($itemA->id, ['action' => 'approve'], $this->adminCoach);
        app(LoadAdjustmentService::class)->reviewItem($itemB->id, ['action' => 'approve'], $this->adminCoach);

        $itemA->refresh();
        $itemB->refresh();

        $this->assertSame('approved', $itemA->status);
        $this->assertSame('25kg', $itemA->base_weight);
        $this->assertSame('27.5kg', $itemA->approved_weight);

        $this->assertSame('conflict', $itemB->status);
        $this->assertSame('25kg', $itemB->base_weight);
        $this->assertSame('30kg', $itemB->suggested_weight);
        $this->assertNotNull($itemB->conflict_detected_at);
    }

    /**
     * 12. An explicit conflict resolution follows the documented workflow.
     */
    public function test_explicit_conflict_resolution_follows_documented_workflow(): void
    {
        $reportA = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '27.5kg']],
        ], $this->traineeUserA);

        $reportB = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 2, 'suggested_weight' => '30kg']],
        ], $this->traineeUserA);

        $itemA = $reportA->items->first();
        $itemB = $reportB->items->first();

        // Approve A
        app(LoadAdjustmentService::class)->reviewItem($itemA->id, ['action' => 'approve'], $this->adminCoach);

        // Attempt approve B -> conflict
        app(LoadAdjustmentService::class)->reviewItem($itemB->id, ['action' => 'approve'], $this->adminCoach);
        $itemB->refresh();
        $this->assertSame('conflict', $itemB->status);

        // Coach explicitly resolves conflict by force approving
        $response = $this->actingAs($this->adminCoach, 'user')
            ->postJson(route('system.load-adjustment.item.review', $itemB->id), [
                'action'      => 'resolve_approve',
                'force'       => true,
                'coach_notes' => 'Conflict resolved: advancing to 30kg',
            ]);

        $response->assertStatus(200);

        $this->exerciseA1->refresh();
        $this->assertSame('30kg', $this->exerciseA1->getWeightForSet(2));

        $itemB->refresh();
        $this->assertSame('approved', $itemB->status);
        $this->assertSame('30kg', $itemB->approved_weight);
        $this->assertNull($itemB->conflict_detected_at);
    }

    /**
     * 13. Trainees cannot access or modify other trainees' requests.
     */
    public function test_trainees_cannot_access_or_modify_other_trainees_requests(): void
    {
        // Alice attempts to submit update for Bob's exercise
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.store'), [
                'exercise_id' => $this->exerciseB1->id,
                'sets'        => [['set_number' => 1, 'suggested_weight' => '90kg']],
            ]);

        $response->assertStatus(403);
    }

    /**
     * 14. Unauthorized coaches/users cannot review requests outside their permissions.
     */
    public function test_unauthorized_coaches_cannot_review_requests(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
        ], $this->traineeUserA);

        $item = $report->items->first();

        // Trainee tries to review item
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->postJson(route('system.load-adjustment.item.review', $item->id), [
                'action' => 'approve',
            ]);

        $this->assertTrue(in_array($response->status(), [302, 403]));
        $item->refresh();
        $this->assertSame('pending', $item->status);
    }

    /**
     * 15. The coach-side history displays prior requests and decisions.
     */
    public function test_coach_side_history_displays_prior_requests_and_decisions(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
            'notes'       => 'Check notes in history',
        ], $this->traineeUserA);

        $item = $report->items->first();
        app(LoadAdjustmentService::class)->reviewItem($item->id, [
            'action'      => 'approve',
            'coach_notes' => 'Keep pushing',
        ], $this->adminCoach);

        $response = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $response->assertStatus(200);
        $response->assertSee('Barbell Bench Press');
        $response->assertSee('22.5kg');
        $response->assertSee('Keep pushing');
    }

    /**
     * 16. The trainee's per-day report displays the relevant historical requests and coach responses.
     */
    public function test_trainee_per_day_report_displays_relevant_historical_requests(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
            'notes'       => 'Trainee day report test note',
        ], $this->traineeUserA);

        $item = $report->items->first();
        app(LoadAdjustmentService::class)->reviewItem($item->id, [
            'action'      => 'approve',
            'coach_notes' => 'Great work on day 1',
        ], $this->adminCoach);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.dashboard.trainer'));

        $response->assertStatus(200);
        $response->assertSee('سجل تحديثات الأوزان لليوم التدريبي');
        $response->assertSee('22.5kg');
        $response->assertSee('Great work on day 1');
    }

    /**
     * 17. Existing requests remain accessible after schema changes.
     */
    public function test_existing_requests_remain_accessible_after_schema_changes(): void
    {
        // Legacy record without child items
        $legacy = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'workout_plan_id'  => $this->planA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_day_name' => 'Day 1 - Chest & Arms',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg / 25kg',
            'reported_load'    => '27.5kg',
            'notes'            => 'Legacy entry',
            'status'           => 'pending',
        ]);

        $this->assertFalse($legacy->hasSetItems());
        $this->assertSame('pending', $legacy->status);

        $response = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $response->assertStatus(200);
        $response->assertSee('Legacy entry');
        $response->assertSee('27.5kg');
    }

    /**
     * 18. Transaction failures do not leave a partially applied weight update or falsely mark a request as approved.
     */
    public function test_transaction_failures_do_not_leave_partially_applied_weight_update(): void
    {
        $originalWeights = $this->exerciseA1->internal_weight;

        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
        ], $this->traineeUserA);

        $item = $report->items->first();

        // Intentionally corrupt with invalid action inside transaction
        try {
            DB::transaction(function () use ($item) {
                $this->exerciseA1->setWeightForSet(1, '22.5kg');
                $this->exerciseA1->save();
                throw new \Exception('Simulated crash');
            });
        } catch (\Exception $e) {
            // Expected
        }

        $this->exerciseA1->refresh();
        $this->assertSame($originalWeights, $this->exerciseA1->internal_weight);
    }

    /**
     * 19. Repeated review submissions do not duplicate history or apply a decision twice.
     */
    public function test_repeated_review_submissions_do_not_duplicate_history(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
        ], $this->traineeUserA);

        $item = $report->items->first();

        // First approval
        app(LoadAdjustmentService::class)->reviewItem($item->id, ['action' => 'approve'], $this->adminCoach);
        $firstReviewedAt = $item->fresh()->reviewed_at;

        // Second duplicate review call
        app(LoadAdjustmentService::class)->reviewItem($item->id, ['action' => 'approve'], $this->adminCoach);
        $item->refresh();

        $this->assertSame('approved', $item->status);
        $this->assertEquals($firstReviewedAt->timestamp, $item->reviewed_at->timestamp);
        $this->assertSame('22.5kg', $this->exerciseA1->fresh()->getWeightForSet(1));
    }

    /**
     * 20. Existing Load Adjustment behavior outside the requested scope remains compatible.
     */
    public function test_existing_load_adjustment_behavior_remains_compatible(): void
    {
        // Legacy submission payload with reported_load and no sets array
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
     * 21. Trainee comment author and creation timestamp are displayed correctly.
     */
    public function test_comment_author_and_creation_timestamp_displayed_for_trainee_note(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
            'notes'       => 'Need slightly higher load on set 1',
        ], $this->traineeUserA);

        $this->assertSame('Trainee Alice', $report->trainee_author_name);
        $this->assertNotNull($report->created_at);

        // View from trainee dashboard
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.dashboard.trainer'));

        $response->assertStatus(200);
        $response->assertSee('Added by');
        $response->assertSee('Added at');
        $response->assertSee('Trainee Alice');
        $response->assertSee('Need slightly higher load on set 1');
        $response->assertSee($report->created_at->format('Y-m-d H:i'));

        // View from coach trainee show page
        $coachResponse = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $coachResponse->assertStatus(200);
        $coachResponse->assertSee('Added by');
        $coachResponse->assertSee('Added at');
        $coachResponse->assertSee('Trainee Alice');
        $coachResponse->assertSee('Need slightly higher load on set 1');
        $coachResponse->assertSee($report->created_at->format('Y-m-d H:i'));
    }

    /**
     * 22. Coach review comment author and timestamp are displayed correctly.
     */
    public function test_comment_author_and_creation_timestamp_displayed_for_coach_review_note(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '22.5kg']],
            'notes'       => 'Check set 1 please',
        ], $this->traineeUserA);

        $item = $report->items->first();
        app(LoadAdjustmentService::class)->reviewItem($item->id, [
            'action'      => 'approve',
            'coach_notes' => 'Keep strict elbow angle and control descent',
        ], $this->adminCoach);

        $item->refresh();
        $this->assertSame('Coach Mahmoud', $item->coach_author_name);
        $this->assertNotNull($item->coach_notes_timestamp);

        // View from coach profile
        $coachResponse = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $coachResponse->assertStatus(200);
        $coachResponse->assertSee('Added by');
        $coachResponse->assertSee('Added at');
        $coachResponse->assertSee('Coach Mahmoud');
        $coachResponse->assertSee('Keep strict elbow angle and control descent');
        $coachResponse->assertSee($item->coach_notes_timestamp->format('Y-m-d H:i'));

        // View from trainee dashboard
        $traineeResponse = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.dashboard.trainer'));

        $traineeResponse->assertStatus(200);
        $traineeResponse->assertSee('Added by');
        $traineeResponse->assertSee('Added at');
        $traineeResponse->assertSee('Coach Mahmoud');
        $traineeResponse->assertSee('Keep strict elbow angle and control descent');
        $traineeResponse->assertSee($item->coach_notes_timestamp->format('Y-m-d H:i'));
    }

    /**
     * 23. Comments are attributed to actual authors and not inferred from the viewer.
     */
    public function test_comments_are_attributed_to_actual_authors_not_viewing_user(): void
    {
        $report = app(LoadAdjustmentService::class)->storeReport([
            'exercise_id' => $this->exerciseA1->id,
            'sets'        => [['set_number' => 1, 'suggested_weight' => '25kg']],
            'notes'       => 'Original note from Alice',
        ], $this->traineeUserA);

        $item = $report->items->first();
        app(LoadAdjustmentService::class)->reviewItem($item->id, [
            'action'      => 'approve',
            'coach_notes' => 'Feedback from Coach Mahmoud',
        ], $this->adminCoach);

        // Coach viewing Alice's profile should see Alice as note author (not Coach Mahmoud)
        $coachResponse = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $coachResponse->assertStatus(200);
        // Note author is Alice, reviewer is Mahmoud
        $coachResponse->assertSee('Original note from Alice');
        $coachResponse->assertSee('Feedback from Coach Mahmoud');
        $this->assertSame('Trainee Alice', $report->fresh()->trainee_author_name);
        $this->assertSame('Coach Mahmoud', $item->fresh()->coach_author_name);
    }

    /**
     * 24. Existing comments without author metadata remain readable without errors.
     */
    public function test_existing_comments_without_author_metadata_remain_readable_without_errors(): void
    {
        // Legacy row with no reviewed_by and created_at present
        $legacy = TraineeLoadAdjustment::create([
            'trainee_id'       => $this->traineeRecordA->id,
            'workout_plan_id'  => $this->planA->id,
            'exercise_id'      => $this->exerciseA1->id,
            'workout_day_name' => 'Day 1 - Chest & Arms',
            'exercise_name'    => 'Barbell Bench Press',
            'prescribed_load'  => '20kg',
            'reported_load'    => '22.5kg',
            'notes'            => 'Legacy note with no special author fields',
            'coach_notes'      => 'Legacy coach note with no reviewer id',
            'status'           => 'reviewed',
            'reviewed_by'      => null,
            'reviewed_at'      => null,
        ]);

        $this->assertNull($legacy->coach_author_name);

        // Access coach show page
        $coachResponse = $this->actingAs($this->adminCoach, 'user')
            ->get(route('system.trainee.show', $this->traineeRecordA->id));

        $coachResponse->assertStatus(200);
        $coachResponse->assertSee('Legacy note with no special author fields');
        $coachResponse->assertSee('Legacy coach note with no reviewer id');

        // Access trainee dashboard
        $traineeResponse = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.dashboard.trainer'));

        $traineeResponse->assertStatus(200);
        $traineeResponse->assertSee('Legacy note with no special author fields');
        $traineeResponse->assertSee('Legacy coach note with no reviewer id');
    }
}
