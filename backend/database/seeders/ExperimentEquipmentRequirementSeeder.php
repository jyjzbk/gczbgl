<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentCatalog;
use App\Models\Equipment;
use App\Models\ExperimentEquipmentRequirement;

class ExperimentEquipmentRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建实验器材需求配置...');

        // 获取实验目录和器材
        $catalogs = ExperimentCatalog::all();
        $equipments = Equipment::all();

        if ($catalogs->isEmpty() || $equipments->isEmpty()) {
            $this->command->error('缺少实验目录或器材数据，请先运行相关种子文件');
            return;
        }

        $requirementCount = 0;

        // 为每个实验目录配置器材需求
        foreach ($catalogs as $catalog) {
            $this->command->info("配置实验: {$catalog->name}");
            
            // 根据实验类型和学科配置不同的器材
            $requirements = $this->getRequirementsForCatalog($catalog, $equipments);
            
            foreach ($requirements as $requirement) {
                ExperimentEquipmentRequirement::create([
                    'catalog_id' => $catalog->id,
                    'equipment_id' => $requirement['equipment_id'],
                    'required_quantity' => $requirement['required_quantity'],
                    'min_quantity' => $requirement['min_quantity'],
                    'is_required' => $requirement['is_required'],
                    'calculation_type' => $requirement['calculation_type'],
                    'group_size' => $requirement['group_size'] ?? null,
                    'usage_note' => $requirement['usage_note'] ?? null,
                    'safety_note' => $requirement['safety_note'] ?? null,
                    'sort_order' => $requirement['sort_order'] ?? 0,
                    'is_active' => true,
                    'created_by' => 1 // 假设管理员ID为1
                ]);
                
                $requirementCount++;
            }
        }

        $this->command->info("器材需求配置创建完成！共创建 {$requirementCount} 条配置记录");
    }

    /**
     * 根据实验目录获取器材需求配置
     */
    private function getRequirementsForCatalog($catalog, $equipments)
    {
        $requirements = [];

        // 为每个实验配置不同的器材，确保有区别
        $catalogId = $catalog->id;

        // 按器材名称分组，避免选择同名器材
        $equipmentsByName = $equipments->groupBy('name');
        $uniqueEquipmentNames = $equipmentsByName->keys()->toArray();
        $nameCount = count($uniqueEquipmentNames);

        if ($nameCount == 0) {
            return $requirements;
        }

        // 为每个实验目录分配2-3个不同名称的器材
        $startIndex = ($catalogId - 1) * 2 % $nameCount;
        $selectedEquipmentNames = [];

        // 选择2-3个不同名称的器材
        for ($i = 0; $i < min(3, $nameCount); $i++) {
            $nameIndex = ($startIndex + $i) % $nameCount;
            $selectedEquipmentNames[] = $uniqueEquipmentNames[$nameIndex];
        }

        foreach ($selectedEquipmentNames as $index => $equipmentName) {
            // 从同名器材中选择第一个
            $equipment = $equipmentsByName[$equipmentName]->first();

            $requirements[] = [
                'equipment_id' => $equipment->id,
                'required_quantity' => rand(1, 3),
                'min_quantity' => 1,
                'is_required' => $index === 0, // 第一个设为必需
                'calculation_type' => ['fixed', 'per_group', 'per_student'][rand(0, 2)],
                'group_size' => rand(2, 6),
                'usage_note' => '实验用器材 - ' . $equipment->name,
                'safety_note' => '注意安全使用',
                'sort_order' => $index + 1
            ];
        }

        return $requirements;
    }
}
