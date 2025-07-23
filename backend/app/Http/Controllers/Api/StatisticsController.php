<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Equipment;
use App\Models\ExperimentRecord;
use App\Models\ExperimentReservation;
use App\Models\Laboratory;
use App\Models\School;
use App\Models\AdministrativeRegion;
use App\Models\EquipmentBorrow;
use App\Models\EquipmentMaintenance;
use App\Http\Middleware\DataScopeMiddleware;
use App\Http\Middleware\StatisticsPermissionMiddleware;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取实验使用统计
     */
    public function getExperimentStats(Request $request)
    {
        // 检查权限
        $user = auth()->user();
        if (!$user->hasPermission('statistics.experiment') &&
            !$user->hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问实验统计数据'
            ], 403);
        }
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $schoolId = $request->get('school_id');

        $query = ExperimentRecord::query()
            ->whereBetween('start_time', [$startDate, $endDate]);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'experiment_records');

        if ($schoolId) {
            // 验证用户是否可以访问指定学校
            if (DataScopeMiddleware::canAccess($request, 'school', $schoolId)) {
                $query->where('school_id', $schoolId);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定学校的数据'
                ], 403);
            }
        }
        
        // 基础统计
        $totalExperiments = $query->count();
        $completedExperiments = $query->where('status', 2)->count();
        $completionRate = $totalExperiments > 0 ? round(($completedExperiments / $totalExperiments) * 100, 2) : 0;
        
        // 质量评分统计
        $qualityStats = $query->where('status', 2)
            ->whereNotNull('quality_score')
            ->selectRaw('
                AVG(quality_score) as avg_score,
                COUNT(CASE WHEN quality_score >= 4 THEN 1 END) as excellent_count,
                COUNT(CASE WHEN quality_score = 3 THEN 1 END) as good_count,
                COUNT(CASE WHEN quality_score <= 2 THEN 1 END) as poor_count
            ')
            ->first();
            
        // 按月份统计
        $monthlyStats = $query->selectRaw('
                DATE_FORMAT(start_time, "%Y-%m") as month,
                COUNT(*) as total,
                COUNT(CASE WHEN status = 2 THEN 1 END) as completed
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // 按学校统计
        $schoolStatsQuery = ExperimentRecord::query()
            ->whereBetween('start_time', [$startDate, $endDate])
            ->join('schools', 'experiment_records.school_id', '=', 'schools.id');

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($schoolStatsQuery, $request, 'experiment_records');

        $schoolStats = $schoolStatsQuery->selectRaw('
                schools.id,
                schools.name,
                COUNT(*) as total,
                COUNT(CASE WHEN experiment_records.status = 2 THEN 1 END) as completed,
                ROUND(COUNT(CASE WHEN experiment_records.status = 2 THEN 1 END) * 100.0 / COUNT(*), 2) as completion_rate
            ')
            ->groupBy('schools.id', 'schools.name')
            ->orderBy('completion_rate', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_experiments' => $totalExperiments,
                    'completed_experiments' => $completedExperiments,
                    'completion_rate' => $completionRate,
                    'avg_quality_score' => round($qualityStats->avg_score ?? 0, 2),
                    'excellent_rate' => $totalExperiments > 0 ? round(($qualityStats->excellent_count / $totalExperiments) * 100, 2) : 0
                ],
                'quality_distribution' => [
                    'excellent' => $qualityStats->excellent_count ?? 0,
                    'good' => $qualityStats->good_count ?? 0,
                    'poor' => $qualityStats->poor_count ?? 0
                ],
                'monthly_trend' => $monthlyStats,
                'school_ranking' => $schoolStats
            ]
        ]);
    }
    
    /**
     * 获取设备利用率统计
     */
    public function getEquipmentStats(Request $request)
    {
        // 检查权限
        $user = auth()->user();
        if (!$user->hasPermission('statistics.equipment') &&
            !$user->hasAnyPermission(['equipment.view', 'equipment.list'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问设备统计数据'
            ], 403);
        }
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $schoolId = $request->get('school_id');

        $query = Equipment::query();

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'equipments');

        if ($schoolId) {
            // 验证用户是否可以访问指定学校
            if (DataScopeMiddleware::canAccess($request, 'school', $schoolId)) {
                $query->where('school_id', $schoolId);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定学校的数据'
                ], 403);
            }
        }
        
        // 设备状态统计
        $statusStats = clone $query;
        $statusStats = $statusStats->selectRaw('
                status,
                COUNT(*) as count,
                SUM(purchase_price) as total_value
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // 设备分类统计
        $categoryQuery = Equipment::query();
        DataScopeMiddleware::applyDataScope($categoryQuery, $request, 'equipments');

        if ($schoolId) {
            if (DataScopeMiddleware::canAccess($request, 'school', $schoolId)) {
                $categoryQuery->where('school_id', $schoolId);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定学校的数据'
                ], 403);
            }
        }

        $categoryStats = $categoryQuery->join('equipment_categories', 'equipments.category_id', '=', 'equipment_categories.id')
            ->selectRaw('
                equipment_categories.name as category_name,
                COUNT(*) as count,
                SUM(equipments.purchase_price) as total_value
            ')
            ->groupBy('equipment_categories.id', 'equipment_categories.name')
            ->orderBy('total_value', 'desc')
            ->get();
            
        // 设备使用频率统计（基于借用记录）
        $utilizationStats = EquipmentBorrow::query()
            ->join('equipments', 'equipment_borrows.equipment_id', '=', 'equipments.id')
            ->whereBetween('equipment_borrows.borrow_date', [$startDate, $endDate]);

        // 应用数据权限过滤（通过equipments表）
        DataScopeMiddleware::applyDataScope($utilizationStats, $request, 'equipments');

        if ($schoolId) {
            // 验证权限已在上面完成，这里直接过滤
            $utilizationStats->where('equipments.school_id', $schoolId);
        }
        
        $utilizationData = $utilizationStats->selectRaw('
                equipments.id,
                equipments.name,
                equipments.code,
                COUNT(*) as borrow_count,
                SUM(DATEDIFF(COALESCE(equipment_borrows.actual_return_date, NOW()), equipment_borrows.borrow_date)) as total_days
            ')
            ->groupBy('equipments.id', 'equipments.name', 'equipments.code')
            ->orderBy('borrow_count', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_equipment' => $query->count(),
                    'normal_equipment' => $statusStats->get(1)->count ?? 0,
                    'maintenance_equipment' => $statusStats->get(3)->count ?? 0,
                    'scrapped_equipment' => $statusStats->get(4)->count ?? 0,
                    'total_value' => $statusStats->sum('total_value')
                ],
                'status_distribution' => $statusStats->map(function($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'value' => $item->total_value
                    ];
                })->values(),
                'category_distribution' => $categoryStats,
                'top_utilized' => $utilizationData
            ]
        ]);
    }
    
    /**
     * 获取用户活跃度统计
     */
    public function getUserActivityStats(Request $request)
    {
        // 检查权限
        $user = auth()->user();
        if (!$user->hasPermission('statistics.user') &&
            !$user->hasAnyPermission(['user', 'user.list'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问用户统计数据'
            ], 403);
        }
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // 创建基础查询
        $baseQuery = User::query()->where('status', 1);
        DataScopeMiddleware::applyDataScope($baseQuery, $request, 'users');

        // 登录活跃度统计 - 使用克隆的查询
        $totalUsers = (clone $baseQuery)->count();
        $activeUsers = (clone $baseQuery)->whereBetween('last_login_at', [$startDate, $endDate])->count();
        $neverLoginUsers = (clone $baseQuery)->whereNull('last_login_at')->count();

        // 角色分布统计 - 使用新的查询实例
        $roleStats = (clone $baseQuery)->selectRaw('
                role,
                COUNT(*) as count
            ')
            ->groupBy('role')
            ->get();

        // 组织层级分布 - 使用新的查询实例
        $levelStats = (clone $baseQuery)->selectRaw('
                organization_level,
                COUNT(*) as count
            ')
            ->whereNotNull('organization_level')
            ->groupBy('organization_level')
            ->orderBy('organization_level')
            ->get();

        // 最近登录用户 - 使用新的查询实例
        $recentActiveUsers = (clone $baseQuery)->whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->limit(10)
            ->select('id', 'real_name', 'role', 'last_login_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'activity_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
                    'never_login_users' => $neverLoginUsers
                ],
                'role_distribution' => $roleStats,
                'level_distribution' => $levelStats,
                'recent_active' => $recentActiveUsers
            ]
        ]);
    }

    /**
     * 获取组织绩效统计
     */
    public function getOrganizationPerformance(Request $request)
    {
        // 检查权限 - 绩效统计需要管理员级别权限
        $user = auth()->user();
        if (!$user->hasPermission('statistics.performance') &&
            !$user->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问绩效统计数据'
            ], 403);
        }
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // 学校绩效统计
        $schoolQuery = School::query();

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($schoolQuery, $request, 'schools');

        $schoolPerformance = $schoolQuery->leftJoin('experiment_records', function($join) use ($startDate, $endDate) {
                $join->on('schools.id', '=', 'experiment_records.school_id')
                     ->whereBetween('experiment_records.start_time', [$startDate, $endDate]);
            })
            ->leftJoin('equipments', 'schools.id', '=', 'equipments.school_id')
            ->leftJoin('users', function($join) {
                $join->on('schools.id', '=', 'users.organization_id')
                     ->where('users.organization_type', '=', 'school');
            })
            ->selectRaw('
                schools.id,
                schools.name,
                schools.type,
                COUNT(DISTINCT experiment_records.id) as total_experiments,
                COUNT(DISTINCT CASE WHEN experiment_records.status = 2 THEN experiment_records.id END) as completed_experiments,
                COUNT(DISTINCT equipments.id) as total_equipment,
                COALESCE(SUM(equipments.purchase_price), 0) as equipment_value,
                COUNT(DISTINCT users.id) as total_users,
                AVG(experiment_records.quality_score) as avg_quality_score
            ')
            ->groupBy('schools.id', 'schools.name', 'schools.type')
            ->get()
            ->map(function($item) {
                $item->completion_rate = $item->total_experiments > 0
                    ? round(($item->completed_experiments / $item->total_experiments) * 100, 2)
                    : 0;
                $item->avg_quality_score = round($item->avg_quality_score ?? 0, 2);
                return $item;
            });

        // 综合排名
        $rankings = $schoolPerformance->sortByDesc(function($school) {
            // 综合评分：完成率40% + 质量评分30% + 设备价值20% + 用户活跃度10%
            $completionScore = $school->completion_rate;
            $qualityScore = ($school->avg_quality_score / 5) * 100;
            $equipmentScore = min(($school->equipment_value / 100000) * 100, 100); // 10万为满分
            $userScore = min(($school->total_users / 50) * 100, 100); // 50人为满分

            return ($completionScore * 0.4) + ($qualityScore * 0.3) + ($equipmentScore * 0.2) + ($userScore * 0.1);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'school_performance' => $schoolPerformance,
                'rankings' => $rankings->take(10),
                'summary' => [
                    'total_schools' => $schoolPerformance->count(),
                    'avg_completion_rate' => round($schoolPerformance->avg('completion_rate'), 2),
                    'avg_quality_score' => round($schoolPerformance->avg('avg_quality_score'), 2),
                    'total_equipment_value' => $schoolPerformance->sum('equipment_value')
                ]
            ]
        ]);
    }

    /**
     * 获取综合仪表盘数据
     */
    public function getDashboardStats(Request $request)
    {
        // 基础统计
        $stats = [];

        // 学校数量
        $schoolQuery = School::query();
        DataScopeMiddleware::applyDataScope($schoolQuery, $request, 'schools');
        $stats['total_schools'] = $schoolQuery->count();

        // 用户数量
        $userQuery = User::query()->where('status', 1);
        DataScopeMiddleware::applyDataScope($userQuery, $request, 'users');
        $stats['total_users'] = $userQuery->count();

        // 设备数量和价值
        $equipmentQuery = Equipment::query();
        DataScopeMiddleware::applyDataScope($equipmentQuery, $request, 'equipments');
        $stats['total_equipment'] = $equipmentQuery->count();
        $stats['equipment_value'] = $equipmentQuery->sum('purchase_price');

        // 实验室数量
        $labQuery = Laboratory::query();
        DataScopeMiddleware::applyDataScope($labQuery, $request, 'laboratories');
        $stats['total_laboratories'] = $labQuery->count();

        // 本月实验统计
        $currentMonth = Carbon::now()->format('Y-m');
        $experimentQuery = ExperimentRecord::query()
            ->whereRaw("DATE_FORMAT(start_time, '%Y-%m') = ?", [$currentMonth]);
        DataScopeMiddleware::applyDataScope($experimentQuery, $request, 'experiment_records');

        $stats['monthly_experiments'] = $experimentQuery->count();
        $stats['monthly_completed'] = $experimentQuery->where('status', 2)->count();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * 获取实验完成率趋势
     */
    public function getExperimentCompletionTrend(Request $request)
    {
        $request->validate([
            'teacher_id' => 'nullable',
            'months' => 'nullable|integer|min:1|max:24'
        ]);

        $teacherId = $request->teacher_id === 'current' ? auth()->id() : $request->teacher_id;
        $months = $request->months ?? 12;

        // 获取指定月份数的趋势数据
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $trendData = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $query = ExperimentReservation::whereBetween('reservation_date', [$monthStart, $monthEnd]);

            if ($teacherId) {
                $query->where('teacher_id', $teacherId);
            }

            $totalExperiments = $query->count();
            $completedExperiments = $query->where('status', ExperimentReservation::STATUS_COMPLETED)->count();

            $completionRate = $totalExperiments > 0 ? round(($completedExperiments / $totalExperiments) * 100, 2) : 0;

            $trendData[] = [
                'month' => $current->format('Y-m'),
                'completion_rate' => $completionRate,
                'total_experiments' => $totalExperiments,
                'completed_experiments' => $completedExperiments
            ];

            $current->addMonth();
        }

        return response()->json([
            'success' => true,
            'data' => $trendData
        ]);
    }
}
