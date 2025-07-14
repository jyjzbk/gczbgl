<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentBorrow;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;

class EquipmentBorrowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipments = Equipment::all();
        $users = User::all();

        if ($equipments->isEmpty() || $users->isEmpty()) {
            $this->command->info('缺少基础数据，跳过借用记录数据填充');
            return;
        }

        // 创建不同状态的借用记录
        $borrowRecords = [
            // 待审批记录
            [
                'status' => EquipmentBorrow::STATUS_PENDING,
                'count' => 5,
                'days_ago' => 1
            ],
            // 借用中记录
            [
                'status' => EquipmentBorrow::STATUS_BORROWED,
                'count' => 8,
                'days_ago' => 7
            ],
            // 已归还记录
            [
                'status' => EquipmentBorrow::STATUS_RETURNED,
                'count' => 12,
                'days_ago' => 15
            ],
            // 逾期记录
            [
                'status' => EquipmentBorrow::STATUS_OVERDUE,
                'count' => 3,
                'days_ago' => 20
            ]
        ];

        foreach ($borrowRecords as $recordType) {
            for ($i = 0; $i < $recordType['count']; $i++) {
                $equipment = $equipments->random();
                $borrower = $users->random();

                // 如果只有一个用户，审批人就是同一个用户
                $approver = $users->count() > 1
                    ? $users->where('id', '!=', $borrower->id)->random()
                    : $borrower;

                $borrowDate = Carbon::now()->subDays($recordType['days_ago'] + rand(0, 5));
                $expectedReturnDate = $borrowDate->copy()->addDays(rand(7, 30));
                
                $quantity = min(rand(1, 3), $equipment->quantity);

                $borrowData = [
                    'equipment_id' => $equipment->id,
                    'borrower_id' => $borrower->id,
                    'quantity' => $quantity,
                    'borrow_date' => $borrowDate->toDateString(),
                    'expected_return_date' => $expectedReturnDate->toDateString(),
                    'purpose' => $this->getRandomPurpose(),
                    'remark' => $this->getRandomRemark(),
                    'status' => $recordType['status']
                ];

                // 根据状态设置不同的字段
                switch ($recordType['status']) {
                    case EquipmentBorrow::STATUS_PENDING:
                        // 待审批记录不需要额外字段
                        break;

                    case EquipmentBorrow::STATUS_BORROWED:
                        $borrowData['approver_id'] = $approver->id;
                        $borrowData['approved_at'] = $borrowDate->copy()->addHours(rand(1, 24));
                        $borrowData['approval_remark'] = '审批通过';
                        break;

                    case EquipmentBorrow::STATUS_RETURNED:
                        $borrowData['approver_id'] = $approver->id;
                        $borrowData['approved_at'] = $borrowDate->copy()->addHours(rand(1, 24));
                        $borrowData['approval_remark'] = '审批通过';
                        $borrowData['actual_return_date'] = $expectedReturnDate->copy()->subDays(rand(0, 3))->toDateString();
                        break;

                    case EquipmentBorrow::STATUS_OVERDUE:
                        $borrowData['approver_id'] = $approver->id;
                        $borrowData['approved_at'] = $borrowDate->copy()->addHours(rand(1, 24));
                        $borrowData['approval_remark'] = '审批通过';
                        // 逾期记录的预期归还日期已过
                        $borrowData['expected_return_date'] = Carbon::now()->subDays(rand(1, 10))->toDateString();
                        break;
                }

                EquipmentBorrow::create($borrowData);
            }
        }

        // 创建一些拒绝的记录
        for ($i = 0; $i < 3; $i++) {
            $equipment = $equipments->random();
            $borrower = $users->random();
            $approver = $users->count() > 1
                ? $users->where('id', '!=', $borrower->id)->random()
                : $borrower;

            $borrowDate = Carbon::now()->subDays(rand(5, 15));

            EquipmentBorrow::create([
                'equipment_id' => $equipment->id,
                'borrower_id' => $borrower->id,
                'quantity' => rand(1, 2),
                'borrow_date' => $borrowDate->toDateString(),
                'expected_return_date' => $borrowDate->copy()->addDays(rand(7, 14))->toDateString(),
                'purpose' => $this->getRandomPurpose(),
                'remark' => '申请借用',
                'status' => EquipmentBorrow::STATUS_REJECTED,
                'approver_id' => $approver->id,
                'approved_at' => $borrowDate->copy()->addHours(rand(1, 48)),
                'approval_remark' => '设备维修中，暂不可借用'
            ]);
        }

        $this->command->info('设备借用记录数据填充完成');
    }

    /**
     * 获取随机借用目的
     */
    private function getRandomPurpose(): string
    {
        $purposes = [
            '物理实验课教学使用',
            '化学实验课教学使用',
            '生物实验课教学使用',
            '学生课外实验活动',
            '教师演示实验',
            '科学兴趣小组活动',
            '实验技能竞赛准备',
            '教学研究实验',
            '设备功能测试',
            '学生毕业设计实验',
            '教师培训使用',
            '实验室开放日活动',
            '科普展示活动',
            '教学观摩课使用',
            '实验方法研究'
        ];

        return $purposes[array_rand($purposes)];
    }

    /**
     * 获取随机备注
     */
    private function getRandomRemark(): string
    {
        $remarks = [
            '请妥善保管，按时归还',
            '实验结束后及时清洁设备',
            '如有损坏请及时报告',
            '注意安全操作',
            '设备使用前请检查完好性',
            '实验过程中如有问题请联系管理员',
            '借用期间请勿转借他人',
            '归还时请检查设备完整性',
            '使用完毕请断电并整理',
            '感谢配合实验室管理工作'
        ];

        // 有30%的概率没有备注
        if (rand(1, 10) <= 3) {
            return '';
        }

        return $remarks[array_rand($remarks)];
    }
}
