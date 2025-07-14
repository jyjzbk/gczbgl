<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratory;
use App\Models\School;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取学校
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->command->error('请先运行 SchoolSeeder');
            return;
        }

        foreach ($schools as $school) {
            // 为每个学校创建基础实验室
            $laboratories = [
                [
                    'school_id' => $school->id,
                    'name' => '物理实验室1',
                    'code' => 'PHY_LAB_01',
                    'type' => Laboratory::TYPE_PHYSICS,
                    'location' => '教学楼3楼301室',
                    'area' => 120.00,
                    'capacity' => 50,
                    'equipment_list' => '力学实验台、电学实验台、光学实验台等',
                    'safety_rules' => '1.进入实验室必须穿实验服\n2.严禁携带易燃易爆物品\n3.实验结束后整理器材',
                    'status' => Laboratory::STATUS_NORMAL
                ],
                [
                    'school_id' => $school->id,
                    'name' => '化学实验室1',
                    'code' => 'CHE_LAB_01',
                    'type' => Laboratory::TYPE_CHEMISTRY,
                    'location' => '教学楼3楼302室',
                    'area' => 130.00,
                    'capacity' => 48,
                    'equipment_list' => '通风橱、实验台、试剂柜、洗眼器等',
                    'safety_rules' => '1.必须穿实验服和护目镜\n2.严格按照实验步骤操作\n3.注意通风和防火',
                    'status' => Laboratory::STATUS_NORMAL
                ],
                [
                    'school_id' => $school->id,
                    'name' => '生物实验室1',
                    'code' => 'BIO_LAB_01',
                    'type' => Laboratory::TYPE_BIOLOGY,
                    'location' => '教学楼3楼303室',
                    'area' => 110.00,
                    'capacity' => 45,
                    'equipment_list' => '显微镜、解剖台、标本柜、培养箱等',
                    'safety_rules' => '1.爱护显微镜等精密仪器\n2.正确处理生物标本\n3.保持实验室清洁',
                    'status' => Laboratory::STATUS_NORMAL
                ]
            ];

            // 如果是高中，增加更多实验室
            if ($school->type === 3) { // 高中
                $laboratories[] = [
                    'school_id' => $school->id,
                    'name' => '物理实验室2',
                    'code' => 'PHY_LAB_02',
                    'type' => Laboratory::TYPE_PHYSICS,
                    'location' => '教学楼4楼401室',
                    'area' => 125.00,
                    'capacity' => 52,
                    'equipment_list' => '高级力学实验台、电磁学实验台、现代物理实验设备等',
                    'safety_rules' => '1.进入实验室必须穿实验服\n2.严禁携带易燃易爆物品\n3.实验结束后整理器材',
                    'status' => Laboratory::STATUS_NORMAL
                ];

                $laboratories[] = [
                    'school_id' => $school->id,
                    'name' => '综合实验室',
                    'code' => 'COM_LAB_01',
                    'type' => Laboratory::TYPE_COMPREHENSIVE,
                    'location' => '教学楼4楼402室',
                    'area' => 140.00,
                    'capacity' => 60,
                    'equipment_list' => '多媒体设备、计算机、通用实验台等',
                    'safety_rules' => '1.爱护设备\n2.按规定使用\n3.保持整洁',
                    'status' => Laboratory::STATUS_NORMAL
                ];
            }

            foreach ($laboratories as $laboratory) {
                Laboratory::create($laboratory);
            }
        }
    }
}
