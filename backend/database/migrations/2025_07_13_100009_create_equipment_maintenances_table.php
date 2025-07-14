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
        Schema::create('equipment_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipment_id')->comment('设备ID');
            $table->unsignedBigInteger('reporter_id')->comment('报修人ID');
            $table->text('fault_description')->comment('故障描述');
            $table->string('fault_type', 50)->nullable()->comment('故障类型');
            $table->tinyInteger('urgency_level')->default(2)->comment('紧急程度：1低 2中 3高');
            $table->json('photos')->nullable()->comment('故障照片');
            $table->date('report_date')->comment('报修日期');
            $table->unsignedBigInteger('maintainer_id')->nullable()->comment('维修人ID');
            $table->date('start_date')->nullable()->comment('开始维修日期');
            $table->date('complete_date')->nullable()->comment('完成维修日期');
            $table->decimal('cost', 10, 2)->nullable()->comment('维修费用');
            $table->text('solution')->nullable()->comment('解决方案');
            $table->text('parts_replaced')->nullable()->comment('更换部件');
            $table->tinyInteger('status')->default(1)->comment('状态：1待维修 2维修中 3已完成 4无法修复');
            $table->tinyInteger('quality_rating')->nullable()->comment('维修质量评分(1-5)');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            // 索引
            $table->index('equipment_id');
            $table->index('reporter_id');
            $table->index('maintainer_id');
            $table->index('report_date');
            $table->index('status');

            // 外键
            $table->foreign('equipment_id')->references('id')->on('equipments');
            $table->foreign('reporter_id')->references('id')->on('users');
            $table->foreign('maintainer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenances');
    }
};
