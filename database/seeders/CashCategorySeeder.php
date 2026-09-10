<?php

namespace Database\Seeders;

use App\Models\App;
use App\Models\CashCategory;
use Illuminate\Database\Seeder;

class CashCategorySeeder extends Seeder
{
    public function run(): void
    {
        $apps = App::all();
        foreach ($apps as $app) {
            CashCategory::seedDefaultCategoriesForApp($app->id);
        }
    }
}