<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->decimal('price', 10, 2)->default(0); 
            $table->integer('duration_in_days')->default(30); 
            $table->integer('max_branches')->default(1);
            $table->integer('max_drivers')->default(0);
            $table->integer('max_shipments')->default(0);  
            $table->integer('max_packages')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};