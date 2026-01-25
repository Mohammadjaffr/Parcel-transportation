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
        Schema::create('branch_ledgers', function (Blueprint $table) {
            $table->id();
            
            // Branch this entry belongs to (owner of this ledger line)
            $table->string('branch_code');
            $table->foreign('branch_code')
                  ->references('code')
                  ->on('branches')
                  ->onDelete('cascade');
            
            // The counterparty branch
            $table->string('related_branch_code');
            $table->foreign('related_branch_code')
                  ->references('code')
                  ->on('branches')
                  ->onDelete('cascade');
            
            // Reference to the shipment
            $table->foreignId('shipment_id')
                  ->constrained('shipments')
                  ->onDelete('cascade');
            
            // Type of ledger entry
            $table->string('type'); // 'shipment_cod', 'settlement'
            
            // Double-entry: Debit = owes, Credit = is owed
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Index for common queries
            $table->index(['branch_code', 'type']);
            $table->index(['shipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_ledgers');
    }
};
