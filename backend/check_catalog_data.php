<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ExperimentCatalog;
use App\Models\Subject;

echo "🔍 检查实验目录数据...\n\n";

// 1. 检查小学科学学科
$primaryScience = Subject::where('name', 'LIKE', '%科学%')->first();
if (!$primaryScience) {
    echo "❌ 未找到小学科学学科\n";
    exit(1);
}

echo "📚 学科信息: {$primaryScience->name} (ID: {$primaryScience->id})\n\n";

// 2. 检查该学科下的实验目录
$catalogs = ExperimentCatalog::where('subject_id', $primaryScience->id)
    ->with('textbookVersion')
    ->get();

echo "📊 实验目录总数: {$catalogs->count()}\n\n";

// 3. 按管理级别分组显示
$levelNames = [
    1 => '省级',
    2 => '市级', 
    3 => '区县级',
    4 => '学区级',
    5 => '学校级'
];

foreach ($levelNames as $level => $name) {
    $levelCatalogs = $catalogs->where('management_level', $level);
    if ($levelCatalogs->count() > 0) {
        echo "🏢 {$name} ({$level}级) - {$levelCatalogs->count()} 个实验:\n";
        foreach ($levelCatalogs as $catalog) {
            $versionName = $catalog->textbookVersion ? $catalog->textbookVersion->name : '无版本';
            echo "  - {$catalog->name} ({$catalog->code}) - {$catalog->grade}年级{$catalog->semester}学期 - {$versionName}\n";
        }
        echo "\n";
    }
}

// 4. 检查一年级下册的数据
echo "🎯 一年级下册实验目录:\n";
$grade1Semester2 = $catalogs->where('grade', 1)->where('semester', 2);
echo "数量: {$grade1Semester2->count()}\n";

foreach ($grade1Semester2 as $catalog) {
    $versionName = $catalog->textbookVersion ? $catalog->textbookVersion->name : '无版本';
    echo "  - {$catalog->name} - 管理级别: {$levelNames[$catalog->management_level]} - 版本: {$versionName}\n";
}

echo "\n💡 如果学校级用户看不到数据，可能需要检查权限控制逻辑\n";
