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
        Schema::create('school_experiment_catalog_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->enum('config_type', ['selection', 'assignment'])->comment('配置类型：selection=学校选择，assignment=上级指定');
            
            // 选择/指定的目录来源
            $table->tinyInteger('source_level')->comment('目录来源级别：1省 2市 3区县');
            $table->unsignedBigInteger('source_org_id')->comment('目录来源组织ID');
            $table->string('source_org_name', 100)->comment('目录来源组织名称');
            
            // 权限控制
            $table->boolean('can_modify_selection')->default(false)->comment('是否允许修改选择');
            $table->boolean('can_delete_experiments')->default(false)->comment('是否允许删除实验项目');
            
            // 操作记录
            $table->unsignedBigInteger('configured_by')->comment('配置操作人ID');
            $table->tinyInteger('configured_by_level')->comment('配置操作人级别');
            $table->timestamp('configured_at')->useCurrent()->comment('配置时间');
            $table->text('config_reason')->nullable()->comment('配置理由');
            
            // 状态管理
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->date('effective_date')->nullable()->comment('生效日期');
            $table->date('expiry_date')->nullable()->comment('失效日期');
            
            $table->timestamps();
            
            // 索引
            $table->index(['school_id'], 'idx_school_id');
            $table->index(['source_level', 'source_org_id'], 'idx_source_org');
            $table->index(['configured_by', 'configured_by_level'], 'idx_configured_by');
            $table->index(['status', 'effective_date'], 'idx_status_effective');
            $table->index(['config_type'], 'idx_config_type');
            
            // 唯一约束：每个学校只能有一个有效配置
            $table->unique(['school_id', 'status'], 'unique_active_school_config');
            
            // 外键
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('configured_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_experiment_catalog_configs');
    }
};
