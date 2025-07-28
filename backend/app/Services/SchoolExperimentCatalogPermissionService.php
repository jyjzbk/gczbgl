<?php

namespace App\Services;

use App\Models\User;
use App\Models\School;
use App\Models\SchoolExperimentCatalogConfig;
use App\Models\ExperimentCatalog;
use Illuminate\Support\Facades\DB;

class SchoolExperimentCatalogPermissionService
{
    /**
     * 检查用户是否可以配置指定学校的实验目录
     */
    public function canConfigureSchoolCatalog(User $user, int $schoolId): bool
    {
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id ?? $user->school_id;

        // 学校管理员只能配置自己的学校（且需要有配置权限）
        if ($userLevel == 5) {
            return $schoolId == $userOrgId && $this->schoolHasConfigPermission($schoolId);
        }

        // 上级管理员可以配置下级学校
        return $this->canManageSchool($user, $school);
    }

    /**
     * 检查用户是否可以指定学校的实验目录
     */
    public function canAssignSchoolCatalog(User $user, int $schoolId): bool
    {
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        $userLevel = $user->organization_level ?? 5;
        
        // 只有上级管理员可以指定下级学校的目录
        if ($userLevel >= 5) {
            return false;
        }

        return $this->canManageSchool($user, $school);
    }

    /**
     * 检查用户是否可以管理指定学校
     */
    public function canManageSchool(User $user, School $school): bool
    {
        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id ?? $user->school_id;

        // 根据学校的管理级别判断权限
        switch ($school->management_level) {
            case 1: // 省直学校
                return $userLevel <= 1;
                
            case 2: // 市直学校
                return $userLevel <= 2 && (
                    $userLevel == 1 || // 省级可以管理所有市直学校
                    ($userLevel == 2 && $school->city_id == $userOrgId) // 市级只能管理本市学校
                );
                
            case 3: // 区县直管学校
                return $userLevel <= 3 && (
                    $userLevel <= 2 || // 省市级可以管理所有区县学校
                    ($userLevel == 3 && $school->district_id == $userOrgId) // 区县级只能管理本区县学校
                );
                
            case 4: // 学区学校
                return $userLevel <= 3 && (
                    $userLevel <= 2 || // 省市级可以管理所有学区学校
                    ($userLevel == 3 && $school->district_id == $userOrgId) // 区县级管理本区县的学区学校
                );
                
            default:
                return false;
        }
    }

    /**
     * 获取用户可以选择的实验目录级别
     */
    public function getAvailableCatalogLevels(User $user, int $schoolId): array
    {
        $school = School::find($schoolId);
        if (!$school) {
            return [];
        }

        $availableLevels = [];

        switch ($school->management_level) {
            case 1: // 省直学校
                $availableLevels = [1]; // 只能选择省级
                break;
                
            case 2: // 市直学校
                $availableLevels = [1, 2]; // 可以选择省级、市级
                break;
                
            case 3: // 区县直管学校
            case 4: // 学区学校
                $availableLevels = [1, 2, 3]; // 可以选择省级、市级、区县级
                break;
        }

        return $availableLevels;
    }

    /**
     * 获取用户可以选择的组织列表
     */
    public function getAvailableOrganizations(User $user, int $schoolId, int $level): array
    {
        $school = School::find($schoolId);
        if (!$school) {
            return [];
        }

        $organizations = [];

        switch ($level) {
            case 1: // 省级
                // 获取学校所在省份
                $organizations = DB::table('administrative_regions')
                    ->where('level', 1)
                    ->where('id', $school->province_id)
                    ->select('id', 'name')
                    ->get()
                    ->toArray();
                break;
                
            case 2: // 市级
                // 获取学校所在城市
                $organizations = DB::table('administrative_regions')
                    ->where('level', 2)
                    ->where('id', $school->city_id)
                    ->select('id', 'name')
                    ->get()
                    ->toArray();
                break;
                
            case 3: // 区县级
                // 获取学校所在区县
                $organizations = DB::table('administrative_regions')
                    ->where('level', 3)
                    ->where('id', $school->district_id)
                    ->select('id', 'name')
                    ->get()
                    ->toArray();
                break;
        }

        return $organizations;
    }

    /**
     * 检查学校是否有配置权限
     */
    public function schoolHasConfigPermission(int $schoolId): bool
    {
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        // 区县直管学校和学区学校由区县统一指定，无选择权
        if (in_array($school->management_level, [3, 4])) {
            return false;
        }

        return true;
    }

    /**
     * 检查用户是否可以删除实验项目
     */
    public function canDeleteExperiments(User $user, int $schoolId): bool
    {
        $config = SchoolExperimentCatalogConfig::getActiveConfig($schoolId);
        if (!$config) {
            return false;
        }

        // 检查配置是否允许删除
        if (!$config->can_delete_experiments) {
            return false;
        }

        // 检查用户权限
        return $this->canConfigureSchoolCatalog($user, $schoolId);
    }

    /**
     * 检查用户是否可以查看完成率统计
     */
    public function canViewCompletionStats(User $user, int $schoolId): bool
    {
        // 学校管理员可以查看自己学校的统计
        if ($user->organization_level == 5 && $user->organization_id == $schoolId) {
            return true;
        }

        // 上级管理员可以查看下级学校的统计
        $school = School::find($schoolId);
        return $school && $this->canManageSchool($user, $school);
    }

    /**
     * 获取用户权限信息
     */
    public function getUserPermissions(User $user, int $schoolId = null): array
    {
        $permissions = [
            'user_level' => $user->organization_level ?? 5,
            'user_org_id' => $user->organization_id ?? $user->school_id,
            'can_create_province_catalog' => $user->organization_level <= 1,
            'can_create_city_catalog' => $user->organization_level <= 2,
            'can_create_county_catalog' => $user->organization_level <= 3,
        ];

        if ($schoolId) {
            $permissions = array_merge($permissions, [
                'can_configure_school' => $this->canConfigureSchoolCatalog($user, $schoolId),
                'can_assign_school' => $this->canAssignSchoolCatalog($user, $schoolId),
                'can_delete_experiments' => $this->canDeleteExperiments($user, $schoolId),
                'can_view_completion_stats' => $this->canViewCompletionStats($user, $schoolId),
                'available_catalog_levels' => $this->getAvailableCatalogLevels($user, $schoolId),
                'school_has_config_permission' => $this->schoolHasConfigPermission($schoolId),
            ]);
        }

        return $permissions;
    }

    /**
     * 验证配置数据
     */
    public function validateConfigData(array $data, User $user): array
    {
        $errors = [];

        // 检查学校权限
        if (!$this->canConfigureSchoolCatalog($user, $data['school_id'])) {
            $errors[] = '无权限配置该学校的实验目录';
        }

        // 检查级别权限
        $availableLevels = $this->getAvailableCatalogLevels($user, $data['school_id']);
        if (!in_array($data['source_level'], $availableLevels)) {
            $errors[] = '该学校不能选择此级别的实验目录';
        }

        // 检查组织权限
        $availableOrgs = $this->getAvailableOrganizations($user, $data['school_id'], $data['source_level']);
        $orgIds = array_column($availableOrgs, 'id');
        if (!in_array($data['source_org_id'], $orgIds)) {
            $errors[] = '该学校不能选择此组织的实验目录';
        }

        return $errors;
    }
}
