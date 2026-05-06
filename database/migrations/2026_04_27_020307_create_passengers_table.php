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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->date('date');                         
            $table->string('passenger_number');  
            $table->enum('status', ['pending', 'completed', 'cancel'])->default('pending');          
            $table->string('location');                    
            $table->integer('count')->default(1);         
            $table->decimal('total_commission', 10, 2);   
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');  
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->text('note')->nullable();             
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
