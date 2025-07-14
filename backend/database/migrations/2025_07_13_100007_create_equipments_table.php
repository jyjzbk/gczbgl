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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->comment('学校ID');
            $table->unsignedBigInteger('laboratory_id')->nullable()->comment('实验室ID');
            $table->unsignedBigInteger('category_id')->comment('分类ID');
            $table->string('name', 200)->comment('设备名称');
            $table->string('code', 100)->nullable()->comment('设备编号');
            $table->string('model', 100)->nullable()->comment('型号规格');
            $table->string('brand', 100)->nullable()->comment('品牌');
            $table->string('supplier', 200)->nullable()->comment('供应商');
            $table->string('supplier_phone', 20)->nullable()->comment('供应商电话');
            $table->date('purchase_date')->nullable()->comment('购入日期');
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('购入价格');
            $table->integer('quantity')->default(1)->comment('数量');
            $table->string('unit', 20)->default('台')->comment('单位');
            $table->integer('warranty_period')->nullable()->comment('保修期(月)');
            $table->integer('service_life')->nullable()->comment('使用年限(年)');
            $table->string('funding_source', 100)->nullable()->comment('经费来源');
            $table->string('storage_location', 200)->nullable()->comment('存放位置');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('保管人ID');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常 2维修 3报废 0停用');
            $table->string('qr_code', 255)->nullable()->comment('二维码');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            // 索引
            $table->index('school_id');
            $table->index('laboratory_id');
            $table->index('category_id');
            $table->index('code');
            $table->index('manager_id');
            $table->index('status');

            // 外键
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('category_id')->references('id')->on('equipment_categories');
            $table->foreign('manager_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
