<?php

namespace App\Http\Controllers;

use App\Models\EquipmentBorrow;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EquipmentBorrowController extends Controller
{
    /**
     * 获取借用记录列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = EquipmentBorrow::with([
            'equipment.category',
            'equipment.school',
            'borrower',
            'approver',
            'reservation'
        ]);

        // 筛选条件
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('borrower_id')) {
            $query->where('borrower_id', $request->borrower_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('school_id')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // 日期范围筛选
        if ($request->filled('borrow_date_start')) {
            $query->where('borrow_date', '>=', $request->borrow_date_start);
        }

        if ($request->filled('borrow_date_end')) {
            $query->where('borrow_date', '<=', $request->borrow_date_end);
        }

        // 逾期筛选
        if ($request->boolean('overdue_only')) {
            $query->overdue();
        }

        // 待审批筛选
        if ($request->boolean('pending_only')) {
            $query->pending();
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('equipment', function($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('borrower', function($user) use ($search) {
                    $user->where('real_name', 'like', "%{$search}%");
                });
            });
        }

        // 排序
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // 分页
        $perPage = $request->get('per_page', 15);
        $borrows = $query->paginate($perPage);

        // 添加计算字段
        $borrows->getCollection()->transform(function ($borrow) {
            $borrow->overdue_days = $borrow->overdue_days;
            $borrow->borrow_days = $borrow->borrow_days;
            return $borrow;
        });

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => [
                'items' => $borrows->items(),
                'pagination' => [
                    'current_page' => $borrows->currentPage(),
                    'last_page' => $borrows->lastPage(),
                    'per_page' => $borrows->perPage(),
                    'total' => $borrows->total()
                ]
            ]
        ]);
    }

    /**
     * 创建借用申请
     */
    public function store(Request $request): JsonResponse
    {
        // 支持两种数据格式：单个设备借用和批量设备借用
        if ($request->has('equipment_id')) {
            // 单个设备借用格式
            $request->validate([
                'equipment_id' => 'required|integer|exists:equipments,id',
                'borrower_id' => 'required|integer|exists:users,id',
                'borrower_name' => 'required|string|max:100',
                'borrower_phone' => 'required|string|max:20',
                'quantity' => 'nullable|integer|min:1',
                'reservation_id' => 'nullable|exists:experiment_reservations,id',
                'borrow_date' => 'required|date|after_or_equal:today',
                'expected_return_date' => 'required|date|after_or_equal:borrow_date',
                'purpose' => 'required|string|max:500',
                'remark' => 'nullable|string|max:1000'
            ]);

            // 转换为批量格式进行处理
            $equipmentIds = [$request->equipment_id];
            $quantities = [$request->get('quantity', 1)];
            $borrowerId = $request->borrower_id;
            $borrowerName = $request->borrower_name;
            $borrowerPhone = $request->borrower_phone;
        } else {
            // 批量设备借用格式
            $request->validate([
                'equipment_ids' => 'required|array|min:1',
                'equipment_ids.*' => 'exists:equipments,id',
                'quantities' => 'required|array|min:1',
                'quantities.*' => 'integer|min:1',
                'borrower_id' => 'required|integer|exists:users,id',
                'reservation_id' => 'nullable|exists:experiment_reservations,id',
                'borrow_date' => 'required|date|after_or_equal:today',
                'expected_return_date' => 'required|date|after_or_equal:borrow_date',
                'purpose' => 'required|string|max:500',
                'remark' => 'nullable|string|max:1000'
            ], [
                'equipment_ids.required' => '请选择要借用的设备',
                'equipment_ids.min' => '至少需要选择一个设备',
                'equipment_ids.*.exists' => '选择的设备不存在',
                'quantities.required' => '请输入借用数量',
                'quantities.*.min' => '借用数量必须大于0',
                'borrower_id.required' => '请选择借用人',
                'borrower_id.exists' => '借用人不存在',
                'borrow_date.required' => '请选择借用日期',
                'borrow_date.date' => '借用日期格式不正确',
                'borrow_date.after_or_equal' => '借用日期不能早于今天',
                'expected_return_date.required' => '请选择预计归还日期',
                'expected_return_date.date' => '预计归还日期格式不正确',
                'expected_return_date.after_or_equal' => '预计归还日期不能早于借用日期',
                'purpose.required' => '请输入借用用途',
                'purpose.max' => '借用用途不能超过500个字符',
                'remark.max' => '备注不能超过1000个字符'
            ]);

            // 验证设备数量和数量数组长度一致
            if (count($request->equipment_ids) !== count($request->quantities)) {
                return response()->json([
                    'code' => 422,
                    'message' => '设备ID和数量数组长度不一致'
                ], 422);
            }

            $equipmentIds = $request->equipment_ids;
            $quantities = $request->quantities;
            $borrowerId = $request->borrower_id;
            $borrowerName = $request->borrower_name;
            $borrowerPhone = $request->borrower_phone;
        }

        DB::beginTransaction();
        try {
            $borrowRecords = [];

            foreach ($equipmentIds as $index => $equipmentId) {
                $equipment = Equipment::find($equipmentId);
                $quantity = $quantities[$index];

                // 检查设备是否可借用
                if (!$equipment->canBorrow($quantity)) {
                    DB::rollBack();
                    return response()->json([
                        'code' => 422,
                        'message' => "设备 '{$equipment->name}' 可借用数量不足，当前可借用：{$equipment->available_quantity}"
                    ], 422);
                }

                // 创建借用记录
                $borrow = EquipmentBorrow::create([
                    'equipment_id' => $equipmentId,
                    'reservation_id' => $request->reservation_id,
                    'borrower_id' => $borrowerId,
                    'quantity' => $quantity,
                    'borrow_date' => $request->borrow_date,
                    'expected_return_date' => $request->expected_return_date,
                    'purpose' => $request->purpose,
                    'remark' => $request->remark,
                    'status' => EquipmentBorrow::STATUS_PENDING
                ]);

                $borrowRecords[] = $borrow;
            }

            DB::commit();

            // 加载关联数据
            foreach ($borrowRecords as $borrow) {
                $borrow->load(['equipment.category', 'borrower']);
            }

            return response()->json([
                'code' => 201,
                'message' => '借用申请提交成功',
                'data' => [
                    'borrow_records' => $borrowRecords,
                    'total_count' => count($borrowRecords)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => '借用申请失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取借用记录详情
     */
    public function show(EquipmentBorrow $equipmentBorrow): JsonResponse
    {
        $equipmentBorrow->load([
            'equipment.category',
            'equipment.school',
            'equipment.laboratory',
            'borrower',
            'approver',
            'reservation'
        ]);

        // 添加计算字段
        $equipmentBorrow->overdue_days = $equipmentBorrow->overdue_days;
        $equipmentBorrow->borrow_days = $equipmentBorrow->borrow_days;
        $equipmentBorrow->can_return = $equipmentBorrow->canReturn();
        $equipmentBorrow->can_approve = $equipmentBorrow->canApprove();

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $equipmentBorrow
        ]);
    }

    /**
     * 更新借用记录
     */
    public function update(Request $request, EquipmentBorrow $equipmentBorrow): JsonResponse
    {
        // 只有待审批状态的记录可以修改
        if ($equipmentBorrow->status !== EquipmentBorrow::STATUS_PENDING) {
            return response()->json([
                'code' => 422,
                'message' => '只有待审批状态的借用记录可以修改'
            ], 422);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'borrow_date' => 'required|date|after_or_equal:today',
            'expected_return_date' => 'required|date|after_or_equal:borrow_date',
            'purpose' => 'required|string|max:500',
            'remark' => 'nullable|string|max:1000'
        ]);

        // 检查设备可借用数量
        $equipment = $equipmentBorrow->equipment;
        $currentBorrowedQuantity = $equipment->borrowed_quantity - $equipmentBorrow->quantity;
        $availableQuantity = $equipment->quantity - $currentBorrowedQuantity;

        if ($request->quantity > $availableQuantity) {
            return response()->json([
                'code' => 422,
                'message' => "设备可借用数量不足，当前可借用：{$availableQuantity}"
            ], 422);
        }

        $equipmentBorrow->update([
            'quantity' => $request->quantity,
            'borrow_date' => $request->borrow_date,
            'expected_return_date' => $request->expected_return_date,
            'purpose' => $request->purpose,
            'remark' => $request->remark
        ]);

        $equipmentBorrow->load(['equipment.category', 'borrower']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $equipmentBorrow
        ]);
    }

    /**
     * 删除借用记录
     */
    public function destroy(EquipmentBorrow $equipmentBorrow): JsonResponse
    {
        // 只有待审批状态的记录可以删除
        if ($equipmentBorrow->status !== EquipmentBorrow::STATUS_PENDING) {
            return response()->json([
                'code' => 422,
                'message' => '只有待审批状态的借用记录可以删除'
            ], 422);
        }

        $equipmentBorrow->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 审批借用申请
     */
    public function approve(Request $request, EquipmentBorrow $equipmentBorrow): JsonResponse
    {
        // 支持两种参数格式：action 和 status
        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|in:1,2,6', // 1=借用中, 2=已归还, 6=已拒绝
                'remark' => 'nullable|string|max:1000'
            ]);

            // 将status转换为action
            $action = $request->status == 6 ? 'reject' : 'approve';
        } else {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'remark' => 'nullable|string|max:1000'
            ]);
            $action = $request->action;
        }

        if (!$equipmentBorrow->canApprove()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许审批'
            ], 422);
        }

        $approverId = auth()->id();

        try {
            if ($action === 'approve') {
                // 再次检查设备可借用数量
                if (!$equipmentBorrow->equipment->canBorrow($equipmentBorrow->quantity)) {
                    return response()->json([
                        'code' => 422,
                        'message' => '设备可借用数量不足，审批失败'
                    ], 422);
                }

                $equipmentBorrow->approve($approverId, $request->remark);
                $message = '借用申请审批通过';
            } else {
                $equipmentBorrow->reject($approverId, $request->remark);
                $message = '借用申请已拒绝';
            }

            $equipmentBorrow->load(['equipment.category', 'borrower', 'approver']);

            return response()->json([
                'code' => 200,
                'message' => $message,
                'data' => $equipmentBorrow
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '审批失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 归还设备
     */
    public function returnEquipment(Request $request, EquipmentBorrow $equipmentBorrow): JsonResponse
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000'
        ]);

        if (!$equipmentBorrow->canReturn()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许归还'
            ], 422);
        }

        try {
            $equipmentBorrow->returnEquipment($request->remark);
            $equipmentBorrow->load(['equipment.category', 'borrower']);

            return response()->json([
                'code' => 200,
                'message' => '设备归还成功',
                'data' => $equipmentBorrow
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '归还失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量操作
     */
    public function batchAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject,return',
            'borrow_ids' => 'required|array|min:1',
            'borrow_ids.*' => 'exists:equipment_borrows,id',
            'remark' => 'nullable|string|max:1000'
        ]);

        $borrowRecords = EquipmentBorrow::whereIn('id', $request->borrow_ids)->get();
        $successCount = 0;
        $errors = [];

        foreach ($borrowRecords as $borrow) {
            try {
                switch ($request->action) {
                    case 'approve':
                        if ($borrow->canApprove()) {
                            $borrow->approve(auth()->id(), $request->remark);
                            $successCount++;
                        } else {
                            $errors[] = "借用记录 #{$borrow->id} 状态不允许审批";
                        }
                        break;
                    case 'reject':
                        if ($borrow->canApprove()) {
                            $borrow->reject(auth()->id(), $request->remark);
                            $successCount++;
                        } else {
                            $errors[] = "借用记录 #{$borrow->id} 状态不允许审批";
                        }
                        break;
                    case 'return':
                        if ($borrow->canReturn()) {
                            $borrow->returnEquipment($request->remark);
                            $successCount++;
                        } else {
                            $errors[] = "借用记录 #{$borrow->id} 状态不允许归还";
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "借用记录 #{$borrow->id} 操作失败：" . $e->getMessage();
            }
        }

        return response()->json([
            'code' => 200,
            'message' => "批量操作完成，成功处理 {$successCount} 条记录",
            'data' => [
                'success_count' => $successCount,
                'total_count' => count($borrowRecords),
                'errors' => $errors
            ]
        ]);
    }

    /**
     * 更新逾期状态
     */
    public function updateOverdueStatus(): JsonResponse
    {
        $count = EquipmentBorrow::where('status', EquipmentBorrow::STATUS_BORROWED)
                               ->where('expected_return_date', '<', now()->toDateString())
                               ->update(['status' => EquipmentBorrow::STATUS_OVERDUE]);

        return response()->json([
            'code' => 200,
            'message' => "已更新 {$count} 条逾期记录"
        ]);
    }
}
