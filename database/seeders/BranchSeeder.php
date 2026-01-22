<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
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
            'address' => 'اربعين شقة',
            'phone' => '967774996316',
            'code' => 'MKL',
            'city' => 'حضرموت',
        ]);
    }
}
