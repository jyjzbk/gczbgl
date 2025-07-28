<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolExperimentCatalogSelection extends Model
{
    use HasFactory;

    protected $table = 'school_experiment_catalog_selections';

    protected $fillable = [
        'school_id',
        'selected_level',
        'selected_org_id',
        'selected_org_name',
        'can_delete_experiments',
        'selection_reason',
        'selected_by',
        'selected_at'
    ];

    protected $casts = [
        'can_delete_experiments' => 'boolean',
        'selected_at' => 'datetime',
    ];

    /**
     * 选择级别常量
     */
    const SELECTION_LEVELS = [
        'province' => '省级',
        'city' => '市级',
        'county' => '区县级'
    ];

    /**
     * 学校关联
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * 选择操作人关联
     */
    public function selectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }

    /**
     * 获取选择级别中文名称
     */
    public function getSelectedLevelNameAttribute(): string
    {
        return self::SELECTION_LEVELS[$this->selected_level] ?? $this->selected_level;
    }

    /**
     * 获取学校的实验目录选择
     * 
     * @param int $schoolId 学校ID
     * @return SchoolExperimentCatalogSelection|null
     */
    public static function getSchoolSelection(int $schoolId): ?self
    {
        return self::where('school_id', $schoolId)->first();
    }

    /**
     * 设置学校的实验目录选择
     * 
     * @param int $schoolId 学校ID
     * @param string $selectedLevel 选择级别
     * @param int $selectedOrgId 选择的组织ID
     * @param string $selectedOrgName 选择的组织名称
     * @param bool $canDeleteExperiments 是否允许删除实验
     * @param string|null $selectionReason 选择理由
     * @param int $selectedBy 选择操作人
     * @return SchoolExperimentCatalogSelection
     */
    public static function setSchoolSelection(
        int $schoolId,
        string $selectedLevel,
        int $selectedOrgId,
        string $selectedOrgName,
        bool $canDeleteExperiments = false,
        ?string $selectionReason = null,
        int $selectedBy = 0
    ): self {
        return self::updateOrCreate(
            ['school_id' => $schoolId],
            [
                'selected_level' => $selectedLevel,
                'selected_org_id' => $selectedOrgId,
                'selected_org_name' => $selectedOrgName,
                'can_delete_experiments' => $canDeleteExperiments,
                'selection_reason' => $selectionReason,
                'selected_by' => $selectedBy,
                'selected_at' => now()
            ]
        );
    }

    /**
     * 检查学校是否可以删除实验
     * 
     * @param int $schoolId 学校ID
     * @return bool
     */
    public static function canSchoolDeleteExperiments(int $schoolId): bool
    {
        $selection = self::getSchoolSelection($schoolId);
        return $selection ? $selection->can_delete_experiments : false;
    }

    /**
     * 获取学校可用的实验目录
     * 
     * @param int $schoolId 学校ID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableExperimentCatalogs(int $schoolId)
    {
        $selection = self::getSchoolSelection($schoolId);
        
        if (!$selection) {
            // 如果没有选择，返回空集合
            return collect();
        }

        // 根据选择的级别获取对应的实验目录
        $query = ExperimentCatalog::where('management_level', self::getLevelNumber($selection->selected_level))
            ->where('created_by_org_id', $selection->selected_org_id)
            ->where('status', 1);

        // 如果学校不能删除实验，则过滤掉已删除的实验
        if (!$selection->can_delete_experiments) {
            $query->where('is_deleted_by_lower', false);
        }

        return $query->get();
    }

    /**
     * 获取级别对应的数字
     */
    private static function getLevelNumber(string $level): int
    {
        $levelMap = [
            'province' => 1,
            'city' => 2,
            'county' => 3
        ];
        
        return $levelMap[$level] ?? 5;
    }

    /**
     * 验证选择数据
     */
    public function validateSelection(): array
    {
        $errors = [];

        if (!in_array($this->selected_level, array_keys(self::SELECTION_LEVELS))) {
            $errors[] = '选择的级别无效';
        }

        if ($this->selected_org_id <= 0) {
            $errors[] = '选择的组织ID无效';
        }

        if (empty($this->selected_org_name)) {
            $errors[] = '组织名称不能为空';
        }

        if ($this->selected_by <= 0) {
            $errors[] = '选择操作人无效';
        }

        return $errors;
    }

    /**
     * 获取选择历史记录
     */
    public static function getSelectionHistory(int $schoolId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('school_id', $schoolId)
            ->with(['selectedBy:id,name'])
            ->orderBy('selected_at', 'desc')
            ->get();
    }
}
