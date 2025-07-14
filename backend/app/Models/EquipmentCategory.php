<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'level',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'level' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer'
    ];

    // 状态常量
    const STATUS_ACTIVE = 1;   // 启用
    const STATUS_INACTIVE = 0; // 禁用

    /**
     * 获取父级分类
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'parent_id');
    }

    /**
     * 获取子级分类
     */
    public function children(): HasMany
    {
        return $this->hasMany(EquipmentCategory::class, 'parent_id');
    }

    /**
     * 获取所有子级分类（递归）
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * 获取分类下的设备
     */
    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'category_id');
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? '启用' : '禁用';
    }

    /**
     * 获取完整路径名称
     */
    public function getFullNameAttribute()
    {
        $names = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($names, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $names);
    }

    /**
     * 作用域：启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * 作用域：根分类
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * 作用域：指定级别
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * 检查是否为叶子节点
     */
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    /**
     * 检查是否为根节点
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * 获取所有祖先分类
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }

    /**
     * 获取所有后代分类ID
     */
    public function getDescendantIds()
    {
        $ids = collect([$this->id]);
        
        foreach ($this->children as $child) {
            $ids = $ids->merge($child->getDescendantIds());
        }
        
        return $ids;
    }
}
