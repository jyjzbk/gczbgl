<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentRecord;
use App\Models\ExperimentCatalog;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\School;
use App\Models\ExperimentReservation;
use Carbon\Carbon;

class ExperimentRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = ExperimentReservation::where('status', 2)->get(); // 只处理已批准的预约

        if ($reservations->isEmpty()) {
            echo "没有已批准的实验预约，跳过实验记录创建\n";
            return;
        }

        $experiments = ExperimentCatalog::all();
        $laboratories = Laboratory::all();
        $teachers = User::where('role', 'teacher')->get();
        $schools = School::all();

        $grades = ['一年级', '二年级', '三年级', '四年级', '五年级', '六年级', '七年级', '八年级', '九年级', '高一', '高二', '高三'];

        // 为每个学校创建实验记录
        foreach ($schools->take(5) as $school) {
            $schoolLabs = $laboratories->where('school_id', $school->id);
            $schoolTeachers = $teachers->where('organization_id', $school->id);

            if ($schoolLabs->isEmpty() || $schoolTeachers->isEmpty()) {
                continue;
            }

            // 为每个实验室创建多个实验记录
            foreach ($schoolLabs->take(2) as $lab) {
                for ($i = 0; $i < 10; $i++) {
                    $experiment = $experiments->random();
                    $teacher = $schoolTeachers->random();
                    // 删除这行，不需要status变量
                    $grade = $grades[array_rand($grades)];

                    // 随机生成日期（过去3个月内）
                    $experimentDate = Carbon::now()->subDays(rand(1, 90));
                    
                    $startTime = $experimentDate->copy()->setTime(rand(8, 15), rand(0, 59));
                    $endTime = $startTime->copy()->addMinutes(rand(40, 90));

                    // 使用真实的预约ID
                    $availableReservations = ExperimentReservation::where('status', 2)->pluck('id');
                    if ($availableReservations->isEmpty()) {
                        continue;
                    }

                    ExperimentRecord::create([
                        'reservation_id' => $availableReservations->random(),
                        'school_id' => $school->id,
                        'laboratory_id' => $lab->id,
                        'catalog_id' => $experiment->id,
                        'teacher_id' => $teacher->id,
                        'class_name' => $grade . '(' . rand(1, 6) . ')班',
                        'student_count' => rand(25, 45),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'completion_rate' => rand(80, 100),
                        'quality_score' => rand(7, 10),
                        'summary' => '实验' . $experiment->name . '顺利完成，学生掌握了实验要点。',
                        'problems' => rand(0, 1) ? '部分学生操作不够熟练' : null,
                        'suggestions' => '建议增加预习环节，提高实验效率。',
                        'status' => rand(1, 3), // 1进行中 2已完成 3已取消
                        'created_at' => $experimentDate,
                        'updated_at' => $experimentDate,
                    ]);
                }
            }
        }

        echo "实验记录种子数据创建完成！\n";
        echo "总实验记录数: " . ExperimentRecord::count() . "\n";
    }
}
