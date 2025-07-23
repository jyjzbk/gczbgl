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
        Schema::create('experiment_catalog_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id')->comment('实验目录ID');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('学科ID');
            $table->string('organization_type', 20)->comment('组织类型');
            $table->unsignedBigInteger('organization_id')->comment('组织ID');
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('permission_type', 30)->comment('权限类型');
            $table->unsignedBigInteger('granted_by')->comment('授权人ID');
            $table->timestamp('expires_at')->nullable()->comment('过期时间');
            $table->timestamps();

            $table->index(['catalog_id', 'organization_type', 'organization_id'], 'idx_catalog_org');
            $table->index(['user_id', 'permission_type'], 'idx_user_permission');
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('granted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalog_permissions');
    }
};
