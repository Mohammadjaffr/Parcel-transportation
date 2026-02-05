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
            'name' => 'سعيد العويد',
            'phone' => '967774996316',
            'whatsapp_number' => '967774996316',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'admin',
            'branch_code' => 'MKL',
        ]);
        
        User::create([
            'name' => 'محمد السعدي',
            'phone' => '967780236552',
            'whatsapp_number' => '966500000002',
            'phone_verified_at'=> now(),
            'password' => '14171417Nn',
            'type' => 'super_admin',
            'branch_code' => 'ADN',
        ]);
    }
}