<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\AdministrativeRegion;

class AdministrativeRegionController extends Controller
{
    /**
     * 获取行政区域列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = AdministrativeRegion::with(['parent', 'children']);

        // 按级别筛选
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // 按父级ID筛选
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
