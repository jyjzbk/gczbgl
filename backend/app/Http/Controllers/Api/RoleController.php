<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * 获取角色列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::query();

        // 按级别筛选
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // 按状态筛选
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // 搜索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('level')->orderBy('id');

        // 如果请求参数中包含 all=true，则返回所有数据不分页
        if ($request->get('all') === 'true') {
            $roles = $query->get();

            // 添加统计信息
            $roles->each(function ($role) {
                $role->user_count = \App\Models\User::where('role', $role->code)->count();
                $role->permission_count = $role->permissions()->count();
            });

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        }

        // 默认分页返回
        $roles = $query->paginate($request->get('per_page', 15));

        // 添加统计信息
        $roles->getCollection()->each(function ($role) {
            $role->user_count = \App\Models\User::where('role', $role->code)->count();
            $role->permission_count = $role->permissions()->count();
        });

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * 创建角色
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:roles',
            'level' => 'required|integer|in:1,2,3,4,5',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create($request->all());

        return response()->json([
            'success' => true,
            'message' => '角色创建成功',
            'data' => $role
        ], 201);
    }

    /**
     * 获取角色详情
     */
    public function show(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * 更新角色
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:roles,code,' . $id,
            'level' => 'required|integer|in:1,2,3,4,5',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $role->update($request->all());

        return response()->json([
            'success' => true,
            'message' => '角色更新成功',
            'data' => $role
        ]);
    }

    /**
     * 删除角色
     */
    public function destroy(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        // 检查是否有用户使用此角色
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => '该角色正在被使用，无法删除'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => '角色删除成功'
        ]);
    }

    /**
     * 获取角色权限
     */
    public function getPermissions(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        // 先检查数据库中是否有自定义权限
        $customPermissions = $role->getPermissionCodes();

        // 如果没有自定义权限，使用默认权限
        if (empty($customPermissions)) {
            $permissions = $this->getDefaultPermissionsByRole($role);
        } else {
            $permissions = $customPermissions;
        }

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * 根据角色获取默认权限配置
     */
    private function getDefaultPermissionsByRole(Role $role): array
    {
        $permissionMap = [
            // 省级角色
            'province_admin' => [
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit', 'user.export', 'user.reset_password',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'statistics', 'statistics.view', 'statistics.dashboard', 'statistics.experiment', 'statistics.equipment', 'statistics.user', 'statistics.performance', 'statistics.export',
                'system', 'system.read', 'log', 'log.read'
            ],
            'province_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 市级角色
            'city_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow', 'equipment.maintenance'
            ],
            'city_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 区县级角色
            'county_admin' => [
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow', 'equipment.maintenance'
            ],
            'county_researcher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],

            // 学区级角色
            'district_admin' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],

            // 学校级角色
            'school_principal' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list'
            ],
            'school_dean' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],
            'school_experimenter' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow', 'equipment.maintenance'
            ],
            'school_teacher' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow'
            ],
            'school_student' => [
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.borrow'
            ]
        ];

        return $permissionMap[$role->code] ?? [];
    }

    /**
     * 分配角色权限
     */
    public function assignPermissions(Request $request, string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \Log::info('分配角色权限开始', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions' => $request->permissions
            ]);

            // 删除现有权限
            $deletedCount = $role->permissions()->delete();
            \Log::info('删除现有权限', ['deleted_count' => $deletedCount]);

            // 添加新权限
            $permissionData = [];
            foreach ($request->permissions as $permissionCode) {
                $permissionData[] = [
                    'role_id' => $role->id,
                    'permission_code' => $permissionCode,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($permissionData)) {
                \App\Models\RolePermission::insert($permissionData);
                \Log::info('插入新权限', ['permission_count' => count($permissionData)]);
            } else {
                \Log::info('没有权限需要插入');
            }

            return response()->json([
                'success' => true,
                'message' => '权限分配成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '权限分配失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取角色默认权限配置
     */
    public function getDefaultPermissions(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => '角色不存在'
            ], 404);
        }

        // 根据角色级别返回默认权限配置
        $defaultPermissions = $this->getDefaultPermissionsByLevel($role->level);

        return response()->json([
            'success' => true,
            'data' => $defaultPermissions
        ]);
    }

    /**
     * 根据角色级别获取默认权限
     */
    private function getDefaultPermissionsByLevel(int $level): array
    {
        $permissionMap = [
            1 => [ // 省级管理员
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit', 'user.export', 'user.reset_password',
                'role', 'role.list', 'role.create', 'role.update', 'role.delete',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
                'system', 'system.read', 'log', 'log.read'
            ],
            2 => [ // 市级管理员
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance'
            ],
            3 => [ // 区县管理员
                'user', 'user.list', 'user.create', 'user.update', 'user.delete', 'user.edit',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance'
            ],
            4 => [ // 学区管理员
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update'
            ],
            5 => [ // 学校管理员
                'user', 'user.list', 'user.create', 'user.update',
                'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
                'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.borrow'
            ]
        ];

        return $permissionMap[$level] ?? [];
    }
}
