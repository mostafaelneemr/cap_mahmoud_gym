<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'id'         => 1,
                'name'       => 'English',
                'code'       => 'en-gb',
                'locale'     => 'en_US.UTF-8,en_US,en-gb,english',
                'image'      => '',
                'directory'  => 'english',
                'sort_order' => 1,
                'status'     => 1,
            ],
            [
                'id'         => 2,
                'name'       => 'عربي',
                'code'       => 'ar',
                'locale'     => 'ar_AE.UTF-8,ar_AE,ar',
                'image'      => '',
                'directory'  => '',
                'sort_order' => 2,
                'status'     => 1,
            ],
        ];

        foreach ($languages as $lang) {
            DB::table('language')->updateOrInsert(['code' => $lang['code']], $lang);
        }
    }
}
