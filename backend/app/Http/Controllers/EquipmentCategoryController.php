<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class EquipmentCategoryController extends Controller
{
    /**
     * 获取设备分类列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = EquipmentCategory::with(['parent', 'children']);

        // 筛选条件
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 排序
        $query->orderBy('sort_order')->orderBy('id');

        // 分页或全部
        if ($request->boolean('all')) {
            $categories = $query->get();
            return response()->json([
                'code' => 200,
                'message' => '查询成功',
                'data' => $categories
            ]);
        }

        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => [
                'items' => $categories->items(),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total()
                ]
            ]
        ]);
    }

    /**
     * 创建设备分类
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:equipment_categories,code',
            'parent_id' => 'nullable|exists:equipment_categories,id',
            'level' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1'
        ]);

        $data = $request->only([
            'name', 'code', 'parent_id', 'level', 'sort_order', 'status'
        ]);

        // 设置默认值
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['status'] = $data['status'] ?? EquipmentCategory::STATUS_ACTIVE;

        // 验证层级关系
        if ($data['parent_id']) {
            $parent = EquipmentCategory::find($data['parent_id']);
            if ($parent && $parent->level >= $data['level']) {
                return response()->json([
                    'code' => 422,
                    'message' => '子分类级别必须大于父分类级别'
                ], 422);
            }
        }

        $category = EquipmentCategory::create($data);
        $category->load(['parent', 'children']);

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $category
        ], 201);
    }

    /**
     * 获取设备分类详情
     */
    public function show(EquipmentCategory $equipmentCategory): JsonResponse
    {
        $equipmentCategory->load(['parent', 'children', 'equipments']);

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $equipmentCategory
        ]);
    }

    /**
     * 更新设备分类
     */
    public function update(Request $request, EquipmentCategory $equipmentCategory): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipment_categories', 'code')->ignore($equipmentCategory->id)
            ],
            'parent_id' => 'nullable|exists:equipment_categories,id',
            'level' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1'
        ]);

        $data = $request->only([
            'name', 'code', 'parent_id', 'level', 'sort_order', 'status'
        ]);

        // 验证不能将自己设为父分类
        if ($data['parent_id'] == $equipmentCategory->id) {
            return response()->json([
                'code' => 422,
                'message' => '不能将自己设为父分类'
            ], 422);
        }

        // 验证不能将子分类设为父分类
        if ($data['parent_id']) {
            $descendantIds = $equipmentCategory->getDescendantIds();
            if ($descendantIds->contains($data['parent_id'])) {
                return response()->json([
                    'code' => 422,
                    'message' => '不能将子分类设为父分类'
                ], 422);
            }

            // 验证层级关系
            $parent = EquipmentCategory::find($data['parent_id']);
            if ($parent && $parent->level >= $data['level']) {
                return response()->json([
                    'code' => 422,
                    'message' => '子分类级别必须大于父分类级别'
                ], 422);
            }
        }

        $equipmentCategory->update($data);
        $equipmentCategory->load(['parent', 'children']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $equipmentCategory
        ]);
    }

    /**
     * 删除设备分类
     */
    public function destroy(EquipmentCategory $equipmentCategory): JsonResponse
    {
        // 检查是否有子分类
        if ($equipmentCategory->children()->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该分类下还有子分类，无法删除'
            ], 422);
        }

        // 检查是否有设备
        if ($equipmentCategory->equipments()->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该分类下还有设备，无法删除'
            ], 422);
        }

        $equipmentCategory->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取分类选项（用于下拉框）
     */
    public function options(Request $request): JsonResponse
    {
        $query = EquipmentCategory::active();

        // 筛选条件
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->orderBy('sort_order')
                           ->orderBy('id')
                           ->get(['id', 'name', 'code', 'parent_id', 'level']);

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $categories
        ]);
    }

    /**
     * 获取分类树形结构
     */
    public function tree(Request $request): JsonResponse
    {
        $query = EquipmentCategory::with(['allChildren' => function($q) {
            $q->where('status', EquipmentCategory::STATUS_ACTIVE)
              ->orderBy('sort_order')
              ->orderBy('id');
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        $categories = $query->whereNull('parent_id')
                           ->orderBy('sort_order')
                           ->orderBy('id')
                           ->get();

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $categories
        ]);
    }

    /**
     * 批量更新排序
     */
    public function updateSort(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:equipment_categories,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->items as $item) {
            EquipmentCategory::where('id', $item['id'])
                           ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'code' => 200,
            'message' => '排序更新成功'
        ]);
    }
}
