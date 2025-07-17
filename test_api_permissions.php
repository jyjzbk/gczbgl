<?php

/**
 * API权限控制测试脚本
 * 测试组织层级权限控制系统的API接口
 */

require_once __DIR__ . '/backend/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// 创建Laravel应用实例
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 测试用户配置
$testUsers = [
    [
        'username' => 'province_admin_test',
        'password' => 'password',
        'expectedLevel' => 1,
        'description' => '省级管理员'
    ],
    [
        'username' => 'city_admin_test',
        'password' => 'password',
        'expectedLevel' => 2,
        'description' => '市级管理员'
    ],
    [
        'username' => 'county_admin_test',
        'password' => 'password',
        'expectedLevel' => 3,
        'description' => '区县管理员'
    ],
    [
        'username' => 'district_admin_test',
        'password' => 'password',
        'expectedLevel' => 4,
        'description' => '学区管理员'
    ],
    [
        'username' => 'school_admin_test',
        'password' => 'password',
        'expectedLevel' => 5,
        'description' => '学校管理员'
    ]
];

/**
 * 发送API请求
 */
function makeApiRequest($method, $uri, $data = [], $headers = []) {
    global $app;
    
    $request = Request::create($uri, $method, $data, [], [], [], json_encode($data));
    
    // 设置请求头
    foreach ($headers as $key => $value) {
        $request->headers->set($key, $value);
    }
    
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    try {
        $response = $app->handle($request);
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        return [
            'success' => $response->getStatusCode() < 400,
            'status' => $response->getStatusCode(),
            'data' => $data
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * 用户登录
 */
function loginUser($username, $password) {
    echo "🔐 正在登录用户: {$username}\n";
    
    $result = makeApiRequest('POST', '/api/auth/login', [
        'username' => $username,
        'password' => $password
    ]);
    
    if ($result['success'] && isset($result['data']['success']) && $result['data']['success']) {
        echo "✅ 登录成功\n";
        return $result['data']['data'];
    } else {
        echo "❌ 登录失败: " . ($result['data']['message'] ?? $result['error'] ?? 'Unknown error') . "\n";
        return null;
    }
}

/**
 * 测试API权限
 */
function testApiPermissions($token, $userLevel, $description) {
    echo "\n📊 测试API权限: {$description} (级别: {$userLevel})\n";
    echo str_repeat('-', 50) . "\n";
    
    $headers = ['Authorization' => "Bearer {$token}"];
    $results = [];
    
    // 测试用户列表API
    $usersResult = makeApiRequest('GET', '/api/users', [], $headers);
    $userCount = $usersResult['success'] && isset($usersResult['data']['data']) ? count($usersResult['data']['data']) : 0;
    echo "  用户列表API: " . ($usersResult['success'] ? "✅ 可访问 ({$userCount}条数据)" : "❌ 无权限") . "\n";
    $results['users'] = $userCount;
    
    // 测试学校列表API
    $schoolsResult = makeApiRequest('GET', '/api/schools', [], $headers);
    $schoolCount = $schoolsResult['success'] && isset($schoolsResult['data']['data']) ? count($schoolsResult['data']['data']) : 0;
    echo "  学校列表API: " . ($schoolsResult['success'] ? "✅ 可访问 ({$schoolCount}条数据)" : "❌ 无权限") . "\n";
    $results['schools'] = $schoolCount;
    
    // 测试设备列表API
    $equipmentResult = makeApiRequest('GET', '/api/equipments', [], $headers);
    $equipmentCount = $equipmentResult['success'] && isset($equipmentResult['data']['data']) ? count($equipmentResult['data']['data']) : 0;
    echo "  设备列表API: " . ($equipmentResult['success'] ? "✅ 可访问 ({$equipmentCount}条数据)" : "❌ 无权限") . "\n";
    $results['equipment'] = $equipmentCount;
    
    // 测试实验列表API
    $experimentsResult = makeApiRequest('GET', '/api/experiment-catalogs', [], $headers);
    $experimentCount = $experimentsResult['success'] && isset($experimentsResult['data']['data']) ? count($experimentsResult['data']['data']) : 0;
    echo "  实验目录API: " . ($experimentsResult['success'] ? "✅ 可访问 ({$experimentCount}条数据)" : "❌ 无权限") . "\n";
    $results['experiments'] = $experimentCount;
    
    return $results;
}

/**
 * 验证用户组织信息
 */
function validateUserOrganization($user, $expectedLevel) {
    echo "\n🏢 验证组织信息:\n";
    echo "  用户名: {$user['username']}\n";
    echo "  真实姓名: {$user['real_name']}\n";
    echo "  角色: {$user['role']}\n";
    echo "  组织级别: " . ($user['organization_level'] ?? '未设置') . " (期望: {$expectedLevel})\n";
    echo "  组织类型: " . ($user['organization_type'] ?? '未设置') . "\n";
    echo "  组织名称: " . ($user['organization_name'] ?? $user['school_name'] ?? '未设置') . "\n";
    echo "  权限数量: " . (isset($user['permissions']) ? count($user['permissions']) : 0) . "\n";
    
    $levelMatch = isset($user['organization_level']) && $user['organization_level'] == $expectedLevel;
    echo "  级别匹配: " . ($levelMatch ? '✅ 正确' : '❌ 错误') . "\n";
    
    return $levelMatch;
}

/**
 * 主测试函数
 */
function runApiPermissionTest() {
    global $testUsers;
    
    echo "🚀 开始API权限控制测试\n";
    echo str_repeat('=', 60) . "\n";
    
    $results = [];
    
    foreach ($testUsers as $testUser) {
        echo "\n📋 测试用户: {$testUser['description']} ({$testUser['username']})\n";
        echo str_repeat('=', 60) . "\n";
        
        // 登录
        $loginData = loginUser($testUser['username'], $testUser['password']);
        if (!$loginData) {
            $results[] = [
                'user' => $testUser['username'],
                'success' => false,
                'error' => '登录失败'
            ];
            continue;
        }
        
        // 验证组织信息
        $orgValid = validateUserOrganization($loginData['user'], $testUser['expectedLevel']);
        
        // 测试API权限
        $apiResults = testApiPermissions(
            $loginData['token'], 
            $testUser['expectedLevel'], 
            $testUser['description']
        );
        
        $results[] = [
            'user' => $testUser['username'],
            'description' => $testUser['description'],
            'success' => true,
            'organizationValid' => $orgValid,
            'apiResults' => $apiResults
        ];
    }
    
    // 输出测试总结
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "📊 API权限测试结果总结\n";
    echo str_repeat('=', 60) . "\n";
    
    foreach ($results as $result) {
        if ($result['success']) {
            echo "\n{$result['description']} ({$result['user']}):\n";
            echo "  组织信息: " . ($result['organizationValid'] ? '✅ 正确' : '❌ 错误') . "\n";
            echo "  用户数据: {$result['apiResults']['users']} 条\n";
            echo "  学校数据: {$result['apiResults']['schools']} 条\n";
            echo "  设备数据: {$result['apiResults']['equipment']} 条\n";
            echo "  实验数据: {$result['apiResults']['experiments']} 条\n";
        } else {
            echo "\n{$result['user']}: ❌ {$result['error']}\n";
        }
    }
    
    echo "\n🎉 API权限测试完成!\n";
    
    return $results;
}

// 运行测试
if (php_sapi_name() === 'cli') {
    runApiPermissionTest();
} else {
    echo "请在命令行环境中运行此脚本\n";
}
