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
        Schema::create('experiment_catalog_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id')->comment('实验目录ID');
            $table->integer('version')->comment('版本号');
            $table->string('name', 200)->comment('实验名称');
            $table->text('content')->nullable()->comment('实验内容');
            $table->text('objective')->nullable()->comment('实验目标');
            $table->text('procedure')->nullable()->comment('实验步骤');
            $table->text('safety_notes')->nullable()->comment('安全注意事项');
            $table->string('change_reason', 500)->comment('变更原因');
            $table->unsignedBigInteger('changed_by')->comment('变更人ID');
            $table->timestamps();

            $table->index(['catalog_id', 'version'], 'idx_catalog_version');
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalog_versions');
    }
};
