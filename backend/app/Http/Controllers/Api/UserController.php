<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdministrativeRegion;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Middleware\DataScopeMiddleware;

class UserController extends Controller
{
    /**
     * 获取用户列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'school']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'users');

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('real_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 角色筛选
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'teacher') {
                // 当筛选教师时，包含所有教师相关角色
                $query->where(function($q) {
                    $q->where('role', 'teacher')
                      ->orWhere('role', 'school_teacher')
                      ->orWhere('role', 'subject_teacher')
                      ->orWhere('role', '任课教师');
                });
            } else {
                $query->where('role', $role);
            }
        }

        // 状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 组织级别筛选
        if ($request->filled('organization_level')) {
            $query->where('organization_level', $request->organization_level);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

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

        // 更新分页数据的items
        $paginationData = $users->toArray();
        $paginationData['data'] = $usersWithOrganization->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $usersWithOrganization->toArray(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * 创建用户
     */
    public function store(Request $request): JsonResponse
    {
        // 获取所有有效的角色代码
        $validRoles = \App\Models\Role::where('status', 1)->pluck('code')->toArray();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:20|unique:users',
            'real_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in($validRoles)],
            'password' => 'required|string|min:6',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        // 获取当前用户信息，用于设置新用户的组织归属
        $currentUser = auth()->user();
        $permissionService = app(\App\Services\PermissionService::class);
        $currentUserDataScope = $permissionService->getUserDataScope($currentUser);

        // 根据角色确定组织信息
        $roleInfo = \App\Models\Role::where('code', $request->role)->first();
        $organizationLevel = $roleInfo ? $roleInfo->level : 5; // 默认为学校级别

        // 设置组织归属信息
        $organizationId = $request->organization_id;
        $organizationType = 'region';
        $schoolId = $request->school_id;

        if ($organizationLevel == 5) {
            // 学校级用户
            if ($schoolId) {
                $organizationId = $schoolId;
                $organizationType = 'school';
            } else {
                // 如果没有指定学校，使用当前用户可管理的第一个学校
                $manageableSchools = $currentUserDataScope['school_ids'];
                if (!empty($manageableSchools)) {
                    $schoolId = $manageableSchools[0];
                    $organizationId = $schoolId;
                    $organizationType = 'school';
                }
            }
        } else {
            // 区域级用户
            if ($organizationId) {
                // 使用前端指定的组织ID
                $organizationType = 'region';
            } else {
                // 如果没有指定组织，使用当前用户可管理的区域
                $manageableRegions = $currentUserDataScope['region_ids'] ?? [];
                if (!empty($manageableRegions)) {
                    $organizationId = $manageableRegions[0];
                    $organizationType = 'region';
                }
            }
        }

        $user = User::create([
            'username' => $request->username,
            'real_name' => $request->real_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'position' => $request->position,
            'school_id' => $schoolId,
            'organization_id' => $organizationId,
            'organization_type' => $organizationType,
            'organization_level' => $organizationLevel,
            'status' => 1, // 默认启用
        ]);

        // 分配角色
        if ($roleInfo) {
            $user->roles()->attach($roleInfo->id, [
                'scope_type' => $organizationType,
                'scope_id' => $organizationId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 重新加载用户数据，包含角色信息
        $user->load(['roles', 'school']);

        return response()->json([
            'success' => true,
            'message' => '用户创建成功',
            'data' => $user
        ], 201);
    }

    /**
     * 获取单个用户
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * 更新用户
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // 获取所有有效的角色代码
        $validRoles = \App\Models\Role::where('status', 1)->pluck('code')->toArray();

        $validator = Validator::make($request->all(), [
            'real_name' => 'required|string|max:50',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in($validRoles)],
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'status' => 'nullable|in:0,1',
            'school_id' => 'nullable|exists:schools,id',
            'organization_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        // 检查角色是否发生变化，如果变化需要更新组织信息
        $roleChanged = $request->role !== $user->role;
        $organizationChanged = $request->filled('organization_id') || $request->filled('school_id');

        if ($roleChanged || $organizationChanged) {
            // 获取新角色信息
            $roleInfo = \App\Models\Role::where('code', $request->role)->first();
            $organizationLevel = $roleInfo ? $roleInfo->level : 5;

            // 设置组织归属信息
            $organizationId = $request->organization_id ?? $user->organization_id;
            $organizationType = 'region';
            $schoolId = $request->school_id ?? $user->school_id;

            if ($organizationLevel == 5) {
                // 学校级用户
                if ($schoolId) {
                    $organizationId = $schoolId;
                    $organizationType = 'school';
                }
            } else {
                // 区域级用户
                $organizationType = 'region';
            }

            // 更新组织信息
            $user->update([
                'real_name' => $request->real_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'department' => $request->department,
                'position' => $request->position,
                'status' => $request->status ?? $user->status,
                'school_id' => $schoolId,
                'organization_id' => $organizationId,
                'organization_type' => $organizationType,
                'organization_level' => $organizationLevel,
            ]);

            // 更新用户角色关联
            if ($roleInfo) {
                $user->roles()->sync([
                    $roleInfo->id => [
                        'scope_type' => $organizationType,
                        'scope_id' => $organizationId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }
        } else {
            // 只更新基本信息
            $user->update($request->only([
                'real_name', 'email', 'phone', 'department', 'position', 'status'
            ]));
        }

        // 重新加载用户数据
        $user->load(['roles', 'school']);

        return response()->json([
            'success' => true,
            'message' => '用户更新成功',
            'data' => $user
        ]);
    }

    /**
     * 删除用户
     */
    public function destroy(User $user): JsonResponse
    {
        // 防止删除自己
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => '不能删除自己的账号'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => '用户删除成功'
        ]);
    }

    /**
     * 重置用户密码
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => '密码重置成功'
        ]);
    }

    /**
     * 获取用户个人资料
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * 更新用户个人资料
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'real_name' => 'required|string|max:50',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'real_name', 'email', 'phone', 'department', 'position', 'bio'
        ]));

        return response()->json([
            'success' => true,
            'message' => '个人资料更新成功',
            'data' => $user
        ]);
    }
}
