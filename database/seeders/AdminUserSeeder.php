<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminGroup = DB::table('permission_groups')
            ->where('is_supervisor', 'yes')
            ->first();

        $adminEmail = env('SEED_ADMIN_EMAIL');
        $adminPassword = env('SEED_ADMIN_PASSWORD');

        if (empty($adminEmail)) {
            throw new \RuntimeException("Missing required environment variable SEED_ADMIN_EMAIL for AdminUserSeeder.");
        }

        if (empty($adminPassword)) {
            throw new \RuntimeException("Missing required environment variable SEED_ADMIN_PASSWORD for AdminUserSeeder.");
        }

        DB::table('user')->updateOrInsert(
            ['email' => $adminEmail],
            [
                'permission_group_id'  => $superAdminGroup ? $superAdminGroup->id : 119,
                'password'             => Hash::make($adminPassword),
                'name'                 => 'Administrator',
                'google_id'            => null,
                'mobile'               => '+201000000000',
                'status'               => 1,
                'user_type'            => 1,
                'default_language'     => 'en-gb',
                'force_reset_password' => 0,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]
        );
    }
}
