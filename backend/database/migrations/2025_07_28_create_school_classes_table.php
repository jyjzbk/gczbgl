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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->string('name', 50)->comment('班级名称，如：一年级（1）');
            $table->string('code', 20)->comment('班级代码，如：G1C1');
            $table->tinyInteger('grade')->comment('年级：1-9');
            $table->tinyInteger('class_number')->comment('班级序号：1,2,3...');
            $table->integer('student_count')->default(0)->comment('学生人数');
            $table->unsignedBigInteger('head_teacher_id')->nullable()->comment('班主任ID');
            $table->string('classroom_location', 100)->nullable()->comment('教室位置');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常 0停用');
            $table->timestamps();

            // 索引
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('head_teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['school_id', 'grade']);
            $table->unique(['school_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
