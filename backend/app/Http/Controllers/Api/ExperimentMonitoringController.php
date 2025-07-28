<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentAlert;
use App\Models\ExperimentAlertConfig;
use App\Models\ExperimentMonitoringStatistics;
use App\Models\ExperimentWork;
use App\Models\School;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExperimentMonitoringController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取监控仪表板数据
     */
    public function getDashboard(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $semester = $request->get('semester', $this->getCurrentSemester());

            // 检查权限
            if (!$this->canViewMonitoring($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问监控数据'
                ], 403);
            }

            // 获取区域汇总数据
            $regionSummary = $this->getRegionSummaryForUser($user, $semester);

            // 获取预警统计
            $alertStats = $this->getAlertStatisticsForUser($user);

            // 获取最近预警
            $recentAlerts = $this->getRecentAlertsForUser($user, 10);

            // 获取趋势数据
            $trendData = $this->getTrendDataForUser($user, $semester);

            return response()->json([
                'success' => true,
                'data' => [
                    'region_summary' => $regionSummary,
                    'alert_statistics' => $alertStats,
                    'recent_alerts' => $recentAlerts,
                    'trend_data' => $trendData,
                    'semester' => $semester
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取监控数据失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取预警列表
     */
    public function getAlerts(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->canViewMonitoring($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问预警数据'
                ], 403);
            }

            $query = ExperimentAlert::with(['resolvedBy:id,name']);

            // 根据用户权限过滤数据
            $this->applyAlertDataScopeFilter($query, $user);

            // 筛选条件
            if ($request->filled('alert_type')) {
                $query->where('alert_type', $request->alert_type);
            }

            if ($request->filled('alert_level')) {
                $query->where('alert_level', $request->alert_level);
            }

            if ($request->filled('target_type')) {
                $query->where('target_type', $request->target_type);
            }

            if ($request->filled('is_resolved')) {
                $query->where('is_resolved', $request->boolean('is_resolved'));
            }

            if ($request->filled('is_read')) {
                $query->where('is_read', $request->boolean('is_read'));
            }

            if ($request->filled('date_from')) {
                $query->where('alert_time', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('alert_time', '<=', $request->date_to);
            }

            $alerts = $query->orderBy('alert_time', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取预警列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 标记预警为已读
     */
    public function markAlertAsRead(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'alert_ids' => 'required|array',
                'alert_ids.*' => 'integer|exists:experiment_alerts,id'
            ]);

            // 检查权限
            if (!$this->canManageAlerts($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理预警'
                ], 403);
            }

            $updated = ExperimentAlert::markMultipleAsRead($validated['alert_ids']);

            return response()->json([
                'success' => true,
                'message' => "成功标记 {$updated} 条预警为已读"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '标记预警失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 解决预警
     */
    public function resolveAlert(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'alert_ids' => 'required|array',
                'alert_ids.*' => 'integer|exists:experiment_alerts,id',
                'resolve_note' => 'nullable|string|max:500'
            ]);

            // 检查权限
            if (!$this->canManageAlerts($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理预警'
                ], 403);
            }

            DB::beginTransaction();

            $updated = ExperimentAlert::resolveMultiple(
                $validated['alert_ids'],
                $user->id,
                $validated['resolve_note'] ?? ''
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "成功解决 {$updated} 条预警"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '解决预警失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取学校监控详情
     */
    public function getSchoolMonitoring(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
                'semester' => 'nullable|string'
            ]);

            $schoolId = $validated['school_id'];
            $semester = $validated['semester'] ?? $this->getCurrentSemester();

            // 检查权限
            if (!$this->canViewSchoolMonitoring($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校监控数据'
                ], 403);
            }

            // 获取学校基本信息
            $school = School::find($schoolId);

            // 获取统计数据
            $statistics = ExperimentMonitoringStatistics::where('target_type', 'school')
                ->where('target_id', $schoolId)
                ->where('semester', $semester)
                ->latest('statistics_date')
                ->first();

            // 获取学校预警
            $alerts = ExperimentAlert::where('target_type', 'school')
                ->where('target_id', $schoolId)
                ->where('is_resolved', false)
                ->orderBy('alert_time', 'desc')
                ->get();

            // 获取趋势数据
            $trendData = ExperimentMonitoringStatistics::getTrendData('school', $schoolId, $semester);

            // 获取超期实验列表
            $overdueExperiments = ExperimentWork::where('school_id', $schoolId)
                ->where('semester', $semester)
                ->where('is_overdue', true)
                ->with(['catalog:id,name,code', 'teacher:id,name'])
                ->orderBy('planned_date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'statistics' => $statistics,
                    'alerts' => $alerts,
                    'trend_data' => $trendData,
                    'overdue_experiments' => $overdueExperiments,
                    'semester' => $semester
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取学校监控数据失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 手动触发预警检查
     */
    public function triggerAlertCheck(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->canManageAlerts($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限触发预警检查'
                ], 403);
            }

            $validated = $request->validate([
                'semester' => 'nullable|string',
                'organization_type' => ['nullable', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'nullable|integer'
            ]);

            $semester = $validated['semester'] ?? $this->getCurrentSemester();
            $organizationType = $validated['organization_type'] ?? $user->organization_type;
            $organizationId = $validated['organization_id'] ?? $user->organization_id;

            // 触发预警检查
            $alertsCreated = $this->performAlertCheck($organizationType, $organizationId, $semester);

            return response()->json([
                'success' => true,
                'message' => "预警检查完成，创建了 {$alertsCreated} 条新预警",
                'alerts_created' => $alertsCreated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '触发预警检查失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取预警统计
     */
    public function getAlertStatistics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->canViewMonitoring($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问预警统计'
                ], 403);
            }

            $filters = [];

            // 根据用户权限设置过滤条件
            if ($user->organization_level <= 3) {
                // 省、市、区县级用户可以查看辖区内的预警
                $filters['organization_type'] = $user->organization_type;
                $filters['organization_id'] = $user->organization_id;
            }

            if ($request->filled('date_from')) {
                $filters['date_from'] = $request->date_from;
            }

            if ($request->filled('date_to')) {
                $filters['date_to'] = $request->date_to;
            }

            $statistics = ExperimentAlert::getAlertStatistics($filters);

            // 获取预警趋势
            $trendData = ExperimentAlert::getAlertTrend($request->get('trend_days', 30));

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'trend_data' => $trendData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取预警统计失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取当前学期
     */
    private function getCurrentSemester(): string
    {
        $now = now();
        $year = $now->year;

        // 简单的学期判断逻辑：9月-1月为上学期，2月-8月为下学期
        if ($now->month >= 9 || $now->month <= 1) {
            return $year . '-' . ($year + 1) . '-1'; // 上学期
        } else {
            return ($year - 1) . '-' . $year . '-2'; // 下学期
        }
    }

    /**
     * 获取用户的区域汇总数据
     */
    private function getRegionSummaryForUser($user, string $semester): array
    {
        if ($user->organization_level <= 3) {
            return ExperimentMonitoringStatistics::getRegionSummary(
                $user->organization_type,
                $user->organization_id,
                $semester
            );
        }

        // 学校用户只能看自己学校的数据
        if ($user->organization_type === 'school') {
            $stats = ExperimentMonitoringStatistics::where('target_type', 'school')
                ->where('target_id', $user->organization_id)
                ->where('semester', $semester)
                ->latest('statistics_date')
                ->first();

            return [
                'total_schools' => 1,
                'total_planned_experiments' => $stats->total_planned_experiments ?? 0,
                'total_completed_experiments' => $stats->completed_experiments ?? 0,
                'total_overdue_experiments' => $stats->overdue_experiments ?? 0,
                'avg_completion_rate' => $stats->completion_rate ?? 0,
                'avg_overdue_rate' => $stats->overdue_rate ?? 0,
                'avg_quality_score' => $stats->quality_score ?? 0,
                'schools_with_alerts' => 0,
                'school_statistics' => []
            ];
        }

        return [];
    }

    /**
     * 获取用户的预警统计
     */
    private function getAlertStatisticsForUser($user): array
    {
        $filters = [];

        if ($user->organization_level <= 3) {
            // 根据用户权限获取辖区内的预警
            $schools = School::where('organization_type', $user->organization_type)
                ->where('organization_id', $user->organization_id)
                ->pluck('id');

            if ($schools->isNotEmpty()) {
                $filters['target_ids'] = $schools->toArray();
                $filters['target_type'] = 'school';
            }
        } elseif ($user->organization_type === 'school') {
            $filters['target_type'] = 'school';
            $filters['target_ids'] = [$user->organization_id];
        }

        return ExperimentAlert::getAlertStatistics($filters);
    }

    /**
     * 获取用户的最近预警
     */
    private function getRecentAlertsForUser($user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $filters = [];

        if ($user->organization_level <= 3) {
            // 根据用户权限获取辖区内的预警
            $schools = School::where('organization_type', $user->organization_type)
                ->where('organization_id', $user->organization_id)
                ->pluck('id');

            if ($schools->isNotEmpty()) {
                $filters['target_ids'] = $schools->toArray();
                $filters['target_type'] = 'school';
            }
        } elseif ($user->organization_type === 'school') {
            $filters['target_type'] = 'school';
            $filters['target_ids'] = [$user->organization_id];
        }

        return ExperimentAlert::getRecentAlerts($limit, $filters);
    }

    /**
     * 获取用户的趋势数据
     */
    private function getTrendDataForUser($user, string $semester): array
    {
        if ($user->organization_type === 'school') {
            return ExperimentMonitoringStatistics::getTrendData('school', $user->organization_id, $semester);
        }

        // 对于上级管理员，返回辖区内学校的平均趋势
        $schools = School::where('organization_type', $user->organization_type)
            ->where('organization_id', $user->organization_id)
            ->pluck('id');

        $trendData = [];
        foreach ($schools as $schoolId) {
            $schoolTrend = ExperimentMonitoringStatistics::getTrendData('school', $schoolId, $semester);
            // 合并趋势数据
            foreach ($schoolTrend as $data) {
                $date = $data['date'];
                if (!isset($trendData[$date])) {
                    $trendData[$date] = [
                        'date' => $date,
                        'completion_rate' => 0,
                        'overdue_rate' => 0,
                        'quality_score' => 0,
                        'total_experiments' => 0,
                        'completed_experiments' => 0,
                        'school_count' => 0
                    ];
                }

                $trendData[$date]['completion_rate'] += $data['completion_rate'];
                $trendData[$date]['overdue_rate'] += $data['overdue_rate'];
                $trendData[$date]['quality_score'] += $data['quality_score'];
                $trendData[$date]['total_experiments'] += $data['total_experiments'];
                $trendData[$date]['completed_experiments'] += $data['completed_experiments'];
                $trendData[$date]['school_count']++;
            }
        }

        // 计算平均值
        foreach ($trendData as &$data) {
            if ($data['school_count'] > 0) {
                $data['completion_rate'] /= $data['school_count'];
                $data['overdue_rate'] /= $data['school_count'];
                $data['quality_score'] /= $data['school_count'];
            }
            unset($data['school_count']);
        }

        return array_values($trendData);
    }

    /**
     * 执行预警检查
     */
    private function performAlertCheck(string $organizationType, int $organizationId, string $semester): int
    {
        $alertsCreated = 0;

        // 获取辖区内的学校
        $schools = School::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->get();

        foreach ($schools as $school) {
            // 检查超期实验
            $alertsCreated += $this->checkOverdueExperiments($school, $semester);

            // 检查完成率
            $alertsCreated += $this->checkCompletionRate($school, $semester);

            // 检查质量评分
            $alertsCreated += $this->checkQualityScore($school, $semester);
        }

        return $alertsCreated;
    }

    /**
     * 检查超期实验
     */
    private function checkOverdueExperiments(School $school, string $semester): int
    {
        $alertsCreated = 0;

        // 获取超期实验
        $overdueExperiments = ExperimentWork::where('school_id', $school->id)
            ->where('semester', $semester)
            ->where('is_overdue', true)
            ->where('status', '!=', 'completed')
            ->get();

        foreach ($overdueExperiments as $experiment) {
            // 检查是否已有预警
            if (ExperimentAlert::isDuplicateAlert('overdue', 'experiment', $experiment->id)) {
                continue;
            }

            $daysOverdue = $experiment->planned_date ? now()->diffInDays($experiment->planned_date) : 0;

            $alertLevel = 'medium';
            if ($daysOverdue > 7) {
                $alertLevel = 'critical';
            } elseif ($daysOverdue > 3) {
                $alertLevel = 'high';
            }

            ExperimentAlert::createAlert([
                'alert_type' => 'overdue',
                'target_type' => 'experiment',
                'target_id' => $experiment->id,
                'target_name' => $experiment->catalog->name ?? '未知实验',
                'alert_level' => $alertLevel,
                'alert_title' => '实验超期未开',
                'alert_message' => "实验「{$experiment->catalog->name}」已超期 {$daysOverdue} 天未开课",
                'alert_data' => [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'teacher_id' => $experiment->teacher_id,
                    'planned_date' => $experiment->planned_date,
                    'days_overdue' => $daysOverdue
                ],
                'alert_value' => $daysOverdue,
                'threshold_value' => 0
            ]);

            $alertsCreated++;
        }

        return $alertsCreated;
    }

    /**
     * 检查完成率
     */
    private function checkCompletionRate(School $school, string $semester): int
    {
        $alertsCreated = 0;

        // 获取学校统计数据
        $stats = ExperimentMonitoringStatistics::where('target_type', 'school')
            ->where('target_id', $school->id)
            ->where('semester', $semester)
            ->latest('statistics_date')
            ->first();

        if (!$stats) {
            return 0;
        }

        // 获取预警配置
        $config = ExperimentAlertConfig::getEffectiveConfig('school', $school->id, 'completion_rate');
        if (!$config) {
            $defaultConfig = ExperimentAlertConfig::getDefaultConfig('completion_rate');
            $threshold = $defaultConfig['threshold_value'];
        } else {
            $threshold = $config->threshold_value;
        }

        // 检查是否需要预警
        if ($stats->completion_rate < $threshold) {
            // 检查是否已有预警
            if (!ExperimentAlert::isDuplicateAlert('completion_rate', 'school', $school->id)) {
                $alertLevel = 'medium';
                if ($stats->completion_rate < $threshold * 0.5) {
                    $alertLevel = 'critical';
                } elseif ($stats->completion_rate < $threshold * 0.7) {
                    $alertLevel = 'high';
                }

                ExperimentAlert::createAlert([
                    'alert_type' => 'completion_rate',
                    'target_type' => 'school',
                    'target_id' => $school->id,
                    'target_name' => $school->name,
                    'alert_level' => $alertLevel,
                    'alert_title' => '实验完成率过低',
                    'alert_message' => "学校「{$school->name}」实验完成率为 {$stats->completion_rate}%，低于预警阈值 {$threshold}%",
                    'alert_data' => [
                        'semester' => $semester,
                        'total_experiments' => $stats->total_planned_experiments,
                        'completed_experiments' => $stats->completed_experiments,
                        'overdue_experiments' => $stats->overdue_experiments
                    ],
                    'alert_value' => $stats->completion_rate,
                    'threshold_value' => $threshold
                ]);

                $alertsCreated++;
            }
        }

        return $alertsCreated;
    }

    /**
     * 检查质量评分
     */
    private function checkQualityScore(School $school, string $semester): int
    {
        $alertsCreated = 0;

        // 获取学校统计数据
        $stats = ExperimentMonitoringStatistics::where('target_type', 'school')
            ->where('target_id', $school->id)
            ->where('semester', $semester)
            ->latest('statistics_date')
            ->first();

        if (!$stats || $stats->quality_score <= 0) {
            return 0;
        }

        // 获取预警配置
        $config = ExperimentAlertConfig::getEffectiveConfig('school', $school->id, 'quality_score');
        if (!$config) {
            $defaultConfig = ExperimentAlertConfig::getDefaultConfig('quality_score');
            $threshold = $defaultConfig['threshold_value'];
        } else {
            $threshold = $config->threshold_value;
        }

        // 检查是否需要预警
        if ($stats->quality_score < $threshold) {
            // 检查是否已有预警
            if (!ExperimentAlert::isDuplicateAlert('quality_score', 'school', $school->id)) {
                $alertLevel = 'medium';
                if ($stats->quality_score < $threshold * 0.5) {
                    $alertLevel = 'critical';
                } elseif ($stats->quality_score < $threshold * 0.7) {
                    $alertLevel = 'high';
                }

                ExperimentAlert::createAlert([
                    'alert_type' => 'quality_score',
                    'target_type' => 'school',
                    'target_id' => $school->id,
                    'target_name' => $school->name,
                    'alert_level' => $alertLevel,
                    'alert_title' => '实验质量评分过低',
                    'alert_message' => "学校「{$school->name}」实验质量评分为 {$stats->quality_score} 分，低于预警阈值 {$threshold} 分",
                    'alert_data' => [
                        'semester' => $semester,
                        'total_experiments' => $stats->total_planned_experiments,
                        'completed_experiments' => $stats->completed_experiments
                    ],
                    'alert_value' => $stats->quality_score,
                    'threshold_value' => $threshold
                ]);

                $alertsCreated++;
            }
        }

        return $alertsCreated;
    }

    /**
     * 检查是否可以查看监控数据
     */
    private function canViewMonitoring($user): bool
    {
        // 省、市、区县级管理员和学校管理员可以查看监控数据
        return $user->organization_level <= 5;
    }

    /**
     * 检查是否可以管理预警
     */
    private function canManageAlerts($user): bool
    {
        // 省、市、区县级管理员可以管理预警
        return $user->organization_level <= 3;
    }

    /**
     * 检查是否可以查看学校监控数据
     */
    private function canViewSchoolMonitoring($user, int $schoolId): bool
    {
        // 学校管理员只能查看自己学校的数据
        if ($user->organization_type === 'school') {
            return $user->organization_id === $schoolId;
        }

        // 上级管理员可以查看下级学校的数据
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        return $this->permissionService->canAccessSchool($user, $school);
    }

    /**
     * 应用预警数据范围过滤
     */
    private function applyAlertDataScopeFilter($query, $user): void
    {
        if ($user->organization_level <= 3) {
            // 省、市、区县级管理员可以查看辖区内的预警
            $schools = School::where('organization_type', $user->organization_type)
                ->where('organization_id', $user->organization_id)
                ->pluck('id');

            if ($schools->isNotEmpty()) {
                $query->where(function ($q) use ($schools) {
                    $q->where('target_type', 'school')
                      ->whereIn('target_id', $schools)
                      ->orWhere(function ($subQ) use ($schools) {
                          // 也包括这些学校的实验和教师预警
                          $subQ->whereIn('target_type', ['experiment', 'teacher'])
                               ->whereHas('alertData', function ($dataQ) use ($schools) {
                                   $dataQ->whereIn('school_id', $schools);
                               });
                      });
                });
            } else {
                // 如果没有管辖的学校，则不显示任何预警
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->organization_type === 'school') {
            // 学校管理员只能查看自己学校的预警
            $query->where(function ($q) use ($user) {
                $q->where('target_type', 'school')
                  ->where('target_id', $user->organization_id)
                  ->orWhere(function ($subQ) use ($user) {
                      // 也包括本校的实验和教师预警
                      $subQ->whereIn('target_type', ['experiment', 'teacher'])
                           ->whereJsonContains('alert_data->school_id', $user->organization_id);
                  });
            });
        } else {
            // 其他用户不能查看预警
            $query->whereRaw('1 = 0');
        }
    }
}
