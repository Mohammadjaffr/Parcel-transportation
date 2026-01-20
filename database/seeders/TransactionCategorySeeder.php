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
                'name' => 'Shipment Payment',
                'type' => 'in',
                'code' => 'SHIPMENT_PAYMENT',
                'is_active' => true,
            ],
            [
                'name' => 'Deposit from HQ',
                'type' => 'in',
                'code' => 'HQ_DEPOSIT',
                'is_active' => true,
            ],
            [
                'name' => 'Cash Surplus',
                'type' => 'in',
                'code' => 'CASH_SURPLUS',
                'is_active' => true,
            ],

            // Expense categories
            [
                'name' => 'Employee Salary',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Electricity Bill',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Daily Expenses',
                'type' => 'out',
                'code' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer to HQ',
                'type' => 'out',
                'code' => 'HQ_TRANSFER',
                'is_active' => true,
            ],
            [
                'name' => 'Cash Shortage',
                'type' => 'out',
                'code' => 'CASH_SHORTAGE',
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

