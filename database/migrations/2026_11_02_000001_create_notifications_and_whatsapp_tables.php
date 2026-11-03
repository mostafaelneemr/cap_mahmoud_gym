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
        // 1. In-App Notifications table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id'); // Matches user.id int(11)
                $table->string('type', 60); // e.g. load_adjustment, workout_plan, nutrition_plan, progress_checkin
                $table->string('title', 255);
                $table->text('message');
                $table->string('action_url', 255)->nullable();
                $table->string('icon', 60)->nullable()->default('fa-bell');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
                $table->index(['user_id', 'created_at'], 'idx_notifications_user_created');
            });
        }

        // 2. Trainee WhatsApp Opt-out field
        if (Schema::hasTable('trainees') && !Schema::hasColumn('trainees', 'whatsapp_opt_out')) {
            Schema::table('trainees', function (Blueprint $table) {
                $table->boolean('whatsapp_opt_out')->default(false)->after('status');
                $table->index('whatsapp_opt_out', 'idx_trainees_whatsapp_opt_out');
            });
        }

        // 3. WhatsApp Campaigns table
        if (!Schema::hasTable('whatsapp_campaigns')) {
            Schema::create('whatsapp_campaigns', function (Blueprint $table) {
                $table->id();
                $table->integer('coach_id'); // Matches user.id int(11)
                $table->string('title', 255);
                $table->text('message_template');
                $table->unsignedInteger('total_recipients')->default(0);
                $table->unsignedInteger('opened_count')->default(0);
                $table->unsignedInteger('confirmed_count')->default(0);
                $table->unsignedInteger('skipped_count')->default(0);
                $table->string('status', 30)->default('draft'); // draft, in_progress, completed, cancelled
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['coach_id', 'created_at'], 'idx_wac_coach_created');
                $table->index('status', 'idx_wac_status');
            });
        }

        // 4. WhatsApp Campaign Recipients table
        if (!Schema::hasTable('whatsapp_campaign_recipients')) {
            Schema::create('whatsapp_campaign_recipients', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->unsignedBigInteger('trainee_id');
                $table->string('recipient_name', 255);
                $table->string('phone_raw', 50)->nullable();
                $table->string('phone_normalized', 50)->nullable();
                $table->text('message_rendered');
                $table->string('status', 30)->default('pending'); // pending, opened, confirmed, skipped, failed
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('campaign_id', 'fk_wacr_campaign')
                    ->references('id')->on('whatsapp_campaigns')
                    ->onDelete('cascade');

                $table->foreign('trainee_id', 'fk_wacr_trainee')
                    ->references('id')->on('trainees')
                    ->onDelete('cascade');

                $table->index(['campaign_id', 'status'], 'idx_wacr_campaign_status');
                $table->index(['trainee_id', 'created_at'], 'idx_wacr_trainee_created');
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
        Schema::dropIfExists('whatsapp_campaign_recipients');
        Schema::dropIfExists('whatsapp_campaigns');
        if (Schema::hasTable('trainees') && Schema::hasColumn('trainees', 'whatsapp_opt_out')) {
            Schema::table('trainees', function (Blueprint $table) {
                $table->dropColumn('whatsapp_opt_out');
            });
        }
        Schema::dropIfExists('notifications');
    }
};