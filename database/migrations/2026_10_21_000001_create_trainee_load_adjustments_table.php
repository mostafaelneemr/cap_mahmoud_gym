<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيل جدول تقارير تعديل الأحمال التدريبية (trainee_load_adjustments).
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('trainee_load_adjustments')) {
            Schema::create('trainee_load_adjustments', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');

                // مرجع المتدرب صاحب التقرير (يقبل null للحفاظ على السجل عند الحذف الفيزيائي)
                $table->unsignedBigInteger('trainee_id')->nullable();
                $table->string('trainee_name_snapshot', 150)->nullable();

                // مراجع اليوم التدريبي والتمرين
                $table->unsignedBigInteger('workout_plan_id')->nullable();
                $table->unsignedBigInteger('exercise_id')->nullable();

                // لقطات السياق التاريخي الثابتة لحماية التقرير عند تعديل أو أرشفة أو حذف التمرين
                $table->string('workout_day_name', 100);
                $table->string('exercise_name', 150);
                $table->string('prescribed_load', 100)->nullable();

                // بيانات التقرير المقدمة من المتدرب
                $table->string('reported_load', 100);
                $table->text('notes')->nullable();

                // دورة المراجعة والتدقيق الإداري
                $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
                $table->text('coach_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();

                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                // الفهارس
                $table->index(['trainee_id', 'status'], 'idx_load_adj_trainee_status');
                $table->index('exercise_id', 'idx_load_adj_exercise');
                $table->index('workout_plan_id', 'idx_load_adj_plan');
                $table->index('created_at', 'idx_load_adj_created_at');

                // المفاتيح الأجنبية الآمنة
                if (Schema::hasTable('trainees')) {
                    $table->foreign('trainee_id', 'fk_load_adj_trainee')
                        ->references('id')->on('trainees')
                        ->onDelete('set null');
                }

                if (Schema::hasTable('workout_plans')) {
                    $table->foreign('workout_plan_id', 'fk_load_adj_plan')
                        ->references('id')->on('workout_plans')
                        ->onDelete('set null');
                }

                if (Schema::hasTable('exercises')) {
                    $table->foreign('exercise_id', 'fk_load_adj_exercise')
                        ->references('id')->on('exercises')
                        ->onDelete('set null');
                }

                if (Schema::hasTable('user')) {
                    $table->foreign('reviewed_by', 'fk_load_adj_reviewer')
                        ->references('id')->on('user')
                        ->onDelete('set null');
                }
            });
        }
    }

    /**
     * التراجع عن الترحيل.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('trainee_load_adjustments')) {
            Schema::dropIfExists('trainee_load_adjustments');
        }
    }
};
