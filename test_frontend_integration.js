/**
 * 前后端集成测试脚本
 * 测试组织层级权限控制系统的前端集成
 */

const API_BASE_URL = 'http://127.0.0.1:8000/api';

// 测试用户账号
const TEST_USERS = [
    {
        username: 'province_admin_test',
        password: 'password',
        expectedLevel: 1,
        description: '省级管理员'
    },
    {
        username: 'city_admin_test',
        password: 'password',
        expectedLevel: 2,
        description: '市级管理员'
    },
    {
        username: 'county_admin_test',
        password: 'password',
        expectedLevel: 3,
        description: '区县管理员'
    },
    {
        username: 'district_admin_test',
        password: 'password',
        expectedLevel: 4,
        description: '学区管理员'
    },
    {
        username: 'school_admin_test',
        password: 'password',
        expectedLevel: 5,
        description: '学校管理员'
    }
];

/**
 * 发送HTTP请求
 */
async function makeRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        const data = await response.json();
        return { success: response.ok, status: response.status, data };
    } catch (error) {
        return { success: false, error: error.message };
    }
}

/**
 * 用户登录
 */
async function login(username, password) {
    console.log(`\n🔐 正在登录用户: ${username}`);
    
    const result = await makeRequest(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        body: JSON.stringify({ username, password })
    });
    
    if (result.success && result.data.success) {
        console.log(`✅ 登录成功`);
        return result.data.data;
    } else {
        console.log(`❌ 登录失败:`, result.data?.message || result.error);
        return null;
    }
}

/**
 * 获取用户信息
 */
async function getUserInfo(token) {
    const result = await makeRequest(`${API_BASE_URL}/auth/me`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    
    if (result.success && result.data.success) {
        return result.data.data;
    } else {
        console.log(`❌ 获取用户信息失败:`, result.data?.message || result.error);
        return null;
    }
}

/**
 * 测试API权限
 */
async function testAPIPermissions(token, userLevel) {
    console.log(`\n📊 测试API权限 (级别: ${userLevel})`);
    
    // 测试用户列表API
    const usersResult = await makeRequest(`${API_BASE_URL}/users`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    
    console.log(`  用户列表API: ${usersResult.success ? '✅ 可访问' : '❌ 无权限'}`);
    if (usersResult.success && usersResult.data.data) {
        console.log(`    返回用户数量: ${usersResult.data.data.length}`);
    }
    
    // 测试学校列表API
    const schoolsResult = await makeRequest(`${API_BASE_URL}/schools`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    
    console.log(`  学校列表API: ${schoolsResult.success ? '✅ 可访问' : '❌ 无权限'}`);
    if (schoolsResult.success && schoolsResult.data.data) {
        console.log(`    返回学校数量: ${schoolsResult.data.data.length}`);
    }
    
    // 测试设备列表API
    const equipmentResult = await makeRequest(`${API_BASE_URL}/equipments`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    
    console.log(`  设备列表API: ${equipmentResult.success ? '✅ 可访问' : '❌ 无权限'}`);
    if (equipmentResult.success && equipmentResult.data.data) {
        console.log(`    返回设备数量: ${equipmentResult.data.data.length}`);
    }
    
    return {
        users: usersResult.success ? usersResult.data.data?.length || 0 : 0,
        schools: schoolsResult.success ? schoolsResult.data.data?.length || 0 : 0,
        equipment: equipmentResult.success ? equipmentResult.data.data?.length || 0 : 0
    };
}

/**
 * 验证用户组织信息
 */
function validateUserOrganization(user, expectedLevel) {
    console.log(`\n🏢 验证组织信息:`);
    console.log(`  用户名: ${user.username}`);
    console.log(`  真实姓名: ${user.real_name}`);
    console.log(`  角色: ${user.role}`);
    console.log(`  组织级别: ${user.organization_level} (期望: ${expectedLevel})`);
    console.log(`  组织类型: ${user.organization_type}`);
    console.log(`  组织名称: ${user.organization_name || user.school_name || '未设置'}`);
    console.log(`  权限数量: ${user.permissions?.length || 0}`);
    
    const levelMatch = user.organization_level === expectedLevel;
    console.log(`  级别匹配: ${levelMatch ? '✅' : '❌'}`);
    
    return levelMatch;
}

/**
 * 主测试函数
 */
async function runIntegrationTest() {
    console.log('🚀 开始前后端集成测试\n');
    console.log('=' .repeat(60));
    
    const results = [];
    
    for (const testUser of TEST_USERS) {
        console.log(`\n📋 测试用户: ${testUser.description} (${testUser.username})`);
        console.log('-'.repeat(50));
        
        // 登录
        const loginData = await login(testUser.username, testUser.password);
        if (!loginData) {
            results.push({
                user: testUser.username,
                success: false,
                error: '登录失败'
            });
            continue;
        }
        
        // 获取用户信息
        const userInfo = await getUserInfo(loginData.token);
        if (!userInfo) {
            results.push({
                user: testUser.username,
                success: false,
                error: '获取用户信息失败'
            });
            continue;
        }
        
        // 验证组织信息
        const orgValid = validateUserOrganization(userInfo, testUser.expectedLevel);
        
        // 测试API权限
        const apiResults = await testAPIPermissions(loginData.token, testUser.expectedLevel);
        
        results.push({
            user: testUser.username,
            description: testUser.description,
            success: true,
            organizationValid: orgValid,
            apiResults
        });
    }
    
    // 输出测试总结
    console.log('\n' + '='.repeat(60));
    console.log('📊 测试结果总结');
    console.log('='.repeat(60));
    
    results.forEach(result => {
        if (result.success) {
            console.log(`\n${result.description} (${result.user}):`);
            console.log(`  组织信息: ${result.organizationValid ? '✅ 正确' : '❌ 错误'}`);
            console.log(`  用户数据: ${result.apiResults.users} 条`);
            console.log(`  学校数据: ${result.apiResults.schools} 条`);
            console.log(`  设备数据: ${result.apiResults.equipment} 条`);
        } else {
            console.log(`\n${result.user}: ❌ ${result.error}`);
        }
    });
    
    console.log('\n🎉 集成测试完成!');
}

// 运行测试
if (typeof window === 'undefined') {
    // Node.js 环境
    const fetch = require('node-fetch');
    runIntegrationTest().catch(console.error);
} else {
    // 浏览器环境
    window.runIntegrationTest = runIntegrationTest;
    console.log('集成测试函数已加载，请在控制台运行: runIntegrationTest()');
}
