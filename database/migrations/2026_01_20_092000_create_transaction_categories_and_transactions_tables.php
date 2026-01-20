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
        // Create transaction_categories table
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['in', 'out']);
            $table->string('code', 50)->unique()->nullable()->comment('System hook code, e.g., SHIPMENT_PAYMENT');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->unsignedBigInteger('transaction_category_id');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('reference_number', 50)->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('shipment_id')->nullable()->comment('Link to shipment if applicable');
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
        Schema::dropIfExists('transaction_categories');
    }
};
