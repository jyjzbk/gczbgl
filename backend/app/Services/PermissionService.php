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

        // 检查是否在用户的数据访问范围内
        return $this->canAccessSchool($user, $borrow->equipment->school_id);
    }

    /**
     * 获取用户的数据访问范围
     */
    public function getUserDataScope(User $user): array
    {
        // 超级管理员可以访问所有数据
        if ($user->hasRole('super_admin')) {
            return [
                'type' => 'all',
                'school_ids' => [],
                'region_ids' => []
            ];
        }

        $scope = [
            'type' => 'limited',
            'school_ids' => [],
            'region_ids' => []
        ];

        // 根据用户组织级别确定数据范围
        switch ($user->organization_level) {
            case 1: // 省级
                $scope['type'] = 'province';
                $scope['region_ids'] = $this->getProvinceRegionIds($user->organization_id);
                $scope['school_ids'] = $this->getProvinceSchoolIds($user->organization_id);
                break;

            case 2: // 市级
                $scope['type'] = 'city';
                $scope['region_ids'] = $this->getCityRegionIds($user->organization_id);
                $scope['school_ids'] = $this->getCitySchoolIds($user->organization_id);
                break;

            case 3: // 区县级
                $scope['type'] = 'county';
                $scope['region_ids'] = $this->getCountyRegionIds($user->organization_id);
                $scope['school_ids'] = $this->getCountySchoolIds($user->organization_id);
                break;

            case 4: // 学区级
                $scope['type'] = 'district';
                $scope['school_ids'] = $this->getDistrictSchoolIds($user->organization_id);
                break;

            case 5: // 学校级
                $scope['type'] = 'school';
                $scope['school_ids'] = [$user->school_id];
                break;

            default:
                // 兼容旧数据，使用school_id
                if ($user->school_id) {
                    $scope['type'] = 'school';
                    $scope['school_ids'] = [$user->school_id];
                }
        }

        return $scope;
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

        // 检查是否在用户的数据访问范围内
        return $this->canAccessSchool($user, $maintenance->equipment->school_id);
    }

    /**
     * 检查用户是否可以访问指定组织
     */
    public function canAccessOrganization(User $user, string $organizationType, int $organizationId): bool
    {
        // 超级管理员可以访问所有组织
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $dataScope = $this->getUserDataScope($user);

        if ($organizationType === 'school') {
            return in_array($organizationId, $dataScope['school_ids']);
        } elseif ($organizationType === 'region') {
            return in_array($organizationId, $dataScope['region_ids']);
        }

        return false;
    }

    /**
     * 检查用户是否可以访问指定学校
     */
    public function canAccessSchool(User $user, int $schoolId): bool
    {
        return $this->canAccessOrganization($user, 'school', $schoolId);
    }

    /**
     * 获取用户可管理的学校ID列表
     */
    public function getManageableSchoolIds(User $user): array
    {
        $dataScope = $this->getUserDataScope($user);
        return $dataScope['school_ids'];
    }

    /**
     * 获取所有下级区域ID（递归）
     */
    private function getAllSubRegionIds(int $regionId): array
    {
        $allRegionIds = [$regionId]; // 包含自身ID
        $directSubRegions = \App\Models\AdministrativeRegion::where('parent_id', $regionId)->pluck('id')->toArray();

        foreach ($directSubRegions as $subRegionId) {
            $allRegionIds = array_merge($allRegionIds, $this->getAllSubRegionIds($subRegionId));
        }

        return $allRegionIds;
    }

    /**
     * 获取省级用户可访问的区域ID
     */
    private function getProvinceRegionIds(int $provinceId): array
    {
        return $this->getAllSubRegionIds($provinceId);
    }

    /**
     * 获取省级用户可访问的学校ID
     */
    private function getProvinceSchoolIds(int $provinceId): array
    {
        // 获取省及所有下级区域ID
        $allRegionIds = $this->getAllSubRegionIds($provinceId);

        // 获取所有区域下的学校
        $allSchools = \App\Models\School::whereIn('region_id', $allRegionIds)
            ->pluck('id')
            ->toArray();

        return $allSchools;
    }

    /**
     * 获取市级用户可访问的区域ID
     */
    private function getCityRegionIds(int $cityId): array
    {
        return $this->getAllSubRegionIds($cityId);
    }

    /**
     * 获取市级用户可访问的学校ID
     */
    private function getCitySchoolIds(int $cityId): array
    {
        // 获取市及所有下级区域ID
        $allRegionIds = $this->getAllSubRegionIds($cityId);

        // 获取所有区域下的学校
        $allSchools = \App\Models\School::whereIn('region_id', $allRegionIds)
            ->pluck('id')
            ->toArray();

        return $allSchools;
    }

    /**
     * 获取区县级用户可访问的区域ID
     */
    private function getCountyRegionIds(int $countyId): array
    {
        return $this->getAllSubRegionIds($countyId);
    }

    /**
     * 获取区县级用户可访问的学校ID
     */
    private function getCountySchoolIds(int $countyId): array
    {
        // 获取区县及所有下级区域ID
        $allRegionIds = $this->getAllSubRegionIds($countyId);

        // 获取所有区域下的学校
        $allSchools = \App\Models\School::whereIn('region_id', $allRegionIds)
            ->pluck('id')
            ->toArray();

        return $allSchools;
    }

    /**
     * 获取学区级用户可访问的学校ID
     */
    private function getDistrictSchoolIds(int $districtId): array
    {
        return \App\Models\School::where('region_id', $districtId)
            ->pluck('id')
            ->toArray();
    }
}
