<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingEquipmentStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'standard_name',
        'standard_code',
        'authority_type',
        'stage',
        'subject_code',
        'subject_name',
        'description',
        'category_level_1',
        'category_level_2',
        'category_level_3',
        'item_code',
        'item_name',
        'specs_requirements',
        'unit',
        'quantity',
        'unit_price',
        'total_amount',
        'is_basic',
        'is_optional',
        'standard_reference',
        'remarks',
        'activity_suggestion',
        'version',
        'effective_date',
        'expiry_date',
        'status'
    ];

    protected $casts = [
        'authority_type' => 'integer',
        'stage' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_basic' => 'boolean',
        'is_optional' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'status' => 'integer'
    ];

    // 制定机构类型
    const AUTHORITY_TYPE_MOE = 1; // 教育部
    const AUTHORITY_TYPE_PROVINCIAL = 2; // 教育厅
    const AUTHORITY_TYPE_LOCAL = 3; // 地方

    // 学段
    const STAGE_PRIMARY = 1; // 小学
    const STAGE_JUNIOR = 2; // 初中
    const STAGE_SENIOR = 3; // 高中

    // 状态
    const STATUS_ACTIVE = 1; // 启用
    const STATUS_INACTIVE = 0; // 禁用

    /**
     * 获取制定机构类型文本
     */
    public function getAuthorityTypeTextAttribute()
    {
        $types = [
            self::AUTHORITY_TYPE_MOE => '教育部',
            self::AUTHORITY_TYPE_PROVINCIAL => '教育厅',
            self::AUTHORITY_TYPE_LOCAL => '地方'
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
     * 自动计算总金额
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->unit_price && $model->quantity) {
                $model->total_amount = $model->unit_price * $model->quantity;
            }
        });
    }

    /**
     * 按学段和学科分组
     */
    public function scopeByStageAndSubject($query, $stage, $subjectCode)
    {
        return $query->where('stage', $stage)
                    ->where('subject_code', $subjectCode)
                    ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * 按分类查询
     */
    public function scopeByCategory($query, $level1, $level2 = null, $level3 = null)
    {
        $query->where('category_level_1', $level1);
        
        if ($level2) {
            $query->where('category_level_2', $level2);
        }
        
        if ($level3) {
            $query->where('category_level_3', $level3);
        }
        
        return $query;
    }
}
