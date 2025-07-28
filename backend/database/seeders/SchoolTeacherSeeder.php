<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\User;

class SchoolTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建学校教师数据...');

        // 获取前5个学校
        $schools = School::take(5)->get();

        // 学科列表
        $subjects = [
            '语文', '数学', '英语', '物理', '化学', '生物',
            '历史', '地理', '政治', '音乐', '美术', '体育',
            '信息技术', '科学', '道德与法治'
        ];

        // 职称列表
        $titles = ['助教', '讲师', '副教授', '教授', '特级教师', '高级教师', '中级教师', '初级教师'];

        // 学历列表
        $educations = ['本科', '硕士', '博士', '专科'];

        foreach ($schools as $school) {
            $this->command->info("为学校 {$school->name} 创建教师...");

            // 获取该学校相关的用户（这里我们创建一些示例用户）
            $this->createTeacherUsers($school, $subjects, $titles, $educations);
        }

        $totalTeachers = SchoolTeacher::count();
        $this->command->info("教师数据创建完成，共创建 {$totalTeachers} 个教师记录");
    }

    /**
     * 为学校创建教师用户
     */
    private function createTeacherUsers(School $school, array $subjects, array $titles, array $educations): void
    {
        // 每个学校创建8-12个教师
        $teacherCount = rand(8, 12);

        for ($i = 1; $i <= $teacherCount; $i++) {
            // 创建用户
            $username = "teacher_{$school->id}_{$i}";
            
            // 检查用户是否已存在
            $user = User::where('username', $username)->first();
            
            if (!$user) {
                $user = User::create([
                    'username' => $username,
                    'email' => "teacher_{$school->id}_{$i}@{$school->code}.edu.cn",
                    'password' => bcrypt('password'),
                    'real_name' => $this->generateTeacherName(),
                    'phone' => $this->generatePhone(),
                    'status' => User::STATUS_ACTIVE,
                    'role' => 'teacher',
                    'school_id' => $school->id,
                    'organization_type' => 'school',
                    'organization_id' => $school->id,
                    'organization_level' => 5 // 学校级别
                ]);
            }

            // 检查是否已经是该学校的教师
            if (SchoolTeacher::where('school_id', $school->id)->where('user_id', $user->id)->exists()) {
                continue;
            }

            // 随机选择学科
            $subject = $subjects[array_rand($subjects)];
            
            // 根据学校类型确定任教年级
            $teachingGrades = $this->getTeachingGrades($school->type);

            SchoolTeacher::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'employee_number' => $this->generateEmployeeNumber($school->id, $i),
                'subject' => $subject,
                'teaching_grades' => $teachingGrades,
                'title' => $titles[array_rand($titles)],
                'education' => $educations[array_rand($educations)],
                'join_date' => now()->subDays(rand(30, 1000)),
                'status' => SchoolTeacher::STATUS_ACTIVE
            ]);

            $this->command->info("  创建教师: {$user->real_name} - {$subject}");
        }
    }

    /**
     * 生成教师姓名
     */
    private function generateTeacherName(): string
    {
        $surnames = ['王', '李', '张', '刘', '陈', '杨', '赵', '黄', '周', '吴', '徐', '孙', '胡', '朱', '高', '林', '何', '郭', '马', '罗'];
        $names = ['伟', '芳', '娜', '秀英', '敏', '静', '丽', '强', '磊', '军', '洋', '勇', '艳', '杰', '娟', '涛', '明', '超', '秀兰', '霞'];
        
        $surname = $surnames[array_rand($surnames)];
        $name = $names[array_rand($names)];
        
        return $surname . $name;
    }

    /**
     * 生成手机号
     */
    private function generatePhone(): string
    {
        $prefixes = ['130', '131', '132', '133', '134', '135', '136', '137', '138', '139', '150', '151', '152', '153', '155', '156', '157', '158', '159', '180', '181', '182', '183', '184', '185', '186', '187', '188', '189'];
        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        
        return $prefix . $suffix;
    }

    /**
     * 生成工号
     */
    private function generateEmployeeNumber(int $schoolId, int $index): string
    {
        return 'T' . str_pad($schoolId, 3, '0', STR_PAD_LEFT) . str_pad($index, 3, '0', STR_PAD_LEFT);
    }

    /**
     * 根据学校类型获取任教年级
     */
    private function getTeachingGrades(int $type): array
    {
        switch ($type) {
            case School::TYPE_PRIMARY:
                // 小学教师通常教1-3个年级
                $allGrades = [1, 2, 3, 4, 5, 6];
                $gradeCount = rand(2, 3);
                break;
            case School::TYPE_JUNIOR:
                // 初中教师通常教1-2个年级
                $allGrades = [7, 8, 9];
                $gradeCount = rand(1, 2);
                break;
            case School::TYPE_SENIOR:
                // 高中教师通常教1-2个年级
                $allGrades = [10, 11, 12];
                $gradeCount = rand(1, 2);
                break;
            case School::TYPE_NINE_YEAR:
                // 九年一贯制教师可能教小学或初中
                if (rand(0, 1)) {
                    $allGrades = [1, 2, 3, 4, 5, 6]; // 小学部
                    $gradeCount = rand(2, 3);
                } else {
                    $allGrades = [7, 8, 9]; // 初中部
                    $gradeCount = rand(1, 2);
                }
                break;
            default:
                $allGrades = [1, 2, 3, 4, 5, 6];
                $gradeCount = rand(2, 3);
        }

        // 随机选择年级
        shuffle($allGrades);
        return array_slice($allGrades, 0, $gradeCount);
    }
}
