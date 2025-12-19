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
            $table->foreignId('sender_branch_code')->constrained()->onDelete('cascade');
            $table->foreignId('receiver_branch_code')->constrained()->onDelete('cascade');
            $table->foreignId('sender_customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('receiver_customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bond_number')->unique();
            $table->string('no_gallons_honey');
            $table->string('no_honey_jars');
            $table->enum('status', ['pending', 'in_transit', 'delivered'])->default('pending');
            $table->enum('customer_debt_status', ['pending', 'partially_paid', 'fully_paid', 'overdue'])->default('pending');
            $table->decimal('weight', 8, 2)->nullable();
            $table->enum('payment_method', ['prepaid', 'cod','customer_credit']);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            //
            $table->string('code');
            $table->string('package_type')->nullable();
            
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