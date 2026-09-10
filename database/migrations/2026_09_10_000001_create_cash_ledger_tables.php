<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول تصنيفات الصندوق (مقبوضات / مصروفات)
        Schema::create('cash_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['income', 'expense']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['app_id', 'name', 'type'], 'unique_app_cat_name_type');
            $table->index(['app_id', 'type', 'is_active'], 'idx_app_cat_active');
        });

        // 2. جدول حركات الصندوق المستمر (بدون إقفال)
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('cash_category_id')->constrained('cash_categories')->restrictOnDelete();

            $table->enum('type', ['income', 'expense']);
            $table->enum('payment_method', ['cash', 'bank_transfer'])->default('cash');
            $table->string('receipt_number', 50);
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');

            // ربط متعدد الأشكال بمصدر العملية (شحنة، دفعة عميل، بيان استلام، إلخ)
            $table->nullableMorphs('source');
            $table->string('reference_number', 100)->nullable()->comment('رقم الإيداع البنكي أو رقم السند اليدوي');
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // فهارس الفلترة السريعة وتعدد المستأجرين
            $table->index(['app_id', 'branch_id', 'transaction_date', 'type'], 'idx_app_branch_date_type');
            $table->index(['app_id', 'transaction_date'], 'idx_app_date');
            $table->unique(['app_id', 'receipt_number'], 'unique_app_receipt_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('cash_categories');
    }
};