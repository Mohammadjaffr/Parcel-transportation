<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\App;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App::create([
            'name' => 'مكتب الزاجل',
            'phone' => '967780236552',
            'email' => 'zajel@gmail.com',
            'is_active' => true,
        ]);

        App::create([
            'name' => 'مكتب السعدي',
            'phone' => '967780236551',
            'email' => 'admin@gmail.com',
            'is_active' => true,
        ]);

        App::create([
            'name' => 'مكتب عوض',
            'phone' => '967780236553',
            'email' => 'awad@gmail.com',
            'is_active' => false,
        ]);
    }
}
