<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * 用户登录
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => '用户名或密码错误'
            ], 401);
        }

        $user = auth('api')->user();

        // 更新最后登录时间
        $user->update(['last_login_at' => now()]);

        // 获取用户权限
        $permissions = $user->getPermissions();

        // 获取用户角色信息
        $roles = $user->roles()->with('permissions')->get();
        $roleInfo = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'code' => $role->code,
                'level' => $role->level,
                'description' => $role->description
            ];
        });

        // 获取组织信息
        $organizationName = null;
        if ($user->organization_type === 'school' && $user->school) {
            $organizationName = $user->school->name;
        } elseif ($user->organization_type === 'region' && $user->organization_id) {
            // 获取行政区域名称
            $region = \App\Models\AdministrativeRegion::find($user->organization_id);
            $organizationName = $region ? $region->name : null;
        }

        return response()->json([
            'success' => true,
            'message' => '登录成功',
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'real_name' => $user->real_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'department' => $user->department,
                    'position' => $user->position,
                    'school_id' => $user->school_id,
                    'school_name' => $user->school_name,
                    'organization_id' => $user->organization_id,
                    'organization_type' => $user->organization_type,
                    'organization_level' => $user->organization_level,
                    'organization_name' => $organizationName,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                    'permissions' => $permissions,
                    'roles' => $roleInfo
                ]
            ]
        ]);
    }

    /**
     * 用户注册
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'real_name' => 'required|string|max:50',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'real_name' => $request->real_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => User::STATUS_ACTIVE
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => '注册成功',
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'real_name' => $user->real_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status
                ]
            ]
        ], 201);
    }

    /**
     * 获取当前用户信息
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        // 获取用户权限
        $permissions = $user->getPermissions();

        // 获取用户角色信息
        $roles = $user->roles()->with('permissions')->get();
        $roleInfo = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'code' => $role->code,
                'level' => $role->level,
                'description' => $role->description
            ];
        });

        // 获取组织信息
        $organizationName = null;
        if ($user->organization_type === 'school' && $user->school) {
            $organizationName = $user->school->name;
        } elseif ($user->organization_type === 'region' && $user->organization_id) {
            // 获取行政区域名称
            $region = \App\Models\AdministrativeRegion::find($user->organization_id);
            $organizationName = $region ? $region->name : null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'real_name' => $user->real_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'department' => $user->department,
                'position' => $user->position,
                'school_id' => $user->school_id,
                'school_name' => $user->school_name,
                'organization_id' => $user->organization_id,
                'organization_type' => $user->organization_type,
                'organization_level' => $user->organization_level,
                'organization_name' => $organizationName,
                'gender' => $user->gender,
                'birthday' => $user->birthday,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'permissions' => $permissions,
                'roles' => $roleInfo
            ]
        ]);
    }

    /**
     * 用户登出
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => '登出成功'
        ]);
    }

    /**
     * 刷新token
     */
    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]
        ]);
    }
}
