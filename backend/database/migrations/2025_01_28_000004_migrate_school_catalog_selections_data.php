<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 检查旧表是否存在
        if (!Schema::hasTable('school_experiment_catalog_selections')) {
            return;
        }

        // 迁移数据从旧表到新表
        $oldSelections = DB::table('school_experiment_catalog_selections')->get();

        foreach ($oldSelections as $selection) {
            // 将旧的选择级别转换为新的级别数字
            $sourceLevel = $this->convertLevelToNumber($selection->selected_level);
            
            // 确定配置类型（旧数据都是学校选择）
            $configType = 'selection';
            
            // 获取配置操作人的级别
            $configuredByLevel = $this->getUserLevel($selection->selected_by);
            
            DB::table('school_experiment_catalog_configs')->insert([
                'school_id' => $selection->school_id,
                'config_type' => $configType,
                'source_level' => $sourceLevel,
                'source_org_id' => $selection->selected_org_id,
                'source_org_name' => $selection->selected_org_name,
                'can_modify_selection' => true, // 旧数据默认允许修改
                'can_delete_experiments' => $selection->can_delete_experiments ?? false,
                'configured_by' => $selection->selected_by,
                'configured_by_level' => $configuredByLevel,
                'configured_at' => $selection->selected_at ?? $selection->created_at,
                'config_reason' => $selection->selection_reason,
                'status' => 1, // 启用状态
                'effective_date' => now()->toDateString(),
                'created_at' => $selection->created_at,
                'updated_at' => $selection->updated_at,
            ]);
        }

        // 更新实验目录的基准标识
        $this->updateExperimentCatalogsBaseline();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 清空新表数据（如果需要回滚）
        DB::table('school_experiment_catalog_configs')->truncate();
        
        // 重置实验目录的基准标识
        DB::table('experiment_catalogs')->update([
            'is_baseline_catalog' => false,
            'baseline_priority' => 0,
            'usage_count' => 0,
            'last_used_at' => null
        ]);
    }

    /**
     * 将旧的级别字符串转换为数字
     */
    private function convertLevelToNumber(string $level): int
    {
        switch ($level) {
            case 'province':
                return 1;
            case 'city':
                return 2;
            case 'county':
                return 3;
            default:
                return 5; // 默认学校级
        }
    }

    /**
     * 获取用户的组织级别
     */
    private function getUserLevel(int $userId): int
    {
        $user = DB::table('users')->where('id', $userId)->first();
        return $user->organization_level ?? 5;
    }

    /**
     * 更新实验目录的基准标识
     */
    private function updateExperimentCatalogsBaseline(): void
    {
        // 获取所有被选择的目录组织
        $selectedOrgs = DB::table('school_experiment_catalog_configs')
            ->select('source_level', 'source_org_id')
            ->groupBy('source_level', 'source_org_id')
            ->get();

        foreach ($selectedOrgs as $org) {
            // 统计使用次数
            $usageCount = DB::table('school_experiment_catalog_configs')
                ->where('source_level', $org->source_level)
                ->where('source_org_id', $org->source_org_id)
                ->where('status', 1)
                ->count();

            // 更新对应的实验目录
            DB::table('experiment_catalogs')
                ->where('management_level', $org->source_level)
                ->where('created_by_org_id', $org->source_org_id)
                ->update([
                    'is_baseline_catalog' => true,
                    'baseline_priority' => $usageCount >= 5 ? 1 : 0, // 使用次数>=5设为推荐
                    'usage_count' => $usageCount,
                    'last_used_at' => now()
                ]);
        }
    }
};
