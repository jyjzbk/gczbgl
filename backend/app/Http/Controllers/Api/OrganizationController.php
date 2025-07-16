<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeRegion;
use App\Models\School;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 获取当前用户可管理的组织列表
     */
    public function getManageableOrganizations(Request $request): JsonResponse
    {
        $user = auth()->user();
        $level = $request->get('level', 2); // 默认获取市级

        $dataScope = $this->permissionService->getUserDataScope($user);
        
        if ($dataScope['type'] === 'all') {
            // 超级管理员，获取所有指定级别的组织
            $organizations = AdministrativeRegion::where('level', $level)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'level', 'parent_id']);
        } else {
            // 根据用户权限获取可管理的组织
            $regionIds = $dataScope['region_ids'] ?? [];
            
            if (empty($regionIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // 获取用户可管理的指定级别的下级组织
            $organizations = AdministrativeRegion::where('level', $level)
                ->where('status', 1)
                ->where(function($query) use ($regionIds, $level, $user) {
                    if ($user->organization_level < $level) {
                        // 如果用户级别高于目标级别，获取下级组织
                        $query->whereIn('parent_id', $regionIds)
                              ->orWhereIn('id', $regionIds);
                    } else {
                        // 如果用户级别等于或低于目标级别，只能管理自己的组织
                        $query->whereIn('id', $regionIds);
                    }
                })
                ->orderBy('sort_order')
                ->get(['id', 'name', 'level', 'parent_id']);
        }

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * 获取当前用户可管理的学校列表
     */
    public function getManageableSchools(Request $request): JsonResponse
    {
        $user = auth()->user();
        $dataScope = $this->permissionService->getUserDataScope($user);
        
        if ($dataScope['type'] === 'all') {
            // 超级管理员，获取所有学校
            $schools = School::where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'level', 'region_id']);
        } else {
            // 根据用户权限获取可管理的学校
            $schoolIds = $dataScope['school_ids'] ?? [];
            
            if (empty($schoolIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $schools = School::whereIn('id', $schoolIds)
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'level', 'region_id']);
        }

        return response()->json([
            'success' => true,
            'data' => $schools
        ]);
    }

    /**
     * 获取组织树结构
     */
    public function getOrganizationTree(Request $request): JsonResponse
    {
        $user = auth()->user();
        $dataScope = $this->permissionService->getUserDataScope($user);
        
        if ($dataScope['type'] === 'all') {
            // 超级管理员，获取完整的组织树
            $regions = AdministrativeRegion::where('status', 1)
                ->orderBy('level')
                ->orderBy('sort_order')
                ->get();
        } else {
            // 根据用户权限获取组织树
            $regionIds = $dataScope['region_ids'] ?? [];
            
            if (empty($regionIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // 获取用户可管理的组织及其下级组织
            $allRegionIds = $regionIds;

            // 递归获取所有下级组织ID
            foreach ($regionIds as $regionId) {
                $childIds = $this->getChildRegionIds($regionId);
                $allRegionIds = array_merge($allRegionIds, $childIds);
            }

            $allRegionIds = array_unique($allRegionIds);

            $regions = AdministrativeRegion::where('status', 1)
                ->whereIn('id', $allRegionIds)
                ->orderBy('level')
                ->orderBy('sort_order')
                ->get();
        }

        // 构建树结构并添加统计信息
        if ($dataScope['type'] === 'all') {
            // 超级管理员，从根节点开始构建完整树
            $tree = $this->buildTree($regions->toArray());
        } else {
            // 非超级管理员，从用户可管理的最高级别组织开始构建树
            $tree = $this->buildTreeForUser($regions->toArray(), $regionIds);
        }
        $tree = $this->addStatsToTree($tree);

        return response()->json([
            'success' => true,
            'data' => $tree
        ]);
    }

    /**
     * 获取组织下的用户列表
     */
    public function getOrganizationUsers(Request $request): JsonResponse
    {
        $user = auth()->user();
        $organizationId = $request->get('organization_id');
        $organizationLevel = $request->get('organization_level');

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] !== 'all') {
            $regionIds = $dataScope['region_ids'] ?? [];
            if (!in_array($organizationId, $regionIds)) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该组织的用户数据'
                ], 403);
            }
        }

        // 构建查询
        $query = User::with(['roles']);

        // 根据组织层级过滤用户
        if ($organizationLevel == 5) {
            // 学校级：只显示该学校的用户
            $query->where('school_id', $organizationId);
        } else {
            // 其他级别：显示该组织及其下级组织的用户
            $region = AdministrativeRegion::find($organizationId);
            if (!$region) {
                return response()->json([
                    'code' => 404,
                    'message' => '组织不存在'
                ], 404);
            }

            // 获取下级区域ID
            $childRegionIds = $this->getChildRegionIds($organizationId);
            $childRegionIds[] = $organizationId;

            // 获取这些区域下的学校ID
            $schoolIds = School::whereIn('region_id', $childRegionIds)->pluck('id');

            // 构建查询条件：包含学校用户和组织用户
            $query->where(function($q) use ($schoolIds, $childRegionIds) {
                // 条件1：有school_id的用户（传统学校用户）
                if ($schoolIds->isNotEmpty()) {
                    $q->whereIn('school_id', $schoolIds);
                }

                // 条件2：有organization_id的用户（新的组织用户）
                // 只查询直接属于当前组织或其下级组织的用户
                if (!empty($childRegionIds)) {
                    if ($schoolIds->isNotEmpty()) {
                        $q->orWhereIn('organization_id', $childRegionIds);
                    } else {
                        $q->whereIn('organization_id', $childRegionIds);
                    }
                }
            });
        }

        // 搜索过滤
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('real_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 角色过滤
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('code', $request->role);
            });
        }

        // 状态过滤
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage()
                ]
            ]
        ]);
    }

    /**
     * 获取组织统计信息
     */
    public function getOrganizationStats(Request $request): JsonResponse
    {
        $user = auth()->user();
        $organizationId = $request->get('organization_id');

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] !== 'all') {
            $regionIds = $dataScope['region_ids'] ?? [];
            if (!in_array($organizationId, $regionIds)) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该组织的统计数据'
                ], 403);
            }
        }

        $region = AdministrativeRegion::find($organizationId);
        if (!$region) {
            return response()->json([
                'code' => 404,
                'message' => '组织不存在'
            ], 404);
        }

        // 获取下级区域ID
        $childRegionIds = $this->getChildRegionIds($organizationId);
        $childRegionIds[] = $organizationId;

        // 获取这些区域下的学校ID
        $schoolIds = School::whereIn('region_id', $childRegionIds)->pluck('id');

        // 统计用户数据（包含学校用户和组织用户）
        $userQuery = User::where(function($q) use ($schoolIds, $childRegionIds) {
            // 条件1：有school_id的用户（传统学校用户）
            if ($schoolIds->isNotEmpty()) {
                $q->whereIn('school_id', $schoolIds);
            }

            // 条件2：有organization_id的用户（新的组织用户）
            if (!empty($childRegionIds)) {
                if ($schoolIds->isNotEmpty()) {
                    $q->orWhereIn('organization_id', $childRegionIds);
                } else {
                    $q->whereIn('organization_id', $childRegionIds);
                }
            }
        });

        $totalUsers = $userQuery->count();
        $activeUsers = (clone $userQuery)->where('status', 1)->count();
        $disabledUsers = (clone $userQuery)->where('status', 0)->count();

        // 统计学校数量
        $totalSchools = $schoolIds->count();

        // 统计设备数量（如果有设备表）
        $totalEquipments = 0;
        if (class_exists(Equipment::class) && $schoolIds->isNotEmpty()) {
            $totalEquipments = Equipment::whereIn('school_id', $schoolIds)->count();
        }

        // 统计实验室数量（如果有实验室表）
        $totalLaboratories = 0;
        if (class_exists(Laboratory::class) && $schoolIds->isNotEmpty()) {
            $totalLaboratories = Laboratory::whereIn('school_id', $schoolIds)->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'disabled_users' => $disabledUsers,
                'total_schools' => $totalSchools,
                'total_equipments' => $totalEquipments,
                'total_laboratories' => $totalLaboratories
            ]
        ]);
    }

    /**
     * 获取组织下的学校列表
     */
    public function getOrganizationSchools(Request $request): JsonResponse
    {
        $user = auth()->user();
        $organizationId = $request->get('organization_id');
        $organizationLevel = $request->get('organization_level');

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] !== 'all') {
            $regionIds = $dataScope['region_ids'] ?? [];
            if (!in_array($organizationId, $regionIds)) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该组织的学校数据'
                ], 403);
            }
        }

        // 获取下级区域ID
        $childRegionIds = $this->getChildRegionIds($organizationId);
        $childRegionIds[] = $organizationId;

        // 构建查询
        $query = School::with(['region']);

        // 根据区域过滤学校
        $query->whereIn('region_id', $childRegionIds);

        // 搜索过滤
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 类型过滤
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // 状态过滤
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $schools = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $schools->items(),
                'pagination' => [
                    'current_page' => $schools->currentPage(),
                    'per_page' => $schools->perPage(),
                    'total' => $schools->total(),
                    'last_page' => $schools->lastPage()
                ]
            ]
        ]);
    }

    /**
     * 获取组织下的设备列表
     */
    public function getOrganizationEquipments(Request $request): JsonResponse
    {
        $user = auth()->user();
        $organizationId = $request->get('organization_id');
        $organizationLevel = $request->get('organization_level');

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] !== 'all') {
            $regionIds = $dataScope['region_ids'] ?? [];
            if (!in_array($organizationId, $regionIds)) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该组织的设备数据'
                ], 403);
            }
        }

        // 获取下级区域ID
        $childRegionIds = $this->getChildRegionIds($organizationId);
        $childRegionIds[] = $organizationId;

        // 获取这些区域下的学校ID
        $schoolIds = School::whereIn('region_id', $childRegionIds)->pluck('id');

        if ($schoolIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $request->get('per_page', 20),
                        'total' => 0,
                        'last_page' => 1
                    ]
                ]
            ]);
        }

        // 构建查询
        $query = Equipment::with(['school', 'category']);

        // 根据学校过滤设备
        $query->whereIn('school_id', $schoolIds);

        // 搜索过滤
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // 分类过滤
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // 状态过滤
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // 状况过滤
        if ($request->has('condition') && $request->condition !== '') {
            $query->where('condition', $request->condition);
        }

        // 位置过滤
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $equipments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $equipments->items(),
                'pagination' => [
                    'current_page' => $equipments->currentPage(),
                    'per_page' => $equipments->perPage(),
                    'total' => $equipments->total(),
                    'last_page' => $equipments->lastPage()
                ]
            ]
        ]);
    }

    /**
     * 获取组织下的实验室列表
     */
    public function getOrganizationLaboratories(Request $request): JsonResponse
    {
        $user = auth()->user();
        $organizationId = $request->get('organization_id');
        $organizationLevel = $request->get('organization_level');

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] !== 'all') {
            $regionIds = $dataScope['region_ids'] ?? [];
            if (!in_array($organizationId, $regionIds)) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该组织的实验室数据'
                ], 403);
            }
        }

        // 获取下级区域ID
        $childRegionIds = $this->getChildRegionIds($organizationId);
        $childRegionIds[] = $organizationId;

        // 获取这些区域下的学校ID
        $schoolIds = School::whereIn('region_id', $childRegionIds)->pluck('id');

        if ($schoolIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $request->get('per_page', 20),
                        'total' => 0,
                        'last_page' => 1
                    ]
                ]
            ]);
        }

        // 构建查询
        $query = Laboratory::with(['school']);

        // 根据学校过滤实验室
        $query->whereIn('school_id', $schoolIds);

        // 搜索过滤
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 类型过滤
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // 状态过滤
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $laboratories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $laboratories->items(),
                'pagination' => [
                    'current_page' => $laboratories->currentPage(),
                    'per_page' => $laboratories->perPage(),
                    'total' => $laboratories->total(),
                    'last_page' => $laboratories->lastPage()
                ]
            ]
        ]);
    }

    /**
     * 获取下级区域ID
     */
    private function getChildRegionIds(int $parentId): array
    {
        $childIds = [];
        $directChildren = AdministrativeRegion::where('parent_id', $parentId)->pluck('id');

        foreach ($directChildren as $childId) {
            $childIds[] = $childId;
            $childIds = array_merge($childIds, $this->getChildRegionIds($childId));
        }

        return $childIds;
    }

    /**
     * 为树结构添加统计信息
     */
    private function addStatsToTree(array $tree): array
    {
        foreach ($tree as &$node) {
            // 获取下级区域ID
            $childRegionIds = $this->getChildRegionIds($node['id']);
            $childRegionIds[] = $node['id'];

            // 获取这些区域下的学校ID
            $schoolIds = School::whereIn('region_id', $childRegionIds)->pluck('id');

            // 统计信息
            $node['school_count'] = $schoolIds->count();
            $node['equipment_count'] = 0;
            $node['laboratory_count'] = 0;

            // 统计用户数据（包含学校用户和组织用户）
            $userQuery = User::where(function($q) use ($schoolIds, $childRegionIds) {
                // 条件1：有school_id的用户（传统学校用户）
                if ($schoolIds->isNotEmpty()) {
                    $q->whereIn('school_id', $schoolIds);
                }

                // 条件2：有organization_id的用户（新的组织用户）
                if (!empty($childRegionIds)) {
                    if ($schoolIds->isNotEmpty()) {
                        $q->orWhereIn('organization_id', $childRegionIds);
                    } else {
                        $q->whereIn('organization_id', $childRegionIds);
                    }
                }
            });

            $node['user_count'] = $userQuery->count();

            // 统计设备和实验室
            if ($schoolIds->isNotEmpty()) {
                if (class_exists(Equipment::class)) {
                    $node['equipment_count'] = Equipment::whereIn('school_id', $schoolIds)->count();
                }

                if (class_exists(Laboratory::class)) {
                    $node['laboratory_count'] = Laboratory::whereIn('school_id', $schoolIds)->count();
                }
            }

            // 递归处理子节点
            if (isset($node['children'])) {
                $node['children'] = $this->addStatsToTree($node['children']);
            }
        }

        return $tree;
    }

    /**
     * 为用户构建树结构（从用户可管理的组织开始）
     */
    private function buildTreeForUser(array $items, array $userRegionIds): array
    {
        $tree = [];

        // 找到用户可管理的最高级别组织作为根节点
        $rootNodes = [];
        foreach ($items as $item) {
            if (in_array($item['id'], $userRegionIds)) {
                $rootNodes[] = $item;
            }
        }

        // 按级别排序，取最高级别的组织作为根节点
        usort($rootNodes, function($a, $b) {
            return $a['level'] - $b['level'];
        });

        // 从最高级别的组织开始构建树
        if (!empty($rootNodes)) {
            $minLevel = $rootNodes[0]['level'];
            foreach ($rootNodes as $rootNode) {
                if ($rootNode['level'] == $minLevel) {
                    $children = $this->buildTree($items, $rootNode['id']);
                    if (!empty($children)) {
                        $rootNode['children'] = $children;
                    }
                    $tree[] = $rootNode;
                }
            }
        }

        return $tree;
    }

    /**
     * 构建树结构
     */
    private function buildTree(array $items, int $parentId = 0): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }
}
