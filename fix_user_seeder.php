<?php

// 修复用户种子文件中的字段名
$file = 'backend/database/seeders/UserSeeder.php';
$content = file_get_contents($file);

// 替换所有的 User::STATUS_ACTIVE 为 1
$content = str_replace('User::STATUS_ACTIVE', '1', $content);

// 替换所有的 user_type 为 role
$content = str_replace("'user_type'", "'role'", $content);

file_put_contents($file, $content);
echo "用户种子文件修复完成！\n";
