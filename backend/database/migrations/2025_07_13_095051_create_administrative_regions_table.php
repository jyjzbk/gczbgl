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
        Schema::create('administrative_regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->comment('区域代码');
            $table->string('name', 100)->comment('区域名称');
            $table->tinyInteger('level')->comment('级别：1省 2市 3区县 4学区');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父级ID');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();

            // 索引
            $table->index('code');
            $table->index('parent_id');
            $table->index('level');
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrative_regions');
    }
};
