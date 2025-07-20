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
        Schema::create('teaching_equipment_standards', function (Blueprint $table) {
            $table->id();
            
            // 基本信息
            $table->string('standard_name', 200)->comment('标准名称');
            $table->string('standard_code', 100)->unique()->comment('标准代码');
            $table->tinyInteger('authority_type')->comment('制定机构：1教育部 2教育厅 3地方');
            $table->tinyInteger('stage')->comment('学段：1小学 2初中 3高中');
            $table->string('subject_code', 50)->comment('学科代码');
            $table->string('subject_name', 100)->comment('学科名称');
            $table->text('description')->nullable()->comment('标准描述');
            
            // 分类层级
            $table->string('category_level_1', 100)->comment('一级分类');
            $table->string('category_level_2', 100)->nullable()->comment('二级分类');
            $table->string('category_level_3', 100)->nullable()->comment('三级分类');
            
            // 器材信息
            $table->string('item_code', 50)->comment('器材编码');
            $table->string('item_name', 200)->comment('器材名称');
            $table->text('specs_requirements')->nullable()->comment('规格、教学性能要求');
            $table->string('unit', 20)->comment('单位');
            $table->integer('quantity')->comment('配备数量');
            
            // 价格信息
            $table->decimal('unit_price', 10, 2)->nullable()->comment('单价（元）');
            $table->decimal('total_amount', 12, 2)->nullable()->comment('金额（元）');
            
            // 配备类型
            $table->boolean('is_basic')->default(false)->comment('是否基本配备');
            $table->boolean('is_optional')->default(false)->comment('是否选配');
            
            // 标准信息
            $table->string('standard_reference', 100)->nullable()->comment('执行标准代号');
            $table->text('remarks')->nullable()->comment('备注');
            $table->text('activity_suggestion')->nullable()->comment('实践活动建议');
            
            // 版本控制
            $table->string('version', 20)->default('1.0')->comment('版本号');
            $table->date('effective_date')->comment('生效日期');
            $table->date('expiry_date')->nullable()->comment('失效日期');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            
            $table->timestamps();
            
            // 索引
            $table->index(['authority_type', 'stage', 'subject_code'], 'idx_authority_stage_subject');
            $table->index(['category_level_1', 'category_level_2'], 'idx_category_levels');
            $table->index(['status', 'effective_date'], 'idx_status_effective');
            $table->index(['item_code', 'standard_code'], 'idx_item_standard_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_equipment_standards');
    }
};
