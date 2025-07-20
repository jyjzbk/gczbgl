<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentReservation;
use App\Models\ExperimentCatalog;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\School;
use Carbon\Carbon;

class ExperimentReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experiments = ExperimentCatalog::all();
        $laboratories = Laboratory::all();
        $teachers = User::where('role', 'teacher')->get();
        $schools = School::all();

        if ($experiments->isEmpty() || $laboratories->isEmpty() || $teachers->isEmpty()) {
            echo "缺少必要的基础数据，跳过实验预约创建\n";
            return;
        }

        $statuses = [1, 2, 3]; // 1待审核 2已批准 3已拒绝
        $grades = ['一年级', '二年级', '三年级', '四年级', '五年级', '六年级', '七年级', '八年级', '九年级', '高一', '高二', '高三'];

        // 为每个学校创建实验预约
        foreach ($schools->take(5) as $school) {
            $schoolLabs = $laboratories->where('school_id', $school->id);
            $schoolTeachers = $teachers->where('organization_id', $school->id);

            if ($schoolLabs->isEmpty() || $schoolTeachers->isEmpty()) {
                continue;
            }

            // 为每个实验室创建多个实验预约
            foreach ($schoolLabs->take(2) as $lab) {
                for ($i = 0; $i < 15; $i++) {
                    $experiment = $experiments->random();
                    $teacher = $schoolTeachers->random();
                    $status = $statuses[array_rand($statuses)];
                    $grade = $grades[array_rand($grades)];

                    // 随机生成预约日期（未来1个月内）
                    $reservationDate = Carbon::now()->addDays(rand(1, 30));
                    $startTime = $reservationDate->copy()->setTime(rand(8, 15), rand(0, 59));
                    $endTime = $startTime->copy()->addMinutes(rand(40, 90));
                    
                    ExperimentReservation::create([
                        'school_id' => $school->id,
                        'laboratory_id' => $lab->id,
                        'catalog_id' => $experiment->id,
                        'teacher_id' => $teacher->id,
                        'class_name' => $grade . '(' . rand(1, 6) . ')班',
                        'student_count' => rand(25, 45),
                        'reservation_date' => $reservationDate->toDateString(),
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $endTime->format('H:i'),
                        'status' => $status,
                        'remark' => '完成' . $experiment->name . '实验教学任务',
                        'reviewer_id' => $status == 2 ? $teachers->random()->id : null,
                        'reviewed_at' => $status == 2 ? Carbon::now()->subDays(rand(1, 7)) : null,
                        'review_remark' => $status == 3 ? '实验室设备维护中' : ($status == 2 ? '审核通过' : null),
                        'created_at' => Carbon::now()->subDays(rand(1, 10)),
                        'updated_at' => Carbon::now()->subDays(rand(1, 10)),
                    ]);
                }
            }
        }

        echo "实验预约种子数据创建完成！\n";
        echo "总实验预约数: " . ExperimentReservation::count() . "\n";
    }
}
