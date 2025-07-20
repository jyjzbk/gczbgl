<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_name',
        'school_id',
        'subject_id',
        'grade',
        'semester',
        'start_date',
        'end_date',
        'created_by',
        'status',
        'reviewer_id',
        'reviewed_at',
        'review_remark',
        'total_reservations',
        'completed_reservations'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'subject_id' => 'integer',
        'grade' => 'integer',
        'semester' => 'integer',
        'created_by' => 'integer',
        'reviewer_id' => 'integer',
        'total_reservations' => 'integer',
        'completed_reservations' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime'
    ];

    // 状态常量
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

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
     * 关联创建人（备课组长）
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 关联审核人
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * 关联预约记录
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(ExperimentReservation::class, 'batch_id');
    }

    /**
     * 获取状态名称
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => '草稿',
            self::STATUS_SUBMITTED => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
            self::STATUS_COMPLETED => '已完成',
            default => '未知'
        };
    }

    /**
     * 获取学期名称
     */
    public function getSemesterNameAttribute(): string
    {
        return $this->semester == 1 ? '上学期' : '下学期';
    }

    /**
     * 获取年级名称
     */
    public function getGradeNameAttribute(): string
    {
        return $this->grade . '年级';
    }

    /**
     * 获取完成率
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->total_reservations == 0) {
            return 0;
        }
        return round(($this->completed_reservations / $this->total_reservations) * 100, 2);
    }

    /**
     * 是否可以编辑
     */
    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * 是否可以提交
     */
    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * 是否可以审核
     */
    public function canReview(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * 是否可以取消
     */
    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]);
    }

    /**
     * 提交审核
     */
    public function submit(): bool
    {
        if (!$this->canSubmit()) {
            return false;
        }

        $this->status = self::STATUS_SUBMITTED;
        return $this->save();
    }

    /**
     * 审核通过
     */
    public function approve($reviewerId, $remark = null): bool
    {
        if (!$this->canReview()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->reviewer_id = $reviewerId;
        $this->reviewed_at = now();
        $this->review_remark = $remark;
        
        return $this->save();
    }

    /**
     * 审核拒绝
     */
    public function reject($reviewerId, $remark): bool
    {
        if (!$this->canReview()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->reviewer_id = $reviewerId;
        $this->reviewed_at = now();
        $this->review_remark = $remark;
        
        return $this->save();
    }

    /**
     * 更新完成统计
     */
    public function updateCompletionStats(): void
    {
        $this->total_reservations = $this->reservations()->count();
        $this->completed_reservations = $this->reservations()
            ->where('status', ExperimentReservation::STATUS_COMPLETED)
            ->count();
        
        // 如果所有预约都完成了，更新批次状态
        if ($this->total_reservations > 0 && 
            $this->completed_reservations >= $this->total_reservations) {
            $this->status = self::STATUS_COMPLETED;
        }
        
        $this->save();
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按学科筛选
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按创建人筛选
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('created_by', $creatorId);
    }

    /**
     * 作用域：待审核的批次
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }
}
