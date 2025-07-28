<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentCatalogDeletePermission extends Model
{
    use HasFactory;

    protected $table = 'experiment_catalog_delete_permissions';

    protected $fillable = [
        'organization_type',
        'organization_id',
        'organization_name',
        'allow_school_delete',
        'require_delete_reason',
        'max_delete_percentage',
        'delete_rules',
        'created_by',
        'is_active'
    ];

    protected $casts = [
        'allow_school_delete' => 'boolean',
        'require_delete_reason' => 'boolean',
        'is_active' => 'boolean',
        'max_delete_percentage' => 'integer',
    ];

    /**
     * 组织类型常量
     */
    const ORGANIZATION_TYPES = [
        'province' => '省级',
        'city' => '市级',
        'county' => '区县级'
    ];

    /**
     * 创建人关联
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 获取组织类型中文名称
     */
    public function getOrganizationTypeNameAttribute(): string
    {
        return self::ORGANIZATION_TYPES[$this->organization_type] ?? $this->organization_type;
    }

    /**
     * 获取有效的删除权限配置
     * 
     * @param string $organizationType 组织类型
     * @param int $organizationId 组织ID
     * @return ExperimentCatalogDeletePermission|null
     */
    public static function getEffectivePermission(string $organizationType, int $organizationId): ?self
    {
        // 首先查找当前组织的配置
        $permission = self::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->first();

        if ($permission) {
            return $permission;
        }

        // 如果没有找到，则查找上级配置
        return self::getParentPermission($organizationType, $organizationId);
    }

    /**
     * 获取上级权限配置
     */
    private static function getParentPermission(string $organizationType, int $organizationId): ?self
    {
        // 根据组织类型确定上级类型
        $parentType = null;
        $parentId = null;

        switch ($organizationType) {
            case 'county':
                // 区县级的上级是市级
                $region = AdministrativeRegion::find($organizationId);
                if ($region && $region->parent_id) {
                    $parentRegion = AdministrativeRegion::find($region->parent_id);
                    if ($parentRegion && $parentRegion->level === 2) {
                        $parentType = 'city';
                        $parentId = $parentRegion->id;
                    }
                }
                break;
            case 'city':
                // 市级的上级是省级
                $region = AdministrativeRegion::find($organizationId);
                if ($region && $region->parent_id) {
                    $parentRegion = AdministrativeRegion::find($region->parent_id);
                    if ($parentRegion && $parentRegion->level === 1) {
                        $parentType = 'province';
                        $parentId = $parentRegion->id;
                    }
                }
                break;
        }

        if ($parentType && $parentId) {
            return self::getEffectivePermission($parentType, $parentId);
        }

        return null;
    }

    /**
     * 获取默认权限配置
     */
    public static function getDefaultPermission(): array
    {
        return [
            'allow_school_delete' => true,
            'require_delete_reason' => true,
            'max_delete_percentage' => 20,
            'delete_rules' => '学校可以删除不适合本校条件的实验，但需要说明理由，删除比例不得超过20%'
        ];
    }

    /**
     * 检查学校是否可以删除指定实验
     * 
     * @param int $schoolId 学校ID
     * @param int $catalogId 实验目录ID
     * @return array
     */
    public static function canSchoolDeleteCatalog(int $schoolId, int $catalogId): array
    {
        // 获取学校选择的标准
        $selection = SchoolExperimentCatalogSelection::getSchoolSelection($schoolId);
        if (!$selection) {
            return [
                'can_delete' => false,
                'reason' => '学校尚未选择实验目录标准'
            ];
        }

        // 获取有效的删除权限配置
        $permission = self::getEffectivePermission($selection->selected_level, $selection->selected_org_id);
        if (!$permission) {
            $permission = (object) self::getDefaultPermission();
        }

        // 检查是否允许删除
        if (!$permission->allow_school_delete) {
            return [
                'can_delete' => false,
                'reason' => '当前配置不允许学校删除实验'
            ];
        }

        // 检查删除比例限制
        $totalCatalogs = ExperimentCatalog::where('management_level', self::getLevelNumber($selection->selected_level))
            ->where('created_by_org_id', $selection->selected_org_id)
            ->where('status', 1)
            ->count();

        $deletedCatalogs = ExperimentCatalogDeletion::where('deleted_by_org_id', $schoolId)
            ->where('deleted_by_org_type', 'school')
            ->whereNull('restored_at')
            ->count();

        $deletePercentage = $totalCatalogs > 0 ? ($deletedCatalogs / $totalCatalogs) * 100 : 0;

        if ($deletePercentage >= $permission->max_delete_percentage) {
            return [
                'can_delete' => false,
                'reason' => "已删除实验比例({$deletePercentage}%)超过限制({$permission->max_delete_percentage}%)"
            ];
        }

        return [
            'can_delete' => true,
            'require_reason' => $permission->require_delete_reason ?? true,
            'delete_rules' => $permission->delete_rules ?? ''
        ];
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
     * 验证权限配置数据
     */
    public function validatePermission(): array
    {
        $errors = [];

        if (!in_array($this->organization_type, array_keys(self::ORGANIZATION_TYPES))) {
            $errors[] = '组织类型无效';
        }

        if ($this->organization_id <= 0) {
            $errors[] = '组织ID无效';
        }

        if (empty($this->organization_name)) {
            $errors[] = '组织名称不能为空';
        }

        if ($this->max_delete_percentage < 0 || $this->max_delete_percentage > 100) {
            $errors[] = '删除比例必须在0-100之间';
        }

        if ($this->created_by <= 0) {
            $errors[] = '创建人无效';
        }

        return $errors;
    }

    /**
     * 获取组织下的学校删除统计
     */
    public static function getSchoolDeleteStatistics(string $organizationType, int $organizationId): array
    {
        // 获取组织下的所有学校
        $schools = School::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->get();

        $statistics = [];
        foreach ($schools as $school) {
            $selection = SchoolExperimentCatalogSelection::getSchoolSelection($school->id);
            if (!$selection) {
                continue;
            }

            $totalCatalogs = ExperimentCatalog::where('management_level', self::getLevelNumber($selection->selected_level))
                ->where('created_by_org_id', $selection->selected_org_id)
                ->where('status', 1)
                ->count();

            $deletedCatalogs = ExperimentCatalogDeletion::where('deleted_by_org_id', $school->id)
                ->where('deleted_by_org_type', 'school')
                ->whereNull('restored_at')
                ->count();

            $deletePercentage = $totalCatalogs > 0 ? ($deletedCatalogs / $totalCatalogs) * 100 : 0;

            $statistics[] = [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'total_catalogs' => $totalCatalogs,
                'deleted_catalogs' => $deletedCatalogs,
                'delete_percentage' => round($deletePercentage, 2),
                'selected_level' => $selection->selected_level_name,
                'can_delete_experiments' => $selection->can_delete_experiments
            ];
        }

        return $statistics;
    }
}
