<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class LaboratoryController extends Controller
{
    /**
     * 获取实验室列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Laboratory::with(['school', 'manager']);

        // 按学校筛选
        if ($request->filled('school_id')) {
            $query->bySchool($request->school_id);
        }

        // 按类型筛选
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->available(); // 默认只显示可用的
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // 排序
        $sortField = $request->get('sort_field', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        // 分页
        $perPage = $request->get('per_page', 15);
        $laboratories = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $laboratories
        ]);
    }

    /**
     * 创建实验室
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'location' => 'nullable|string|max:200',
            'area' => 'nullable|numeric|min:0',
            'capacity' => 'integer|min:1|max:200',
            'manager_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|string',
            'safety_rules' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // 检查同一学校内编号是否重复
        $exists = Laboratory::where('school_id', $validated['school_id'])
                           ->where('code', $validated['code'])
                           ->exists();
        
        if ($exists) {
            return response()->json([
                'code' => 400,
                'message' => '该学校内实验室编号已存在'
            ], 400);
        }

        $laboratory = Laboratory::create($validated);
        $laboratory->load(['school', 'manager']);

        return response()->json([
            'code' => 201,
            'message' => '创建成功',
            'data' => $laboratory
        ], 201);
    }

    /**
     * 获取实验室详情
     */
    public function show(Laboratory $laboratory): JsonResponse
    {
        $laboratory->load(['school', 'manager', 'equipments']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $laboratory
        ]);
    }

    /**
     * 更新实验室
     */
    public function update(Request $request, Laboratory $laboratory): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'location' => 'nullable|string|max:200',
            'area' => 'nullable|numeric|min:0',
            'capacity' => 'integer|min:1|max:200',
            'manager_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|string',
            'safety_rules' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // 检查同一学校内编号是否重复（排除自己）
        $exists = Laboratory::where('school_id', $validated['school_id'])
                           ->where('code', $validated['code'])
                           ->where('id', '!=', $laboratory->id)
                           ->exists();
        
        if ($exists) {
            return response()->json([
                'code' => 400,
                'message' => '该学校内实验室编号已存在'
            ], 400);
        }

        $laboratory->update($validated);
        $laboratory->load(['school', 'manager']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $laboratory
        ]);
    }

    /**
     * 删除实验室
     */
    public function destroy(Laboratory $laboratory): JsonResponse
    {
        // 检查是否有关联的预约记录
        if ($laboratory->reservations()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该实验室已有预约记录，无法删除'
            ], 400);
        }

        // 检查是否有关联的设备
        if ($laboratory->equipments()->exists()) {
            return response()->json([
                'code' => 400,
                'message' => '该实验室下有设备，无法删除'
            ], 400);
        }

        $laboratory->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 获取实验室选项（用于下拉框）
     */
    public function options(Request $request): JsonResponse
    {
        $query = Laboratory::normal();

        // 按学校筛选
        if ($request->filled('school_id')) {
            $query->bySchool($request->school_id);
        }

        // 按类型筛选
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $laboratories = $query->orderBy('name')
                             ->get(['id', 'name', 'code', 'type', 'capacity']);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => $laboratories
        ]);
    }

    /**
     * 检查实验室可用性
     */
    public function checkAvailability(Request $request, Laboratory $laboratory): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_reservation_id' => 'nullable|integer'
        ]);

        $isAvailable = $laboratory->isAvailableAt(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->exclude_reservation_id
        );

        return response()->json([
            'code' => 200,
            'message' => '检查完成',
            'data' => [
                'available' => $isAvailable,
                'laboratory' => $laboratory->only(['id', 'name', 'code', 'status'])
            ]
        ]);
    }

    /**
     * 获取实验室课表
     */
    public function schedule(Request $request, Laboratory $laboratory): JsonResponse
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $reservations = $laboratory->getReservationsForDate($request->date);

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'laboratory' => $laboratory->only(['id', 'name', 'code', 'capacity']),
                'date' => $request->date,
                'reservations' => $reservations
            ]
        ]);
    }
}
