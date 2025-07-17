<?php

/**
 * 调试设备API的具体错误
 */

require_once __DIR__ . '/backend/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// 创建Laravel应用实例
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

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
            'data' => $data,
            'content' => $content
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
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

// 测试省级管理员
echo "🚀 调试设备API访问问题\n";
echo str_repeat('=', 50) . "\n";

$loginData = loginUser('province_admin_test', 'password');
if (!$loginData) {
    echo "❌ 无法登录，退出测试\n";
    exit(1);
}

$token = $loginData['token'];
$headers = ['Authorization' => "Bearer {$token}"];

echo "\n📊 测试设备API详细响应:\n";

// 测试设备列表API
$equipmentResult = makeApiRequest('GET', '/api/equipments', [], $headers);

echo "状态码: {$equipmentResult['status']}\n";
echo "成功: " . ($equipmentResult['success'] ? 'true' : 'false') . "\n";
echo "响应内容:\n";
echo $equipmentResult['content'] . "\n";

if (isset($equipmentResult['error'])) {
    echo "错误信息: {$equipmentResult['error']}\n";
}

if (isset($equipmentResult['trace'])) {
    echo "错误堆栈:\n{$equipmentResult['trace']}\n";
}

// 检查设备数据
echo "\n📋 检查设备数据:\n";
try {
    $equipmentCount = \App\Models\Equipment::count();
    echo "设备总数: {$equipmentCount}\n";
    
    $schoolEquipment = \App\Models\Equipment::with('school')->take(5)->get();
    echo "前5个设备:\n";
    foreach ($schoolEquipment as $equipment) {
        echo "- ID: {$equipment->id}, 名称: {$equipment->name}, 学校: " . ($equipment->school->name ?? 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "❌ 查询设备数据失败: {$e->getMessage()}\n";
}

echo "\n🎉 调试完成!\n";
