<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TextbookVersionAssignmentService;
use App\Models\TextbookVersionAssignment;
use App\Models\School;
use App\Models\Subject;
use App\Models\TextbookVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TextbookVersionAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(TextbookVersionAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * 获取可管理的学校列表
     */
    public function getManageableSchools(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schools = $this->assignmentService->getManageableSchools($user);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $schools
        ]);
    }

    /**
     * 获取用户可管理的所有学校的教材版本指定列表
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'grade_level' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,0,1',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);

        $user = auth()->user();

        // 如果指定了school_id，检查权限并返回单个学校的数据
        if (!empty($validated['school_id'])) {
            if (!$this->assignmentService->canAssignTextbookVersion($user, $validated['school_id'])) {
                return response()->json([
                    'code' => 403,
                    'message' => '无权限查看此学校的教材版本指定'
                ], 403);
            }

            $assignments = $this->assignmentService->getSchoolAssignments(
                $validated['school_id'],
                $validated
            );
        } else {
            // 获取用户可管理的所有学校的指定记录
            $assignments = $this->assignmentService->getAllManageableAssignments($user, $validated);
        }

        // 分页处理
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;
        $total = $assignments->count();
        $items = $assignments->forPage($page, $perPage)->values();

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'items' => $items,
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
     * 创建教材版本指定
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|string|max:20',
            'textbook_version_id' => 'required|exists:textbook_versions,id',
            'assignment_reason' => 'nullable|string|max:500',
            'effective_date' => 'nullable|date|after_or_equal:today',
            'expiry_date' => 'nullable|date|after:effective_date'
        ]);

        $user = auth()->user();

        // 检查权限
        if (!$this->assignmentService->canAssignTextbookVersion($user, $validated['school_id'])) {
            return response()->json([
                'code' => 403,
                'message' => '无权限为此学校指定教材版本'
            ], 403);
        }

        // 添加指定者信息
        $validated['assigner_level'] = $user->organization_level ?? 5;
        $validated['assigner_org_id'] = $user->organization_id;
        $validated['assigner_org_type'] = $user->organization_type;
        $validated['assigner_user_id'] = $user->id;
        $validated['effective_date'] = $validated['effective_date'] ?? now();

        try {
            $assignment = $this->assignmentService->assignTextbookVersion($validated);
            $assignment->load(['subject', 'textbookVersion', 'school']);

            return response()->json([
                'code' => 201,
                'message' => '指定成功',
                'data' => $assignment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '指定失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量指定教材版本
     */
    public function batchStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.school_id' => 'required|exists:schools,id',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
            'assignments.*.grade_level' => 'required|string|max:20',
            'assignments.*.textbook_version_id' => 'required|exists:textbook_versions,id',
            'assignments.*.assignment_reason' => 'nullable|string|max:500',
            'assignments.*.effective_date' => 'nullable|date|after_or_equal:today',
            'assignments.*.expiry_date' => 'nullable|date|after:assignments.*.effective_date'
        ]);

        $user = auth()->user();
        $result = $this->assignmentService->batchAssignTextbookVersions(
            $validated['assignments'],
            $user
        );

        return response()->json([
            'code' => 200,
            'message' => "批量指定完成，成功 {$result['success_count']} 条，失败 {$result['error_count']} 条",
            'data' => $result
        ]);
    }

    /**
     * 使用模板批量指定
     */
    public function assignByTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:textbook_assignment_templates,id',
            'school_ids' => 'required|array|min:1',
            'school_ids.*' => 'exists:schools,id'
        ]);

        $user = auth()->user();

        try {
            $result = $this->assignmentService->assignByTemplate(
                $validated['template_id'],
                $validated['school_ids'],
                $user
            );

            return response()->json([
                'code' => 200,
                'message' => "模板指定完成，成功 {$result['success_count']} 条，失败 {$result['error_count']} 条",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '模板指定失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 撤销指定
     */
    public function revoke(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $user = auth()->user();

        try {
            $this->assignmentService->revokeAssignment($id, $validated['reason'], $user);

            return response()->json([
                'code' => 200,
                'message' => '撤销成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '撤销失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取学校指定的教材版本（用于实验目录筛选）
     */
    public function getAssignedVersion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|string|max:20'
        ]);

        $textbookVersionId = $this->assignmentService->getAssignedTextbookVersion(
            $validated['school_id'],
            $validated['subject_id'],
            $validated['grade_level']
        );

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'textbook_version_id' => $textbookVersionId
            ]
        ]);
    }

    /**
     * 获取指定统计信息
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schools = $this->assignmentService->getManageableSchools($user);
        
        $statistics = [
            'total_schools' => $schools->count(),
            'assigned_schools' => 0,
            'unassigned_schools' => 0,
            'total_assignments' => 0,
            'active_assignments' => 0
        ];

        foreach ($schools as $school) {
            $assignments = $this->assignmentService->getSchoolAssignments($school->id);
            $activeAssignments = $assignments->where('status', 1);
            
            if ($activeAssignments->count() > 0) {
                $statistics['assigned_schools']++;
            } else {
                $statistics['unassigned_schools']++;
            }
            
            $statistics['total_assignments'] += $assignments->count();
            $statistics['active_assignments'] += $activeAssignments->count();
        }

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $statistics
        ]);
    }
}
