<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // 小学科学
            [
                'name' => '小学科学',
                'code' => 'PRIMARY_SCIENCE',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_PRIMARY,
                'sort_order' => 1,
                'status' => Subject::STATUS_ACTIVE
            ],
            
            // 初中理科
            [
                'name' => '初中物理',
                'code' => 'MIDDLE_PHYSICS',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_MIDDLE,
                'sort_order' => 2,
                'status' => Subject::STATUS_ACTIVE
            ],
            [
                'name' => '初中化学',
                'code' => 'MIDDLE_CHEMISTRY',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_MIDDLE,
                'sort_order' => 3,
                'status' => Subject::STATUS_ACTIVE
            ],
            [
                'name' => '初中生物',
                'code' => 'MIDDLE_BIOLOGY',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_MIDDLE,
                'sort_order' => 4,
                'status' => Subject::STATUS_ACTIVE
            ],
            
            // 高中理科
            [
                'name' => '高中物理',
                'code' => 'HIGH_PHYSICS',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_HIGH,
                'sort_order' => 5,
                'status' => Subject::STATUS_ACTIVE
            ],
            [
                'name' => '高中化学',
                'code' => 'HIGH_CHEMISTRY',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_HIGH,
                'sort_order' => 6,
                'status' => Subject::STATUS_ACTIVE
            ],
            [
                'name' => '高中生物',
                'code' => 'HIGH_BIOLOGY',
                'type' => Subject::TYPE_SCIENCE,
                'stage' => Subject::STAGE_HIGH,
                'sort_order' => 7,
                'status' => Subject::STATUS_ACTIVE
            ],
            
            // 综合学科
            [
                'name' => '通用技术',
                'code' => 'GENERAL_TECH',
                'type' => Subject::TYPE_COMPREHENSIVE,
                'stage' => Subject::STAGE_HIGH,
                'sort_order' => 8,
                'status' => Subject::STATUS_ACTIVE
            ],
            [
                'name' => '信息技术',
                'code' => 'INFO_TECH',
                'type' => Subject::TYPE_COMPREHENSIVE,
                'stage' => Subject::STAGE_HIGH,
                'sort_order' => 9,
                'status' => Subject::STATUS_ACTIVE
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
