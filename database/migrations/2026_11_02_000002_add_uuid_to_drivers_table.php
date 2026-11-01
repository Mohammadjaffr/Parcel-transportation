<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('drivers', 'uuid')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id')->index();
            });

            // تعبئة UUID فريد لجميع السائقين الحاليين
            $drivers = DB::table('drivers')->whereNull('uuid')->orWhere('uuid', '')->get();
            foreach ($drivers as $driver) {
                DB::table('drivers')->where('id', $driver->id)->update([
                    'uuid' => (string) Str::uuid(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('drivers', 'uuid')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

