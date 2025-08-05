<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EquipmentBorrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'reservation_id',
        'borrower_id',
        'quantity',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'purpose',
        'remark',
        'status',
        'approver_id',
        'approved_at',
        'approval_remark'
    ];

    protected $casts = [
        'equipment_id' => 'integer',
        'reservation_id' => 'integer',
        'borrower_id' => 'integer',
        'quantity' => 'integer',
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'status' => 'integer',
        'approver_id' => 'integer',
        'approved_at' => 'datetime'
    ];

    protected $appends = [
        'status_text',
        'status_color',
        'overdue_days',
        'borrow_days',
        'borrower_name',
        'borrower_phone'
    ];

    // 状态常量
    const STATUS_BORROWED = 1;  // 借用中
    const STATUS_RETURNED = 2;  // 已归还
    const STATUS_OVERDUE = 3;   // 逾期
    const STATUS_DAMAGED = 4;   // 损坏
    const STATUS_PENDING = 5;   // 待审批
    const STATUS_REJECTED = 6;  // 已拒绝

    /**
     * 获取设备信息
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * 获取实验预约信息
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(ExperimentReservation::class, 'reservation_id');
    }

    /**
     * 获取借用人信息
     */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    /**
     * 获取审批人信息
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_BORROWED => '借用中',
            self::STATUS_RETURNED => '已归还',
            self::STATUS_OVERDUE => '逾期',
            self::STATUS_DAMAGED => '损坏',
            self::STATUS_PENDING => '待审批',
            self::STATUS_REJECTED => '已拒绝'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_BORROWED => 'blue',
            self::STATUS_RETURNED => 'green',
            self::STATUS_OVERDUE => 'red',
            self::STATUS_DAMAGED => 'orange',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REJECTED => 'gray'
        ];
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * 检查是否逾期
     */
    public function isOverdue()
    {
        if ($this->status === self::STATUS_RETURNED) {
            return false;
        }

        return $this->expected_return_date < now()->toDateString();
    }

    /**
     * 获取逾期天数
     */
    public function getOverdueDaysAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return Carbon::parse($this->expected_return_date)->diffInDays(now());
    }

    /**
     * 获取借用天数
     */
    public function getBorrowDaysAttribute()
    {
        $endDate = $this->actual_return_date ?: now();
        return Carbon::parse($this->borrow_date)->diffInDays($endDate);
    }

    /**
     * 检查是否可以归还
     */
    public function canReturn()
    {
        return in_array($this->status, [
            self::STATUS_BORROWED,
            self::STATUS_OVERDUE
        ]);
    }

    /**
     * 检查是否可以审批
     */
    public function canApprove()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 归还设备
     */
    public function returnEquipment($remark = null)
    {
        if (!$this->canReturn()) {
            throw new \Exception('当前状态不允许归还');
        }

        $this->update([
            'status' => self::STATUS_RETURNED,
            'actual_return_date' => now()->toDateString(),
            'remark' => $remark ? $this->remark . "\n归还备注：" . $remark : $this->remark
        ]);

        return $this;
    }

    /**
     * 审批借用申请
     */
    public function approve($approverId, $remark = null)
    {
        if (!$this->canApprove()) {
            throw new \Exception('当前状态不允许审批');
        }

        $this->update([
            'status' => self::STATUS_BORROWED,
            'approver_id' => $approverId,
            'approved_at' => now(),
            'approval_remark' => $remark
        ]);

        return $this;
    }

    /**
     * 拒绝借用申请
     */
    public function reject($approverId, $remark = null)
    {
        if (!$this->canApprove()) {
            throw new \Exception('当前状态不允许审批');
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'approver_id' => $approverId,
            'approved_at' => now(),
            'approval_remark' => $remark
        ]);

        return $this;
    }

    /**
     * 作用域：借用中
     */
    public function scopeBorrowed($query)
    {
        return $query->where('status', self::STATUS_BORROWED);
    }

    /**
     * 作用域：已归还
     */
    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    /**
     * 作用域：逾期
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE)
                    ->orWhere(function($q) {
                        $q->where('status', self::STATUS_BORROWED)
                          ->where('expected_return_date', '<', now()->toDateString());
                    });
    }

    /**
     * 作用域：待审批
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：按借用人筛选
     */
    public function scopeByBorrower($query, $borrowerId)
    {
        return $query->where('borrower_id', $borrowerId);
    }

    /**
     * 作用域：按设备筛选
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('borrow_date', [$startDate, $endDate]);
    }

    /**
     * 获取借用人姓名
     */
    public function getBorrowerNameAttribute()
    {
        return $this->borrower ? $this->borrower->real_name : '';
    }

    /**
     * 获取借用人电话
     */
    public function getBorrowerPhoneAttribute()
    {
        return $this->borrower ? $this->borrower->phone : '';
    }

    /**
     * 自动更新逾期状态
     */
    public static function updateOverdueStatus()
    {
        self::where('status', self::STATUS_BORROWED)
            ->where('expected_return_date', '<', now()->toDateString())
            ->update(['status' => self::STATUS_OVERDUE]);
    }
}
