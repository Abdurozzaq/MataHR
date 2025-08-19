<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->upsert([
            ['key' => 'office_lat', 'value' => '-6.2546847'],
            ['key' => 'office_lon', 'value' => '106.624721'],
            ['key' => 'office_name', 'value' => 'Kantor Pusat'],
            ['key' => 'office_radius', 'value' => '100'],
        ], ['key'], ['value']);
    }
}
