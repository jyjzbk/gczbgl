<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = [
        'role_id',
        'permission_code'
    ];

    protected $casts = [
        'role_id' => 'integer'
    ];

    /**
     * 获取角色
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
