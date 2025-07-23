<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "检查实验目录数据...\n\n";

// 检查实验目录总数
$totalCatalogs = DB::table('experiment_catalogs')->count();
echo "实验目录总数: {$totalCatalogs}\n\n";

// 检查有新字段数据的实验目录
$newFieldCatalogs = DB::table('experiment_catalogs')
    ->whereNotNull('grade_level')
    ->whereNotNull('volume')
    ->count();
echo "有新字段数据的实验目录: {$newFieldCatalogs}\n\n";

// 检查特定筛选条件的实验目录
echo "检查特定筛选条件的实验目录:\n";

// 假设用户选择了小学科学、教科版、一年级、下册
$testParams = [
    'subject_name' => '小学科学',
    'version_name' => '教科版', 
    'grade_level' => '1',
    'volume' => '下册'
];

// 先获取学科和教材版本ID
$subject = DB::table('subjects')->where('name', $testParams['subject_name'])->first();
$version = DB::table('textbook_versions')->where('name', $testParams['version_name'])->first();

if (!$subject) {
    echo "未找到学科: {$testParams['subject_name']}\n";
    return;
}

if (!$version) {
    echo "未找到教材版本: {$testParams['version_name']}\n";
    return;
}

echo "学科ID: {$subject->id}, 教材版本ID: {$version->id}\n";

// 查询匹配的实验目录
$matchingCatalogs = DB::table('experiment_catalogs')
    ->join('subjects', 'experiment_catalogs.subject_id', '=', 'subjects.id')
    ->leftJoin('textbook_versions', 'experiment_catalogs.textbook_version_id', '=', 'textbook_versions.id')
    ->where('experiment_catalogs.subject_id', $subject->id)
    ->where('experiment_catalogs.grade_level', $testParams['grade_level'])
    ->where('experiment_catalogs.volume', $testParams['volume'])
    ->select(
        'experiment_catalogs.*',
        'subjects.name as subject_name',
        'textbook_versions.name as version_name'
    )
    ->get();

echo "匹配条件的实验目录数: " . $matchingCatalogs->count() . "\n";

if ($matchingCatalogs->count() > 0) {
    echo "\n匹配的实验目录:\n";
    foreach ($matchingCatalogs as $catalog) {
        echo "  - ID: {$catalog->id}, 名称: {$catalog->name}\n";
        echo "    学科: {$catalog->subject_name}, 版本: {$catalog->version_name}\n";
        echo "    年级: {$catalog->grade_level}, 册次: {$catalog->volume}\n";
        echo "    教材版本ID: {$catalog->textbook_version_id}\n\n";
    }
} else {
    echo "\n没有找到匹配的实验目录，检查可能的原因:\n";
    
    // 检查是否有该学科的实验目录
    $subjectCatalogs = DB::table('experiment_catalogs')
        ->where('subject_id', $subject->id)
        ->count();
    echo "该学科的实验目录总数: {$subjectCatalogs}\n";
    
    // 检查是否有该年级的实验目录
    $gradeCatalogs = DB::table('experiment_catalogs')
        ->where('subject_id', $subject->id)
        ->where('grade_level', $testParams['grade_level'])
        ->count();
    echo "该学科该年级的实验目录数: {$gradeCatalogs}\n";
    
    // 检查是否有该册次的实验目录
    $volumeCatalogs = DB::table('experiment_catalogs')
        ->where('subject_id', $subject->id)
        ->where('grade_level', $testParams['grade_level'])
        ->where('volume', $testParams['volume'])
        ->count();
    echo "该学科该年级该册次的实验目录数: {$volumeCatalogs}\n";
    
    // 显示该学科的所有实验目录
    echo "\n该学科的所有实验目录:\n";
    $allSubjectCatalogs = DB::table('experiment_catalogs')
        ->where('subject_id', $subject->id)
        ->select('id', 'name', 'grade_level', 'volume', 'textbook_version_id')
        ->get();
    
    foreach ($allSubjectCatalogs as $catalog) {
        echo "  - ID: {$catalog->id}, 名称: {$catalog->name}, 年级: {$catalog->grade_level}, 册次: {$catalog->volume}, 版本ID: {$catalog->textbook_version_id}\n";
    }
}
