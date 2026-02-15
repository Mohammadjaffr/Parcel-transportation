<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_header_id')->constrained('receipt_headers')->cascadeOnDelete();
            $table->string('number')->comment('رقم السند/البوليصة');
            $table->string('sender_name')->nullable();
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('package_type')->default('carton')->comment('نوع الطرد: كرتون، كيس، ظرف');
            $table->text('item_notes')->nullable()->comment('ملاحظات خاصة بالطرد');
            $table->boolean('is_delivered')->default(false)->comment('هل تم تسليم الطرد؟');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_items');
    }
};