<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolExperimentCatalogSelection;
use App\Models\ExperimentCatalogDeletePermission;
use App\Models\School;
use App\Models\User;
use App\Models\AdministrativeRegion;

class SchoolExperimentCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建删除权限配置
        $this->createDeletePermissions();
        
        // 创建学校实验目录选择
        $this->createSchoolSelections();
        
        $this->command->info('学校实验目录管理数据创建完成！');
    }

    /**
     * 创建删除权限配置
     */
    private function createDeletePermissions(): void
    {
        // 获取省级管理员
        $provinceAdmin = User::where('organization_level', 1)->first();
        if (!$provinceAdmin) {
            $this->command->warn('未找到省级管理员用户，跳过创建省级删除权限配置');
            return;
        }

        // 获取河北省
        $hebeiProvince = AdministrativeRegion::where('name', '河北省')->where('level', 1)->first();
        if (!$hebeiProvince) {
            $this->command->warn('未找到河北省数据，跳过创建删除权限配置');
            return;
        }

        // 创建省级删除权限配置
        ExperimentCatalogDeletePermission::updateOrCreate(
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id
            ],
            [
                'organization_name' => $hebeiProvince->name,
                'allow_school_delete' => true,
                'require_delete_reason' => true,
                'max_delete_percentage' => 20,
                'delete_rules' => '学校可以根据自身条件删除不适合的实验，但需要详细说明删除理由，删除比例不得超过20%。删除的实验不会影响上级统计，但需要在年度报告中说明情况。',
                'created_by' => $provinceAdmin->id,
                'is_active' => true
            ]
        );

        $this->command->info('省级删除权限配置创建完成');

        // 获取市级管理员
        $cityAdmin = User::where('organization_level', 2)->first();
        if (!$cityAdmin) {
            $this->command->warn('未找到市级管理员用户，跳过创建市级删除权限配置');
            return;
        }

        // 获取石家庄市
        $shijiazhuang = AdministrativeRegion::where('name', '石家庄市')->where('level', 2)->first();
        if (!$shijiazhuang) {
            $this->command->warn('未找到石家庄市数据，跳过创建市级删除权限配置');
            return;
        }

        // 创建市级删除权限配置（更严格的要求）
        ExperimentCatalogDeletePermission::updateOrCreate(
            [
                'organization_type' => 'city',
                'organization_id' => $shijiazhuang->id
            ],
            [
                'organization_name' => $shijiazhuang->name,
                'allow_school_delete' => true,
                'require_delete_reason' => true,
                'max_delete_percentage' => 15,
                'delete_rules' => '石家庄市对实验删除要求更严格，删除比例不得超过15%。学校需要提供详细的删除理由和替代方案，并经过学校实验教学委员会审议。',
                'created_by' => $cityAdmin->id,
                'is_active' => true
            ]
        );

        $this->command->info('市级删除权限配置创建完成');
    }

    /**
     * 创建学校实验目录选择
     */
    private function createSchoolSelections(): void
    {
        // 获取学校管理员
        $schoolAdmin = User::where('organization_level', 5)->first();
        if (!$schoolAdmin) {
            $this->command->warn('未找到学校管理员用户，跳过创建学校选择');
            return;
        }

        // 获取测试学校
        $school = School::first();
        if (!$school) {
            $this->command->warn('未找到学校数据，跳过创建学校选择');
            return;
        }

        // 获取石家庄市
        $shijiazhuang = AdministrativeRegion::where('name', '石家庄市')->where('level', 2)->first();
        if (!$shijiazhuang) {
            $this->command->warn('未找到石家庄市数据，跳过创建学校选择');
            return;
        }

        // 创建学校实验目录选择（选择市级标准）
        SchoolExperimentCatalogSelection::setSchoolSelection(
            $school->id,
            'city',
            $shijiazhuang->id,
            $shijiazhuang->name,
            true, // 允许删除实验
            '本校选择石家庄市级实验目录标准，该标准更符合我校的实际教学条件和设备配置情况。同时申请删除权限，以便根据学校实际情况调整实验内容。',
            $schoolAdmin->id
        );

        $this->command->info('学校实验目录选择创建完成');

        // 为其他学校创建不同的选择示例
        $schools = School::skip(1)->take(3)->get();
        foreach ($schools as $index => $school) {
            $selections = [
                [
                    'level' => 'province',
                    'org_id' => AdministrativeRegion::where('name', '河北省')->where('level', 1)->first()?->id,
                    'org_name' => '河北省',
                    'can_delete' => false,
                    'reason' => '本校选择省级标准实验目录，严格按照省级要求执行，不申请删除权限。'
                ],
                [
                    'level' => 'city',
                    'org_id' => $shijiazhuang->id,
                    'org_name' => $shijiazhuang->name,
                    'can_delete' => true,
                    'reason' => '选择市级标准，申请删除权限以适应学校特色教学需求。'
                ],
                [
                    'level' => 'county',
                    'org_id' => AdministrativeRegion::where('name', '长安区')->where('level', 3)->first()?->id,
                    'org_name' => '长安区',
                    'can_delete' => false,
                    'reason' => '选择区县级标准，该标准最贴近本校实际情况。'
                ]
            ];

            $selection = $selections[$index] ?? $selections[0];
            
            if ($selection['org_id']) {
                SchoolExperimentCatalogSelection::setSchoolSelection(
                    $school->id,
                    $selection['level'],
                    $selection['org_id'],
                    $selection['org_name'],
                    $selection['can_delete'],
                    $selection['reason'],
                    $schoolAdmin->id
                );
            }
        }

        $this->command->info('其他学校实验目录选择创建完成');
    }
}
