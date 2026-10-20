<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل ترحيلات المزامنة لإنشاء الجداول والأعمدة الناقصة بأمان ودون التأثير على البيانات الحالية.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. مزامنة حقل روابط الإحماء في جدول خطط التمرين (workout_plans.warmup_links)
        if (Schema::hasTable('workout_plans') && !Schema::hasColumn('workout_plans', 'warmup_links')) {
            Schema::table('workout_plans', function (Blueprint $table) {
                $table->json('warmup_links')->nullable()->after('warmup');
            });
        }

        // 2. مزامنة الحقول الناقصة في جدول التمارين (exercises: type, notes_en, machine_image)
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                if (!Schema::hasColumn('exercises', 'type')) {
                    $table->enum('type', ['warmup', 'main', 'post_workout'])
                        ->default('main')
                        ->after('workout_plan_id');
                }

                if (!Schema::hasColumn('exercises', 'notes_en')) {
                    $table->text('notes_en')
                        ->nullable()
                        ->after('tempo');
                }

                if (!Schema::hasColumn('exercises', 'machine_image')) {
                    $table->string('machine_image', 255)
                        ->nullable()
                        ->after('link');
                }
            });
        }

        // 3. إنشاء جدول تسجيلات متابعة وتقدم المتدربين (trainee_progress_checkins) إن لم يكن موجوداً
        if (!Schema::hasTable('trainee_progress_checkins')) {
            Schema::create('trainee_progress_checkins', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('trainee_id');
                $table->unsignedBigInteger('coach_id')->nullable();
                $table->date('checkin_date');
                $table->decimal('weight', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->text('coach_notes')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

                $table->index('checkin_date', 'idx_checkin_date');
                $table->index('trainee_id', 'idx_trainee_id');

                if (Schema::hasTable('trainees')) {
                    $table->foreign('trainee_id', 'fk_checkins_trainee')
                        ->references('id')->on('trainees')
                        ->onDelete('cascade');
                }
            });
        }

        // 4. إنشاء جدول صور تقدم المتدربين (trainee_progress_photos) إن لم يكن موجوداً
        if (!Schema::hasTable('trainee_progress_photos')) {
            Schema::create('trainee_progress_photos', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedBigInteger('checkin_id');
                $table->string('photo_path', 255);
                $table->string('caption', 150)->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

                $table->index('checkin_id', 'idx_checkin_id');

                if (Schema::hasTable('trainee_progress_checkins')) {
                    $table->foreign('checkin_id', 'fk_progress_photos_checkin')
                        ->references('id')->on('trainee_progress_checkins')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * التراجع عن الترحيلات مع مراعاة العلاقات والقيود المرجعية.
     *
     * @return void
     */
    public function down(): void
    {
        // 1. حذف جدول صور المتابعة
        if (Schema::hasTable('trainee_progress_photos')) {
            Schema::dropIfExists('trainee_progress_photos');
        }

        // 2. حذف جدول تسجيلات المتابعة
        if (Schema::hasTable('trainee_progress_checkins')) {
            Schema::dropIfExists('trainee_progress_checkins');
        }

        // 3. إزالة الأعمدة المضافة لجدول التمارين
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('exercises', 'machine_image')) {
                    $columnsToDrop[] = 'machine_image';
                }
                if (Schema::hasColumn('exercises', 'notes_en')) {
                    $columnsToDrop[] = 'notes_en';
                }
                if (Schema::hasColumn('exercises', 'type')) {
                    $columnsToDrop[] = 'type';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }

        // 4. إزالة حقل روابط الإحماء من جدول خطط التمرين
        if (Schema::hasTable('workout_plans') && Schema::hasColumn('workout_plans', 'warmup_links')) {
            Schema::table('workout_plans', function (Blueprint $table) {
                $table->dropColumn('warmup_links');
            });
        }
    }
};
