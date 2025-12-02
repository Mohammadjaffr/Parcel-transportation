<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'عوض لشرم',
            'phone' => '967780236551',
            'whatsapp_number' => '966500000001',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'user',
            'is_banned' => false,
        ]);
        User::create([
            'name' => 'أحمد شرجبي',
            'phone' => '967780236552',
            'whatsapp_number' => '966500000002',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'admin',
            'is_banned' => false,
        ]);
    }
}
