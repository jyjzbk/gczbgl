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
        Schema::table('equipment_standards', function (Blueprint $table) {
            // 添加价格相关字段
            $table->decimal('estimated_unit_price', 10, 2)->nullable()->after('equipment_list')->comment('预估单价（元）');
            $table->decimal('estimated_total_amount', 12, 2)->nullable()->after('estimated_unit_price')->comment('预估总金额（元）');
            
            // 添加分类层级字段
            $table->string('category_level_1', 100)->nullable()->after('subject_name')->comment('一级分类');
            $table->string('category_level_2', 100)->nullable()->after('category_level_1')->comment('二级分类');
            $table->string('category_level_3', 100)->nullable()->after('category_level_2')->comment('三级分类');
            
            // 添加配备类型字段
            $table->boolean('is_basic_standard')->default(true)->after('estimated_total_amount')->comment('是否基本配备标准');
            $table->boolean('is_optional_standard')->default(false)->after('is_basic_standard')->comment('是否选配标准');
            
            // 添加标准参考字段
            $table->string('standard_reference', 100)->nullable()->after('is_optional_standard')->comment('执行标准代号');
            $table->text('implementation_notes')->nullable()->after('standard_reference')->comment('实施说明');
            
            // 添加索引
            $table->index(['category_level_1', 'category_level_2'], 'idx_eq_std_category_levels');
            $table->index(['is_basic_standard', 'is_optional_standard'], 'idx_eq_std_basic_optional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_standards', function (Blueprint $table) {
            $table->dropIndex('idx_eq_std_category_levels');
            $table->dropIndex('idx_eq_std_basic_optional');
            
            $table->dropColumn([
                'estimated_unit_price',
                'estimated_total_amount',
                'category_level_1',
                'category_level_2',
                'category_level_3',
                'is_basic_standard',
                'is_optional_standard',
                'standard_reference',
                'implementation_notes'
            ]);
        });
    }
};
