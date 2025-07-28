<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentRequirementsConfig;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExperimentRequirementsConfigController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取实验要求配置列表
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageExperimentRequirements($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问实验要求配置'
                ], 403);
            }

            $query = ExperimentRequirementsConfig::with(['creator:id,name'])
                ->where('is_active', true);

            // 根据用户权限过滤数据
            $this->applyDataScopeFilter($query, $user);

            // 筛选条件
            if ($request->filled('organization_type')) {
                $query->where('organization_type', $request->organization_type);
            }

            if ($request->filled('experiment_type')) {
                $query->where('experiment_type', $request->experiment_type);
            }

            if ($request->filled('organization_id')) {
                $query->where('organization_id', $request->organization_id);
            }

            $configs = $query->orderBy('organization_type')
                ->orderBy('organization_id')
                ->orderBy('experiment_type')
                ->paginate($request->get('per_page', 15));

            // 为每个配置添加有效配置信息
            $configs->getCollection()->transform(function ($config) {
                $effectiveConfig = ExperimentRequirementsConfig::getEffectiveConfig(
                    $config->organization_type,
                    $config->organization_id,
                    $config->experiment_type
                );
                
                $config->effective_config = $effectiveConfig ? [
                    'min_images' => $effectiveConfig->min_images,
                    'max_images' => $effectiveConfig->max_images,
                    'min_videos' => $effectiveConfig->min_videos,
                    'max_videos' => $effectiveConfig->max_videos,
                    'is_inherited' => $effectiveConfig->id !== $config->id
                ] : ExperimentRequirementsConfig::getDefaultConfig($config->experiment_type);

                return $config;
            });

            return response()->json([
                'success' => true,
                'data' => $configs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取配置列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 创建实验要求配置
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageExperimentRequirements($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限创建实验要求配置'
                ], 403);
            }

            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1',
                'experiment_type' => ['required', Rule::in(['分组实验', '演示实验'])],
                'min_images' => 'required|integer|min:0|max:20',
                'max_images' => 'required|integer|min:0|max:20',
                'min_videos' => 'required|integer|min:0|max:10',
                'max_videos' => 'required|integer|min:0|max:10',
                'is_inherited' => 'boolean',
                'description' => 'nullable|string|max:500'
            ]);

            // 验证数量关系
            if ($validated['max_images'] < $validated['min_images']) {
                return response()->json([
                    'success' => false,
                    'message' => '最多图片数量不能小于最少图片数量'
                ], 422);
            }

            if ($validated['max_videos'] < $validated['min_videos']) {
                return response()->json([
                    'success' => false,
                    'message' => '最多视频数量不能小于最少视频数量'
                ], 422);
            }

            // 检查是否有权限管理指定组织
            if (!$this->canManageOrganization($user, $validated['organization_type'], $validated['organization_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理指定组织的配置'
                ], 403);
            }

            $validated['created_by'] = $user->id;

            DB::beginTransaction();

            $config = ExperimentRequirementsConfig::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '创建配置成功',
                'data' => $config->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '创建配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取单个配置详情
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $config = ExperimentRequirementsConfig::with(['creator:id,name'])
                ->findOrFail($id);

            // 检查权限
            if (!$this->canViewConfig($user, $config)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看此配置'
                ], 403);
            }

            // 获取有效配置
            $effectiveConfig = ExperimentRequirementsConfig::getEffectiveConfig(
                $config->organization_type,
                $config->organization_id,
                $config->experiment_type
            );

            $config->effective_config = $effectiveConfig ? [
                'min_images' => $effectiveConfig->min_images,
                'max_images' => $effectiveConfig->max_images,
                'min_videos' => $effectiveConfig->min_videos,
                'max_videos' => $effectiveConfig->max_videos,
                'is_inherited' => $effectiveConfig->id !== $config->id
            ] : ExperimentRequirementsConfig::getDefaultConfig($config->experiment_type);

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取配置详情失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新配置
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $config = ExperimentRequirementsConfig::findOrFail($id);

            // 检查权限
            if (!$this->canEditConfig($user, $config)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限编辑此配置'
                ], 403);
            }

            $validated = $request->validate([
                'min_images' => 'required|integer|min:0|max:20',
                'max_images' => 'required|integer|min:0|max:20',
                'min_videos' => 'required|integer|min:0|max:10',
                'max_videos' => 'required|integer|min:0|max:10',
                'is_inherited' => 'boolean',
                'description' => 'nullable|string|max:500'
            ]);

            // 验证数量关系
            if ($validated['max_images'] < $validated['min_images']) {
                return response()->json([
                    'success' => false,
                    'message' => '最多图片数量不能小于最少图片数量'
                ], 422);
            }

            if ($validated['max_videos'] < $validated['min_videos']) {
                return response()->json([
                    'success' => false,
                    'message' => '最多视频数量不能小于最少视频数量'
                ], 422);
            }

            DB::beginTransaction();

            $config->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '更新配置成功',
                'data' => $config->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '更新配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除配置
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $config = ExperimentRequirementsConfig::findOrFail($id);

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
                'message' => '删除配置成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '删除配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取有效配置（供其他模块调用）
     */
    public function getEffectiveConfig(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1',
                'experiment_type' => ['required', Rule::in(['分组实验', '演示实验'])]
            ]);

            $config = ExperimentRequirementsConfig::getEffectiveConfig(
                $validated['organization_type'],
                $validated['organization_id'],
                $validated['experiment_type']
            );

            if ($config) {
                $result = [
                    'min_images' => $config->min_images,
                    'max_images' => $config->max_images,
                    'min_videos' => $config->min_videos,
                    'max_videos' => $config->max_videos,
                    'source' => [
                        'organization_type' => $config->organization_type,
                        'organization_id' => $config->organization_id,
                        'is_inherited' => $config->organization_type !== $validated['organization_type'] ||
                                        $config->organization_id !== $validated['organization_id']
                    ]
                ];
            } else {
                $result = array_merge(
                    ExperimentRequirementsConfig::getDefaultConfig($validated['experiment_type']),
                    [
                        'source' => [
                            'organization_type' => 'default',
                            'organization_id' => 0,
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
                'message' => '获取有效配置失败：' . $e->getMessage()
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
     * 检查是否可以管理实验要求配置
     */
    private function canManageExperimentRequirements($user): bool
    {
        // 只有省、市、区县级管理员可以管理实验要求配置
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
    private function canViewConfig($user, ExperimentRequirementsConfig $config): bool
    {
        return $this->canManageOrganization($user, $config->organization_type, $config->organization_id);
    }

    /**
     * 检查是否可以编辑配置
     */
    private function canEditConfig($user, ExperimentRequirementsConfig $config): bool
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
