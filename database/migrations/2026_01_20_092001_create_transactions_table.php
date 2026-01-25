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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique()->nullable();
            $table->string('branch_code');
            $table->unsignedBigInteger('transaction_category_id');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('reference_number', 50)->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('shipment_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamps();

            // Foreign key constraints
            // CRITICAL: branch_code references branches.code (string primary key)
            $table->foreign('branch_code')
                ->references('code')
                ->on('branches')
                ->onDelete('cascade');

            $table->foreign('transaction_category_id')
                ->references('id')
                ->on('transaction_categories')
                ->onDelete('restrict');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('shipment_id')
                ->references('id')
                ->on('shipments')
                ->onDelete('set null');

            // Indexes for performance
            $table->index('branch_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
