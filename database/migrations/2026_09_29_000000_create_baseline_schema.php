<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. permission_groups
        if (!Schema::hasTable('permission_groups')) {
            Schema::create('permission_groups', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id', true);
                $table->string('name', 191);
                $table->string('default_route', 255)->nullable();
                $table->string('new_admin_default_route', 255)->nullable();
                $table->string('system', 50)->default('po');
                $table->enum('is_supervisor', ['yes', 'no'])->default('no');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        // 2. user
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id', true);
                $table->integer('permission_group_id')->nullable();
                $table->string('password', 255);
                $table->string('name', 100);
                $table->string('email', 96)->nullable();
                $table->string('google_id', 255)->nullable();
                $table->string('mobile', 50)->nullable();
                $table->boolean('status');
                $table->boolean('user_type')->nullable();
                $table->string('default_language', 100)->default('ar');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('deleted_at')->nullable();
                $table->string('two_fa_secret', 255)->nullable();
                $table->boolean('force_reset_password')->nullable()->default(0);

                $table->unique('email', 'user_email_unique');
                $table->index('google_id', 'user_google_id_index');
            });
        }

        // 3. trainees
        if (!Schema::hasTable('trainees')) {
            Schema::create('trainees', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->integer('user_id')->nullable();
                $table->string('name', 255)->nullable();
                $table->string('email', 255)->unique('trainees_email_unique');
                $table->string('google_id', 255)->nullable();
                $table->string('mobile', 255)->nullable();
                $table->string('password', 255)->nullable();
                $table->integer('age')->nullable();
                $table->decimal('weight', 5, 2)->nullable();
                $table->decimal('height', 5, 2)->nullable();
                $table->date('membership_start');
                $table->date('membership_end');
                $table->enum('training_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
                $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('google_id', 'trainees_google_id_index');
                $table->index('user_id', 'trainees_user_id_index');
                $table->foreign('user_id', 'fk_trainees_user')
                    ->references('id')->on('user')
                    ->onDelete('restrict');
            });
        }

        // 4. workout_plans
        if (!Schema::hasTable('workout_plans')) {
            Schema::create('workout_plans', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('trainee_id');
                $table->string('day_name', 100);
                $table->text('warmup')->nullable();
                $table->text('post_workout')->nullable();
                $table->enum('status', ['active', 'archived'])->default('active');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('trainee_id', 'fk_workout_trainee');
                $table->foreign('trainee_id', 'fk_workout_plans_trainee')
                    ->references('id')->on('trainees')
                    ->onDelete('cascade');
            });
        }

        // 5. exercises
        if (!Schema::hasTable('exercises')) {
            Schema::create('exercises', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('workout_plan_id');
                $table->string('name', 150);
                $table->integer('sets')->nullable();
                $table->string('reps', 50)->nullable();
                $table->string('rest', 50)->nullable();
                $table->string('internal_weight', 100)->nullable();
                $table->string('tempo', 50)->nullable();
                $table->string('link', 255)->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('workout_plan_id', 'fk_exercise_plan');
                $table->foreign('workout_plan_id', 'fk_exercise_plan')
                    ->references('id')->on('workout_plans')
                    ->onDelete('cascade');
            });
        }

        // 6. workout_logs
        if (!Schema::hasTable('workout_logs')) {
            Schema::create('workout_logs', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('trainee_id');
                $table->unsignedBigInteger('exercise_id');
                $table->integer('set_number');
                $table->integer('reps_completed')->nullable();
                $table->decimal('weight_lifted', 6, 2)->nullable();
                $table->date('performed_at');
                $table->timestamp('created_at')->nullable()->useCurrent();

                $table->index('trainee_id', 'fk_log_trainee');
                $table->index('exercise_id', 'fk_log_exercise');
                $table->foreign('exercise_id', 'fk_log_exercise')
                    ->references('id')->on('exercises')
                    ->onDelete('cascade');
                $table->foreign('trainee_id', 'fk_workout_logs_trainee')
                    ->references('id')->on('trainees')
                    ->onDelete('cascade');
            });
        }

        // 7. nutrition_plans
        if (!Schema::hasTable('nutrition_plans')) {
            Schema::create('nutrition_plans', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('trainee_id');
                $table->string('name', 255)->nullable();
                $table->longText('description')->nullable();
                $table->enum('status', ['active', 'archived'])->default('active');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

                $table->index('trainee_id', 'fk_nutrition_trainee_id');
                $table->foreign('trainee_id', 'fk_nutrition_trainee_id')
                    ->references('id')->on('trainees')
                    ->onDelete('cascade');
            });
        }

        // 8. posts
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->enum('type', [
                    'hero', 'about', 'service', 'why_us', 'quote',
                    'transformation_hero', 'transformation', 'review',
                    'join_hero', 'contact_hero'
                ]);
                $table->string('title', 255)->nullable();
                $table->string('title_ar', 255)->nullable();
                $table->string('title_en', 255)->nullable();
                $table->string('subtitle', 255)->nullable();
                $table->string('subtitle_ar', 255)->nullable();
                $table->string('subtitle_en', 255)->nullable();
                $table->longText('description')->nullable();
                $table->longText('description_ar')->nullable();
                $table->longText('description_en')->nullable();
                $table->string('image', 255)->nullable();
                $table->string('extra_image', 255)->nullable();
                $table->string('link', 255)->nullable();
                $table->string('btn_text_ar', 100)->nullable();
                $table->string('btn_text_en', 100)->nullable();
                $table->unsignedTinyInteger('rating')->nullable()->default(5);
                $table->string('price', 100)->nullable();
                $table->integer('sort')->default(0);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index(['type', 'status', 'sort'], 'idx_type_status_sort');
            });
        }

        // 9. post_items
        if (!Schema::hasTable('post_items')) {
            Schema::create('post_items', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('post_id');
                $table->string('email', 255)->nullable();
                $table->string('title_ar', 255)->nullable();
                $table->string('title_en', 255)->nullable();
                $table->string('subtitle_ar', 255)->nullable();
                $table->string('subtitle_en', 255)->nullable();
                $table->longText('description_ar')->nullable();
                $table->longText('description_en')->nullable();
                $table->string('image', 255)->nullable();
                $table->string('extra_image', 255)->nullable();
                $table->string('link', 255)->nullable();
                $table->string('btn_text_ar', 100)->nullable();
                $table->string('btn_text_en', 100)->nullable();
                $table->unsignedTinyInteger('rating')->nullable()->default(5);
                $table->string('price', 100)->nullable();
                $table->integer('sort')->default(0);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

                $table->index('post_id', 'fk_post_items_post_id');
                $table->foreign('post_id', 'fk_post_items_post_id')
                    ->references('id')->on('posts')
                    ->onDelete('cascade');
            });
        }

        // 10. join_us_submissions
        if (!Schema::hasTable('join_us_submissions')) {
            Schema::create('join_us_submissions', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->string('phone', 50);
                $table->unsignedTinyInteger('age');
                $table->string('country', 100);
                $table->string('governorate', 100);
                $table->string('training_level', 100);
                $table->mediumText('goal');
                $table->string('injuries', 255);
                $table->mediumText('injury_details')->nullable();
                $table->mediumText('reason');
                $table->mediumText('routine');
                $table->enum('status', ['new', 'contacted', 'converted', 'rejected'])->default('new');
                $table->mediumText('notes')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            });
        }

        // 11. message
        if (!Schema::hasTable('message')) {
            Schema::create('message', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id', true);
                $table->string('name', 100);
                $table->string('email', 100);
                $table->string('telephone', 15)->nullable();
                $table->text('message');
                $table->enum('is_read', ['yes', 'no'])->nullable()->default('no');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            });
        }

        // 12. settings
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->string('name', 100);
                $table->text('value')->nullable();
                $table->string('shown_name_ar', 150);
                $table->string('shown_name_en', 150);
                $table->string('input_type', 100);
                $table->text('option_list')->nullable();
                $table->string('group_name', 255);
                $table->integer('sort');
                $table->enum('is_visible', ['yes', 'no'])->default('yes');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        // 13. permissions
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id', true);
                $table->string('route_name', 255);
                $table->integer('permission_group_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        // 14. auth_session
        if (!Schema::hasTable('auth_session')) {
            Schema::create('auth_session', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigInteger('id', true);
                $table->string('guard_name', 50);
                $table->string('access_token', 100);
                $table->integer('user_id');
                $table->string('ip', 45);
                $table->text('user_agent');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index('guard_name', 'guard_name');
                $table->index('access_token', 'access_token');
                $table->unique('access_token', 'access_token_2');
                $table->index('user_id', 'user_id');
            });
        }

        // 15. activity_log (dump version matching app/Models/Activity.php)
        if (!Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->increments('id');
                $table->string('log_name', 191)->nullable();
                $table->string('description', 191)->nullable();
                $table->integer('subject_id')->nullable();
                $table->string('subject_type', 191)->nullable();
                $table->integer('causer_id')->nullable();
                $table->string('causer_type', 191)->nullable();
                $table->string('ip', 255)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('url')->nullable();
                $table->text('properties')->nullable();
                $table->string('event', 255)->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();

                $table->index('log_name', 'activity_log_log_name_index');
                $table->index('subject_id', 'subject_id');
            });
        }

        // 16. activate_section
        if (!Schema::hasTable('activate_section')) {
            Schema::create('activate_section', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id');
                $table->string('name', 100);
                $table->enum('value', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        }

        // 17. language
        if (!Schema::hasTable('language')) {
            Schema::create('language', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->integer('id', true);
                $table->string('name', 32);
                $table->string('code', 5);
                $table->string('locale', 255);
                $table->string('image', 64);
                $table->string('directory', 32);
                $table->integer('sort_order')->default(0);
                $table->boolean('status');

                $table->index('name', 'name');
            });
        }

        // 18. social_links
        if (!Schema::hasTable('social_links')) {
            Schema::create('social_links', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->increments('id');
                $table->string('title', 255);
                $table->string('url', 500);
                $table->string('icon', 100)->nullable()->default('fa-link');
                $table->boolean('is_active')->nullable()->default(1);
                $table->integer('order')->nullable()->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        // 19. password_resets
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->string('email', 255)->primary();
                $table->string('token', 255);
                $table->timestamp('created_at')->nullable();
            });
        }

        // 20. failed_jobs
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->string('uuid', 255)->unique('failed_jobs_uuid_unique');
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // 21. personal_access_tokens
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->string('tokenable_type', 255);
                $table->unsignedBigInteger('tokenable_id');
                $table->string('name', 255);
                $table->string('token', 64)->unique('personal_access_tokens_token_unique');
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('language');
        Schema::dropIfExists('activate_section');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('auth_session');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('message');
        Schema::dropIfExists('join_us_submissions');
        Schema::dropIfExists('post_items');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('nutrition_plans');
        Schema::dropIfExists('workout_logs');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('workout_plans');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('user');
        Schema::dropIfExists('permission_groups');
    }
};
