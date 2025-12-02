<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('app_settings')->delete();
        DB::table('app_settings')->insert([
            [
                'commission_rate' => 10, 
                'auto_assign_to_drivers' => false,
                'version' => '1.0.1',
                'update_url' => 'https://www.google.com/',
                'company_website' => 'https://www.instagram.com/codak.dev?igsh=emtmaDkzOWNxcTlm',
                'company_whatsapp' => '+967774712979',
                'ref_no' => '1007',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
