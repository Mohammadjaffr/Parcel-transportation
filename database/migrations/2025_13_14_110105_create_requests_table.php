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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('from_city');

            // بيانات المستلم
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('to_city');

            // الفرع (إن كانت الشركة لها أكثر من فرع)
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');

            // تفاصيل الطرد
            $table->string('package_type')->nullable(); // نوع الطرد
            $table->float('weight')->nullable(); // وزن الطرد

            // الدفع
            $table->enum('payment_method', ['prepaid', 'cod'])->default('cod');
            $table->decimal('cod_amount', 10, 2)->nullable(); // مبلغ عند الاستلام

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
        Schema::dropIfExists('requests');
    }
};