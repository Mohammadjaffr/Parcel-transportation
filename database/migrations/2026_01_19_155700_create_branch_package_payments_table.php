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
        Schema::create('branch_package_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_shipment_package_id')
                ->constrained('branch_shipment_package')
                ->onDelete('cascade');
            $table->decimal('paid_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'other'])
                ->default('cash');
            $table->string('bond_number')->nullable();
            $table->dateTime('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_package_payments');
    }
};
