<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentReservation;
use App\Models\Laboratory;
use App\Models\ExperimentCatalog;
use App\Http\Middleware\DataScopeMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ExperimentReservationController extends Controller
{
    /**
     * 获取实验预约列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExperimentReservation::with(['catalog', 'laboratory', 'teacher', 'reviewer']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'experiment_reservations');

        // 按学校筛选
        if ($request->filled('school_id')) {
            // 验证用户是否可以访问指定学校
            if (DataScopeMiddleware::canAccess($request, 'school', $request->school_id)) {
                $query->bySchool($request->school_id);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => '无权访问指定学校的数据'
                ], 403);
            }
        }

        // 按教师筛选
        if ($request->filled('teacher_id')) {
            $query->byTeacher($request->teacher_id);
        }

        // 按实验室筛选
        if ($request->filled('laboratory_id')) {
            $query->where('laboratory_id', $request->laboratory_id);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // 按日期范围筛选
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                  ->orWhereHas('catalog', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function($subQ) use ($search) {
                      $subQ->where('real_name', 'like', "%{$search}%");
                  });
            });
        }

        // 排序
        $sortField = $request->get('sort_field', 'reservation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // 分页
        $perPage = $request->get('per_page', 15);
        $reservations = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $reservations
        ]);
    }

    /**
     * 创建实验预约
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'catalog_id' => 'required|exists:experiment_catalogs,id',
            'laboratory_id' => 'required|exists:laboratories,id',
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string|max:100',
            'student_count' => 'required|integer|min:1|max:100',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'remark' => 'nullable|string'
        ]);

        // 验证创建权限
        if (!DataScopeMiddleware::canCreate($request, $validated)) {
            return response()->json([
                'code' => 403,
                'message' => '无权在指定学校创建实验预约'
            ], 403);
        }

        // 检查实验室是否可用
        $laboratory = Laboratory::find($validated['laboratory_id']);
        if (!$laboratory->isAvailableAt(
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return response()->json([
                'code' => 400,
                'message' => '该时间段实验室不可用，请选择其他时间'
            ], 400);
        }

        // 检查实验室容量
        if ($validated['student_count'] > $laboratory->capacity) {
            return response()->json([
                'code' => 400,
                'message' => "学生人数超过实验室容量限制（{$laboratory->capacity}人）"
            ], 400);
        }

        $reservation = ExperimentReservation::create($validated);
        $reservation->load(['catalog', 'laboratory', 'teacher']);

        return response()->json([
            'code' => 201,
            'message' => '预约成功',
            'data' => $reservation
        ], 201);
    }

    /**
     * 获取预约详情
     */
    public function show(ExperimentReservation $experimentReservation): JsonResponse
    {
        $experimentReservation->load(['catalog', 'laboratory', 'teacher', 'reviewer', 'record']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $experimentReservation
        ]);
    }

    /**
     * 更新预约
     */
    public function update(Request $request, ExperimentReservation $experimentReservation): JsonResponse
    {
        if (!$experimentReservation->canEdit()) {
            return response()->json([
                'code' => 400,
                'message' => '当前状态不允许修改'
            ], 400);
        }

        $validated = $request->validate([
            'catalog_id' => 'required|exists:experiment_catalogs,id',
            'laboratory_id' => 'required|exists:laboratories,id',
            'class_name' => 'required|string|max:100',
            'student_count' => 'required|integer|min:1|max:100',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'remark' => 'nullable|string'
        ]);

        // 检查实验室是否可用（排除当前预约）
        $laboratory = Laboratory::find($validated['laboratory_id']);
        if (!$laboratory->isAvailableAt(
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time'],
            $experimentReservation->id
        )) {
            return response()->json([
                'code' => 400,
                'message' => '该时间段实验室不可用，请选择其他时间'
            ], 400);
        }

        // 检查实验室容量
        if ($validated['student_count'] > $laboratory->capacity) {
            return response()->json([
                'code' => 400,
                'message' => "学生人数超过实验室容量限制（{$laboratory->capacity}人）"
            ], 400);
        }

        $experimentReservation->update($validated);
        $experimentReservation->load(['catalog', 'laboratory', 'teacher']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $experimentReservation
        ]);
    }

    /**
     * 取消预约
     */
    public function cancel(ExperimentReservation $experimentReservation): JsonResponse
    {
        if (!$experimentReservation->canCancel()) {
            return response()->json([
                'code' => 400,
                'message' => '当前状态不允许取消'
            ], 400);
        }

        $experimentReservation->update([
            'status' => ExperimentReservation::STATUS_CANCELLED
        ]);

        return response()->json([
            'code' => 200,
            'message' => '取消成功'
        ]);
    }

    /**
     * 审核预约
     */
    public function review(Request $request, ExperimentReservation $experimentReservation): JsonResponse
    {
        if (!$experimentReservation->canReview()) {
            return response()->json([
                'code' => 400,
                'message' => '当前状态不允许审核'
            ], 400);
        }

        $validated = $request->validate([
            'status' => ['required', 'integer', Rule::in([2, 3])], // 直接使用数字而不是常量
            'review_remark' => 'nullable|string'
        ]);

        $experimentReservation->update([
            'status' => $validated['status'],
            'reviewer_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_remark' => $validated['review_remark'] ?? null
        ]);

        $statusText = $validated['status'] === ExperimentReservation::STATUS_APPROVED ? '通过' : '拒绝';

        return response()->json([
            'code' => 200,
            'message' => "审核{$statusText}成功"
        ]);
    }



    /**
     * 获取个人预约统计
     */
    public function getPersonalStats(Request $request): JsonResponse
    {
        try {
            $teacherId = $request->teacher_id === 'current' ? auth()->id() : $request->teacher_id;

            $stats = [
                'total_reservations' => ExperimentReservation::where('teacher_id', $teacherId)->count(),
                'completed_experiments' => ExperimentReservation::where('teacher_id', $teacherId)
                    ->where('status', ExperimentReservation::STATUS_COMPLETED)
                    ->count(),
                'pending_reservations' => ExperimentReservation::where('teacher_id', $teacherId)
                    ->where('status', ExperimentReservation::STATUS_PENDING)
                    ->count(),
                'approved_reservations' => ExperimentReservation::where('teacher_id', $teacherId)
                    ->where('status', ExperimentReservation::STATUS_APPROVED)
                    ->count()
            ];

            // 计算完成率
            $stats['completion_rate'] = $stats['total_reservations'] > 0
                ? round(($stats['completed_experiments'] / $stats['total_reservations']) * 100, 2)
                : 0;

            // 获取作品总数 - 暂时设为0，等待work_count字段迁移完成
            $stats['total_works'] = 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取统计数据失败：' . $e->getMessage()
            ], 500);
        }
    }
}
