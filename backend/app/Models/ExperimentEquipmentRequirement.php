<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentEquipmentRequirement extends Model
{
    protected $fillable = [
        'catalog_id',
        'equipment_id', 
        'required_quantity',
        'min_quantity',
        'is_required',
        'calculation_type',
        'group_size',
        'usage_note',
        'safety_note',
        'sort_order',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'required_quantity' => 'integer',
        'min_quantity' => 'integer',
        'group_size' => 'integer',
        'sort_order' => 'integer'
    ];

    // 计算方式常量
    const CALCULATION_FIXED = 'fixed';
    const CALCULATION_PER_GROUP = 'per_group';
    const CALCULATION_PER_STUDENT = 'per_student';

    public static function getCalculationTypes(): array
    {
        return [
            self::CALCULATION_FIXED => '固定数量',
            self::CALCULATION_PER_GROUP => '按组计算', 
            self::CALCULATION_PER_STUDENT => '按学生计算'
        ];
    }

    /**
     * 关联实验目录
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联器材设备
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * 关联创建人
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 根据学生人数计算实际需要数量
     */
    public function calculateActualQuantity(int $studentCount = 30): int
    {
        switch ($this->calculation_type) {
            case self::CALCULATION_FIXED:
                return $this->required_quantity;
                
            case self::CALCULATION_PER_GROUP:
                $groupSize = $this->group_size ?: 4;
                $groupCount = ceil($studentCount / $groupSize);
                return max($this->min_quantity, $this->required_quantity * $groupCount);
                
            case self::CALCULATION_PER_STUDENT:
                return max($this->min_quantity, $this->required_quantity * $studentCount);
                
            default:
                return $this->required_quantity;
        }
    }

    /**
     * 获取计算方式文本
     */
    public function getCalculationTypeTextAttribute(): string
    {
        return self::getCalculationTypes()[$this->calculation_type] ?? '未知';
    }

    /**
     * 作用域：启用的配置
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：必需器材
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}