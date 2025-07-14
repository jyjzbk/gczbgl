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
        Schema::create('equipment_borrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipment_id')->comment('设备ID');
            $table->unsignedBigInteger('reservation_id')->nullable()->comment('实验预约ID');
            $table->unsignedBigInteger('borrower_id')->comment('借用人ID');
            $table->integer('quantity')->default(1)->comment('借用数量');
            $table->date('borrow_date')->comment('借用日期');
            $table->date('expected_return_date')->comment('预期归还日期');
            $table->date('actual_return_date')->nullable()->comment('实际归还日期');
            $table->text('purpose')->nullable()->comment('借用目的');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('status')->default(1)->comment('状态：1借用中 2已归还 3逾期 4损坏');
            $table->unsignedBigInteger('approver_id')->nullable()->comment('审批人ID');
            $table->timestamp('approved_at')->nullable()->comment('审批时间');
            $table->text('approval_remark')->nullable()->comment('审批备注');
            $table->timestamps();

            // 索引
            $table->index('equipment_id');
            $table->index('reservation_id');
            $table->index('borrower_id');
            $table->index('borrow_date');
            $table->index('status');
            $table->index('approver_id');

            // 外键
            $table->foreign('equipment_id')->references('id')->on('equipments');
            $table->foreign('reservation_id')->references('id')->on('experiment_reservations');
            $table->foreign('borrower_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_borrows');
    }
};
