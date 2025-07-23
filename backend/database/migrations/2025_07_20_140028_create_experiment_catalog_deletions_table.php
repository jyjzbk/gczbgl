<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('experiment_catalog_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id')->comment('被删除的实验目录ID');
            $table->string('deleted_by_org_type', 20)->comment('删除组织类型');
            $table->unsignedBigInteger('deleted_by_org_id')->comment('删除组织ID');
            $table->unsignedBigInteger('deleted_by_user_id')->comment('删除用户ID');
            $table->text('delete_reason')->comment('删除理由');
            $table->timestamp('deleted_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('删除时间');
            $table->timestamp('restored_at')->nullable()->comment('恢复时间');
            $table->unsignedBigInteger('restored_by')->nullable()->comment('恢复人ID');
            $table->timestamps();

            $table->index(['catalog_id', 'deleted_by_org_type', 'deleted_by_org_id'], 'idx_catalog_org');
            $table->foreign('catalog_id')->references('id')->on('experiment_catalogs')->onDelete('cascade');
            $table->foreign('deleted_by_user_id')->references('id')->on('users');
            $table->foreign('restored_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_catalog_deletions');
    }
};
