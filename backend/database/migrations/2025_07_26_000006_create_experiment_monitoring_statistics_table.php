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
        Schema::create('experiment_monitoring_statistics', function (Blueprint $table) {
            $table->id();
            $table->enum('target_type', ['school', 'teacher', 'subject', 'grade'])->comment('统计对象类型');
            $table->unsignedBigInteger('target_id')->comment('统计对象ID');
            $table->string('target_name', 200)->comment('统计对象名称');
            $table->string('semester', 20)->comment('学期');
            $table->date('statistics_date')->comment('统计日期');
            
            // 实验统计数据
            $table->integer('total_planned_experiments')->default(0)->comment('计划实验总数');
            $table->integer('completed_experiments')->default(0)->comment('已完成实验数');
            $table->integer('overdue_experiments')->default(0)->comment('超期实验数');
            $table->integer('pending_experiments')->default(0)->comment('待开实验数');
            
            // 完成率统计
            $table->decimal('completion_rate', 5, 2)->default(0)->comment('完成率(%)');
            $table->decimal('overdue_rate', 5, 2)->default(0)->comment('超期率(%)');
            $table->decimal('quality_score', 5, 2)->default(0)->comment('质量评分');
            
            // 时间统计
            $table->decimal('avg_completion_days', 8, 2)->default(0)->comment('平均完成天数');
            $table->integer('max_overdue_days')->default(0)->comment('最大超期天数');
            
            // 分类统计
            $table->json('subject_statistics')->nullable()->comment('学科统计');
            $table->json('grade_statistics')->nullable()->comment('年级统计');
            $table->json('monthly_statistics')->nullable()->comment('月度统计');
            
            $table->timestamp('calculated_at')->useCurrent()->comment('计算时间');
            $table->timestamps();

            // 索引
            $table->index(['target_type', 'target_id'], 'idx_target');
            $table->index(['semester'], 'idx_semester');
            $table->index(['statistics_date'], 'idx_statistics_date');
            $table->index(['completion_rate'], 'idx_completion_rate');
            $table->index(['overdue_rate'], 'idx_overdue_rate');
            
            // 唯一约束：同一对象同一学期同一日期只能有一条统计记录
            $table->unique(['target_type', 'target_id', 'semester', 'statistics_date'], 'unique_target_semester_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_monitoring_statistics');
    }
};
