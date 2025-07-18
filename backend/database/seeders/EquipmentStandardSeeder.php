<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standards = [
            // 小学科学
            [
                'name' => '小学科学教学仪器配备标准（教育部）',
                'code' => 'MOE_PRIMARY_SCIENCE_2023',
                'authority_type' => 1, // 教育部
                'stage' => 1, // 小学
                'subject_code' => 'SCIENCE',
                'subject_name' => '科学',
                'description' => '根据教育部最新标准制定的小学科学教学仪器配备要求',
                'equipment_list' => json_encode([
                    [
                        'category' => '测量工具',
                        'items' => [
                            ['name' => '直尺', 'specification' => '30cm', 'quantity' => 50, 'unit' => '把'],
                            ['name' => '卷尺', 'specification' => '5m', 'quantity' => 5, 'unit' => '个'],
                            ['name' => '天平', 'specification' => '托盘天平500g', 'quantity' => 10, 'unit' => '台'],
                            ['name' => '量筒', 'specification' => '100ml', 'quantity' => 25, 'unit' => '个']
                        ]
                    ],
                    [
                        'category' => '观察工具',
                        'items' => [
                            ['name' => '放大镜', 'specification' => '5倍', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '显微镜', 'specification' => '学生用', 'quantity' => 15, 'unit' => '台'],
                            ['name' => '望远镜', 'specification' => '双筒', 'quantity' => 5, 'unit' => '个']
                        ]
                    ]
                ]),
                'version' => '2023.1',
                'effective_date' => '2023-09-01',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 初中物理
            [
                'name' => '初中物理教学仪器配备标准（教育部）',
                'code' => 'MOE_JUNIOR_PHYSICS_2023',
                'authority_type' => 1,
                'stage' => 2, // 初中
                'subject_code' => 'PHYSICS',
                'subject_name' => '物理',
                'description' => '根据教育部最新标准制定的初中物理教学仪器配备要求',
                'equipment_list' => json_encode([
                    [
                        'category' => '力学实验器材',
                        'items' => [
                            ['name' => '弹簧测力计', 'specification' => '5N', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '滑轮组', 'specification' => '演示用', 'quantity' => 5, 'unit' => '套'],
                            ['name' => '杠杆', 'specification' => '演示用', 'quantity' => 5, 'unit' => '个'],
                            ['name' => '斜面', 'specification' => '可调角度', 'quantity' => 10, 'unit' => '个']
                        ]
                    ],
                    [
                        'category' => '电学实验器材',
                        'items' => [
                            ['name' => '电流表', 'specification' => '0-0.6A', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '电压表', 'specification' => '0-3V', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '滑动变阻器', 'specification' => '20Ω 2A', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '电源', 'specification' => '学生电源', 'quantity' => 25, 'unit' => '台']
                        ]
                    ]
                ]),
                'version' => '2023.1',
                'effective_date' => '2023-09-01',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 初中化学
            [
                'name' => '初中化学教学仪器配备标准（教育部）',
                'code' => 'MOE_JUNIOR_CHEMISTRY_2023',
                'authority_type' => 1,
                'stage' => 2,
                'subject_code' => 'CHEMISTRY',
                'subject_name' => '化学',
                'description' => '根据教育部最新标准制定的初中化学教学仪器配备要求',
                'equipment_list' => json_encode([
                    [
                        'category' => '玻璃仪器',
                        'items' => [
                            ['name' => '试管', 'specification' => '18×180mm', 'quantity' => 100, 'unit' => '支'],
                            ['name' => '烧杯', 'specification' => '250ml', 'quantity' => 50, 'unit' => '个'],
                            ['name' => '锥形瓶', 'specification' => '250ml', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '量筒', 'specification' => '100ml', 'quantity' => 25, 'unit' => '个']
                        ]
                    ],
                    [
                        'category' => '加热器材',
                        'items' => [
                            ['name' => '酒精灯', 'specification' => '150ml', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '三脚架', 'specification' => '铁制', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '石棉网', 'specification' => '标准', 'quantity' => 50, 'unit' => '个']
                        ]
                    ]
                ]),
                'version' => '2023.1',
                'effective_date' => '2023-09-01',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 初中生物
            [
                'name' => '初中生物教学仪器配备标准（教育部）',
                'code' => 'MOE_JUNIOR_BIOLOGY_2023',
                'authority_type' => 1,
                'stage' => 2,
                'subject_code' => 'BIOLOGY',
                'subject_name' => '生物',
                'description' => '根据教育部最新标准制定的初中生物教学仪器配备要求',
                'equipment_list' => json_encode([
                    [
                        'category' => '观察器材',
                        'items' => [
                            ['name' => '显微镜', 'specification' => '学生用双目', 'quantity' => 25, 'unit' => '台'],
                            ['name' => '放大镜', 'specification' => '10倍', 'quantity' => 25, 'unit' => '个'],
                            ['name' => '解剖镜', 'specification' => '双目立体', 'quantity' => 5, 'unit' => '台']
                        ]
                    ],
                    [
                        'category' => '标本模型',
                        'items' => [
                            ['name' => '人体骨骼模型', 'specification' => '85cm', 'quantity' => 1, 'unit' => '个'],
                            ['name' => '心脏模型', 'specification' => '自然大', 'quantity' => 1, 'unit' => '个'],
                            ['name' => '植物细胞模型', 'specification' => '放大', 'quantity' => 1, 'unit' => '个']
                        ]
                    ]
                ]),
                'version' => '2023.1',
                'effective_date' => '2023-09-01',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('equipment_standards')->insert($standards);
    }
}
