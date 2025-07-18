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
        Schema::create('equipment_standards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('标准名称');
            $table->string('code', 100)->unique()->comment('标准代码');
            $table->tinyInteger('authority_type')->comment('制定机构：1教育部 2教育厅');
            $table->tinyInteger('stage')->comment('学段：1小学 2初中 3高中');
            $table->string('subject_code', 50)->comment('学科代码');
            $table->string('subject_name', 100)->comment('学科名称');
            $table->text('description')->nullable()->comment('标准描述');
            $table->json('equipment_list')->comment('设备清单JSON');
            $table->string('version', 20)->default('1.0')->comment('版本号');
            $table->date('effective_date')->comment('生效日期');
            $table->date('expiry_date')->nullable()->comment('失效日期');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();
            
            $table->index(['authority_type', 'stage', 'subject_code']);
            $table->index(['status', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_standards');
    }
};
