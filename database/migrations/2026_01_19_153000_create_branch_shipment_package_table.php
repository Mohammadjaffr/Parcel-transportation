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
        Schema::create('branch_shipment_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_package_id')->constrained('shipment_packages')->onDelete('cascade');
            $table->string('branch_code');
            $table->foreign('branch_code')->references('code')->on('branches')->onDelete('cascade');
            $table->enum('status', ['pending', 'arrived', 'delivered'])->default('pending');
            $table->dateTime('arrival_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['shipment_package_id', 'branch_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_shipment_package');
    }
};
