<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentCatalog;
use App\Models\Subject;

class ExperimentCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 获取学科ID
        $physicsMiddle = Subject::where('code', 'MIDDLE_PHYSICS')->first();
        $chemistryMiddle = Subject::where('code', 'MIDDLE_CHEMISTRY')->first();
        $biologyMiddle = Subject::where('code', 'MIDDLE_BIOLOGY')->first();
        $physicsHigh = Subject::where('code', 'HIGH_PHYSICS')->first();

        if (!$physicsMiddle || !$chemistryMiddle || !$biologyMiddle || !$physicsHigh) {
            $this->command->error('请先运行 SubjectSeeder');
            return;
        }

        $catalogs = [
            // 初中物理实验
            [
                'subject_id' => $physicsMiddle->id,
                'name' => '测量物体的长度',
                'code' => 'MP_001',
                'type' => ExperimentCatalog::TYPE_GROUP,
                'grade' => 8,
                'semester' => ExperimentCatalog::SEMESTER_FIRST,
                'chapter' => '第一章 机械运动',
                'duration' => 45,
                'student_count' => 2,
                'objective' => '学会使用刻度尺测量物体长度，掌握测量的基本方法',
                'materials' => '刻度尺、铅笔、硬币、细线等',
                'procedure' => '1.观察刻度尺的结构\n2.学习正确的测量方法\n3.测量不同物体的长度\n4.记录测量结果',
                'safety_notes' => '使用刻度尺时要轻拿轻放，避免弯折',
                'difficulty_level' => 1,
                'is_standard' => 1,
                'status' => 1
            ],
            [
                'subject_id' => $physicsMiddle->id,
                'name' => '测量重力加速度',
                'code' => 'MP_002',
                'type' => ExperimentCatalog::TYPE_GROUP,
                'grade' => 9,
                'semester' => ExperimentCatalog::SEMESTER_FIRST,
                'chapter' => '第十三章 力和机械',
                'duration' => 90,
                'student_count' => 4,
                'objective' => '通过实验测量重力加速度的大小',
                'materials' => '单摆装置、秒表、刻度尺、小球等',
                'procedure' => '1.组装单摆装置\n2.测量摆长\n3.测量周期\n4.计算重力加速度',
                'safety_notes' => '注意摆球的安全，避免碰撞',
                'difficulty_level' => 3,
                'is_standard' => 1,
                'status' => 1
            ],

            // 初中化学实验
            [
                'subject_id' => $chemistryMiddle->id,
                'name' => '氧气的制取和性质',
                'code' => 'MC_001',
                'type' => ExperimentCatalog::TYPE_DEMO,
                'grade' => 9,
                'semester' => ExperimentCatalog::SEMESTER_FIRST,
                'chapter' => '第二单元 我们周围的空气',
                'duration' => 45,
                'student_count' => 1,
                'objective' => '学习氧气的制取方法，观察氧气的性质',
                'materials' => '高锰酸钾、试管、酒精灯、导管、集气瓶等',
                'procedure' => '1.装置连接\n2.加热制取氧气\n3.收集氧气\n4.验证氧气性质',
                'safety_notes' => '注意用火安全，避免烫伤；注意通风',
                'difficulty_level' => 2,
                'is_standard' => 1,
                'status' => 1
            ],

            // 初中生物实验
            [
                'subject_id' => $biologyMiddle->id,
                'name' => '观察植物细胞',
                'code' => 'MB_001',
                'type' => ExperimentCatalog::TYPE_GROUP,
                'grade' => 7,
                'semester' => ExperimentCatalog::SEMESTER_FIRST,
                'chapter' => '第二单元 生物体的结构层次',
                'duration' => 45,
                'student_count' => 2,
                'objective' => '学会制作临时装片，观察植物细胞的基本结构',
                'materials' => '显微镜、载玻片、盖玻片、洋葱、碘液等',
                'procedure' => '1.制作洋葱表皮临时装片\n2.显微镜观察\n3.绘制细胞结构图\n4.总结细胞特点',
                'safety_notes' => '小心使用显微镜，避免损坏镜头',
                'difficulty_level' => 2,
                'is_standard' => 1,
                'status' => 1
            ],

            // 高中物理实验
            [
                'subject_id' => $physicsHigh->id,
                'name' => '验证牛顿第二定律',
                'code' => 'HP_001',
                'type' => ExperimentCatalog::TYPE_GROUP,
                'grade' => 10,
                'semester' => ExperimentCatalog::SEMESTER_SECOND,
                'chapter' => '第四章 牛顿运动定律',
                'duration' => 90,
                'student_count' => 4,
                'objective' => '通过实验验证牛顿第二定律F=ma',
                'materials' => '气垫导轨、滑块、砝码、光电门、计时器等',
                'procedure' => '1.调节气垫导轨水平\n2.测量不同力下的加速度\n3.测量不同质量下的加速度\n4.分析数据验证定律',
                'safety_notes' => '注意气垫导轨的使用，避免损坏设备',
                'difficulty_level' => 4,
                'is_standard' => 1,
                'status' => 1
            ]
        ];

        foreach ($catalogs as $catalog) {
            ExperimentCatalog::create($catalog);
        }
    }
}
