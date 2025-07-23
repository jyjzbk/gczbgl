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
        Schema::table('experiment_catalogs', function (Blueprint $table) {
            // 新增字段
            $table->unsignedBigInteger('textbook_version_id')->nullable()->after('subject_id')->comment('教材版本ID');
            $table->unsignedBigInteger('chapter_id')->nullable()->after('textbook_version_id')->comment('章节ID');
            $table->string('grade_level', 20)->nullable()->after('chapter_id')->comment('年级');
            $table->string('volume', 20)->nullable()->after('grade_level')->comment('册次');
            $table->tinyInteger('management_level')->default(5)->after('volume')->comment('管理级别（1省2市3区县4学区5学校）');
            $table->enum('experiment_type', ['必做','选做','演示','分组'])->default('必做')->after('management_level')->comment('实验类型');
            $table->unsignedBigInteger('parent_catalog_id')->nullable()->after('experiment_type')->comment('上级实验目录ID（继承关系）');
            $table->unsignedBigInteger('original_catalog_id')->nullable()->after('parent_catalog_id')->comment('原始实验目录ID（版本追踪）');
            $table->integer('version')->default(1)->after('original_catalog_id')->comment('版本号');
            $table->boolean('is_deleted_by_lower')->default(false)->after('version')->comment('是否被下级删除');
            $table->text('delete_reason')->nullable()->after('is_deleted_by_lower')->comment('删除理由');
            $table->tinyInteger('created_by_level')->nullable()->after('delete_reason')->comment('创建者级别');
            $table->unsignedBigInteger('created_by_org_id')->nullable()->after('created_by_level')->comment('创建者组织ID');
            $table->string('created_by_org_type', 20)->nullable()->after('created_by_org_id')->comment('创建者组织类型');

            // 添加索引
            $table->index(['management_level'], 'idx_management_level');
            $table->index(['subject_id', 'grade_level', 'textbook_version_id'], 'idx_subject_grade_version');
            $table->index(['parent_catalog_id'], 'idx_parent_catalog');
            $table->index(['original_catalog_id'], 'idx_original_catalog');
            $table->index(['experiment_type'], 'idx_experiment_type');
            $table->index(['created_by_level', 'created_by_org_id'], 'idx_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experiment_catalogs', function (Blueprint $table) {
            // 删除索引
            $table->dropIndex('idx_management_level');
            $table->dropIndex('idx_subject_grade_version');
            $table->dropIndex('idx_parent_catalog');
            $table->dropIndex('idx_original_catalog');
            $table->dropIndex('idx_experiment_type');
            $table->dropIndex('idx_created_by');

            // 删除字段
            $table->dropColumn([
                'textbook_version_id', 'chapter_id', 'grade_level', 'volume',
                'management_level', 'experiment_type', 'parent_catalog_id',
                'original_catalog_id', 'version', 'is_deleted_by_lower',
                'delete_reason', 'created_by_level', 'created_by_org_id',
                'created_by_org_type'
            ]);
        });
    }
};
