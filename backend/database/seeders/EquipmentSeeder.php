<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\School;
use App\Models\Laboratory;
use App\Models\User;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取基础数据
        $schools = School::all();
        $laboratories = Laboratory::all();
        $categories = EquipmentCategory::where('level', 3)->get(); // 只使用三级分类
        $users = User::all();

        if ($schools->isEmpty() || $categories->isEmpty()) {
            $this->command->info('缺少基础数据，跳过设备数据填充');
            return;
        }

        $equipments = [
            // 显微镜类设备
            [
                'category_code' => 'BIO_MICROSCOPE',
                'equipments' => [
                    [
                        'name' => '生物显微镜XSP-2CA',
                        'model' => 'XSP-2CA',
                        'brand' => '奥林巴斯',
                        'supplier' => '北京科学仪器公司',
                        'supplier_phone' => '010-12345678',
                        'purchase_price' => 1500.00,
                        'quantity' => 30,
                        'unit' => '台',
                        'warranty_period' => 24,
                        'service_life' => 10,
                        'funding_source' => '教育专项资金'
                    ],
                    [
                        'name' => '学生用生物显微镜',
                        'model' => 'XSP-1C',
                        'brand' => '上海光学',
                        'supplier' => '上海教学设备公司',
                        'supplier_phone' => '021-87654321',
                        'purchase_price' => 800.00,
                        'quantity' => 50,
                        'unit' => '台',
                        'warranty_period' => 12,
                        'service_life' => 8,
                        'funding_source' => '学校自筹'
                    ]
                ]
            ],
            // 天平类设备
            [
                'category_code' => 'BALANCE',
                'equipments' => [
                    [
                        'name' => '电子天平',
                        'model' => 'FA2004',
                        'brand' => '上海精科',
                        'supplier' => '上海精密仪器公司',
                        'supplier_phone' => '021-11111111',
                        'purchase_price' => 3200.00,
                        'quantity' => 5,
                        'unit' => '台',
                        'warranty_period' => 36,
                        'service_life' => 15,
                        'funding_source' => '实验室建设专项'
                    ],
                    [
                        'name' => '分析天平',
                        'model' => 'FA1004',
                        'brand' => '梅特勒',
                        'supplier' => '北京梅特勒代理商',
                        'supplier_phone' => '010-22222222',
                        'purchase_price' => 8500.00,
                        'quantity' => 2,
                        'unit' => '台',
                        'warranty_period' => 24,
                        'service_life' => 12,
                        'funding_source' => '教育部专项'
                    ]
                ]
            ],
            // 万用表类设备
            [
                'category_code' => 'MULTIMETER',
                'equipments' => [
                    [
                        'name' => '数字万用表',
                        'model' => 'UT33D',
                        'brand' => '优利德',
                        'supplier' => '深圳优利德公司',
                        'supplier_phone' => '0755-33333333',
                        'purchase_price' => 120.00,
                        'quantity' => 40,
                        'unit' => '台',
                        'warranty_period' => 12,
                        'service_life' => 5,
                        'funding_source' => '学校自筹'
                    ]
                ]
            ],
            // 烧杯类设备
            [
                'category_code' => 'BEAKER',
                'equipments' => [
                    [
                        'name' => '玻璃烧杯100ml',
                        'model' => 'GB-100',
                        'brand' => '舒博玻璃',
                        'supplier' => '江苏舒博实验器材公司',
                        'supplier_phone' => '0512-44444444',
                        'purchase_price' => 8.50,
                        'quantity' => 200,
                        'unit' => '个',
                        'warranty_period' => 6,
                        'service_life' => 3,
                        'funding_source' => '实验耗材专项'
                    ],
                    [
                        'name' => '玻璃烧杯250ml',
                        'model' => 'GB-250',
                        'brand' => '舒博玻璃',
                        'supplier' => '江苏舒博实验器材公司',
                        'supplier_phone' => '0512-44444444',
                        'purchase_price' => 12.00,
                        'quantity' => 150,
                        'unit' => '个',
                        'warranty_period' => 6,
                        'service_life' => 3,
                        'funding_source' => '实验耗材专项'
                    ]
                ]
            ],
            // 解剖工具
            [
                'category_code' => 'SCALPEL',
                'equipments' => [
                    [
                        'name' => '解剖刀套装',
                        'model' => 'JPS-01',
                        'brand' => '金鹏生物',
                        'supplier' => '北京金鹏生物器材公司',
                        'supplier_phone' => '010-55555555',
                        'purchase_price' => 25.00,
                        'quantity' => 60,
                        'unit' => '套',
                        'warranty_period' => 3,
                        'service_life' => 2,
                        'funding_source' => '生物实验专项'
                    ]
                ]
            ]
        ];

        foreach ($equipments as $categoryData) {
            $category = $categories->where('code', $categoryData['category_code'])->first();
            if (!$category) {
                continue;
            }

            foreach ($categoryData['equipments'] as $equipmentData) {
                // 为每个学校创建设备
                foreach ($schools->take(3) as $school) { // 只为前3个学校创建设备
                    // 查找该学校的实验室
                    $schoolLabs = $laboratories->where('school_id', $school->id);
                    $laboratory = $schoolLabs->first();
                    
                    // 随机选择一个管理员
                    $manager = $users->random();

                    $equipment = Equipment::create([
                        'school_id' => $school->id,
                        'laboratory_id' => $laboratory ? $laboratory->id : null,
                        'category_id' => $category->id,
                        'name' => $equipmentData['name'],
                        'code' => $this->generateEquipmentCode($category, $school),
                        'model' => $equipmentData['model'],
                        'brand' => $equipmentData['brand'],
                        'supplier' => $equipmentData['supplier'],
                        'supplier_phone' => $equipmentData['supplier_phone'],
                        'purchase_date' => now()->subDays(rand(30, 365)),
                        'purchase_price' => $equipmentData['purchase_price'],
                        'quantity' => $equipmentData['quantity'],
                        'unit' => $equipmentData['unit'],
                        'warranty_period' => $equipmentData['warranty_period'],
                        'service_life' => $equipmentData['service_life'],
                        'funding_source' => $equipmentData['funding_source'],
                        'storage_location' => $laboratory ? $laboratory->name : '设备室',
                        'manager_id' => $manager->id,
                        'status' => Equipment::STATUS_NORMAL,
                        'remark' => '系统初始化数据'
                    ]);

                    // 生成二维码
                    $equipment->generateQrCode();
                }
            }
        }

        $this->command->info('设备数据填充完成');
    }

    /**
     * 生成设备编号
     */
    private function generateEquipmentCode($category, $school): string
    {
        $prefix = strtoupper(substr($category->code, 0, 3));
        $schoolCode = str_pad($school->id, 3, '0', STR_PAD_LEFT);
        
        // 获取当前分类下的最大编号
        $lastEquipment = Equipment::where('category_id', $category->id)
                                 ->where('school_id', $school->id)
                                 ->where('code', 'like', "{$prefix}{$schoolCode}%")
                                 ->orderBy('code', 'desc')
                                 ->first();
        
        $sequence = 1;
        if ($lastEquipment && preg_match('/(\d+)$/', $lastEquipment->code, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }
        
        return $prefix . $schoolCode . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
