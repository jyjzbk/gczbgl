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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('email', 100)->unique()->nullable()->comment('邮箱');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('password')->comment('密码');
            $table->string('real_name', 50)->comment('真实姓名');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('gender')->nullable()->comment('性别：1男 2女');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('id_card', 18)->nullable()->comment('身份证号');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常 0禁用');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->rememberToken();
            $table->timestamps();

            // 索引
            $table->index('phone');
            $table->index('status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
