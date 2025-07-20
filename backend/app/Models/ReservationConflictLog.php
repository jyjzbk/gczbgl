<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationConflictLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'conflict_type',
        'conflict_details',
        'severity',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'resolution_note'
    ];

    protected $casts = [
        'reservation_id' => 'integer',
        'conflict_details' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'resolved_by' => 'integer'
    ];

    // 冲突类型常量
    const TYPE_TIME = 'time';
    const TYPE_EQUIPMENT = 'equipment';
    const TYPE_CAPACITY = 'capacity';
    const TYPE_TEACHER = 'teacher';

    // 严重程度常量
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';

    /**
     * 关联预约记录
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(ExperimentReservation::class, 'reservation_id');
    }

    /**
     * 关联解决人
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * 获取冲突类型名称
     */
    public function getConflictTypeNameAttribute(): string
    {
        return match($this->conflict_type) {
            self::TYPE_TIME => '时间冲突',
            self::TYPE_EQUIPMENT => '设备冲突',
            self::TYPE_CAPACITY => '容量冲突',
            self::TYPE_TEACHER => '教师冲突',
            default => '未知冲突'
        };
    }

    /**
     * 获取严重程度名称
     */
    public function getSeverityNameAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => '低',
            self::SEVERITY_MEDIUM => '中',
            self::SEVERITY_HIGH => '高',
            default => '中'
        };
    }

    /**
     * 获取严重程度颜色
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'info',
            self::SEVERITY_MEDIUM => 'warning',
            self::SEVERITY_HIGH => 'danger',
            default => 'warning'
        };
    }

    /**
     * 解决冲突
     */
    public function resolve($resolvedBy, $note = null): bool
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->resolved_by = $resolvedBy;
        $this->resolution_note = $note;
        
        return $this->save();
    }

    /**
     * 作用域：按预约筛选
     */
    public function scopeByReservation($query, $reservationId)
    {
        return $query->where('reservation_id', $reservationId);
    }

    /**
     * 作用域：按冲突类型筛选
     */
    public function scopeByType($query, $type)
    {
        return $query->where('conflict_type', $type);
    }

    /**
     * 作用域：按严重程度筛选
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * 作用域：未解决的冲突
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * 作用域：已解决的冲突
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    /**
     * 创建时间冲突日志
     */
    public static function createTimeConflict($reservationId, $conflictDetails, $severity = self::SEVERITY_MEDIUM)
    {
        return self::create([
            'reservation_id' => $reservationId,
            'conflict_type' => self::TYPE_TIME,
            'conflict_details' => $conflictDetails,
            'severity' => $severity
        ]);
    }

    /**
     * 创建设备冲突日志
     */
    public static function createEquipmentConflict($reservationId, $conflictDetails, $severity = self::SEVERITY_MEDIUM)
    {
        return self::create([
            'reservation_id' => $reservationId,
            'conflict_type' => self::TYPE_EQUIPMENT,
            'conflict_details' => $conflictDetails,
            'severity' => $severity
        ]);
    }

    /**
     * 创建容量冲突日志
     */
    public static function createCapacityConflict($reservationId, $conflictDetails, $severity = self::SEVERITY_HIGH)
    {
        return self::create([
            'reservation_id' => $reservationId,
            'conflict_type' => self::TYPE_CAPACITY,
            'conflict_details' => $conflictDetails,
            'severity' => $severity
        ]);
    }

    /**
     * 创建教师冲突日志
     */
    public static function createTeacherConflict($reservationId, $conflictDetails, $severity = self::SEVERITY_HIGH)
    {
        return self::create([
            'reservation_id' => $reservationId,
            'conflict_type' => self::TYPE_TEACHER,
            'conflict_details' => $conflictDetails,
            'severity' => $severity
        ]);
    }
}
