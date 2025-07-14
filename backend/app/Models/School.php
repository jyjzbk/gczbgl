<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'level',
        'region_id',
        'address',
        'contact_person',
        'contact_phone',
        'student_count',
        'class_count',
        'teacher_count',
        'status'
    ];

    protected $casts = [
        'type' => 'integer',
        'level' => 'integer',
        'region_id' => 'integer',
        'student_count' => 'integer',
        'class_count' => 'integer',
        'teacher_count' => 'integer',
        'status' => 'integer'
    ];

    /**
     * 获取所属区域
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(AdministrativeRegion::class, 'region_id');
    }

    /**
     * 学校类型常量
     */
    const TYPE_PRIMARY = 1;        // 小学
    const TYPE_JUNIOR = 2;         // 初中
    const TYPE_SENIOR = 3;         // 高中
    const TYPE_NINE_YEAR = 4;      // 九年一贯制

    /**
     * 管理级别常量
     */
    const LEVEL_PROVINCE = 1;      // 省直
    const LEVEL_CITY = 2;          // 市直
    const LEVEL_COUNTY = 3;        // 区县直
    const LEVEL_DISTRICT = 4;      // 学区

    /**
     * 获取学校类型名称
     */
    public function getTypeNameAttribute(): string
    {
        $types = [
            self::TYPE_PRIMARY => '小学',
            self::TYPE_JUNIOR => '初中',
            self::TYPE_SENIOR => '高中',
            self::TYPE_NINE_YEAR => '九年一贯制'
        ];

        return $types[$this->type] ?? '未知';
    }

    /**
     * 获取管理级别名称
     */
    public function getLevelNameAttribute(): string
    {
        $levels = [
            self::LEVEL_PROVINCE => '省直',
            self::LEVEL_CITY => '市直',
            self::LEVEL_COUNTY => '区县直',
            self::LEVEL_DISTRICT => '学区'
        ];

        return $levels[$this->level] ?? '未知';
    }
}
