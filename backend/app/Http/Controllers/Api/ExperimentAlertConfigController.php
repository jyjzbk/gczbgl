<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentAlertConfig;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExperimentAlertConfigController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取预警配置列表
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageAlertConfig($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问预警配置'
                ], 403);
            }

            $query = ExperimentAlertConfig::with(['creator:id,name'])
                ->where('is_active', true);

            // 根据用户权限过滤数据
            $this->applyDataScopeFilter($query, $user);

            // 筛选条件
            if ($request->filled('organization_type')) {
                $query->where('organization_type', $request->organization_type);
            }

            if ($request->filled('alert_type')) {
                $query->where('alert_type', $request->alert_type);
            }

            if ($request->filled('organization_id')) {
                $query->where('organization_id', $request->organization_id);
            }

            $configs = $query->orderBy('organization_type')
                ->orderBy('organization_id')
                ->orderBy('alert_type')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $configs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取预警配置列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 创建预警配置
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageAlertConfig($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限创建预警配置'
                ], 403);
            }

            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1',
                'organization_name' => 'required|string|max:100',
                'alert_type' => ['required', Rule::in(['overdue', 'completion_rate', 'quality_score'])],
                'threshold_value' => 'required|numeric|min:0',
                'alert_days' => 'required|integer|min:0|max:365',
                'alert_rules' => 'nullable|string|max:1000',
                'notification_settings' => 'nullable|array'
            ]);

            // 检查是否有权限管理指定组织
            if (!$this->canManageOrganization($user, $validated['organization_type'], $validated['organization_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理指定组织的配置'
                ], 403);
            }

            // 验证阈值范围
            if (in_array($validated['alert_type'], ['completion_rate', 'quality_score']) && $validated['threshold_value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => '完成率和质量评分的阈值不能超过100'
                ], 422);
            }

            $validated['created_by'] = $user->id;

            DB::beginTransaction();

            $config = ExperimentAlertConfig::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '创建预警配置成功',
                'data' => $config->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '创建预警配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取单个预警配置详情
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $config = ExperimentAlertConfig::with(['creator:id,name'])
                ->findOrFail($id);

            // 检查权限
            if (!$this->canViewConfig($user, $config)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看此配置'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取预警配置详情失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新预警配置
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $config = ExperimentAlertConfig::findOrFail($id);

            // 检查权限
            if (!$this->canEditConfig($user, $config)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限编辑此配置'
                ], 403);
            }

            $validated = $request->validate([
                'threshold_value' => 'required|numeric|min:0',
                'alert_days' => 'required|integer|min:0|max:365',
                'alert_rules' => 'nullable|string|max:1000',
                'notification_settings' => 'nullable|array',
                'is_active' => 'boolean'
            ]);

            // 验证阈值范围
            if (in_array($config->alert_type, ['completion_rate', 'quality_score']) && $validated['threshold_value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => '完成率和质量评分的阈值不能超过100'
                ], 422);
            }

            DB::beginTransaction();

            $config->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '更新预警配置成功',
                'data' => $config->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '更新预警配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除预警配置
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $config = ExperimentAlertConfig::findOrFail($id);

            // 检查权限
            if (!$this->canEditConfig($user, $config)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限删除此配置'
                ], 403);
            }

            DB::beginTransaction();

            // 软删除：设置为非活跃状态
            $config->update(['is_active' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '删除预警配置成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '删除预警配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取有效预警配置
     */
    public function getEffectiveConfig(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1',
                'alert_type' => ['required', Rule::in(['overdue', 'completion_rate', 'quality_score'])]
            ]);

            $config = ExperimentAlertConfig::getEffectiveConfig(
                $validated['organization_type'],
                $validated['organization_id'],
                $validated['alert_type']
            );

            if ($config) {
                $result = [
                    'threshold_value' => $config->threshold_value,
                    'alert_days' => $config->alert_days,
                    'alert_rules' => $config->alert_rules,
                    'notification_settings' => $config->getNotificationSettings(),
                    'source' => [
                        'organization_type' => $config->organization_type,
                        'organization_id' => $config->organization_id,
                        'organization_name' => $config->organization_name,
                        'is_inherited' => $config->organization_type !== $validated['organization_type'] || 
                                        $config->organization_id !== $validated['organization_id']
                    ]
                ];
            } else {
                $result = array_merge(
                    ExperimentAlertConfig::getDefaultConfig($validated['alert_type']),
                    [
                        'notification_settings' => [
                            'email' => true,
                            'sms' => false,
                            'system' => true,
                            'recipients' => []
                        ],
                        'source' => [
                            'organization_type' => 'default',
                            'organization_id' => 0,
                            'organization_name' => '系统默认',
                            'is_inherited' => false
                        ]
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取有效预警配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取组织选项
     */
    public function getOrganizationOptions(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $organizationType = $request->get('organization_type');

            $options = [];

            // 根据用户权限和组织类型返回可管理的组织列表
            switch ($organizationType) {
                case 'province':
                    if ($user->organization_level <= 1) {
                        $options = \App\Models\AdministrativeRegion::where('level', 1)
                            ->select('id', 'name')
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'value' => $item->id,
                                    'label' => $item->name
                                ];
                            });
                    }
                    break;
                case 'city':
                    if ($user->organization_level <= 2) {
                        $query = \App\Models\AdministrativeRegion::where('level', 2);
                        if ($user->organization_level === 2) {
                            $query->where('id', $user->organization_id);
                        }
                        $options = $query->select('id', 'name')
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'value' => $item->id,
                                    'label' => $item->name
                                ];
                            });
                    }
                    break;
                case 'county':
                    if ($user->organization_level <= 3) {
                        $query = \App\Models\AdministrativeRegion::where('level', 3);
                        if ($user->organization_level === 3) {
                            $query->where('id', $user->organization_id);
                        } elseif ($user->organization_level === 2) {
                            $query->where('parent_id', $user->organization_id);
                        }
                        $options = $query->select('id', 'name')
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'value' => $item->id,
                                    'label' => $item->name
                                ];
                            });
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $options
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取组织选项失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否可以管理预警配置
     */
    private function canManageAlertConfig($user): bool
    {
        // 只有省、市、区县级管理员可以管理预警配置
        return $user->organization_level <= 3;
    }

    /**
     * 检查是否可以管理指定组织
     */
    private function canManageOrganization($user, string $organizationType, int $organizationId): bool
    {
        $levelMap = [
            'province' => 1,
            'city' => 2,
            'county' => 3
        ];

        $requiredLevel = $levelMap[$organizationType] ?? 999;

        // 用户级别必须小于等于目标组织级别
        if ($user->organization_level > $requiredLevel) {
            return false;
        }

        // 如果是同级，必须是同一个组织
        if ($user->organization_level === $requiredLevel) {
            return $user->organization_id === $organizationId;
        }

        // 如果是上级，需要检查层级关系
        return $this->isParentOrganization($user, $organizationType, $organizationId);
    }

    /**
     * 检查是否为上级组织
     */
    private function isParentOrganization($user, string $organizationType, int $organizationId): bool
    {
        if ($user->organization_level >= 3) {
            return false;
        }

        $targetRegion = \App\Models\AdministrativeRegion::find($organizationId);
        if (!$targetRegion) {
            return false;
        }

        // 检查层级关系
        $currentRegion = $targetRegion;
        while ($currentRegion && $currentRegion->parent_id) {
            $currentRegion = \App\Models\AdministrativeRegion::find($currentRegion->parent_id);
            if ($currentRegion && $currentRegion->id === $user->organization_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查是否可以查看配置
     */
    private function canViewConfig($user, ExperimentAlertConfig $config): bool
    {
        return $this->canManageOrganization($user, $config->organization_type, $config->organization_id);
    }

    /**
     * 检查是否可以编辑配置
     */
    private function canEditConfig($user, ExperimentAlertConfig $config): bool
    {
        return $this->canManageOrganization($user, $config->organization_type, $config->organization_id);
    }

    /**
     * 应用数据范围过滤
     */
    private function applyDataScopeFilter($query, $user): void
    {
        if ($user->organization_level === 1) {
            // 省级用户可以看到所有配置
            return;
        } elseif ($user->organization_level === 2) {
            // 市级用户只能看到省级和本市的配置
            $query->where(function ($q) use ($user) {
                $q->where('organization_type', 'province')
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('organization_type', 'city')
                           ->where('organization_id', $user->organization_id);
                  });
            });
        } elseif ($user->organization_level === 3) {
            // 区县级用户只能看到省级、市级和本区县的配置
            $parentRegion = \App\Models\AdministrativeRegion::find($user->organization_id);
            $cityId = $parentRegion ? $parentRegion->parent_id : null;

            $query->where(function ($q) use ($user, $cityId) {
                $q->where('organization_type', 'province')
                  ->orWhere(function ($subQ) use ($cityId) {
                      if ($cityId) {
                          $subQ->where('organization_type', 'city')
                               ->where('organization_id', $cityId);
                      }
                  })
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('organization_type', 'county')
                           ->where('organization_id', $user->organization_id);
                  });
            });
        }
    }
}
