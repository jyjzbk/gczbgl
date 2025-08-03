<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TextbookVersionAssignment;
use App\Models\TextbookAssignmentTemplate;
use App\Models\School;
use App\Models\Subject;
use App\Models\TextbookVersion;
use App\Models\User;
use Carbon\Carbon;

class TextbookVersionAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建教材版本指定模板
        $this->createTemplates();
        
        // 创建教材版本指定记录
        $this->createAssignments();
    }

    /**
     * 创建教材版本指定模板
     */
    private function createTemplates(): void
    {
        $subjects = Subject::all();
        $textbookVersions = TextbookVersion::all();
        $adminUser = User::where('organization_level', 1)->first(); // 省级管理员

        if (!$adminUser || $subjects->isEmpty() || $textbookVersions->isEmpty()) {
            $this->command->warn('缺少必要的基础数据，跳过模板创建');
            return;
        }

        // 小学科学教材版本指定模板
        $scienceSubject = $subjects->where('name', '科学')->first();
        $scienceVersion = $textbookVersions->where('name', 'like', '%教科版%')->first() 
                         ?? $textbookVersions->first();

        if ($scienceSubject && $scienceVersion) {
            TextbookAssignmentTemplate::create([
                'name' => '小学科学标准配置',
                'description' => '适用于小学1-6年级科学学科的标准教材版本配置',
                'creator_level' => 1,
                'creator_org_id' => $adminUser->organization_id,
                'creator_org_type' => $adminUser->organization_type,
                'creator_user_id' => $adminUser->id,
                'assignment_config' => [
                    $scienceSubject->id => $scienceVersion->id
                ],
                'applicable_grades' => ['1', '2', '3', '4', '5', '6'],
                'applicable_school_types' => [1], // 小学
                'status' => 1,
                'is_default' => 1
            ]);
        }

        // 初中理科教材版本指定模板
        $physicsSubject = $subjects->where('name', '物理')->first();
        $chemistrySubject = $subjects->where('name', '化学')->first();
        $biologySubject = $subjects->where('name', '生物')->first();

        if ($physicsSubject || $chemistrySubject || $biologySubject) {
            $config = [];
            
            if ($physicsSubject) {
                $physicsVersion = $textbookVersions->where('name', 'like', '%人教版%')->first() 
                                 ?? $textbookVersions->first();
                $config[$physicsSubject->id] = $physicsVersion->id;
            }
            
            if ($chemistrySubject) {
                $chemistryVersion = $textbookVersions->where('name', 'like', '%人教版%')->first() 
                                   ?? $textbookVersions->first();
                $config[$chemistrySubject->id] = $chemistryVersion->id;
            }
            
            if ($biologySubject) {
                $biologyVersion = $textbookVersions->where('name', 'like', '%人教版%')->first() 
                                 ?? $textbookVersions->first();
                $config[$biologySubject->id] = $biologyVersion->id;
            }

            if (!empty($config)) {
                TextbookAssignmentTemplate::create([
                    'name' => '初中理科标准配置',
                    'description' => '适用于初中7-9年级理科学科的标准教材版本配置',
                    'creator_level' => 1,
                    'creator_org_id' => $adminUser->organization_id,
                    'creator_org_type' => $adminUser->organization_type,
                    'creator_user_id' => $adminUser->id,
                    'assignment_config' => $config,
                    'applicable_grades' => ['7', '8', '9'],
                    'applicable_school_types' => [2, 4], // 初中、九年一贯制
                    'status' => 1,
                    'is_default' => 0
                ]);
            }
        }
    }

    /**
     * 创建教材版本指定记录
     */
    private function createAssignments(): void
    {
        $schools = School::limit(10)->get(); // 限制数量避免数据过多
        $subjects = Subject::all();
        $textbookVersions = TextbookVersion::all();
        $adminUsers = User::whereIn('organization_level', [1, 2, 3])->get();

        if ($schools->isEmpty() || $subjects->isEmpty() || $textbookVersions->isEmpty() || $adminUsers->isEmpty()) {
            $this->command->warn('缺少必要的基础数据，跳过指定记录创建');
            return;
        }

        $grades = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $assignmentReasons = [
            '统一教材版本，确保教学质量',
            '根据学校实际情况指定',
            '配合实验设备标准化',
            '提高教学效果',
            '规范实验教学管理'
        ];

        foreach ($schools as $school) {
            // 为每个学校随机指定一些教材版本
            $assignmentCount = rand(3, 8); // 每个学校3-8个指定
            $usedCombinations = [];

            for ($i = 0; $i < $assignmentCount; $i++) {
                $subject = $subjects->random();
                $grade = $grades[array_rand($grades)];
                $textbookVersion = $textbookVersions->random();
                $adminUser = $adminUsers->random();

                // 避免重复的学校-学科-年级组合
                $combination = "{$school->id}-{$subject->id}-{$grade}";
                if (in_array($combination, $usedCombinations)) {
                    continue;
                }
                $usedCombinations[] = $combination;

                // 检查管理权限（简化版本）
                $canAssign = true;
                if ($adminUser->organization_level == 2 && $school->management_level != 2) {
                    $canAssign = false;
                }
                if ($adminUser->organization_level == 3 && !in_array($school->management_level, [3, 4, 5])) {
                    $canAssign = false;
                }

                if (!$canAssign) {
                    continue;
                }

                TextbookVersionAssignment::create([
                    'assigner_level' => $adminUser->organization_level,
                    'assigner_org_id' => $adminUser->organization_id,
                    'assigner_org_type' => $adminUser->organization_type,
                    'assigner_user_id' => $adminUser->id,
                    'school_id' => $school->id,
                    'subject_id' => $subject->id,
                    'grade_level' => $grade,
                    'textbook_version_id' => $textbookVersion->id,
                    'status' => 1,
                    'assignment_reason' => $assignmentReasons[array_rand($assignmentReasons)],
                    'effective_date' => Carbon::now()->subDays(rand(1, 30)),
                    'expiry_date' => rand(0, 1) ? Carbon::now()->addMonths(rand(6, 24)) : null
                ]);
            }
        }

        $this->command->info('教材版本指定数据创建完成');
    }
}
