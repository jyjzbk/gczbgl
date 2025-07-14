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
        Schema::create('experiment_catalogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->string('name', 200)->comment('实验名称');
            $table->string('code', 50)->comment('实验编号');
            $table->tinyInteger('type')->comment('实验类型：1必做 2选做 3演示 4分组');
            $table->tinyInteger('grade')->comment('年级');
            $table->tinyInteger('semester')->comment('学期：1上学期 2下学期');
            $table->string('chapter', 100)->nullable()->comment('章节');
            $table->integer('duration')->default(45)->comment('实验时长(分钟)');
            $table->integer('student_count')->default(1)->comment('建议学生数');
            $table->text('objective')->nullable()->comment('实验目的');
            $table->text('materials')->nullable()->comment('实验器材');
            $table->text('procedure')->nullable()->comment('实验步骤');
            $table->text('safety_notes')->nullable()->comment('安全注意事项');
            $table->integer('difficulty_level')->default(1)->comment('难度等级：1-5');
            $table->tinyInteger('is_standard')->default(1)->comment('是否标准实验：1是 0否');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();

            // 索引
            $table->index('subject_id');
            $table->index('code');
            $table->index('type');
            $table->index('grade');
            $table->index('semester');
            $table->index('is_standard');
            $table->index('status');

            // 外键
            $table->foreign('subject_id')->references('id')->on('subjects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalogs');
    }
};
