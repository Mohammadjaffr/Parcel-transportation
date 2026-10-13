<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // ================= WhatsApp Settings (per branch) =================
            $table->string('whatsapp_base_url')->nullable()->after('is_main');
            $table->string('whatsapp_instance_id')->nullable()->after('whatsapp_base_url');
            $table->string('whatsapp_instance_name')->nullable()->after('whatsapp_instance_id');
            $table->text('whatsapp_api_key')->nullable()->after('whatsapp_instance_name');
            $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_api_key');
            $table->unsignedInteger('whatsapp_timeout')->nullable()->default(30)->after('whatsapp_webhook_url');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_base_url',
                'whatsapp_instance_id',
                'whatsapp_instance_name',
                'whatsapp_api_key',
                'whatsapp_webhook_url',
                'whatsapp_timeout',
            ]);
        });
    }
};
