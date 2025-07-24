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
            $schoolIds = $dataScope['school_ids'] ?? [];

            // 如果用户有学校权限但没有区域权限（学校管理员），需要获取学校所在的区域路径
            if (empty($regionIds) && !empty($schoolIds)) {
                $schools = School::whereIn('id', $schoolIds)->get();
                foreach ($schools as $school) {
                    if ($school->region_id) {
                        // 获取从根到学校所在区域的完整路径
                        $regionPath = $this->getRegionPath($school->region_id);
                        $regionIds = array_merge($regionIds, $regionPath);
                    }
                }
                $regionIds = array_unique($regionIds);
            }

            if (empty($regionIds) && empty($schoolIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // 获取用户可管理的组织及其下级组织
            $allRegionIds = $regionIds;

            // 对于学校管理员，不需要获取下级组织（学校是最底层）
            if ($dataScope['type'] !== 'school') {
                // 递归获取所有下级组织ID
                foreach ($regionIds as $regionId) {
                    $childIds = $this->getChildRegionIds($regionId);
                    $allRegionIds = array_merge($allRegionIds, $childIds);
                }
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
            $regionsArray = $this->formatRegionsForTree($regions);
            $tree = $this->buildTree($regionsArray);
        } else {
            // 非超级管理员，从用户可管理的最高级别组织开始构建树
            $regionsArray = $this->formatRegionsForTree($regions);
            $tree = $this->buildTreeForUser($regionsArray, $regionIds);

            // 如果用户有学校权限，需要将学校添加到树中
            $schoolIds = $dataScope['school_ids'] ?? [];
            if (!empty($schoolIds)) {
                $schools = School::whereIn('id', $schoolIds)->where('status', 1)->get();
                $tree = $this->addSchoolsToTree($tree, $schools);
            }
        }
        $tree = $this->addStatsToTree($tree);

        return response()->json([
            'success' => true,
            'data' => $tree
        ]);
    }

    /**
     * 获取用户可编辑的组织信息（树形结构）
     */
    public function getEditableOrganizations(Request $request): JsonResponse
    {
        $user = auth()->user();
        $dataScope = $this->permissionService->getUserDataScope($user);

        // 根据用户权限获取可编辑的组织
        $regionIds = [];
        $schoolIds = [];

        switch ($dataScope['type']) {
            case 'all':
                // 超级管理员可以编辑所有组织
                $regionIds = AdministrativeRegion::where('status', 1)->pluck('id')->toArray();
                $schoolIds = School::where('status', 1)->pluck('id')->toArray();
                break;

            case 'province':
            case 'city':
            case 'county':
            case 'district':
                // 区域管理员可以编辑自己管辖的区域和学校
                $regionIds = $dataScope['region_ids'] ?? [];
                $schoolIds = $dataScope['school_ids'] ?? [];
                break;

            case 'school':
                // 学校管理员只能编辑自己的学校，但需要显示学校所在的区域路径
                $schoolIds = $dataScope['school_ids'] ?? [];
                $regionIds = [];

                // 获取学校所在的区域路径
                if (!empty($schoolIds)) {
                    $schools = School::whereIn('id', $schoolIds)->get();
                    foreach ($schools as $school) {
                        if ($school->region_id) {
                            // 获取从根到学校所在区域的完整路径
                            $regionPath = $this->getRegionPath($school->region_id);
                            $regionIds = array_merge($regionIds, $regionPath);
                        }
                    }
                    $regionIds = array_unique($regionIds);
                }
                break;
        }

        // 获取区域数据
        $regions = AdministrativeRegion::whereIn('id', $regionIds)
            ->where('status', 1)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        // 获取学校数据
        $schools = School::whereIn('id', $schoolIds)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // 构建树形结构
        $tree = $this->buildEditableTree($regions, $schools, $user);

        return response()->json([
            'success' => true,
            'data' => $tree
        ]);
    }

    /**
     * 构建可编辑的组织树
     */
    private function buildEditableTree($regions, $schools, $user): array
    {
        $tree = [];
        $regionMap = [];

        // 首先处理区域数据
        foreach ($regions as $region) {
            // 检查用户是否可以编辑此区域（学校管理员只能查看，不能编辑区域）
            $canEditRegion = $user->organization_level <= $region->level;

            $nodeData = [
                'id' => $region->id,
                'type' => 'region',
                'name' => $region->name,
                'code' => $region->code,
                'level' => $region->level,
                'parent_id' => $region->parent_id,
                'sort_order' => $region->sort_order,
                'address' => $region->address ?? '',
                'contact_person' => $region->contact_person ?? '',
                'contact_phone' => $region->contact_phone ?? '',
                'email' => $region->email ?? '',
                'description' => $region->description ?? '',
                'children' => [],
                'editable_fields' => $canEditRegion ? $this->getEditableFields('region', $user->organization_level, $region->level) : [],
                'stats' => $this->getRegionStats($region->id),
                'readonly' => !$canEditRegion
            ];

            $regionMap[$region->id] = $nodeData;
        }

        // 构建区域树形结构
        foreach ($regionMap as $id => $node) {
            if ($node['parent_id'] && isset($regionMap[$node['parent_id']])) {
                $regionMap[$node['parent_id']]['children'][] = &$regionMap[$id];
            } else {
                $tree[] = &$regionMap[$id];
            }
        }

        // 添加学校到对应的区域节点
        foreach ($schools as $school) {
            $schoolNode = [
                'id' => $school->id,
                'type' => 'school',
                'name' => $school->name,
                'code' => $school->code,
                'level' => 5,
                'region_id' => $school->region_id,
                'school_type' => $school->type, // 添加学校类型字段
                'school_level' => $school->level, // 添加学校管理级别字段
                'address' => $school->address ?? '',
                'contact_person' => $school->contact_person ?? '',
                'contact_phone' => $school->contact_phone ?? '',
                'student_count' => $school->student_count ?? 0,
                'class_count' => $school->class_count ?? 0,
                'teacher_count' => $school->teacher_count ?? 0,
                'children' => [],
                'editable_fields' => $this->getEditableFields('school', $user->organization_level, 5),
                'stats' => $this->getSchoolStats($school->id)
            ];

            // 找到学校所属的区域节点并添加
            $this->addSchoolToRegion($tree, $school->region_id, $schoolNode);
        }

        return $tree;
    }

    /**
     * 将学校添加到对应的区域节点
     */
    private function addSchoolToRegion(array &$tree, int $regionId, array $schoolNode): void
    {
        foreach ($tree as &$node) {
            if ($node['type'] === 'region' && $node['id'] === $regionId) {
                $node['children'][] = $schoolNode;
                return;
            }
            if (!empty($node['children'])) {
                $this->addSchoolToRegion($node['children'], $regionId, $schoolNode);
            }
        }
    }

    /**
     * 获取区域统计信息
     */
    private function getRegionStats(int $regionId): array
    {
        // 获取下级区域数量
        $subRegionCount = AdministrativeRegion::where('parent_id', $regionId)
            ->where('status', 1)
            ->count();

        // 获取直属学校数量
        $schoolCount = School::where('region_id', $regionId)
            ->where('status', 1)
            ->count();

        // 获取用户数量
        $userCount = User::where('organization_id', $regionId)
            ->where('organization_type', 'region')
            ->count();

        return [
            'sub_regions' => $subRegionCount,
            'schools' => $schoolCount,
            'users' => $userCount
        ];
    }

    /**
     * 获取学校统计信息
     */
    private function getSchoolStats(int $schoolId): array
    {
        // 获取学校用户数量
        $userCount = User::where('school_id', $schoolId)->count();

        return [
            'users' => $userCount
        ];
    }

    /**
     * 获取区域的完整路径（从根到指定区域）
     */
    private function getRegionPath(int $regionId): array
    {
        $path = [];
        $currentId = $regionId;

        while ($currentId) {
            $region = AdministrativeRegion::find($currentId);
            if (!$region) {
                break;
            }

            array_unshift($path, $region->id);
            $currentId = $region->parent_id;
        }

        return $path;
    }

    /**
     * 获取可编辑字段列表
     */
    private function getEditableFields(string $orgType, int $userLevel, int $targetLevel): array
    {
        if ($orgType === 'region') {
            // 区域的可编辑字段
            $baseFields = ['name', 'address', 'contact_person', 'contact_phone', 'email', 'description'];
            $restrictedFields = ['code', 'level', 'parent_id'];
        } else {
            // 学校的可编辑字段
            $baseFields = ['name', 'address', 'contact_person', 'contact_phone'];
            $restrictedFields = ['code', 'level', 'region_id', 'student_count', 'class_count', 'teacher_count'];
        }

        // 同级或上级可以编辑更多字段
        if ($userLevel <= $targetLevel) {
            return array_merge($baseFields, $restrictedFields);
        }

        // 下级只能编辑基础字段
        return $baseFields;
    }

    /**
     * 更新组织信息
     */
    public function updateOrganization(Request $request, string $type, int $id): JsonResponse
    {
        $user = auth()->user();

        // 验证权限
        if (!$this->canEditOrganization($user, $type, $id)) {
            return response()->json([
                'success' => false,
                'message' => '没有权限编辑此组织信息'
            ], 403);
        }

        // 根据类型设置不同的验证规则
        if ($type === 'region') {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'code' => 'sometimes|string|max:50',
                'address' => 'sometimes|nullable|string|max:500',
                'contact_person' => 'sometimes|nullable|string|max:100',
                'contact_phone' => 'sometimes|nullable|string|max:20',
                'email' => 'sometimes|nullable|email|max:100',
                'description' => 'sometimes|nullable|string|max:1000',
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'code' => 'sometimes|string|max:50',
                'address' => 'sometimes|string|max:500',
                'contact_person' => 'sometimes|string|max:100',
                'contact_phone' => 'sometimes|string|max:20',
                'student_count' => 'sometimes|integer|min:0',
                'class_count' => 'sometimes|integer|min:0',
                'teacher_count' => 'sometimes|integer|min:0',
            ]);
        }

        try {
            if ($type === 'region') {
                $organization = AdministrativeRegion::findOrFail($id);
            } else {
                $organization = School::findOrFail($id);
            }

            // 获取可编辑字段
            $editableFields = $this->getEditableFields($type, $user->organization_level,
                $type === 'region' ? $organization->level : 5);

            // 只更新允许编辑的字段，并且字段必须存在于模型中
            foreach ($validated as $field => $value) {
                if (in_array($field, $editableFields) && $organization->isFillable($field)) {
                    $organization->$field = $value;
                }
            }

            $organization->save();

            // 记录操作日志
            $this->logOrganizationChange($user, $organization, $validated, $type);

            return response()->json([
                'success' => true,
                'message' => '组织信息更新成功',
                'data' => $organization
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查是否可以编辑组织
     */
    private function canEditOrganization($user, string $type, int $id): bool
    {
        $dataScope = $this->permissionService->getUserDataScope($user);

        if ($dataScope['type'] === 'all') {
            return true;
        }

        if ($type === 'region') {
            return in_array($id, $dataScope['region_ids'] ?? []);
        } else {
            return in_array($id, $dataScope['school_ids'] ?? []);
        }
    }

    /**
     * 记录组织变更日志
     */
    private function logOrganizationChange($user, $organization, array $changes, string $type): void
    {
        // 这里可以实现详细的日志记录
        \Log::info('组织信息变更', [
            'user_id' => $user->id,
            'user_name' => $user->real_name,
            'organization_type' => $type,
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'changes' => $changes,
            'timestamp' => now()
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
            // 对于学校级组织，检查school_ids权限
            if ($organizationLevel == 5) {
                $schoolIds = $dataScope['school_ids'] ?? [];
                if (!in_array($organizationId, $schoolIds)) {
                    return response()->json([
                        'code' => 403,
                        'message' => '无权访问该学校的用户数据'
                    ], 403);
                }
            } else {
                // 对于区域级组织，检查region_ids权限
                $regionIds = $dataScope['region_ids'] ?? [];
                if (!in_array($organizationId, $regionIds)) {
                    return response()->json([
                        'code' => 403,
                        'message' => '无权访问该组织的用户数据'
                    ], 403);
                }
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
        $users = $query->with('school')->paginate($perPage);

        // 为每个用户添加组织名称信息
        $usersWithOrganization = $users->getCollection()->map(function ($user) {
            $organizationName = null;

            // 优先使用school_id获取学校信息
            if ($user->school_id && $user->school) {
                $organizationName = $user->school->name;
            } elseif ($user->organization_id) {
                // 根据organization_type获取对应的组织名称
                if (in_array($user->organization_type, ['province', 'city', 'county', 'district', 'region'])) {
                    // 获取行政区域名称
                    $region = AdministrativeRegion::find($user->organization_id);
                    $organizationName = $region ? $region->name : null;
                } elseif ($user->organization_type === 'school') {
                    // 如果organization_type是school，但没有school_id，尝试通过organization_id获取学校
                    $school = School::find($user->organization_id);
                    $organizationName = $school ? $school->name : null;
                }
            }

            // 添加组织名称到用户数据
            $userData = $user->toArray();
            $userData['organization_name'] = $organizationName;

            return $userData;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $usersWithOrganization->toArray(),
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
        $organizationType = $request->get('organization_type'); // 新增：节点类型参数

        // 验证权限
        $dataScope = $this->permissionService->getUserDataScope($user);

        // 如果明确指定了节点类型，直接按类型处理
        if ($organizationType === 'school') {
            $school = School::find($organizationId);
            if ($school) {
                return $this->getSchoolStatsForOrganization($school, $user, $dataScope);
            } else {
                return response()->json([
                    'code' => 404,
                    'message' => '学校不存在'
                ], 404);
            }
        }

        // 优先检查是否是区域ID（避免与学校ID冲突）
        $region = AdministrativeRegion::find($organizationId);
        if ($region) {
            // 这是区域节点，验证权限
            if ($dataScope['type'] !== 'all') {
                $regionIds = $dataScope['region_ids'] ?? [];
                if (!in_array($organizationId, $regionIds)) {
                    return response()->json([
                        'code' => 403,
                        'message' => '无权访问该组织的统计数据'
                    ], 403);
                }
            }

            // 处理区域统计逻辑
            return $this->getRegionStatsForOrganization($region, $user, $dataScope);
        }

        // 如果不是区域，再检查是否是学校ID
        $school = School::find($organizationId);
        if ($school) {
            // 这是学校节点，返回学校统计信息
            return $this->getSchoolStatsForOrganization($school, $user, $dataScope);
        }

        // 既不是区域也不是学校
        return response()->json([
            'code' => 404,
            'message' => '组织不存在'
        ], 404);
    }

    /**
     * 获取区域详细统计信息（用于组织统计API）
     */
    private function getRegionStatsForOrganization(AdministrativeRegion $region, $user, array $dataScope): JsonResponse
    {
        $regionId = $region->id;

        // 获取下级区域ID
        $childRegionIds = $this->getChildRegionIds($regionId);
        $childRegionIds[] = $regionId;

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
     * 获取学校详细统计信息（用于组织统计API）
     */
    private function getSchoolStatsForOrganization(School $school, $user, array $dataScope): JsonResponse
    {
        // 验证权限：检查用户是否有权限访问该学校
        if ($dataScope['type'] !== 'all') {
            $schoolIds = $dataScope['school_ids'] ?? [];
            $regionIds = $dataScope['region_ids'] ?? [];

            // 检查是否有直接的学校权限或者区域权限
            $hasPermission = in_array($school->id, $schoolIds) ||
                           in_array($school->region_id, $regionIds);

            if (!$hasPermission) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问该学校的统计数据'
                ], 403);
            }
        }

        // 统计该学校的用户数据
        $userQuery = User::where('school_id', $school->id);
        $totalUsers = $userQuery->count();
        $activeUsers = (clone $userQuery)->where('status', 1)->count();
        $disabledUsers = (clone $userQuery)->where('status', 0)->count();

        // 学校数量固定为1（自己）
        $totalSchools = 1;

        // 统计设备数量
        $totalEquipments = 0;
        if (class_exists(Equipment::class)) {
            $totalEquipments = Equipment::where('school_id', $school->id)->count();
        }

        // 统计实验室数量
        $totalLaboratories = 0;
        if (class_exists(Laboratory::class)) {
            $totalLaboratories = Laboratory::where('school_id', $school->id)->count();
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

        // 首先检查是否是学校节点
        $school = School::find($organizationId);
        if ($school) {
            // 这是学校节点，验证学校权限
            if ($dataScope['type'] !== 'all') {
                $schoolIds = $dataScope['school_ids'] ?? [];
                $regionIds = $dataScope['region_ids'] ?? [];

                // 检查是否有直接的学校权限或者区域权限
                $hasPermission = in_array($school->id, $schoolIds) ||
                               in_array($school->region_id, $regionIds);

                if (!$hasPermission) {
                    return response()->json([
                        'code' => 403,
                        'message' => '无权访问该学校的设备数据'
                    ], 403);
                }
            }

            // 直接查询该学校的设备
            $schoolIds = collect([$school->id]);
        } else {
            // 这是区域节点，验证区域权限
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
            // 区分区域节点和学校节点
            if (isset($node['type']) && $node['type'] === 'school') {
                // 学校节点：只统计该学校的数据
                $schoolId = $node['id'];

                // 统计学校用户
                $node['user_count'] = User::where('school_id', $schoolId)->count();
                $node['school_count'] = 0; // 学校节点本身不包含其他学校

                // 统计学校设备和实验室
                if (class_exists(Equipment::class)) {
                    $node['equipment_count'] = Equipment::where('school_id', $schoolId)->count();
                }

                if (class_exists(Laboratory::class)) {
                    $node['laboratory_count'] = Laboratory::where('school_id', $schoolId)->count();
                }
            } else {
                // 区域节点：统计该区域及其下级区域的数据
                $regionId = $node['id'];

                // 获取下级区域ID
                $childRegionIds = $this->getChildRegionIds($regionId);
                $childRegionIds[] = $regionId;

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
     * 格式化区域数据为树结构所需的格式
     */
    private function formatRegionsForTree($regions): array
    {
        $regionsArray = [];
        foreach ($regions as $region) {
            $regionsArray[] = [
                'id' => $region->id,
                'type' => 'region',
                'name' => $region->name,
                'code' => $region->code,
                'level' => $region->level,
                'parent_id' => $region->parent_id,
                'sort_order' => $region->sort_order,
                'address' => $region->address ?? '',
                'contact_person' => $region->contact_person ?? '',
                'contact_phone' => $region->contact_phone ?? '',
                'email' => $region->email ?? '',
                'description' => $region->description ?? '',
                'status' => $region->status,
                'created_at' => $region->created_at,
                'updated_at' => $region->updated_at,
            ];
        }
        return $regionsArray;
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

    /**
     * 将学校添加到组织树中
     */
    private function addSchoolsToTree(array $tree, $schools): array
    {
        foreach ($schools as $school) {
            $schoolNode = [
                'id' => $school->id,
                'type' => 'school',
                'name' => $school->name,
                'code' => $school->code,
                'level' => 5,
                'parent_id' => $school->region_id,
                'region_id' => $school->region_id,
                'school_type' => $school->type, // 添加学校类型字段
                'school_level' => $school->level, // 添加学校管理级别字段
                'address' => $school->address ?? '',
                'contact_person' => $school->contact_person ?? '',
                'contact_phone' => $school->contact_phone ?? '',
                'student_count' => $school->student_count ?? 0,
                'class_count' => $school->class_count ?? 0,
                'teacher_count' => $school->teacher_count ?? 0,
                'children' => [],
                'user_count' => 0, // 将在 addStatsToTree 中计算
                'school_count' => 0,
                'equipment_count' => 0
            ];

            // 找到学校所属的区域节点并添加学校
            $this->addSchoolToTreeNode($tree, $school->region_id, $schoolNode);
        }

        return $tree;
    }

    /**
     * 递归查找区域节点并添加学校
     */
    private function addSchoolToTreeNode(array &$tree, int $regionId, array $schoolNode): bool
    {
        foreach ($tree as &$node) {
            if ($node['id'] == $regionId && $node['type'] == 'region') {
                if (!isset($node['children'])) {
                    $node['children'] = [];
                }
                $node['children'][] = $schoolNode;
                return true;
            }

            if (isset($node['children']) && !empty($node['children'])) {
                if ($this->addSchoolToTreeNode($node['children'], $regionId, $schoolNode)) {
                    return true;
                }
            }
        }

        return false;
    }
}
