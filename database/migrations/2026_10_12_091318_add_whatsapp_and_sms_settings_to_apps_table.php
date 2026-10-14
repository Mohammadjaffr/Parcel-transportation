<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            // ================= WhatsApp Settings =================
            if (!Schema::hasColumn('apps', 'whatsapp_base_url')) {
                $table->string('whatsapp_base_url')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('apps', 'whatsapp_instance_id')) {
                $table->string('whatsapp_instance_id')->nullable()->after('whatsapp_base_url');
            }
            if (!Schema::hasColumn('apps', 'whatsapp_instance_name')) {
                $table->string('whatsapp_instance_name')->nullable()->after('whatsapp_base_url');
            }
            if (!Schema::hasColumn('apps', 'whatsapp_api_key')) {
                $table->text('whatsapp_api_key')->nullable()->after('whatsapp_instance_name');
            }
            if (!Schema::hasColumn('apps', 'whatsapp_webhook_url')) {
                $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_api_key');
            }
            if (!Schema::hasColumn('apps', 'whatsapp_timeout')) {
                $table->unsignedInteger('whatsapp_timeout')->nullable()->default(30)->after('whatsapp_webhook_url');
            }

            // ================= SMS Settings (HttpSms Gateway) =================
            if (!Schema::hasColumn('apps', 'sms_base_url')) {
                $table->string('sms_base_url')->nullable()->after('whatsapp_timeout');
            }
            if (!Schema::hasColumn('apps', 'sms_api_key')) {
                $table->text('sms_api_key')->nullable()->after('sms_base_url');
            }
            if (!Schema::hasColumn('apps', 'sms_from_phone')) {
                $table->string('sms_from_phone')->nullable()->after('sms_api_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                // WhatsApp
                'whatsapp_base_url',
                'whatsapp_instance_id',
                'whatsapp_instance_name',
                'whatsapp_api_key',
                'whatsapp_webhook_url',
                'whatsapp_timeout',
                // SMS
                'sms_base_url',
                'sms_api_key',
                'sms_from_phone',
            ]);
        });
    }
};