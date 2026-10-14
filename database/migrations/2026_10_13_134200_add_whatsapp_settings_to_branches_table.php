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
            if (!Schema::hasColumn('branches', 'whatsapp_base_url')) {
                $table->string('whatsapp_base_url')->nullable()->after('is_main');
            }
            if (!Schema::hasColumn('branches', 'whatsapp_instance_id')) {
                $table->string('whatsapp_instance_id')->nullable()->after('whatsapp_base_url');
            }
            if (!Schema::hasColumn('branches', 'whatsapp_instance_name')) {
                $table->string('whatsapp_instance_name')->nullable()->after('whatsapp_instance_id');
            }
            if (!Schema::hasColumn('branches', 'whatsapp_api_key')) {
                $table->text('whatsapp_api_key')->nullable()->after('whatsapp_instance_name');
            }
            if (!Schema::hasColumn('branches', 'whatsapp_webhook_url')) {
                $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_api_key');
            }
            if (!Schema::hasColumn('branches', 'whatsapp_timeout')) {
                $table->unsignedInteger('whatsapp_timeout')->nullable()->default(30)->after('whatsapp_webhook_url');
            }
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
