<?php

require_once 'backend/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$client = new Client([
    'base_uri' => 'http://127.0.0.1:8000/api/',
    'timeout' => 30,
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ]
]);

function testDistrictAdminPermissions() {
    global $client;
    
    echo "🧪 测试学区级管理员权限\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    // 1. 登录学区管理员
    echo "1. 登录学区管理员...\n";
    try {
        $response = $client->post('auth/login', [
            'json' => [
                'username' => 'district_admin_test',
                'password' => 'password'
            ]
        ]);
        
        $loginData = json_decode($response->getBody(), true);
        if (!$loginData['success']) {
            echo "❌ 登录失败: " . $loginData['message'] . "\n";
            return;
        }
        
        $token = $loginData['data']['access_token'];
        $userInfo = $loginData['data']['user'];
        
        echo "✅ 登录成功\n";
        echo "   用户: {$userInfo['real_name']}\n";
        echo "   组织级别: {$userInfo['organization_level']}\n";
        echo "   组织ID: {$userInfo['organization_id']}\n";
        echo "   组织类型: {$userInfo['organization_type']}\n\n";
        
    } catch (RequestException $e) {
        echo "❌ 登录请求失败: " . $e->getMessage() . "\n";
        return;
    }
    
    // 设置认证头
    $authHeaders = [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];
    
    // 2. 测试用户管理权限
    echo "2. 测试用户管理权限...\n";
    try {
        $response = $client->get('users', [
            'headers' => $authHeaders
        ]);
        
        $userData = json_decode($response->getBody(), true);
        if ($userData['success']) {
            $users = $userData['data']['items'];
            echo "✅ 可访问用户数量: " . count($users) . "\n";
            
            // 显示用户详情
            foreach ($users as $user) {
                $orgInfo = '';
                if ($user['organization_type'] === 'school') {
                    $orgInfo = "学校ID: {$user['organization_id']}";
                } else {
                    $orgInfo = "区域ID: {$user['organization_id']}, 级别: {$user['organization_level']}";
                }
                echo "   - {$user['real_name']} ({$user['username']}) - {$orgInfo}\n";
            }
        } else {
            echo "❌ 获取用户列表失败: " . $userData['message'] . "\n";
        }
    } catch (RequestException $e) {
        echo "❌ 用户管理请求失败: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // 3. 测试组织信息管理权限
    echo "3. 测试组织信息管理权限...\n";
    try {
        $response = $client->get('administrative-regions', [
            'headers' => $authHeaders
        ]);
        
        $regionData = json_decode($response->getBody(), true);
        if ($regionData['success']) {
            $regions = $regionData['data']['items'];
            echo "✅ 可访问区域数量: " . count($regions) . "\n";
            
            // 显示区域详情
            foreach ($regions as $region) {
                echo "   - {$region['name']} (级别: {$region['level']}, ID: {$region['id']})\n";
            }
        } else {
            echo "❌ 获取区域列表失败: " . $regionData['message'] . "\n";
        }
    } catch (RequestException $e) {
        echo "❌ 组织管理请求失败: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // 4. 测试学校管理权限
    echo "4. 测试学校管理权限...\n";
    try {
        $response = $client->get('schools', [
            'headers' => $authHeaders
        ]);
        
        $schoolData = json_decode($response->getBody(), true);
        if ($schoolData['success']) {
            $schools = $schoolData['data']['items'];
            echo "✅ 可访问学校数量: " . count($schools) . "\n";
            
            // 显示学校详情
            foreach ($schools as $school) {
                echo "   - {$school['name']} (区域ID: {$school['region_id']})\n";
            }
        } else {
            echo "❌ 获取学校列表失败: " . $schoolData['message'] . "\n";
        }
    } catch (RequestException $e) {
        echo "❌ 学校管理请求失败: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    echo "🎯 测试完成\n";
}

// 运行测试
testDistrictAdminPermissions();
