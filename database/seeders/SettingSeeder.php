<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'name'          => 'company_name',
                'value'         => 'Cap Mahmoud Shaltout',
                'shown_name_ar' => 'اسم الموقع',
                'shown_name_en' => 'Company Name',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'system',
                'sort'          => 100,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'site_title',
                'value'         => 'Mahmoud Shaltout',
                'shown_name_ar' => 'عنوان الموقع',
                'shown_name_en' => 'Site Title',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'email',
                'value'         => 'coach@example.com',
                'shown_name_ar' => 'البريد الإلكتروني',
                'shown_name_en' => 'Email',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'mobile',
                'value'         => '+201000000000',
                'shown_name_ar' => 'رقم الهاتف',
                'shown_name_en' => 'Mobile',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'whatsapp',
                'value'         => '+201000000000',
                'shown_name_ar' => 'واتساب',
                'shown_name_en' => 'WhatsApp',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'address',
                'value'         => 'Cairo, Egypt',
                'shown_name_ar' => 'العنوان',
                'shown_name_en' => 'Address',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'system',
                'sort'          => 100,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'facebook',
                'value'         => 'https://facebook.com/',
                'shown_name_ar' => 'فيسبوك',
                'shown_name_en' => 'Facebook',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'instagram',
                'value'         => 'https://instagram.com/',
                'shown_name_ar' => 'انستجرام',
                'shown_name_en' => 'Instagram',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'tiktok',
                'value'         => 'https://tiktok.com/',
                'shown_name_ar' => 'تيك توك',
                'shown_name_en' => 'TikTok',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'logo',
                'value'         => 'assets/web/img/icons/logo.ico',
                'shown_name_ar' => 'الشعار',
                'shown_name_en' => 'Logo',
                'input_type'    => 'text',
                'option_list'   => null,
                'group_name'    => 'landing_page_home',
                'sort'          => 0,
                'is_visible'    => 'yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('settings')->upsert($settings, ['name'], ['value', 'shown_name_ar', 'shown_name_en', 'input_type', 'group_name', 'sort', 'is_visible', 'updated_at']);
    }
}
