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
        Schema::table('apps', function (Blueprint $table) {
            $table->string('whatsapp_base_url', 500)->nullable()->after('sms_from_phone');
            $table->string('whatsapp_instance_id', 150)->nullable()->after('whatsapp_base_url');
            $table->text('whatsapp_instance_token')->nullable()->after('whatsapp_instance_id');
            $table->string('whatsapp_webhook_url', 500)->nullable()->after('whatsapp_instance_token');
            $table->unsignedSmallInteger('whatsapp_timeout')->default(30)->after('whatsapp_webhook_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_base_url',
                'whatsapp_instance_id',
                'whatsapp_instance_token',
                'whatsapp_webhook_url',
                'whatsapp_timeout',
            ]);
        });
    }
};
