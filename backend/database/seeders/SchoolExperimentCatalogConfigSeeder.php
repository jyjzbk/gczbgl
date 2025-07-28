<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;
use App\Models\AdministrativeRegion;
use App\Models\Subject;

class SchoolExperimentCatalogConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建学校实验目录配置测试数据...');

        // 创建学校实验目录配置
        $this->createSchoolConfigs();
        
        // 创建完成率基准数据
        $this->createCompletionBaselines();
        
        // 更新实验目录基准标识
        $this->updateExperimentCatalogBaselines();

        $this->command->info('学校实验目录配置测试数据创建完成！');
    }

    /**
     * 创建学校实验目录配置
     */
    private function createSchoolConfigs(): void
    {
        // 获取测试用户
        $provinceAdmin = User::where('organization_level', 1)->first();
        $cityAdmin = User::where('organization_level', 2)->first();
        $countyAdmin = User::where('organization_level', 3)->first();
        $schoolAdmin = User::where('organization_level', 5)->first();

        if (!$provinceAdmin || !$cityAdmin || !$countyAdmin || !$schoolAdmin) {
            $this->command->warn('缺少测试用户，请先运行用户数据填充器');
            return;
        }

        // 获取行政区域
        $hebei = AdministrativeRegion::where('name', '河北省')->where('level', 1)->first();
        $shijiazhuang = AdministrativeRegion::where('name', '石家庄市')->where('level', 2)->first();
        $changan = AdministrativeRegion::where('name', '长安区')->where('level', 3)->first();

        if (!$hebei || !$shijiazhuang || !$changan) {
            $this->command->warn('缺少行政区域数据，请先运行行政区域数据填充器');
            return;
        }

        // 获取学校
        $schools = School::limit(10)->get();
        if ($schools->isEmpty()) {
            $this->command->warn('缺少学校数据，请先运行学校数据填充器');
            return;
        }

        $configs = [];
        $now = now();

        foreach ($schools as $index => $school) {
            // 模拟不同的配置场景
            switch ($index % 4) {
                case 0: // 省直学校 - 选择省级目录
                    $configs[] = [
                        'school_id' => $school->id,
                        'config_type' => 'selection',
                        'source_level' => 1,
                        'source_org_id' => $hebei->id,
                        'source_org_name' => $hebei->name,
                        'can_modify_selection' => true,
                        'can_delete_experiments' => false,
                        'configured_by' => $schoolAdmin->id,
                        'configured_by_level' => 5,
                        'configured_at' => $now->copy()->subDays(rand(1, 30)),
                        'config_reason' => '本校为省直学校，选择省级实验目录标准，确保与省级教学要求保持一致。',
                        'status' => 1,
                        'effective_date' => $now->copy()->subDays(rand(1, 30))->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    break;

                case 1: // 市直学校 - 选择市级目录
                    $configs[] = [
                        'school_id' => $school->id,
                        'config_type' => 'selection',
                        'source_level' => 2,
                        'source_org_id' => $shijiazhuang->id,
                        'source_org_name' => $shijiazhuang->name,
                        'can_modify_selection' => true,
                        'can_delete_experiments' => true,
                        'configured_by' => $schoolAdmin->id,
                        'configured_by_level' => 5,
                        'configured_at' => $now->copy()->subDays(rand(1, 30)),
                        'config_reason' => '本校选择石家庄市级实验目录标准，该标准更符合我校的实际教学条件和设备配置情况。',
                        'status' => 1,
                        'effective_date' => $now->copy()->subDays(rand(1, 30))->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    break;

                case 2: // 区县直管学校 - 上级指定区县级目录
                    $configs[] = [
                        'school_id' => $school->id,
                        'config_type' => 'assignment',
                        'source_level' => 3,
                        'source_org_id' => $changan->id,
                        'source_org_name' => $changan->name,
                        'can_modify_selection' => false,
                        'can_delete_experiments' => false,
                        'configured_by' => $countyAdmin->id,
                        'configured_by_level' => 3,
                        'configured_at' => $now->copy()->subDays(rand(1, 30)),
                        'config_reason' => '根据区县统一管理要求，为本校指定长安区实验目录标准，确保区域内教学标准统一。',
                        'status' => 1,
                        'effective_date' => $now->copy()->subDays(rand(1, 30))->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    break;

                case 3: // 学区学校 - 上级指定市级目录
                    $configs[] = [
                        'school_id' => $school->id,
                        'config_type' => 'assignment',
                        'source_level' => 2,
                        'source_org_id' => $shijiazhuang->id,
                        'source_org_name' => $shijiazhuang->name,
                        'can_modify_selection' => false,
                        'can_delete_experiments' => true,
                        'configured_by' => $countyAdmin->id,
                        'configured_by_level' => 3,
                        'configured_at' => $now->copy()->subDays(rand(1, 30)),
                        'config_reason' => '考虑到学区学校的实际情况，为本校指定石家庄市级实验目录标准，并允许删除不适合的实验项目。',
                        'status' => 1,
                        'effective_date' => $now->copy()->subDays(rand(1, 30))->toDateString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    break;
            }
        }

        DB::table('school_experiment_catalog_configs')->insert($configs);
        $this->command->info('已创建 ' . count($configs) . ' 条学校实验目录配置记录');
    }

    /**
     * 创建完成率基准数据
     */
    private function createCompletionBaselines(): void
    {
        $configs = DB::table('school_experiment_catalog_configs')->get();
        $subjects = Subject::limit(3)->get(); // 获取前3个学科

        if ($subjects->isEmpty()) {
            $this->command->warn('缺少学科数据，跳过创建完成率基准数据');
            return;
        }

        $baselines = [];
        $now = now();

        foreach ($configs as $config) {
            foreach ($subjects as $subject) {
                for ($grade = 1; $grade <= 6; $grade++) {
                    for ($semester = 1; $semester <= 2; $semester++) {
                        // 模拟实验数量
                        $totalExperiments = rand(8, 15);
                        $requiredExperiments = rand(5, 8);
                        $optionalExperiments = rand(2, 4);
                        $demoExperiments = rand(1, 3);
                        $groupExperiments = $totalExperiments - $requiredExperiments - $optionalExperiments - $demoExperiments;
                        $groupExperiments = max(0, $groupExperiments);

                        // 模拟完成情况
                        $completedExperiments = rand(0, $totalExperiments);
                        $completionRate = $totalExperiments > 0 ? round(($completedExperiments / $totalExperiments) * 100, 2) : 0;

                        $baselines[] = [
                            'school_id' => $config->school_id,
                            'config_id' => $config->id,
                            'subject_id' => $subject->id,
                            'grade' => $grade,
                            'semester' => $semester,
                            'total_experiments' => $totalExperiments,
                            'required_experiments' => $requiredExperiments,
                            'optional_experiments' => $optionalExperiments,
                            'demo_experiments' => $demoExperiments,
                            'group_experiments' => $groupExperiments,
                            'completed_experiments' => $completedExperiments,
                            'completion_rate' => $completionRate,
                            'last_calculated_at' => $now->copy()->subDays(rand(0, 7)),
                            'calculated_by' => $config->configured_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // 分批插入数据
        $chunks = array_chunk($baselines, 100);
        foreach ($chunks as $chunk) {
            DB::table('experiment_catalog_completion_baselines')->insert($chunk);
        }

        $this->command->info('已创建 ' . count($baselines) . ' 条完成率基准记录');
    }

    /**
     * 更新实验目录基准标识
     */
    private function updateExperimentCatalogBaselines(): void
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
                    'baseline_priority' => $usageCount >= 3 ? 1 : 0, // 使用次数>=3设为推荐
                    'usage_count' => $usageCount,
                    'last_used_at' => now()
                ]);
        }

        $this->command->info('已更新实验目录基准标识');
    }
}
