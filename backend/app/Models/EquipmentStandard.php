<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'authority_type',
        'stage',
        'subject_code',
        'subject_name',
        'category_level_1',
        'category_level_2',
        'category_level_3',
        'description',
        'equipment_list',
        'estimated_unit_price',
        'estimated_total_amount',
        'is_basic_standard',
        'is_optional_standard',
        'standard_reference',
        'implementation_notes',
        'version',
        'effective_date',
        'expiry_date',
        'status'
    ];

    protected $casts = [
        'authority_type' => 'integer',
        'stage' => 'integer',
        'equipment_list' => 'array',
        'estimated_unit_price' => 'decimal:2',
        'estimated_total_amount' => 'decimal:2',
        'is_basic_standard' => 'boolean',
        'is_optional_standard' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'status' => 'integer'
    ];

    // 制定机构常量
    const AUTHORITY_MOE = 1;        // 教育部
    const AUTHORITY_PROVINCIAL = 2; // 教育厅

    // 学段常量
    const STAGE_PRIMARY = 1;   // 小学
    const STAGE_JUNIOR = 2;    // 初中
    const STAGE_SENIOR = 3;    // 高中

    // 状态常量
    const STATUS_ACTIVE = 1;   // 启用
    const STATUS_INACTIVE = 0; // 禁用

    /**
     * 获取制定机构文本
     */
    public function getAuthorityTypeTextAttribute()
    {
        $types = [
            self::AUTHORITY_MOE => '教育部',
            self::AUTHORITY_PROVINCIAL => '教育厅'
        ];
        return $types[$this->authority_type] ?? '未知';
    }

    /**
     * 获取学段文本
     */
    public function getStageTextAttribute()
    {
        $stages = [
            self::STAGE_PRIMARY => '小学',
            self::STAGE_JUNIOR => '初中',
            self::STAGE_SENIOR => '高中'
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
     * 作用域：启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * 作用域：有效期内
     */
    public function scopeEffective($query)
    {
        $now = now()->toDateString();
        return $query->where('effective_date', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', $now);
                    });
    }

    /**
     * 作用域：按制定机构筛选
     */
    public function scopeByAuthority($query, $authorityType)
    {
        return $query->where('authority_type', $authorityType);
    }

    /**
     * 作用域：按学段筛选
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * 作用域：按学科筛选
     */
    public function scopeBySubject($query, $subjectCode)
    {
        return $query->where('subject_code', $subjectCode);
    }
}
