<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentCatalogDeletion extends Model
{
    protected $fillable = [
        'catalog_id',
        'deleted_by_org_type',
        'deleted_by_org_id',
        'deleted_by_user_id',
        'delete_reason',
        'deleted_at',
        'restored_at',
        'restored_by'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'restored_at' => 'datetime'
    ];

    /**
     * 关联实验目录
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联删除用户
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }

    /**
     * 关联恢复用户
     */
    public function restoredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by');
    }
}
