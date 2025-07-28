<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentAlert extends Model
{
    use HasFactory;

    protected $table = 'experiment_alerts';

    protected $fillable = [
        'alert_type',
        'target_type',
        'target_id',
        'target_name',
        'alert_level',
        'alert_title',
        'alert_message',
        'alert_data',
        'alert_value',
        'threshold_value',
        'is_read',
        'is_resolved',
        'resolve_note',
        'resolved_by',
        'resolved_at',
        'alert_time'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'alert_data' => 'array',
        'alert_value' => 'decimal:2',
        'threshold_value' => 'decimal:2',
        'alert_time' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * 预警类型常量
     */
    const ALERT_TYPES = [
        'overdue' => '超期未开',
        'completion_rate' => '完成率低',
        'quality_score' => '质量评分低'
    ];

    /**
     * 预警对象类型常量
     */
    const TARGET_TYPES = [
        'school' => '学校',
        'teacher' => '教师',
        'experiment' => '实验',
        'class' => '班级'
    ];

    /**
     * 预警级别常量
     */
    const ALERT_LEVELS = [
        'low' => '低级',
        'medium' => '中级',
        'high' => '高级',
        'critical' => '严重'
    ];

    /**
     * 解决人关联
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * 获取预警类型中文名称
     */
    public function getAlertTypeNameAttribute(): string
    {
        return self::ALERT_TYPES[$this->alert_type] ?? $this->alert_type;
    }

    /**
     * 获取预警对象类型中文名称
     */
    public function getTargetTypeNameAttribute(): string
    {
        return self::TARGET_TYPES[$this->target_type] ?? $this->target_type;
    }

    /**
     * 获取预警级别中文名称
     */
    public function getAlertLevelNameAttribute(): string
    {
        return self::ALERT_LEVELS[$this->alert_level] ?? $this->alert_level;
    }

    /**
     * 获取预警级别颜色
     */
    public function getAlertLevelColorAttribute(): string
    {
        $colors = [
            'low' => '#67C23A',
            'medium' => '#E6A23C',
            'high' => '#F56C6C',
            'critical' => '#F56C6C'
        ];
        
        return $colors[$this->alert_level] ?? '#909399';
    }

    /**
     * 创建预警记录
     */
    public static function createAlert(array $data): self
    {
        return self::create([
            'alert_type' => $data['alert_type'],
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'target_name' => $data['target_name'],
            'alert_level' => $data['alert_level'],
            'alert_title' => $data['alert_title'],
            'alert_message' => $data['alert_message'],
            'alert_data' => $data['alert_data'] ?? null,
            'alert_value' => $data['alert_value'] ?? null,
            'threshold_value' => $data['threshold_value'] ?? null,
            'alert_time' => $data['alert_time'] ?? now()
        ]);
    }

    /**
     * 标记为已读
     */
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * 解决预警
     */
    public function resolve(int $resolvedBy, string $resolveNote = ''): bool
    {
        return $this->update([
            'is_resolved' => true,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolve_note' => $resolveNote
        ]);
    }

    /**
     * 重新打开预警
     */
    public function reopen(): bool
    {
        return $this->update([
            'is_resolved' => false,
            'resolved_by' => null,
            'resolved_at' => null,
            'resolve_note' => null
        ]);
    }

    /**
     * 获取预警统计
     */
    public static function getAlertStatistics(array $filters = []): array
    {
        $query = self::query();

        // 应用过滤条件
        if (isset($filters['alert_type'])) {
            $query->where('alert_type', $filters['alert_type']);
        }

        if (isset($filters['target_type'])) {
            $query->where('target_type', $filters['target_type']);
        }

        if (isset($filters['alert_level'])) {
            $query->where('alert_level', $filters['alert_level']);
        }

        if (isset($filters['is_resolved'])) {
            $query->where('is_resolved', $filters['is_resolved']);
        }

        if (isset($filters['date_from'])) {
            $query->where('alert_time', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('alert_time', '<=', $filters['date_to']);
        }

        // 统计数据
        $total = $query->count();
        $unresolved = (clone $query)->where('is_resolved', false)->count();
        $resolved = (clone $query)->where('is_resolved', true)->count();
        $unread = (clone $query)->where('is_read', false)->count();

        // 按级别统计
        $levelStats = (clone $query)->selectRaw('alert_level, COUNT(*) as count')
            ->groupBy('alert_level')
            ->pluck('count', 'alert_level')
            ->toArray();

        // 按类型统计
        $typeStats = (clone $query)->selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->pluck('count', 'alert_type')
            ->toArray();

        return [
            'total' => $total,
            'unresolved' => $unresolved,
            'resolved' => $resolved,
            'unread' => $unread,
            'level_statistics' => $levelStats,
            'type_statistics' => $typeStats
        ];
    }

    /**
     * 获取最近的预警
     */
    public static function getRecentAlerts(int $limit = 10, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::query();

        // 应用过滤条件
        if (isset($filters['alert_type'])) {
            $query->where('alert_type', $filters['alert_type']);
        }

        if (isset($filters['target_type'])) {
            $query->where('target_type', $filters['target_type']);
        }

        if (isset($filters['alert_level'])) {
            $query->where('alert_level', $filters['alert_level']);
        }

        if (isset($filters['is_resolved'])) {
            $query->where('is_resolved', $filters['is_resolved']);
        }

        return $query->orderBy('alert_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 批量标记为已读
     */
    public static function markMultipleAsRead(array $alertIds): int
    {
        return self::whereIn('id', $alertIds)->update(['is_read' => true]);
    }

    /**
     * 批量解决预警
     */
    public static function resolveMultiple(array $alertIds, int $resolvedBy, string $resolveNote = ''): int
    {
        return self::whereIn('id', $alertIds)->update([
            'is_resolved' => true,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'resolve_note' => $resolveNote
        ]);
    }

    /**
     * 清理过期预警
     */
    public static function cleanupExpiredAlerts(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return self::where('alert_time', '<', $cutoffDate)
            ->where('is_resolved', true)
            ->delete();
    }

    /**
     * 检查是否存在重复预警
     */
    public static function isDuplicateAlert(string $alertType, string $targetType, int $targetId, int $withinHours = 24): bool
    {
        $cutoffTime = now()->subHours($withinHours);
        
        return self::where('alert_type', $alertType)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('alert_time', '>=', $cutoffTime)
            ->where('is_resolved', false)
            ->exists();
    }

    /**
     * 获取预警趋势数据
     */
    public static function getAlertTrend(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $alerts = self::selectRaw('DATE(alert_time) as date, alert_level, COUNT(*) as count')
            ->whereBetween('alert_time', [$startDate, $endDate])
            ->groupBy('date', 'alert_level')
            ->orderBy('date')
            ->get();

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - 1 - $i)->format('Y-m-d');
            $trend[$date] = [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'critical' => 0,
                'total' => 0
            ];
        }

        foreach ($alerts as $alert) {
            if (isset($trend[$alert->date])) {
                $trend[$alert->date][$alert->alert_level] = $alert->count;
                $trend[$alert->date]['total'] += $alert->count;
            }
        }

        return $trend;
    }
}
