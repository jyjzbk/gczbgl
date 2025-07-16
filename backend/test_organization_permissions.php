<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Equipment;
use App\Services\PermissionService;
use Illuminate\Foundation\Application;

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$permissionService = new PermissionService();

echo "=== 组织层级权限控制测试 ===\n\n";

// 获取测试用户
$users = [
    'province_admin_test' => '省级管理员',
    'city_admin_test' => '市级管理员', 
    'county_admin_test' => '区县管理员',
    'district_admin_test' => '学区管理员',
    'school_admin_test' => '学校管理员'
];

foreach ($users as $username => $title) {
    $user = User::where('username', $username)->first();
    if (!$user) {
        echo "用户 {$username} 不存在\n";
        continue;
    }

    echo "=== {$title} ({$username}) ===\n";
    echo "组织级别: {$user->organization_level}\n";
    echo "组织类型: {$user->organization_type}\n";
    echo "组织ID: {$user->organization_id}\n\n";

    // 获取数据访问范围
    $dataScope = $permissionService->getUserDataScope($user);
    echo "数据访问范围:\n";
    echo "- 类型: {$dataScope['type']}\n";
    echo "- 可访问学校数量: " . count($dataScope['school_ids']) . "\n";
    echo "- 可访问区域数量: " . count($dataScope['region_ids']) . "\n";

    if (!empty($dataScope['school_ids'])) {
        echo "- 可访问学校ID: " . implode(', ', $dataScope['school_ids']) . "\n";
    }

    // 测试设备访问权限
    $equipments = Equipment::with('school')->get();
    $accessibleCount = 0;
    
    foreach ($equipments as $equipment) {
        if ($permissionService->canAccessSchool($user, $equipment->school_id)) {
            $accessibleCount++;
        }
    }

    echo "- 可访问设备数量: {$accessibleCount} / " . $equipments->count() . "\n";

    // 显示可访问的学校名称
    if (!empty($dataScope['school_ids'])) {
        $schools = \App\Models\School::whereIn('id', $dataScope['school_ids'])->get();
        echo "- 可管理学校: ";
        foreach ($schools as $school) {
            echo $school->name . " ";
        }
        echo "\n";
    }

    echo "\n";
}

echo "=== 设备分布情况 ===\n";
$schools = \App\Models\School::with('equipments')->get();
foreach ($schools as $school) {
    echo "{$school->name}: {$school->equipments->count()} 台设备\n";
}

echo "\n=== 测试完成 ===\n";
