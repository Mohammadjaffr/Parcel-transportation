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
            if (!Schema::hasColumn('shipments', 'show_other_fee_in_receipt')) {
                $table->boolean('show_other_fee_in_receipt')
                    ->default(false)
                    ->after('other_fee_description')
                    ->comment('إظهار الأجور الأخرى وتوضيحها في سند الشحن');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'show_other_fee_in_receipt')) {
                $table->dropColumn('show_other_fee_in_receipt');
            }
        });
    }
};
