<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "👥 检查系统用户...\n\n";

$users = User::select('username', 'organization_level')->get();

foreach($users as $user) {
    $levelName = match($user->organization_level) {
        1 => '省级',
        2 => '市级', 
        3 => '区县级',
        4 => '学区级',
        5 => '学校级',
        default => '未知'
    };
    
    echo "{$user->username} ({$levelName})\n";
}

echo "\n总用户数: {$users->count()}\n";
