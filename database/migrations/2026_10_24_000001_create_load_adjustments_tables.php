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
        // 1. Ensure parent table exists (adopting preexisting local table if present)
        if (!Schema::hasTable('trainee_load_adjustments')) {
            Schema::create('trainee_load_adjustments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('trainee_id');
                $table->unsignedBigInteger('workout_plan_id')->nullable();
                $table->unsignedBigInteger('exercise_id')->nullable();
                $table->string('workout_day_name', 255)->nullable();
                $table->string('exercise_name', 150);
                $table->string('prescribed_load', 100)->nullable();
                $table->string('reported_load', 255)->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 20)->default('pending');
                $table->text('coach_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->index(['trainee_id', 'created_at'], 'idx_tla_trainee_created');
                $table->index('workout_plan_id', 'idx_tla_workout_plan');
                $table->index(['exercise_id', 'created_at'], 'idx_tla_exercise_created');
                $table->index(['status', 'created_at'], 'idx_tla_status_created');
                $table->index('reviewed_by', 'idx_tla_reviewed_by');
            });
        }

        // 2. Child table for independent set-level items
        if (!Schema::hasTable('trainee_load_adjustment_items')) {
            Schema::create('trainee_load_adjustment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('load_adjustment_id');
                $table->unsignedTinyInteger('set_number');
                $table->string('base_weight', 50)->nullable();
                $table->string('suggested_weight', 50);
                $table->string('approved_weight', 50)->nullable();
                $table->string('status', 25)->default('pending');
                $table->text('coach_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('conflict_detected_at')->nullable();
                $table->string('conflict_current_weight', 50)->nullable();
                $table->timestamps();

                $table->foreign('load_adjustment_id', 'fk_tlai_adjustment')
                    ->references('id')->on('trainee_load_adjustments')
                    ->onDelete('cascade');

                $table->index(['load_adjustment_id', 'set_number'], 'idx_tlai_adjustment_set');
                $table->index('status', 'idx_tlai_status');
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
        Schema::dropIfExists('trainee_load_adjustment_items');
        // Do not drop parent table automatically to protect legacy audit records
    }
};
