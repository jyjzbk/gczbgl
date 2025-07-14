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
        Schema::create('statistics_summary', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type', 20)->comment('统计范围：province/city/county/district/school');
            $table->unsignedBigInteger('scope_id')->comment('范围ID');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('学科ID');
            $table->date('stat_date')->comment('统计日期');
            $table->string('stat_type', 50)->comment('统计类型');
            $table->integer('total_experiments')->default(0)->comment('总实验数');
            $table->integer('completed_experiments')->default(0)->comment('已完成实验数');
            $table->decimal('completion_rate', 5, 2)->default(0)->comment('完成率');
            $table->integer('group_experiments')->default(0)->comment('分组实验数');
            $table->integer('demo_experiments')->default(0)->comment('演示实验数');
            $table->decimal('total_value', 15, 2)->default(0)->comment('总价值');
            $table->timestamps();

            // 索引
            $table->unique(['scope_type', 'scope_id', 'subject_id', 'stat_date', 'stat_type'], 'uk_scope_subject_date_type');
            $table->index('stat_date');
            $table->index('stat_type');
            $table->index('subject_id');

            // 外键
            $table->foreign('subject_id')->references('id')->on('subjects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics_summary');
    }
};
