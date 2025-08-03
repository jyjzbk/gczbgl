<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "检查数据库表结构:\n\n";

// 检查roles表
echo "Roles表字段:\n";
$roleColumns = Schema::getColumnListing('roles');
foreach ($roleColumns as $column) {
    echo "  - $column\n";
}

echo "\n所有角色:\n";
$roles = DB::table('roles')->get();
foreach ($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}";
    if (isset($role->code)) {
        echo ", Code: {$role->code}";
    }
    echo "\n";
}

// 检查permissions表
echo "\nPermissions表字段:\n";
if (Schema::hasTable('permissions')) {
    $permissionColumns = Schema::getColumnListing('permissions');
    foreach ($permissionColumns as $column) {
        echo "  - $column\n";
    }
} else {
    echo "permissions表不存在\n";
}

// 检查role_permissions表
echo "\nRole_permissions表字段:\n";
if (Schema::hasTable('role_permissions')) {
    $rpColumns = Schema::getColumnListing('role_permissions');
    foreach ($rpColumns as $column) {
        echo "  - $column\n";
    }
} else {
    echo "role_permissions表不存在\n";
}

// 查找任课教师角色
echo "\n查找任课教师角色:\n";
$teacherRoles = DB::table('roles')
    ->where('name', 'like', '%教师%')
    ->orWhere('name', 'like', '%teacher%')
    ->get();

foreach ($teacherRoles as $role) {
    echo "找到角色: ID={$role->id}, Name={$role->name}\n";
}
