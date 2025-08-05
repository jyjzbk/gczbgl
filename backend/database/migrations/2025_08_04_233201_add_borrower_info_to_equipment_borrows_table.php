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
        Schema::table('equipment_borrows', function (Blueprint $table) {
            $table->string('borrower_name', 100)->nullable()->after('borrower_id')->comment('借用人姓名');
            $table->string('borrower_phone', 20)->nullable()->after('borrower_name')->comment('借用人电话');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_borrows', function (Blueprint $table) {
            $table->dropColumn(['borrower_name', 'borrower_phone']);
        });
    }
};
