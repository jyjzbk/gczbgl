<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SchoolExperimentCatalogConfig extends Model
{
    use HasFactory;

    protected $table = 'school_experiment_catalog_configs';

    protected $fillable = [
        'school_id',
        'config_type',
        'source_level',
        'source_org_id',
        'source_org_name',
        'can_modify_selection',
        'can_delete_experiments',
        'configured_by',
        'configured_by_level',
        'configured_at',
        'config_reason',
        'status',
        'effective_date',
        'expiry_date'
    ];

    protected $casts = [
        'can_modify_selection' => 'boolean',
        'can_delete_experiments' => 'boolean',
        'configured_at' => 'datetime',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'status' => 'integer',
        'source_level' => 'integer',
        'configured_by_level' => 'integer'
    ];

    /**
     * 配置类型常量
     */
    const CONFIG_TYPES = [
        'selection' => '学校选择',
        'assignment' => '上级指定'
    ];

    /**
     * 来源级别常量
     */
    const SOURCE_LEVELS = [
        1 => '省级',
        2 => '市级',
        3 => '区县级'
    ];

    /**
     * 状态常量
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * 学校关联
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * 配置操作人关联
     */
    public function configuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'configured_by');
    }

    /**
     * 完成率基准关联
     */
    public function completionBaselines(): HasMany
    {
        return $this->hasMany(ExperimentCatalogCompletionBaseline::class, 'config_id');
    }

    /**
     * 获取配置类型中文名称
     */
    public function getConfigTypeNameAttribute(): string
    {
        return self::CONFIG_TYPES[$this->config_type] ?? $this->config_type;
    }

    /**
     * 获取来源级别中文名称
     */
    public function getSourceLevelNameAttribute(): string
    {
        return self::SOURCE_LEVELS[$this->source_level] ?? '未知级别';
    }

    /**
     * 检查配置是否有效
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $now = Carbon::now()->toDateString();
        
        // 检查生效日期
        if ($this->effective_date && $this->effective_date > $now) {
            return false;
        }

        // 检查失效日期
        if ($this->expiry_date && $this->expiry_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * 获取学校的有效配置
     */
    public static function getActiveConfig(int $schoolId): ?self
    {
        return self::where('school_id', $schoolId)
            ->where('status', self::STATUS_ACTIVE)
            ->where(function($query) {
                $now = Carbon::now()->toDateString();
                $query->whereNull('effective_date')
                      ->orWhere('effective_date', '<=', $now);
            })
            ->where(function($query) {
                $now = Carbon::now()->toDateString();
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $now);
            })
            ->first();
    }

    /**
     * 设置学校配置
     */
    public static function setSchoolConfig(array $data): self
    {
        // 先禁用该学校的其他配置
        self::where('school_id', $data['school_id'])
            ->where('status', self::STATUS_ACTIVE)
            ->update(['status' => self::STATUS_INACTIVE]);

        // 创建新配置
        return self::create(array_merge($data, [
            'status' => self::STATUS_ACTIVE,
            'configured_at' => now(),
            'effective_date' => $data['effective_date'] ?? now()->toDateString()
        ]));
    }

    /**
     * 获取可用的实验目录
     */
    public function getAvailableExperimentCatalogs()
    {
        $query = ExperimentCatalog::where('management_level', $this->source_level)
            ->where('created_by_org_id', $this->source_org_id)
            ->where('status', 1);

        // 如果不允许删除实验，则过滤掉被下级删除的实验
        if (!$this->can_delete_experiments) {
            $query->where('is_deleted_by_lower', false);
        }

        return $query->get();
    }

    /**
     * 获取配置统计信息
     */
    public function getConfigStats(): array
    {
        $catalogs = $this->getAvailableExperimentCatalogs();
        
        return [
            'total_experiments' => $catalogs->count(),
            'by_subject' => $catalogs->groupBy('subject_id')->map->count(),
            'by_grade' => $catalogs->groupBy('grade')->map->count(),
            'by_semester' => $catalogs->groupBy('semester')->map->count(),
            'by_type' => $catalogs->groupBy('experiment_type')->map->count(),
        ];
    }

    /**
     * 检查是否可以修改配置
     */
    public function canModify(User $user): bool
    {
        // 检查基本权限
        if (!$this->can_modify_selection) {
            return false;
        }

        // 学校选择类型：学校管理员可以修改
        if ($this->config_type === 'selection') {
            return $user->organization_level == 5 && $user->organization_id == $this->school_id;
        }

        // 上级指定类型：只有指定的上级可以修改
        if ($this->config_type === 'assignment') {
            return $user->organization_level <= $this->configured_by_level && 
                   $user->organization_level < 5;
        }

        return false;
    }

    /**
     * 获取配置历史
     */
    public static function getConfigHistory(int $schoolId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('school_id', $schoolId)
            ->with(['configuredBy:id,name', 'school:id,name'])
            ->orderBy('configured_at', 'desc')
            ->get();
    }

    /**
     * 批量设置学校配置
     */
    public static function batchSetConfigs(array $schoolIds, array $configData, User $configuredBy): array
    {
        $results = [];
        
        foreach ($schoolIds as $schoolId) {
            try {
                $data = array_merge($configData, [
                    'school_id' => $schoolId,
                    'configured_by' => $configuredBy->id,
                    'configured_by_level' => $configuredBy->organization_level ?? 5
                ]);
                
                $config = self::setSchoolConfig($data);
                $results[$schoolId] = ['success' => true, 'config' => $config];
            } catch (\Exception $e) {
                $results[$schoolId] = ['success' => false, 'error' => $e->getMessage()];
            }
        }
        
        return $results;
    }

    /**
     * 获取使用统计
     */
    public static function getUsageStats(): array
    {
        $stats = self::selectRaw('
            source_level,
            source_org_id,
            source_org_name,
            config_type,
            COUNT(*) as usage_count,
            COUNT(CASE WHEN status = 1 THEN 1 END) as active_count
        ')
        ->groupBy(['source_level', 'source_org_id', 'source_org_name', 'config_type'])
        ->get();

        return $stats->groupBy('source_level')->toArray();
    }
}
