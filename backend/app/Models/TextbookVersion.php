<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextbookVersion extends Model
{
    protected $fillable = [
        'name',
        'code',
        'publisher',
        'description',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * 获取该版本的章节
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(TextbookChapter::class);
    }

    /**
     * 获取该版本的实验目录
     */
    public function experimentCatalogs(): HasMany
    {
        return $this->hasMany(ExperimentCatalog::class);
    }

    /**
     * 作用域：启用的版本
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
