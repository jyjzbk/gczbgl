<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== 角色菜单权限测试 ===\n\n";

// 测试用户
$testUsers = [
    'teacher001' => 'school_teacher',
    'student001' => 'school_student',
    'test_admin' => 'province_admin'
];

foreach ($testUsers as $username => $expectedRole) {
    $user = User::where('username', $username)->first();
    
    if (!$user) {
        echo "用户 {$username} 不存在\n\n";
        continue;
    }
    
    echo "用户: {$username} (角色: {$user->role})\n";
    echo "权限: " . implode(', ', $user->getPermissions()) . "\n";
    
    // 测试一级菜单权限
    echo "\n一级菜单权限:\n";
    $permissions = $user->getPermissions();
    
    // 用户管理
    $hasUserPermission = array_intersect(['user', 'user.list', 'role', 'role.list'], $permissions);
    echo "  用户管理: " . (!empty($hasUserPermission) ? '✅' : '❌') . "\n";
    
    // 基础数据
    $hasBasicDataPermission = array_intersect(['user', 'user.list', 'user.create'], $permissions);
    echo "  基础数据: " . (!empty($hasBasicDataPermission) ? '✅' : '❌') . "\n";
    
    // 实验管理
    $hasExperimentPermission = array_intersect(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'], $permissions);
    echo "  实验管理: " . (!empty($hasExperimentPermission) ? '✅' : '❌') . "\n";
    
    // 设备管理
    $hasEquipmentPermission = array_intersect(['equipment', 'equipment.list'], $permissions);
    echo "  设备管理: " . (!empty($hasEquipmentPermission) ? '✅' : '❌') . "\n";
    
    // 统计报表
    $hasStatisticsPermission = !empty(array_intersect(['experiment', 'experiment.catalog', 'experiment.record'], $permissions)) ||
                              !empty(array_intersect(['equipment', 'equipment.list'], $permissions)) ||
                              !empty(array_intersect(['user', 'user.list'], $permissions));
    echo "  统计报表: " . ($hasStatisticsPermission ? '✅' : '❌') . "\n";
    
    // 系统管理
    $hasSystemPermission = array_intersect(['system', 'system.read', 'log', 'log.read'], $permissions);
    echo "  系统管理: " . (!empty($hasSystemPermission) ? '✅' : '❌') . "\n";
    
    // 测试二级菜单权限
    if (!empty($hasExperimentPermission)) {
        echo "\n实验管理子菜单:\n";
        echo "    实验目录: " . (in_array('experiment', $permissions) || in_array('experiment.catalog', $permissions) ? '✅' : '❌') . "\n";
        echo "    实验预约: " . (in_array('experiment.booking', $permissions) ? '✅' : '❌') . "\n";
        echo "    实验记录: " . (in_array('experiment.record', $permissions) ? '✅' : '❌') . "\n";
        echo "    实验统计: " . (in_array('experiment', $permissions) || in_array('experiment.catalog', $permissions) || in_array('experiment.record', $permissions) ? '✅' : '❌') . "\n";
    }
    
    if (!empty($hasEquipmentPermission)) {
        echo "\n设备管理子菜单:\n";
        echo "    设备档案: " . (in_array('equipment', $permissions) || in_array('equipment.list', $permissions) ? '✅' : '❌') . "\n";
        echo "    设备借用: " . (in_array('equipment.borrow', $permissions) ? '✅' : '❌') . "\n";
        echo "    设备维修: " . (in_array('equipment.maintenance', $permissions) ? '✅' : '❌') . "\n";
        echo "    二维码管理: " . (in_array('equipment', $permissions) || in_array('equipment.list', $permissions) ? '✅' : '❌') . "\n";
    }
    
    if ($hasStatisticsPermission) {
        echo "\n统计报表子菜单:\n";
        echo "    实验统计: " . (in_array('experiment', $permissions) || in_array('experiment.catalog', $permissions) ? '✅' : '❌') . "\n";
        echo "    设备统计: " . (in_array('equipment', $permissions) || in_array('equipment.list', $permissions) ? '✅' : '❌') . "\n";
        echo "    区域分析: " . (in_array('user', $permissions) || in_array('user.list', $permissions) ? '✅' : '❌') . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "测试完成！\n";
