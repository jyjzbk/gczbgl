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
        // 为users表添加name字段（作为real_name的别名）
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 50)->nullable()->after('real_name')->comment('姓名（别名字段）');
        });

        // 为schools表添加organization相关字段
        Schema::table('schools', function (Blueprint $table) {
            $table->string('organization_type', 20)->nullable()->after('region_id')->comment('组织类型：province/city/county');
            $table->unsignedBigInteger('organization_id')->nullable()->after('organization_type')->comment('组织ID');

            // 添加索引
            $table->index(['organization_type', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['organization_type', 'organization_id']);
            $table->dropColumn(['organization_type', 'organization_id']);
        });
    }
};
