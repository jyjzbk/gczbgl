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
        Schema::create('textbook_chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->comment('学科ID');
            $table->unsignedBigInteger('textbook_version_id')->comment('教材版本ID');
            $table->string('grade_level', 20)->comment('年级');
            $table->string('volume', 20)->comment('册次（上册、下册）');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父级章节ID');
            $table->tinyInteger('level')->comment('层级（1章2节3小节）');
            $table->string('code', 50)->comment('章节编码（如：01、01-01、01-01-01）');
            $table->string('name', 200)->comment('章节名称');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();

            $table->index(['subject_id', 'textbook_version_id', 'grade_level'], 'idx_subject_version_grade');
            $table->index(['parent_id'], 'idx_parent');
            $table->index(['level', 'sort_order'], 'idx_level_sort');

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('textbook_version_id')->references('id')->on('textbook_versions')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('textbook_chapters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbook_chapters');
    }
};
