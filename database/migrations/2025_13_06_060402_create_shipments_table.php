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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('bond_number')->unique();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('from_city');
            $table->string('to_city');
            $table->string('no_gallons_honey');
            $table->string('no_honey_jars');
            $table->string('code');
            $table->string('package_type')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->enum('payment_method', ['prepaid', 'cod']);
            $table->decimal('cod_amount', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, in_transit, delivered
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};