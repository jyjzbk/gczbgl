<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Middleware\DataScopeMiddleware;

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 组织API测试 ===\n\n";

$testUsers = [
    'province_admin_test' => '省级管理员',
    'city_admin_test' => '市级管理员',
    'county_admin_test' => '区县管理员'
];

foreach ($testUsers as $username => $title) {
    echo "测试用户: {$title} ({$username})\n";
    
    $user = User::where('username', $username)->first();
    if (!$user) {
        echo "   ❌ 用户不存在\n\n";
        continue;
    }
    
    // 模拟登录
    auth()->login($user);
    
    // 创建请求对象并应用数据权限中间件
    $request = new Request();
    $middleware = new DataScopeMiddleware(app(\App\Services\PermissionService::class));
    $middleware->handle($request, function($req) {
        return response()->json(['success' => true]);
    });
    
    $controller = new OrganizationController(app(\App\Services\PermissionService::class));
    
    // 测试获取可管理的组织
    echo "   测试获取可管理的组织:\n";
    
    for ($level = 2; $level <= 4; $level++) {
        $orgRequest = new Request(['level' => $level]);
        $orgRequest->merge($request->all()); // 复制数据权限信息
        
        try {
            $response = $controller->getManageableOrganizations($orgRequest);
            $data = json_decode($response->getContent(), true);
            
            if ($data['success']) {
                $count = count($data['data']);
                $levelName = ['', '', '市级', '区县级', '学区级'][$level] ?? "Level {$level}";
                echo "     {$levelName}组织: {$count} 个\n";
                
                foreach ($data['data'] as $org) {
                    echo "       - {$org['name']} (ID: {$org['id']})\n";
                }
            } else {
                echo "     Level {$level}: 获取失败\n";
            }
        } catch (Exception $e) {
            echo "     Level {$level}: 异常 - " . $e->getMessage() . "\n";
        }
    }
    
    // 测试获取可管理的学校
    echo "   测试获取可管理的学校:\n";
    try {
        $schoolRequest = new Request();
        $schoolRequest->merge($request->all()); // 复制数据权限信息
        
        $response = $controller->getManageableSchools($schoolRequest);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            $count = count($data['data']);
            echo "     可管理学校: {$count} 所\n";
            
            foreach ($data['data'] as $school) {
                $levelName = ['', '省直', '市直', '区县直', '学区'][$school['level']] ?? '未知';
                echo "       - {$school['name']} ({$levelName})\n";
            }
        } else {
            echo "     获取学校失败\n";
        }
    } catch (Exception $e) {
        echo "     获取学校异常: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== 测试完成 ===\n";
