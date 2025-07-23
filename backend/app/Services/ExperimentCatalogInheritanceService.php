<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExperimentCatalog;
use App\Models\ExperimentEquipmentRequirement;
use App\Models\ExperimentCatalogVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExperimentCatalogInheritanceService
{
    protected $permissionService;

    public function __construct(ExperimentCatalogPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 下级继承上级实验目录
     */
    public function inheritFromParent(
        ExperimentCatalog $parentCatalog,
        User $user,
        array $customizations = []
    ): ExperimentCatalog {
        // 验证权限
        if (!$this->permissionService->checkPermission($user, $parentCatalog, 'copy')) {
            throw new \Exception('没有权限继承该实验目录');
        }

        DB::beginTransaction();
        try {
            // 创建继承的实验目录
            $inheritedCatalog = $this->createInheritedCatalog($parentCatalog, $user, $customizations);

            // 继承器材需求配置
            $this->inheritEquipmentRequirements($parentCatalog, $inheritedCatalog, $user);

            // 建立继承关系
            $this->establishInheritanceRelationship($parentCatalog, $inheritedCatalog);

            // 记录继承操作
            $this->logInheritanceOperation($parentCatalog, $inheritedCatalog, $user, 'inherit');

            DB::commit();
            return $inheritedCatalog;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('实验目录继承失败', [
                'parent_catalog_id' => $parentCatalog->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * 创建继承的实验目录
     */
    private function createInheritedCatalog(
        ExperimentCatalog $parentCatalog,
        User $user,
        array $customizations
    ): ExperimentCatalog {
        $data = $parentCatalog->toArray();
        
        // 移除不需要继承的字段
        unset($data['id'], $data['created_at'], $data['updated_at']);

        // 设置继承关系字段
        $data['parent_catalog_id'] = $parentCatalog->id;
        $data['original_catalog_id'] = $parentCatalog->original_catalog_id ?? $parentCatalog->id;
        $data['version'] = 1;
        $data['management_level'] = $user->organization_level ?? 5;
        $data['created_by_level'] = $user->organization_level ?? 5;
        $data['created_by_org_id'] = $user->organization_id ?? $user->school_id;
        $data['created_by_org_type'] = $user->organization_type ?? 'school';
        $data['created_by'] = $user->id;
        $data['is_deleted_by_lower'] = false;
        $data['delete_reason'] = null;

        // 应用自定义修改
        foreach ($customizations as $key => $value) {
            if (in_array($key, ['name', 'content', 'objective', 'procedure', 'safety_notes'])) {
                $data[$key] = $value;
            }
        }

        // 如果没有自定义名称，添加后缀
        if (!isset($customizations['name'])) {
            $orgName = $this->getOrganizationName($user);
            $data['name'] = $parentCatalog->name . " ({$orgName}版)";
        }

        return ExperimentCatalog::create($data);
    }

    /**
     * 继承器材需求配置
     */
    private function inheritEquipmentRequirements(
        ExperimentCatalog $parentCatalog,
        ExperimentCatalog $inheritedCatalog,
        User $user
    ): void {
        $parentRequirements = ExperimentEquipmentRequirement::where('catalog_id', $parentCatalog->id)
            ->where('is_active', true)
            ->get();

        foreach ($parentRequirements as $requirement) {
            $data = $requirement->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);

            $data['catalog_id'] = $inheritedCatalog->id;
            $data['created_by'] = $user->id;

            ExperimentEquipmentRequirement::create($data);
        }
    }

    /**
     * 建立继承关系
     */
    private function establishInheritanceRelationship(
        ExperimentCatalog $parentCatalog,
        ExperimentCatalog $inheritedCatalog
    ): void {
        // 这里可以添加更多的继承关系逻辑
        // 比如权限继承、标签继承等
    }

    /**
     * 同步上级更新
     */
    public function syncFromParent(ExperimentCatalog $catalog, User $user, array $syncOptions = []): bool
    {
        if (!$catalog->parent_catalog_id) {
            throw new \Exception('该实验目录不是继承的目录，无法同步');
        }

        $parentCatalog = ExperimentCatalog::find($catalog->parent_catalog_id);
        if (!$parentCatalog) {
            throw new \Exception('上级实验目录不存在');
        }

        // 验证权限
        if (!$this->permissionService->checkPermission($user, $catalog, 'edit')) {
            throw new \Exception('没有权限同步该实验目录');
        }

        DB::beginTransaction();
        try {
            // 保存当前版本
            $this->saveCurrentVersion($catalog, $user, '同步前备份');

            // 同步基本信息
            if ($syncOptions['sync_basic_info'] ?? true) {
                $this->syncBasicInfo($parentCatalog, $catalog, $syncOptions);
            }

            // 同步器材需求
            if ($syncOptions['sync_equipment'] ?? true) {
                $this->syncEquipmentRequirements($parentCatalog, $catalog, $user);
            }

            // 更新版本号
            $catalog->increment('version');

            // 记录同步操作
            $this->logInheritanceOperation($parentCatalog, $catalog, $user, 'sync');

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('实验目录同步失败', [
                'catalog_id' => $catalog->id,
                'parent_catalog_id' => $parentCatalog->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * 同步基本信息
     */
    private function syncBasicInfo(
        ExperimentCatalog $parentCatalog,
        ExperimentCatalog $catalog,
        array $syncOptions
    ): void {
        $fieldsToSync = $syncOptions['fields'] ?? [
            'content', 'objective', 'procedure', 'safety_notes', 
            'duration', 'difficulty_level', 'experiment_type'
        ];

        $updateData = [];
        foreach ($fieldsToSync as $field) {
            if (isset($parentCatalog->$field)) {
                $updateData[$field] = $parentCatalog->$field;
            }
        }

        if (!empty($updateData)) {
            $catalog->update($updateData);
        }
    }

    /**
     * 同步器材需求配置
     */
    private function syncEquipmentRequirements(
        ExperimentCatalog $parentCatalog,
        ExperimentCatalog $catalog,
        User $user
    ): void {
        // 删除现有的器材需求配置
        ExperimentEquipmentRequirement::where('catalog_id', $catalog->id)->delete();

        // 重新继承器材需求配置
        $this->inheritEquipmentRequirements($parentCatalog, $catalog, $user);
    }

    /**
     * 保存当前版本
     */
    private function saveCurrentVersion(ExperimentCatalog $catalog, User $user, string $changeReason): void
    {
        ExperimentCatalogVersion::create([
            'catalog_id' => $catalog->id,
            'version' => $catalog->version,
            'name' => $catalog->name,
            'content' => $catalog->content,
            'objective' => $catalog->objective,
            'procedure' => $catalog->procedure,
            'safety_notes' => $catalog->safety_notes,
            'change_reason' => $changeReason,
            'changed_by' => $user->id
        ]);
    }

    /**
     * 获取继承树
     */
    public function getInheritanceTree(ExperimentCatalog $catalog): array
    {
        $tree = [];

        // 获取根节点
        $root = $this->findRootCatalog($catalog);
        $tree['root'] = $this->buildCatalogNode($root);

        // 递归构建子树
        $tree['children'] = $this->buildChildrenTree($root);

        return $tree;
    }

    /**
     * 查找根节点
     */
    private function findRootCatalog(ExperimentCatalog $catalog): ExperimentCatalog
    {
        $current = $catalog;
        while ($current->parent_catalog_id) {
            $parent = ExperimentCatalog::find($current->parent_catalog_id);
            if (!$parent) break;
            $current = $parent;
        }
        return $current;
    }

    /**
     * 构建目录节点
     */
    private function buildCatalogNode(ExperimentCatalog $catalog): array
    {
        return [
            'id' => $catalog->id,
            'name' => $catalog->name,
            'management_level' => $catalog->management_level,
            'created_by_org_type' => $catalog->created_by_org_type,
            'created_by_org_id' => $catalog->created_by_org_id,
            'version' => $catalog->version,
            'is_deleted_by_lower' => $catalog->is_deleted_by_lower,
            'created_at' => $catalog->created_at,
            'updated_at' => $catalog->updated_at
        ];
    }

    /**
     * 递归构建子树
     */
    private function buildChildrenTree(ExperimentCatalog $catalog): array
    {
        $children = ExperimentCatalog::where('parent_catalog_id', $catalog->id)->get();
        $tree = [];

        foreach ($children as $child) {
            $node = $this->buildCatalogNode($child);
            $node['children'] = $this->buildChildrenTree($child);
            $tree[] = $node;
        }

        return $tree;
    }

    /**
     * 计算完成率
     */
    public function calculateCompletionRate(User $user, array $filters = []): array
    {
        $userOrgId = $user->organization_id ?? $user->school_id;
        $userOrgType = $user->organization_type ?? 'school';

        // 获取应做的实验总数（上级要求）
        $requiredQuery = $this->permissionService->getAccessibleCatalogsQuery($user)
            ->where('management_level', '<', $user->organization_level ?? 5);

        // 应用筛选条件
        if (!empty($filters['subject_id'])) {
            $requiredQuery->where('subject_id', $filters['subject_id']);
        }
        if (!empty($filters['grade_level'])) {
            $requiredQuery->where('grade_level', $filters['grade_level']);
        }

        $totalRequired = $requiredQuery->count();

        // 获取已删除的实验数（本级删除）
        $deletedCount = $requiredQuery->where('is_deleted_by_lower', true)
            ->whereHas('deletionRecords', function($q) use ($userOrgId, $userOrgType) {
                $q->where('deleted_by_org_id', $userOrgId)
                  ->where('deleted_by_org_type', $userOrgType)
                  ->whereNull('restored_at');
            })
            ->count();

        // 获取已完成的实验数（这里需要根据实际业务逻辑定义"完成"）
        // 暂时以存在预约记录作为完成标准
        $completedCount = $requiredQuery->whereHas('reservations', function($q) use ($userOrgId) {
            $q->where('status', 'completed')
              ->whereHas('user', function($subQ) use ($userOrgId) {
                  $subQ->where('organization_id', $userOrgId)
                       ->orWhere('school_id', $userOrgId);
              });
        })->count();

        $effectiveRequired = $totalRequired - $deletedCount;
        $completionRate = $effectiveRequired > 0 ? ($completedCount / $effectiveRequired) * 100 : 0;

        return [
            'total_required' => $totalRequired,
            'deleted_count' => $deletedCount,
            'effective_required' => $effectiveRequired,
            'completed_count' => $completedCount,
            'completion_rate' => round($completionRate, 2)
        ];
    }

    /**
     * 获取组织名称
     */
    private function getOrganizationName(User $user): string
    {
        $levelNames = [
            1 => '省级',
            2 => '市级', 
            3 => '区县级',
            4 => '学区级',
            5 => '学校级'
        ];

        return $levelNames[$user->organization_level ?? 5] ?? '未知';
    }

    /**
     * 记录继承操作日志
     */
    private function logInheritanceOperation(
        ExperimentCatalog $parentCatalog,
        ExperimentCatalog $childCatalog,
        User $user,
        string $operation
    ): void {
        Log::info('实验目录继承操作', [
            'operation' => $operation,
            'parent_catalog_id' => $parentCatalog->id,
            'child_catalog_id' => $childCatalog->id,
            'user_id' => $user->id,
            'user_org_level' => $user->organization_level,
            'user_org_id' => $user->organization_id ?? $user->school_id,
            'timestamp' => now()
        ]);
    }
}
