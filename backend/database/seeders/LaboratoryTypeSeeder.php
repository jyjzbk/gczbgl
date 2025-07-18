<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaboratoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => '物理实验室',
                'code' => 'PHYSICS',
                'description' => '用于物理实验教学的专用实验室',
                'icon' => 'Physics',
                'color' => '#409EFF',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '化学实验室',
                'code' => 'CHEMISTRY',
                'description' => '用于化学实验教学的专用实验室',
                'icon' => 'Chemistry',
                'color' => '#67C23A',
                'sort_order' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '生物实验室',
                'code' => 'BIOLOGY',
                'description' => '用于生物实验教学的专用实验室',
                'icon' => 'Biology',
                'color' => '#E6A23C',
                'sort_order' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '综合实验室',
                'code' => 'COMPREHENSIVE',
                'description' => '用于多学科综合实验教学的实验室',
                'icon' => 'Comprehensive',
                'color' => '#F56C6C',
                'sort_order' => 4,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '科学实验室',
                'code' => 'SCIENCE',
                'description' => '用于小学科学实验教学的实验室',
                'icon' => 'Science',
                'color' => '#909399',
                'sort_order' => 5,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '音乐实验室',
                'code' => 'MUSIC',
                'description' => '用于音乐教学和实践的专用教室',
                'icon' => 'Music',
                'color' => '#9C27B0',
                'sort_order' => 6,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '美术实验室',
                'code' => 'ART',
                'description' => '用于美术教学和创作的专用教室',
                'icon' => 'Art',
                'color' => '#FF5722',
                'sort_order' => 7,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '体育器材室',
                'code' => 'SPORTS',
                'description' => '用于体育器材存放和管理的专用场所',
                'icon' => 'Sports',
                'color' => '#4CAF50',
                'sort_order' => 8,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'STEAM实验室',
                'code' => 'STEAM',
                'description' => '用于科学、技术、工程、艺术、数学综合教育的实验室',
                'icon' => 'Steam',
                'color' => '#795548',
                'sort_order' => 9,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '创客实验室',
                'code' => 'MAKER',
                'description' => '用于创新制作和动手实践的实验室',
                'icon' => 'Maker',
                'color' => '#607D8B',
                'sort_order' => 10,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('laboratory_types')->insert($types);
    }
}
