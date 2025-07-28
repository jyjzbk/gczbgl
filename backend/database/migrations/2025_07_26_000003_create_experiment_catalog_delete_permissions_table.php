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
        Schema::create('experiment_catalog_delete_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('organization_type', ['province', 'city', 'county'])->comment('组织类型');
            $table->unsignedBigInteger('organization_id')->comment('组织ID');
            $table->string('organization_name', 100)->comment('组织名称');
            $table->boolean('allow_school_delete')->default(true)->comment('是否允许学校删除实验');
            $table->boolean('require_delete_reason')->default(true)->comment('是否要求填写删除理由');
            $table->integer('max_delete_percentage')->default(20)->comment('最大删除比例(%)');
            $table->text('delete_rules')->nullable()->comment('删除规则说明');
            $table->unsignedBigInteger('created_by')->comment('创建人');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->timestamps();

            // 索引
            $table->index(['organization_type', 'organization_id'], 'idx_organization');
            $table->index(['is_active'], 'idx_is_active');
            
            // 唯一约束：同一组织只能有一个权限配置
            $table->unique(['organization_type', 'organization_id'], 'unique_org_permission');

            // 外键
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalog_delete_permissions');
    }
};
