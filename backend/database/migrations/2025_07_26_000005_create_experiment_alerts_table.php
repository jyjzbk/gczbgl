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
        Schema::create('experiment_alerts', function (Blueprint $table) {
            $table->id();
            $table->enum('alert_type', ['overdue', 'completion_rate', 'quality_score'])->comment('预警类型');
            $table->enum('target_type', ['school', 'teacher', 'experiment', 'class'])->comment('预警对象类型');
            $table->unsignedBigInteger('target_id')->comment('预警对象ID');
            $table->string('target_name', 200)->comment('预警对象名称');
            $table->enum('alert_level', ['low', 'medium', 'high', 'critical'])->comment('预警级别');
            $table->string('alert_title', 200)->comment('预警标题');
            $table->text('alert_message')->comment('预警消息');
            $table->json('alert_data')->nullable()->comment('预警相关数据');
            $table->decimal('alert_value', 8, 2)->nullable()->comment('预警数值');
            $table->decimal('threshold_value', 8, 2)->nullable()->comment('阈值');
            $table->boolean('is_read')->default(false)->comment('是否已读');
            $table->boolean('is_resolved')->default(false)->comment('是否已解决');
            $table->text('resolve_note')->nullable()->comment('解决说明');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('解决人');
            $table->timestamp('resolved_at')->nullable()->comment('解决时间');
            $table->timestamp('alert_time')->useCurrent()->comment('预警时间');
            $table->timestamps();

            // 索引
            $table->index(['alert_type'], 'idx_alert_type');
            $table->index(['target_type', 'target_id'], 'idx_target');
            $table->index(['alert_level'], 'idx_alert_level');
            $table->index(['is_read'], 'idx_is_read');
            $table->index(['is_resolved'], 'idx_is_resolved');
            $table->index(['alert_time'], 'idx_alert_time');

            // 外键
            $table->foreign('resolved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_alerts');
    }
};
