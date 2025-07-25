<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 修复现有实验记录的完成率
        // 将所有进行中状态且完成率为100%的记录重置为0%
        DB::table('experiment_records')
            ->where('status', 1) // STATUS_IN_PROGRESS
            ->where('completion_rate', 100.00)
            ->whereNull('end_time') // 没有结束时间
            ->whereNull('summary') // 没有总结
            ->update(['completion_rate' => 0.00]);

        // 修改表结构，将默认值改为0
        Schema::table('experiment_records', function (Blueprint $table) {
            $table->decimal('completion_rate', 5, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 恢复默认值为100
        Schema::table('experiment_records', function (Blueprint $table) {
            $table->decimal('completion_rate', 5, 2)->default(100.00)->change();
        });
    }
};
