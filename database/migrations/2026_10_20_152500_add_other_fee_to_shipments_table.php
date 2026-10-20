<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'other_fee')) {
                $table->decimal('other_fee', 10, 2)
                    ->nullable()
                    ->after('package_commission_amount')
                    ->comment('أجور أخرى');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'other_fee')) {
                $table->dropColumn('other_fee');
            }
        });
    }
};
