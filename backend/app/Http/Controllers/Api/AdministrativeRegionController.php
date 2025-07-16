<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\DataScopeMiddleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\AdministrativeRegion;

class AdministrativeRegionController extends Controller
{
    /**
     * 获取行政区域列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = AdministrativeRegion::with(['parent', 'children']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'administrative_regions');

        // 按级别筛选
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // 按父级ID筛选
        if ($request->has('parent_id')) {
            // 验证用户是否可以访问指定区域
            if (DataScopeMiddleware::canAccess($request, 'region', $request->parent_id)) {
                $query->where('parent_id', $request->parent_id);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定区域的数据'
                ], 403);
            }
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

        // 是否返回树形结构
        if ($request->get('tree', false)) {
            $regions = $query->where('parent_id', null)
                           ->orderBy('sort_order')
                           ->get();

            // 递归加载子级
            $this->loadChildren($regions);

            return response()->json([
                'success' => true,
                'data' => $regions
            ]);
        }

        $regions = $query->orderBy('level')
                        ->orderBy('sort_order')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $regions
        ]);
    }

    /**
     * 递归加载子级区域
     */
    private function loadChildren($regions)
    {
        foreach ($regions as $region) {
            $region->load(['children' => function($query) {
                $query->orderBy('sort_order');
            }]);

            if ($region->children->count() > 0) {
                $this->loadChildren($region->children);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:administrative_regions',
            'name' => 'required|string|max:100',
            'level' => 'required|integer|between:1,4',
            'parent_id' => 'nullable|exists:administrative_regions,id',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['code', 'name', 'level', 'parent_id', 'sort_order', 'status']);

        // 验证创建权限
        if ($data['parent_id'] && !DataScopeMiddleware::canAccess($request, 'region', $data['parent_id'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权限在指定区域下创建子区域'
            ], 403);
        }

        $region = AdministrativeRegion::create($data);
        $region->load('parent');

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $region
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, AdministrativeRegion $region): JsonResponse
    {
        // 验证访问权限
        if (!DataScopeMiddleware::canAccess($request, 'region', $region->id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权访问该区域信息'
            ], 403);
        }

        $region->load(['parent', 'children', 'schools']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $region
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdministrativeRegion $region): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('administrative_regions')->ignore($region->id)
            ],
            'name' => 'required|string|max:100',
            'level' => 'required|integer|between:1,4',
            'parent_id' => 'nullable|exists:administrative_regions,id',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['code', 'name', 'level', 'parent_id', 'sort_order', 'status']);

        // 验证更新权限
        if (!DataScopeMiddleware::canUpdate($request, $region, $data)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限更新该区域或修改其归属'
            ], 403);
        }

        $region->update($data);
        $region->load('parent');

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $region
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, AdministrativeRegion $region): JsonResponse
    {
        // 验证删除权限
        if (!DataScopeMiddleware::canAccess($request, 'region', $region->id)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限删除该区域'
            ], 403);
        }

        // 检查是否有子区域
        if ($region->children()->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该区域下还有子区域，无法删除'
            ], 422);
        }

        // 检查是否有关联学校
        if ($region->schools()->count() > 0) {
            return response()->json([
                'code' => 422,
                'message' => '该区域下还有学校，无法删除'
            ], 422);
        }

        $region->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }
}
