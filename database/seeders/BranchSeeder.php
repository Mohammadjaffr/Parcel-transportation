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
            'app_id' => 1,
            'code' => 'MKL',
            'name' => 'فرع المكلا',
            'city'    => 'المكلا',
            'address' => 'الشارع العام، بجوار البنك الأهلي',
            'phone' => '967711111111',
        ]);

        Branch::create([
            'app_id' => 1,
            'code' => 'ADN',
            'name' => 'فرع عدن',
            'city'    => 'المكلا',
            'address' => 'الشارع العام، بجوار البنك الأهلي',
            'phone' => '967722222222',
        ]);

        Branch::create([
            'app_id' => 2,
            'code' => 'MKL',
            'name' => 'فرع المكلا',
            'city'    => 'المكلا',
            'address' => 'الشارع العام، بجوار البنك الأهلي',
            'phone' => '967711111111',
        ]);

        Branch::create([
            'app_id' => 2,
            'code' => 'ADN',
            'name' => 'فرع عدن',
            'city'    => 'المكلا',
            'address' => 'الشارع العام، بجوار البنك الأهلي',
            'phone' => '967722222222',
        ]);

        Branch::create([
            'app_id' => 3,
            'code' => 'MKL',
            'name' => 'فرع المكلا',
            'city'    => 'المكلا',
            'address' => 'الشارع العام، بجوار البنك الأهلي',
            'phone' => '967711111111',
        ]);
    }
}
