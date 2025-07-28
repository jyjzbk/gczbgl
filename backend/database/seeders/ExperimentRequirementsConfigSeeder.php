<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentRequirementsConfig;
use App\Models\User;
use App\Models\AdministrativeRegion;

class ExperimentRequirementsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取省级管理员用户
        $provinceAdmin = User::where('organization_level', 1)->first();
        if (!$provinceAdmin) {
            $this->command->warn('未找到省级管理员用户，跳过创建省级配置');
            return;
        }

        // 获取河北省
        $hebeiProvince = AdministrativeRegion::where('name', '河北省')->where('level', 1)->first();
        if (!$hebeiProvince) {
            $this->command->warn('未找到河北省数据，跳过创建配置');
            return;
        }

        // 创建省级配置
        $provinceConfigs = [
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id,
                'experiment_type' => '分组实验',
                'min_images' => 3,
                'max_images' => 8,
                'min_videos' => 0,
                'max_videos' => 2,
                'is_inherited' => false,
                'created_by' => $provinceAdmin->id,
                'description' => '河北省分组实验图片视频上传要求：每次实验至少上传3张图片，最多8张；视频可选，最多2个',
                'is_active' => true
            ],
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id,
                'experiment_type' => '演示实验',
                'min_images' => 2,
                'max_images' => 5,
                'min_videos' => 0,
                'max_videos' => 1,
                'is_inherited' => false,
                'created_by' => $provinceAdmin->id,
                'description' => '河北省演示实验图片视频上传要求：每次实验至少上传2张图片，最多5张；视频可选，最多1个',
                'is_active' => true
            ]
        ];

        foreach ($provinceConfigs as $config) {
            ExperimentRequirementsConfig::updateOrCreate(
                [
                    'organization_type' => $config['organization_type'],
                    'organization_id' => $config['organization_id'],
                    'experiment_type' => $config['experiment_type']
                ],
                $config
            );
        }

        $this->command->info('省级实验要求配置创建完成');

        // 获取市级管理员用户
        $cityAdmin = User::where('organization_level', 2)->first();
        if (!$cityAdmin) {
            $this->command->warn('未找到市级管理员用户，跳过创建市级配置');
            return;
        }

        // 获取石家庄市
        $shijiazhuang = AdministrativeRegion::where('name', '石家庄市')->where('level', 2)->first();
        if (!$shijiazhuang) {
            $this->command->warn('未找到石家庄市数据，跳过创建市级配置');
            return;
        }

        // 创建市级配置（继承省级配置）
        $cityConfigs = [
            [
                'organization_type' => 'city',
                'organization_id' => $shijiazhuang->id,
                'experiment_type' => '分组实验',
                'min_images' => 4,
                'max_images' => 10,
                'min_videos' => 1,
                'max_videos' => 3,
                'is_inherited' => false,
                'created_by' => $cityAdmin->id,
                'description' => '石家庄市分组实验要求：在省级基础上提高要求，至少4张图片，至少1个视频',
                'is_active' => true
            ],
            [
                'organization_type' => 'city',
                'organization_id' => $shijiazhuang->id,
                'experiment_type' => '演示实验',
                'min_images' => 2,
                'max_images' => 5,
                'min_videos' => 0,
                'max_videos' => 1,
                'is_inherited' => true,
                'created_by' => $cityAdmin->id,
                'description' => '石家庄市演示实验要求：继承省级配置',
                'is_active' => true
            ]
        ];

        foreach ($cityConfigs as $config) {
            ExperimentRequirementsConfig::updateOrCreate(
                [
                    'organization_type' => $config['organization_type'],
                    'organization_id' => $config['organization_id'],
                    'experiment_type' => $config['experiment_type']
                ],
                $config
            );
        }

        $this->command->info('市级实验要求配置创建完成');

        // 获取区县级管理员用户
        $countyAdmin = User::where('organization_level', 3)->first();
        if (!$countyAdmin) {
            $this->command->warn('未找到区县级管理员用户，跳过创建区县级配置');
            return;
        }

        // 获取长安区
        $changanDistrict = AdministrativeRegion::where('name', '长安区')->where('level', 3)->first();
        if (!$changanDistrict) {
            $this->command->warn('未找到长安区数据，跳过创建区县级配置');
            return;
        }

        // 创建区县级配置
        $countyConfigs = [
            [
                'organization_type' => 'county',
                'organization_id' => $changanDistrict->id,
                'experiment_type' => '分组实验',
                'min_images' => 4,
                'max_images' => 10,
                'min_videos' => 1,
                'max_videos' => 3,
                'is_inherited' => true,
                'created_by' => $countyAdmin->id,
                'description' => '长安区分组实验要求：继承市级配置',
                'is_active' => true
            ],
            [
                'organization_type' => 'county',
                'organization_id' => $changanDistrict->id,
                'experiment_type' => '演示实验',
                'min_images' => 3,
                'max_images' => 6,
                'min_videos' => 0,
                'max_videos' => 2,
                'is_inherited' => false,
                'created_by' => $countyAdmin->id,
                'description' => '长安区演示实验要求：在省级基础上适当提高要求',
                'is_active' => true
            ]
        ];

        foreach ($countyConfigs as $config) {
            ExperimentRequirementsConfig::updateOrCreate(
                [
                    'organization_type' => $config['organization_type'],
                    'organization_id' => $config['organization_id'],
                    'experiment_type' => $config['experiment_type']
                ],
                $config
            );
        }

        $this->command->info('区县级实验要求配置创建完成');
        $this->command->info('所有实验要求配置数据创建完成！');
    }
}
