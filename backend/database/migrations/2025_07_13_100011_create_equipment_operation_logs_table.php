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
        Schema::create('equipment_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('operation_type', 50)->comment('操作类型：create,update,delete,borrow,return,maintenance,etc');
            $table->string('operation_module', 50)->comment('操作模块：equipment,borrow,maintenance,qrcode');
            $table->string('operation_description')->comment('操作描述');
            $table->json('old_data')->nullable()->comment('操作前数据');
            $table->json('new_data')->nullable()->comment('操作后数据');
            $table->string('ip_address', 45)->nullable()->comment('操作IP地址');
            $table->string('user_agent', 500)->nullable()->comment('用户代理');
            $table->json('extra_data')->nullable()->comment('额外数据');
            $table->timestamps();
            
            $table->index(['equipment_id', 'operation_type']);
            $table->index(['user_id', 'created_at']);
            $table->index('operation_module');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_operation_logs');
    }
};
