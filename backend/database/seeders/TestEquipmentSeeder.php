<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Laboratory;
use App\Models\School;

class TestEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取学校
        $schools = School::whereIn('code', ['HB001', 'SJZ001', 'GC001', 'LZ001'])->get();
        
        if ($schools->isEmpty()) {
            $this->command->info('No schools found. Please run OrganizationPermissionSeeder first.');
            return;
        }

        // 创建设备分类
        $categories = [
            ['name' => '物理实验设备', 'code' => 'PHYSICS', 'level' => 1, 'sort_order' => 1],
            ['name' => '化学实验设备', 'code' => 'CHEMISTRY', 'level' => 1, 'sort_order' => 2],
            ['name' => '生物实验设备', 'code' => 'BIOLOGY', 'level' => 1, 'sort_order' => 3],
            ['name' => '通用设备', 'code' => 'GENERAL', 'level' => 1, 'sort_order' => 4],
        ];

        foreach ($categories as $categoryData) {
            EquipmentCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }

        $physicsCategory = EquipmentCategory::where('code', 'PHYSICS')->first();
        $chemistryCategory = EquipmentCategory::where('code', 'CHEMISTRY')->first();
        $generalCategory = EquipmentCategory::where('code', 'GENERAL')->first();

        // 为每个学校创建实验室和设备
        foreach ($schools as $school) {
            // 创建实验室
            $laboratory = Laboratory::firstOrCreate(
                ['school_id' => $school->id, 'code' => 'LAB001'],
                [
                    'name' => $school->name . '实验室1',
                    'type' => 1, // 物理实验室
                    'location' => '教学楼3楼',
                    'area' => 120.00,
                    'capacity' => 50,
                    'status' => 1
                ]
            );

            // 创建设备
            $equipments = [
                [
                    'name' => '数字万用表',
                    'code' => $school->code . '_DMM001',
                    'model' => 'DT9205A',
                    'brand' => '优利德',
                    'category_id' => $physicsCategory->id,
                    'purchase_price' => 150.00,
                    'quantity' => 10,
                ],
                [
                    'name' => '示波器',
                    'code' => $school->code . '_OSC001',
                    'model' => 'DS1054Z',
                    'brand' => '普源精电',
                    'category_id' => $physicsCategory->id,
                    'purchase_price' => 2500.00,
                    'quantity' => 2,
                ],
                [
                    'name' => '电子天平',
                    'code' => $school->code . '_BAL001',
                    'model' => 'FA2004',
                    'brand' => '上海精科',
                    'category_id' => $chemistryCategory->id,
                    'purchase_price' => 3200.00,
                    'quantity' => 1,
                ],
                [
                    'name' => '投影仪',
                    'code' => $school->code . '_PRJ001',
                    'model' => 'EB-C760X',
                    'brand' => '爱普生',
                    'category_id' => $generalCategory->id,
                    'purchase_price' => 4500.00,
                    'quantity' => 1,
                ],
                [
                    'name' => '计算机',
                    'code' => $school->code . '_PC001',
                    'model' => 'OptiPlex 7090',
                    'brand' => '戴尔',
                    'category_id' => $generalCategory->id,
                    'purchase_price' => 5500.00,
                    'quantity' => 5,
                ]
            ];

            foreach ($equipments as $equipmentData) {
                Equipment::firstOrCreate(
                    ['code' => $equipmentData['code']],
                    array_merge($equipmentData, [
                        'school_id' => $school->id,
                        'laboratory_id' => $laboratory->id,
                        'supplier' => '测试供应商',
                        'purchase_date' => now()->subMonths(rand(1, 12)),
                        'warranty_period' => 36,
                        'storage_location' => $laboratory->name,
                        'status' => 1,
                        'remark' => '测试设备，用于验证权限控制功能',
                        'qr_code' => 'QR_' . $equipmentData['code']
                    ])
                );
            }

            $this->command->info("Created equipment for school: {$school->name}");
        }

        $this->command->info('Test equipment data created successfully!');
    }
}
