<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentAlertConfig;
use App\Models\ExperimentAlert;
use App\Models\ExperimentMonitoringStatistics;
use App\Models\User;
use App\Models\School;
use App\Models\AdministrativeRegion;

class ExperimentMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建预警配置
        $this->createAlertConfigs();
        
        // 创建监控统计数据
        $this->createMonitoringStatistics();
        
        // 创建测试预警
        $this->createTestAlerts();
        
        $this->command->info('实验监控预警数据创建完成！');
    }

    /**
     * 创建预警配置
     */
    private function createAlertConfigs(): void
    {
        // 获取省级管理员
        $provinceAdmin = User::where('organization_level', 1)->first();
        if (!$provinceAdmin) {
            $this->command->warn('未找到省级管理员用户，跳过创建省级预警配置');
            return;
        }

        // 获取河北省
        $hebeiProvince = AdministrativeRegion::where('name', '河北省')->where('level', 1)->first();
        if (!$hebeiProvince) {
            $this->command->warn('未找到河北省数据，跳过创建预警配置');
            return;
        }

        // 创建省级预警配置
        $provinceConfigs = [
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id,
                'organization_name' => $hebeiProvince->name,
                'alert_type' => 'overdue',
                'threshold_value' => 0,
                'alert_days' => 3,
                'is_active' => true,
                'alert_rules' => '实验计划时间到期前3天开始预警，超期后立即发出高级预警',
                'notification_settings' => [
                    'email' => true,
                    'sms' => false,
                    'system' => true,
                    'recipients' => []
                ],
                'created_by' => $provinceAdmin->id
            ],
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id,
                'organization_name' => $hebeiProvince->name,
                'alert_type' => 'completion_rate',
                'threshold_value' => 80.00,
                'alert_days' => 7,
                'is_active' => true,
                'alert_rules' => '学期过半时完成率低于80%发出预警，学期末低于60%发出严重预警',
                'notification_settings' => [
                    'email' => true,
                    'sms' => true,
                    'system' => true,
                    'recipients' => []
                ],
                'created_by' => $provinceAdmin->id
            ],
            [
                'organization_type' => 'province',
                'organization_id' => $hebeiProvince->id,
                'organization_name' => $hebeiProvince->name,
                'alert_type' => 'quality_score',
                'threshold_value' => 70.00,
                'alert_days' => 0,
                'is_active' => true,
                'alert_rules' => '实验质量评分低于70分立即预警，低于60分发出严重预警',
                'notification_settings' => [
                    'email' => true,
                    'sms' => false,
                    'system' => true,
                    'recipients' => []
                ],
                'created_by' => $provinceAdmin->id
            ]
        ];

        foreach ($provinceConfigs as $config) {
            ExperimentAlertConfig::updateOrCreate(
                [
                    'organization_type' => $config['organization_type'],
                    'organization_id' => $config['organization_id'],
                    'alert_type' => $config['alert_type']
                ],
                $config
            );
        }

        $this->command->info('省级预警配置创建完成');

        // 获取市级管理员
        $cityAdmin = User::where('organization_level', 2)->first();
        if (!$cityAdmin) {
            $this->command->warn('未找到市级管理员用户，跳过创建市级预警配置');
            return;
        }

        // 获取石家庄市
        $shijiazhuang = AdministrativeRegion::where('name', '石家庄市')->where('level', 2)->first();
        if (!$shijiazhuang) {
            $this->command->warn('未找到石家庄市数据，跳过创建市级预警配置');
            return;
        }

        // 创建市级预警配置（更严格的要求）
        $cityConfigs = [
            [
                'organization_type' => 'city',
                'organization_id' => $shijiazhuang->id,
                'organization_name' => $shijiazhuang->name,
                'alert_type' => 'completion_rate',
                'threshold_value' => 85.00,
                'alert_days' => 5,
                'is_active' => true,
                'alert_rules' => '石家庄市对实验完成率要求更高，低于85%即发出预警',
                'notification_settings' => [
                    'email' => true,
                    'sms' => true,
                    'system' => true,
                    'recipients' => []
                ],
                'created_by' => $cityAdmin->id
            ]
        ];

        foreach ($cityConfigs as $config) {
            ExperimentAlertConfig::updateOrCreate(
                [
                    'organization_type' => $config['organization_type'],
                    'organization_id' => $config['organization_id'],
                    'alert_type' => $config['alert_type']
                ],
                $config
            );
        }

        $this->command->info('市级预警配置创建完成');
    }

    /**
     * 创建监控统计数据
     */
    private function createMonitoringStatistics(): void
    {
        $schools = School::take(5)->get();
        $semester = '2024-2025-1';

        foreach ($schools as $school) {
            // 创建学校统计数据
            ExperimentMonitoringStatistics::updateOrCreate(
                [
                    'target_type' => 'school',
                    'target_id' => $school->id,
                    'semester' => $semester,
                    'statistics_date' => now()->toDateString()
                ],
                [
                    'target_name' => $school->name,
                    'total_planned_experiments' => rand(50, 100),
                    'completed_experiments' => rand(30, 80),
                    'overdue_experiments' => rand(0, 10),
                    'pending_experiments' => rand(5, 20),
                    'completion_rate' => rand(60, 95),
                    'overdue_rate' => rand(0, 15),
                    'quality_score' => rand(65, 95),
                    'avg_completion_days' => rand(1, 7),
                    'max_overdue_days' => rand(0, 14),
                    'subject_statistics' => [
                        'physics' => ['total' => 20, 'completed' => 15, 'completion_rate' => 75],
                        'chemistry' => ['total' => 18, 'completed' => 16, 'completion_rate' => 89],
                        'biology' => ['total' => 15, 'completed' => 12, 'completion_rate' => 80]
                    ],
                    'grade_statistics' => [
                        '7' => ['total' => 15, 'completed' => 12, 'completion_rate' => 80],
                        '8' => ['total' => 20, 'completed' => 16, 'completion_rate' => 80],
                        '9' => ['total' => 18, 'completed' => 15, 'completion_rate' => 83]
                    ],
                    'monthly_statistics' => [
                        '2024-09' => ['total' => 10, 'completed' => 8, 'completion_rate' => 80],
                        '2024-10' => ['total' => 15, 'completed' => 12, 'completion_rate' => 80],
                        '2024-11' => ['total' => 12, 'completed' => 10, 'completion_rate' => 83]
                    ],
                    'calculated_at' => now()
                ]
            );
        }

        $this->command->info('监控统计数据创建完成');
    }

    /**
     * 创建测试预警
     */
    private function createTestAlerts(): void
    {
        $schools = School::take(3)->get();

        foreach ($schools as $index => $school) {
            // 创建不同类型的预警
            $alerts = [
                [
                    'alert_type' => 'completion_rate',
                    'target_type' => 'school',
                    'target_id' => $school->id,
                    'target_name' => $school->name,
                    'alert_level' => 'medium',
                    'alert_title' => '实验完成率过低',
                    'alert_message' => "学校「{$school->name}」实验完成率为 75%，低于预警阈值 80%",
                    'alert_data' => [
                        'semester' => '2024-2025-1',
                        'total_experiments' => 60,
                        'completed_experiments' => 45,
                        'overdue_experiments' => 5
                    ],
                    'alert_value' => 75.00,
                    'threshold_value' => 80.00,
                    'is_read' => false,
                    'is_resolved' => false,
                    'alert_time' => now()->subDays($index + 1)
                ],
                [
                    'alert_type' => 'overdue',
                    'target_type' => 'experiment',
                    'target_id' => rand(1, 100),
                    'target_name' => '物理实验：测量物体的密度',
                    'alert_level' => 'high',
                    'alert_title' => '实验超期未开',
                    'alert_message' => '实验「测量物体的密度」已超期 5 天未开课',
                    'alert_data' => [
                        'school_id' => $school->id,
                        'school_name' => $school->name,
                        'teacher_id' => rand(1, 20),
                        'planned_date' => now()->subDays(5)->toDateString(),
                        'days_overdue' => 5
                    ],
                    'alert_value' => 5,
                    'threshold_value' => 0,
                    'is_read' => $index > 0,
                    'is_resolved' => false,
                    'alert_time' => now()->subDays($index)
                ]
            ];

            foreach ($alerts as $alertData) {
                ExperimentAlert::create($alertData);
            }
        }

        $this->command->info('测试预警数据创建完成');
    }
}
