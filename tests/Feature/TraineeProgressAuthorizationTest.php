<?php

namespace Tests\Feature;

use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use App\Modules\Api\TraineePhotoApiController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TraineeProgressAuthorizationTest extends TestCase
{
    protected User $adminUser;
    protected User $traineeUserA;
    protected Trainee $traineeRecordA;
    protected User $traineeUserB;
    protected Trainee $traineeRecordB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Verify strict test database isolation
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // 2. Build isolated test schema in SQLite in-memory
        $this->createIsolatedSchema();

        // 3. Fake public storage
        Storage::fake('public');

        // 4. Create synthetic test users and trainee records
        $this->seedSyntheticData();
    }

    protected function createIsolatedSchema(): void
    {
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
        // Admin user (user_type = 1, supervisor group)
        $adminGroup = DB::table('permission_groups')->insertGetId([
            'name'          => 'Super Admin',
            'is_supervisor' => 'yes',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('permission_groups')->insert([
            'id'            => 125,
            'name'          => 'Trainees',
            'is_supervisor' => 'no',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->adminUser = User::create([
            'name'                 => 'Coach Mahmoud Admin',
            'email'                => 'admin@synthetic-gym-test.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => $adminGroup,
            'user_type'            => 1,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        // Trainee A (Alice)
        $this->traineeUserA = User::create([
            'name'                 => 'Trainee Alice',
            'email'                => 'alice@synthetic-gym-test.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordA = Trainee::create([
            'user_id' => $this->traineeUserA->id,
            'email'   => $this->traineeUserA->email,
            'status'  => 'active',
        ]);

        // Trainee B (Bob)
        $this->traineeUserB = User::create([
            'name'                 => 'Trainee Bob',
            'email'                => 'bob@synthetic-gym-test.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 2,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $this->traineeRecordB = Trainee::create([
            'user_id' => $this->traineeUserB->id,
            'email'   => $this->traineeUserB->email,
            'status'  => 'active',
        ]);
    }

    /**
     * Rule 1: Authenticated trainee can view their own progress gallery.
     */
    public function test_authenticated_trainee_can_access_own_progress(): void
    {
        TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 75.5,
            'notes'        => 'Feeling energetic!',
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.index'));

        $response->assertStatus(200);
        $response->assertViewHas('trainee', function ($trainee) {
            return (int) $trainee->id === (int) $this->traineeRecordA->id;
        });
    }

    /**
     * Rule 2: Trainee cannot view another trainee's progress by tampering with trainee_id.
     */
    public function test_trainee_cannot_access_another_trainee_progress_via_trainee_id(): void
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.index', [
                'trainee_id' => $this->traineeRecordB->id
            ]));

        $response->assertStatus(403);
    }

    /**
     * Rule 3: Trainee cannot view another trainee's progress by passing encrypted trainee parameter.
     */
    public function test_trainee_cannot_access_another_trainee_progress_via_encrypted_param(): void
    {
        $encryptedBobId = Crypt::encrypt($this->traineeRecordB->id);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.index', [
                'trainee' => $encryptedBobId
            ]));

        $response->assertStatus(403);
    }

    /**
     * Rule 4: Trainee cannot compare another trainee's progress check-ins via side-by-side compare.
     */
    public function test_trainee_cannot_compare_another_trainees_progress(): void
    {
        TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 92.0,
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.compare', [
                'trainee_id' => $this->traineeRecordB->id,
                'date1'      => '2026-10-01',
            ]));

        $response->assertStatus(403);
    }

    /**
     * Rule 5: Trainee can compare their own check-ins.
     */
    public function test_trainee_can_compare_own_checkins(): void
    {
        TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 75.5,
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->get(route('system.trainee-progress.compare', [
                'trainee_id' => $this->traineeRecordA->id,
                'date1'      => '2026-10-01',
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
    }

    /**
     * Rule 6: Unauthorized write operation (submitting check-in for another trainee) is rejected.
     */
    public function test_trainee_cannot_submit_progress_checkin_for_another_trainee(): void
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.trainee-progress.store'), [
                'trainee_id'   => $this->traineeRecordB->id,
                'checkin_date' => '2026-10-15',
                'weight'       => 70.0,
                'notes'        => 'Unauthorized submission test',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('trainee_progress_checkins', [
            'trainee_id' => $this->traineeRecordB->id,
            'notes'      => 'Unauthorized submission test',
        ]);
    }

    /**
     * Rule 7: Trainee cannot forge coach notes when creating their own check-in.
     */
    public function test_trainee_submitting_checkin_strips_forged_coach_notes(): void
    {
        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.trainee-progress.store'), [
                'trainee_id'   => $this->traineeRecordA->id,
                'checkin_date' => '2026-10-15',
                'weight'       => 74.0,
                'notes'        => 'Legitimate trainee note',
                'coach_notes'  => 'Malicious forged coach evaluation',
            ]);

        $response->assertRedirect();

        $checkin = TraineeProgressCheckin::where('trainee_id', $this->traineeRecordA->id)
            ->first();

        $this->assertNotNull($checkin, 'Checkin must be created for trainee Alice.');
        $this->assertSame('Legitimate trainee note', $checkin->notes);
        $this->assertNull($checkin->coach_notes, 'Coach notes must be stripped when submitted by trainee.');
        $this->assertNull($checkin->coach_id, 'Coach ID must be null for trainee self-submission.');
    }

    /**
     * Rule 8: Trainee cannot update coach feedback / notes on check-in.
     */
    public function test_trainee_cannot_update_coach_notes(): void
    {
        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 75.0,
            'coach_notes'  => 'Original coach advice',
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->post(route('system.trainee-progress.update-notes', $checkin->id), [
                'coach_notes' => 'Tampered coach advice',
            ]);

        $response->assertStatus(403);

        $checkin->refresh();
        $this->assertSame('Original coach advice', $checkin->coach_notes);
    }

    /**
     * Rule 9: Trainee cannot delete another trainee's check-in record.
     */
    public function test_trainee_cannot_delete_another_trainees_checkin(): void
    {
        $bobCheckin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 90.0,
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->delete(route('system.trainee-progress.destroy', $bobCheckin->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('trainee_progress_checkins', [
            'id' => $bobCheckin->id,
        ]);
    }

    /**
     * Rule 10: Trainee can delete their own check-in.
     */
    public function test_trainee_can_delete_own_checkin(): void
    {
        $aliceCheckin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 75.0,
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->delete(route('system.trainee-progress.destroy', $aliceCheckin->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('trainee_progress_checkins', [
            'id' => $aliceCheckin->id,
        ]);
    }

    /**
     * Rule 11: Trainee cannot delete another trainee's photo.
     */
    public function test_trainee_cannot_delete_another_trainees_photo(): void
    {
        $bobCheckin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-10-01',
        ]);

        $bobPhoto = TraineeProgressPhoto::create([
            'checkin_id' => $bobCheckin->id,
            'photo_path' => 'progress_photos/synthetic_bob.webp',
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->delete(route('system.trainee-progress.destroy-photo', $bobPhoto->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('trainee_progress_photos', [
            'id' => $bobPhoto->id,
        ]);
    }

    /**
     * Rule 12: Trainee can delete their own photo.
     */
    public function test_trainee_can_delete_own_photo(): void
    {
        $aliceCheckin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordA->id,
            'checkin_date' => '2026-10-01',
        ]);

        $alicePhoto = TraineeProgressPhoto::create([
            'checkin_id' => $aliceCheckin->id,
            'photo_path' => 'progress_photos/synthetic_alice.webp',
        ]);

        $response = $this->actingAs($this->traineeUserA, 'user')
            ->delete(route('system.trainee-progress.destroy-photo', $alicePhoto->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('trainee_progress_photos', [
            'id' => $alicePhoto->id,
        ]);
    }

    /**
     * Rule 13: Privileged administrator / coach can access and manage any trainee's progress.
     */
    public function test_administrator_can_access_and_manage_trainee_progress(): void
    {
        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-10-01',
            'weight'       => 91.5,
            'notes'        => 'Bob initial note',
        ]);

        // Coach can view Bob's gallery
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.index', [
                'trainee_id' => $this->traineeRecordB->id,
            ]));
        $response->assertStatus(200);

        // Coach can update coach notes on Bob's checkin
        $response = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee-progress.update-notes', $checkin->id), [
                'coach_notes' => 'Great consistency this week, Bob!',
            ]);
        $response->assertRedirect();

        $checkin->refresh();
        $this->assertSame('Great consistency this week, Bob!', $checkin->coach_notes);
        $this->assertSame($this->adminUser->id, $checkin->coach_id);

        // Coach can compare Bob's progress
        $response = $this->actingAs($this->adminUser, 'user')
            ->get(route('system.trainee-progress.compare', [
                'trainee_id' => $this->traineeRecordB->id,
                'date1'      => '2026-10-01',
            ]));
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
    }

    /**
     * Rule 14: Unauthenticated requests follow application's authentication behavior (redirect to login).
     */
    public function test_unauthenticated_request_is_redirected_to_login(): void
    {
        $response = $this->get(route('system.trainee-progress.index'));

        $response->assertStatus(302);
        $this->assertTrue(
            $response->isRedirect(url('system/login')) || $response->isRedirect(route('login')),
            'Unauthenticated request must redirect to the login page.'
        );
    }

    /**
     * Rule 15: TraineePhotoApiController enforces authentication and trainee check-in authorization.
     */
    public function test_trainee_photo_api_controller_authorization(): void
    {
        $controller = app(TraineePhotoApiController::class);

        // 1. Unauthenticated request rejected with 401
        $requestUnauth = Request::create('/api/trainee/photos/upload', 'POST', []);
        $responseUnauth = $controller->upload($requestUnauth);
        $this->assertSame(401, $responseUnauth->getStatusCode());

        // 2. Trainee Alice cannot upload for Trainee Bob (trainee_id tampering rejected with 403)
        $this->actingAs($this->traineeUserA, 'user');
        $requestTamperTrainee = Request::create('/api/trainee/photos/upload', 'POST', [
            'trainee_id' => $this->traineeRecordB->id,
        ]);
        $responseTamperTrainee = $controller->upload($requestTamperTrainee);
        $this->assertSame(403, $responseTamperTrainee->getStatusCode());

        // 3. Trainee Alice cannot attach photo to Trainee Bob's checkin (checkin_id tampering rejected with 403)
        $bobCheckin = TraineeProgressCheckin::create([
            'trainee_id'   => $this->traineeRecordB->id,
            'checkin_date' => '2026-10-01',
        ]);

        $requestTamperCheckin = Request::create('/api/trainee/photos/upload', 'POST', [
            'checkin_id' => $bobCheckin->id,
        ]);
        $responseTamperCheckin = $controller->upload($requestTamperCheckin);
        $this->assertSame(403, $responseTamperCheckin->getStatusCode());
    }

    /**
     * Rule 16: Trainee with user_type 3 is restricted to their own progress and cannot access others.
     */
    public function test_trainee_with_user_type_3_is_strictly_authorized(): void
    {
        $traineeUserC = User::create([
            'name'                 => 'Trainee Charlie',
            'email'                => 'charlie@synthetic-gym-test.local',
            'password'             => Hash::make('Secret123!'),
            'status'               => '1',
            'permission_group_id'  => 125,
            'user_type'            => 3,
            'default_language'     => 'en-gb',
            'force_reset_password' => false,
        ]);

        $traineeRecordC = Trainee::create([
            'user_id' => $traineeUserC->id,
            'email'   => $traineeUserC->email,
            'status'  => 'active',
        ]);

        // Charlie can view own gallery
        $responseOwn = $this->actingAs($traineeUserC, 'user')
            ->get(route('system.trainee-progress.index'));
        $responseOwn->assertStatus(200);

        // Charlie cannot access Bob's gallery
        $responseOther = $this->actingAs($traineeUserC, 'user')
            ->get(route('system.trainee-progress.index', [
                'trainee_id' => $this->traineeRecordB->id
            ]));
        $responseOther->assertStatus(403);

        // Charlie cannot update coach notes
        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $traineeRecordC->id,
            'checkin_date' => '2026-10-01',
        ]);

        $responseNotes = $this->actingAs($traineeUserC, 'user')
            ->post(route('system.trainee-progress.update-notes', $checkin->id), [
                'coach_notes' => 'Tampered evaluation',
            ]);
        $responseNotes->assertStatus(403);
    }

    /**
     * Rule 17: Administrator can create check-in on behalf of trainee with coach feedback, and delete records.
     */
    public function test_administrator_can_create_checkin_and_delete_photo(): void
    {
        // Coach creates check-in on behalf of Bob
        $response = $this->actingAs($this->adminUser, 'user')
            ->post(route('system.trainee-progress.store'), [
                'trainee_id'   => $this->traineeRecordB->id,
                'checkin_date' => '2026-10-18',
                'weight'       => 89.5,
                'notes'        => 'Bob reported good progress to coach',
                'coach_notes'  => 'Coach evaluated form as excellent',
            ]);

        $response->assertRedirect();

        $checkin = TraineeProgressCheckin::where('trainee_id', $this->traineeRecordB->id)
            ->where('notes', 'Bob reported good progress to coach')
            ->first();

        $this->assertNotNull($checkin);
        $this->assertSame('Coach evaluated form as excellent', $checkin->coach_notes);
        $this->assertSame($this->adminUser->id, $checkin->coach_id);

        // Coach creates photo and deletes it
        $photo = TraineeProgressPhoto::create([
            'checkin_id' => $checkin->id,
            'photo_path' => 'progress_photos/coach_uploaded.webp',
        ]);

        $delResponse = $this->actingAs($this->adminUser, 'user')
            ->delete(route('system.trainee-progress.destroy-photo', $photo->id));
        $delResponse->assertRedirect();

        $this->assertDatabaseMissing('trainee_progress_photos', ['id' => $photo->id]);
    }
}
