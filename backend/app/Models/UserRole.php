<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'scope_type',
        'scope_id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
        'scope_id' => 'integer'
    ];

    /**
     * 获取用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取角色
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * 权限范围类型常量
     */
    const SCOPE_REGION = 'region';     // 区域范围
    const SCOPE_SCHOOL = 'school';     // 学校范围

    /**
     * 获取权限范围对象
     */
    public function scopeObject()
    {
        switch ($this->scope_type) {
            case self::SCOPE_REGION:
                return AdministrativeRegion::find($this->scope_id);
            case self::SCOPE_SCHOOL:
                return School::find($this->scope_id);
            default:
                return null;
        }
    }
}
