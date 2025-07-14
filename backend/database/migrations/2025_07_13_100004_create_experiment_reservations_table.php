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
        Schema::create('experiment_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('catalog_id')->comment('实验目录ID');
            $table->unsignedBigInteger('laboratory_id')->comment('实验室ID');
            $table->unsignedBigInteger('teacher_id')->comment('授课教师ID');
            $table->string('class_name', 100)->comment('班级名称');
            $table->integer('student_count')->comment('学生人数');
            $table->date('reservation_date')->comment('预约日期');
            $table->time('start_time')->comment('开始时间');
            $table->time('end_time')->comment('结束时间');
            $table->tinyInteger('status')->default(1)->comment('状态：1待审核 2已通过 3已拒绝 4已完成 5已取消');
            $table->text('remark')->nullable()->comment('备注');
            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核人ID');
            $table->timestamp('reviewed_at')->nullable()->comment('审核时间');
            $table->text('review_remark')->nullable()->comment('审核备注');
            $table->timestamps();

            // 索引
            $table->index('school_id');
            $table->index('catalog_id');
            $table->index('laboratory_id');
            $table->index('teacher_id');
            $table->index('reservation_date');
            $table->index('status');
            $table->index('reviewer_id');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_reservations');
    }
};
