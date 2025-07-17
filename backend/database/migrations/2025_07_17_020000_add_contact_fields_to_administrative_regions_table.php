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
        Schema::table('administrative_regions', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->comment('详细地址');
            $table->string('contact_person', 100)->nullable()->comment('联系人');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->string('email', 100)->nullable()->comment('邮箱地址');
            $table->text('description')->nullable()->comment('机构描述');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrative_regions', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'contact_person', 
                'contact_phone',
                'email',
                'description'
            ]);
        });
    }
};
