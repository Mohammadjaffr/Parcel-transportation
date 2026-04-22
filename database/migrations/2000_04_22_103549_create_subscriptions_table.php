<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->decimal('price_paid', 10, 2); 
            $table->integer('allowed_branches');
            $table->integer('allowed_drivers');
            $table->integer('allowed_shipments');
            $table->integer('allowed_packages');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->index(); 
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};