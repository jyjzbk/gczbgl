<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\SchoolClass;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建学校班级数据...');

        // 获取前5个学校
        $schools = School::take(5)->get();

        foreach ($schools as $school) {
            $this->command->info("为学校 {$school->name} 创建班级...");

            // 根据学校类型创建不同的年级班级
            $grades = $this->getGradesBySchoolType($school->type);

            foreach ($grades as $grade) {
                // 每个年级创建2-4个班级
                $classCount = rand(2, 4);
                
                for ($i = 1; $i <= $classCount; $i++) {
                    $className = $this->generateClassName($grade, $i);
                    $code = SchoolClass::generateCode($school->id, $grade, $i);

                    // 检查是否已存在
                    if (SchoolClass::where('school_id', $school->id)->where('code', $code)->exists()) {
                        continue;
                    }

                    SchoolClass::create([
                        'school_id' => $school->id,
                        'name' => $className,
                        'code' => $code,
                        'grade' => $grade,
                        'class_number' => $i,
                        'student_count' => rand(25, 45), // 随机学生人数
                        'classroom_location' => $this->generateClassroomLocation($grade, $i),
                        'status' => SchoolClass::STATUS_ACTIVE
                    ]);

                    $this->command->info("  创建班级: {$className}");
                }
            }
        }

        $totalClasses = SchoolClass::count();
        $this->command->info("班级数据创建完成，共创建 {$totalClasses} 个班级");
    }

    /**
     * 根据学校类型获取年级范围
     */
    private function getGradesBySchoolType(int $type): array
    {
        switch ($type) {
            case School::TYPE_PRIMARY:
                return [1, 2, 3, 4, 5, 6]; // 小学1-6年级
            case School::TYPE_JUNIOR:
                return [7, 8, 9]; // 初中7-9年级
            case School::TYPE_SENIOR:
                return [10, 11, 12]; // 高中10-12年级（这里用10-12表示高一到高三）
            case School::TYPE_NINE_YEAR:
                return [1, 2, 3, 4, 5, 6, 7, 8, 9]; // 九年一贯制1-9年级
            default:
                return [1, 2, 3, 4, 5, 6]; // 默认小学
        }
    }

    /**
     * 生成班级名称
     */
    private function generateClassName(int $grade, int $classNumber): string
    {
        $grades = [
            1 => '一年级', 2 => '二年级', 3 => '三年级',
            4 => '四年级', 5 => '五年级', 6 => '六年级',
            7 => '七年级', 8 => '八年级', 9 => '九年级',
            10 => '高一', 11 => '高二', 12 => '高三'
        ];
        
        $gradeName = $grades[$grade] ?? '未知年级';
        return $gradeName . '（' . $classNumber . '）';
    }

    /**
     * 生成教室位置
     */
    private function generateClassroomLocation(int $grade, int $classNumber): string
    {
        $floor = ceil($grade / 3); // 每3个年级一层楼
        $roomNumber = ($grade % 3) * 10 + $classNumber;
        
        return "{$floor}楼{$roomNumber}教室";
    }
}
