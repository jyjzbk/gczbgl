<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentMaintenance;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;

class EquipmentMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipments = Equipment::all();
        $users = User::all();

        if ($equipments->isEmpty() || $users->isEmpty()) {
            $this->command->info('缺少基础数据，跳过维修记录数据填充');
            return;
        }

        // 创建不同状态的维修记录
        $maintenanceRecords = [
            // 待维修记录
            [
                'status' => EquipmentMaintenance::STATUS_PENDING,
                'count' => 6,
                'days_ago' => 1
            ],
            // 维修中记录
            [
                'status' => EquipmentMaintenance::STATUS_PROCESSING,
                'count' => 4,
                'days_ago' => 5
            ],
            // 已完成记录
            [
                'status' => EquipmentMaintenance::STATUS_COMPLETED,
                'count' => 8,
                'days_ago' => 15
            ],
            // 无法修复记录
            [
                'status' => EquipmentMaintenance::STATUS_UNREPAIRABLE,
                'count' => 2,
                'days_ago' => 10
            ]
        ];

        foreach ($maintenanceRecords as $recordType) {
            for ($i = 0; $i < $recordType['count']; $i++) {
                $equipment = $equipments->random();
                $reporter = $users->random();
                $maintainer = $users->count() > 1 
                    ? $users->where('id', '!=', $reporter->id)->random()
                    : $reporter;

                $reportDate = Carbon::now()->subDays($recordType['days_ago'] + rand(0, 5));
                
                $maintenanceData = [
                    'equipment_id' => $equipment->id,
                    'reporter_id' => $reporter->id,
                    'fault_description' => $this->getRandomFaultDescription(),
                    'fault_type' => $this->getRandomFaultType(),
                    'urgency_level' => rand(1, 3),
                    'report_date' => $reportDate->toDateString(),
                    'status' => $recordType['status'],
                    'remark' => $this->getRandomRemark()
                ];

                // 根据状态设置不同的字段
                switch ($recordType['status']) {
                    case EquipmentMaintenance::STATUS_PENDING:
                        // 待维修记录不需要额外字段
                        break;

                    case EquipmentMaintenance::STATUS_PROCESSING:
                        $maintenanceData['maintainer_id'] = $maintainer->id;
                        $maintenanceData['start_date'] = $reportDate->copy()->addDays(rand(1, 3))->toDateString();
                        break;

                    case EquipmentMaintenance::STATUS_COMPLETED:
                        $startDate = $reportDate->copy()->addDays(rand(1, 3));
                        $completeDate = $startDate->copy()->addDays(rand(1, 7));
                        
                        $maintenanceData['maintainer_id'] = $maintainer->id;
                        $maintenanceData['start_date'] = $startDate->toDateString();
                        $maintenanceData['complete_date'] = $completeDate->toDateString();
                        $maintenanceData['cost'] = rand(0, 500);
                        $maintenanceData['solution'] = $this->getRandomSolution();
                        $maintenanceData['parts_replaced'] = $this->getRandomPartsReplaced();
                        $maintenanceData['quality_rating'] = rand(3, 5);
                        break;

                    case EquipmentMaintenance::STATUS_UNREPAIRABLE:
                        $startDate = $reportDate->copy()->addDays(rand(1, 3));
                        $completeDate = $startDate->copy()->addDays(rand(1, 5));
                        
                        $maintenanceData['maintainer_id'] = $maintainer->id;
                        $maintenanceData['start_date'] = $startDate->toDateString();
                        $maintenanceData['complete_date'] = $completeDate->toDateString();
                        $maintenanceData['solution'] = $this->getRandomUnrepairableReason();
                        break;
                }

                EquipmentMaintenance::create($maintenanceData);
            }
        }

        $this->command->info('设备维修记录数据填充完成');
    }

    /**
     * 获取随机故障描述
     */
    private function getRandomFaultDescription(): string
    {
        $descriptions = [
            '设备无法正常启动，按下电源键无反应',
            '显示屏出现花屏现象，图像不清晰',
            '设备运行过程中发出异常噪音',
            '温度控制失效，无法达到设定温度',
            '电源指示灯不亮，疑似电源故障',
            '按键失灵，部分功能无法操作',
            '设备外壳出现裂纹，影响使用安全',
            '精度下降，测量结果不准确',
            '设备频繁死机，需要重启才能使用',
            '连接线路松动，信号传输不稳定',
            '镜头模糊，无法清晰观察',
            '校准失效，需要重新校准',
            '设备过热，运行一段时间后自动关机',
            '机械部件卡死，无法正常转动',
            '软件系统出错，界面显示异常'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * 获取随机故障类型
     */
    private function getRandomFaultType(): string
    {
        $types = [
            '电源故障',
            '机械故障',
            '电子元件故障',
            '软件故障',
            '传感器故障',
            '显示故障',
            '控制系统故障',
            '连接故障',
            '校准问题',
            '磨损老化',
            '外部损坏',
            '过载故障',
            '温度异常',
            '精度问题',
            '操作系统故障'
        ];

        return $types[array_rand($types)];
    }

    /**
     * 获取随机解决方案
     */
    private function getRandomSolution(): string
    {
        $solutions = [
            '更换损坏的电源模块，重新测试设备功能',
            '清洁内部灰尘，更换老化的电子元件',
            '重新校准设备，调整相关参数',
            '更换磨损的机械部件，添加润滑油',
            '修复软件系统，重新安装驱动程序',
            '更换损坏的传感器，重新连接线路',
            '调整温度控制系统，更换温控元件',
            '修复显示模块，更换显示屏',
            '重新焊接松动的连接点',
            '更新软件版本，修复系统漏洞',
            '更换老化的光学元件',
            '重新设置系统参数，恢复出厂设置',
            '加强散热系统，清理散热通道',
            '更换控制电路板',
            '重新安装操作系统'
        ];

        return $solutions[array_rand($solutions)];
    }

    /**
     * 获取随机更换部件
     */
    private function getRandomPartsReplaced(): string
    {
        $parts = [
            '电源适配器',
            '主控制板',
            '显示屏',
            '传感器模块',
            '电机',
            '电容器',
            '继电器',
            '保险丝',
            '连接线',
            '散热风扇',
            '光学镜头',
            '按键开关',
            '温控器',
            '变压器',
            '电路板'
        ];

        // 有50%的概率没有更换部件
        if (rand(1, 2) == 1) {
            return '';
        }

        return $parts[array_rand($parts)];
    }

    /**
     * 获取随机无法修复原因
     */
    private function getRandomUnrepairableReason(): string
    {
        $reasons = [
            '设备主板严重损坏，无法修复，建议报废',
            '关键部件已停产，无法找到替换件',
            '维修成本过高，超过设备价值，不建议修复',
            '设备老化严重，多个部件同时故障',
            '结构性损坏，无法恢复原有功能',
            '精密部件损坏，无法达到原有精度要求',
            '安全隐患严重，不适合继续使用',
            '技术过于陈旧，已无维修价值'
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * 获取随机备注
     */
    private function getRandomRemark(): string
    {
        $remarks = [
            '设备使用频率较高，建议加强日常维护',
            '故障可能与使用环境有关，建议改善使用条件',
            '建议定期检查相关部件',
            '使用过程中请注意操作规范',
            '如再次出现类似问题，请及时联系维修人员',
            '维修后请进行功能测试',
            '建议更新使用手册和操作指南',
            '需要对使用人员进行培训',
            '建议建立设备维护档案',
            '故障原因已查明并解决'
        ];

        // 有40%的概率没有备注
        if (rand(1, 10) <= 4) {
            return '';
        }

        return $remarks[array_rand($remarks)];
    }
}
