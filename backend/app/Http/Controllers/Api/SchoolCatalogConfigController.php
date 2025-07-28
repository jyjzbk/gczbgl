<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExperimentCatalogConfig;
use App\Models\School;
use App\Models\AdministrativeRegion;
use App\Services\SchoolExperimentCatalogPermissionService;
use App\Services\ExperimentCompletionCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolCatalogConfigController extends Controller
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
     * 获取我的目录配置
     */
    public function getMyConfig(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $schoolId = $request->get('school_id');

            // 如果没有指定学校ID，使用当前用户的学校
            if (!$schoolId && $user->organization_type === 'school') {
                $schoolId = $user->organization_id;
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 检查权限
            if (!$this->permissionService->canConfigureSchoolCatalog($user, $schoolId) &&
                !$this->permissionService->canAssignSchoolCatalog($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校配置'
                ], 403);
            }

            $config = SchoolExperimentCatalogConfig::getActiveConfig($schoolId);
            $school = School::find($schoolId);

            if (!$config) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'school' => $school,
                        'config' => null,
                        'permissions' => $this->permissionService->getUserPermissions($user, $schoolId)
                    ]
                ]);
            }

            // 获取配置统计信息
            $stats = $config->getConfigStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'config' => $config->load(['configuredBy:id,name']),
                    'stats' => $stats,
                    'permissions' => $this->permissionService->getUserPermissions($user, $schoolId)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 配置学校目录
     */
    public function configureSchool(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
                'config_type' => ['required', Rule::in(['selection', 'assignment'])],
                'source_level' => 'required|integer|in:1,2,3',
                'source_org_id' => 'required|integer|min:1',
                'source_org_name' => 'required|string|max:100',
                'can_modify_selection' => 'boolean',
                'can_delete_experiments' => 'boolean',
                'config_reason' => 'nullable|string|max:1000',
                'effective_date' => 'nullable|date|after_or_equal:today',
                'expiry_date' => 'nullable|date|after:effective_date'
            ]);

            // 检查权限
            if (!$this->permissionService->canConfigureSchoolCatalog($user, $validated['school_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限配置该学校目录'
                ], 403);
            }

            // 验证配置数据
            $errors = $this->permissionService->validateConfigData($validated, $user);
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => '配置数据验证失败',
                    'errors' => $errors
                ], 422);
            }

            DB::beginTransaction();

            // 设置配置
            $configData = array_merge($validated, [
                'configured_by' => $user->id,
                'configured_by_level' => $user->organization_level ?? 5
            ]);

            $config = SchoolExperimentCatalogConfig::setSchoolConfig($configData);

            // 重新计算完成率基准
            $this->calculatorService->calculateSchoolCompletionRate($validated['school_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '学校目录配置成功',
                'data' => $config->load(['school:id,name', 'configuredBy:id,name'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取下级学校列表
     */
    public function getSubordinateSchools(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 检查权限
            if (!$this->permissionService->canAssignSchoolCatalog($user, 0)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理下级学校'
                ], 403);
            }

            $validated = $request->validate([
                'management_level' => 'nullable|integer|in:1,2,3,4',
                'region_id' => 'nullable|integer',
                'config_status' => 'nullable|in:all,configured,unconfigured',
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100'
            ]);

            $query = School::with(['config' => function($q) {
                $q->where('status', 1);
            }]);

            // 根据用户级别筛选可管理的学校
            $userLevel = $user->organization_level ?? 5;
            $userOrgId = $user->organization_id ?? $user->school_id;

            switch ($userLevel) {
                case 1: // 省级 - 可管理所有学校
                    break;
                case 2: // 市级 - 可管理本市学校
                    $query->where('city_id', $userOrgId);
                    break;
                case 3: // 区县级 - 可管理本区县学校
                    $query->where('district_id', $userOrgId);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => '权限不足'
                    ], 403);
            }

            // 应用筛选条件
            if (!empty($validated['management_level'])) {
                $query->where('management_level', $validated['management_level']);
            }

            if (!empty($validated['region_id'])) {
                $query->where(function($q) use ($validated) {
                    $q->where('province_id', $validated['region_id'])
                      ->orWhere('city_id', $validated['region_id'])
                      ->orWhere('district_id', $validated['region_id']);
                });
            }

            // 配置状态筛选
            if (!empty($validated['config_status'])) {
                if ($validated['config_status'] === 'configured') {
                    $query->whereHas('config', function($q) {
                        $q->where('status', 1);
                    });
                } elseif ($validated['config_status'] === 'unconfigured') {
                    $query->whereDoesntHave('config', function($q) {
                        $q->where('status', 1);
                    });
                }
            }

            $schools = $query->paginate($validated['per_page'] ?? 15);

            // 添加配置状态信息
            $schools->getCollection()->transform(function($school) {
                $school->config_status = $school->config ? 'configured' : 'unconfigured';
                $school->config_type_name = $school->config ? $school->config->config_type_name : null;
                return $school;
            });

            return response()->json([
                'success' => true,
                'data' => $schools
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取学校列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量指定学校目录
     */
    public function batchAssignSchools(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'school_ids' => 'required|array|min:1',
                'school_ids.*' => 'integer|exists:schools,id',
                'source_level' => 'required|integer|in:1,2,3',
                'source_org_id' => 'required|integer|min:1',
                'source_org_name' => 'required|string|max:100',
                'can_delete_experiments' => 'boolean',
                'config_reason' => 'nullable|string|max:1000'
            ]);

            // 检查权限
            foreach ($validated['school_ids'] as $schoolId) {
                if (!$this->permissionService->canAssignSchoolCatalog($user, $schoolId)) {
                    return response()->json([
                        'success' => false,
                        'message' => "无权限指定学校ID {$schoolId} 的目录"
                    ], 403);
                }
            }

            DB::beginTransaction();

            $configData = [
                'config_type' => 'assignment',
                'source_level' => $validated['source_level'],
                'source_org_id' => $validated['source_org_id'],
                'source_org_name' => $validated['source_org_name'],
                'can_modify_selection' => false,
                'can_delete_experiments' => $validated['can_delete_experiments'] ?? false,
                'config_reason' => $validated['config_reason']
            ];

            $results = SchoolExperimentCatalogConfig::batchSetConfigs(
                $validated['school_ids'],
                $configData,
                $user
            );

            // 重新计算完成率基准
            foreach ($validated['school_ids'] as $schoolId) {
                if ($results[$schoolId]['success']) {
                    try {
                        $this->calculatorService->calculateSchoolCompletionRate($schoolId);
                    } catch (\Exception $e) {
                        // 记录错误但不影响主流程
                        \Log::warning("计算学校 {$schoolId} 完成率失败: " . $e->getMessage());
                    }
                }
            }

            DB::commit();

            $successCount = collect($results)->where('success', true)->count();
            $failCount = collect($results)->where('success', false)->count();

            return response()->json([
                'success' => true,
                'message' => "批量指定完成，成功 {$successCount} 个，失败 {$failCount} 个",
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
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '批量指定失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取可选择的组织列表
     */
    public function getAvailableOrganizations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $schoolId = $request->get('school_id');
            $level = $request->get('level');

            if (!$schoolId || !$level) {
                return response()->json([
                    'success' => false,
                    'message' => '缺少必要参数'
                ], 400);
            }

            $organizations = $this->permissionService->getAvailableOrganizations($user, $schoolId, $level);

            return response()->json([
                'success' => true,
                'data' => $organizations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取组织列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取配置历史
     */
    public function getConfigHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $schoolId = $request->get('school_id');

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => '请指定学校ID'
                ], 400);
            }

            // 检查权限
            if (!$this->permissionService->canConfigureSchoolCatalog($user, $schoolId) &&
                !$this->permissionService->canAssignSchoolCatalog($user, $schoolId)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该学校配置历史'
                ], 403);
            }

            $history = SchoolExperimentCatalogConfig::getConfigHistory($schoolId);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取配置历史失败：' . $e->getMessage()
            ], 500);
        }
    }
}
