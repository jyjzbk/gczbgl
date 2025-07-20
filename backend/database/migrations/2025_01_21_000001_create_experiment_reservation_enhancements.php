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
        // 1. 预约模板表
        Schema::create('experiment_reservation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('模板名称');
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->tinyInteger('grade')->comment('年级');
            $table->tinyInteger('semester')->comment('学期：1上学期 2下学期');
            $table->json('template_data')->comment('模板数据：包含实验安排、时间等');
            $table->unsignedBigInteger('created_by')->comment('创建人');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->text('description')->nullable()->comment('模板描述');
            $table->integer('use_count')->default(0)->comment('使用次数');
            $table->timestamps();

            // 索引
            $table->index(['school_id', 'subject_id']);
            $table->index(['grade', 'semester']);
            $table->index('created_by');
            $table->index('is_active');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. 预约冲突日志表
        Schema::create('reservation_conflict_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->comment('预约ID');
            $table->enum('conflict_type', ['time', 'equipment', 'capacity', 'teacher'])->comment('冲突类型');
            $table->json('conflict_details')->comment('冲突详情');
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium')->comment('严重程度');
            $table->boolean('is_resolved')->default(false)->comment('是否已解决');
            $table->timestamp('resolved_at')->nullable()->comment('解决时间');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('解决人');
            $table->text('resolution_note')->nullable()->comment('解决说明');
            $table->timestamps();

            // 索引
            $table->index('reservation_id');
            $table->index('conflict_type');
            $table->index('is_resolved');
            $table->index('severity');

            // 外键
            $table->foreign('reservation_id')->references('id')->on('experiment_reservations')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. 实验作品表
        Schema::create('experiment_works', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->comment('实验记录ID');
            $table->unsignedBigInteger('student_id')->nullable()->comment('学生ID（如果是学生作品）');
            $table->string('title', 200)->comment('作品标题');
            $table->text('description')->nullable()->comment('作品描述');
            $table->enum('type', ['photo', 'video', 'document', 'other'])->comment('作品类型');
            $table->string('file_path')->comment('文件路径');
            $table->string('file_name')->comment('原始文件名');
            $table->string('file_size')->comment('文件大小');
            $table->string('mime_type')->comment('文件类型');
            $table->json('metadata')->nullable()->comment('文件元数据');
            $table->tinyInteger('quality_score')->nullable()->comment('质量评分(1-5)');
            $table->text('teacher_comment')->nullable()->comment('教师评语');
            $table->boolean('is_featured')->default(false)->comment('是否精选作品');
            $table->boolean('is_public')->default(false)->comment('是否公开展示');
            $table->unsignedBigInteger('uploaded_by')->comment('上传人');
            $table->timestamps();

            // 索引
            $table->index('record_id');
            $table->index('student_id');
            $table->index('type');
            $table->index('is_featured');
            $table->index('is_public');
            $table->index('uploaded_by');

            // 外键
            $table->foreign('record_id')->references('id')->on('experiment_records')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. 预约批次表（用于批量预约管理）
        Schema::create('reservation_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name', 100)->comment('批次名称');
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->tinyInteger('grade')->comment('年级');
            $table->tinyInteger('semester')->comment('学期');
            $table->date('start_date')->comment('开始日期');
            $table->date('end_date')->comment('结束日期');
            $table->unsignedBigInteger('created_by')->comment('创建人（备课组长）');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'completed'])->default('draft')->comment('状态');
            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核人');
            $table->timestamp('reviewed_at')->nullable()->comment('审核时间');
            $table->text('review_remark')->nullable()->comment('审核备注');
            $table->integer('total_reservations')->default(0)->comment('总预约数');
            $table->integer('completed_reservations')->default(0)->comment('已完成预约数');
            $table->timestamps();

            // 索引
            $table->index(['school_id', 'subject_id']);
            $table->index(['grade', 'semester']);
            $table->index('created_by');
            $table->index('status');
            $table->index('reviewer_id');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
        });

        // 5. 为现有表添加字段
        Schema::table('experiment_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('reviewer_id')->comment('批次ID');
            $table->json('equipment_requirements')->nullable()->after('review_remark')->comment('器材需求清单');
            $table->boolean('auto_borrow_equipment')->default(true)->after('equipment_requirements')->comment('是否自动借用器材');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('auto_borrow_equipment')->comment('优先级');
            $table->text('preparation_notes')->nullable()->after('priority')->comment('实验准备说明');
            
            // 添加外键
            $table->foreign('batch_id')->references('id')->on('reservation_batches')->onDelete('set null');
            
            // 添加索引
            $table->index('batch_id');
            $table->index('priority');
        });

        // 6. 为实验记录表添加字段
        Schema::table('experiment_records', function (Blueprint $table) {
            $table->integer('work_count')->default(0)->after('suggestions')->comment('作品数量');
            $table->json('attendance_data')->nullable()->after('work_count')->comment('考勤数据');
            $table->decimal('equipment_usage_rate', 5, 2)->default(100.00)->after('attendance_data')->comment('器材使用率(%)');
            $table->text('safety_incidents')->nullable()->after('equipment_usage_rate')->comment('安全事件记录');
            
            // 添加索引
            $table->index('work_count');
        });

        // 7. 为设备借用表添加字段
        Schema::table('equipment_borrows', function (Blueprint $table) {
            $table->integer('actual_quantity')->nullable()->after('quantity')->comment('实际借用数量');
            $table->json('condition_before')->nullable()->after('approval_remark')->comment('借用前状态');
            $table->json('condition_after')->nullable()->after('condition_before')->comment('归还后状态');
            $table->boolean('has_damage')->default(false)->after('condition_after')->comment('是否有损坏');
            $table->text('damage_description')->nullable()->after('has_damage')->comment('损坏描述');
            $table->decimal('damage_cost', 10, 2)->nullable()->after('damage_description')->comment('损坏赔偿费用');
            
            // 添加索引
            $table->index('has_damage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 删除添加的字段
        Schema::table('equipment_borrows', function (Blueprint $table) {
            $table->dropColumn([
                'actual_quantity', 'condition_before', 'condition_after', 
                'has_damage', 'damage_description', 'damage_cost'
            ]);
        });

        Schema::table('experiment_records', function (Blueprint $table) {
            $table->dropColumn([
                'work_count', 'attendance_data', 'equipment_usage_rate', 'safety_incidents'
            ]);
        });

        Schema::table('experiment_reservations', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn([
                'batch_id', 'equipment_requirements', 'auto_borrow_equipment', 
                'priority', 'preparation_notes'
            ]);
        });

        // 删除新建的表
        Schema::dropIfExists('reservation_batches');
        Schema::dropIfExists('experiment_works');
        Schema::dropIfExists('reservation_conflict_logs');
        Schema::dropIfExists('experiment_reservation_templates');
    }
};
