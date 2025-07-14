<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Services\PermissionService;

// 启动Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permissionService = new PermissionService();

echo "=== 权限测试 ===\n\n";

$testUsers = ['school_admin', 'student001', 'admin'];

foreach ($testUsers as $username) {
    $user = User::where('username', $username)->first();
    if (!$user) {
        echo "用户 {$username} 不存在\n";
        continue;
    }

    echo "用户: {$username} (ID: {$user->id})\n";
    echo "角色字段: {$user->role}\n";
    
    // 获取用户角色关联
    $roles = $user->roles()->with('permissions')->get();
    echo "关联角色数量: " . $roles->count() . "\n";
    
    foreach ($roles as $role) {
        echo "  - 角色: {$role->name} (code: {$role->code})\n";
        echo "    权限数量: " . $role->permissions->count() . "\n";
        foreach ($role->permissions as $perm) {
            echo "      * {$perm->permission_code}\n";
        }
    }
    
    // 获取用户权限
    $permissions = $permissionService->getUserPermissions($user);
    echo "用户权限: " . implode(', ', $permissions) . "\n";
    
    // 测试具体权限
    $testPermissions = ['equipment', 'equipment.list', 'equipment.create'];
    foreach ($testPermissions as $perm) {
        $hasPermission = $permissionService->hasPermission($user, $perm);
        echo "  {$perm}: " . ($hasPermission ? '✅' : '❌') . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}
