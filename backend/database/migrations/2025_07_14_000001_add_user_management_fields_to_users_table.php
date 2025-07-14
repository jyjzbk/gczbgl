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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->nullable()->after('status')->comment('用户角色');
            $table->string('department', 100)->nullable()->after('role')->comment('部门');
            $table->string('position', 100)->nullable()->after('department')->comment('职位');
            $table->text('bio')->nullable()->after('position')->comment('个人简介');
            $table->unsignedBigInteger('school_id')->nullable()->after('bio')->comment('学校ID');
            $table->string('school_name', 100)->nullable()->after('school_id')->comment('学校名称');
            
            // 添加索引
            $table->index('role');
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['school_id']);
            $table->dropColumn(['role', 'department', 'position', 'bio', 'school_id', 'school_name']);
        });
    }
};
