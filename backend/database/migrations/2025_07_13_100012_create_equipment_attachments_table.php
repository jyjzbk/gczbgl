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
        Schema::create('equipment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->string('file_name')->comment('文件名');
            $table->string('original_name')->comment('原始文件名');
            $table->string('file_path')->comment('文件路径');
            $table->string('file_type', 50)->comment('文件类型：image,document,video,audio,other');
            $table->string('mime_type', 100)->comment('MIME类型');
            $table->bigInteger('file_size')->comment('文件大小(字节)');
            $table->string('file_extension', 10)->comment('文件扩展名');
            $table->string('attachment_type', 50)->comment('附件类型：photo,manual,certificate,other');
            $table->text('description')->nullable()->comment('文件描述');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_primary')->default(false)->comment('是否主图片');
            $table->foreignId('uploaded_by')->constrained('users')->comment('上传者');
            $table->timestamps();
            
            $table->index(['equipment_id', 'attachment_type']);
            $table->index(['file_type', 'is_primary']);
            $table->index('uploaded_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_attachments');
    }
};
