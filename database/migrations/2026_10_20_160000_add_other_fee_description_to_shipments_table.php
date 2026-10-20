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
            if (!Schema::hasColumn('shipments', 'other_fee_description')) {
                $table->string('other_fee_description', 255)
                    ->nullable()
                    ->after('other_fee')
                    ->comment('توضيح أجور أخرى');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'other_fee_description')) {
                $table->dropColumn('other_fee_description');
            }
        });
    }
};
