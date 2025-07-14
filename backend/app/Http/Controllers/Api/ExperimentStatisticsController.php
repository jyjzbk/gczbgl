<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExperimentRecord;
use App\Models\ExperimentReservation;
use App\Models\ExperimentCatalog;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExperimentStatisticsController extends Controller
{
    /**
     * 获取实验开出率统计
     */
    public function completionRate(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'grade' => 'nullable|integer|min:1|max:12'
        ]);

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // 基础查询
        $catalogQuery = ExperimentCatalog::query();
        $recordQuery = ExperimentRecord::query();

        // 应用筛选条件
        if ($request->school_id) {
            $recordQuery->where('school_id', $request->school_id);
        }

        if ($request->subject_id) {
            $catalogQuery->where('subject_id', $request->subject_id);
            $recordQuery->whereHas('catalog', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->grade) {
            $catalogQuery->where('grade', $request->grade);
            $recordQuery->whereHas('catalog', function($q) use ($request) {
                $q->where('grade', $request->grade);
            });
        }

        // 统计应做实验数
        $totalExperiments = $catalogQuery->where('status', 1)->count();

        // 统计已完成实验数
        $completedExperiments = $recordQuery
            ->where('status', ExperimentRecord::STATUS_COMPLETED)
            ->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        // 计算开出率
        $completionRate = $totalExperiments > 0 ? 
            round(($completedExperiments / $totalExperiments) * 100, 2) : 0;

        // 按类型统计
        $typeStats = $recordQuery
            ->join('experiment_catalogs', 'experiment_records.catalog_id', '=', 'experiment_catalogs.id')
            ->where('experiment_records.status', ExperimentRecord::STATUS_COMPLETED)
            ->whereBetween('experiment_records.start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('experiment_catalogs.type', DB::raw('COUNT(*) as count'))
            ->groupBy('experiment_catalogs.type')
            ->get()
            ->mapWithKeys(function($item) {
                $types = [
                    1 => 'required',    // 必做实验
                    2 => 'optional',    // 选做实验
                    3 => 'demo',        // 演示实验
                    4 => 'group'        // 分组实验
                ];
                return [$types[$item->type] ?? 'unknown' => $item->count];
            });

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'total_experiments' => $totalExperiments,
                'completed_experiments' => $completedExperiments,
                'completion_rate' => $completionRate,
                'type_statistics' => [
                    'required' => $typeStats['required'] ?? 0,
                    'optional' => $typeStats['optional'] ?? 0,
                    'demo' => $typeStats['demo'] ?? 0,
                    'group' => $typeStats['group'] ?? 0
                ]
            ]
        ]);
    }

    /**
     * 获取学校实验统计排行
     */
    public function schoolRanking(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();
        $limit = $request->limit ?? 10;

        $rankings = School::select([
                'schools.id',
                'schools.name',
                'schools.type',
                DB::raw('COUNT(experiment_records.id) as total_experiments'),
                DB::raw('AVG(experiment_records.completion_rate) as avg_completion_rate'),
                DB::raw('AVG(experiment_records.quality_score) as avg_quality_score')
            ])
            ->leftJoin('experiment_records', 'schools.id', '=', 'experiment_records.school_id')
            ->where('experiment_records.status', ExperimentRecord::STATUS_COMPLETED)
            ->whereBetween('experiment_records.start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('schools.id', 'schools.name', 'schools.type')
            ->orderByDesc('total_experiments')
            ->orderByDesc('avg_completion_rate')
            ->limit($limit)
            ->get()
            ->map(function($school, $index) {
                return [
                    'rank' => $index + 1,
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'school_type' => $school->type,
                    'total_experiments' => (int)$school->total_experiments,
                    'avg_completion_rate' => round($school->avg_completion_rate, 2),
                    'avg_quality_score' => round($school->avg_quality_score, 2)
                ];
            });

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'rankings' => $rankings
            ]
        ]);
    }

    /**
     * 获取实验趋势分析
     */
    public function trends(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'period' => 'nullable|in:week,month,quarter,year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $period = $request->period ?? 'month';
        $startDate = $request->start_date ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        // 根据周期确定分组格式
        $dateFormat = match($period) {
            'week' => '%Y-%u',      // 年-周
            'month' => '%Y-%m',     // 年-月
            'quarter' => '%Y-Q%q',  // 年-季度
            'year' => '%Y',         // 年
            default => '%Y-%m'
        };

        $query = ExperimentRecord::select([
                DB::raw("DATE_FORMAT(start_time, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('AVG(completion_rate) as avg_completion_rate'),
                DB::raw('AVG(quality_score) as avg_quality_score')
            ])
            ->where('status', ExperimentRecord::STATUS_COMPLETED)
            ->whereBetween('start_time', [$startDate, $endDate]);

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $trends = $query->groupBy('period')
                       ->orderBy('period')
                       ->get()
                       ->map(function($item) {
                           return [
                               'period' => $item->period,
                               'total_count' => (int)$item->total_count,
                               'avg_completion_rate' => round($item->avg_completion_rate, 2),
                               'avg_quality_score' => round($item->avg_quality_score, 2)
                           ];
                       });

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'period_type' => $period,
                'date_range' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString()
                ],
                'trends' => $trends
            ]
        ]);
    }

    /**
     * 获取学科实验统计
     */
    public function subjectStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        $query = Subject::select([
                'subjects.id',
                'subjects.name',
                'subjects.code',
                DB::raw('COUNT(experiment_records.id) as total_experiments'),
                DB::raw('AVG(experiment_records.completion_rate) as avg_completion_rate'),
                DB::raw('AVG(experiment_records.quality_score) as avg_quality_score')
            ])
            ->leftJoin('experiment_catalogs', 'subjects.id', '=', 'experiment_catalogs.subject_id')
            ->leftJoin('experiment_records', 'experiment_catalogs.id', '=', 'experiment_records.catalog_id')
            ->where('experiment_records.status', ExperimentRecord::STATUS_COMPLETED)
            ->whereBetween('experiment_records.start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->school_id) {
            $query->where('experiment_records.school_id', $request->school_id);
        }

        $statistics = $query->groupBy('subjects.id', 'subjects.name', 'subjects.code')
                           ->orderByDesc('total_experiments')
                           ->get()
                           ->map(function($subject) {
                               return [
                                   'subject_id' => $subject->id,
                                   'subject_name' => $subject->name,
                                   'subject_code' => $subject->code,
                                   'total_experiments' => (int)$subject->total_experiments,
                                   'avg_completion_rate' => round($subject->avg_completion_rate, 2),
                                   'avg_quality_score' => round($subject->avg_quality_score, 2)
                               ];
                           });

        return response()->json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'statistics' => $statistics
            ]
        ]);
    }
}
