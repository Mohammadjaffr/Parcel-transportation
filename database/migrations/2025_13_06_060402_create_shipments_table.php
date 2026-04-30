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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // 1. فروع الإرسال والاستقبال (باستخدام ID الفرع)
            $table->foreignId('sender_branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('sender_office_branch_id')->nullable()->constrained('office_branches')->cascadeOnDelete();
            $table->foreignId('receiver_branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('receiver_office_branch_id')->nullable()->constrained('office_branches')->cascadeOnDelete();

            // 2. العملاء (مرسل ومستقبل)
            $table->foreignId('sender_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('receiver_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // 3. معلومات الطرد
            $table->foreignId('shipment_package_id')->nullable()->constrained('shipment_packages')->nullOnDelete();
            $table->string('code')->nullable(); 
            $table->string('bond_number')->unique(); 
            $table->string('package_type')->nullable(); 
            $table->decimal('weight', 8, 2)->nullable();
            
            // 4. الحقول الخاصة بالعسل
            $table->string('no_gallons_honey')->nullable();
            $table->string('no_honey_jars')->nullable();
            
            // 5. حالات الشحنة والدفع
            $table->enum('status', ['pending','in_transit','received_at_branch','delivered','cancelled','returned'])->default('pending');
            $table->enum('customer_debt_status', ['pending', 'partially_paid', 'fully_paid', 'overdue'])->nullable()->default('pending');
            
            // 6. المبالغ وطرق الدفع
            $table->enum('payment_method', ['prepaid', 'cod', 'customer_credit', 'partial_payment'])->default('prepaid');
            $table->decimal('partial_amount', 10, 2)->nullable()->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // 7. ملاحظات
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);

            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};