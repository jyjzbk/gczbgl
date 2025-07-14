<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Services\PermissionService;

// 启动Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== 测试登录权限返回 ===\n\n";

$testUsers = ['school_admin', 'student001', 'admin'];

foreach ($testUsers as $username) {
    $user = User::where('username', $username)->first();
    if (!$user) {
        echo "用户 {$username} 不存在\n";
        continue;
    }

    echo "用户: {$username}\n";
    echo "角色: {$user->role}\n";
    
    // 模拟登录API返回的数据
    $permissions = $user->getPermissions();
    $roles = $user->roles()->with('permissions')->get();
    $roleInfo = $roles->map(function ($role) {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'code' => $role->code,
            'level' => $role->level,
            'description' => $role->description
        ];
    });

    echo "权限: " . implode(', ', $permissions) . "\n";
    echo "角色信息: " . json_encode($roleInfo, JSON_UNESCAPED_UNICODE) . "\n";
    
    // 测试关键权限
    $keyPermissions = ['user', 'user.list', 'role', 'role.list', 'equipment', 'equipment.list'];
    foreach ($keyPermissions as $perm) {
        $hasPermission = in_array($perm, $permissions);
        echo "  {$perm}: " . ($hasPermission ? '✅' : '❌') . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}
