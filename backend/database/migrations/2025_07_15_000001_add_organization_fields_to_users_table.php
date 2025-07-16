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
        Schema::table('users', function (Blueprint $table) {
            // 组织归属字段
            $table->unsignedBigInteger('organization_id')->nullable()->after('school_name')->comment('主要组织ID（区域或学校）');
            $table->string('organization_type', 20)->nullable()->after('organization_id')->comment('组织类型：region/school');
            $table->tinyInteger('organization_level')->nullable()->after('organization_type')->comment('组织级别：1省 2市 3区县 4学区 5学校');
            
            // 添加索引
            $table->index('organization_id');
            $table->index('organization_type');
            $table->index('organization_level');
            $table->index(['organization_type', 'organization_id'], 'idx_organization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['organization_type']);
            $table->dropIndex(['organization_level']);
            $table->dropIndex('idx_organization');
            $table->dropColumn([
                'organization_id',
                'organization_type',
                'organization_level'
            ]);
        });
    }
};
