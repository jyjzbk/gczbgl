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
        Schema::create('experiment_alert_config', function (Blueprint $table) {
            $table->id();
            $table->enum('organization_type', ['province', 'city', 'county'])->comment('组织类型');
            $table->unsignedBigInteger('organization_id')->comment('组织ID');
            $table->string('organization_name', 100)->comment('组织名称');
            $table->enum('alert_type', ['overdue', 'completion_rate', 'quality_score'])->comment('预警类型');
            $table->decimal('threshold_value', 5, 2)->comment('预警阈值');
            $table->integer('alert_days')->default(7)->comment('预警提前天数');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->text('alert_rules')->nullable()->comment('预警规则说明');
            $table->json('notification_settings')->nullable()->comment('通知设置');
            $table->unsignedBigInteger('created_by')->comment('创建人');
            $table->timestamps();

            // 索引
            $table->index(['organization_type', 'organization_id'], 'idx_organization');
            $table->index(['alert_type'], 'idx_alert_type');
            $table->index(['is_active'], 'idx_is_active');
            
            // 唯一约束：同一组织同一预警类型只能有一个配置
            $table->unique(['organization_type', 'organization_id', 'alert_type'], 'unique_org_alert_type');

            // 外键
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_alert_config');
    }
};
