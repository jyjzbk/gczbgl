<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentCatalog;
use App\Models\Subject;

class PrimaryGradeOneVolume2ExperimentCatalogsSeeder extends Seeder
{
    /**
     * 创建教科版小学科学一年级下册实验目录数据
     * 根据图片内容生成完整的实验目录
     */
    public function run(): void
    {
        // 获取小学科学学科
        $subject = Subject::where('name', 'LIKE', '%科学%')->first();

        if (!$subject) {
            // 如果没有找到科学学科，尝试查找其他可能的学科
            $subject = Subject::where('code', 'PRIMARY_SCIENCE')->first();
            if (!$subject) {
                $this->command->error('未找到小学科学学科，请先运行 SubjectSeeder');
                return;
            }
        }

        $this->command->info('找到学科: ' . $subject->name . ' (ID: ' . $subject->id . ')');

        // 清理已有的一年级下册实验数据
        ExperimentCatalog::where([
            'subject_id' => $subject->id,
            'grade' => 1,
            'semester' => 2
        ])->delete();

        // 实验目录数据（根据图片内容）
        $experiments = [
            // 第一单元：我们周围的物体
            [
                'sequence' => 1,
                'lesson' => '1-1',
                'page' => 3,
                'name' => '观察物体',
                'materials' => '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 2,
                'lesson' => '1-2',
                'page' => 6,
                'name' => '探轻重排序',
                'materials' => '乒乓球、木块、塑料块、小橡皮、大橡皮、天平、回形针',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 3,
                'lesson' => '1-3',
                'page' => 9,
                'name' => '平描物体',
                'materials' => '乒乓球、木块、橡皮、蜡笔、方盒子',
                'type' => '演示',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 4,
                'lesson' => '1-4',
                'page' => 11,
                'name' => '给物体分类',
                'materials' => '玻璃球、蜡笔、乒乓球、橡皮、泡沫块、纸片等',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 5,
                'lesson' => '1-5',
                'page' => 14,
                'name' => '观察水和洗发液',
                'materials' => '水、洗发液',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 6,
                'lesson' => '1-6',
                'page' => 17,
                'name' => '溶解实验',
                'materials' => '水、红糖、食盐、小石子、小木根、小勺、烧杯',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            [
                'sequence' => 7,
                'lesson' => '1-7',
                'page' => 20,
                'name' => '观察空气',
                'materials' => '空气、水、木块、塑料袋',
                'type' => '分组',
                'chapter' => '我们周围的物体'
            ],
            // 第二单元：动物
            [
                'sequence' => 8,
                'lesson' => '2-1',
                'page' => 25,
                'name' => '观察动物',
                'materials' => '动物图片或标本等',
                'type' => '演示',
                'chapter' => '动物'
            ],
            [
                'sequence' => 9,
                'lesson' => '2-2',
                'page' => 27,
                'name' => '寻找小动物',
                'materials' => '放大镜、镊子',
                'type' => '分组',
                'chapter' => '动物'
            ],
            [
                'sequence' => 10,
                'lesson' => '2-3',
                'page' => 33,
                'name' => '观察蜗牛',
                'materials' => '蜗牛、放大镜、玻璃',
                'type' => '分组',
                'chapter' => '动物'
            ],
            [
                'sequence' => 11,
                'lesson' => '2-4',
                'page' => 37,
                'name' => '饲养蜗牛',
                'materials' => '蜗牛、饲养箱、菜叶',
                'type' => '演示',
                'chapter' => '动物'
            ],
            [
                'sequence' => 12,
                'lesson' => '2-5',
                'page' => 38,
                'name' => '观察鱼',
                'materials' => '鱼缸、鱼、图片',
                'type' => '演示',
                'chapter' => '动物'
            ],
            [
                'sequence' => 13,
                'lesson' => '2-6',
                'page' => 40,
                'name' => '给动物分类',
                'materials' => '动物卡片',
                'type' => '分组',
                'chapter' => '动物'
            ]
        ];

        // 插入实验目录数据
        foreach ($experiments as $expData) {
            // 确定实验类型
            $type = match($expData['type']) {
                '分组' => 4, // TYPE_GROUP
                '演示' => 3, // TYPE_DEMO
                default => 1 // TYPE_REQUIRED
            };

            ExperimentCatalog::create([
                'subject_id' => $subject->id,
                'name' => $expData['name'],
                'code' => sprintf('JKB-1X-%02d', $expData['sequence']), // 教科版-一年级下册-序号
                'type' => $type,
                'grade' => 1,
                'semester' => 2, // 下学期
                'chapter' => $expData['chapter'],
                'duration' => 45,
                'student_count' => $expData['type'] === '分组' ? 4 : 1,
                'objective' => "通过{$expData['name']}实验，培养学生的观察能力和科学探究精神，了解{$expData['chapter']}相关知识。",
                'materials' => $expData['materials'],
                'procedure' => "1. 准备实验器材：{$expData['materials']}\n2. 按照教材第{$expData['page']}页的步骤进行实验\n3. 仔细观察并记录实验现象\n4. 讨论实验结果\n5. 整理实验器材",
                'safety_notes' => '注意实验安全，小心使用实验器材，避免误食或误伤。教师需全程指导。',
                'difficulty_level' => 1,
                'is_standard' => 1,
                'status' => 1
            ]);
        }

        $this->command->info('✅ 教科版小学科学一年级下册实验目录数据创建完成！');
        $this->command->info('📊 共创建 ' . count($experiments) . ' 个实验目录');
        $this->command->info('📚 包含两个单元：我们周围的物体、动物');
        $this->command->info('🔬 实验类型：分组实验、演示实验');
    }
}
