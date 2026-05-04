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
        Schema::create('cash_register_closings', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->unsignedBigInteger('closed_by');
            $table->decimal('expected_balance', 10, 2)->comment('System balance before closing');
            $table->decimal('actual_cash', 10, 2)->comment('Physical cash counted by user');
            $table->decimal('difference', 10, 2)->comment('Actual - Expected');
            $table->decimal('transferred_amount', 10, 2)->default(0)->comment('Amount sent to HQ');
            $table->decimal('remaining_cash', 10, 2)->default(0)->comment('Cash left in box (Float)');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            // $table->foreign('branch_code')
            //     ->references('code')
            //     ->on('branches')
            //     ->onDelete('cascade');

            $table->foreign('closed_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            // Indexes
            $table->index('branch_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_closings');
    }
};
