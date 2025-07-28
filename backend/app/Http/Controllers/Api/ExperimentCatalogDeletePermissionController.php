<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentCatalogDeletePermission;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExperimentCatalogDeletePermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取删除权限配置列表
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageDeletePermissions($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限访问删除权限配置'
                ], 403);
            }

            $query = ExperimentCatalogDeletePermission::with(['creator:id,name'])
                ->where('is_active', true);

            // 根据用户权限过滤数据
            $this->applyDataScopeFilter($query, $user);

            // 筛选条件
            if ($request->filled('organization_type')) {
                $query->where('organization_type', $request->organization_type);
            }

            if ($request->filled('organization_id')) {
                $query->where('organization_id', $request->organization_id);
            }

            $permissions = $query->orderBy('organization_type')
                ->orderBy('organization_id')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取权限配置列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 创建删除权限配置
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 检查权限
            if (!$this->canManageDeletePermissions($user)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限创建删除权限配置'
                ], 403);
            }

            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1',
                'organization_name' => 'required|string|max:100',
                'allow_school_delete' => 'boolean',
                'require_delete_reason' => 'boolean',
                'max_delete_percentage' => 'required|integer|min:0|max:100',
                'delete_rules' => 'nullable|string|max:1000'
            ]);

            // 检查是否有权限管理指定组织
            if (!$this->canManageOrganization($user, $validated['organization_type'], $validated['organization_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限管理指定组织的配置'
                ], 403);
            }

            $validated['created_by'] = $user->id;

            DB::beginTransaction();

            $permission = ExperimentCatalogDeletePermission::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '创建权限配置成功',
                'data' => $permission->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '创建权限配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取单个权限配置详情
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $permission = ExperimentCatalogDeletePermission::with(['creator:id,name'])
                ->findOrFail($id);

            // 检查权限
            if (!$this->canViewPermission($user, $permission)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看此配置'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $permission
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取权限配置详情失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新权限配置
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $permission = ExperimentCatalogDeletePermission::findOrFail($id);

            // 检查权限
            if (!$this->canEditPermission($user, $permission)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限编辑此配置'
                ], 403);
            }

            $validated = $request->validate([
                'allow_school_delete' => 'boolean',
                'require_delete_reason' => 'boolean',
                'max_delete_percentage' => 'required|integer|min:0|max:100',
                'delete_rules' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $permission->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '更新权限配置成功',
                'data' => $permission->load('creator:id,name')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '更新权限配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除权限配置
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $permission = ExperimentCatalogDeletePermission::findOrFail($id);

            // 检查权限
            if (!$this->canEditPermission($user, $permission)) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限删除此配置'
                ], 403);
            }

            DB::beginTransaction();

            // 软删除：设置为非活跃状态
            $permission->update(['is_active' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '删除权限配置成功'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '删除权限配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取有效权限配置
     */
    public function getEffectivePermission(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1'
            ]);

            $permission = ExperimentCatalogDeletePermission::getEffectivePermission(
                $validated['organization_type'],
                $validated['organization_id']
            );

            if ($permission) {
                $result = [
                    'allow_school_delete' => $permission->allow_school_delete,
                    'require_delete_reason' => $permission->require_delete_reason,
                    'max_delete_percentage' => $permission->max_delete_percentage,
                    'delete_rules' => $permission->delete_rules,
                    'source' => [
                        'organization_type' => $permission->organization_type,
                        'organization_id' => $permission->organization_id,
                        'organization_name' => $permission->organization_name,
                        'is_inherited' => $permission->organization_type !== $validated['organization_type'] || 
                                        $permission->organization_id !== $validated['organization_id']
                    ]
                ];
            } else {
                $result = array_merge(
                    ExperimentCatalogDeletePermission::getDefaultPermission(),
                    [
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
                'message' => '获取有效权限配置失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取学校删除统计
     */
    public function getSchoolDeleteStatistics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'organization_type' => ['required', Rule::in(['province', 'city', 'county'])],
                'organization_id' => 'required|integer|min:1'
            ]);

            // 检查权限
            if (!$this->canManageOrganization($user, $validated['organization_type'], $validated['organization_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => '无权限查看该组织的统计信息'
                ], 403);
            }

            $statistics = ExperimentCatalogDeletePermission::getSchoolDeleteStatistics(
                $validated['organization_type'],
                $validated['organization_id']
            );

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取删除统计失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否可以管理删除权限配置
     */
    private function canManageDeletePermissions($user): bool
    {
        // 只有省、市、区县级管理员可以管理删除权限配置
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
     * 检查是否可以查看权限配置
     */
    private function canViewPermission($user, ExperimentCatalogDeletePermission $permission): bool
    {
        return $this->canManageOrganization($user, $permission->organization_type, $permission->organization_id);
    }

    /**
     * 检查是否可以编辑权限配置
     */
    private function canEditPermission($user, ExperimentCatalogDeletePermission $permission): bool
    {
        return $this->canManageOrganization($user, $permission->organization_type, $permission->organization_id);
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
