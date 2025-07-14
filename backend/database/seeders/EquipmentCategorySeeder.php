<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一级分类
        $categories = [
            [
                'name' => '光学仪器',
                'code' => 'OPTICAL',
                'parent_id' => null,
                'level' => 1,
                'sort_order' => 1,
                'status' => 1,
                'children' => [
                    [
                        'name' => '显微镜',
                        'code' => 'MICROSCOPE',
                        'level' => 2,
                        'sort_order' => 1,
                        'children' => [
                            ['name' => '生物显微镜', 'code' => 'BIO_MICROSCOPE', 'level' => 3, 'sort_order' => 1],
                            ['name' => '体视显微镜', 'code' => 'STEREO_MICROSCOPE', 'level' => 3, 'sort_order' => 2],
                            ['name' => '电子显微镜', 'code' => 'ELECTRON_MICROSCOPE', 'level' => 3, 'sort_order' => 3],
                        ]
                    ],
                    [
                        'name' => '望远镜',
                        'code' => 'TELESCOPE',
                        'level' => 2,
                        'sort_order' => 2,
                        'children' => [
                            ['name' => '天文望远镜', 'code' => 'ASTRO_TELESCOPE', 'level' => 3, 'sort_order' => 1],
                            ['name' => '地理望远镜', 'code' => 'GEO_TELESCOPE', 'level' => 3, 'sort_order' => 2],
                        ]
                    ],
                    [
                        'name' => '放大镜',
                        'code' => 'MAGNIFIER',
                        'level' => 2,
                        'sort_order' => 3,
                        'children' => [
                            ['name' => '手持放大镜', 'code' => 'HAND_MAGNIFIER', 'level' => 3, 'sort_order' => 1],
                            ['name' => '台式放大镜', 'code' => 'DESK_MAGNIFIER', 'level' => 3, 'sort_order' => 2],
                        ]
                    ]
                ]
            ],
            [
                'name' => '测量仪器',
                'code' => 'MEASUREMENT',
                'parent_id' => null,
                'level' => 1,
                'sort_order' => 2,
                'status' => 1,
                'children' => [
                    [
                        'name' => '长度测量',
                        'code' => 'LENGTH_MEASURE',
                        'level' => 2,
                        'sort_order' => 1,
                        'children' => [
                            ['name' => '直尺', 'code' => 'RULER', 'level' => 3, 'sort_order' => 1],
                            ['name' => '卷尺', 'code' => 'TAPE_MEASURE', 'level' => 3, 'sort_order' => 2],
                            ['name' => '游标卡尺', 'code' => 'VERNIER_CALIPER', 'level' => 3, 'sort_order' => 3],
                            ['name' => '螺旋测微器', 'code' => 'MICROMETER', 'level' => 3, 'sort_order' => 4],
                        ]
                    ],
                    [
                        'name' => '质量测量',
                        'code' => 'MASS_MEASURE',
                        'level' => 2,
                        'sort_order' => 2,
                        'children' => [
                            ['name' => '天平', 'code' => 'BALANCE', 'level' => 3, 'sort_order' => 1],
                            ['name' => '电子秤', 'code' => 'ELECTRONIC_SCALE', 'level' => 3, 'sort_order' => 2],
                            ['name' => '弹簧秤', 'code' => 'SPRING_SCALE', 'level' => 3, 'sort_order' => 3],
                        ]
                    ],
                    [
                        'name' => '时间测量',
                        'code' => 'TIME_MEASURE',
                        'level' => 2,
                        'sort_order' => 3,
                        'children' => [
                            ['name' => '秒表', 'code' => 'STOPWATCH', 'level' => 3, 'sort_order' => 1],
                            ['name' => '计时器', 'code' => 'TIMER', 'level' => 3, 'sort_order' => 2],
                        ]
                    ]
                ]
            ],
            [
                'name' => '电学仪器',
                'code' => 'ELECTRICAL',
                'parent_id' => null,
                'level' => 1,
                'sort_order' => 3,
                'status' => 1,
                'children' => [
                    [
                        'name' => '电源设备',
                        'code' => 'POWER_SUPPLY',
                        'level' => 2,
                        'sort_order' => 1,
                        'children' => [
                            ['name' => '直流电源', 'code' => 'DC_POWER', 'level' => 3, 'sort_order' => 1],
                            ['name' => '交流电源', 'code' => 'AC_POWER', 'level' => 3, 'sort_order' => 2],
                            ['name' => '稳压电源', 'code' => 'REGULATED_POWER', 'level' => 3, 'sort_order' => 3],
                        ]
                    ],
                    [
                        'name' => '测量仪表',
                        'code' => 'ELECTRICAL_METER',
                        'level' => 2,
                        'sort_order' => 2,
                        'children' => [
                            ['name' => '万用表', 'code' => 'MULTIMETER', 'level' => 3, 'sort_order' => 1],
                            ['name' => '电压表', 'code' => 'VOLTMETER', 'level' => 3, 'sort_order' => 2],
                            ['name' => '电流表', 'code' => 'AMMETER', 'level' => 3, 'sort_order' => 3],
                            ['name' => '示波器', 'code' => 'OSCILLOSCOPE', 'level' => 3, 'sort_order' => 4],
                        ]
                    ]
                ]
            ],
            [
                'name' => '化学仪器',
                'code' => 'CHEMICAL',
                'parent_id' => null,
                'level' => 1,
                'sort_order' => 4,
                'status' => 1,
                'children' => [
                    [
                        'name' => '玻璃仪器',
                        'code' => 'GLASSWARE',
                        'level' => 2,
                        'sort_order' => 1,
                        'children' => [
                            ['name' => '烧杯', 'code' => 'BEAKER', 'level' => 3, 'sort_order' => 1],
                            ['name' => '试管', 'code' => 'TEST_TUBE', 'level' => 3, 'sort_order' => 2],
                            ['name' => '量筒', 'code' => 'GRADUATED_CYLINDER', 'level' => 3, 'sort_order' => 3],
                            ['name' => '容量瓶', 'code' => 'VOLUMETRIC_FLASK', 'level' => 3, 'sort_order' => 4],
                        ]
                    ],
                    [
                        'name' => '加热设备',
                        'code' => 'HEATING_EQUIPMENT',
                        'level' => 2,
                        'sort_order' => 2,
                        'children' => [
                            ['name' => '酒精灯', 'code' => 'ALCOHOL_LAMP', 'level' => 3, 'sort_order' => 1],
                            ['name' => '电热板', 'code' => 'HOT_PLATE', 'level' => 3, 'sort_order' => 2],
                            ['name' => '马弗炉', 'code' => 'MUFFLE_FURNACE', 'level' => 3, 'sort_order' => 3],
                        ]
                    ]
                ]
            ],
            [
                'name' => '生物仪器',
                'code' => 'BIOLOGICAL',
                'parent_id' => null,
                'level' => 1,
                'sort_order' => 5,
                'status' => 1,
                'children' => [
                    [
                        'name' => '解剖工具',
                        'code' => 'DISSECTION_TOOLS',
                        'level' => 2,
                        'sort_order' => 1,
                        'children' => [
                            ['name' => '解剖刀', 'code' => 'SCALPEL', 'level' => 3, 'sort_order' => 1],
                            ['name' => '解剖剪', 'code' => 'DISSECTION_SCISSORS', 'level' => 3, 'sort_order' => 2],
                            ['name' => '镊子', 'code' => 'FORCEPS', 'level' => 3, 'sort_order' => 3],
                        ]
                    ],
                    [
                        'name' => '培养设备',
                        'code' => 'CULTURE_EQUIPMENT',
                        'level' => 2,
                        'sort_order' => 2,
                        'children' => [
                            ['name' => '培养皿', 'code' => 'PETRI_DISH', 'level' => 3, 'sort_order' => 1],
                            ['name' => '培养箱', 'code' => 'INCUBATOR', 'level' => 3, 'sort_order' => 2],
                            ['name' => '接种环', 'code' => 'INOCULATION_LOOP', 'level' => 3, 'sort_order' => 3],
                        ]
                    ]
                ]
            ]
        ];

        $this->createCategories($categories);
    }

    /**
     * 递归创建分类
     */
    private function createCategories($categories, $parentId = null)
    {
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $categoryData['parent_id'] = $parentId;
            $categoryData['status'] = $categoryData['status'] ?? 1;
            
            $category = EquipmentCategory::create($categoryData);
            
            if (!empty($children)) {
                $this->createCategories($children, $category->id);
            }
        }
    }
}
