<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;
use App\Models\Equipment;
use App\Models\EquipmentBorrow;
use App\Models\EquipmentMaintenance;
use App\Models\School;
use App\Models\Laboratory;
use App\Models\User;

class CompleteEquipmentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建完整的设备管理示例数据...');

        // 1. 确保基础数据存在
        $this->ensureBasicData();

        // 2. 创建更多设备数据
        $this->createAdditionalEquipments();

        // 3. 创建更多借用记录
        $this->createAdditionalBorrows();

        // 4. 创建更多维修记录
        $this->createAdditionalMaintenances();

        // 5. 生成设备二维码
        $this->generateQrCodes();

        // 6. 显示统计信息
        $this->showStatistics();

        $this->command->info('完整的设备管理示例数据创建完成！');
    }

    /**
     * 确保基础数据存在
     */
    private function ensureBasicData()
    {
        $this->command->info('检查基础数据...');

        $categoryCount = EquipmentCategory::count();
        $schoolCount = School::count();
        $userCount = User::count();

        $this->command->info("设备分类数量: {$categoryCount}");
        $this->command->info("学校数量: {$schoolCount}");
        $this->command->info("用户数量: {$userCount}");

        if ($categoryCount === 0) {
            $this->command->warn('设备分类数据不存在，请先运行 EquipmentCategorySeeder');
        }

        if ($schoolCount === 0) {
            $this->command->warn('学校数据不存在，请先运行 SchoolSeeder');
        }

        if ($userCount === 0) {
            $this->command->warn('用户数据不存在，请先运行基础数据填充');
        }
    }

    /**
     * 创建更多设备数据
     */
    private function createAdditionalEquipments()
    {
        $this->command->info('创建更多设备数据...');

        $schools = School::all();
        $categories = EquipmentCategory::where('level', 3)->get();
        $users = User::all();

        if ($schools->isEmpty() || $categories->isEmpty() || $users->isEmpty()) {
            $this->command->warn('缺少基础数据，跳过设备创建');
            return;
        }

        // 为每个学校创建更多设备
        foreach ($schools as $school) {
            $laboratories = Laboratory::where('school_id', $school->id)->get();
            
            foreach ($categories->take(10) as $category) {
                // 为每个分类创建1-3个设备
                $equipmentCount = rand(1, 3);
                
                for ($i = 0; $i < $equipmentCount; $i++) {
                    $laboratory = $laboratories->count() > 0 ? $laboratories->random() : null;
                    $manager = $users->random();

                    $equipment = Equipment::create([
                        'school_id' => $school->id,
                        'laboratory_id' => $laboratory ? $laboratory->id : null,
                        'category_id' => $category->id,
                        'name' => $this->generateEquipmentName($category),
                        'code' => $this->generateEquipmentCode($category, $school),
                        'model' => $this->generateModel(),
                        'brand' => $this->generateBrand(),
                        'supplier' => $this->generateSupplier(),
                        'supplier_phone' => $this->generatePhone(),
                        'purchase_date' => now()->subDays(rand(30, 1095)), // 1-3年前
                        'purchase_price' => $this->generatePrice($category),
                        'quantity' => rand(1, 10),
                        'unit' => $this->generateUnit($category),
                        'warranty_period' => rand(6, 36), // 6-36个月
                        'service_life' => rand(5, 15), // 5-15年
                        'funding_source' => $this->generateFundingSource(),
                        'storage_location' => $laboratory ? $laboratory->name : '设备室',
                        'manager_id' => $manager->id,
                        'status' => $this->generateStatus(),
                        'remark' => $this->generateRemark()
                    ]);

                    // 生成二维码
                    $equipment->generateQrCode();
                }
            }
        }

        $totalEquipments = Equipment::count();
        $this->command->info("设备总数: {$totalEquipments}");
    }

    /**
     * 创建更多借用记录
     */
    private function createAdditionalBorrows()
    {
        $this->command->info('创建更多借用记录...');

        $equipments = Equipment::where('status', Equipment::STATUS_NORMAL)->get();
        $users = User::all();

        if ($equipments->isEmpty() || $users->isEmpty()) {
            return;
        }

        // 创建50条借用记录
        for ($i = 0; $i < 50; $i++) {
            $equipment = $equipments->random();
            $borrower = $users->random();
            $approver = $users->random();

            $borrowDate = now()->subDays(rand(1, 90));
            $expectedReturnDate = $borrowDate->copy()->addDays(rand(7, 30));

            $status = $this->generateBorrowStatus();
            $quantity = min(rand(1, 3), $equipment->quantity);

            $borrowData = [
                'equipment_id' => $equipment->id,
                'borrower_id' => $borrower->id,
                'quantity' => $quantity,
                'borrow_date' => $borrowDate->toDateString(),
                'expected_return_date' => $expectedReturnDate->toDateString(),
                'purpose' => $this->generateBorrowPurpose(),
                'remark' => $this->generateBorrowRemark(),
                'status' => $status
            ];

            // 根据状态设置额外字段
            if (in_array($status, [EquipmentBorrow::STATUS_BORROWED, EquipmentBorrow::STATUS_RETURNED, EquipmentBorrow::STATUS_OVERDUE])) {
                $borrowData['approver_id'] = $approver->id;
                $borrowData['approved_at'] = $borrowDate->copy()->addHours(rand(1, 48));
                $borrowData['approval_remark'] = '审批通过';
            }

            if ($status === EquipmentBorrow::STATUS_RETURNED) {
                $borrowData['actual_return_date'] = $expectedReturnDate->copy()->subDays(rand(0, 5))->toDateString();
            }

            EquipmentBorrow::create($borrowData);
        }

        $totalBorrows = EquipmentBorrow::count();
        $this->command->info("借用记录总数: {$totalBorrows}");
    }

    /**
     * 创建更多维修记录
     */
    private function createAdditionalMaintenances()
    {
        $this->command->info('创建更多维修记录...');

        $equipments = Equipment::all();
        $users = User::all();

        if ($equipments->isEmpty() || $users->isEmpty()) {
            return;
        }

        // 创建30条维修记录
        for ($i = 0; $i < 30; $i++) {
            $equipment = $equipments->random();
            $reporter = $users->random();
            $maintainer = $users->random();

            $reportDate = now()->subDays(rand(1, 180));
            $status = $this->generateMaintenanceStatus();

            $maintenanceData = [
                'equipment_id' => $equipment->id,
                'reporter_id' => $reporter->id,
                'fault_description' => $this->generateFaultDescription(),
                'fault_type' => $this->generateFaultType(),
                'urgency_level' => rand(1, 3),
                'report_date' => $reportDate->toDateString(),
                'status' => $status,
                'remark' => $this->generateMaintenanceRemark()
            ];

            // 根据状态设置额外字段
            if (in_array($status, [EquipmentMaintenance::STATUS_PROCESSING, EquipmentMaintenance::STATUS_COMPLETED, EquipmentMaintenance::STATUS_UNREPAIRABLE])) {
                $startDate = $reportDate->copy()->addDays(rand(1, 5));
                $maintenanceData['maintainer_id'] = $maintainer->id;
                $maintenanceData['start_date'] = $startDate->toDateString();

                if (in_array($status, [EquipmentMaintenance::STATUS_COMPLETED, EquipmentMaintenance::STATUS_UNREPAIRABLE])) {
                    $completeDate = $startDate->copy()->addDays(rand(1, 10));
                    $maintenanceData['complete_date'] = $completeDate->toDateString();

                    if ($status === EquipmentMaintenance::STATUS_COMPLETED) {
                        $maintenanceData['cost'] = rand(50, 1000);
                        $maintenanceData['solution'] = $this->generateSolution();
                        $maintenanceData['parts_replaced'] = $this->generatePartsReplaced();
                        $maintenanceData['quality_rating'] = rand(3, 5);
                    } else {
                        $maintenanceData['solution'] = $this->generateUnrepairableReason();
                    }
                }
            }

            EquipmentMaintenance::create($maintenanceData);
        }

        $totalMaintenances = EquipmentMaintenance::count();
        $this->command->info("维修记录总数: {$totalMaintenances}");
    }

    /**
     * 生成设备二维码
     */
    private function generateQrCodes()
    {
        $this->command->info('为所有设备生成二维码...');

        $equipments = Equipment::whereNull('qr_code')->get();
        
        foreach ($equipments as $equipment) {
            $equipment->generateQrCode();
        }

        $qrCodeCount = Equipment::whereNotNull('qr_code')->count();
        $this->command->info("已生成二维码的设备数量: {$qrCodeCount}");
    }

    /**
     * 显示统计信息
     */
    private function showStatistics()
    {
        $this->command->info('=== 设备管理数据统计 ===');
        
        $this->command->info('设备分类统计:');
        $this->command->info('- 总分类数: ' . EquipmentCategory::count());
        $this->command->info('- 根分类数: ' . EquipmentCategory::whereNull('parent_id')->count());
        $this->command->info('- 叶子分类数: ' . EquipmentCategory::where('level', 3)->count());

        $this->command->info('设备统计:');
        $this->command->info('- 总设备数: ' . Equipment::count());
        $this->command->info('- 正常设备数: ' . Equipment::where('status', Equipment::STATUS_NORMAL)->count());
        $this->command->info('- 维修中设备数: ' . Equipment::where('status', Equipment::STATUS_MAINTENANCE)->count());
        $this->command->info('- 有二维码设备数: ' . Equipment::whereNotNull('qr_code')->count());

        $this->command->info('借用记录统计:');
        $this->command->info('- 总借用记录数: ' . EquipmentBorrow::count());
        $this->command->info('- 待审批记录数: ' . EquipmentBorrow::where('status', EquipmentBorrow::STATUS_PENDING)->count());
        $this->command->info('- 借用中记录数: ' . EquipmentBorrow::where('status', EquipmentBorrow::STATUS_BORROWED)->count());
        $this->command->info('- 已归还记录数: ' . EquipmentBorrow::where('status', EquipmentBorrow::STATUS_RETURNED)->count());

        $this->command->info('维修记录统计:');
        $this->command->info('- 总维修记录数: ' . EquipmentMaintenance::count());
        $this->command->info('- 待维修记录数: ' . EquipmentMaintenance::where('status', EquipmentMaintenance::STATUS_PENDING)->count());
        $this->command->info('- 维修中记录数: ' . EquipmentMaintenance::where('status', EquipmentMaintenance::STATUS_PROCESSING)->count());
        $this->command->info('- 已完成记录数: ' . EquipmentMaintenance::where('status', EquipmentMaintenance::STATUS_COMPLETED)->count());
    }

    // 以下是各种数据生成方法
    private function generateEquipmentName($category)
    {
        $names = [
            '高精度' . $category->name,
            '数字式' . $category->name,
            '便携式' . $category->name,
            '台式' . $category->name,
            '专业级' . $category->name,
            '教学用' . $category->name,
            '实验室' . $category->name,
            '多功能' . $category->name
        ];
        return $names[array_rand($names)];
    }

    private function generateEquipmentCode($category, $school)
    {
        $prefix = strtoupper(substr($category->code, 0, 3));
        $schoolCode = str_pad($school->id, 3, '0', STR_PAD_LEFT);
        $sequence = Equipment::where('category_id', $category->id)
                            ->where('school_id', $school->id)
                            ->count() + 1;
        return $prefix . $schoolCode . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateModel()
    {
        $models = ['XS-2000', 'Pro-500', 'Elite-300', 'Standard-100', 'Advanced-800', 'Basic-200'];
        return $models[array_rand($models)] . rand(1, 99);
    }

    private function generateBrand()
    {
        $brands = ['奥林巴斯', '尼康', '蔡司', '徕卡', '佳能', '索尼', '松下', '富士', '海康威视', '大华'];
        return $brands[array_rand($brands)];
    }

    private function generateSupplier()
    {
        $suppliers = ['北京科学仪器公司', '上海教学设备公司', '广州实验器材公司', '深圳科技设备公司', '天津仪器设备公司'];
        return $suppliers[array_rand($suppliers)];
    }

    private function generatePhone()
    {
        return '0' . rand(10, 99) . '-' . rand(10000000, 99999999);
    }

    private function generatePrice($category)
    {
        $ranges = [
            'MICROSCOPE' => [1000, 5000],
            'BALANCE' => [2000, 10000],
            'MULTIMETER' => [100, 500],
            'BEAKER' => [5, 50],
            'SCALPEL' => [10, 100]
        ];
        
        $range = $ranges[$category->code] ?? [100, 1000];
        return rand($range[0], $range[1]);
    }

    private function generateUnit($category)
    {
        $units = ['台', '个', '套', '支', '只', '件'];
        return $units[array_rand($units)];
    }

    private function generateFundingSource()
    {
        $sources = ['教育专项资金', '学校自筹', '实验室建设专项', '教育部专项', '省级专项资金'];
        return $sources[array_rand($sources)];
    }

    private function generateStatus()
    {
        $statuses = [Equipment::STATUS_NORMAL, Equipment::STATUS_NORMAL, Equipment::STATUS_NORMAL, Equipment::STATUS_MAINTENANCE];
        return $statuses[array_rand($statuses)];
    }

    private function generateRemark()
    {
        $remarks = ['设备状态良好', '需要定期维护', '使用频率较高', '新购入设备', ''];
        return $remarks[array_rand($remarks)];
    }

    private function generateBorrowStatus()
    {
        $statuses = [
            EquipmentBorrow::STATUS_PENDING,
            EquipmentBorrow::STATUS_BORROWED,
            EquipmentBorrow::STATUS_RETURNED,
            EquipmentBorrow::STATUS_RETURNED,
            EquipmentBorrow::STATUS_OVERDUE
        ];
        return $statuses[array_rand($statuses)];
    }

    private function generateBorrowPurpose()
    {
        $purposes = ['教学实验', '科研项目', '学生实践', '设备测试', '教师培训', '实验演示'];
        return $purposes[array_rand($purposes)];
    }

    private function generateBorrowRemark()
    {
        $remarks = ['请按时归还', '注意安全使用', '如有问题及时联系', ''];
        return $remarks[array_rand($remarks)];
    }

    private function generateMaintenanceStatus()
    {
        $statuses = [
            EquipmentMaintenance::STATUS_PENDING,
            EquipmentMaintenance::STATUS_PROCESSING,
            EquipmentMaintenance::STATUS_COMPLETED,
            EquipmentMaintenance::STATUS_COMPLETED,
            EquipmentMaintenance::STATUS_UNREPAIRABLE
        ];
        return $statuses[array_rand($statuses)];
    }

    private function generateFaultDescription()
    {
        $descriptions = [
            '设备无法正常启动',
            '显示屏出现异常',
            '精度下降明显',
            '运行时有异响',
            '按键失灵',
            '连接线路故障'
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function generateFaultType()
    {
        $types = ['电源故障', '机械故障', '电子故障', '软件故障', '传感器故障', '显示故障'];
        return $types[array_rand($types)];
    }

    private function generateMaintenanceRemark()
    {
        $remarks = ['紧急维修', '定期保养', '预防性维护', '故障维修', ''];
        return $remarks[array_rand($remarks)];
    }

    private function generateSolution()
    {
        $solutions = [
            '更换损坏部件，重新校准',
            '清洁内部，更换老化元件',
            '修复软件系统，更新驱动',
            '调整参数，恢复正常功能',
            '更换传感器，重新测试'
        ];
        return $solutions[array_rand($solutions)];
    }

    private function generatePartsReplaced()
    {
        $parts = ['电源模块', '传感器', '显示屏', '控制板', '连接线', ''];
        return $parts[array_rand($parts)];
    }

    private function generateUnrepairableReason()
    {
        $reasons = [
            '设备老化严重，无法修复',
            '关键部件损坏，无替换件',
            '维修成本过高，建议报废',
            '技术过于陈旧，无维修价值'
        ];
        return $reasons[array_rand($reasons)];
    }
}
