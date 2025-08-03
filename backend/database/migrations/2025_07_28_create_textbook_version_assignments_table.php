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
        Schema::create('textbook_version_assignments', function (Blueprint $table) {
            $table->id();
            
            // 指定组织信息
            $table->tinyInteger('assigner_level')->comment('指定者级别（1省2市3区县4学区5学校）');
            $table->unsignedBigInteger('assigner_org_id')->comment('指定者组织ID');
            $table->string('assigner_org_type', 20)->comment('指定者组织类型');
            $table->unsignedBigInteger('assigner_user_id')->comment('指定操作用户ID');
            
            // 目标学校信息
            $table->unsignedBigInteger('school_id')->comment('目标学校ID');
            
            // 教材版本指定信息
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->string('grade_level', 20)->comment('年级');
            $table->unsignedBigInteger('textbook_version_id')->comment('指定的教材版本ID');
            
            // 指定状态和时间
            $table->tinyInteger('status')->default(1)->comment('状态：1生效 0失效');
            $table->text('assignment_reason')->nullable()->comment('指定理由');
            $table->timestamp('effective_date')->comment('生效日期');
            $table->timestamp('expiry_date')->nullable()->comment('失效日期');
            
            // 变更记录
            $table->unsignedBigInteger('replaced_assignment_id')->nullable()->comment('被替换的指定记录ID');
            $table->text('change_reason')->nullable()->comment('变更理由');
            
            $table->timestamps();
            
            // 索引
            $table->index(['school_id', 'subject_id', 'grade_level', 'status'], 'idx_school_subject_grade_status');
            $table->index(['assigner_level', 'assigner_org_id'], 'idx_assigner');
            $table->index(['textbook_version_id'], 'idx_textbook_version');
            $table->index(['effective_date', 'expiry_date'], 'idx_date_range');
            $table->index(['status'], 'idx_status');
            
            // 外键约束
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('textbook_version_id')->references('id')->on('textbook_versions');
            $table->foreign('assigner_user_id')->references('id')->on('users');
            $table->foreign('replaced_assignment_id')->references('id')->on('textbook_version_assignments');
            
            // 唯一约束：同一学校、学科、年级在同一时间只能有一个生效的指定
            $table->unique(['school_id', 'subject_id', 'grade_level', 'status'], 'uk_school_subject_grade_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbook_version_assignments');
    }
};
