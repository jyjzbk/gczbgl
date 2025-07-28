<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExperimentMonitoringStatistics extends Model
{
    use HasFactory;

    protected $table = 'experiment_monitoring_statistics';

    protected $fillable = [
        'target_type',
        'target_id',
        'target_name',
        'semester',
        'statistics_date',
        'total_planned_experiments',
        'completed_experiments',
        'overdue_experiments',
        'pending_experiments',
        'completion_rate',
        'overdue_rate',
        'quality_score',
        'avg_completion_days',
        'max_overdue_days',
        'subject_statistics',
        'grade_statistics',
        'monthly_statistics',
        'calculated_at'
    ];

    protected $casts = [
        'statistics_date' => 'date',
        'completion_rate' => 'decimal:2',
        'overdue_rate' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'avg_completion_days' => 'decimal:2',
        'total_planned_experiments' => 'integer',
        'completed_experiments' => 'integer',
        'overdue_experiments' => 'integer',
        'pending_experiments' => 'integer',
        'max_overdue_days' => 'integer',
        'subject_statistics' => 'array',
        'grade_statistics' => 'array',
        'monthly_statistics' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * 统计对象类型常量
     */
    const TARGET_TYPES = [
        'school' => '学校',
        'teacher' => '教师',
        'subject' => '学科',
        'grade' => '年级'
    ];

    /**
     * 获取统计对象类型中文名称
     */
    public function getTargetTypeNameAttribute(): string
    {
        return self::TARGET_TYPES[$this->target_type] ?? $this->target_type;
    }

    /**
     * 计算学校实验统计
     */
    public static function calculateSchoolStatistics(int $schoolId, string $semester): self
    {
        $school = School::find($schoolId);
        if (!$school) {
            throw new \Exception('学校不存在');
        }

        // 获取学校的所有实验记录
        $experiments = ExperimentWork::where('school_id', $schoolId)
            ->where('semester', $semester)
            ->get();

        // 计算统计数据
        $totalPlanned = $experiments->count();
        $completed = $experiments->where('status', 'completed')->count();
        $overdue = $experiments->where('is_overdue', true)->count();
        $pending = $experiments->where('status', 'pending')->count();

        $completionRate = $totalPlanned > 0 ? ($completed / $totalPlanned) * 100 : 0;
        $overdueRate = $totalPlanned > 0 ? ($overdue / $totalPlanned) * 100 : 0;

        // 计算质量评分
        $qualityScore = $experiments->where('status', 'completed')->avg('quality_score') ?? 0;

        // 计算平均完成天数
        $completedExperiments = $experiments->where('status', 'completed');
        $avgCompletionDays = 0;
        if ($completedExperiments->count() > 0) {
            $totalDays = $completedExperiments->sum(function ($exp) {
                return $exp->completed_at ? $exp->completed_at->diffInDays($exp->planned_date) : 0;
            });
            $avgCompletionDays = $totalDays / $completedExperiments->count();
        }

        // 计算最大超期天数
        $maxOverdueDays = 0;
        $overdueExperiments = $experiments->where('is_overdue', true);
        if ($overdueExperiments->count() > 0) {
            $maxOverdueDays = $overdueExperiments->max(function ($exp) {
                return $exp->planned_date ? now()->diffInDays($exp->planned_date) : 0;
            });
        }

        // 按学科统计
        $subjectStats = $experiments->groupBy('subject_id')->map(function ($subjectExps) {
            $total = $subjectExps->count();
            $completed = $subjectExps->where('status', 'completed')->count();
            return [
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0
            ];
        })->toArray();

        // 按年级统计
        $gradeStats = $experiments->groupBy('grade')->map(function ($gradeExps) {
            $total = $gradeExps->count();
            $completed = $gradeExps->where('status', 'completed')->count();
            return [
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0
            ];
        })->toArray();

        // 按月统计
        $monthlyStats = $experiments->groupBy(function ($exp) {
            return $exp->planned_date ? $exp->planned_date->format('Y-m') : 'unknown';
        })->map(function ($monthExps) {
            $total = $monthExps->count();
            $completed = $monthExps->where('status', 'completed')->count();
            return [
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0
            ];
        })->toArray();

        // 创建或更新统计记录
        return self::updateOrCreate(
            [
                'target_type' => 'school',
                'target_id' => $schoolId,
                'semester' => $semester,
                'statistics_date' => now()->toDateString()
            ],
            [
                'target_name' => $school->name,
                'total_planned_experiments' => $totalPlanned,
                'completed_experiments' => $completed,
                'overdue_experiments' => $overdue,
                'pending_experiments' => $pending,
                'completion_rate' => $completionRate,
                'overdue_rate' => $overdueRate,
                'quality_score' => $qualityScore,
                'avg_completion_days' => $avgCompletionDays,
                'max_overdue_days' => $maxOverdueDays,
                'subject_statistics' => $subjectStats,
                'grade_statistics' => $gradeStats,
                'monthly_statistics' => $monthlyStats,
                'calculated_at' => now()
            ]
        );
    }

    /**
     * 计算教师实验统计
     */
    public static function calculateTeacherStatistics(int $teacherId, string $semester): self
    {
        $teacher = User::find($teacherId);
        if (!$teacher) {
            throw new \Exception('教师不存在');
        }

        // 获取教师的所有实验记录
        $experiments = ExperimentWork::where('teacher_id', $teacherId)
            ->where('semester', $semester)
            ->get();

        // 计算统计数据（类似学校统计）
        $totalPlanned = $experiments->count();
        $completed = $experiments->where('status', 'completed')->count();
        $overdue = $experiments->where('is_overdue', true)->count();
        $pending = $experiments->where('status', 'pending')->count();

        $completionRate = $totalPlanned > 0 ? ($completed / $totalPlanned) * 100 : 0;
        $overdueRate = $totalPlanned > 0 ? ($overdue / $totalPlanned) * 100 : 0;
        $qualityScore = $experiments->where('status', 'completed')->avg('quality_score') ?? 0;

        // 计算平均完成天数
        $completedExperiments = $experiments->where('status', 'completed');
        $avgCompletionDays = 0;
        if ($completedExperiments->count() > 0) {
            $totalDays = $completedExperiments->sum(function ($exp) {
                return $exp->completed_at ? $exp->completed_at->diffInDays($exp->planned_date) : 0;
            });
            $avgCompletionDays = $totalDays / $completedExperiments->count();
        }

        // 计算最大超期天数
        $maxOverdueDays = 0;
        $overdueExperiments = $experiments->where('is_overdue', true);
        if ($overdueExperiments->count() > 0) {
            $maxOverdueDays = $overdueExperiments->max(function ($exp) {
                return $exp->planned_date ? now()->diffInDays($exp->planned_date) : 0;
            });
        }

        return self::updateOrCreate(
            [
                'target_type' => 'teacher',
                'target_id' => $teacherId,
                'semester' => $semester,
                'statistics_date' => now()->toDateString()
            ],
            [
                'target_name' => $teacher->name,
                'total_planned_experiments' => $totalPlanned,
                'completed_experiments' => $completed,
                'overdue_experiments' => $overdue,
                'pending_experiments' => $pending,
                'completion_rate' => $completionRate,
                'overdue_rate' => $overdueRate,
                'quality_score' => $qualityScore,
                'avg_completion_days' => $avgCompletionDays,
                'max_overdue_days' => $maxOverdueDays,
                'calculated_at' => now()
            ]
        );
    }

    /**
     * 获取区域统计汇总
     */
    public static function getRegionSummary(string $organizationType, int $organizationId, string $semester): array
    {
        // 根据组织类型获取下级学校
        $schools = School::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->get();

        $summary = [
            'total_schools' => $schools->count(),
            'total_planned_experiments' => 0,
            'total_completed_experiments' => 0,
            'total_overdue_experiments' => 0,
            'avg_completion_rate' => 0,
            'avg_overdue_rate' => 0,
            'avg_quality_score' => 0,
            'schools_with_alerts' => 0,
            'school_statistics' => []
        ];

        $totalCompletionRate = 0;
        $totalOverdueRate = 0;
        $totalQualityScore = 0;
        $schoolsWithData = 0;

        foreach ($schools as $school) {
            $stats = self::where('target_type', 'school')
                ->where('target_id', $school->id)
                ->where('semester', $semester)
                ->latest('statistics_date')
                ->first();

            if ($stats) {
                $summary['total_planned_experiments'] += $stats->total_planned_experiments;
                $summary['total_completed_experiments'] += $stats->completed_experiments;
                $summary['total_overdue_experiments'] += $stats->overdue_experiments;
                
                $totalCompletionRate += $stats->completion_rate;
                $totalOverdueRate += $stats->overdue_rate;
                $totalQualityScore += $stats->quality_score;
                $schoolsWithData++;

                // 检查是否有预警
                $hasAlerts = ExperimentAlert::where('target_type', 'school')
                    ->where('target_id', $school->id)
                    ->where('is_resolved', false)
                    ->exists();

                if ($hasAlerts) {
                    $summary['schools_with_alerts']++;
                }

                $summary['school_statistics'][] = [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'completion_rate' => $stats->completion_rate,
                    'overdue_rate' => $stats->overdue_rate,
                    'quality_score' => $stats->quality_score,
                    'has_alerts' => $hasAlerts
                ];
            }
        }

        // 计算平均值
        if ($schoolsWithData > 0) {
            $summary['avg_completion_rate'] = $totalCompletionRate / $schoolsWithData;
            $summary['avg_overdue_rate'] = $totalOverdueRate / $schoolsWithData;
            $summary['avg_quality_score'] = $totalQualityScore / $schoolsWithData;
        }

        return $summary;
    }

    /**
     * 获取趋势数据
     */
    public static function getTrendData(string $targetType, int $targetId, string $semester, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $statistics = self::where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('semester', $semester)
            ->whereBetween('statistics_date', [$startDate, $endDate])
            ->orderBy('statistics_date')
            ->get();

        $trend = [];
        foreach ($statistics as $stat) {
            $trend[] = [
                'date' => $stat->statistics_date->format('Y-m-d'),
                'completion_rate' => $stat->completion_rate,
                'overdue_rate' => $stat->overdue_rate,
                'quality_score' => $stat->quality_score,
                'total_experiments' => $stat->total_planned_experiments,
                'completed_experiments' => $stat->completed_experiments
            ];
        }

        return $trend;
    }

    /**
     * 批量计算统计数据
     */
    public static function batchCalculateStatistics(string $semester): int
    {
        $calculated = 0;

        // 计算所有学校的统计
        $schools = School::all();
        foreach ($schools as $school) {
            try {
                self::calculateSchoolStatistics($school->id, $semester);
                $calculated++;
            } catch (\Exception $e) {
                \Log::error("计算学校 {$school->id} 统计失败: " . $e->getMessage());
            }
        }

        // 计算所有教师的统计
        $teachers = User::where('role', 'teacher')->get();
        foreach ($teachers as $teacher) {
            try {
                self::calculateTeacherStatistics($teacher->id, $semester);
                $calculated++;
            } catch (\Exception $e) {
                \Log::error("计算教师 {$teacher->id} 统计失败: " . $e->getMessage());
            }
        }

        return $calculated;
    }
}
