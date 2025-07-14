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
        Schema::create('equipment_qrcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->string('qr_code_url')->comment('二维码图片URL');
            $table->string('qr_code_content', 1000)->comment('二维码内容');
            $table->string('qr_type', 50)->default('basic')->comment('二维码类型：basic,detailed,url');
            $table->json('qr_options')->nullable()->comment('二维码生成选项');
            $table->integer('size')->default(200)->comment('二维码尺寸');
            $table->string('format', 10)->default('png')->comment('图片格式');
            $table->integer('download_count')->default(0)->comment('下载次数');
            $table->integer('scan_count')->default(0)->comment('扫描次数');
            $table->timestamp('last_scanned_at')->nullable()->comment('最后扫描时间');
            $table->boolean('is_active')->default(true)->comment('是否有效');
            $table->timestamps();
            
            $table->index(['equipment_id', 'is_active']);
            $table->index('qr_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_qrcodes');
    }
};
