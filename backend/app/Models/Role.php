<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'code',
        'level',
        'description',
        'status'
    ];

    protected $casts = [
        'level' => 'integer',
        'status' => 'integer'
    ];

    /**
     * 获取拥有此角色的用户
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withPivot(['scope_type', 'scope_id'])
                    ->withTimestamps();
    }

    /**
     * 获取角色权限
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * 获取角色权限代码数组
     */
    public function getPermissionCodes(): array
    {
        return $this->permissions()->pluck('permission_code')->toArray();
    }

    /**
     * 角色级别常量
     */
    const LEVEL_PROVINCE = 1;      // 省级
    const LEVEL_CITY = 2;          // 市级
    const LEVEL_COUNTY = 3;        // 区县级
    const LEVEL_DISTRICT = 4;      // 学区级
    const LEVEL_SCHOOL = 5;        // 学校级

    /**
     * 获取级别名称
     */
    public function getLevelNameAttribute(): string
    {
        $levels = [
            self::LEVEL_PROVINCE => '省级',
            self::LEVEL_CITY => '市级',
            self::LEVEL_COUNTY => '区县级',
            self::LEVEL_DISTRICT => '学区级',
            self::LEVEL_SCHOOL => '学校级'
        ];

        return $levels[$this->level] ?? '未知';
    }
}
