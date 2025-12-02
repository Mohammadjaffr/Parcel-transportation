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
        Schema::create('vehicle_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2);
            $table->decimal('min_distance_km', 5, 2)->default(0);
            $table->decimal('max_distance_km', 5, 2)->default(999.99);
            $table->timestamps();

            $table->unique(['vehicle_id', 'min_distance_km', 'max_distance_km'], 'vp_vehicle_distance_unique');
            $table->index(['vehicle_id'], 'vp_vehicle_id_index');
            $table->index(['min_distance_km', 'max_distance_km'], 'vp_distance_range_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_pricings');
    }
};
