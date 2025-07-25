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
        Schema::create('experiment_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->comment('预约ID');
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('catalog_id')->comment('实验目录ID');
            $table->unsignedBigInteger('laboratory_id')->comment('实验室ID');
            $table->unsignedBigInteger('teacher_id')->comment('授课教师ID');
            $table->string('class_name', 100)->comment('班级名称');
            $table->integer('student_count')->comment('实际学生人数');
            $table->datetime('start_time')->nullable()->comment('实际开始时间');
            $table->datetime('end_time')->nullable()->comment('实际结束时间');
            $table->decimal('completion_rate', 5, 2)->default(0.00)->comment('完成率(%)');
            $table->tinyInteger('quality_score')->nullable()->comment('质量评分(1-5)');
            $table->json('photos')->nullable()->comment('实验照片');
            $table->json('videos')->nullable()->comment('实验视频');
            $table->text('summary')->nullable()->comment('实验总结');
            $table->text('problems')->nullable()->comment('存在问题');
            $table->text('suggestions')->nullable()->comment('改进建议');
            $table->tinyInteger('status')->default(1)->comment('状态：1进行中 2已完成 3异常结束');
            $table->timestamps();

            // 索引
            $table->index('reservation_id');
            $table->index('school_id');
            $table->index('catalog_id');
            $table->index('laboratory_id');
            $table->index('teacher_id');
            $table->index('start_time');
            $table->index('status');

            // 外键
            $table->foreign('reservation_id')->references('id')->on('experiment_reservations');
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('teacher_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_records');
    }
};
