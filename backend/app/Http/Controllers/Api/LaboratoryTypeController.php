<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaboratoryTypeController extends Controller
{
    /**
     * 获取实验室类型列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = LaboratoryType::query();

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 排序
        $query->ordered();

        // 分页
        if ($request->filled('per_page')) {
            $data = $query->paginate($request->per_page);
        } else {
            $data = $query->get();
        }

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $data
        ]);
    }

    /**
     * 创建实验室类型
     */
    public function store(Request $request): JsonResponse
    {
        // 权限检查：只有省级管理员可以创建
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理实验室类型'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:laboratory_types,code',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        $laboratoryType = LaboratoryType::create($validated);

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $laboratoryType
        ], 201);
    }

    /**
     * 显示指定实验室类型
     */
    public function show(LaboratoryType $laboratoryType): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $laboratoryType
        ]);
    }

    /**
     * 更新实验室类型
     */
    public function update(Request $request, LaboratoryType $laboratoryType): JsonResponse
    {
        // 权限检查：只有省级管理员可以更新
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理实验室类型'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:50', Rule::unique('laboratory_types')->ignore($laboratoryType->id)],
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        $laboratoryType->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $laboratoryType
        ]);
    }

    /**
     * 删除实验室类型
     */
    public function destroy(Request $request, LaboratoryType $laboratoryType): JsonResponse
    {
        // 权限检查：只有省级管理员可以删除
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理实验室类型'
            ], 403);
        }

        // 检查是否有关联的实验室
        if ($laboratoryType->laboratories()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该类型下还有实验室，无法删除'
            ], 400);
        }

        $laboratoryType->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 批量更新排序
     */
    public function updateSort(Request $request): JsonResponse
    {
        // 权限检查：只有省级管理员可以更新排序
        if (!$this->hasProvincePermission($request)) {
            return response()->json([
                'code' => 403,
                'message' => '只有省级管理员可以管理实验室类型'
            ], 403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:laboratory_types,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['items'] as $item) {
            LaboratoryType::where('id', $item['id'])
                         ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'code' => 200,
            'message' => '排序更新成功'
        ]);
    }

    /**
     * 检查是否有省级权限
     */
    private function hasProvincePermission(Request $request): bool
    {
        $user = $request->user();

        // 检查用户是否有省级管理员角色
        return $user && $user->hasRole('province_admin');
    }
}
