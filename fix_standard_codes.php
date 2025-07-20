<?php

// 修复种子文件中重复的标准代码
$file = 'backend/database/seeders/TeachingEquipmentStandardSeeder.php';
$content = file_get_contents($file);

// 定义替换规则
$replacements = [
    // 小学科学
    "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023'," => [
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_001',",
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_002',",
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_003',",
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_004',",
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_005',",
        "'standard_code' => 'MOE_PRIMARY_SCIENCE_2023_006',"
    ],
    // 初中物理
    "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023'," => [
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_001',",
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_002',",
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_003',",
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_004',",
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_005',",
        "'standard_code' => 'MOE_JUNIOR_PHYSICS_2023_006',"
    ],
    // 初中化学
    "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023'," => [
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_001',",
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_002',",
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_003',",
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_004',",
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_005',",
        "'standard_code' => 'MOE_JUNIOR_CHEMISTRY_2023_006',"
    ],
    // 初中生物
    "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023'," => [
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_001',",
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_002',",
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_003',",
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_004',",
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_005',",
        "'standard_code' => 'MOE_JUNIOR_BIOLOGY_2023_006',"
    ],
    // 高中物理
    "'standard_code' => 'MOE_SENIOR_PHYSICS_2023'," => [
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_001',",
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_002',",
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_003',",
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_004',",
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_005',",
        "'standard_code' => 'MOE_SENIOR_PHYSICS_2023_006',"
    ],
    // 高中化学
    "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023'," => [
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_001',",
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_002',",
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_003',",
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_004',",
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_005',",
        "'standard_code' => 'MOE_SENIOR_CHEMISTRY_2023_006',"
    ],
    // 高中生物
    "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023'," => [
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_001',",
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_002',",
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_003',",
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_004',",
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_005',",
        "'standard_code' => 'MOE_SENIOR_BIOLOGY_2023_006',"
    ]
];

foreach ($replacements as $search => $replaceArray) {
    $count = 0;
    foreach ($replaceArray as $replace) {
        $content = preg_replace('/' . preg_quote($search, '/') . '/', $replace, $content, 1);
        $count++;
    }
    echo "替换了 $count 个 $search\n";
}

file_put_contents($file, $content);
echo "修复完成！\n";
