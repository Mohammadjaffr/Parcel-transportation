<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_app_id')->constrained('apps')->onDelete('cascade');
            $table->foreignId('receiver_app_id')->constrained('apps')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');
            $table->timestamps();
            $table->unique(['sender_app_id', 'receiver_app_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_connections');
    }
};