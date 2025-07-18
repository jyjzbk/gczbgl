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
        Schema::table('laboratories', function (Blueprint $table) {
            // 添加类型ID字段，关联到laboratory_types表
            $table->unsignedBigInteger('type_id')->nullable()->after('type')->comment('实验室类型ID');
            
            // 添加外键约束
            $table->foreign('type_id')->references('id')->on('laboratory_types')->onDelete('set null');
            
            // 添加索引
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropIndex(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};
