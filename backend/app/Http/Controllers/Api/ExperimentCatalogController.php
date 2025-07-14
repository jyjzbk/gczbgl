<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentCatalog;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ExperimentCatalogController extends Controller
{
    /**
     * 获取实验目录列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExperimentCatalog::with('subject');

        // 按学科筛选
        if ($request->filled('subject_id')) {
            $query->bySubject($request->subject_id);
        }

        // 按年级筛选
        if ($request->filled('grade')) {
            $query->byGrade($request->grade);
        }

        // 按学期筛选
        if ($request->filled('semester')) {
            $query->bySemester($request->semester);
        }

        // 按类型筛选
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // 按是否标准实验筛选
        if ($request->filled('is_standard')) {
            $query->where('is_standard', $request->is_standard);
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
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('chapter', 'like', "%{$search}%");
            });
        }

        // 排序
        $sortField = $request->get('sort_field', 'grade');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        // 分页
        $perPage = $request->get('per_page', 15);
        $catalogs = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $catalogs
        ]);
    }

    /**
     * 创建实验目录
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:experiment_catalogs',
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'grade' => 'required|integer|min:1|max:12',
            'semester' => ['required', 'integer', Rule::in([1, 2])],
            'chapter' => 'nullable|string|max:100',
            'duration' => 'integer|min:1|max:300',
            'student_count' => 'integer|min:1|max:100',
            'objective' => 'nullable|string',
            'materials' => 'nullable|string',
            'procedure' => 'nullable|string',
            'safety_notes' => 'nullable|string',
            'difficulty_level' => 'integer|min:1|max:5',
            'is_standard' => 'boolean',
            'status' => 'boolean'
        ]);

        $catalog = ExperimentCatalog::create($validated);
        $catalog->load('subject');

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $catalog
        ], 201);
    }

    /**
     * 获取实验目录详情
     */
    public function show(ExperimentCatalog $experimentCatalog): JsonResponse
    {
        $experimentCatalog->load('subject');

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $experimentCatalog
        ]);
    }

    /**
     * 更新实验目录
     */
    public function update(Request $request, ExperimentCatalog $experimentCatalog): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:200',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('experiment_catalogs')->ignore($experimentCatalog->id)
            ],
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'grade' => 'required|integer|min:1|max:12',
            'semester' => ['required', 'integer', Rule::in([1, 2])],
            'chapter' => 'nullable|string|max:100',
            'duration' => 'integer|min:1|max:300',
            'student_count' => 'integer|min:1|max:100',
            'objective' => 'nullable|string',
            'materials' => 'nullable|string',
            'procedure' => 'nullable|string',
            'safety_notes' => 'nullable|string',
            'difficulty_level' => 'integer|min:1|max:5',
            'is_standard' => 'boolean',
            'status' => 'boolean'
        ]);

        $experimentCatalog->update($validated);
        $experimentCatalog->load('subject');

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $experimentCatalog
        ]);
    }

    /**
     * 删除实验目录
     */
    public function destroy(ExperimentCatalog $experimentCatalog): JsonResponse
    {
        // 检查是否有关联的预约记录
        if ($experimentCatalog->reservations()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该实验目录已有预约记录，无法删除'
            ], 400);
        }

        $experimentCatalog->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 批量导入实验目录
     */
    public function batchImport(Request $request): JsonResponse
    {
        $request->validate([
            'catalogs' => 'required|array',
            'catalogs.*.subject_id' => 'required|exists:subjects,id',
            'catalogs.*.name' => 'required|string|max:200',
            'catalogs.*.code' => 'required|string|max:50',
            'catalogs.*.type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'catalogs.*.grade' => 'required|integer|min:1|max:12',
            'catalogs.*.semester' => ['required', 'integer', Rule::in([1, 2])],
        ]);

        $imported = 0;
        $errors = [];

        foreach ($request->catalogs as $index => $catalogData) {
            try {
                // 检查代码是否重复
                if (ExperimentCatalog::where('code', $catalogData['code'])->exists()) {
                    $errors[] = "第" . ($index + 1) . "行：实验编号已存在";
                    continue;
                }

                ExperimentCatalog::create($catalogData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "第" . ($index + 1) . "行：" . $e->getMessage();
            }
        }

        return response()->json([
            'code' => 200,
            'message' => "导入完成，成功导入 {$imported} 条记录",
            'data' => [
                'imported' => $imported,
                'errors' => $errors
            ]
        ]);
    }
}
