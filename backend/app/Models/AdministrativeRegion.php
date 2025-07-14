<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdministrativeRegion extends Model
{
    protected $fillable = [
        'code',
        'name',
        'level',
        'parent_id',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'level' => 'integer',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer'
    ];

    /**
     * 获取父级区域
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AdministrativeRegion::class, 'parent_id');
    }

    /**
     * 获取子级区域
     */
    public function children(): HasMany
    {
        return $this->hasMany(AdministrativeRegion::class, 'parent_id');
    }

    /**
     * 获取所属学校
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'region_id');
    }

    /**
     * 级别常量
     */
    const LEVEL_PROVINCE = 1;  // 省级
    const LEVEL_CITY = 2;      // 市级
    const LEVEL_COUNTY = 3;    // 区县级
    const LEVEL_DISTRICT = 4;  // 学区级

    /**
     * 获取级别名称
     */
    public function getLevelNameAttribute(): string
    {
        $levels = [
            self::LEVEL_PROVINCE => '省级',
            self::LEVEL_CITY => '市级',
            self::LEVEL_COUNTY => '区县级',
            self::LEVEL_DISTRICT => '学区级'
        ];

        return $levels[$this->level] ?? '未知';
    }
}
