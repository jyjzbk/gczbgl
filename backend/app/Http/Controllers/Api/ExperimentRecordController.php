<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentRecord;
use App\Models\ExperimentReservation;
use App\Http\Middleware\DataScopeMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ExperimentRecordController extends Controller
{
    /**
     * 获取实验记录列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExperimentRecord::with(['catalog', 'laboratory', 'teacher', 'reservation']);

        // 应用数据权限过滤
        DataScopeMiddleware::applyDataScope($query, $request, 'experiment_records');

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
            $query->byDateRange($request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59');
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
        $sortField = $request->get('sort_field', 'start_time');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // 分页
        $perPage = $request->get('per_page', 15);
        $records = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $records
        ]);
    }

    /**
     * 开始实验记录
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:experiment_reservations,id',
            'student_count' => 'required|integer|min:1|max:100',
            'start_time' => 'nullable|date',
            'remark' => 'nullable|string'
        ]);

        // 获取预约信息
        $reservation = ExperimentReservation::with(['catalog', 'laboratory', 'teacher'])
                                          ->find($validated['reservation_id']);

        if (!$reservation->canStartExperiment()) {
            return response()->json([
                'code' => 400,
                'message' => '该预约不能开始实验'
            ], 400);
        }

        // 检查是否已有记录
        if ($reservation->record) {
            return response()->json([
                'code' => 400,
                'message' => '该预约已有实验记录'
            ], 400);
        }

        $recordData = [
            'reservation_id' => $reservation->id,
            'school_id' => $reservation->school_id,
            'catalog_id' => $reservation->catalog_id,
            'laboratory_id' => $reservation->laboratory_id,
            'teacher_id' => $reservation->teacher_id,
            'class_name' => $reservation->class_name,
            'student_count' => $validated['student_count'],
            'start_time' => $validated['start_time'] ?? now(),
            'status' => ExperimentRecord::STATUS_IN_PROGRESS
        ];

        // 验证创建权限
        if (!DataScopeMiddleware::canCreate($request, $recordData)) {
            return response()->json([
                'code' => 403,
                'message' => '无权在指定学校创建实验记录'
            ], 403);
        }

        $record = ExperimentRecord::create($recordData);
        $record->load(['catalog', 'laboratory', 'teacher', 'reservation']);

        return response()->json([
            'code' => 201,
            'message' => '实验开始记录成功',
            'data' => $record
        ], 201);
    }

    /**
     * 获取实验记录详情
     */
    public function show(ExperimentRecord $experimentRecord): JsonResponse
    {
        $experimentRecord->load(['catalog', 'laboratory', 'teacher', 'reservation']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $experimentRecord
        ]);
    }

    /**
     * 更新实验记录
     */
    public function update(Request $request, ExperimentRecord $experimentRecord): JsonResponse
    {
        if (!$experimentRecord->canEdit()) {
            return response()->json([
                'code' => 400,
                'message' => '当前状态不允许修改'
            ], 400);
        }

        $validated = $request->validate([
            'student_count' => 'integer|min:1|max:100',
            'end_time' => 'nullable|date|after:start_time',
            'completion_rate' => 'numeric|min:0|max:100',
            'quality_score' => 'integer|min:1|max:5',
            'photos' => 'array',
            'photos.*' => 'string',
            'videos' => 'array', 
            'videos.*' => 'string',
            'summary' => 'nullable|string',
            'problems' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'status' => ['integer', Rule::in([
                ExperimentRecord::STATUS_IN_PROGRESS,
                ExperimentRecord::STATUS_COMPLETED,
                ExperimentRecord::STATUS_ABNORMAL
            ])]
        ]);

        $experimentRecord->update($validated);
        $experimentRecord->load(['catalog', 'laboratory', 'teacher']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $experimentRecord
        ]);
    }

    /**
     * 完成实验
     */
    public function complete(Request $request, ExperimentRecord $experimentRecord): JsonResponse
    {
        if (!$experimentRecord->canComplete()) {
            return response()->json([
                'code' => 400,
                'message' => '当前状态不允许完成'
            ], 400);
        }

        $validated = $request->validate([
            'end_time' => 'nullable|date',
            'completion_rate' => 'required|numeric|min:0|max:100',
            'quality_score' => 'required|integer|min:1|max:5',
            'summary' => 'required|string',
            'problems' => 'nullable|string',
            'suggestions' => 'nullable|string'
        ]);

        $validated['end_time'] = $validated['end_time'] ?? now();
        $validated['status'] = ExperimentRecord::STATUS_COMPLETED;

        $experimentRecord->update($validated);

        return response()->json([
            'code' => 200,
            'message' => '实验完成记录成功'
        ]);
    }

    /**
     * 上传实验照片
     */
    public function uploadPhotos(Request $request, ExperimentRecord $experimentRecord): JsonResponse
    {
        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB
        ]);

        $photos = $experimentRecord->photos ?? [];
        
        foreach ($request->file('photos') as $photo) {
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('experiment_photos', $filename, 'public');
            $photos[] = $path;
        }

        $experimentRecord->update(['photos' => $photos]);

        return response()->json([
            'code' => 200,
            'message' => '照片上传成功',
            'data' => ['photos' => $photos]
        ]);
    }

    /**
     * 上传实验视频
     */
    public function uploadVideos(Request $request, ExperimentRecord $experimentRecord): JsonResponse
    {
        $request->validate([
            'videos' => 'required|array|max:3',
            'videos.*' => 'mimes:mp4,avi,mov,wmv|max:51200' // 50MB
        ]);

        $videos = $experimentRecord->videos ?? [];
        
        foreach ($request->file('videos') as $video) {
            $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
            $path = $video->storeAs('experiment_videos', $filename, 'public');
            $videos[] = $path;
        }

        $experimentRecord->update(['videos' => $videos]);

        return response()->json([
            'code' => 200,
            'message' => '视频上传成功',
            'data' => ['videos' => $videos]
        ]);
    }

    /**
     * 删除实验记录
     */
    public function destroy(ExperimentRecord $experimentRecord): JsonResponse
    {
        // 只有进行中的记录可以删除
        if ($experimentRecord->status !== ExperimentRecord::STATUS_IN_PROGRESS) {
            return response()->json([
                'code' => 400,
                'message' => '只有进行中的记录可以删除'
            ], 400);
        }

        $experimentRecord->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }
}
