<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. 实验器材需求配置表
        Schema::create('experiment_equipment_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id')->comment('实验目录ID');
            $table->unsignedBigInteger('equipment_id')->comment('设备ID');
            $table->integer('required_quantity')->default(1)->comment('标准需要数量');
            $table->integer('min_quantity')->default(1)->comment('最少需要数量');
            $table->boolean('is_required')->default(true)->comment('是否必需器材');
            $table->enum('calculation_type', ['fixed', 'per_group', 'per_student'])->default('fixed')->comment('计算方式：固定数量/按组/按学生');
            $table->integer('group_size')->nullable()->comment('分组大小（当calculation_type为per_group时使用）');
            $table->text('usage_note')->nullable()->comment('使用说明');
            $table->text('safety_note')->nullable()->comment('安全注意事项');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人');
            $table->timestamps();
            
            // 修复外键引用 - 使用正确的表名
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade'); // 修改为 equipments
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['catalog_id', 'equipment_id'], 'unique_catalog_equipment');
            $table->index(['catalog_id', 'is_active', 'sort_order']);
        });

        // 2. 器材配置模板表
        Schema::create('equipment_requirement_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('模板名称');
            $table->text('description')->nullable()->comment('模板描述');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('学科ID');
            $table->enum('experiment_type', ['演示实验', '分组实验', '探究实验', '综合实验'])->comment('实验类型');
            $table->json('equipment_list')->comment('器材清单JSON');
            $table->boolean('is_public')->default(false)->comment('是否公开模板');
            $table->unsignedBigInteger('created_by')->comment('创建人');
            $table->unsignedBigInteger('school_id')->nullable()->comment('学校ID（私有模板）');
            $table->integer('use_count')->default(0)->comment('使用次数');
            $table->timestamps();
            
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['subject_id', 'experiment_type', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_requirement_templates');
        Schema::dropIfExists('experiment_equipment_requirements');
    }
};
