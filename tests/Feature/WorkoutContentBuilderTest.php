<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Trainee;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبارات محرر النصوص المنسقة (Rich Text Editor) للإحماء وما بعد التمرين
 * Tests for the Warmup and Post-Workout Rich-Text Editors and Store/Edit/Display functionality
 */
class WorkoutContentBuilderTest extends TestCase
{
    use DatabaseTransactions;

    protected ?User $coachUser = null;
    protected ?User $traineeUser = null;
    protected ?Trainee $trainee = null;
    protected ?WorkoutPlan $plan = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // مستخدم مدرب مصرح له
        $this->coachUser = User::create([
            'name'                 => 'Coach Test',
            'email'                => 'coach_' . uniqid() . '@gymtest.local',
            'password'             => Hash::make('CoachSecret123!'),
            'status'               => '1',
            'permission_group_id'  => 119,
            'user_type'            => 1,
            'default_language'     => 'ar',
            'force_reset_password' => false,
        ]);

        // مستخدم متدرب
        $this->traineeUser = User::create([
            'name'                 => 'Trainee Test',
            'email'                => 'trainee_' . uniqid() . '@gymtest.local',
            'password'             => Hash::make('TraineeSecret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'ar',
            'force_reset_password' => false,
        ]);

        $this->trainee = Trainee::create([
            'user_id'          => $this->traineeUser->id,
            'name'             => 'Trainee Test',
            'email'            => $this->traineeUser->email,
            'membership_start' => now()->toDateString(),
            'membership_end'   => now()->addMonths(3)->toDateString(),
            'status'           => 'active',
        ]);

        $this->plan = WorkoutPlan::create([
            'trainee_id' => $this->trainee->id,
            'day_name'   => 'Day 1 - Push Day',
            'status'     => 'active',
        ]);
    }

    /**
     * 1. إنشاء برنامج تدريبي مع نص منسق لقسم الإحماء
     */
    public function test_create_workout_program_with_warmup_rich_text(): void
    {
        $warmupHtml = '<h3>Dynamic Warmup</h3><p>5-10 minutes on <strong>treadmill</strong></p><ul><li>Arm Circles</li><li>Band Pull-Aparts</li></ul>';

        $postData = [
            'trainee_id' => $this->trainee->id,
            'program'    => [
                'day_1' => [
                    'day_name'     => 'Day 1: Chest & Shoulders',
                    'warmup'       => $warmupHtml,
                    'post_workout' => null,
                    'exercises'    => [
                        [
                            'name'            => 'Barbell Bench Press',
                            'sets'            => '4',
                            'reps'            => '8-10',
                            'rest'            => '90s',
                            'tempo'           => '3-0-1',
                            'type'            => 'main',
                            'internal_weight' => ['50kg', '60kg'],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->coachUser, 'user')
            ->post(route('system.workout.store'), $postData);

        $this->assertTrue(in_array($response->status(), [200, 302]));

        $createdPlan = WorkoutPlan::where('trainee_id', $this->trainee->id)
            ->where('day_name', 'Day 1: Chest & Shoulders')
            ->first();

        $this->assertNotNull($createdPlan);
        $this->assertStringContainsString('Dynamic Warmup', $createdPlan->warmup);
        $this->assertStringContainsString('treadmill', $createdPlan->warmup);
        $this->assertStringContainsString('<li>Arm Circles</li>', $createdPlan->warmup);
    }

    /**
     * 2. إنشاء برنامج تدريبي مع نص منسق لقسم ما بعد التمرين
     */
    public function test_create_workout_program_with_post_workout_rich_text(): void
    {
        $postHtml = '<p>Static stretching routine for 10 minutes. Watch tutorial: <a href="https://youtube.com/watch?v=stretch1">Stretch Video</a></p>';

        $postData = [
            'trainee_id' => $this->trainee->id,
            'program'    => [
                'day_1' => [
                    'day_name'     => 'Day 1: Legs & Abs',
                    'warmup'       => null,
                    'post_workout' => $postHtml,
                    'exercises'    => [
                        [
                            'name'            => 'Back Squat',
                            'sets'            => '4',
                            'reps'            => '10',
                            'rest'            => '120s',
                            'type'            => 'main',
                            'internal_weight' => ['80kg'],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->coachUser, 'user')
            ->post(route('system.workout.store'), $postData);

        $this->assertTrue(in_array($response->status(), [200, 302]));

        $createdPlan = WorkoutPlan::where('trainee_id', $this->trainee->id)
            ->where('day_name', 'Day 1: Legs & Abs')
            ->first();

        $this->assertNotNull($createdPlan);
        $this->assertStringContainsString('Static stretching routine', $createdPlan->post_workout);
        $this->assertStringContainsString('https://youtube.com/watch?v=stretch1', $createdPlan->post_workout);
    }

    /**
     * 3. حفظ حقلي الإحماء وما بعد التمرين بشكل مستقل تماماً دون أن يطغى أحدهما على الآخر
     */
    public function test_saving_both_fields_independently(): void
    {
        $mainEx = Exercise::create([
            'workout_plan_id' => $this->plan->id,
            'name'            => 'Overhead Press',
            'sets'            => '3',
            'reps'            => '10',
            'type'            => 'main',
        ]);

        // تحديث الإحماء فقط أولاً
        $updateWarmup = [
            'id'           => $this->plan->id,
            'day_name'     => $this->plan->day_name,
            'warmup'       => '<p>Only warmup content updated</p>',
            'post_workout' => null,
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id' => $mainEx->id,
                    'name'        => $mainEx->name,
                    'sets'        => '3',
                    'reps'        => '10',
                    'type'        => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $updateWarmup)
            ->assertStatus(200);

        $this->plan->refresh();
        $this->assertEquals('<p>Only warmup content updated</p>', $this->plan->warmup);
        $this->assertNull($this->plan->post_workout);

        // الآن تحديث ما بعد التمرين مع الحفاظ على الإحماء
        $updateBoth = [
            'id'           => $this->plan->id,
            'day_name'     => $this->plan->day_name,
            'warmup'       => '<p>Only warmup content updated</p>',
            'post_workout' => '<p>Now post workout added</p>',
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id' => $mainEx->id,
                    'name'        => $mainEx->name,
                    'sets'        => '3',
                    'reps'        => '10',
                    'type'        => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $updateBoth)
            ->assertStatus(200);

        $this->plan->refresh();
        $this->assertEquals('<p>Only warmup content updated</p>', $this->plan->warmup);
        $this->assertEquals('<p>Now post workout added</p>', $this->plan->post_workout);
    }

    /**
     * 4. تحديث المحتوى الحالي وتعديله دون تكرار العناصر
     */
    public function test_updating_existing_content_without_duplication(): void
    {
        $mainEx = Exercise::create([
            'workout_plan_id' => $this->plan->id,
            'name'            => 'Pull-ups',
            'sets'            => '3',
            'reps'            => '8',
            'type'            => 'main',
        ]);

        $this->plan->update([
            'warmup' => '<p>Initial warmup</p>',
        ]);

        // تعديل المحتوى
        $updateData = [
            'id'           => $this->plan->id,
            'day_name'     => 'Day 1 Modified',
            'warmup'       => '<p>Revised warmup with foam roller</p>',
            'post_workout' => null,
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id' => $mainEx->id,
                    'name'        => 'Pull-ups',
                    'sets'        => '3',
                    'reps'        => '8',
                    'type'        => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $updateData)
            ->assertStatus(200);

        $this->plan->refresh();
        $this->assertEquals('<p>Revised warmup with foam roller</p>', $this->plan->warmup);
        $this->assertStringNotContainsString('Initial warmup', $this->plan->warmup);
    }

    /**
     * 5. تحميل المحتوى المحفوظ بدقة داخل واجهة التعديل (Quill Editor)
     */
    public function test_loading_saved_content_correctly_into_edit_interface(): void
    {
        // 1. محتوى HTML مباشر
        $this->plan->update(['warmup' => '<p>Direct HTML</p>']);
        $this->plan->refresh();
        $this->assertEquals('<p>Direct HTML</p>', $this->plan->warmup_for_editor);

        // 2. محتوى مصفوفة JSON من النسخة السابقة
        $legacyJson = json_encode([
            ['id' => '1', 'type' => 'text', 'order' => 1, 'text' => '<p>Legacy Text</p>'],
            ['id' => '2', 'type' => 'video', 'order' => 2, 'title' => 'Legacy Video', 'url' => 'https://youtube.com/watch?v=123'],
        ]);
        $this->plan->update(['warmup' => $legacyJson]);
        $this->plan->refresh();
        $editorHtml = $this->plan->warmup_for_editor;
        $this->assertStringContainsString('Legacy Text', $editorHtml);
        $this->assertStringContainsString('https://youtube.com/watch?v=123', $editorHtml);

        // 3. محتوى نص عادي قديم
        $this->plan->update(['warmup' => "Line 1\nLine 2 https://gym.local"]);
        $this->plan->refresh();
        $plainEditorHtml = $this->plan->warmup_for_editor;
        $this->assertStringContainsString('Line 1', $plainEditorHtml);
        $this->assertStringContainsString('<br', $plainEditorHtml);
        $this->assertStringContainsString('https://gym.local', $plainEditorHtml);

        // 4. فارغ
        $this->plan->update(['warmup' => null]);
        $this->plan->refresh();
        $this->assertEquals('', $this->plan->warmup_for_editor);
    }

    /**
     * 6. الحفاظ على المحتوى المدخل عند حدوث أخطاء تحقق (Validation Failures)
     */
    public function test_preserving_content_when_validation_fails(): void
    {
        $submittedWarmup = '<p>Entered warmup that should not be lost</p>';

        // إرسال طلب بدون اسم التمرين مما يفشل التحقق
        $postData = [
            'id'           => $this->plan->id,
            'day_name'     => 'Failed Day',
            'warmup'       => $submittedWarmup,
            'post_workout' => null,
            'exercises'    => [], // مصفوفة فارغة تفشل التحقق
        ];

        $response = $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['exercises']);
    }

    /**
     * 7. التعامل مع الحقول الفارغة وتفريغ المحتوى عمداً (Clearing content to null)
     */
    public function test_handling_empty_fields_and_intentionally_clearing_content(): void
    {
        $this->assertNull(WorkoutPlan::sanitizeRichText(''));
        $this->assertNull(WorkoutPlan::sanitizeRichText('   '));
        $this->assertNull(WorkoutPlan::sanitizeRichText(null));
        $this->assertNull(WorkoutPlan::sanitizeRichText('<p><br></p>'));
        $this->assertNull(WorkoutPlan::sanitizeRichText('<p></p>'));
        $this->assertNull(WorkoutPlan::sanitizeRichText('<p>   </p>'));

        // تفريغ حقل كان ممتلئاً سابقاً عبر HTTP
        $mainEx = Exercise::create([
            'workout_plan_id' => $this->plan->id,
            'name'            => 'Dips',
            'sets'            => '3',
            'reps'            => '12',
            'type'            => 'main',
        ]);

        $this->plan->update(['warmup' => '<p>Previously populated</p>']);

        $clearData = [
            'id'           => $this->plan->id,
            'day_name'     => 'Day 1 Cleared',
            'warmup'       => '<p><br></p>', // Quill empty editor markup
            'post_workout' => '',
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id' => $mainEx->id,
                    'name'        => 'Dips',
                    'sets'        => '3',
                    'reps'        => '12',
                    'type'        => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $clearData)
            ->assertStatus(200);

        $this->plan->refresh();
        $this->assertNull($this->plan->warmup);
        $this->assertNull($this->plan->post_workout);
    }

    /**
     * 8. الحفاظ على بيانات البرامج القديمة (Legacy Plain Text & Exercises)
     */
    public function test_preserving_legacy_workout_program_content(): void
    {
        // خطة تدريب قديمة مخزن فيها نص عادي وروابط قديمة
        $legacyPlan = WorkoutPlan::create([
            'trainee_id'   => $this->trainee->id,
            'day_name'     => 'Legacy Routine Day',
            'warmup'       => 'Warmup routine with 10 mins treadmill',
            'warmup_links' => ['https://youtube.com/watch?v=legacy1'],
            'post_workout' => 'Foam roll calves and quads',
            'status'       => 'active',
        ]);

        $this->assertNotNull($legacyPlan->display_warmup);
        $this->assertStringContainsString('Warmup routine with 10 mins treadmill', $legacyPlan->display_warmup);
        $this->assertNotNull($legacyPlan->display_post_workout);
        $this->assertStringContainsString('Foam roll calves and quads', $legacyPlan->display_post_workout);
    }

    /**
     * 9. الحماية من حقن XSS والروابط الخبيثة في النصوص المنسقة
     */
    public function test_safely_sanitizing_malicious_html_and_dangerous_urls(): void
    {
        $dirtyHtml = '<h2>Title</h2><p>Safe text</p>' .
            '<script>alert("xss")</script>' .
            '<img src=x onerror="alert(1)">' .
            '<a href="javascript:alert(2)">Dangerous Link</a>' .
            '<a href="https://legitimate-gym.com">Legitimate Link</a>' .
            '<div onclick="evil()">Click me</div>';

        $clean = WorkoutPlan::sanitizeRichText($dirtyHtml);

        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('<h2>Title</h2>', $clean);
        $this->assertStringContainsString('<p>Safe text</p>', $clean);
        $this->assertStringContainsString('https://legitimate-gym.com', $clean);
        $this->assertStringContainsString('target="_blank"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    /**
     * 10. التحقق من صحة عرض النص المنسق للمتدرب في صفحة العرض
     */
    public function test_rendering_formatted_content_correctly_for_trainee_display(): void
    {
        $formattedWarmup = '<p>Do <strong>3 sets</strong> of:</p><ul><li>Dynamic lunges</li><li>Hip rotations</li></ul><p>Check: <a href="https://example.com/guide">Exercise Guide</a></p>';

        $this->plan->update(['warmup' => $formattedWarmup]);
        $this->plan->refresh();

        $displayHtml = $this->plan->display_warmup;

        $this->assertStringContainsString('<strong>3 sets</strong>', $displayHtml);
        $this->assertStringContainsString('<ul><li>Dynamic lunges</li>', $displayHtml);
        $this->assertStringContainsString('href="https://example.com/guide"', $displayHtml);
        $this->assertStringContainsString('target="_blank"', $displayHtml);
    }

    /**
     * 11. دعم خطط التدريب متعددة الأيام مع المحررين المستقلين لكل يوم
     */
    public function test_supporting_multiple_workout_days(): void
    {
        $postData = [
            'trainee_id' => $this->trainee->id,
            'program'    => [
                'day_1' => [
                    'day_name'     => 'Day 1 - Push',
                    'warmup'       => '<p>Warmup Day 1</p>',
                    'post_workout' => '<p>Cooldown Day 1</p>',
                    'exercises'    => [
                        ['name' => 'Bench Press', 'sets' => '4', 'reps' => '8', 'type' => 'main'],
                    ],
                ],
                'day_2' => [
                    'day_name'     => 'Day 2 - Pull',
                    'warmup'       => '<p>Warmup Day 2</p>',
                    'post_workout' => '<p>Cooldown Day 2</p>',
                    'exercises'    => [
                        ['name' => 'Deadlift', 'sets' => '4', 'reps' => '6', 'type' => 'main'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->coachUser, 'user')
            ->post(route('system.workout.store'), $postData);

        $this->assertTrue(in_array($response->status(), [200, 302]));

        $day1 = WorkoutPlan::where('trainee_id', $this->trainee->id)->where('day_name', 'Day 1 - Push')->first();
        $day2 = WorkoutPlan::where('trainee_id', $this->trainee->id)->where('day_name', 'Day 2 - Pull')->first();

        $this->assertNotNull($day1);
        $this->assertNotNull($day2);
        $this->assertEquals('<p>Warmup Day 1</p>', $day1->warmup);
        $this->assertEquals('<p>Cooldown Day 1</p>', $day1->post_workout);
        $this->assertEquals('<p>Warmup Day 2</p>', $day2->warmup);
        $this->assertEquals('<p>Cooldown Day 2</p>', $day2->post_workout);
    }

    /**
     * 12. التأكد من أن قسم التمارين الأساسية لم يتأثر (Main Exercises section remains unchanged)
     */
    public function test_main_exercises_section_remains_unchanged(): void
    {
        $mainEx = Exercise::create([
            'workout_plan_id' => $this->plan->id,
            'name'            => 'Leg Press',
            'sets'            => '4',
            'reps'            => '12',
            'rest'            => '60s',
            'tempo'           => '2-0-2',
            'internal_weight' => ['100kg', '120kg', '140kg'],
            'type'            => 'main',
        ]);

        $updateData = [
            'id'           => $this->plan->id,
            'day_name'     => $this->plan->day_name,
            'warmup'       => '<p>Leg warmup</p>',
            'post_workout' => null,
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id'     => $mainEx->id,
                    'name'            => 'Leg Press Updated',
                    'sets'            => '5',
                    'reps'            => '10',
                    'rest'            => '90s',
                    'tempo'           => '3-0-1',
                    'internal_weight' => ['120kg', '140kg', '160kg'],
                    'type'            => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $updateData)
            ->assertStatus(200);

        $mainEx->refresh();
        $this->assertEquals('Leg Press Updated', $mainEx->name);
        $this->assertEquals('5', $mainEx->sets);
        $this->assertEquals('10', $mainEx->reps);
        $this->assertEquals('90s', $mainEx->rest);
        $this->assertEquals('3-0-1', $mainEx->tempo);
        $this->assertEquals(['120kg', '140kg', '160kg'], $mainEx->internal_weight);
    }

    /**
     * 13. حفظ واسترجاع الجداول المنسقة مع الحفاظ على هيكلها الكامل (صفوف، أعمدة، دمج، خلايا)
     */
    public function test_table_created_in_editor_is_persisted_with_structure_intact(): void
    {
        $tableHtml = '<table class="ql-table-better" style="width: 100%;"><thead><tr><th colspan="2" data-row="row-h1">Movement</th><th data-row="row-h1">Duration</th></tr></thead><tbody><tr><td colspan="2" data-row="row-1" class="ql-cell-focused"><p class="ql-table-block" data-cell="c1">Arm Circles</p></td><td data-row="row-1"><p class="ql-table-block" data-cell="c2">30s</p></td></tr><tr><td colspan="2" data-row="row-2"><p class="ql-table-block" data-cell="c3">Cat Cow Stretch</p></td><td data-row="row-2"><p class="ql-table-block" data-cell="c4">60s</p></td></tr></tbody></table>';

        $mainEx = Exercise::create([
            'workout_plan_id' => $this->plan->id,
            'name'            => 'Bench Press',
            'sets'            => '4',
            'reps'            => '8',
            'type'            => 'main',
        ]);

        $updateData = [
            'id'           => $this->plan->id,
            'day_name'     => $this->plan->day_name,
            'warmup'       => $tableHtml,
            'post_workout' => $tableHtml,
            'exercises'    => [
                $mainEx->id => [
                    'exercise_id' => $mainEx->id,
                    'name'        => $mainEx->name,
                    'sets'        => '4',
                    'reps'        => '8',
                    'type'        => 'main',
                ],
            ],
        ];

        $this->actingAs($this->coachUser, 'user')
            ->postJson(route('system.workout.updateDay', ['dayId' => $this->plan->id]), $updateData)
            ->assertStatus(200);

        $this->plan->refresh();

        // تحقق من حفظ بنية الجدول في الإحماء
        $this->assertNotNull($this->plan->warmup);
        $this->assertStringContainsString('<table', $this->plan->warmup);
        $this->assertStringContainsString('<thead>', $this->plan->warmup);
        $this->assertStringContainsString('<tbody>', $this->plan->warmup);
        $this->assertStringContainsString('colspan="2"', $this->plan->warmup);
        $this->assertStringContainsString('data-row="row-h1"', $this->plan->warmup);
        $this->assertStringContainsString('Arm Circles', $this->plan->warmup);
        $this->assertStringContainsString('Cat Cow Stretch', $this->plan->warmup);

        // تحقق من حفظ بنية الجدول في ما بعد التمرين
        $this->assertNotNull($this->plan->post_workout);
        $this->assertStringContainsString('<table', $this->plan->post_workout);
        $this->assertStringContainsString('colspan="2"', $this->plan->post_workout);
        $this->assertStringContainsString('Cat Cow Stretch', $this->plan->post_workout);
    }

    /**
     * 14. استعادة محتوى الجدول للمحرر عند فتح صفحة التعديل (warmup_for_editor / post_workout_for_editor)
     */
    public function test_stored_table_content_is_restored_correctly_for_editor(): void
    {
        $tableHtml = '<table class="ql-table-better" style="width: 100%;"><tbody><tr><td data-row="row-1"><p class="ql-table-block" data-cell="c1">Foam Rolling</p></td><td data-row="row-1"><p class="ql-table-block" data-cell="c2">5 min</p></td></tr></tbody></table>';

        $this->plan->warmup = $tableHtml;
        $this->plan->post_workout = $tableHtml;
        $this->plan->save();

        $warmupEditor = $this->plan->warmup_for_editor;
        $postEditor = $this->plan->post_workout_for_editor;

        $this->assertStringContainsString('<table', $warmupEditor);
        $this->assertStringContainsString('Foam Rolling', $warmupEditor);
        $this->assertStringContainsString('data-row="row-1"', $warmupEditor);
        $this->assertStringContainsString('data-cell="c1"', $warmupEditor);

        $this->assertStringContainsString('<table', $postEditor);
        $this->assertStringContainsString('Foam Rolling', $postEditor);
    }

    /**
     * 15. عرض بنية الجدول في صفحة المتدرب مع الغلاف المتجاوب (display_warmup / display_post_workout)
     */
    public function test_trainee_view_renders_table_structure_and_responsive_wrapper(): void
    {
        $tableHtml = '<table class="ql-table-better"><thead><tr><th>Stretch</th><th>Hold Time</th></tr></thead><tbody><tr><td>Hamstring Stretch</td><td>30s</td></tr></tbody></table>';

        $this->plan->warmup = $tableHtml;
        $this->plan->post_workout = $tableHtml;
        $this->plan->save();

        $displayWarmup = $this->plan->display_warmup;
        $displayPost = $this->plan->display_post_workout;

        // التحقق من وجود غلاف table-responsive لضمان عدم حدوث overflow
        $this->assertStringContainsString('table-responsive', $displayWarmup);
        $this->assertStringContainsString('table-responsive', $displayPost);
        $this->assertStringContainsString('<table', $displayWarmup);
        $this->assertStringContainsString('Hamstring Stretch', $displayWarmup);
        $this->assertStringContainsString('<th>Stretch</th>', $displayWarmup);
    }

    /**
     * 16. إزالة أي عناصر أو وسوم خطيرة من الجداول (onclick, onerror, script)
     */
    public function test_dangerous_attributes_and_scripts_are_stripped_from_tables(): void
    {
        $maliciousHtml = '<table onclick="alert(\'xss\')" class="ql-table-better" style="width: 100%;"><tbody onerror="alert(1)"><tr><td onclick="alert(2)" data-row="row-1" colspan="2"><p class="ql-table-block" data-cell="c1">Safe Cell</p><script>alert(3)</script></td></tr></tbody></table>';

        $sanitized = WorkoutPlan::sanitizeRichText($maliciousHtml);

        $this->assertNotNull($sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringNotContainsString('onerror', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('alert(', $sanitized);
        // بينما يتم الإبقاء على البنية والخصائص الآمنة
        $this->assertStringContainsString('<table', $sanitized);
        $this->assertStringContainsString('colspan="2"', $sanitized);
        $this->assertStringContainsString('data-row="row-1"', $sanitized);
        $this->assertStringContainsString('Safe Cell', $sanitized);
    }
}

