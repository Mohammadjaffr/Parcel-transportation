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
            $table->string('pickup_location');
            $table->string('destination')->nullable();                  
            $table->integer('count')->default(1);         
            $table->decimal('office_commission', 10, 2)->default(0.00);
            $table->decimal('other_office_commission', 10, 2)->default(0.00);   
            $table->foreignId('trip_id')->nullable()->constrained('passenger_trips')->onDelete('set null'); 
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->nullOnDelete();
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
