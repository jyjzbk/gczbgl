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
        Schema::create('school_teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('user_id')->comment('教师用户ID');
            $table->string('employee_number', 50)->nullable()->comment('工号');
            $table->string('subject', 50)->nullable()->comment('任教学科');
            $table->json('teaching_grades')->nullable()->comment('任教年级，如：[1,2,3]');
            $table->string('title', 50)->nullable()->comment('职称');
            $table->string('education', 50)->nullable()->comment('学历');
            $table->date('join_date')->nullable()->comment('入职日期');
            $table->tinyInteger('status')->default(1)->comment('状态：1在职 2离职 0停用');
            $table->timestamps();

            // 索引
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['school_id', 'user_id']);
            $table->index(['school_id', 'subject']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_teachers');
    }
};
