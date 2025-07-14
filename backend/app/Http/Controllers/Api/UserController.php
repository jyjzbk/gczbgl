<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * 获取用户列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

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
            $query->where('role', $request->role);
        }

        // 状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 分页
        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $users->items(),
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
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
            'status' => 1, // 默认启用
        ]);

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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'real_name', 'email', 'phone', 'role', 'department', 'position', 'status'
        ]));

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
