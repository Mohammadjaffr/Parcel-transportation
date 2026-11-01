<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('shipment_package_id')->nullable()->constrained('shipment_packages')->nullOnDelete();
            $table->foreignId('passenger_trip_id')->nullable()->constrained('passenger_trips')->nullOnDelete();
            $table->foreignId('cash_transaction_id')->nullable()->constrained('cash_transactions')->nullOnDelete();
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 15, 2);
            $table->string('description', 500);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Composite indexes for performance
            $table->index(['app_id', 'driver_id']);
            $table->index(['app_id', 'branch_id', 'driver_id']);
            $table->index(['driver_id', 'type']);
            $table->index(['driver_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_transactions');
    }
};

