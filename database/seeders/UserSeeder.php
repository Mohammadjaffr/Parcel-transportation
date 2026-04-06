<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        User::create([
            'app_id' => 1,
            'name' => 'سعيد العويد',
            'phone' => '967774996316',
            'whatsapp_number' => '967774996316',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'admin',
            'branch_id' => 1,
        ]);
        
        User::create([
            'app_id' => 2,
            'name' => 'محمد السعدي',
            'phone' => '967780236552',
            'whatsapp_number' => '966500000002',
            'phone_verified_at'=> now(),
            'password' => '14171417Nn',
            'type' => 'admin',
            'branch_id' => 2,

        ]);

        User::create([
            'app_id' => 3,
            'name' => 'عوض لشرم',
            'phone' => '967780236553',
            'whatsapp_number' => '966500000002',
            'phone_verified_at'=> now(),
            'password' => '14171417Nn',
            'type' => 'admin',
            'branch_id' => 3,

        ]);
    }
}