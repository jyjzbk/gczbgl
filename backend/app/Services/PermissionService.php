<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    /**
     * 检查用户是否有指定权限
     */
    public function hasPermission(User $user, string $permission): bool
    {
        // 缓存用户权限，避免重复查询
        $cacheKey = "user_permissions_{$user->id}";
        $userPermissions = Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->getUserPermissions($user);
        });

        return in_array($permission, $userPermissions);
    }

    /**
     * 获取用户所有权限
     */
    public function getUserPermissions(User $user): array
    {
        $permissions = [];

        // 获取用户角色
        $roles = $user->roles()->with('permissions')->get();

        foreach ($roles as $role) {
            // 超级管理员拥有所有权限
            if ($role->code === 'super_admin') {
                return $this->getAllPermissions();
            }

            // 收集角色权限
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->permission_code;
            }
        }

        return array_unique($permissions);
    }

    /**
     * 获取所有权限列表
     */
    public function getAllPermissions(): array
    {
        return [
            // 设备档案权限
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',
            'equipment.import',
            'equipment.export',
            'equipment.photo.upload',
            'equipment.photo.delete',

            // 设备借用权限
            'equipment.borrow.view',
            'equipment.borrow.create',
            'equipment.borrow.edit',
            'equipment.borrow.approve',
            'equipment.borrow.return',
            'equipment.borrow.batch',

            // 设备维修权限
            'equipment.maintenance.view',
            'equipment.maintenance.create',
            'equipment.maintenance.edit',
            'equipment.maintenance.assign',
            'equipment.maintenance.complete',
            'equipment.maintenance.statistics',

            // 设备二维码权限
            'equipment.qrcode.generate',
            'equipment.qrcode.view',
            'equipment.qrcode.delete',
            'equipment.qrcode.batch',

            // 设备分类权限
            'equipment.category.view',
            'equipment.category.create',
            'equipment.category.edit',
            'equipment.category.delete',
        ];
    }

    /**
     * 为角色分配权限
     */
    public function assignPermissionsToRole(Role $role, array $permissions): void
    {
        $role->permissions()->sync($permissions);
        $this->clearPermissionCache();
    }

    /**
     * 为用户分配角色
     */
    public function assignRoleToUser(User $user, $roleId): void
    {
        $user->roles()->syncWithoutDetaching([$roleId]);
        $this->clearUserPermissionCache($user);
    }

    /**
     * 移除用户角色
     */
    public function removeRoleFromUser(User $user, $roleId): void
    {
        $user->roles()->detach($roleId);
        $this->clearUserPermissionCache($user);
    }

    /**
     * 清除权限缓存
     */
    public function clearPermissionCache(): void
    {
        Cache::flush(); // 简单粗暴的清除所有缓存，生产环境可以更精确
    }

    /**
     * 清除用户权限缓存
     */
    public function clearUserPermissionCache(User $user): void
    {
        Cache::forget("user_permissions_{$user->id}");
    }

    /**
     * 获取角色权限配置
     */
    public function getRolePermissions(): array
    {
        return [
            'super_admin' => [
                'name' => '超级管理员',
                'permissions' => $this->getAllPermissions()
            ],
            'admin' => [
                'name' => '管理员',
                'permissions' => [
                    'equipment.view',
                    'equipment.create',
                    'equipment.edit',
                    'equipment.delete',
                    'equipment.import',
                    'equipment.export',
                    'equipment.photo.upload',
                    'equipment.photo.delete',
                    'equipment.borrow.view',
                    'equipment.borrow.approve',
                    'equipment.borrow.return',
                    'equipment.borrow.batch',
                    'equipment.maintenance.view',
                    'equipment.maintenance.assign',
                    'equipment.maintenance.complete',
                    'equipment.maintenance.statistics',
                    'equipment.qrcode.generate',
                    'equipment.qrcode.view',
                    'equipment.qrcode.delete',
                    'equipment.qrcode.batch',
                ]
            ],
            'teacher' => [
                'name' => '教师',
                'permissions' => [
                    'equipment.view',
                    'equipment.borrow.view',
                    'equipment.borrow.create',
                    'equipment.borrow.edit',
                    'equipment.maintenance.view',
                    'equipment.maintenance.create',
                    'equipment.qrcode.view',
                ]
            ],
            'lab_manager' => [
                'name' => '实验员',
                'permissions' => [
                    'equipment.view',
                    'equipment.create',
                    'equipment.edit',
                    'equipment.photo.upload',
                    'equipment.photo.delete',
                    'equipment.borrow.view',
                    'equipment.borrow.approve',
                    'equipment.borrow.return',
                    'equipment.maintenance.view',
                    'equipment.maintenance.create',
                    'equipment.maintenance.assign',
                    'equipment.maintenance.complete',
                    'equipment.qrcode.generate',
                    'equipment.qrcode.view',
                    'equipment.qrcode.delete',
                ]
            ],
            'student' => [
                'name' => '学生',
                'permissions' => [
                    'equipment.view',
                    'equipment.borrow.view',
                    'equipment.borrow.create',
                    'equipment.qrcode.view',
                ]
            ],
        ];
    }

    /**
     * 检查数据访问权限
     */
    public function canAccessEquipment(User $user, $equipmentId): bool
    {
        // 超级管理员可以访问所有数据
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 管理员可以访问所有数据
        if ($user->hasRole('admin')) {
            return true;
        }

        // 其他用户只能访问自己学校的设备
        $equipment = \App\Models\Equipment::find($equipmentId);
        if (!$equipment) {
            return false;
        }

        return $user->school_id === $equipment->school_id;
    }

    /**
     * 检查借用记录访问权限
     */
    public function canAccessBorrowRecord(User $user, $borrowId): bool
    {
        // 超级管理员和管理员可以访问所有数据
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        $borrow = \App\Models\EquipmentBorrow::with('equipment')->find($borrowId);
        if (!$borrow) {
            return false;
        }

        // 借用人可以访问自己的借用记录
        if ($user->id === $borrow->borrower_id) {
            return true;
        }

        // 同学校用户可以访问
        return $user->school_id === $borrow->equipment->school_id;
    }

    /**
     * 检查维修记录访问权限
     */
    public function canAccessMaintenanceRecord(User $user, $maintenanceId): bool
    {
        // 超级管理员和管理员可以访问所有数据
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        $maintenance = \App\Models\EquipmentMaintenance::with('equipment')->find($maintenanceId);
        if (!$maintenance) {
            return false;
        }

        // 报修人和维修人可以访问
        if ($user->id === $maintenance->reporter_id || $user->id === $maintenance->technician_id) {
            return true;
        }

        // 同学校用户可以访问
        return $user->school_id === $maintenance->equipment->school_id;
    }
}
