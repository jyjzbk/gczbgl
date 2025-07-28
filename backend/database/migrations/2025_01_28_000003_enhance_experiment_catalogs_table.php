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
        Schema::table('experiment_catalogs', function (Blueprint $table) {
            // 基准目录相关字段
            $table->boolean('is_baseline_catalog')->default(false)->after('status')->comment('是否为基准目录（被学校选择的目录）');
            $table->tinyInteger('baseline_priority')->default(0)->after('is_baseline_catalog')->comment('基准优先级：0普通 1推荐 2强制');
            $table->json('applicable_school_types')->nullable()->after('baseline_priority')->comment('适用学校类型配置');
            
            // 目录使用统计
            $table->integer('usage_count')->default(0)->after('applicable_school_types')->comment('被学校选择次数');
            $table->timestamp('last_used_at')->nullable()->after('usage_count')->comment('最后被使用时间');
            
            // 索引
            $table->index(['is_baseline_catalog', 'baseline_priority'], 'idx_baseline_catalog');
            $table->index(['management_level', 'is_baseline_catalog'], 'idx_level_baseline');
            $table->index(['usage_count'], 'idx_usage_count');
            $table->index(['last_used_at'], 'idx_last_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experiment_catalogs', function (Blueprint $table) {
            // 删除索引
            $table->dropIndex('idx_baseline_catalog');
            $table->dropIndex('idx_level_baseline');
            $table->dropIndex('idx_usage_count');
            $table->dropIndex('idx_last_used');
            
            // 删除字段
            $table->dropColumn([
                'is_baseline_catalog',
                'baseline_priority',
                'applicable_school_types',
                'usage_count',
                'last_used_at'
            ]);
        });
    }
};
