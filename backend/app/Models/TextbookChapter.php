<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextbookChapter extends Model
{
    protected $fillable = [
        'subject_id',
        'textbook_version_id',
        'grade_level',
        'volume',
        'parent_id',
        'level',
        'code',
        'name',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer'
    ];

    /**
     * 关联学科
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * 关联教材版本
     */
    public function textbookVersion(): BelongsTo
    {
        return $this->belongsTo(TextbookVersion::class);
    }

    /**
     * 关联父级章节
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TextbookChapter::class, 'parent_id');
    }

    /**
     * 关联子级章节
     */
    public function children(): HasMany
    {
        return $this->hasMany(TextbookChapter::class, 'parent_id');
    }

    /**
     * 获取该章节的实验目录
     */
    public function experimentCatalogs(): HasMany
    {
        return $this->hasMany(ExperimentCatalog::class, 'chapter_id');
    }

    /**
     * 作用域：启用的章节
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 作用域：按层级和排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * 作用域：根据学科筛选
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * 作用域：根据教材版本筛选
     */
    public function scopeByVersion($query, $versionId)
    {
        return $query->where('textbook_version_id', $versionId);
    }

    /**
     * 作用域：根据年级筛选
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade_level', $grade);
    }

    /**
     * 获取完整的章节路径
     */
    public function getFullPathAttribute(): string
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }
        
        return implode(' > ', $path);
    }
}
