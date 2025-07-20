<?php

namespace App\Services;

use App\Models\ExperimentReservation;
use App\Models\EquipmentBorrow;
use App\Models\Equipment;
use App\Models\ExperimentEquipmentRequirement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EquipmentBorrowService
{
    /**
     * 根据预约自动创建设备借用记录
     */
    public function createBorrowsFromReservation(ExperimentReservation $reservation): array
    {
        if (!$reservation->auto_borrow_equipment || !$reservation->equipment_requirements) {
            return [];
        }

        $borrows = [];
        
        DB::transaction(function () use ($reservation, &$borrows) {
            foreach ($reservation->equipment_requirements as $requirement) {
                // 检查设备是否可用
                $equipment = Equipment::find($requirement['equipment_id']);
                if (!$equipment || $equipment->status !== Equipment::STATUS_NORMAL) {
                    continue;
                }

                // 检查是否已经存在借用记录
                $existingBorrow = EquipmentBorrow::where('reservation_id', $reservation->id)
                    ->where('equipment_id', $requirement['equipment_id'])
                    ->first();

                if ($existingBorrow) {
                    continue;
                }

                // 创建借用记录
                $borrow = EquipmentBorrow::create([
                    'equipment_id' => $requirement['equipment_id'],
                    'reservation_id' => $reservation->id,
                    'borrower_id' => $reservation->teacher_id,
                    'quantity' => $requirement['required_quantity'],
                    'actual_quantity' => $requirement['required_quantity'],
                    'borrow_date' => $reservation->reservation_date,
                    'expected_return_date' => $reservation->reservation_date,
                    'purpose' => '实验教学：' . $reservation->catalog->name,
                    'status' => EquipmentBorrow::STATUS_APPROVED, // 自动审核通过
                    'approval_remark' => '系统自动创建（实验预约关联）',
                    'approved_by' => $reservation->teacher_id,
                    'approved_at' => now()
                ]);

                $borrows[] = $borrow;
            }
        });

        return $borrows;
    }

    /**
     * 更新预约关联的设备借用记录
     */
    public function updateBorrowsFromReservation(ExperimentReservation $reservation): array
    {
        if (!$reservation->auto_borrow_equipment) {
            // 如果关闭了自动借用，删除相关借用记录
            $this->deleteBorrowsFromReservation($reservation);
            return [];
        }

        // 获取现有的借用记录
        $existingBorrows = EquipmentBorrow::where('reservation_id', $reservation->id)->get();
        $existingEquipmentIds = $existingBorrows->pluck('equipment_id')->toArray();

        $newBorrows = [];
        $updatedBorrows = [];

        if ($reservation->equipment_requirements) {
            foreach ($reservation->equipment_requirements as $requirement) {
                $equipmentId = $requirement['equipment_id'];
                $existingBorrow = $existingBorrows->where('equipment_id', $equipmentId)->first();

                if ($existingBorrow) {
                    // 更新现有借用记录
                    $existingBorrow->update([
                        'quantity' => $requirement['required_quantity'],
                        'actual_quantity' => $requirement['required_quantity'],
                        'borrow_date' => $reservation->reservation_date,
                        'expected_return_date' => $reservation->reservation_date
                    ]);
                    $updatedBorrows[] = $existingBorrow;
                } else {
                    // 创建新的借用记录
                    $equipment = Equipment::find($equipmentId);
                    if ($equipment && $equipment->status === Equipment::STATUS_NORMAL) {
                        $borrow = EquipmentBorrow::create([
                            'equipment_id' => $equipmentId,
                            'reservation_id' => $reservation->id,
                            'borrower_id' => $reservation->teacher_id,
                            'quantity' => $requirement['required_quantity'],
                            'actual_quantity' => $requirement['required_quantity'],
                            'borrow_date' => $reservation->reservation_date,
                            'expected_return_date' => $reservation->reservation_date,
                            'purpose' => '实验教学：' . $reservation->catalog->name,
                            'status' => EquipmentBorrow::STATUS_APPROVED,
                            'approval_remark' => '系统自动创建（实验预约关联）',
                            'approved_by' => $reservation->teacher_id,
                            'approved_at' => now()
                        ]);
                        $newBorrows[] = $borrow;
                    }
                }
            }

            // 删除不再需要的借用记录
            $newEquipmentIds = collect($reservation->equipment_requirements)->pluck('equipment_id')->toArray();
            $toDeleteIds = array_diff($existingEquipmentIds, $newEquipmentIds);
            
            if (!empty($toDeleteIds)) {
                EquipmentBorrow::where('reservation_id', $reservation->id)
                    ->whereIn('equipment_id', $toDeleteIds)
                    ->where('status', '!=', EquipmentBorrow::STATUS_RETURNED)
                    ->delete();
            }
        }

        return array_merge($newBorrows, $updatedBorrows);
    }

    /**
     * 删除预约关联的设备借用记录
     */
    public function deleteBorrowsFromReservation(ExperimentReservation $reservation): int
    {
        return EquipmentBorrow::where('reservation_id', $reservation->id)
            ->where('status', '!=', EquipmentBorrow::STATUS_RETURNED)
            ->delete();
    }

    /**
     * 实验开始时自动借出设备
     */
    public function borrowEquipmentForExperiment(ExperimentReservation $reservation): array
    {
        $borrows = EquipmentBorrow::where('reservation_id', $reservation->id)
            ->where('status', EquipmentBorrow::STATUS_APPROVED)
            ->get();

        $borrowedEquipment = [];

        foreach ($borrows as $borrow) {
            // 检查设备可用性
            if (!$this->isEquipmentAvailable($borrow->equipment_id, $borrow->quantity)) {
                continue;
            }

            // 更新借用状态
            $borrow->update([
                'status' => EquipmentBorrow::STATUS_BORROWED,
                'actual_borrow_time' => now(),
                'condition_before' => $this->getEquipmentCondition($borrow->equipment_id)
            ]);

            $borrowedEquipment[] = $borrow;
        }

        return $borrowedEquipment;
    }

    /**
     * 实验结束时自动归还设备
     */
    public function returnEquipmentFromExperiment(ExperimentReservation $reservation, array $returnData = []): array
    {
        $borrows = EquipmentBorrow::where('reservation_id', $reservation->id)
            ->where('status', EquipmentBorrow::STATUS_BORROWED)
            ->get();

        $returnedEquipment = [];

        foreach ($borrows as $borrow) {
            $equipmentId = $borrow->equipment_id;
            $returnInfo = $returnData[$equipmentId] ?? [];

            // 更新归还信息
            $borrow->update([
                'status' => EquipmentBorrow::STATUS_RETURNED,
                'actual_return_time' => now(),
                'condition_after' => $returnInfo['condition_after'] ?? $this->getEquipmentCondition($equipmentId),
                'has_damage' => $returnInfo['has_damage'] ?? false,
                'damage_description' => $returnInfo['damage_description'] ?? null,
                'damage_cost' => $returnInfo['damage_cost'] ?? null
            ]);

            // 如果有损坏，更新设备状态
            if ($borrow->has_damage) {
                $borrow->equipment->update([
                    'status' => Equipment::STATUS_MAINTENANCE
                ]);
            }

            $returnedEquipment[] = $borrow;
        }

        return $returnedEquipment;
    }

    /**
     * 检查设备是否可用
     */
    public function isEquipmentAvailable(int $equipmentId, int $quantity = 1): bool
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment || $equipment->status !== Equipment::STATUS_NORMAL) {
            return false;
        }

        // 检查是否有其他借用冲突
        $borrowedQuantity = EquipmentBorrow::where('equipment_id', $equipmentId)
            ->where('status', EquipmentBorrow::STATUS_BORROWED)
            ->sum('actual_quantity');

        // 简化处理：假设每个设备ID代表一个设备单位
        $totalQuantity = Equipment::where('id', $equipmentId)
            ->where('status', Equipment::STATUS_NORMAL)
            ->count();

        return ($borrowedQuantity + $quantity) <= $totalQuantity;
    }

    /**
     * 获取设备状态信息
     */
    private function getEquipmentCondition(int $equipmentId): array
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment) {
            return [];
        }

        return [
            'status' => $equipment->status,
            'last_maintenance' => $equipment->last_maintenance_date,
            'condition_note' => '设备状态正常',
            'checked_at' => now()->toISOString()
        ];
    }

    /**
     * 获取预约的设备借用统计
     */
    public function getReservationBorrowStats(ExperimentReservation $reservation): array
    {
        $borrows = EquipmentBorrow::where('reservation_id', $reservation->id)->get();

        return [
            'total_items' => $borrows->count(),
            'approved_items' => $borrows->where('status', EquipmentBorrow::STATUS_APPROVED)->count(),
            'borrowed_items' => $borrows->where('status', EquipmentBorrow::STATUS_BORROWED)->count(),
            'returned_items' => $borrows->where('status', EquipmentBorrow::STATUS_RETURNED)->count(),
            'damaged_items' => $borrows->where('has_damage', true)->count(),
            'total_damage_cost' => $borrows->sum('damage_cost')
        ];
    }

    /**
     * 生成设备借用清单
     */
    public function generateBorrowList(ExperimentReservation $reservation): array
    {
        $borrows = EquipmentBorrow::with('equipment')
            ->where('reservation_id', $reservation->id)
            ->orderBy('equipment_id')
            ->get();

        return $borrows->map(function ($borrow) {
            return [
                'id' => $borrow->id,
                'equipment_id' => $borrow->equipment_id,
                'equipment_name' => $borrow->equipment->name,
                'equipment_code' => $borrow->equipment->code,
                'quantity' => $borrow->quantity,
                'actual_quantity' => $borrow->actual_quantity,
                'status' => $borrow->status,
                'status_text' => $borrow->status_text,
                'borrow_date' => $borrow->borrow_date,
                'expected_return_date' => $borrow->expected_return_date,
                'has_damage' => $borrow->has_damage,
                'damage_cost' => $borrow->damage_cost
            ];
        })->toArray();
    }
}
