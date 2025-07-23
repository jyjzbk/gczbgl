<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperimentCatalogVersion extends Model
{
    protected $fillable = [
        'catalog_id',
        'version',
        'name',
        'content',
        'objective',
        'procedure',
        'safety_notes',
        'change_reason',
        'changed_by'
    ];

    /**
     * 关联实验目录
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ExperimentCatalog::class, 'catalog_id');
    }

    /**
     * 关联变更人
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * 作用域：按目录筛选
     */
    public function scopeByCatalog($query, int $catalogId)
    {
        return $query->where('catalog_id', $catalogId);
    }

    /**
     * 作用域：按版本排序
     */
    public function scopeOrderedByVersion($query, string $direction = 'desc')
    {
        return $query->orderBy('version', $direction);
    }

    /**
     * 获取版本差异
     */
    public function getDifferences(ExperimentCatalogVersion $otherVersion): array
    {
        $differences = [];
        $fields = ['name', 'content', 'objective', 'procedure', 'safety_notes'];

        foreach ($fields as $field) {
            if ($this->$field !== $otherVersion->$field) {
                $differences[$field] = [
                    'old' => $otherVersion->$field,
                    'new' => $this->$field
                ];
            }
        }

        return $differences;
    }
}
