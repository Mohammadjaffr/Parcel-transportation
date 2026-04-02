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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name');
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('address');
            $table->string('city');
            $table->string('phone');
            $table->string('map_link')->nullable();
            $table->unique(['app_id', 'code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};