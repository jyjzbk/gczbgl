<?php

namespace App\Services;

use App\Models\ExperimentReservation;
use App\Models\Laboratory;
use App\Models\EquipmentBorrow;
use App\Models\User;
use Carbon\Carbon;

class ConflictDetectionService
{
    /**
     * 检测预约冲突
     */
    public function detectConflicts(ExperimentReservation $reservation): array
    {
        $conflicts = [];

        // 时间冲突检测
        $timeConflicts = $this->checkTimeConflicts($reservation);
        if (!empty($timeConflicts)) {
            $conflicts[] = [
                'type' => 'time',
                'severity' => 'high',
                'details' => $timeConflicts
            ];
        }

        // 教师冲突检测
        $teacherConflicts = $this->checkTeacherConflicts($reservation);
        if (!empty($teacherConflicts)) {
            $conflicts[] = [
                'type' => 'teacher',
                'severity' => 'high',
                'details' => $teacherConflicts
            ];
        }

        // 容量冲突检测
        $capacityConflicts = $this->checkCapacityConflicts($reservation);
        if (!empty($capacityConflicts)) {
            $conflicts[] = [
                'type' => 'capacity',
                'severity' => 'medium',
                'details' => $capacityConflicts
            ];
        }

        // 设备冲突检测
        $equipmentConflicts = $this->checkEquipmentConflicts($reservation);
        if (!empty($equipmentConflicts)) {
            $conflicts[] = [
                'type' => 'equipment',
                'severity' => 'medium',
                'details' => $equipmentConflicts
            ];
        }

        return $conflicts;
    }

    /**
     * 检查时间段冲突
     */
    public function checkTimeSlotConflicts(
        int $laboratoryId,
        string $reservationDate,
        string $startTime,
        string $endTime,
        ?int $teacherId = null,
        ?int $studentCount = null,
        array $equipmentIds = [],
        ?int $excludeReservationId = null
    ): array {
        $conflicts = [];
        $date = Carbon::parse($reservationDate);
        $start = Carbon::parse($reservationDate . ' ' . $startTime);
        $end = Carbon::parse($reservationDate . ' ' . $endTime);

        // 检查实验室时间冲突
        $existingReservations = ExperimentReservation::where('laboratory_id', $laboratoryId)
            ->where('reservation_date', $date)
            ->where('status', '!=', ExperimentReservation::STATUS_CANCELLED)
            ->when($excludeReservationId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->get();

        foreach ($existingReservations as $existing) {
            $existingStart = Carbon::parse($existing->reservation_date . ' ' . $existing->start_time);
            $existingEnd = Carbon::parse($existing->reservation_date . ' ' . $existing->end_time);

            if ($this->isTimeOverlap($start, $end, $existingStart, $existingEnd)) {
                $conflicts[] = [
                    'type' => 'laboratory_time',
                    'message' => '实验室时间冲突',
                    'existing_reservation' => [
                        'id' => $existing->id,
                        'experiment_name' => $existing->catalog->name,
                        'teacher_name' => $existing->teacher->name,
                        'time_slot' => $existing->time_slot
                    ]
                ];
            }
        }

        // 检查教师时间冲突
        if ($teacherId) {
            $teacherReservations = ExperimentReservation::where('teacher_id', $teacherId)
                ->where('reservation_date', $date)
                ->where('status', '!=', ExperimentReservation::STATUS_CANCELLED)
                ->when($excludeReservationId, function ($query, $excludeId) {
                    return $query->where('id', '!=', $excludeId);
                })
                ->get();

            foreach ($teacherReservations as $existing) {
                $existingStart = Carbon::parse($existing->reservation_date . ' ' . $existing->start_time);
                $existingEnd = Carbon::parse($existing->reservation_date . ' ' . $existing->end_time);

                if ($this->isTimeOverlap($start, $end, $existingStart, $existingEnd)) {
                    $conflicts[] = [
                        'type' => 'teacher_time',
                        'message' => '教师时间冲突',
                        'existing_reservation' => [
                            'id' => $existing->id,
                            'experiment_name' => $existing->catalog->name,
                            'laboratory_name' => $existing->laboratory->name,
                            'time_slot' => $existing->time_slot
                        ]
                    ];
                }
            }
        }

        // 检查实验室容量
        if ($studentCount) {
            $laboratory = Laboratory::find($laboratoryId);
            if ($laboratory && $studentCount > $laboratory->capacity) {
                $conflicts[] = [
                    'type' => 'capacity',
                    'message' => '学生人数超过实验室容量',
                    'details' => [
                        'student_count' => $studentCount,
                        'laboratory_capacity' => $laboratory->capacity,
                        'overflow' => $studentCount - $laboratory->capacity
                    ]
                ];
            }
        }

        // 检查设备冲突
        if (!empty($equipmentIds)) {
            $equipmentConflicts = $this->checkEquipmentTimeConflicts(
                $equipmentIds,
                $date,
                $start,
                $end,
                $excludeReservationId
            );
            $conflicts = array_merge($conflicts, $equipmentConflicts);
        }

        return $conflicts;
    }

    /**
     * 检查时间冲突
     */
    private function checkTimeConflicts(ExperimentReservation $reservation): array
    {
        $conflicts = [];
        $start = Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time);
        $end = Carbon::parse($reservation->reservation_date . ' ' . $reservation->end_time);

        $existingReservations = ExperimentReservation::where('laboratory_id', $reservation->laboratory_id)
            ->where('reservation_date', $reservation->reservation_date)
            ->where('id', '!=', $reservation->id)
            ->where('status', '!=', ExperimentReservation::STATUS_CANCELLED)
            ->get();

        foreach ($existingReservations as $existing) {
            $existingStart = Carbon::parse($existing->reservation_date . ' ' . $existing->start_time);
            $existingEnd = Carbon::parse($existing->reservation_date . ' ' . $existing->end_time);

            if ($this->isTimeOverlap($start, $end, $existingStart, $existingEnd)) {
                $conflicts[] = [
                    'reservation_id' => $existing->id,
                    'experiment_name' => $existing->catalog->name,
                    'teacher_name' => $existing->teacher->name,
                    'time_slot' => $existing->time_slot,
                    'overlap_minutes' => $this->calculateOverlapMinutes($start, $end, $existingStart, $existingEnd)
                ];
            }
        }

        return $conflicts;
    }

    /**
     * 检查教师冲突
     */
    private function checkTeacherConflicts(ExperimentReservation $reservation): array
    {
        $conflicts = [];
        $start = Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time);
        $end = Carbon::parse($reservation->reservation_date . ' ' . $reservation->end_time);

        $teacherReservations = ExperimentReservation::where('teacher_id', $reservation->teacher_id)
            ->where('reservation_date', $reservation->reservation_date)
            ->where('id', '!=', $reservation->id)
            ->where('status', '!=', ExperimentReservation::STATUS_CANCELLED)
            ->get();

        foreach ($teacherReservations as $existing) {
            $existingStart = Carbon::parse($existing->reservation_date . ' ' . $existing->start_time);
            $existingEnd = Carbon::parse($existing->reservation_date . ' ' . $existing->end_time);

            if ($this->isTimeOverlap($start, $end, $existingStart, $existingEnd)) {
                $conflicts[] = [
                    'reservation_id' => $existing->id,
                    'experiment_name' => $existing->catalog->name,
                    'laboratory_name' => $existing->laboratory->name,
                    'time_slot' => $existing->time_slot
                ];
            }
        }

        return $conflicts;
    }

    /**
     * 检查容量冲突
     */
    private function checkCapacityConflicts(ExperimentReservation $reservation): array
    {
        $conflicts = [];
        $laboratory = $reservation->laboratory;

        if ($reservation->student_count > $laboratory->capacity) {
            $conflicts[] = [
                'student_count' => $reservation->student_count,
                'laboratory_capacity' => $laboratory->capacity,
                'overflow' => $reservation->student_count - $laboratory->capacity
            ];
        }

        return $conflicts;
    }

    /**
     * 检查设备冲突
     */
    private function checkEquipmentConflicts(ExperimentReservation $reservation): array
    {
        if (!$reservation->equipment_requirements) {
            return [];
        }

        $conflicts = [];
        $equipmentIds = collect($reservation->equipment_requirements)->pluck('equipment_id')->toArray();

        return $this->checkEquipmentTimeConflicts(
            $equipmentIds,
            $reservation->reservation_date,
            Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time),
            Carbon::parse($reservation->reservation_date . ' ' . $reservation->end_time),
            $reservation->id
        );
    }

    /**
     * 检查设备时间冲突
     */
    private function checkEquipmentTimeConflicts(
        array $equipmentIds,
        $date,
        Carbon $start,
        Carbon $end,
        ?int $excludeReservationId = null
    ): array {
        $conflicts = [];

        $equipmentBorrows = EquipmentBorrow::whereIn('equipment_id', $equipmentIds)
            ->where('borrow_date', '<=', $date)
            ->where('expected_return_date', '>=', $date)
            ->where('status', EquipmentBorrow::STATUS_BORROWED)
            ->get();

        foreach ($equipmentBorrows as $borrow) {
            if ($excludeReservationId && $borrow->reservation_id == $excludeReservationId) {
                continue;
            }

            $conflicts[] = [
                'type' => 'equipment_borrowed',
                'message' => '设备已被借用',
                'equipment_name' => $borrow->equipment->name,
                'borrower_name' => $borrow->borrower->name,
                'borrow_date' => $borrow->borrow_date,
                'expected_return_date' => $borrow->expected_return_date
            ];
        }

        return $conflicts;
    }

    /**
     * 判断时间是否重叠
     */
    private function isTimeOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        return $start1->lt($end2) && $end1->gt($start2);
    }

    /**
     * 计算重叠时间（分钟）
     */
    private function calculateOverlapMinutes(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): int
    {
        $overlapStart = $start1->gt($start2) ? $start1 : $start2;
        $overlapEnd = $end1->lt($end2) ? $end1 : $end2;
        
        return $overlapStart->diffInMinutes($overlapEnd);
    }
}
