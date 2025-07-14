<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * 获取学科列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::query();

        // 按类型筛选
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // 按学段筛选
        if ($request->filled('stage')) {
            $query->byStage($request->stage);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active(); // 默认只显示启用的
        }

        // 搜索
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
        if ($request->get('all')) {
            $subjects = $query->get();
        } else {
            $perPage = $request->get('per_page', 15);
            $subjects = $query->paginate($perPage);
        }

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $subjects
        ]);
    }

    /**
     * 创建学科
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:subjects',
            'type' => ['required', 'integer', Rule::in([1, 2, 3])],
            'stage' => ['required', 'integer', Rule::in([1, 2, 3])],
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $subject
        ], 201);
    }

    /**
     * 获取学科详情
     */
    public function show(Subject $subject): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $subject
        ]);
    }

    /**
     * 更新学科
     */
    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects')->ignore($subject->id)
            ],
            'type' => ['required', 'integer', Rule::in([1, 2, 3])],
            'stage' => ['required', 'integer', Rule::in([1, 2, 3])],
            'sort_order' => 'integer|min:0',
            'status' => 'boolean'
        ]);

        $subject->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $subject
        ]);
    }

    /**
     * 删除学科
     */
    public function destroy(Subject $subject): JsonResponse
    {
        // 检查是否有关联的实验目录
        if ($subject->experimentCatalogs()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该学科下有实验目录，无法删除'
            ], 400);
        }

        $subject->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取学科选项（用于下拉框）
     */
    public function options(Request $request): JsonResponse
    {
        $query = Subject::active();

        // 按学段筛选
        if ($request->filled('stage')) {
            $query->byStage($request->stage);
        }

        $subjects = $query->orderBy('sort_order')
                         ->orderBy('id')
                         ->get(['id', 'name', 'code', 'type', 'stage']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $subjects
        ]);
    }
}
