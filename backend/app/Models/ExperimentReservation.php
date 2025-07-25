<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExperimentReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'catalog_id',
        'laboratory_id',
        'teacher_id',
        'class_name',
        'student_count',
        'reservation_date',
        'start_time',
        'end_time',
        'status',
        'remark',
        'reviewer_id',
        'reviewed_at',
        'review_remark',
        'batch_id',
        'equipment_requirements',
        'auto_borrow_equipment',
        'priority',
        'preparation_notes'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'catalog_id' => 'integer',
        'laboratory_id' => 'integer',
        'teacher_id' => 'integer',
        'student_count' => 'integer',
        'status' => 'integer',
        'reviewer_id' => 'integer',
        'batch_id' => 'integer',
        'auto_borrow_equipment' => 'boolean',
        'reservation_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'reviewed_at' => 'datetime',
        'equipment_requirements' => 'array'
    ];

    // 状态常量
    const STATUS_PENDING = 1;    // 待审核
    const STATUS_APPROVED = 2;   // 已通过
    const STATUS_REJECTED = 3;   // 已拒绝
    const STATUS_COMPLETED = 4;  // 已完成
    const STATUS_CANCELLED = 5;  // 已取消

    // 优先级常量
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_CANCELLED => 'secondary'
        ];
        return $colors[$this->status] ?? 'default';
    }

    /**
     * 关联学校
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联实验目录
     */
    public function catalog()
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联实验室
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * 关联授课教师
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * 关联审核人
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * 关联实验记录
     */
    public function record()
    {
        return $this->hasOne(ExperimentRecord::class, 'reservation_id');
    }

    /**
     * 关联预约批次
     */
    public function batch()
    {
        return $this->belongsTo(ReservationBatch::class, 'batch_id');
    }

    /**
     * 关联设备借用记录
     */
    public function equipmentBorrows()
    {
        return $this->hasMany(EquipmentBorrow::class, 'reservation_id');
    }

    /**
     * 关联冲突日志
     */
    public function conflictLogs()
    {
        return $this->hasMany(ReservationConflictLog::class, 'reservation_id');
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按教师筛选
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reservation_date', [$startDate, $endDate]);
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：已通过
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * 作用域：有效预约（未取消）
     */
    public function scopeValid($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * 检查是否可以取消
     */
    public function canCancel()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]) 
               && $this->reservation_date >= Carbon::today();
    }

    /**
     * 检查是否可以修改
     */
    public function canEdit()
    {
        return $this->status === self::STATUS_PENDING 
               && $this->reservation_date >= Carbon::today();
    }

    /**
     * 检查是否可以审核
     */
    public function canReview()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 检查是否可以开始实验
     */
    public function canStartExperiment()
    {
        return $this->status === self::STATUS_APPROVED
               && $this->reservation_date >= Carbon::today()
               && !$this->record;
    }

    /**
     * 获取时间段文本
     */
    public function getTimeSlotAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * 获取持续时间（分钟）
     */
    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * 获取优先级名称
     */
    public function getPriorityNameAttribute()
    {
        $priorities = [
            self::PRIORITY_LOW => '低',
            self::PRIORITY_NORMAL => '普通',
            self::PRIORITY_HIGH => '高',
            self::PRIORITY_URGENT => '紧急'
        ];
        return $priorities[$this->priority] ?? '普通';
    }

    /**
     * 获取优先级颜色
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            self::PRIORITY_LOW => 'info',
            self::PRIORITY_NORMAL => 'primary',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger'
        ];
        return $colors[$this->priority] ?? 'primary';
    }

    /**
     * 自动生成设备借用记录
     */
    public function generateEquipmentBorrows()
    {
        if (!$this->auto_borrow_equipment || !$this->equipment_requirements) {
            return;
        }

        foreach ($this->equipment_requirements as $requirement) {
            EquipmentBorrow::create([
                'equipment_id' => $requirement['equipment_id'],
                'reservation_id' => $this->id,
                'borrower_id' => $this->teacher_id,
                'quantity' => $requirement['quantity'],
                'borrow_date' => $this->reservation_date,
                'expected_return_date' => $this->reservation_date,
                'purpose' => '实验教学：' . $this->catalog->name,
                'status' => EquipmentBorrow::STATUS_APPROVED
            ]);
        }
    }

    /**
     * 作用域：按批次筛选
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * 作用域：按优先级筛选
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }



    /**
     * 自动创建设备借用记录
     */
    public function createEquipmentBorrows(): void
    {
        if ($this->auto_borrow_equipment && $this->equipment_requirements) {
            $borrowService = app(\App\Services\EquipmentBorrowService::class);
            $borrowService->createBorrowsFromReservation($this);
        }
    }

    /**
     * 更新设备借用记录
     */
    public function updateEquipmentBorrows(): void
    {
        if ($this->auto_borrow_equipment) {
            $borrowService = app(\App\Services\EquipmentBorrowService::class);
            $borrowService->updateBorrowsFromReservation($this);
        }
    }

    /**
     * 获取设备借用统计
     */
    public function getBorrowStats(): array
    {
        $borrowService = app(\App\Services\EquipmentBorrowService::class);
        return $borrowService->getReservationBorrowStats($this);
    }
}
