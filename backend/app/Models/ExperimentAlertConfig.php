<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentAlertConfig extends Model
{
    use HasFactory;

    protected $table = 'experiment_alert_config';

    protected $fillable = [
        'organization_type',
        'organization_id',
        'organization_name',
        'alert_type',
        'threshold_value',
        'alert_days',
        'is_active',
        'alert_rules',
        'notification_settings',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold_value' => 'decimal:2',
        'alert_days' => 'integer',
        'notification_settings' => 'array',
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
     * 预警类型常量
     */
    const ALERT_TYPES = [
        'overdue' => '超期未开',
        'completion_rate' => '完成率低',
        'quality_score' => '质量评分低'
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
     * 获取预警类型中文名称
     */
    public function getAlertTypeNameAttribute(): string
    {
        return self::ALERT_TYPES[$this->alert_type] ?? $this->alert_type;
    }

    /**
     * 获取有效的预警配置
     * 
     * @param string $organizationType 组织类型
     * @param int $organizationId 组织ID
     * @param string $alertType 预警类型
     * @return ExperimentAlertConfig|null
     */
    public static function getEffectiveConfig(string $organizationType, int $organizationId, string $alertType): ?self
    {
        // 首先查找当前组织的配置
        $config = self::where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->where('alert_type', $alertType)
            ->where('is_active', true)
            ->first();

        if ($config) {
            return $config;
        }

        // 如果没有找到，则查找上级配置
        return self::getParentConfig($organizationType, $organizationId, $alertType);
    }

    /**
     * 获取上级配置
     */
    private static function getParentConfig(string $organizationType, int $organizationId, string $alertType): ?self
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
            return self::getEffectiveConfig($parentType, $parentId, $alertType);
        }

        return null;
    }

    /**
     * 获取默认配置
     */
    public static function getDefaultConfig(string $alertType): array
    {
        $defaults = [
            'overdue' => [
                'threshold_value' => 0, // 超期0天即预警
                'alert_days' => 3, // 提前3天预警
                'alert_rules' => '实验计划时间到期前3天开始预警，超期后立即发出高级预警'
            ],
            'completion_rate' => [
                'threshold_value' => 80.00, // 完成率低于80%预警
                'alert_days' => 7, // 提前7天预警
                'alert_rules' => '学期过半时完成率低于80%发出预警，学期末低于60%发出严重预警'
            ],
            'quality_score' => [
                'threshold_value' => 70.00, // 质量评分低于70分预警
                'alert_days' => 0, // 实时预警
                'alert_rules' => '实验质量评分低于70分立即预警，低于60分发出严重预警'
            ]
        ];

        return $defaults[$alertType] ?? [
            'threshold_value' => 0,
            'alert_days' => 7,
            'alert_rules' => '默认预警规则'
        ];
    }

    /**
     * 验证配置数据
     */
    public function validateConfig(): array
    {
        $errors = [];

        if (!in_array($this->organization_type, array_keys(self::ORGANIZATION_TYPES))) {
            $errors[] = '组织类型无效';
        }

        if (!in_array($this->alert_type, array_keys(self::ALERT_TYPES))) {
            $errors[] = '预警类型无效';
        }

        if ($this->threshold_value < 0) {
            $errors[] = '预警阈值不能小于0';
        }

        if ($this->alert_type === 'completion_rate' && $this->threshold_value > 100) {
            $errors[] = '完成率阈值不能大于100';
        }

        if ($this->alert_type === 'quality_score' && $this->threshold_value > 100) {
            $errors[] = '质量评分阈值不能大于100';
        }

        if ($this->alert_days < 0) {
            $errors[] = '预警提前天数不能小于0';
        }

        if ($this->organization_id <= 0) {
            $errors[] = '组织ID无效';
        }

        if (empty($this->organization_name)) {
            $errors[] = '组织名称不能为空';
        }

        return $errors;
    }

    /**
     * 获取通知设置
     */
    public function getNotificationSettings(): array
    {
        return $this->notification_settings ?? [
            'email' => true,
            'sms' => false,
            'system' => true,
            'recipients' => []
        ];
    }

    /**
     * 设置通知设置
     */
    public function setNotificationSettings(array $settings): void
    {
        $this->notification_settings = array_merge($this->getNotificationSettings(), $settings);
    }

    /**
     * 检查是否需要发送预警
     */
    public function shouldAlert(float $currentValue, int $daysUntilDeadline = 0): bool
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->alert_type) {
            case 'overdue':
                // 超期预警：检查是否超期或即将超期
                return $daysUntilDeadline <= $this->alert_days;
            
            case 'completion_rate':
            case 'quality_score':
                // 完成率和质量评分预警：检查是否低于阈值
                return $currentValue < $this->threshold_value;
            
            default:
                return false;
        }
    }

    /**
     * 获取预警级别
     */
    public function getAlertLevel(float $currentValue, int $daysUntilDeadline = 0): string
    {
        switch ($this->alert_type) {
            case 'overdue':
                if ($daysUntilDeadline < 0) {
                    return abs($daysUntilDeadline) > 7 ? 'critical' : 'high';
                } elseif ($daysUntilDeadline <= 1) {
                    return 'medium';
                } else {
                    return 'low';
                }
            
            case 'completion_rate':
            case 'quality_score':
                $percentage = ($currentValue / $this->threshold_value) * 100;
                if ($percentage < 50) {
                    return 'critical';
                } elseif ($percentage < 70) {
                    return 'high';
                } elseif ($percentage < 90) {
                    return 'medium';
                } else {
                    return 'low';
                }
            
            default:
                return 'low';
        }
    }
}
