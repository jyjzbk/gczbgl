<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ExperimentCatalogCompletionBaseline extends Model
{
    use HasFactory;

    protected $table = 'experiment_catalog_completion_baselines';

    protected $fillable = [
        'school_id',
        'config_id',
        'subject_id',
        'grade',
        'semester',
        'total_experiments',
        'required_experiments',
        'optional_experiments',
        'demo_experiments',
        'group_experiments',
        'completed_experiments',
        'completion_rate',
        'last_calculated_at',
        'calculated_by'
    ];

    protected $casts = [
        'grade' => 'integer',
        'semester' => 'integer',
        'total_experiments' => 'integer',
        'required_experiments' => 'integer',
        'optional_experiments' => 'integer',
        'demo_experiments' => 'integer',
        'group_experiments' => 'integer',
        'completed_experiments' => 'integer',
        'completion_rate' => 'decimal:2',
        'last_calculated_at' => 'datetime'
    ];

    /**
     * 学期常量
     */
    const SEMESTER_FIRST = 1;
    const SEMESTER_SECOND = 2;

    const SEMESTERS = [
        self::SEMESTER_FIRST => '上学期',
        self::SEMESTER_SECOND => '下学期'
    ];

    /**
     * 学校关联
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * 配置关联
     */
    public function config(): BelongsTo
    {
        return $this->belongsTo(SchoolExperimentCatalogConfig::class, 'config_id');
    }

    /**
     * 学科关联
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * 计算操作人关联
     */
    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * 获取学期中文名称
     */
    public function getSemesterNameAttribute(): string
    {
        return self::SEMESTERS[$this->semester] ?? '未知学期';
    }

    /**
     * 获取年级中文名称
     */
    public function getGradeNameAttribute(): string
    {
        return $this->grade . '年级';
    }

    /**
     * 检查完成率等级
     */
    public function getCompletionLevelAttribute(): string
    {
        $rate = $this->completion_rate;
        
        if ($rate >= 90) return 'excellent';
        if ($rate >= 80) return 'good';
        if ($rate >= 70) return 'fair';
        if ($rate >= 60) return 'poor';
        return 'very_poor';
    }

    /**
     * 获取完成率等级中文名称
     */
    public function getCompletionLevelNameAttribute(): string
    {
        $levels = [
            'excellent' => '优秀',
            'good' => '良好',
            'fair' => '一般',
            'poor' => '较差',
            'very_poor' => '很差'
        ];

        return $levels[$this->completion_level] ?? '未知';
    }

    /**
     * 获取学校的基准数据
     */
    public static function getSchoolBaselines(int $schoolId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::where('school_id', $schoolId)
            ->with(['subject:id,name', 'config:id,source_org_name']);

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

        return $query->orderBy('subject_id')
                    ->orderBy('grade')
                    ->orderBy('semester')
                    ->get();
    }

    /**
     * 获取学校总体完成率
     */
    public static function getSchoolOverallRate(int $schoolId): array
    {
        $baselines = self::where('school_id', $schoolId)->get();
        
        if ($baselines->isEmpty()) {
            return [
                'total_experiments' => 0,
                'completed_experiments' => 0,
                'completion_rate' => 0
            ];
        }

        $totalExperiments = $baselines->sum('total_experiments');
        $completedExperiments = $baselines->sum('completed_experiments');
        $completionRate = $totalExperiments > 0 ? 
            round(($completedExperiments / $totalExperiments) * 100, 2) : 0;

        return [
            'total_experiments' => $totalExperiments,
            'completed_experiments' => $completedExperiments,
            'completion_rate' => $completionRate
        ];
    }

    /**
     * 获取按维度统计的完成率
     */
    public static function getCompletionByDimension(int $schoolId, string $dimension): array
    {
        $validDimensions = ['subject_id', 'grade', 'semester'];
        if (!in_array($dimension, $validDimensions)) {
            throw new \InvalidArgumentException("Invalid dimension: {$dimension}");
        }

        $results = self::where('school_id', $schoolId)
            ->selectRaw("
                {$dimension},
                SUM(total_experiments) as total_experiments,
                SUM(completed_experiments) as completed_experiments,
                ROUND(SUM(completed_experiments) * 100.0 / SUM(total_experiments), 2) as completion_rate
            ")
            ->groupBy($dimension)
            ->having('total_experiments', '>', 0)
            ->get();

        // 添加维度名称
        return $results->map(function($item) use ($dimension) {
            $item->dimension_name = $this->getDimensionName($dimension, $item->{$dimension});
            return $item;
        })->toArray();
    }

    /**
     * 获取完成率排行榜
     */
    public static function getCompletionRanking(array $schoolIds = [], array $filters = []): array
    {
        $query = self::selectRaw('
            school_id,
            SUM(total_experiments) as total_experiments,
            SUM(completed_experiments) as completed_experiments,
            ROUND(SUM(completed_experiments) * 100.0 / SUM(total_experiments), 2) as completion_rate
        ')
        ->with('school:id,name')
        ->groupBy('school_id')
        ->having('total_experiments', '>', 0);

        // 筛选学校
        if (!empty($schoolIds)) {
            $query->whereIn('school_id', $schoolIds);
        }

        // 应用其他筛选条件
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        $results = $query->orderBy('completion_rate', 'desc')->get();

        // 添加排名
        return $results->map(function($item, $index) {
            return [
                'rank' => $index + 1,
                'school_id' => $item->school_id,
                'school_name' => $item->school->name ?? '未知学校',
                'total_experiments' => $item->total_experiments,
                'completed_experiments' => $item->completed_experiments,
                'completion_rate' => $item->completion_rate
            ];
        })->toArray();
    }

    /**
     * 更新或创建基准数据
     */
    public static function updateOrCreateBaseline(array $data): self
    {
        return self::updateOrCreate(
            [
                'school_id' => $data['school_id'],
                'config_id' => $data['config_id'],
                'subject_id' => $data['subject_id'],
                'grade' => $data['grade'],
                'semester' => $data['semester']
            ],
            array_merge($data, [
                'last_calculated_at' => now(),
                'calculated_by' => auth()->id()
            ])
        );
    }

    /**
     * 批量更新基准数据
     */
    public static function batchUpdateBaselines(array $baselinesData): int
    {
        $updated = 0;
        
        foreach ($baselinesData as $data) {
            self::updateOrCreateBaseline($data);
            $updated++;
        }
        
        return $updated;
    }

    /**
     * 获取趋势数据
     */
    public static function getTrendData(int $schoolId, array $filters = []): array
    {
        $query = self::where('school_id', $schoolId)
            ->selectRaw('
                DATE(last_calculated_at) as date,
                AVG(completion_rate) as avg_completion_rate,
                SUM(total_experiments) as total_experiments,
                SUM(completed_experiments) as completed_experiments
            ')
            ->whereNotNull('last_calculated_at')
            ->groupBy('date')
            ->orderBy('date');

        // 应用筛选条件
        if (!empty($filters['start_date'])) {
            $query->where('last_calculated_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('last_calculated_at', '<=', $filters['end_date']);
        }

        return $query->get()->toArray();
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
                return self::SEMESTERS[$value] ?? '未知学期';
            default:
                return (string)$value;
        }
    }

    /**
     * 清理过期数据
     */
    public static function cleanupExpiredData(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return self::where('last_calculated_at', '<', $cutoffDate)
            ->whereHas('config', function($query) {
                $query->where('status', 0); // 只清理非活跃配置的数据
            })
            ->delete();
    }
}
