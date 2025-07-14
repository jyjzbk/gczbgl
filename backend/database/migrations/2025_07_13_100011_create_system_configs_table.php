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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 100)->comment('配置键');
            $table->text('key_value')->nullable()->comment('配置值');
            $table->string('description', 255)->nullable()->comment('配置描述');
            $table->string('type', 20)->default('string')->comment('数据类型');
            $table->string('group_name', 50)->nullable()->comment('配置分组');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('is_system')->default(0)->comment('是否系统配置');
            $table->timestamps();

            // 索引
            $table->unique('key_name');
            $table->index('group_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
