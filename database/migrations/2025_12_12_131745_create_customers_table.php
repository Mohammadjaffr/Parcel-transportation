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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 10);
            $table->foreign('branch_code')->references(columns: 'code')->on('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('whatsapp_number')->nullable();
            $table->timestamps();
            $table->unique(['phone', 'branch_code']);
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};