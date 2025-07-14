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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->comment('学校代码');
            $table->string('name', 200)->comment('学校名称');
            $table->tinyInteger('type')->comment('学校类型：1小学 2初中 3高中 4九年一贯制');
            $table->tinyInteger('level')->comment('管理级别：1省直 2市直 3区县直 4学区');
            $table->unsignedBigInteger('region_id')->comment('所属区域ID');
            $table->text('address')->nullable()->comment('学校地址');
            $table->string('contact_person', 50)->nullable()->comment('联系人');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->integer('student_count')->default(0)->comment('学生总数');
            $table->integer('class_count')->default(0)->comment('班级总数');
            $table->integer('teacher_count')->default(0)->comment('教师总数');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常 0停用');
            $table->timestamps();

            // 索引
            $table->unique('code');
            $table->index('region_id');
            $table->index('type');
            $table->index('level');

            // 外键
            $table->foreign('region_id')->references('id')->on('administrative_regions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
