<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExperimentRecord;
use App\Models\ExperimentReservation;
use App\Models\EquipmentBorrow;
use App\Models\User;
use App\Models\Equipment;
use App\Models\School;
use App\Models\ExperimentCatalog;
use App\Models\Laboratory;
use Carbon\Carbon;

class StatisticsTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建统计报表测试数据...');

        // 获取基础数据
        $schools = School::limit(5)->get();
        $users = User::where('status', 1)->limit(10)->get();
        $equipments = Equipment::limit(20)->get();
        $catalogs = ExperimentCatalog::limit(5)->get();
        $laboratories = Laboratory::limit(5)->get();

        if ($schools->isEmpty() || $users->isEmpty() || $equipments->isEmpty()) {
            $this->command->error('缺少基础数据，请先运行基础数据种子文件');
            return;
        }

        // 创建实验预约和记录
        $this->createExperimentData($schools, $users, $catalogs, $laboratories);

        // 创建设备借用记录
        $this->createEquipmentBorrows($users, $equipments);

        // 更新用户最后登录时间
        $this->updateUserLoginTimes($users);

        $this->command->info('统计报表测试数据创建完成！');
    }

    /**
     * 创建实验预约和记录
     */
    private function createExperimentData($schools, $users, $catalogs, $laboratories)
    {
        $this->command->info('创建实验预约和记录...');

        $reservationStatuses = [2, 4]; // 已通过、已完成
        $recordStatuses = [1, 2, 3]; // 进行中、已完成、异常结束
        $qualityScores = [3, 4, 5]; // 质量评分

        for ($i = 0; $i < 50; $i++) {
            $school = $schools->random();
            $teacher = $users->random();
            $catalog = $catalogs->random();
            $laboratory = $laboratories->random();
            $reservationStatus = collect($reservationStatuses)->random();

            // 创建实验预约
            $reservationDate = Carbon::now()->subDays(rand(1, 90));
            $reservation = ExperimentReservation::create([
                'school_id' => $school->id,
                'catalog_id' => $catalog->id,
                'laboratory_id' => $laboratory->id,
                'teacher_id' => $teacher->id,
                'class_name' => '测试班级' . rand(1, 10),
                'student_count' => rand(20, 40),
                'reservation_date' => $reservationDate->format('Y-m-d'),
                'start_time' => '08:00',
                'end_time' => '10:00',
                'status' => $reservationStatus,
                'reviewer_id' => $users->random()->id,
                'reviewed_at' => $reservationDate->copy()->addHours(rand(1, 24)),
            ]);

            // 如果预约已完成，创建对应的实验记录
            if ($reservationStatus == 4) {
                $recordStatus = collect($recordStatuses)->random();
                $startTime = $reservationDate->copy()->addHours(8);
                $endTime = $recordStatus == 2 ? $startTime->copy()->addHours(rand(1, 4)) : null;

                ExperimentRecord::create([
                    'reservation_id' => $reservation->id,
                    'school_id' => $school->id,
                    'catalog_id' => $catalog->id,
                    'laboratory_id' => $laboratory->id,
                    'teacher_id' => $teacher->id,
                    'class_name' => $reservation->class_name,
                    'student_count' => $reservation->student_count,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'completion_rate' => rand(80, 100),
                    'quality_score' => $recordStatus == 2 ? collect($qualityScores)->random() : null,
                    'summary' => $recordStatus == 2 ? '实验完成情况良好' : null,
                    'status' => $recordStatus,
                ]);
            }
        }

        $this->command->info('✅ 创建了50条实验预约和相应的实验记录');
    }

    /**
     * 创建设备借用记录
     */
    private function createEquipmentBorrows($users, $equipments)
    {
        $this->command->info('创建设备借用记录...');

        $statuses = [2, 4]; // 已归还、损坏
        $purposes = ['教学实验', '科研项目', '技能竞赛', '设备测试', '维护保养'];

        for ($i = 0; $i < 100; $i++) {
            $borrower = $users->random();
            $equipment = $equipments->random();
            $status = collect($statuses)->random();

            // 随机生成过去3个月内的借用时间
            $borrowDate = Carbon::now()->subDays(rand(1, 90));
            $expectedReturnDate = $borrowDate->copy()->addDays(rand(1, 14));
            $actualReturnDate = $status == 2 ? $borrowDate->copy()->addDays(rand(1, 15)) : null;

            EquipmentBorrow::create([
                'equipment_id' => $equipment->id,
                'reservation_id' => null,
                'borrower_id' => $borrower->id,
                'quantity' => rand(1, 3),
                'borrow_date' => $borrowDate,
                'expected_return_date' => $expectedReturnDate,
                'actual_return_date' => $actualReturnDate,
                'purpose' => collect($purposes)->random(),
                'remark' => '测试借用记录',
                'status' => $status,
                'approver_id' => $users->random()->id,
                'approved_at' => $borrowDate->copy()->addHours(rand(1, 24)),
            ]);
        }

        $this->command->info('✅ 创建了100条设备借用记录');
    }

    /**
     * 更新用户最后登录时间
     */
    private function updateUserLoginTimes($users)
    {
        $this->command->info('更新用户登录时间...');

        foreach ($users as $user) {
            // 随机设置最后登录时间（过去30天内）
            if (rand(1, 10) > 2) { // 80%的用户有登录记录
                $user->update([
                    'last_login_at' => Carbon::now()->subDays(rand(1, 30))
                ]);
            }
        }

        $this->command->info('✅ 更新了用户登录时间');
    }
}
