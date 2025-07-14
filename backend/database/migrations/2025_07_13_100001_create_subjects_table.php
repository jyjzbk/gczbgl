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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('学科名称');
            $table->string('code', 20)->comment('学科代码');
            $table->tinyInteger('type')->comment('学科类型：1理科 2文科 3综合');
            $table->tinyInteger('stage')->comment('学段：1小学 2初中 3高中');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();

            // 索引
            $table->unique('code');
            $table->index('type');
            $table->index('stage');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
