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
        Schema::create('laboratory_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('类型名称');
            $table->string('code', 50)->unique()->comment('类型代码');
            $table->string('description', 500)->nullable()->comment('类型描述');
            $table->string('icon', 50)->nullable()->comment('图标名称');
            $table->string('color', 20)->nullable()->comment('颜色代码');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();
            
            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_types');
    }
};
