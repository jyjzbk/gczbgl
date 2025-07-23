<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TextbookVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TextbookVersionController extends Controller
{
    /**
     * 获取教材版本列表
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // 如果是获取选项列表（用于下拉菜单），不需要权限检查
            $isOptionsRequest = $request->boolean('options', false);

            if (!$isOptionsRequest) {
                // 权限检查：只有省级和市级用户可以管理教材版本
                $user = auth()->user();
                if (!$this->canManageTextbookVersions($user)) {
                    return response()->json([
                        'success' => false,
                        'message' => '权限不足：只有省级和市级管理员可以管理教材版本'
                    ], 403);
                }
            }

            $query = TextbookVersion::query();

            // 如果是选项请求，只返回启用的版本
            if ($isOptionsRequest) {
                $query->where('status', 1);
            } else {
                // 状态筛选
                if ($request->has('status')) {
                    $query->where('status', $request->status);
                }
            }

            // 搜索
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('publisher', 'like', "%{$search}%");
                });
            }

            // 排序
            $query->ordered();

            // 如果是选项请求，不分页
            if ($isOptionsRequest) {
                $versions = $query->get(['id', 'name', 'code', 'publisher']);
                return response()->json([
                    'success' => true,
                    'data' => $versions
                ]);
            }

            // 分页
            if ($request->boolean('paginate', true)) {
                $perPage = $request->input('per_page', 15);
                $versions = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => $versions->items(),
                        'pagination' => [
                            'current_page' => $versions->currentPage(),
                            'last_page' => $versions->lastPage(),
                            'per_page' => $versions->perPage(),
                            'total' => $versions->total()
                        ]
                    ]
                ]);
            } else {
                $versions = $query->get();
                return response()->json([
                    'success' => true,
                    'data' => $versions
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取教材版本列表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 创建教材版本
     */
    public function store(Request $request): JsonResponse
    {
        // 权限检查
        $user = auth()->user();
        if (!$this->canManageTextbookVersions($user)) {
            return response()->json([
                'success' => false,
                'message' => '权限不足：只有省级和市级管理员可以管理教材版本'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:textbook_versions,code',
            'publisher' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0'
        ], [
            'name.required' => '版本名称不能为空',
            'code.required' => '版本代码不能为空',
            'code.unique' => '版本代码已存在',
            'status.in' => '状态值无效'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $version = TextbookVersion::create([
                'name' => $request->name,
                'code' => $request->code,
                'publisher' => $request->publisher,
                'description' => $request->description,
                'status' => $request->input('status', 1),
                'sort_order' => $request->input('sort_order', 0)
            ]);

            return response()->json([
                'success' => true,
                'message' => '教材版本创建成功',
                'data' => $version
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '创建教材版本失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取教材版本详情
     */
    public function show($id): JsonResponse
    {
        try {
            $version = TextbookVersion::with(['chapters', 'experimentCatalogs'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $version
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取教材版本详情失败：' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * 更新教材版本
     */
    public function update(Request $request, $id): JsonResponse
    {
        // 权限检查
        $user = auth()->user();
        if (!$this->canManageTextbookVersions($user)) {
            return response()->json([
                'success' => false,
                'message' => '权限不足：只有省级和市级管理员可以管理教材版本'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:textbook_versions,code,' . $id,
            'publisher' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0'
        ], [
            'name.required' => '版本名称不能为空',
            'code.required' => '版本代码不能为空',
            'code.unique' => '版本代码已存在',
            'status.in' => '状态值无效'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $version = TextbookVersion::findOrFail($id);
            
            $version->update([
                'name' => $request->name,
                'code' => $request->code,
                'publisher' => $request->publisher,
                'description' => $request->description,
                'status' => $request->input('status', $version->status),
                'sort_order' => $request->input('sort_order', $version->sort_order)
            ]);

            return response()->json([
                'success' => true,
                'message' => '教材版本更新成功',
                'data' => $version
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新教材版本失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除教材版本
     */
    public function destroy($id): JsonResponse
    {
        // 权限检查
        $user = auth()->user();
        if (!$this->canManageTextbookVersions($user)) {
            return response()->json([
                'success' => false,
                'message' => '权限不足：只有省级和市级管理员可以管理教材版本'
            ], 403);
        }

        try {
            $version = TextbookVersion::findOrFail($id);

            // 检查是否有关联的章节或实验目录
            $chaptersCount = $version->chapters()->count();
            $catalogsCount = $version->experimentCatalogs()->count();

            if ($chaptersCount > 0 || $catalogsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => '该教材版本下还有章节或实验目录，无法删除'
                ], 400);
            }

            $version->delete();

            return response()->json([
                'success' => true,
                'message' => '教材版本删除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '删除教材版本失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量更新状态
     */
    public function batchUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:textbook_versions,id',
            'status' => 'required|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $affected = TextbookVersion::whereIn('id', $request->ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "成功更新 {$affected} 个教材版本的状态"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '批量更新状态失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新排序
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sort_data' => 'required|array|min:1',
            'sort_data.*.id' => 'required|integer|exists:textbook_versions,id',
            'sort_data.*.sort_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->sort_data as $item) {
                TextbookVersion::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => '排序更新成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新排序失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取教材版本选项（用于下拉菜单）
     */
    public function options(Request $request): JsonResponse
    {
        try {
            $versions = TextbookVersion::where('status', 1)
                ->ordered()
                ->get(['id', 'name', 'code', 'publisher']);

            return response()->json([
                'success' => true,
                'data' => $versions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取教材版本选项失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查用户是否可以管理教材版本
     */
    private function canManageTextbookVersions($user): bool
    {
        if (!$user) {
            return false;
        }

        // 省级和市级用户可以管理教材版本
        $userLevel = $user->organization_level ?? 5;
        return $userLevel <= 2;
    }
}
