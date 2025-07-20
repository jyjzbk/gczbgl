<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentReservation;
use App\Models\ExperimentCatalog;
use App\Models\Laboratory;
use App\Models\EquipmentBorrow;
use App\Models\ReservationConflictLog;
use App\Services\ConflictDetectionService;
use App\Services\EquipmentRequirementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SmartReservationController extends Controller
{
    protected $conflictDetectionService;
    protected $equipmentRequirementService;

    public function __construct(
        ConflictDetectionService $conflictDetectionService,
        EquipmentRequirementService $equipmentRequirementService
    ) {
        $this->conflictDetectionService = $conflictDetectionService;
        $this->equipmentRequirementService = $equipmentRequirementService;
    }

    /**
     * 获取实验室课表
     */
    public function getLaboratorySchedule(Request $request, $laboratoryId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'view_type' => 'in:week,month'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $laboratory = Laboratory::findOrFail($laboratoryId);
            $dateStart = Carbon::parse($request->date_start);
            $dateEnd = Carbon::parse($request->date_end);

            // 获取时间段内的预约记录
            $reservations = ExperimentReservation::with(['catalog', 'teacher', 'school'])
                ->where('laboratory_id', $laboratoryId)
                ->whereBetween('reservation_date', [$dateStart, $dateEnd])
                ->where('status', '!=', ExperimentReservation::STATUS_CANCELLED)
                ->orderBy('reservation_date')
                ->orderBy('start_time')
                ->get();

            // 构建课表数据
            $schedule = [];
            $current = $dateStart->copy();
            
            while ($current <= $dateEnd) {
                $dayReservations = $reservations->where('reservation_date', $current->toDateString());
                
                $schedule[] = [
                    'date' => $current->toDateString(),
                    'day_name' => $current->locale('zh_CN')->dayName,
                    'reservations' => $dayReservations->map(function ($reservation) {
                        return [
                            'id' => $reservation->id,
                            'experiment_name' => $reservation->catalog->name,
                            'teacher_name' => $reservation->teacher->name,
                            'class_name' => $reservation->class_name,
                            'student_count' => $reservation->student_count,
                            'start_time' => $reservation->start_time->format('H:i'),
                            'end_time' => $reservation->end_time->format('H:i'),
                            'status' => $reservation->status,
                            'status_text' => $reservation->status_text,
                            'status_color' => $reservation->status_color,
                            'priority' => $reservation->priority,
                            'priority_name' => $reservation->priority_name,
                            'priority_color' => $reservation->priority_color
                        ];
                    })->values()
                ];
                
                $current->addDay();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'laboratory' => [
                        'id' => $laboratory->id,
                        'name' => $laboratory->name,
                        'capacity' => $laboratory->capacity,
                        'location' => $laboratory->location
                    ],
                    'schedule' => $schedule,
                    'date_range' => [
                        'start' => $dateStart->toDateString(),
                        'end' => $dateEnd->toDateString()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '获取课表失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 智能预约创建
     */
    public function smartCreate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'catalog_id' => 'required|exists:experiment_catalogs,id',
            'laboratory_id' => 'required|exists:laboratories,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'class_name' => 'required|string|max:100',
            'student_count' => 'required|integer|min:1|max:100',
            'priority' => 'in:low,normal,high,urgent',
            'auto_borrow_equipment' => 'boolean',
            'preparation_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $catalog = ExperimentCatalog::findOrFail($request->catalog_id);
            $laboratory = Laboratory::findOrFail($request->laboratory_id);
            
            // 检查实验室容量
            if ($request->student_count > $laboratory->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => '学生人数超过实验室容量限制',
                    'data' => [
                        'student_count' => $request->student_count,
                        'laboratory_capacity' => $laboratory->capacity
                    ]
                ], 422);
            }

            // 生成器材需求清单
            $equipmentRequirements = $this->equipmentRequirementService
                ->generateRequirements($request->catalog_id, $request->student_count);

            // 创建预约记录
            $reservation = ExperimentReservation::create([
                'school_id' => auth()->user()->school_id,
                'catalog_id' => $request->catalog_id,
                'laboratory_id' => $request->laboratory_id,
                'teacher_id' => auth()->id(),
                'class_name' => $request->class_name,
                'student_count' => $request->student_count,
                'reservation_date' => $request->reservation_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => ExperimentReservation::STATUS_PENDING,
                'priority' => $request->priority ?? ExperimentReservation::PRIORITY_NORMAL,
                'equipment_requirements' => $equipmentRequirements,
                'auto_borrow_equipment' => $request->auto_borrow_equipment ?? true,
                'preparation_notes' => $request->preparation_notes,
                'remark' => "智能预约：{$catalog->name}"
            ]);

            // 冲突检测
            $conflicts = $this->conflictDetectionService->detectConflicts($reservation);
            
            // 记录冲突日志
            foreach ($conflicts as $conflict) {
                ReservationConflictLog::create([
                    'reservation_id' => $reservation->id,
                    'conflict_type' => $conflict['type'],
                    'conflict_details' => $conflict['details'],
                    'severity' => $conflict['severity']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '预约创建成功',
                'data' => [
                    'reservation' => [
                        'id' => $reservation->id,
                        'experiment_name' => $catalog->name,
                        'laboratory_name' => $laboratory->name,
                        'reservation_date' => $reservation->reservation_date->format('Y-m-d'),
                        'time_slot' => $reservation->time_slot,
                        'status' => $reservation->status,
                        'status_text' => $reservation->status_text,
                        'equipment_requirements' => $equipmentRequirements
                    ],
                    'conflicts' => $conflicts,
                    'has_conflicts' => count($conflicts) > 0
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '预约创建失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 预约冲突检测
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'laboratory_id' => 'required|exists:laboratories,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'teacher_id' => 'nullable|exists:users,id',
            'student_count' => 'nullable|integer|min:1',
            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'exists:equipments,id',
            'exclude_reservation_id' => 'nullable|exists:experiment_reservations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '参数验证失败',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $conflicts = $this->conflictDetectionService->checkTimeSlotConflicts(
                $request->laboratory_id,
                $request->reservation_date,
                $request->start_time,
                $request->end_time,
                $request->teacher_id,
                $request->student_count,
                $request->equipment_ids ?? [],
                $request->exclude_reservation_id
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'has_conflicts' => count($conflicts) > 0,
                    'conflicts' => $conflicts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '冲突检测失败：' . $e->getMessage()
            ], 500);
        }
    }
}
