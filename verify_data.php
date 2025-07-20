<?php

require_once 'backend/vendor/autoload.php';

$app = require_once 'backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TeachingEquipmentStandard;

echo "=== 教学仪器配备标准数据验证 ===\n\n";

// 总记录数
$total = TeachingEquipmentStandard::count();
echo "总记录数: $total\n\n";

// 按学段和学科统计
echo "按学段和学科统计:\n";
$stats = TeachingEquipmentStandard::selectRaw('stage, subject_name, count(*) as count')
    ->groupBy('stage', 'subject_name')
    ->orderBy('stage')
    ->orderBy('subject_name')
    ->get();

$stageNames = [1 => '小学', 2 => '初中', 3 => '高中'];

foreach ($stats as $stat) {
    $stageName = $stageNames[$stat->stage] ?? '未知';
    echo "  {$stageName} - {$stat->subject_name}: {$stat->count} 条\n";
}

echo "\n";

// 显示每个学科的示例数据
echo "各学科示例数据:\n";
$subjects = TeachingEquipmentStandard::select('stage', 'subject_name')
    ->distinct()
    ->orderBy('stage')
    ->orderBy('subject_name')
    ->get();

foreach ($subjects as $subject) {
    $stageName = $stageNames[$subject->stage] ?? '未知';
    echo "\n{$stageName} - {$subject->subject_name}:\n";
    
    $samples = TeachingEquipmentStandard::where('stage', $subject->stage)
        ->where('subject_name', $subject->subject_name)
        ->limit(3)
        ->get(['item_name', 'unit_price', 'quantity', 'total_amount']);
    
    foreach ($samples as $sample) {
        echo "  - {$sample->item_name}: {$sample->quantity}个 × ¥{$sample->unit_price} = ¥{$sample->total_amount}\n";
    }
}

echo "\n=== 验证完成 ===\n";
