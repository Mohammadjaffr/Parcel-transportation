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
        Schema::create('branch_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('from_branch_id'); // الفرع المرسِل / الدافع
            $table->unsignedBigInteger('to_branch_id');   // الفرع المستلِم
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['cod', 'settlement']); // شحنة آجل / تسوية
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_transactions');
    }
};
