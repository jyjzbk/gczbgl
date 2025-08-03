<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TextbookVersionAssignment extends Model
{
    protected $fillable = [
        'assigner_level',
        'assigner_org_id', 
        'assigner_org_type',
        'assigner_user_id',
        'school_id',
        'subject_id',
        'grade_level',
        'textbook_version_id',
        'status',
        'assignment_reason',
        'effective_date',
        'expiry_date',
        'replaced_assignment_id',
        'change_reason'
    ];

    protected $casts = [
        'assigner_level' => 'integer',
        'assigner_org_id' => 'integer',
        'assigner_user_id' => 'integer',
        'school_id' => 'integer',
        'subject_id' => 'integer',
        'textbook_version_id' => 'integer',
        'status' => 'integer',
        'effective_date' => 'datetime',
        'expiry_date' => 'datetime',
        'replaced_assignment_id' => 'integer'
    ];

    // 状态常量
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // 级别常量
    const LEVEL_PROVINCE = 1;
    const LEVEL_CITY = 2;
    const LEVEL_COUNTY = 3;
    const LEVEL_DISTRICT = 4;
    const LEVEL_SCHOOL = 5;

    /**
     * 关联学校
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联学科
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * 关联教材版本
     */
    public function textbookVersion(): BelongsTo
    {
        return $this->belongsTo(TextbookVersion::class);
    }

    /**
     * 关联指定操作用户
     */
    public function assignerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigner_user_id');
    }

    /**
     * 关联被替换的指定记录
     */
    public function replacedAssignment(): BelongsTo
    {
        return $this->belongsTo(TextbookVersionAssignment::class, 'replaced_assignment_id');
    }

    /**
     * 关联替换此记录的新指定
     */
    public function replacingAssignments(): HasMany
    {
        return $this->hasMany(TextbookVersionAssignment::class, 'replaced_assignment_id');
    }

    /**
     * 作用域：生效的指定
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('effective_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now());
                    });
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按学科筛选
     */
    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * 作用域：按年级筛选
     */
    public function scopeForGrade($query, string $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    /**
     * 作用域：按指定者级别筛选
     */
    public function scopeByAssignerLevel($query, int $level)
    {
        return $query->where('assigner_level', $level);
    }

    /**
     * 作用域：按指定者组织筛选
     */
    public function scopeByAssignerOrg($query, int $orgId, string $orgType)
    {
        return $query->where('assigner_org_id', $orgId)
                    ->where('assigner_org_type', $orgType);
    }

    /**
     * 获取指定者级别名称
     */
    public function getAssignerLevelNameAttribute(): string
    {
        $levels = [
            self::LEVEL_PROVINCE => '省级',
            self::LEVEL_CITY => '市级',
            self::LEVEL_COUNTY => '区县级',
            self::LEVEL_DISTRICT => '学区级',
            self::LEVEL_SCHOOL => '学校级'
        ];

        return $levels[$this->assigner_level] ?? '未知';
    }

    /**
     * 获取状态名称
     */
    public function getStatusNameAttribute(): string
    {
        return $this->status === self::STATUS_ACTIVE ? '生效' : '失效';
    }

    /**
     * 检查指定是否当前生效
     */
    public function isCurrentlyActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->effective_date <= now() &&
               (is_null($this->expiry_date) || $this->expiry_date > now());
    }

    /**
     * 使指定失效
     */
    public function deactivate(string $reason = null): bool
    {
        $this->status = self::STATUS_INACTIVE;
        if ($reason) {
            $this->change_reason = $reason;
        }
        return $this->save();
    }
}
