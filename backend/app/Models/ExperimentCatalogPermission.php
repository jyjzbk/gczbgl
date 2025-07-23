<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentCatalogPermission extends Model
{
    protected $fillable = [
        'catalog_id',
        'subject_id',
        'organization_type',
        'organization_id',
        'user_id',
        'permission_type',
        'granted_by',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * 关联实验目录
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联学科
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * 关联用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 关联授权人
     */
    public function grantedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * 检查权限是否过期
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * 作用域：有效权限
     */
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * 作用域：按组织筛选
     */
    public function scopeByOrganization($query, string $orgType, int $orgId)
    {
        return $query->where('organization_type', $orgType)
                    ->where('organization_id', $orgId);
    }

    /**
     * 作用域：按权限类型筛选
     */
    public function scopeByPermissionType($query, string $permissionType)
    {
        return $query->where('permission_type', $permissionType);
    }
}
