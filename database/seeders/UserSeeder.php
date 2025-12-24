<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Driver;
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
             Branch::create([
            'name' => 'القطن',
            'address' => 'القطن',
            'phone' => '966500000001',
            'code' => 'QTN',
            'city' => 'حضرموت',
        ]);
        Branch::create([
            'name' => 'عدن',
            'address' => 'المعلا',
            'phone' => '966500000001',
            'code' => 'ADN',
            'city' => 'عدن',
        ]);
        Branch::create([
            'name' => 'المكلا',
            'address' => 'الشرج',
            'phone' => '966500000001',
            'code' => 'MKL',
            'city' => 'حضرموت',
        ]);
        User::create([
            'name' => 'عوض لشرم',
            'phone' => '967780236551',
            'whatsapp_number' => '966500000001',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'admin',
            'branch_code' => 'QTN',
        ]);
        User::create([
            'name' => 'محمد السعدي',
            'phone' => '967780236552',
            'whatsapp_number' => '966500000002',
            'phone_verified_at'=> now(),
            'password' => '12121212',
            'type' => 'admin',
            'branch_code' => 'ADN',
        ]);
        // Driver::create([
        //     'name' => 'محمد صالح',
        //     'phone' => '967780236524',
        //     'city' => 'المكلا',
        //     'status' => 'active',
        // ]);
        // Driver::create([
        //     'name' => 'سالم علي',
        //     'phone' => '967780236564',
        //     'city' => 'القطن',
        //     'status' => 'active',
        // ]);
        
   
        
    }
}