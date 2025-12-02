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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');   
            $table->foreignId('discount_code_id')->nullable()->references('id')->on('discount_codes')->onDelete('set null');
            $table->decimal('start_latitude', 10, 8);
            $table->decimal('start_longitude', 10, 8);
            $table->string('start_address');
            $table->decimal('end_latitude', 10, 8);
            $table->decimal('end_longitude', 10, 8);
            $table->string('end_address');
            $table->string('status');
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('app_commission_amount', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->enum('payment_method', ['cash', 'deposit'])->default('cash');
            $table->decimal('distance_km', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
