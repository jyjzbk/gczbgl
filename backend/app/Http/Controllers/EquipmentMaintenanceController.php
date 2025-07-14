<?php

namespace App\Http\Controllers;

use App\Models\EquipmentMaintenance;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class EquipmentMaintenanceController extends Controller
{
    /**
     * 获取维修记录列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = EquipmentMaintenance::with([
            'equipment.category',
            'equipment.school',
            'reporter',
            'maintainer'
        ]);

        // 筛选条件
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('reporter_id')) {
            $query->where('reporter_id', $request->reporter_id);
        }

        if ($request->filled('maintainer_id')) {
            $query->where('maintainer_id', $request->maintainer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency_level')) {
            $query->where('urgency_level', $request->urgency_level);
        }

        if ($request->filled('fault_type')) {
            $query->where('fault_type', 'like', "%{$request->fault_type}%");
        }

        if ($request->filled('school_id')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // 日期范围筛选
        if ($request->filled('report_date_start')) {
            $query->where('report_date', '>=', $request->report_date_start);
        }

        if ($request->filled('report_date_end')) {
            $query->where('report_date', '<=', $request->report_date_end);
        }

        // 成本范围筛选
        if ($request->filled('cost_min')) {
            $query->where('cost', '>=', $request->cost_min);
        }

        if ($request->filled('cost_max')) {
            $query->where('cost', '<=', $request->cost_max);
        }

        // 特殊筛选
        if ($request->boolean('pending_only')) {
            $query->pending();
        }

        if ($request->boolean('processing_only')) {
            $query->processing();
        }

        if ($request->boolean('high_priority_only')) {
            $query->highPriority();
        }

        // 搜索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('equipment', function($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                })->orWhere('fault_description', 'like', "%{$search}%")
                  ->orWhere('fault_type', 'like', "%{$search}%")
                  ->orWhereHas('reporter', function($user) use ($search) {
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
        $maintenances = $query->paginate($perPage);

        // 添加计算字段
        $maintenances->getCollection()->transform(function ($maintenance) {
            $maintenance->maintenance_days = $maintenance->maintenance_days;
            $maintenance->waiting_days = $maintenance->waiting_days;
            return $maintenance;
        });

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => [
                'items' => $maintenances->items(),
                'pagination' => [
                    'current_page' => $maintenances->currentPage(),
                    'last_page' => $maintenances->lastPage(),
                    'per_page' => $maintenances->perPage(),
                    'total' => $maintenances->total()
                ]
            ]
        ]);
    }

    /**
     * 创建维修报告
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'fault_description' => 'required|string|max:1000',
            'fault_type' => 'required|string|max:100',
            'urgency_level' => 'required|integer|in:1,2,3',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remark' => 'nullable|string|max:1000'
        ]);

        $data = $request->only([
            'equipment_id', 'fault_description', 'fault_type',
            'urgency_level', 'remark'
        ]);

        $data['reporter_id'] = auth()->id();
        $data['report_date'] = now()->toDateString();
        $data['status'] = EquipmentMaintenance::STATUS_PENDING;

        // 处理照片上传
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance_photos', 'public');
                $photos[] = $path;
            }
            $data['photos'] = $photos;
        }

        $maintenance = EquipmentMaintenance::create($data);

        // 更新设备状态为维修中
        $equipment = Equipment::find($data['equipment_id']);
        $equipment->update(['status' => Equipment::STATUS_MAINTENANCE]);

        $maintenance->load(['equipment.category', 'reporter']);

        return response()->json([
            'code' => 201,
            'message' => '维修报告提交成功',
            'data' => $maintenance
        ], 201);
    }

    /**
     * 获取维修记录详情
     */
    public function show(EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        $equipmentMaintenance->load([
            'equipment.category',
            'equipment.school',
            'equipment.laboratory',
            'reporter',
            'maintainer'
        ]);

        // 添加计算字段
        $equipmentMaintenance->maintenance_days = $equipmentMaintenance->maintenance_days;
        $equipmentMaintenance->waiting_days = $equipmentMaintenance->waiting_days;
        $equipmentMaintenance->can_start = $equipmentMaintenance->canStart();
        $equipmentMaintenance->can_complete = $equipmentMaintenance->canComplete();
        $equipmentMaintenance->can_assign = $equipmentMaintenance->canAssign();

        return response()->json([
            'code' => 200,
            'message' => '查询成功',
            'data' => $equipmentMaintenance
        ]);
    }

    /**
     * 更新维修记录
     */
    public function update(Request $request, EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        // 只有待维修状态的记录可以修改
        if ($equipmentMaintenance->status !== EquipmentMaintenance::STATUS_PENDING) {
            return response()->json([
                'code' => 422,
                'message' => '只有待维修状态的记录可以修改'
            ], 422);
        }

        $request->validate([
            'fault_description' => 'required|string|max:1000',
            'fault_type' => 'required|string|max:100',
            'urgency_level' => 'required|integer|in:1,2,3',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remark' => 'nullable|string|max:1000'
        ]);

        $data = $request->only([
            'fault_description', 'fault_type', 'urgency_level', 'remark'
        ]);

        // 处理照片上传
        if ($request->hasFile('photos')) {
            // 删除旧照片
            if ($equipmentMaintenance->photos) {
                foreach ($equipmentMaintenance->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance_photos', 'public');
                $photos[] = $path;
            }
            $data['photos'] = $photos;
        }

        $equipmentMaintenance->update($data);
        $equipmentMaintenance->load(['equipment.category', 'reporter']);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'data' => $equipmentMaintenance
        ]);
    }

    /**
     * 删除维修记录
     */
    public function destroy(EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        // 只有待维修状态的记录可以删除
        if ($equipmentMaintenance->status !== EquipmentMaintenance::STATUS_PENDING) {
            return response()->json([
                'code' => 422,
                'message' => '只有待维修状态的记录可以删除'
            ], 422);
        }

        // 删除相关照片
        if ($equipmentMaintenance->photos) {
            foreach ($equipmentMaintenance->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        // 恢复设备状态
        $equipmentMaintenance->equipment->update(['status' => Equipment::STATUS_NORMAL]);

        $equipmentMaintenance->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }

    /**
     * 分配维修人员
     */
    public function assignMaintainer(Request $request, EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        $request->validate([
            'maintainer_id' => 'required|exists:users,id'
        ]);

        if (!$equipmentMaintenance->canAssign()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许分配维修人员'
            ], 422);
        }

        try {
            $equipmentMaintenance->assignMaintainer($request->maintainer_id);
            $equipmentMaintenance->load(['equipment.category', 'reporter', 'maintainer']);

            return response()->json([
                'code' => 200,
                'message' => '维修人员分配成功',
                'data' => $equipmentMaintenance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '分配失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 开始维修
     */
    public function startMaintenance(Request $request, EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        $request->validate([
            'maintainer_id' => 'nullable|exists:users,id'
        ]);

        if (!$equipmentMaintenance->canStart()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许开始维修'
            ], 422);
        }

        try {
            $maintainerId = $request->maintainer_id ?: auth()->id();
            $equipmentMaintenance->startMaintenance($maintainerId);
            $equipmentMaintenance->load(['equipment.category', 'reporter', 'maintainer']);

            return response()->json([
                'code' => 200,
                'message' => '维修已开始',
                'data' => $equipmentMaintenance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '开始维修失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 完成维修
     */
    public function completeMaintenance(Request $request, EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        $request->validate([
            'solution' => 'required|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'parts_replaced' => 'nullable|string|max:500',
            'quality_rating' => 'nullable|integer|min:1|max:5'
        ]);

        if (!$equipmentMaintenance->canComplete()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许完成维修'
            ], 422);
        }

        try {
            $equipmentMaintenance->completeMaintenance(
                $request->solution,
                $request->cost,
                $request->parts_replaced,
                $request->quality_rating
            );

            $equipmentMaintenance->load(['equipment.category', 'reporter', 'maintainer']);

            return response()->json([
                'code' => 200,
                'message' => '维修完成',
                'data' => $equipmentMaintenance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '完成维修失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 标记为无法修复
     */
    public function markUnrepairable(Request $request, EquipmentMaintenance $equipmentMaintenance): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        if (!$equipmentMaintenance->canComplete()) {
            return response()->json([
                'code' => 422,
                'message' => '当前状态不允许标记为无法修复'
            ], 422);
        }

        try {
            $equipmentMaintenance->markUnrepairable($request->reason);
            $equipmentMaintenance->load(['equipment.category', 'reporter', 'maintainer']);

            return response()->json([
                'code' => 200,
                'message' => '已标记为无法修复',
                'data' => $equipmentMaintenance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '操作失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 批量操作
     */
    public function batchAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:assign,start,complete',
            'maintenance_ids' => 'required|array|min:1',
            'maintenance_ids.*' => 'exists:equipment_maintenances,id',
            'maintainer_id' => 'required_if:action,assign,start|exists:users,id',
            'solution' => 'required_if:action,complete|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'parts_replaced' => 'nullable|string|max:500'
        ]);

        $maintenances = EquipmentMaintenance::whereIn('id', $request->maintenance_ids)->get();
        $successCount = 0;
        $errors = [];

        foreach ($maintenances as $maintenance) {
            try {
                switch ($request->action) {
                    case 'assign':
                        if ($maintenance->canAssign()) {
                            $maintenance->assignMaintainer($request->maintainer_id);
                            $successCount++;
                        } else {
                            $errors[] = "维修记录 #{$maintenance->id} 状态不允许分配维修人员";
                        }
                        break;
                    case 'start':
                        if ($maintenance->canStart()) {
                            $maintenance->startMaintenance($request->maintainer_id);
                            $successCount++;
                        } else {
                            $errors[] = "维修记录 #{$maintenance->id} 状态不允许开始维修";
                        }
                        break;
                    case 'complete':
                        if ($maintenance->canComplete()) {
                            $maintenance->completeMaintenance(
                                $request->solution,
                                $request->cost,
                                $request->parts_replaced
                            );
                            $successCount++;
                        } else {
                            $errors[] = "维修记录 #{$maintenance->id} 状态不允许完成维修";
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "维修记录 #{$maintenance->id} 操作失败：" . $e->getMessage();
            }
        }

        return response()->json([
            'code' => 200,
            'message' => "批量操作完成，成功处理 {$successCount} 条记录",
            'data' => [
                'success_count' => $successCount,
                'total_count' => count($maintenances),
                'errors' => $errors
            ]
        ]);
    }
}
