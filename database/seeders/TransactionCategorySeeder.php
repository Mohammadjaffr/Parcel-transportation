<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income categories
            [
                'name' => 'تحصيل شحنات',
                'type' => 'in',
                'code' => 'SHIPMENT_PAYMENT',
                'is_active' => true,
            ],
            [
                'name' => 'إيداع من المركز الرئيسي',
                'type' => 'in',
                'code' => 'HQ_DEPOSIT',
                'is_active' => true,
            ],
            [
                'name' => 'فائض نقدي (زيادة)',
                'type' => 'in',
                'code' => 'CASH_SURPLUS',
                'is_active' => true,
            ],

            // Expense categories
            [
                'name' => 'رواتب موظفين',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'فاتورة كهرباء',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'نثريات يومية',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'تحويل للمركز الرئيسي',
                'type' => 'out',
                'code' => 'HQ_TRANSFER',
                'is_active' => true,
            ],
            [
                'name' => 'عجز نقدي (نقص)',
                'type' => 'out',
                'code' => 'CASH_SHORTAGE',
                'is_active' => true,
            ],
            [
                'name' => 'تحويل لحساب العمقي',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'تحويل لحساب الكريمي',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'تحويل لحساب القطيبي',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
        ];

        // Use updateOrCreate to avoid duplicate errors
        foreach ($categories as $category) {
            if ($category['code']) {
                // For categories with codes, use code as unique identifier
                DB::table('transaction_categories')->updateOrInsert(
                    ['code' => $category['code']],
                    [
                        'name' => $category['name'],
                        'type' => $category['type'],
                        'is_active' => $category['is_active'],
                        'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                        'updated_at' => now(),
                    ]
                );
            } else {
                // For categories without codes, check by name and type
                $exists = DB::table('transaction_categories')
                    ->where('name', $category['name'])
                    ->where('type', $category['type'])
                    ->exists();
                
                if (!$exists) {
                    DB::table('transaction_categories')->insert([
                        'name' => $category['name'],
                        'type' => $category['type'],
                        'code' => null,
                        'is_active' => $category['is_active'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

