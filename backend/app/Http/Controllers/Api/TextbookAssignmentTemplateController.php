<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TextbookAssignmentTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TextbookAssignmentTemplateController extends Controller
{
    /**
     * 获取模板列表
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:0,1',
            'creator_level' => 'nullable|integer|min:1|max:5',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);

        $query = TextbookAssignmentTemplate::with(['creatorUser']);

        // 搜索条件
        if (!empty($validated['search'])) {
            $query->where('name', 'like', '%' . $validated['search'] . '%');
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['creator_level'])) {
            $query->where('creator_level', $validated['creator_level']);
        }

        // 权限过滤：只能看到自己级别及以上创建的模板
        $user = auth()->user();
        $userLevel = $user->organization_level ?? 5;
        $query->where('creator_level', '<=', $userLevel);

        // 分页
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;
        
        $total = $query->count();
        $templates = $query->orderBy('is_default', 'desc')
                          ->orderBy('usage_count', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->offset(($page - 1) * $perPage)
                          ->limit($perPage)
                          ->get();

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'items' => $templates,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ]
            ]
        ]);
    }

    /**
     * 创建模板
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'assignment_config' => 'required|array',
            'assignment_config.*' => 'exists:textbook_versions,id',
            'applicable_grades' => 'required|array|min:1',
            'applicable_grades.*' => 'string|max:20',
            'applicable_school_types' => 'nullable|array',
            'applicable_school_types.*' => 'integer|min:1|max:4',
            'is_default' => 'boolean'
        ]);

        $user = auth()->user();

        // 检查权限：只有省级、市级、区县级可以创建模板
        $userLevel = $user->organization_level ?? 5;
        if ($userLevel > 3) {
            return response()->json([
                'code' => 403,
                'message' => '无权限创建模板'
            ], 403);
        }

        // 如果设置为默认模板，需要取消其他默认模板
        if ($validated['is_default'] ?? false) {
            TextbookAssignmentTemplate::where('creator_level', $userLevel)
                ->where('creator_org_id', $user->organization_id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        }

        // 添加创建者信息
        $validated['creator_level'] = $userLevel;
        $validated['creator_org_id'] = $user->organization_id;
        $validated['creator_org_type'] = $user->organization_type;
        $validated['creator_user_id'] = $user->id;

        $template = TextbookAssignmentTemplate::create($validated);
        $template->load('creatorUser');

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $template
        ], 201);
    }

    /**
     * 获取模板详情
     */
    public function show(int $id): JsonResponse
    {
        $template = TextbookAssignmentTemplate::with(['creatorUser'])->find($id);

        if (!$template) {
            return response()->json([
                'code' => 404,
                'message' => '模板不存在'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $template
        ]);
    }

    /**
     * 更新模板
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $template = TextbookAssignmentTemplate::find($id);

        if (!$template) {
            return response()->json([
                'code' => 404,
                'message' => '模板不存在'
            ], 404);
        }

        $user = auth()->user();

        // 检查权限：只能修改自己创建的模板
        if ($template->creator_user_id !== $user->id) {
            return response()->json([
                'code' => 403,
                'message' => '无权限修改此模板'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'assignment_config' => 'required|array',
            'assignment_config.*' => 'exists:textbook_versions,id',
            'applicable_grades' => 'required|array|min:1',
            'applicable_grades.*' => 'string|max:20',
            'applicable_school_types' => 'nullable|array',
            'applicable_school_types.*' => 'integer|min:1|max:4',
            'is_default' => 'boolean',
            'status' => 'boolean'
        ]);

        // 如果设置为默认模板，需要取消其他默认模板
        if ($validated['is_default'] ?? false) {
            TextbookAssignmentTemplate::where('creator_level', $template->creator_level)
                ->where('creator_org_id', $template->creator_org_id)
                ->where('is_default', 1)
                ->where('id', '!=', $id)
                ->update(['is_default' => 0]);
        }

        $template->update($validated);
        $template->load('creatorUser');

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $template
        ]);
    }

    /**
     * 删除模板
     */
    public function destroy(int $id): JsonResponse
    {
        $template = TextbookAssignmentTemplate::find($id);

        if (!$template) {
            return response()->json([
                'code' => 404,
                'message' => '模板不存在'
            ], 404);
        }

        $user = auth()->user();

        // 检查权限：只能删除自己创建的模板
        if ($template->creator_user_id !== $user->id) {
            return response()->json([
                'code' => 403,
                'message' => '无权限删除此模板'
            ], 403);
        }

        $template->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取可用模板选项（用于下拉菜单）
     */
    public function options(Request $request): JsonResponse
    {
        $user = auth()->user();
        $userLevel = $user->organization_level ?? 5;

        $templates = TextbookAssignmentTemplate::active()
            ->where('creator_level', '<=', $userLevel)
            ->select(['id', 'name', 'description', 'is_default', 'usage_count'])
            ->orderBy('is_default', 'desc')
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $templates
        ]);
    }
}
