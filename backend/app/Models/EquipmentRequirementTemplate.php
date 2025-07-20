<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentRequirementTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'subject_id',
        'experiment_type',
        'equipment_list',
        'is_public',
        'created_by',
        'school_id',
        'use_count'
    ];

    protected $casts = [
        'equipment_list' => 'array',
        'is_public' => 'boolean',
        'use_count' => 'integer'
    ];

    // 实验类型常量
    const TYPE_DEMO = '演示实验';
    const TYPE_GROUP = '分组实验';
    const TYPE_INQUIRY = '探究实验';
    const TYPE_COMPREHENSIVE = '综合实验';

    public static function getExperimentTypes(): array
    {
        return [
            self::TYPE_DEMO => '演示实验',
            self::TYPE_GROUP => '分组实验',
            self::TYPE_INQUIRY => '探究实验',
            self::TYPE_COMPREHENSIVE => '综合实验'
        ];
    }

    /**
     * 关联学科
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * 关联创建人
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 增加使用次数
     */
    public function incrementUseCount(): void
    {
        $this->increment('use_count');
    }

    /**
     * 作用域：公开模板
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * 作用域：按学科
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * 作用域：按实验类型
     */
    public function scopeByType($query, $type)
    {
        return $query->where('experiment_type', $type);
    }
}
