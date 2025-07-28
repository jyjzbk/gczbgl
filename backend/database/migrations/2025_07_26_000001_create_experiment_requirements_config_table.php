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
        Schema::create('experiment_requirements_config', function (Blueprint $table) {
            $table->id();
            $table->enum('organization_type', ['province', 'city', 'county'])->comment('组织类型');
            $table->unsignedBigInteger('organization_id')->comment('组织ID');
            $table->enum('experiment_type', ['分组实验', '演示实验'])->comment('实验类型');
            $table->integer('min_images')->default(0)->comment('最少图片数量');
            $table->integer('max_images')->default(10)->comment('最多图片数量');
            $table->integer('min_videos')->default(0)->comment('最少视频数量');
            $table->integer('max_videos')->default(3)->comment('最多视频数量');
            $table->boolean('is_inherited')->default(true)->comment('是否继承上级配置');
            $table->unsignedBigInteger('created_by')->comment('创建人');
            $table->text('description')->nullable()->comment('配置说明');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->timestamps();

            // 索引
            $table->index(['organization_type', 'organization_id'], 'idx_organization');
            $table->index(['experiment_type'], 'idx_experiment_type');
            $table->index(['is_active'], 'idx_is_active');
            
            // 唯一约束：同一组织同一实验类型只能有一个配置
            $table->unique(['organization_type', 'organization_id', 'experiment_type'], 'unique_org_exp_type');

            // 外键
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_requirements_config');
    }
};
