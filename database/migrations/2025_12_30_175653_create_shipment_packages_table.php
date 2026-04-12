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
        Schema::create('shipment_packages', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number');
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('sender_branch_id')->constrained('branches');
            $table->foreignId('receiver_branch_id')->constrained('branches');
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->unique(['tracking_number', 'app_id']);
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_packages');
    }
};
