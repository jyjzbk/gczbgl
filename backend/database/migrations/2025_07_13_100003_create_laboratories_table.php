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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->string('name', 100)->comment('实验室名称');
            $table->string('code', 50)->comment('实验室编号');
            $table->tinyInteger('type')->comment('实验室类型：1物理 2化学 3生物 4综合');
            $table->string('location', 200)->nullable()->comment('位置');
            $table->decimal('area', 8, 2)->nullable()->comment('面积(平方米)');
            $table->integer('capacity')->default(50)->comment('容纳人数');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('管理员ID');
            $table->text('equipment_list')->nullable()->comment('设备清单');
            $table->text('safety_rules')->nullable()->comment('安全规则');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常 2维修 0停用');
            $table->timestamps();

            // 索引
            $table->index('school_id');
            $table->index('code');
            $table->index('type');
            $table->index('manager_id');
            $table->index('status');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('manager_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
