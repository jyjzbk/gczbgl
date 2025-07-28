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
        Schema::create('school_experiment_catalog_selections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->enum('selected_level', ['province', 'city', 'county'])->comment('选择的标准级别');
            $table->unsignedBigInteger('selected_org_id')->comment('选择的组织ID');
            $table->string('selected_org_name', 100)->comment('选择的组织名称');
            $table->boolean('can_delete_experiments')->default(false)->comment('是否允许删除实验');
            $table->text('selection_reason')->nullable()->comment('选择理由');
            $table->unsignedBigInteger('selected_by')->comment('选择操作人');
            $table->timestamp('selected_at')->useCurrent()->comment('选择时间');
            $table->timestamps();

            // 索引
            $table->index(['school_id'], 'idx_school_id');
            $table->index(['selected_level', 'selected_org_id'], 'idx_selected_org');
            
            // 唯一约束：每个学校只能有一个选择
            $table->unique(['school_id'], 'unique_school_selection');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('selected_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_experiment_catalog_selections');
    }
};
