<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipt_headers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->comment('رقم بيان الاستلام');
            $table->string('source_branch_code', 10)->comment('الفرع المرسل');
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete()->onUpdate('cascade')->comment('السائق');
            $table->string('destination_branch_code', 10)->comment('الفرع المستلم');
            $table->foreignId('created_by')->constrained('users');
            $table->text('general_notes')->nullable()->comment('ملاحظات عامة على الشحنة بالكامل');
            $table->timestamp('received_at')->useCurrent()->comment('وقت وصول السائق');
                                    
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys for branch codes (string type)
            $table->foreign('source_branch_code')->references('code')->on('branches')->cascadeOnDelete()->onUpdate('cascade');
            $table->foreign('destination_branch_code')->references('code')->on('branches')->cascadeOnDelete()->onUpdate('cascade');
            
            // Unique constraint: Receipt number must be unique per source branch
            $table->unique(['number', 'source_branch_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_headers');
    }
};