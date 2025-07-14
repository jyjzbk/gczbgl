<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'stage',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'type' => 'integer',
        'stage' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer'
    ];

    // 学科类型常量
    const TYPE_SCIENCE = 1;      // 理科
    const TYPE_LIBERAL_ARTS = 2; // 文科
    const TYPE_COMPREHENSIVE = 3; // 综合

    // 学段常量
    const STAGE_PRIMARY = 1;   // 小学
    const STAGE_MIDDLE = 2;    // 初中
    const STAGE_HIGH = 3;      // 高中

    // 状态常量
    const STATUS_ACTIVE = 1;   // 启用
    const STATUS_INACTIVE = 0; // 禁用

    /**
     * 获取学科类型文本
     */
    public function getTypeTextAttribute()
    {
        $types = [
            self::TYPE_SCIENCE => '理科',
            self::TYPE_LIBERAL_ARTS => '文科',
            self::TYPE_COMPREHENSIVE => '综合'
        ];
        return $types[$this->type] ?? '未知';
    }

    /**
     * 获取学段文本
     */
    public function getStageTextAttribute()
    {
        $stages = [
            self::STAGE_PRIMARY => '小学',
            self::STAGE_MIDDLE => '初中',
            self::STAGE_HIGH => '高中'
        ];
        return $stages[$this->stage] ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? '启用' : '禁用';
    }

    /**
     * 关联实验目录
     */
    public function experimentCatalogs()
    {
        return $this->hasMany(ExperimentCatalog::class);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：按学段筛选
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * 作用域：启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
