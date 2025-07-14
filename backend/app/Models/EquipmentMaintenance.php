<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EquipmentMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'reporter_id',
        'fault_description',
        'fault_type',
        'urgency_level',
        'photos',
        'report_date',
        'maintainer_id',
        'start_date',
        'complete_date',
        'cost',
        'solution',
        'parts_replaced',
        'status',
        'quality_rating',
        'remark'
    ];

    protected $casts = [
        'equipment_id' => 'integer',
        'reporter_id' => 'integer',
        'urgency_level' => 'integer',
        'photos' => 'array',
        'report_date' => 'date',
        'maintainer_id' => 'integer',
        'start_date' => 'date',
        'complete_date' => 'date',
        'cost' => 'decimal:2',
        'status' => 'integer',
        'quality_rating' => 'integer'
    ];

    protected $appends = [
        'status_text',
        'status_color',
        'urgency_text',
        'urgency_color'
    ];

    // 状态常量
    const STATUS_PENDING = 1;    // 待维修
    const STATUS_PROCESSING = 2; // 维修中
    const STATUS_COMPLETED = 3;  // 已完成
    const STATUS_UNREPAIRABLE = 4; // 无法修复

    // 紧急程度常量
    const URGENCY_LOW = 1;    // 低
    const URGENCY_MEDIUM = 2; // 中
    const URGENCY_HIGH = 3;   // 高

    /**
     * 获取设备信息
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * 获取报修人信息
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * 获取维修人信息
     */
    public function maintainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maintainer_id');
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => '待维修',
            self::STATUS_PROCESSING => '维修中',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_UNREPAIRABLE => '无法修复'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'orange',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_UNREPAIRABLE => 'red'
        ];
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * 获取紧急程度文本
     */
    public function getUrgencyTextAttribute()
    {
        $urgencies = [
            self::URGENCY_LOW => '低',
            self::URGENCY_MEDIUM => '中',
            self::URGENCY_HIGH => '高'
        ];
        return $urgencies[$this->urgency_level] ?? '未知';
    }

    /**
     * 获取紧急程度颜色
     */
    public function getUrgencyColorAttribute()
    {
        $colors = [
            self::URGENCY_LOW => 'green',
            self::URGENCY_MEDIUM => 'orange',
            self::URGENCY_HIGH => 'red'
        ];
        return $colors[$this->urgency_level] ?? 'gray';
    }

    /**
     * 获取维修天数
     */
    public function getMaintenanceDaysAttribute()
    {
        if (!$this->start_date) {
            return 0;
        }

        $endDate = $this->complete_date ?: now();
        return Carbon::parse($this->start_date)->diffInDays($endDate);
    }

    /**
     * 获取等待天数
     */
    public function getWaitingDaysAttribute()
    {
        $startDate = $this->start_date ?: now();
        return Carbon::parse($this->report_date)->diffInDays($startDate);
    }

    /**
     * 检查是否可以开始维修
     */
    public function canStart()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 检查是否可以完成维修
     */
    public function canComplete()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * 检查是否可以分配维修人
     */
    public function canAssign()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING
        ]);
    }

    /**
     * 开始维修
     */
    public function startMaintenance($maintainerId = null)
    {
        if (!$this->canStart()) {
            throw new \Exception('当前状态不允许开始维修');
        }

        $updateData = [
            'status' => self::STATUS_PROCESSING,
            'start_date' => now()->toDateString()
        ];

        if ($maintainerId) {
            $updateData['maintainer_id'] = $maintainerId;
        }

        $this->update($updateData);

        // 更新设备状态为维修中
        $this->equipment->update(['status' => Equipment::STATUS_MAINTENANCE]);

        return $this;
    }

    /**
     * 完成维修
     */
    public function completeMaintenance($solution, $cost = null, $partsReplaced = null, $qualityRating = null)
    {
        if (!$this->canComplete()) {
            throw new \Exception('当前状态不允许完成维修');
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'complete_date' => now()->toDateString(),
            'solution' => $solution,
            'cost' => $cost,
            'parts_replaced' => $partsReplaced,
            'quality_rating' => $qualityRating
        ]);

        // 更新设备状态为正常
        $this->equipment->update(['status' => Equipment::STATUS_NORMAL]);

        return $this;
    }

    /**
     * 标记为无法修复
     */
    public function markUnrepairable($reason)
    {
        if (!$this->canComplete()) {
            throw new \Exception('当前状态不允许标记为无法修复');
        }

        $this->update([
            'status' => self::STATUS_UNREPAIRABLE,
            'complete_date' => now()->toDateString(),
            'solution' => $reason
        ]);

        // 可能需要将设备状态设为报废
        // $this->equipment->update(['status' => Equipment::STATUS_SCRAPPED]);

        return $this;
    }

    /**
     * 分配维修人
     */
    public function assignMaintainer($maintainerId)
    {
        if (!$this->canAssign()) {
            throw new \Exception('当前状态不允许分配维修人');
        }

        $this->update(['maintainer_id' => $maintainerId]);

        return $this;
    }

    /**
     * 作用域：待维修
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：维修中
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * 作用域：已完成
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * 作用域：按紧急程度筛选
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    /**
     * 作用域：按报修人筛选
     */
    public function scopeByReporter($query, $reporterId)
    {
        return $query->where('reporter_id', $reporterId);
    }

    /**
     * 作用域：按维修人筛选
     */
    public function scopeByMaintainer($query, $maintainerId)
    {
        return $query->where('maintainer_id', $maintainerId);
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
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    /**
     * 作用域：高优先级（紧急程度高且等待时间长）
     */
    public function scopeHighPriority($query)
    {
        return $query->where('urgency_level', self::URGENCY_HIGH)
                    ->where('status', self::STATUS_PENDING)
                    ->orderBy('report_date', 'asc');
    }
}
