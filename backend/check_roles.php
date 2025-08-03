<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;

echo "所有角色列表:\n";
$roles = Role::all(['id', 'name', 'code']);
foreach ($roles as $role) {
    echo "{$role->id} - {$role->code} - {$role->name}\n";
}

echo "\n查找任课教师相关角色:\n";
$teacherRoles = Role::where('code', 'like', '%teacher%')
    ->orWhere('name', 'like', '%教师%')
    ->get(['id', 'name', 'code']);

foreach ($teacherRoles as $role) {
    echo "{$role->id} - {$role->code} - {$role->name}\n";
    
    // 检查权限
    $permissions = $role->permissions()->pluck('code')->toArray();
    echo "  权限数量: " . count($permissions) . "\n";
    echo "  是否有教材章节查看权限: " . (in_array('basic.textbook_chapter.view', $permissions) ? 'YES' : 'NO') . "\n";
    echo "\n";
}
