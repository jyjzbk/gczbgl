<?php

namespace App\Services;

use App\Models\SchoolExperimentCatalogConfig;
use App\Models\ExperimentCatalogCompletionBaseline;
use App\Models\ExperimentCatalog;
use App\Models\ExperimentRecord;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ExperimentCompletionCalculatorService
{
    /**
     * 计算学校实验完成率
     */
    public function calculateSchoolCompletionRate(int $schoolId, array $filters = []): array
    {
        // 1. 获取学校的目录配置
        $config = SchoolExperimentCatalogConfig::getActiveConfig($schoolId);
        if (!$config) {
            throw new \Exception('学校未配置实验目录，无法计算完成率');
        }

        // 2. 获取基准实验目录
        $baselineCatalogs = $this->getBaselineCatalogs($config, $filters);
        
        // 3. 获取学校实际完成的实验
        $completedExperiments = $this->getCompletedExperiments($schoolId, $filters);
        
        // 4. 计算各维度完成率
        $completionData = $this->calculateCompletionRates($baselineCatalogs, $completedExperiments);
        
        // 5. 更新基准表数据
        $this->updateCompletionBaselines($schoolId, $config->id, $completionData);
        
        return $completionData;
    }

    /**
     * 获取基准实验目录
     */
    private function getBaselineCatalogs(SchoolExperimentCatalogConfig $config, array $filters): Collection
    {
        $query = ExperimentCatalog::where('management_level', $config->source_level)
            ->where('created_by_org_id', $config->source_org_id)
            ->where('status', 1);

        // 如果不允许删除实验，则过滤掉被下级删除的实验
        if (!$config->can_delete_experiments) {
            $query->where('is_deleted_by_lower', false);
        }

        // 应用筛选条件
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
        
        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }
        
        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }
        
        if (!empty($filters['experiment_type'])) {
            $query->where('experiment_type', $filters['experiment_type']);
        }

        return $query->get();
    }

    /**
     * 获取学校完成的实验
     */
    private function getCompletedExperiments(int $schoolId, array $filters): Collection
    {
        $query = ExperimentRecord::where('school_id', $schoolId)
            ->where('status', 'completed');

        // 应用时间筛选
        if (!empty($filters['start_date'])) {
            $query->where('completed_at', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('completed_at', '<=', $filters['end_date']);
        }

        // 关联实验目录进行筛选
        $query->whereHas('experimentCatalog', function($q) use ($filters) {
            if (!empty($filters['subject_id'])) {
                $q->where('subject_id', $filters['subject_id']);
            }
            if (!empty($filters['grade'])) {
                $q->where('grade', $filters['grade']);
            }
            if (!empty($filters['semester'])) {
                $q->where('semester', $filters['semester']);
            }
        });

        return $query->with('experimentCatalog')->get();
    }

    /**
     * 计算各维度完成率
     */
    private function calculateCompletionRates(Collection $baselineCatalogs, Collection $completedExperiments): array
    {
        $completedCatalogIds = $completedExperiments->pluck('experiment_catalog_id')->unique();
        
        // 总体完成率
        $totalBaseline = $baselineCatalogs->count();
        $totalCompleted = $completedCatalogIds->count();
        $overallRate = $totalBaseline > 0 ? round(($totalCompleted / $totalBaseline) * 100, 2) : 0;

        // 按学科统计
        $subjectStats = $this->calculateByDimension($baselineCatalogs, $completedCatalogIds, 'subject_id');
        
        // 按年级统计
        $gradeStats = $this->calculateByDimension($baselineCatalogs, $completedCatalogIds, 'grade');
        
        // 按学期统计
        $semesterStats = $this->calculateByDimension($baselineCatalogs, $completedCatalogIds, 'semester');
        
        // 按实验类型统计
        $typeStats = $this->calculateByDimension($baselineCatalogs, $completedCatalogIds, 'experiment_type');

        return [
            'overall' => [
                'total_experiments' => $totalBaseline,
                'completed_experiments' => $totalCompleted,
                'completion_rate' => $overallRate
            ],
            'by_subject' => $subjectStats,
            'by_grade' => $gradeStats,
            'by_semester' => $semesterStats,
            'by_type' => $typeStats,
            'detailed_list' => $this->getDetailedCompletionList($baselineCatalogs, $completedCatalogIds)
        ];
    }

    /**
     * 按维度计算完成率
     */
    private function calculateByDimension(Collection $baselineCatalogs, Collection $completedCatalogIds, string $dimension): array
    {
        $stats = [];
        
        // 按维度分组基准目录
        $baselineGroups = $baselineCatalogs->groupBy($dimension);
        
        foreach ($baselineGroups as $dimensionValue => $catalogs) {
            $catalogIds = $catalogs->pluck('id');
            $completedCount = $completedCatalogIds->intersect($catalogIds)->count();
            $totalCount = $catalogIds->count();
            $rate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 2) : 0;
            
            $stats[$dimensionValue] = [
                'total' => $totalCount,
                'completed' => $completedCount,
                'rate' => $rate,
                'dimension_name' => $this->getDimensionName($dimension, $dimensionValue)
            ];
        }
        
        return $stats;
    }

    /**
     * 获取详细完成列表
     */
    private function getDetailedCompletionList(Collection $baselineCatalogs, Collection $completedCatalogIds): array
    {
        return $baselineCatalogs->map(function($catalog) use ($completedCatalogIds) {
            return [
                'catalog_id' => $catalog->id,
                'name' => $catalog->name,
                'code' => $catalog->code,
                'subject_id' => $catalog->subject_id,
                'grade' => $catalog->grade,
                'semester' => $catalog->semester,
                'experiment_type' => $catalog->experiment_type,
                'is_completed' => $completedCatalogIds->contains($catalog->id),
                'difficulty_level' => $catalog->difficulty_level,
                'duration' => $catalog->duration
            ];
        })->toArray();
    }

    /**
     * 更新完成率基准表
     */
    private function updateCompletionBaselines(int $schoolId, int $configId, array $completionData): void
    {
        foreach ($completionData['by_subject'] as $subjectId => $subjectData) {
            foreach ($completionData['by_grade'] as $grade => $gradeData) {
                foreach ($completionData['by_semester'] as $semester => $semesterData) {
                    // 计算该学科+年级+学期的具体数据
                    $specificData = $this->calculateSpecificBaseline($completionData['detailed_list'], $subjectId, $grade, $semester);
                    
                    ExperimentCatalogCompletionBaseline::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'config_id' => $configId,
                            'subject_id' => $subjectId,
                            'grade' => $grade,
                            'semester' => $semester
                        ],
                        [
                            'total_experiments' => $specificData['total'],
                            'required_experiments' => $specificData['required'],
                            'optional_experiments' => $specificData['optional'],
                            'demo_experiments' => $specificData['demo'],
                            'group_experiments' => $specificData['group'],
                            'completed_experiments' => $specificData['completed'],
                            'completion_rate' => $specificData['rate'],
                            'last_calculated_at' => now(),
                            'calculated_by' => auth()->id()
                        ]
                    );
                }
            }
        }
    }

    /**
     * 计算特定条件的基准数据
     */
    private function calculateSpecificBaseline(array $detailedList, int $subjectId, int $grade, int $semester): array
    {
        $filtered = collect($detailedList)->filter(function($item) use ($subjectId, $grade, $semester) {
            return $item['subject_id'] == $subjectId && 
                   $item['grade'] == $grade && 
                   $item['semester'] == $semester;
        });

        $total = $filtered->count();
        $completed = $filtered->where('is_completed', true)->count();
        $required = $filtered->where('experiment_type', '必做')->count();
        $optional = $filtered->where('experiment_type', '选做')->count();
        $demo = $filtered->where('experiment_type', '演示')->count();
        $group = $filtered->where('experiment_type', '分组')->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'required' => $required,
            'optional' => $optional,
            'demo' => $demo,
            'group' => $group,
            'rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ];
    }

    /**
     * 获取维度名称
     */
    private function getDimensionName(string $dimension, $value): string
    {
        switch ($dimension) {
            case 'subject_id':
                return DB::table('subjects')->where('id', $value)->value('name') ?? '未知学科';
            case 'grade':
                return $value . '年级';
            case 'semester':
                return $value == 1 ? '上学期' : '下学期';
            case 'experiment_type':
                return $value;
            default:
                return (string)$value;
        }
    }

    /**
     * 批量计算多个学校的完成率
     */
    public function batchCalculateCompletionRates(array $schoolIds, array $filters = []): array
    {
        $results = [];
        
        foreach ($schoolIds as $schoolId) {
            try {
                $results[$schoolId] = $this->calculateSchoolCompletionRate($schoolId, $filters);
            } catch (\Exception $e) {
                $results[$schoolId] = [
                    'error' => $e->getMessage(),
                    'overall' => ['completion_rate' => 0]
                ];
            }
        }
        
        return $results;
    }

    /**
     * 获取完成率排行榜
     */
    public function getCompletionRanking(array $schoolIds, array $filters = []): array
    {
        $results = $this->batchCalculateCompletionRates($schoolIds, $filters);
        
        $ranking = [];
        foreach ($results as $schoolId => $data) {
            if (!isset($data['error'])) {
                $school = School::find($schoolId);
                $ranking[] = [
                    'school_id' => $schoolId,
                    'school_name' => $school->name ?? '未知学校',
                    'completion_rate' => $data['overall']['completion_rate'],
                    'total_experiments' => $data['overall']['total_experiments'],
                    'completed_experiments' => $data['overall']['completed_experiments']
                ];
            }
        }
        
        // 按完成率排序
        usort($ranking, function($a, $b) {
            return $b['completion_rate'] <=> $a['completion_rate'];
        });
        
        // 添加排名
        foreach ($ranking as $index => &$item) {
            $item['rank'] = $index + 1;
        }
        
        return $ranking;
    }
}
