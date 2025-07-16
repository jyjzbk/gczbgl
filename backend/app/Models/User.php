<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'real_name',
        'avatar',
        'gender',
        'birthday',
        'id_card',
        'status',
        'last_login_at',
        'role',
        'department',
        'position',
        'bio',
        'school_id',
        'school_name',
        'organization_id',
        'organization_type',
        'organization_level'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'last_login_at' => 'datetime',
            'gender' => 'integer',
            'status' => 'integer'
        ];
    }

    /**
     * 获取用户的角色
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot(['scope_type', 'scope_id'])
                    ->withTimestamps();
    }

    /**
     * 获取用户角色关联记录
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * 获取用户管理的设备
     */
    public function managedEquipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'manager_id');
    }

    /**
     * 获取用户所属的组织（区域或学校）
     */
    public function organization()
    {
        if ($this->organization_type === 'region') {
            return $this->belongsTo(AdministrativeRegion::class, 'organization_id');
        } elseif ($this->organization_type === 'school') {
            return $this->belongsTo(School::class, 'organization_id');
        }
        return null;
    }

    /**
     * 获取用户所属学校（如果是学校级用户或通过school_id）
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * 获取用户的设备借用记录
     */
    public function equipmentBorrows(): HasMany
    {
        return $this->hasMany(EquipmentBorrow::class, 'borrower_id');
    }

    /**
     * 获取用户审批的借用记录
     */
    public function approvedBorrows(): HasMany
    {
        return $this->hasMany(EquipmentBorrow::class, 'approver_id');
    }

    /**
     * 获取用户报修的设备
     */
    public function reportedMaintenances(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class, 'reporter_id');
    }

    /**
     * 获取用户维修的设备
     */
    public function maintainedEquipments(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class, 'maintainer_id');
    }

    /**
     * 检查用户是否有指定角色
     */
    public function hasRole($roles, string $scopeType = null, int $scopeId = null): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $query = $this->roles()->whereIn('code', $roles);

        if ($scopeType && $scopeId) {
            $query->wherePivot('scope_type', $scopeType)
                  ->wherePivot('scope_id', $scopeId);
        }

        return $query->exists();
    }

    /**
     * 检查用户是否有指定权限
     */
    public function hasPermission(string $permission): bool
    {
        $permissionService = app(\App\Services\PermissionService::class);
        return $permissionService->hasPermission($this, $permission);
    }

    /**
     * 获取用户所有权限
     */
    public function getPermissions(): array
    {
        $permissionService = app(\App\Services\PermissionService::class);
        return $permissionService->getUserPermissions($this);
    }

    /**
     * 获取用户的数据访问范围
     */
    public function getDataScope(): array
    {
        $permissionService = app(\App\Services\PermissionService::class);
        return $permissionService->getUserDataScope($this);
    }

    /**
     * 检查用户是否可以访问指定组织的数据
     */
    public function canAccessOrganization($organizationType, $organizationId): bool
    {
        $permissionService = app(\App\Services\PermissionService::class);
        return $permissionService->canAccessOrganization($this, $organizationType, $organizationId);
    }

    /**
     * 获取用户可管理的学校ID列表
     */
    public function getManageableSchoolIds(): array
    {
        $permissionService = app(\App\Services\PermissionService::class);
        return $permissionService->getManageableSchoolIds($this);
    }

    /**
     * 获取用户组织级别文本
     */
    public function getOrganizationLevelTextAttribute(): string
    {
        $levels = [
            1 => '省级',
            2 => '市级',
            3 => '区县级',
            4 => '学区级',
            5 => '学校级'
        ];
        return $levels[$this->organization_level] ?? '未知';
    }

    /**
     * 检查是否为超级管理员
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * 检查是否为管理员
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }

    /**
     * 检查是否为教师
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * 检查是否为实验员
     */
    public function isLabManager(): bool
    {
        return $this->hasRole('lab_manager');
    }

    /**
     * 检查是否为学生
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * 性别常量
     */
    const GENDER_MALE = 1;     // 男
    const GENDER_FEMALE = 2;   // 女

    /**
     * 状态常量
     */
    const STATUS_ACTIVE = 1;   // 正常
    const STATUS_DISABLED = 0; // 禁用

    /**
     * 获取性别名称
     */
    public function getGenderNameAttribute(): string
    {
        $genders = [
            self::GENDER_MALE => '男',
            self::GENDER_FEMALE => '女'
        ];

        return $genders[$this->gender] ?? '未知';
    }

    /**
     * JWT相关方法
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
