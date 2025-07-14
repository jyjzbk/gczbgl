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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->string('scope_type', 20)->comment('权限范围类型：region/school');
            $table->unsignedBigInteger('scope_id')->comment('权限范围ID');
            $table->timestamps();

            // 索引
            $table->unique(['user_id', 'role_id', 'scope_type', 'scope_id'], 'uk_user_role_scope');
            $table->index('user_id');
            $table->index('role_id');

            // 外键
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
