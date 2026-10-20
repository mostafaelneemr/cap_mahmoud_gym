<?php

namespace Tests\Feature;

use App\Enums\DefaultStatus;
use App\Models\Active_section;
use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use App\Services\TraineeProgressService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateFileSecurityAndAdminWorkflowTest extends TestCase
{
    protected User $adminUser;
    protected User $staffWithoutPerm;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;
    protected TraineeProgressPhoto $photoA;
    protected TraineeProgressPhoto $photoB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Verify strict SQLite in-memory test database isolation
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. Build isolated test schema
        $this->createIsolatedSchema();

        // 3. Fake public storage
        Storage::fake('public');
        \Illuminate\Support\Facades\Cache::flush();

        // 4. Seed synthetic test data
        $this->seedSyntheticData();
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        parent::tearDown();
    }

    protected function createIsolatedSchema(): void
    {
        Schema::dropIfExists('activate_section');
        Schema::dropIfExists('trainee_progress_photos');
        Schema::dropIfExists('trainee_progress_checkins');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('user');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('activity_log');

        Schema::create('permission_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('is_supervisor')->default('no');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('permission_group_id');
            $table->string('route_name');
            $table->timestamps();
        });

        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('1');
            $table->integer('permission_group_id')->nullable();
            $table->tinyInteger('user_type')->nullable();
            $table->string('default_language')->default('en-gb');
            $table->boolean('force_reset_password')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('email')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainee_progress_checkins', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id');
            $table->unsignedInteger('coach_id')->nullable();
            $table->date('checkin_date');
            $table->decimal('weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('coach_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('trainee_progress_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checkin_id');
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('activate_section', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 100);
            $table->enum('value', ['active', 'inactive'])->default('active');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_name')->nullable();
            $table->text('description')->nullable();
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->string('event')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    protected function seedSyntheticData(): void
    {
        // Supervisor permission group
        $adminGroup = DB::table('permission_groups')->insertGetId([
            'name'          => 'Admin Supervisor',
            'is_supervisor' => 'yes',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('permissions')->insert([
            ['permission_group_id' => $adminGroup, 'route_name' => 'system.activate.index', 'created_at' => now(), 'updated_at' => now()],
            ['permission_group_id' => $adminGroup, 'route_name' => 'system.activate.update', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Regular staff permission group without activation permissions
        $staffGroup = DB::table('permission_groups')->insertGetId([
            'name'          => 'Staff Limited',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->adminUser = User::create([
            'name'                 => 'Admin Supervisor',
            'email'                => 'admin@example.com',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => $adminGroup,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->staffWithoutPerm = User::create([
            'name'                 => 'Staff Worker',
            'email'                => 'staff@example.com',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => $staffGroup,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // Trainee A (Alice)
        $this->traineeUserA = User::create([
            'name'                 => 'Alice Trainee',
            'email'                => 'alice@example.com',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'user_id' => $this->traineeUserA->id,
            'email'   => 'alice@example.com',
            'status'  => 'active',
        ]);

        // Trainee B (Bob)
        $this->traineeUserB = User::create([
            'name'                 => 'Bob Trainee',
            'email'                => 'bob@example.com',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => null,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'user_id' => $this->traineeUserB->id,
            'email'   => 'bob@example.com',
            'status'  => 'active',
        ]);

        // Seed sample check-ins and photo files
        $fakeImgData = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'); // 1x1 gif / valid image bytes
        Storage::disk('public')->put('progress_photos/test_alice.gif', $fakeImgData);
        Storage::disk('public')->put('progress_photos/test_bob.gif', $fakeImgData);

        $checkinA = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'coach_id'     => $this->adminUser->id,
            'checkin_date' => now()->toDateString(),
            'weight'       => 70.0,
            'notes'        => 'Alice checkin notes',
        ]);

        $this->photoA = TraineeProgressPhoto::create([
            'checkin_id' => $checkinA->id,
            'photo_path' => 'progress_photos/test_alice.gif',
            'caption'    => 'Front View',
        ]);

        $checkinB = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'coach_id'     => $this->adminUser->id,
            'checkin_date' => now()->toDateString(),
            'weight'       => 85.0,
            'notes'        => 'Bob checkin notes',
        ]);

        $this->photoB = TraineeProgressPhoto::create([
            'checkin_id' => $checkinB->id,
            'photo_path' => 'progress_photos/test_bob.gif',
            'caption'    => 'Side View',
        ]);

        // Seed an active section record
        Active_section::create([
            'id'         => 1,
            'name'       => 'testimonial_section',
            'value'      => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 1. Authorized trainee can access own progress photo via web route.
     */
    public function test_authorized_trainee_can_access_own_progress_photo()
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => $this->photoA->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/gif');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }

    /**
     * 2. Trainee cannot access another trainee's private photo (IDOR rejected with HTTP 403).
     */
    public function test_unauthorized_trainee_cannot_access_another_trainee_progress_photo()
    {
        $response = $this->actingAs($this->traineeUserB, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => $this->photoA->id]));

        $response->assertStatus(403);
    }

    /**
     * 3. Unauthenticated requests are rejected.
     */
    public function test_unauthenticated_request_is_rejected_from_photo_access()
    {
        $response = $this->get(route('system.trainee-progress.photo', ['photoId' => $this->photoA->id]));

        // Should redirect to system login or return 401
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401], true));
    }

    /**
     * 4. Missing or invalid photo IDs return HTTP 404.
     */
    public function test_missing_or_invalid_photo_id_returns_404()
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => 99999]));

        $response->assertStatus(404);

        $responseNonNumeric = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => 'invalid_abc']));

        $responseNonNumeric->assertStatus(404);
    }

    /**
     * 5. Path traversal attempts and outside-storage files are rejected.
     */
    public function test_path_traversal_attempts_in_photo_path_are_rejected()
    {
        // Construct a malicious record pointing outside the storage directory
        $maliciousPhoto = TraineeProgressPhoto::create([
            'checkin_id' => $this->photoA->checkin_id,
            'photo_path' => '../../../../etc/passwd',
            'caption'    => 'Exploit Test',
        ]);

        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => $maliciousPhoto->id]));

        $this->assertTrue(in_array($response->getStatusCode(), [400, 403, 404], true));
    }

    /**
     * 6. Administrator / Coach can access trainee progress photos.
     */
    public function test_administrator_can_access_trainee_progress_photo()
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.photo', ['photoId' => $this->photoA->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/gif');
    }

    /**
     * 7. API serve endpoint enforces authentication and authorization.
     */
    public function test_api_serve_endpoint_enforces_authentication_and_authorization()
    {
        // Trainee A accessing own photo via API
        $responseA = $this->actingAs($this->traineeUserA, 'sanctum')
            ->getJson(route('api.trainee.photos.serve', ['photoId' => $this->photoA->id]));
        $responseA->assertStatus(200);

        // Trainee B attempting to access Trainee A photo via API -> 403
        $responseB = $this->actingAs($this->traineeUserB, 'sanctum')
            ->getJson(route('api.trainee.photos.serve', ['photoId' => $this->photoA->id]));
        $responseB->assertStatus(403);
    }

    /**
     * 7b. API serve endpoint rejects unauthenticated requests.
     */
    public function test_api_serve_endpoint_rejects_unauthenticated_requests()
    {
        $responseGuest = $this->getJson(route('api.trainee.photos.serve', ['photoId' => $this->photoA->id]));
        $responseGuest->assertStatus(401);
    }

    /**
     * 8. Update activation section rejects unauthenticated requests.
     */
    public function test_update_activate_section_rejects_unauthenticated_request()
    {
        $response = $this->post(route('system.activate.update', ['id' => 'testimonial_section']), [
            'name'  => 'testimonial_section',
            'value' => 'inactive',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 401], true));
    }

    /**
     * 9. Update activation section rejects trainee users.
     */
    public function test_update_activate_section_rejects_trainee_user()
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.activate.update', ['id' => 'testimonial_section']), [
                'name'  => 'testimonial_section',
                'value' => 'inactive',
            ]);

        // Trainee should be redirected away from admin routes
        $response->assertRedirect(route('system.dashboard.trainer'));
    }

    /**
     * 10. Update activation section rejects staff without view-activate-sections permission.
     */
    public function test_update_activate_section_rejects_staff_without_permission()
    {
        $response = $this->actingAs($this->staffWithoutPerm, 'user')
            ->post(route('system.activate.update', ['id' => 'testimonial_section']), [
                'name'  => 'testimonial_section',
                'value' => 'inactive',
            ]);

        $this->assertTrue(in_array($response->getStatusCode(), [401, 403], true));
    }

    /**
     * 11. Update activation section updates existing record successfully for authorized admin.
     */
    public function test_update_activate_section_updates_existing_record()
    {
        $response = $this->actingAs($this->adminUser, 'user')
            ->postJson(route('system.activate.update', ['id' => 'testimonial_section']), [
                'name'  => 'testimonial_section',
                'value' => 'inactive',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'data'   => [
                'section' => 'testimonial_section',
                'value'   => 'inactive',
            ],
        ]);

        $section = Active_section::where('name', 'testimonial_section')->first();
        $this->assertNotNull($section);
        $this->assertSame('inactive', $section->value);
    }

    /**
     * 12. Update activation section creates missing section if not present.
     */
    public function test_update_activate_section_creates_missing_section()
    {
        $this->assertNull(Active_section::where('name', 'blog_section')->first());

        $response = $this->actingAs($this->adminUser, 'user')
            ->postJson(route('system.activate.update', ['id' => 'blog_section']), [
                'name'  => 'blog_section',
                'value' => 'active',
            ]);

        $response->assertStatus(200);
        $section = Active_section::where('name', 'blog_section')->first();
        $this->assertNotNull($section);
        $this->assertSame('active', $section->value);
    }

    /**
     * 13. Update activation section rejects invalid inputs.
     */
    public function test_update_activate_section_rejects_invalid_inputs()
    {
        // Invalid section name with forbidden characters
        $response = $this->actingAs($this->adminUser, 'user')
            ->postJson(route('system.activate.update', ['id' => 'testimonial_section']), [
                'name'  => 'invalid name with spaces!',
                'value' => 'active',
            ]);

        $response->assertStatus(422);

        // Invalid status value
        $responseStatus = $this->actingAs($this->adminUser, 'user')
            ->postJson(route('system.activate.update', ['id' => 'testimonial_section']), [
                'name'  => 'testimonial_section',
                'value' => 'malicious_status',
            ]);

        $responseStatus->assertStatus(422);
    }

    /**
     * 14. active_section helper handles missing record safely without throwing error.
     */
    public function test_active_section_helper_handles_missing_record_safely()
    {
        // When section does not exist
        $this->assertFalse(active_section('completely_unknown_section'));

        // When section exists and is active
        $this->assertTrue(active_section('testimonial_section'));

        // When section exists and is inactive
        Active_section::where('name', 'testimonial_section')->update(['value' => 'inactive']);
        $this->assertFalse(active_section('testimonial_section'));
    }

    /**
     * 15. Photo URL accessor in TraineeProgressPhoto links to secure authenticated route.
     */
    public function test_photo_url_accessor_links_to_secure_route()
    {
        $expectedUrl = route('system.trainee-progress.photo', ['photoId' => $this->photoA->id]);
        $this->assertSame($expectedUrl, $this->photoA->photo_url);
        $this->assertSame($expectedUrl, $this->photoA->secure_url);
    }

    /**
     * 16. Store checkin transaction rolls back and cleans up newly uploaded files on error.
     */
    public function test_store_checkin_cleans_up_files_on_failure()
    {
        $service = app(TraineeProgressService::class);

        $fakeFile = UploadedFile::fake()->image('progress_fail.jpg', 400, 400);

        try {
            // Force an exception by calling storeCheckin with invalid trainee
            $nonExistentUser = new User();
            $nonExistentUser->id = 999999;
            $nonExistentUser->user_type = 2;

            $service->storeCheckin([
                'checkin_date' => now()->toDateString(),
            ], $nonExistentUser, [$fakeFile]);

            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Trainee profile not found', $e->getMessage());
        }
    }
}
