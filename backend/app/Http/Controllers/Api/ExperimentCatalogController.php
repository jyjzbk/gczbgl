<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentCatalog;
use App\Models\Subject;
use App\Models\TextbookVersion;
use App\Models\TextbookChapter;
use App\Services\TextbookVersionAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ExperimentCatalogController extends Controller
{
    protected $assignmentService;

    public function __construct(TextbookVersionAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * 获取实验目录列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExperimentCatalog::with([
            'subject',
            'textbookVersion',
            'chapter',
            'parentCatalog',
            'originalCatalog'
        ]);

        // 按学科筛选
        if ($request->filled('subject_id')) {
            $query->bySubject($request->subject_id);
        }

        // 按教材版本筛选
        if ($request->filled('textbook_version_id')) {
            $query->where('textbook_version_id', $request->textbook_version_id);
        }

        // 根据学校指定的教材版本筛选（用于实验预约等场景）
        if ($request->filled('school_id') && $request->filled('subject_id') && $request->filled('grade_level')) {
            $assignedVersionId = $this->assignmentService->getAssignedTextbookVersion(
                $request->school_id,
                $request->subject_id,
                $request->grade_level
            );

            if ($assignedVersionId) {
                $query->where('textbook_version_id', $assignedVersionId);
            }
        }

        // 按章节筛选
        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->chapter_id);
        }

        // 按年级筛选
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // 按册次筛选
        if ($request->filled('volume')) {
            $query->where('volume', $request->volume);
        }

        // 按管理级别筛选
        if ($request->filled('management_level')) {
            $query->where('management_level', $request->management_level);
        }

        // 按实验类型筛选
        if ($request->filled('experiment_type')) {
            $query->where('experiment_type', $request->experiment_type);
        }

        // 按是否被下级删除筛选
        if ($request->has('is_deleted_by_lower')) {
            $query->where('is_deleted_by_lower', $request->boolean('is_deleted_by_lower'));
        }

        // 按创建者级别筛选
        if ($request->filled('created_by_level')) {
            $query->where('created_by_level', $request->created_by_level);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active(); // 默认只显示启用的
        }

        // 权限控制：根据用户级别筛选可见的实验目录
        $user = Auth::user();
        if ($user) {
            $this->applyPermissionFilter($query, $user);
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

        // 处理章节信息映射
        $catalogs->getCollection()->transform(function ($catalog) {
            // 如果有章节关联，将其映射为 chapter_info
            // 注意：由于模型中同时有 chapter 字段和 chapter 关联，需要使用 getRelation 方法
            $chapterRelation = $catalog->getRelation('chapter');
            if ($chapterRelation) {
                $catalog->chapter_info = $chapterRelation->toArray();
            } else {
                $catalog->chapter_info = null;
            }

            return $catalog;
        });

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
            'textbook_version_id' => 'nullable|exists:textbook_versions,id',
            'management_level' => 'required|integer|min:1|max:5',
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:experiment_catalogs',
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'grade_level' => 'required|string|max:20',
            'volume' => 'required|string|max:20',
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

        // 处理年级字段的兼容性
        // 将 grade_level 转换为 grade 字段（数字类型）
        if (isset($validated['grade_level'])) {
            // 提取年级数字，例如："三年级" -> 3, "3年级" -> 3, "3" -> 3
            $gradeLevel = $validated['grade_level'];
            if (preg_match('/(\d+)/', $gradeLevel, $matches)) {
                $validated['grade'] = (int)$matches[1];
            } else {
                // 如果无法提取数字，根据中文年级转换
                $gradeMap = [
                    '一年级' => 1, '二年级' => 2, '三年级' => 3, '四年级' => 4,
                    '五年级' => 5, '六年级' => 6, '七年级' => 7, '八年级' => 8, '九年级' => 9
                ];
                $validated['grade'] = $gradeMap[$gradeLevel] ?? 1;
            }
        }

        // 处理学期字段的兼容性
        if (isset($validated['volume'])) {
            $volume = $validated['volume'];
            if (strpos($volume, '上') !== false) {
                $validated['semester'] = 1;
            } elseif (strpos($volume, '下') !== false) {
                $validated['semester'] = 2;
            } else {
                $validated['semester'] = 1; // 默认上学期
            }
        }

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
            'textbook_version_id' => 'nullable|exists:textbook_versions,id',
            'management_level' => 'required|integer|min:1|max:5',
            'name' => 'required|string|max:200',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('experiment_catalogs')->ignore($experimentCatalog->id)
            ],
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'grade_level' => 'required|string|max:20',
            'volume' => 'required|string|max:20',
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

        // 处理年级字段的兼容性
        // 将 grade_level 转换为 grade 字段（数字类型）
        if (isset($validated['grade_level'])) {
            // 提取年级数字，例如："三年级" -> 3, "3年级" -> 3, "3" -> 3
            $gradeLevel = $validated['grade_level'];
            if (preg_match('/(\d+)/', $gradeLevel, $matches)) {
                $validated['grade'] = (int)$matches[1];
            } else {
                // 如果无法提取数字，根据中文年级转换
                $gradeMap = [
                    '一年级' => 1, '二年级' => 2, '三年级' => 3, '四年级' => 4,
                    '五年级' => 5, '六年级' => 6, '七年级' => 7, '八年级' => 8, '九年级' => 9
                ];
                $validated['grade'] = $gradeMap[$gradeLevel] ?? 1;
            }
        }

        // 处理学期字段的兼容性
        if (isset($validated['volume'])) {
            $volume = $validated['volume'];
            if (strpos($volume, '上') !== false) {
                $validated['semester'] = 1;
            } elseif (strpos($volume, '下') !== false) {
                $validated['semester'] = 2;
            } else {
                $validated['semester'] = 1; // 默认上学期
            }
        }

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
            'catalogs.*.grade_level' => 'required|string|max:20',
            'catalogs.*.volume' => 'required|string|max:20',
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

    /**
     * 应用权限过滤
     */
    private function applyPermissionFilter($query, $user)
    {
        // 根据新的权限要求：所有角色、用户都能查看各级管理员创建的实验目录
        // 不需要进行权限过滤，所有用户都能看到所有实验目录
        // 权限控制在操作层面（编辑、删除等）进行
    }

    /**
     * 复制实验目录（继承机制）
     */
    public function copy(Request $request, $id): JsonResponse
    {
        try {
            $sourceCatalog = ExperimentCatalog::with(['equipmentRequirements'])->findOrFail($id);
            $user = Auth::user();

            // 验证权限：用户必须有权限访问源实验目录
            if (!$this->canAccessCatalog($sourceCatalog, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => '没有权限复制该实验目录'
                ], 403);
            }

            // 创建副本
            $newCatalog = $sourceCatalog->replicate();
            $newCatalog->name = $request->input('name', $sourceCatalog->name . ' (副本)');
            $newCatalog->parent_catalog_id = $sourceCatalog->id;
            $newCatalog->original_catalog_id = $sourceCatalog->original_catalog_id ?? $sourceCatalog->id;
            $newCatalog->version = 1;
            $newCatalog->management_level = $user->organization_level ?? 5;
            $newCatalog->created_by_level = $user->organization_level ?? 5;
            $newCatalog->created_by_org_id = $user->organization_id ?? $user->school_id;
            $newCatalog->created_by_org_type = $user->organization_type ?? 'school';
            $newCatalog->created_by = $user->id;
            $newCatalog->save();

            // 复制器材需求配置
            foreach ($sourceCatalog->equipmentRequirements as $requirement) {
                $newRequirement = $requirement->replicate();
                $newRequirement->catalog_id = $newCatalog->id;
                $newRequirement->created_by = $user->id;
                $newRequirement->save();
            }

            $newCatalog->load(['subject', 'textbookVersion', 'chapter']);

            return response()->json([
                'success' => true,
                'message' => '实验目录复制成功',
                'data' => $newCatalog
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '复制实验目录失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 检查用户是否可以访问实验目录
     */
    private function canAccessCatalog($catalog, $user): bool
    {
        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id ?? $user->school_id;

        // 可以访问自己级别及下级级别的实验目录
        if ($catalog->management_level >= $userLevel) {
            return true;
        }

        // 可以访问自己组织创建的实验目录
        if ($catalog->created_by_org_id == $userOrgId) {
            return true;
        }

        return false;
    }
}
