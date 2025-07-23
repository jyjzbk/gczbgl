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
        Schema::create('textbook_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('版本名称（人教版、苏教版等）');
            $table->string('code', 20)->unique()->comment('版本代码');
            $table->string('publisher', 100)->nullable()->comment('出版社');
            $table->text('description')->nullable()->comment('版本描述');
            $table->tinyInteger('status')->default(1)->comment('状态（1启用0禁用）');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbook_versions');
    }
};
