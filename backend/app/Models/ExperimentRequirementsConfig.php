<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentRequirementsConfig extends Model
{
    use HasFactory;

    protected $table = 'experiment_requirements_config';

    protected $fillable = [
        'organization_type',
        'organization_id',
        'experiment_type',
        'min_images',
        'max_images',
        'min_videos',
        'max_videos',
        'is_inherited',
        'created_by',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_inherited' => 'boolean',
        'is_active' => 'boolean',
        'min_images' => 'integer',
        'max_images' => 'integer',
        'min_videos' => 'integer',
        'max_videos' => 'integer',
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
     * 实验类型常量
     */
    const EXPERIMENT_TYPES = [
        '分组实验' => '分组实验',
        '演示实验' => '演示实验'
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
     * 获取有效的配置（考虑继承关系）
     * 
     * @param string $organizationType 组织类型
     * @param int $organizationId 组织ID
     * @param string $experimentType 实验类型
     * @return ExperimentRequirementsConfig|null
     */
    public static function getEffectiveConfig(string $organizationType, int $organizationId, string $experimentType): ?self
    {
        // 首先查找当前组织的配置
        $config = self::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->where('experiment_type', $experimentType)
            ->where('is_active', true)
            ->first();

        if ($config && !$config->is_inherited) {
            return $config;
        }

        // 如果没有找到或者配置为继承，则查找上级配置
        return self::getParentConfig($organizationType, $organizationId, $experimentType);
    }

    /**
     * 获取上级配置
     */
    private static function getParentConfig(string $organizationType, int $organizationId, string $experimentType): ?self
    {
        // 根据组织类型确定上级类型
        $parentType = null;
        $parentId = null;

        switch ($organizationType) {
            case 'county':
                // 区县级的上级是市级，需要通过行政区域表查找
                $region = \App\Models\AdministrativeRegion::find($organizationId);
                if ($region && $region->parent_id) {
                    $parentRegion = \App\Models\AdministrativeRegion::find($region->parent_id);
                    if ($parentRegion && $parentRegion->level === 2) { // 市级
                        $parentType = 'city';
                        $parentId = $parentRegion->id;
                    }
                }
                break;
            case 'city':
                // 市级的上级是省级
                $region = \App\Models\AdministrativeRegion::find($organizationId);
                if ($region && $region->parent_id) {
                    $parentRegion = \App\Models\AdministrativeRegion::find($region->parent_id);
                    if ($parentRegion && $parentRegion->level === 1) { // 省级
                        $parentType = 'province';
                        $parentId = $parentRegion->id;
                    }
                }
                break;
        }

        if ($parentType && $parentId) {
            return self::getEffectiveConfig($parentType, $parentId, $experimentType);
        }

        return null;
    }

    /**
     * 获取默认配置
     */
    public static function getDefaultConfig(string $experimentType): array
    {
        $defaults = [
            '分组实验' => [
                'min_images' => 2,
                'max_images' => 8,
                'min_videos' => 0,
                'max_videos' => 2,
            ],
            '演示实验' => [
                'min_images' => 1,
                'max_images' => 5,
                'min_videos' => 0,
                'max_videos' => 1,
            ]
        ];

        return $defaults[$experimentType] ?? [
            'min_images' => 1,
            'max_images' => 5,
            'min_videos' => 0,
            'max_videos' => 1,
        ];
    }

    /**
     * 验证配置数据
     */
    public function validateConfig(): array
    {
        $errors = [];

        if ($this->min_images < 0) {
            $errors[] = '最少图片数量不能小于0';
        }

        if ($this->max_images < $this->min_images) {
            $errors[] = '最多图片数量不能小于最少图片数量';
        }

        if ($this->min_videos < 0) {
            $errors[] = '最少视频数量不能小于0';
        }

        if ($this->max_videos < $this->min_videos) {
            $errors[] = '最多视频数量不能小于最少视频数量';
        }

        if ($this->max_images > 20) {
            $errors[] = '最多图片数量不能超过20';
        }

        if ($this->max_videos > 10) {
            $errors[] = '最多视频数量不能超过10';
        }

        return $errors;
    }
}
