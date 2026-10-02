<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Permission Groups
        $superAdminGroupId = DB::table('permission_groups')->insertGetId([
            'id'                      => 119,
            'name'                    => 'super admin',
            'default_route'           => 'system.dashboard',
            'new_admin_default_route' => 'system.dashboard',
            'system'                  => 'niceone_admin',
            'is_supervisor'           => 'yes',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        $trainerGroupId = DB::table('permission_groups')->insertGetId([
            'id'                      => 125, // Kept as 125 for compatibility with TraineeService
            'name'                    => 'trainer',
            'default_route'           => 'system.dashboard.trainer',
            'new_admin_default_route' => 'system.dashboard.trainer',
            'system'                  => 'niceone_admin',
            'is_supervisor'           => 'no',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        $captainGroupId = DB::table('permission_groups')->insertGetId([
            'id'                      => 126,
            'name'                    => 'captain',
            'default_route'           => 'system.dashboard',
            'new_admin_default_route' => 'system.dashboard',
            'system'                  => 'gym',
            'is_supervisor'           => 'no',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // 2. Collect routes from app/Modules/System/Permissions.php definition
        $permissionDefinitions = [
            // Users
            'system.user.index', 'system.user.show', 'system.user.create', 'system.user.store',
            'system.user.edit', 'system.user.update', 'system.get-user-activity-log',
            // Permission Groups
            'system.permission-group.index', 'system.permission-group.create', 'system.permission-group.store',
            'system.permission-group.edit', 'system.permission-group.update',
            // Trainees
            'system.trainee.index', 'system.trainee.show', 'system.trainee.create', 'system.trainee.store',
            'system.trainee.edit', 'system.trainee.update', 'system.trainee.reset-plan',
            'system.trainee.get-activity-log', 'system.trainee.get-auth-session', 'system.trainee.get-workout',
            // Workouts
            'system.workout.index', 'system.workout.create', 'system.workout.store',
            'system.workout.edit', 'system.workout.update', 'system.workout.updateDay', 'system.workout.destroy',
            // Nutrition
            'system.nutrition.index', 'system.nutrition.create', 'system.nutrition.store',
            'system.nutrition.show', 'system.nutrition.destroy', 'system.nutrition.my-plan',
            // Social Links
            'system.social-links.index', 'system.social-links.create', 'system.social-links.store',
            'system.social-links.edit', 'system.social-links.update', 'system.social-links.destroy',
            // Join Us
            'system.join-us.index', 'system.join-us.show', 'system.join-us.update-status',
            // Messages
            'system.message.index', 'system.message.update-status',
            // Website CMS
            'system.website.index', 'system.website.show',
            'system.website.settings.update', 'system.website.section.update',
            'system.website.items.store', 'system.website.items.update', 'system.website.items.destroy',
            'system.website.posts.store', 'system.website.posts.update', 'system.website.posts.destroy',
            // Settings
            'system.setting.index', 'system.setting.update',
            // Activate Sections
            'system.activate.index', 'system.activate.update',
            // Activity Log
            'system.activity-log.index', 'system.activity-log.show',
            // Auth Sessions
            'system.auth-sessions.index', 'system.get-auth-session', 'system.auth-sessions.show', 'system.auth-sessions.destroy',
            // Languages
            'system.language.index', 'system.language.create', 'system.language.store', 'system.language.edit', 'system.language.update',
        ];

        // Insert permissions for super admin
        $superAdminRows = [];
        foreach (array_unique($permissionDefinitions) as $route) {
            $superAdminRows[] = [
                'route_name'          => $route,
                'permission_group_id' => $superAdminGroupId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        DB::table('permissions')->insert($superAdminRows);

        // Captain (Coach) permissions - gym domain operations
        $captainRoutes = [
            'system.dashboard',
            'system.trainee.index', 'system.trainee.show', 'system.trainee.create', 'system.trainee.store',
            'system.trainee.edit', 'system.trainee.update', 'system.trainee.reset-plan',
            'system.trainee.get-activity-log', 'system.trainee.get-auth-session', 'system.trainee.get-workout',
            'system.workout.index', 'system.workout.create', 'system.workout.store',
            'system.workout.edit', 'system.workout.update', 'system.workout.updateDay', 'system.workout.destroy',
            'system.nutrition.index', 'system.nutrition.create', 'system.nutrition.store',
            'system.nutrition.show', 'system.nutrition.destroy', 'system.nutrition.my-plan',
            'system.join-us.index', 'system.join-us.show', 'system.join-us.update-status',
            'system.message.index', 'system.message.update-status',
            'system.website.index', 'system.website.settings.update', 'system.website.section.update',
            'system.website.items.store', 'system.website.items.update', 'system.website.items.destroy',
            'system.website.posts.store', 'system.website.posts.update', 'system.website.posts.destroy',
            'system.setting.index', 'system.setting.update',
        ];

        $captainRows = [];
        foreach (array_unique($captainRoutes) as $route) {
            $captainRows[] = [
                'route_name'          => $route,
                'permission_group_id' => $captainGroupId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        DB::table('permissions')->insert($captainRows);

        // Trainer (Trainee role) whitelist routes
        $trainerRoutes = [
            'system.dashboard.trainer',
            'system.nutrition.my-plan',
        ];
        $trainerRows = [];
        foreach ($trainerRoutes as $route) {
            $trainerRows[] = [
                'route_name'          => $route,
                'permission_group_id' => $trainerGroupId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        DB::table('permissions')->insert($trainerRows);
    }
}
