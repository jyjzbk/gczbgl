<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentCatalogCompletionBaseline;
use App\Models\School;
use App\Services\SchoolExperimentCatalogPermissionService;
use App\Services\ExperimentCompletionCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CompletionStatisticsController extends Controller
{
    protected $permissionService;
    protected $calculatorService;

    public function __construct(
        SchoolExperimentCatalogPermissionService $permissionService,
        ExperimentCompletionCalculatorService $calculatorService
    ) {
        $this->permissionService = $permissionService;
        $this->calculatorService = $calculatorService;
    }

    /**
     * 获取学校完成率统计
     */
    public function getSchoolStatistics(Request $request, int $schoolId): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->permissionService->canViewCompletionStats($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校统计数据'
                ], 403);
            }

            $validated = $request->validate([
                'subject_id' => 'nullable|integer|exists:subjects,id',
                'grade' => 'nullable|integer|min:1|max:6',
                'semester' => 'nullable|integer|in:1,2',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'force_recalculate' => 'boolean'
            ]);

            $filters = array_filter($validated, function($value) {
                return $value !== null;
            });

            // 检查是否需要重新计算
            if ($validated['force_recalculate'] ?? false) {
                $completionData = $this->calculatorService->calculateSchoolCompletionRate($schoolId, $filters);
            } else {
                // 尝试从缓存获取
                $cacheKey = "school_completion_stats_{$schoolId}_" . md5(json_encode($filters));
                $completionData = Cache::remember($cacheKey, 300, function() use ($schoolId, $filters) {
                    return $this->calculatorService->calculateSchoolCompletionRate($schoolId, $filters);
                });
            }

            // 获取学校信息
            $school = School::find($schoolId);

            // 获取趋势数据
            $trendData = ExperimentCatalogCompletionBaseline::getTrendData($schoolId, $filters);

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'completion_data' => $completionData,
                    'trend_data' => $trendData,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取统计数据失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取完成率排行榜
     */
    public function getCompletionRanking(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_ids' => 'nullable|array',
                'school_ids.*' => 'integer|exists:schools,id',
                'subject_id' => 'nullable|integer|exists:subjects,id',
                'grade' => 'nullable|integer|min:1|max:6',
                'semester' => 'nullable|integer|in:1,2',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            // 获取用户可查看的学校列表
            $schoolIds = $this->getAccessibleSchoolIds($user, $validated['school_ids'] ?? []);

            if (empty($schoolIds)) {
                return response()->json([
                    'success' => false,
                    'message' => '没有可查看的学校数据'
                ], 403);
            }

            $filters = array_filter([
                'subject_id' => $validated['subject_id'] ?? null,
                'grade' => $validated['grade'] ?? null,
                'semester' => $validated['semester'] ?? null
            ]);

            $ranking = ExperimentCatalogCompletionBaseline::getCompletionRanking($schoolIds, $filters);

            // 应用限制
            if (!empty($validated['limit'])) {
                $ranking = array_slice($ranking, 0, $validated['limit']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'ranking' => $ranking,
                    'total_schools' => count($schoolIds),
                    'filters' => $filters
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取排行榜失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取按维度统计的完成率
     */
    public function getCompletionByDimension(Request $request, int $schoolId): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->permissionService->canViewCompletionStats($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校统计数据'
                ], 403);
            }

            $validated = $request->validate([
                'dimension' => 'required|in:subject_id,grade,semester'
            ]);

            $stats = ExperimentCatalogCompletionBaseline::getCompletionByDimension(
                $schoolId, 
                $validated['dimension']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'dimension' => $validated['dimension'],
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取维度统计失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 重新计算完成率
     */
    public function recalculateCompletion(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_ids' => 'required|array|min:1',
                'school_ids.*' => 'integer|exists:schools,id'
            ]);

            // 检查权限
            foreach ($validated['school_ids'] as $schoolId) {
                if (!$this->permissionService->canViewCompletionStats($user, $schoolId)) {
                    return response()->json([
                        'success' => false,
                        'message' => "无权限操作学校ID {$schoolId}"
                    ], 403);
                }
            }

            $results = [];
            $successCount = 0;
            $failCount = 0;

            foreach ($validated['school_ids'] as $schoolId) {
                try {
                    $completionData = $this->calculatorService->calculateSchoolCompletionRate($schoolId);
                    $results[$schoolId] = [
                        'success' => true,
                        'completion_rate' => $completionData['overall']['completion_rate']
                    ];
                    $successCount++;

                    // 清除缓存
                    Cache::forget("school_completion_stats_{$schoolId}_*");

                } catch (\Exception $e) {
                    $results[$schoolId] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                    $failCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "重新计算完成，成功 {$successCount} 个，失败 {$failCount} 个",
                'data' => [
                    'results' => $results,
                    'summary' => [
                        'total' => count($validated['school_ids']),
                        'success' => $successCount,
                        'failed' => $failCount
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '重新计算失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取统计概览
     */
    public function getStatisticsOverview(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_ids' => 'nullable|array',
                'school_ids.*' => 'integer|exists:schools,id'
            ]);

            // 获取用户可查看的学校列表
            $schoolIds = $this->getAccessibleSchoolIds($user, $validated['school_ids'] ?? []);

            if (empty($schoolIds)) {
                return response()->json([
                    'success' => false,
                    'message' => '没有可查看的学校数据'
                ], 403);
            }

            // 获取总体统计
            $overallStats = [];
            $totalExperiments = 0;
            $totalCompleted = 0;

            foreach ($schoolIds as $schoolId) {
                $schoolStats = ExperimentCatalogCompletionBaseline::getSchoolOverallRate($schoolId);
                $overallStats[$schoolId] = $schoolStats;
                $totalExperiments += $schoolStats['total_experiments'];
                $totalCompleted += $schoolStats['completed_experiments'];
            }

            $overallCompletionRate = $totalExperiments > 0 ? 
                round(($totalCompleted / $totalExperiments) * 100, 2) : 0;

            // 获取排行榜前10
            $topRanking = ExperimentCatalogCompletionBaseline::getCompletionRanking($schoolIds, []);
            $topRanking = array_slice($topRanking, 0, 10);

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_schools' => count($schoolIds),
                        'total_experiments' => $totalExperiments,
                        'total_completed' => $totalCompleted,
                        'overall_completion_rate' => $overallCompletionRate
                    ],
                    'school_stats' => $overallStats,
                    'top_ranking' => $topRanking
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取统计概览失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取用户可访问的学校ID列表
     */
    private function getAccessibleSchoolIds($user, array $requestedSchoolIds = []): array
    {
        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id ?? $user->school_id;

        // 如果是学校用户，只能查看自己的学校
        if ($userLevel == 5) {
            return [$userOrgId];
        }

        // 获取用户可管理的学校
        $query = School::query();

        switch ($userLevel) {
            case 1: // 省级 - 可查看所有学校
                break;
            case 2: // 市级 - 可查看本市学校
                $query->where('city_id', $userOrgId);
                break;
            case 3: // 区县级 - 可查看本区县学校
                $query->where('district_id', $userOrgId);
                break;
            default:
                return [];
        }

        $accessibleSchoolIds = $query->pluck('id')->toArray();

        // 如果指定了学校ID，取交集
        if (!empty($requestedSchoolIds)) {
            return array_intersect($accessibleSchoolIds, $requestedSchoolIds);
        }

        return $accessibleSchoolIds;
    }
}
