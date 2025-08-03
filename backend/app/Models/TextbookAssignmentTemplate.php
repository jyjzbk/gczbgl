<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextbookAssignmentTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'creator_level',
        'creator_org_id',
        'creator_org_type', 
        'creator_user_id',
        'assignment_config',
        'applicable_grades',
        'applicable_school_types',
        'usage_count',
        'last_used_at',
        'status',
        'is_default'
    ];

    protected $casts = [
        'creator_level' => 'integer',
        'creator_org_id' => 'integer',
        'creator_user_id' => 'integer',
        'assignment_config' => 'array',
        'applicable_grades' => 'array',
        'applicable_school_types' => 'array',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'status' => 'integer',
        'is_default' => 'integer'
    ];

    // 状态常量
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * 关联创建用户
     */
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * 作用域：启用的模板
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * 作用域：默认模板
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }

    /**
     * 作用域：按创建者级别筛选
     */
    public function scopeByCreatorLevel($query, int $level)
    {
        return $query->where('creator_level', $level);
    }

    /**
     * 作用域：按创建者组织筛选
     */
    public function scopeByCreatorOrg($query, int $orgId, string $orgType)
    {
        return $query->where('creator_org_id', $orgId)
                    ->where('creator_org_type', $orgType);
    }

    /**
     * 增加使用次数
     */
    public function incrementUsage(): bool
    {
        $this->increment('usage_count');
        $this->last_used_at = now();
        return $this->save();
    }

    /**
     * 获取状态名称
     */
    public function getStatusNameAttribute(): string
    {
        return $this->status === self::STATUS_ACTIVE ? '启用' : '禁用';
    }

    /**
     * 获取创建者级别名称
     */
    public function getCreatorLevelNameAttribute(): string
    {
        $levels = [
            1 => '省级',
            2 => '市级', 
            3 => '区县级',
            4 => '学区级',
            5 => '学校级'
        ];

        return $levels[$this->creator_level] ?? '未知';
    }

    /**
     * 检查模板是否适用于指定年级
     */
    public function isApplicableToGrade(string $gradeLevel): bool
    {
        return in_array($gradeLevel, $this->applicable_grades ?? []);
    }

    /**
     * 检查模板是否适用于指定学校类型
     */
    public function isApplicableToSchoolType(int $schoolType): bool
    {
        if (empty($this->applicable_school_types)) {
            return true; // 如果没有限制，则适用于所有类型
        }
        return in_array($schoolType, $this->applicable_school_types);
    }

    /**
     * 获取配置的学科教材版本映射
     */
    public function getSubjectVersionMappings(): array
    {
        return $this->assignment_config ?? [];
    }
}
