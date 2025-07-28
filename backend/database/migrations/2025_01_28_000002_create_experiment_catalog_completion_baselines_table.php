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
        Schema::create('experiment_catalog_completion_baselines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('config_id')->comment('配置ID');
            
            // 基准信息
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->tinyInteger('grade')->comment('年级');
            $table->tinyInteger('semester')->comment('学期：1上学期 2下学期');
            
            // 目录统计
            $table->integer('total_experiments')->default(0)->comment('总实验数量');
            $table->integer('required_experiments')->default(0)->comment('必做实验数量');
            $table->integer('optional_experiments')->default(0)->comment('选做实验数量');
            $table->integer('demo_experiments')->default(0)->comment('演示实验数量');
            $table->integer('group_experiments')->default(0)->comment('分组实验数量');
            
            // 完成情况
            $table->integer('completed_experiments')->default(0)->comment('已完成实验数量');
            $table->decimal('completion_rate', 5, 2)->default(0.00)->comment('完成率');
            
            // 更新记录
            $table->timestamp('last_calculated_at')->nullable()->comment('最后计算时间');
            $table->unsignedBigInteger('calculated_by')->nullable()->comment('计算操作人');
            
            $table->timestamps();
            
            // 索引
            $table->index(['school_id', 'config_id'], 'idx_school_config');
            $table->index(['subject_id', 'grade', 'semester'], 'idx_subject_grade_semester');
            $table->index(['completion_rate'], 'idx_completion_rate');
            $table->index(['last_calculated_at'], 'idx_last_calculated');
            $table->index(['school_id', 'subject_id'], 'idx_school_subject');
            $table->index(['school_id', 'grade'], 'idx_school_grade');
            
            // 唯一约束
            $table->unique(['school_id', 'config_id', 'subject_id', 'grade', 'semester'], 'unique_baseline');
            
            // 外键
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('config_id')->references('id')->on('school_experiment_catalog_configs')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('calculated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalog_completion_baselines');
    }
};
