<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExperimentCatalog;
use App\Models\ExperimentCatalogPermission;
use Illuminate\Support\Facades\DB;

class ExperimentCatalogPermissionService
{
    /**
     * 检查用户对实验目录的权限
     */
    public function checkPermission(User $user, ExperimentCatalog $catalog, string $permissionType): bool
    {
        // 1. 检查组织级别权限
        if (!$this->checkOrganizationLevelPermission($user, $catalog)) {
            return false;
        }

        // 2. 检查学科权限
        if (!$this->checkSubjectPermission($user, $catalog)) {
            return false;
        }

        // 3. 检查具体操作权限
        return $this->checkOperationPermission($user, $catalog, $permissionType);
    }

    /**
     * 检查组织级别权限
     */
    private function checkOrganizationLevelPermission(User $user, ExperimentCatalog $catalog): bool
    {
        $userLevel = $user->organization_level ?? 5;
        $catalogLevel = $catalog->management_level ?? 5;

        // 基本规则：用户可以访问自己级别及以上级别的实验目录
        if ($catalogLevel <= $userLevel) {
            return true;
        }

        // 特殊情况：用户可以访问自己组织创建的实验目录
        $userOrgId = $user->organization_id ?? $user->school_id;
        if ($catalog->created_by_org_id == $userOrgId) {
            return true;
        }

        return false;
    }

    /**
     * 检查学科权限
     */
    private function checkSubjectPermission(User $user, ExperimentCatalog $catalog): bool
    {
        // 如果用户没有学科限制，则可以访问所有学科
        if (empty($user->subject_permissions)) {
            return true;
        }

        // 检查用户是否有该学科的权限
        $subjectPermissions = json_decode($user->subject_permissions, true) ?? [];
        return in_array($catalog->subject_id, $subjectPermissions);
    }

    /**
     * 检查操作权限
     */
    private function checkOperationPermission(User $user, ExperimentCatalog $catalog, string $permissionType): bool
    {
        $userLevel = $user->organization_level ?? 5;
        $catalogLevel = $catalog->management_level ?? 5;

        switch ($permissionType) {
            case 'view':
                // 查看权限：所有用户都能查看各级管理员创建的实验目录
                return true;

            case 'edit':
                // 编辑权限：各级管理员只能编辑本级的实验目录，上级管理员可以编辑本级和下级实验目录
                if ($catalogLevel == $userLevel &&
                    $catalog->created_by_org_id == ($user->organization_id ?? $user->school_id)) {
                    return true; // 编辑本级的实验目录
                }
                if ($catalogLevel > $userLevel) {
                    return true; // 上级可以编辑下级的实验目录
                }
                return false;

            case 'delete':
                // 删除权限：各级管理员只能删除本级的实验目录，上级管理员可以删除本级和下级实验目录
                if ($catalogLevel == $userLevel &&
                    $catalog->created_by_org_id == ($user->organization_id ?? $user->school_id)) {
                    return true; // 删除本级的实验目录
                }
                if ($catalogLevel > $userLevel) {
                    return true; // 上级可以删除下级的实验目录
                }
                return false;

            case 'copy':
                // 复制权限：可以复制上级和同级的实验目录
                return $catalogLevel <= $userLevel;

            case 'manage_equipment':
                // 器材管理权限：各级管理员只能管理本级的实验目录，上级管理员可以管理本级和下级实验目录
                if ($catalogLevel == $userLevel &&
                    $catalog->created_by_org_id == ($user->organization_id ?? $user->school_id)) {
                    return true; // 管理本级的实验目录
                }
                if ($catalogLevel > $userLevel) {
                    return true; // 上级可以管理下级的实验目录
                }
                return false;

            case 'mark_deleted':
                // 标记删除权限：只能删除上级的实验目录
                return $catalogLevel < $userLevel;

            default:
                return false;
        }
    }

    /**
     * 获取用户可访问的实验目录查询构建器
     */
    public function getAccessibleCatalogsQuery(User $user)
    {
        $userLevel = $user->organization_level ?? 5;
        $userOrgId = $user->organization_id ?? $user->school_id;

        $query = ExperimentCatalog::query();

        // 基础权限过滤
        $query->where(function($q) use ($userLevel, $userOrgId) {
            $q->where('management_level', '<=', $userLevel)
              ->orWhere('created_by_org_id', $userOrgId);
        });

        // 学科权限过滤
        if (!empty($user->subject_permissions)) {
            $subjectPermissions = json_decode($user->subject_permissions, true) ?? [];
            $query->whereIn('subject_id', $subjectPermissions);
        }

        // 排除被当前用户组织删除的实验
        $query->where(function($q) use ($user) {
            $q->where('is_deleted_by_lower', false)
              ->orWhereNotExists(function($subQ) use ($user) {
                  $subQ->select(DB::raw(1))
                       ->from('experiment_catalog_deletions')
                       ->whereColumn('catalog_id', 'experiment_catalogs.id')
                       ->where('deleted_by_org_id', $user->organization_id ?? $user->school_id)
                       ->whereNull('restored_at');
              });
        });

        return $query;
    }

    /**
     * 授予权限
     */
    public function grantPermission(
        ExperimentCatalog $catalog,
        string $organizationType,
        int $organizationId,
        string $permissionType,
        User $grantedBy,
        ?int $userId = null,
        ?int $subjectId = null,
        ?\DateTime $expiresAt = null
    ): ExperimentCatalogPermission {
        return ExperimentCatalogPermission::create([
            'catalog_id' => $catalog->id,
            'subject_id' => $subjectId,
            'organization_type' => $organizationType,
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'permission_type' => $permissionType,
            'granted_by' => $grantedBy->id,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * 撤销权限
     */
    public function revokePermission(
        ExperimentCatalog $catalog,
        string $organizationType,
        int $organizationId,
        string $permissionType,
        ?int $userId = null
    ): bool {
        $query = ExperimentCatalogPermission::where('catalog_id', $catalog->id)
            ->where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->where('permission_type', $permissionType);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->delete() > 0;
    }

    /**
     * 获取用户的权限列表
     */
    public function getUserPermissions(User $user, ?ExperimentCatalog $catalog = null): array
    {
        $permissions = [];

        if ($catalog) {
            // 获取特定实验目录的权限
            $permissions = [
                'view' => $this->checkPermission($user, $catalog, 'view'),
                'edit' => $this->checkPermission($user, $catalog, 'edit'),
                'delete' => $this->checkPermission($user, $catalog, 'delete'),
                'copy' => $this->checkPermission($user, $catalog, 'copy'),
                'manage_equipment' => $this->checkPermission($user, $catalog, 'manage_equipment'),
                'mark_deleted' => $this->checkPermission($user, $catalog, 'mark_deleted'),
            ];
        } else {
            // 获取用户的通用权限信息
            $permissions = [
                'organization_level' => $user->organization_level ?? 5,
                'organization_id' => $user->organization_id ?? $user->school_id,
                'organization_type' => $user->organization_type ?? 'school',
                'subject_permissions' => json_decode($user->subject_permissions, true) ?? [],
                'can_create_catalog' => $this->canCreateCatalog($user),
                'can_manage_versions' => $this->canManageVersions($user),
                'can_manage_chapters' => $this->canManageChapters($user),
            ];
        }

        return $permissions;
    }

    /**
     * 检查是否可以创建实验目录
     */
    private function canCreateCatalog(User $user): bool
    {
        // 省、市、区县级可以创建标准实验目录
        $userLevel = $user->organization_level ?? 5;
        return $userLevel <= 3;
    }

    /**
     * 检查是否可以管理教材版本
     */
    private function canManageVersions(User $user): bool
    {
        // 省、市级可以管理教材版本
        $userLevel = $user->organization_level ?? 5;
        return $userLevel <= 2;
    }

    /**
     * 检查是否可以管理章节结构
     */
    private function canManageChapters(User $user): bool
    {
        // 省、市、区县级可以管理章节结构
        $userLevel = $user->organization_level ?? 5;
        return $userLevel <= 3;
    }

    /**
     * 批量检查权限
     */
    public function batchCheckPermissions(User $user, array $catalogIds, string $permissionType): array
    {
        $results = [];
        $catalogs = ExperimentCatalog::whereIn('id', $catalogIds)->get();

        foreach ($catalogs as $catalog) {
            $results[$catalog->id] = $this->checkPermission($user, $catalog, $permissionType);
        }

        return $results;
    }

    /**
     * 获取权限统计信息
     */
    public function getPermissionStats(User $user): array
    {
        $accessibleQuery = $this->getAccessibleCatalogsQuery($user);
        
        return [
            'total_accessible' => $accessibleQuery->count(),
            'by_management_level' => $accessibleQuery->selectRaw('management_level, count(*) as count')
                ->groupBy('management_level')
                ->pluck('count', 'management_level')
                ->toArray(),
            'by_subject' => $accessibleQuery->with('subject')
                ->get()
                ->groupBy('subject.name')
                ->map(function($group) {
                    return $group->count();
                })
                ->toArray(),
            'by_experiment_type' => $accessibleQuery->selectRaw('experiment_type, count(*) as count')
                ->groupBy('experiment_type')
                ->pluck('count', 'experiment_type')
                ->toArray(),
        ];
    }
}
