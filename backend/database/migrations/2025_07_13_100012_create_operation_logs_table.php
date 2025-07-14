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
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('操作用户ID');
            $table->string('module', 50)->comment('操作模块');
            $table->string('action', 50)->comment('操作动作');
            $table->text('description')->nullable()->comment('操作描述');
            $table->json('request_data')->nullable()->comment('请求数据');
            $table->json('response_data')->nullable()->comment('响应数据');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->text('user_agent')->nullable()->comment('用户代理');
            $table->integer('execution_time')->nullable()->comment('执行时间(毫秒)');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');

            // 索引
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index('created_at');

            // 外键
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
