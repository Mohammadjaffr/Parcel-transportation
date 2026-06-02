<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            // ==========================================
            // 1. المعرّفات الأساسية (Identifiers)
            // ==========================================
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bond_number')->unique(); 
            $table->string('code')->nullable(); 

            // ==========================================
            // 2. التوجيه والعلاقات (Routing & Relations)
            // ==========================================
            $table->foreignId('shipment_package_id')->nullable()->constrained('shipment_packages')->nullOnDelete();
            
            $table->foreignId('sender_branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('sender_office_branch_id')->nullable()->constrained('office_branches')->cascadeOnDelete();
            $table->foreignId('receiver_branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('receiver_office_branch_id')->nullable()->constrained('office_branches')->cascadeOnDelete();

            $table->foreignId('sender_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('receiver_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // ==========================================
            // 📦 3. كتلة الطرد العادي (Package Block)
            // ==========================================
            $table->string('package_type')->nullable(); 
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('package_fee', 10, 2)->default(0)->comment('أجرة شحن الطرد العادي');
            $table->decimal('package_commission_rate', 5, 2)->default(0)->comment('نسبة عمولة الطرد (مثال: 10%)');
            $table->decimal('package_commission_amount', 10, 2)->default(0)->comment('مبلغ عمولة الطرد المحسوب');
            
            // ==========================================
            // 🍯 4. كتلة العسل (Honey Block)
            // ==========================================
            $table->string('no_gallons_honey')->nullable()->comment('عدد جوالين العسل');
            $table->string('no_honey_jars')->nullable()->comment('عدد قروف العسل');
            $table->decimal('honey_fee', 10, 2)->default(0)->comment('أجرة شحن العسل');
            $table->decimal('honey_commission_rate', 5, 2)->default(0)->comment('نسبة عمولة العسل (مثال: 15%)');
            $table->decimal('honey_commission_amount', 10, 2)->default(0)->comment('مبلغ عمولة العسل المحسوب');

            // ==========================================
            // 💰 5. الإجماليات والمالية (Totals & Financials)
            // ==========================================
            $table->decimal('total_amount', 10, 2)->default(0)->comment('إجمالي الشحن (أجرة الطرد + أجرة العسل)');
            $table->decimal('total_commission', 10, 2)->default(0)->comment('إجمالي مبلغ العمولات للفاتورة');
            
            $table->enum('payment_method', ['prepaid', 'cod', 'customer_credit', 'partial_payment'])->default('prepaid');
            $table->decimal('partial_amount', 10, 2)->nullable()->default(0)->comment('المبلغ المدفوع جزئياً حالياً');
            $table->enum('customer_debt_status', ['pending', 'partially_paid', 'fully_paid', 'overdue'])->nullable()->default('pending');

            // ==========================================
            // 📋 6. الحالة والملاحظات (Status & Meta)
            // ==========================================
            $table->enum('status', ['pending','in_transit','received_at_branch','delivered','cancelled','returned'])->default('pending');
            $table->boolean('is_returned')->default(false);
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};