<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Equipment;
use App\Models\School;
use App\Models\AdministrativeRegion;
use Illuminate\Foundation\Application;

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 测试环境设置检查 ===\n\n";

// 检查测试用户
echo "1. 检查测试用户...\n";
$testUsers = [
    'province_admin_test' => '省级管理员',
    'city_admin_test' => '市级管理员',
    'county_admin_test' => '区县管理员',
    'district_admin_test' => '学区管理员',
    'school_admin_test' => '学校管理员'
];

$allUsersExist = true;
foreach ($testUsers as $username => $title) {
    $user = User::where('username', $username)->first();
    if ($user) {
        echo "   ✅ {$title} ({$username}) - 组织级别: {$user->organization_level}\n";
    } else {
        echo "   ❌ {$title} ({$username}) - 不存在\n";
        $allUsersExist = false;
    }
}

if (!$allUsersExist) {
    echo "\n请运行以下命令创建测试用户：\n";
    echo "php artisan db:seed --class=OrganizationPermissionSeeder\n\n";
}

// 检查组织架构
echo "\n2. 检查组织架构...\n";
$regions = AdministrativeRegion::orderBy('level')->orderBy('sort_order')->get();
if ($regions->count() > 0) {
    foreach ($regions as $region) {
        $indent = str_repeat('  ', $region->level - 1);
        echo "   {$indent}📍 {$region->name} (Level {$region->level})\n";
    }
} else {
    echo "   ❌ 组织架构数据不存在\n";
    echo "   请运行: php artisan db:seed --class=OrganizationPermissionSeeder\n";
}

// 检查学校数据
echo "\n3. 检查学校数据...\n";
$schools = School::with('region')->orderBy('level')->get();
if ($schools->count() > 0) {
    foreach ($schools as $school) {
        $levelName = ['', '省直', '市直', '区县直', '学区'][$school->level] ?? '未知';
        echo "   🏫 {$school->name} ({$levelName}) - 区域: {$school->region->name}\n";
    }
} else {
    echo "   ❌ 学校数据不存在\n";
    echo "   请运行: php artisan db:seed --class=OrganizationPermissionSeeder\n";
}

// 检查设备数据
echo "\n4. 检查设备数据...\n";
$equipments = Equipment::with('school')->get();
if ($equipments->count() > 0) {
    $schoolEquipmentCount = [];
    foreach ($equipments as $equipment) {
        $schoolName = $equipment->school->name;
        if (!isset($schoolEquipmentCount[$schoolName])) {
            $schoolEquipmentCount[$schoolName] = 0;
        }
        $schoolEquipmentCount[$schoolName]++;
    }
    
    foreach ($schoolEquipmentCount as $schoolName => $count) {
        echo "   📦 {$schoolName}: {$count} 台设备\n";
    }
    echo "   总计: {$equipments->count()} 台设备\n";
} else {
    echo "   ❌ 设备数据不存在\n";
    echo "   请运行: php artisan db:seed --class=TestEquipmentSeeder\n";
}

// 检查权限配置
echo "\n5. 检查权限配置...\n";
foreach ($testUsers as $username => $title) {
    $user = User::where('username', $username)->first();
    if ($user) {
        $permissionService = app(\App\Services\PermissionService::class);
        $dataScope = $permissionService->getUserDataScope($user);
        $schoolCount = count($dataScope['school_ids']);
        $regionCount = count($dataScope['region_ids']);
        echo "   👤 {$title}: 可管理 {$schoolCount} 所学校, {$regionCount} 个区域\n";
    }
}

// 生成测试URL
echo "\n6. 测试访问信息...\n";
echo "   前端地址: http://localhost:5173\n";
echo "   后端地址: http://localhost:8000\n";
echo "   API基础地址: http://localhost:8000/api/v1\n";

echo "\n7. 测试用户登录信息...\n";
foreach ($testUsers as $username => $title) {
    echo "   {$title}:\n";
    echo "     用户名: {$username}\n";
    echo "     密码: password\n";
}

echo "\n=== 环境检查完成 ===\n";

if ($allUsersExist && $regions->count() > 0 && $schools->count() > 0 && $equipments->count() > 0) {
    echo "✅ 测试环境已就绪，可以开始网页测试！\n";
    echo "\n请按照 docs/WEB_TESTING_GUIDE.md 中的步骤进行测试。\n";
} else {
    echo "❌ 测试环境未完全准备好，请按照上述提示完成数据初始化。\n";
}

echo "\n快速命令：\n";
echo "# 创建所有测试数据\n";
echo "php artisan db:seed --class=OrganizationPermissionSeeder\n";
echo "php artisan db:seed --class=TestEquipmentSeeder\n";
echo "\n# 启动服务\n";
echo "php artisan serve  # 后端服务\n";
echo "cd ../frontend && npm run dev  # 前端服务\n";
