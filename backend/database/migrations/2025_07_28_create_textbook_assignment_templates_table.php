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
        Schema::create('textbook_assignment_templates', function (Blueprint $table) {
            $table->id();
            
            // 模板基本信息
            $table->string('name', 100)->comment('模板名称');
            $table->text('description')->nullable()->comment('模板描述');
            
            // 创建者信息
            $table->tinyInteger('creator_level')->comment('创建者级别');
            $table->unsignedBigInteger('creator_org_id')->comment('创建者组织ID');
            $table->string('creator_org_type', 20)->comment('创建者组织类型');
            $table->unsignedBigInteger('creator_user_id')->comment('创建用户ID');
            
            // 模板配置
            $table->json('assignment_config')->comment('指定配置JSON：{subject_id: textbook_version_id}');
            $table->json('applicable_grades')->comment('适用年级JSON数组');
            $table->json('applicable_school_types')->nullable()->comment('适用学校类型JSON数组');
            
            // 使用统计
            $table->integer('usage_count')->default(0)->comment('使用次数');
            $table->timestamp('last_used_at')->nullable()->comment('最后使用时间');
            
            // 状态
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->tinyInteger('is_default')->default(0)->comment('是否默认模板：1是 0否');
            
            $table->timestamps();
            
            // 索引
            $table->index(['creator_level', 'creator_org_id'], 'idx_creator');
            $table->index(['status', 'is_default'], 'idx_status_default');
            $table->index(['usage_count'], 'idx_usage');
            
            // 外键
            $table->foreign('creator_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbook_assignment_templates');
    }
};
