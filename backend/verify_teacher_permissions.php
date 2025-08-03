<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "验证任课教师权限:\n\n";

// 查找任课教师角色
$teacherRole = DB::table('roles')->where('code', 'school_teacher')->first();
if (!$teacherRole) {
    echo "未找到任课教师角色\n";
    exit;
}

echo "角色信息: ID={$teacherRole->id}, Name={$teacherRole->name}, Code={$teacherRole->code}\n\n";

// 获取角色权限
$permissions = DB::table('role_permissions')
    ->where('role_id', $teacherRole->id)
    ->pluck('permission_code')
    ->toArray();

echo "权限总数: " . count($permissions) . "\n\n";

// 检查关键权限
$keyPermissions = [
    'basic.textbook_version.view',
    'basic.textbook_chapter.view',
    'basic.textbook_chapter.tree',
    'experiment.catalog.view',
    'experiment.booking.view',
    'experiment.booking.create',
    'equipment.view'
];

echo "关键权限检查:\n";
foreach ($keyPermissions as $permission) {
    $hasPermission = in_array($permission, $permissions);
    echo "  {$permission}: " . ($hasPermission ? '✓' : '✗') . "\n";
}

echo "\n所有权限列表:\n";
sort($permissions);
foreach ($permissions as $permission) {
    echo "  - {$permission}\n";
}
