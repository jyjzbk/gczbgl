<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'type',
        'type_id',
        'location',
        'area',
        'capacity',
        'manager_id',
        'equipment_list',
        'safety_rules',
        'status'
    ];

    protected $casts = [
        'school_id' => 'integer',
        'type' => 'integer',
        'type_id' => 'integer',
        'area' => 'decimal:2',
        'capacity' => 'integer',
        'manager_id' => 'integer',
        'status' => 'integer'
    ];

    // 实验室类型常量
    const TYPE_PHYSICS = 1;     // 物理实验室
    const TYPE_CHEMISTRY = 2;   // 化学实验室
    const TYPE_BIOLOGY = 3;     // 生物实验室
    const TYPE_COMPREHENSIVE = 4; // 综合实验室

    // 状态常量
    const STATUS_NORMAL = 1;      // 正常
    const STATUS_MAINTENANCE = 2; // 维修中
    const STATUS_DISABLED = 0;    // 停用

    /**
     * 获取实验室类型文本
     */
    public function getTypeTextAttribute()
    {
        // 优先使用关联的实验室类型
        if ($this->laboratoryType) {
            return $this->laboratoryType->name;
        }

        // 兼容旧数据，使用硬编码类型
        $types = [
            self::TYPE_PHYSICS => '物理实验室',
            self::TYPE_CHEMISTRY => '化学实验室',
            self::TYPE_BIOLOGY => '生物实验室',
            self::TYPE_COMPREHENSIVE => '综合实验室'
        ];
        return $types[$this->type] ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_NORMAL => '正常',
            self::STATUS_MAINTENANCE => '维修中',
            self::STATUS_DISABLED => '停用'
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 关联学校
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * 关联管理员
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * 关联实验室类型
     */
    public function laboratoryType()
    {
        return $this->belongsTo(LaboratoryType::class, 'type_id');
    }

    /**
     * 关联设备
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * 关联实验预约
     */
    public function reservations()
    {
        return $this->hasMany(ExperimentReservation::class);
    }

    /**
     * 关联实验记录
     */
    public function records()
    {
        return $this->hasMany(ExperimentRecord::class);
    }

    /**
     * 作用域：按学校筛选
     */
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：正常状态
     */
    public function scopeNormal($query)
    {
        return $query->where('status', self::STATUS_NORMAL);
    }

    /**
     * 作用域：可用状态（正常或维修中）
     */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', [self::STATUS_NORMAL, self::STATUS_MAINTENANCE]);
    }

    /**
     * 检查指定时间段是否可用
     */
    public function isAvailableAt($date, $startTime, $endTime, $excludeReservationId = null)
    {
        if ($this->status === self::STATUS_DISABLED) {
            return false;
        }

        $query = $this->reservations()
            ->where('reservation_date', $date)
            ->where('status', '!=', 5) // 排除已取消的预约
            ->where(function($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function($subQ) use ($startTime, $endTime) {
                      $subQ->where('start_time', '<=', $startTime)
                           ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return !$query->exists();
    }

    /**
     * 获取指定日期的预约情况
     */
    public function getReservationsForDate($date)
    {
        return $this->reservations()
            ->with(['catalog', 'teacher'])
            ->where('reservation_date', $date)
            ->where('status', '!=', 5) // 排除已取消的预约
            ->orderBy('start_time')
            ->get();
    }
}
