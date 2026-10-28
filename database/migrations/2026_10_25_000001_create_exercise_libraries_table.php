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
        // 1. Create exercise_libraries table
        if (!Schema::hasTable('exercise_libraries')) {
            Schema::create('exercise_libraries', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                $table->bigIncrements('id');
                $table->string('name', 150);
                $table->string('video_url', 255)->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('name', 'idx_exercise_libraries_name');
                $table->index('status', 'idx_exercise_libraries_status');
            });
        }

        // 2. Add nullable exercise_library_id foreign key to exercises table safely
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                if (!Schema::hasColumn('exercises', 'exercise_library_id')) {
                    $table->unsignedBigInteger('exercise_library_id')->nullable()->after('workout_plan_id');

                    $table->foreign('exercise_library_id', 'fk_exercise_library')
                        ->references('id')->on('exercise_libraries')
                        ->onDelete('set null');

                    $table->index('exercise_library_id', 'idx_exercise_library_id');
                }
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
        if (Schema::hasTable('exercises') && Schema::hasColumn('exercises', 'exercise_library_id')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->dropForeign('fk_exercise_library');
                $table->dropIndex('idx_exercise_library_id');
                $table->dropColumn('exercise_library_id');
            });
        }

        Schema::dropIfExists('exercise_libraries');
    }
};
