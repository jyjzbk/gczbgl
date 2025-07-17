# 权限控制快速参考

## 权限级别对照表

| 级别 | 名称 | organization_level | 管理范围 | 示例 |
|------|------|-------------------|----------|------|
| 1 | 省级 | 1 | 全省数据 | 河北省教育厅 |
| 2 | 市级 | 2 | 本市及下级 | 石家庄市教育局 |
| 3 | 区县级 | 3 | 本区县及下级 | 藁城区教育局 |
| 4 | 学区级 | 4 | 本学区学校 | 廉州学区 |
| 5 | 学校级 | 5 | 本校数据 | 廉州东城小学 |

## 数据访问矩阵

| 用户级别 | 省直学校 | 市直学校 | 区县直学校 | 学区学校 | 区域管理 |
|----------|----------|----------|------------|----------|----------|
| 省级 | ✅ | ✅ | ✅ | ✅ | ✅ |
| 市级 | ❌ | ✅ | ✅ | ✅ | 本市 |
| 区县级 | ❌ | ❌ | ✅ | ✅ | 本区县 |
| 学区级 | ❌ | ❌ | ❌ | ✅ | ❌ |
| 学校级 | ❌ | ❌ | ❌ | 本校 | ❌ |

## 用户配置模板

### 省级管理员
```php
[
    'organization_id' => 1,           // 省ID
    'organization_type' => 'region',
    'organization_level' => 1,
    'scope_type' => 'region',
    'scope_id' => 1
]
```

### 市级管理员
```php
[
    'organization_id' => 2,           // 市ID
    'organization_type' => 'region',
    'organization_level' => 2,
    'scope_type' => 'region',
    'scope_id' => 2
]
```

### 区县管理员
```php
[
    'organization_id' => 3,           // 区县ID
    'organization_type' => 'region',
    'organization_level' => 3,
    'scope_type' => 'region',
    'scope_id' => 3
]
```

### 学区管理员
```php
[
    'organization_id' => 4,           // 学区ID
    'organization_type' => 'region',
    'organization_level' => 4,
    'scope_type' => 'region',
    'scope_id' => 4
]
```

### 学校管理员
```php
[
    'organization_id' => 10,          // 学校ID
    'organization_type' => 'school',
    'organization_level' => 5,
    'school_id' => 10,               // 兼容字段
    'scope_type' => 'school',
    'scope_id' => 10
]
```

## API权限验证代码

### 控制器中的标准用法

```php
// 1. 应用数据权限过滤
DataScopeMiddleware::applyDataScope($query, $request, 'equipments');

// 2. 验证单个资源访问权限
if (!DataScopeMiddleware::canAccess($request, 'school', $schoolId)) {
    return response()->json(['code' => 403, 'message' => '无权访问'], 403);
}

// 3. 验证创建权限
if (!DataScopeMiddleware::canCreate($request, $data)) {
    return response()->json(['code' => 403, 'message' => '无权创建'], 403);
}

// 4. 验证更新权限
if (!DataScopeMiddleware::canUpdate($request, $model, $data)) {
    return response()->json(['code' => 403, 'message' => '无权更新'], 403);
}
```

### 路由配置

```php
// 需要权限控制的路由组
Route::middleware(['data.scope'])->group(function () {
    Route::apiResource('equipments', EquipmentController::class);
    Route::apiResource('laboratories', LaboratoryController::class);
    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('regions', AdministrativeRegionController::class);
    Route::apiResource('users', UserController::class);
});
```

## 数据表对应关系

| 表名 | 权限字段 | 过滤方式 |
|------|----------|----------|
| equipments | school_id | 学校ID过滤 |
| laboratories | school_id | 学校ID过滤 |
| schools | id | 学校ID过滤 |
| administrative_regions | id | 区域ID过滤 |
| users | school_id, organization_id | 复合过滤 |
| experiment_reservations | school_id | 学校ID过滤 |
| experiment_records | school_id | 学校ID过滤 |

## 权限服务方法

```php
// 获取用户数据访问范围
$dataScope = $permissionService->getUserDataScope($user);
// 返回: ['type' => 'province', 'school_ids' => [...], 'region_ids' => [...]]

// 检查学校访问权限
$canAccess = $permissionService->canAccessSchool($user, $schoolId);

// 检查组织访问权限
$canAccess = $permissionService->canAccessOrganization($user, 'school', $orgId);

// 获取可管理的学校ID列表
$schoolIds = $permissionService->getManageableSchoolIds($user);
```

## 测试命令

```bash
# 运行权限测试
php test_organization_permissions.php

# 运行API权限测试
php test_api_permissions.php

# 创建测试数据
php artisan db:seed --class=OrganizationPermissionSeeder
php artisan db:seed --class=TestEquipmentSeeder
```

## 常用SQL查询

### 查看用户权限配置
```sql
SELECT u.username, u.real_name, u.organization_level, u.organization_type, u.organization_id
FROM users u 
WHERE u.status = 1;
```

### 查看组织架构
```sql
SELECT ar.id, ar.name, ar.level, ar.parent_id, 
       parent.name as parent_name
FROM administrative_regions ar
LEFT JOIN administrative_regions parent ON ar.parent_id = parent.id
ORDER BY ar.level, ar.sort_order;
```

### 查看学校分布
```sql
SELECT s.name, s.level, ar.name as region_name, 
       COUNT(e.id) as equipment_count
FROM schools s
LEFT JOIN administrative_regions ar ON s.region_id = ar.id
LEFT JOIN equipments e ON s.id = e.school_id
GROUP BY s.id
ORDER BY s.level, s.name;
```

## 故障排除

### 权限异常检查清单

1. ✅ 用户的 `organization_level` 是否正确
2. ✅ 用户的 `organization_type` 是否匹配
3. ✅ 用户的 `organization_id` 是否存在
4. ✅ 路由是否应用了 `data.scope` 中间件
5. ✅ 控制器是否正确调用权限验证方法
6. ✅ 数据表的关联字段是否正确

### 常见错误及解决方案

| 错误 | 原因 | 解决方案 |
|------|------|----------|
| 403 无权访问 | 权限配置错误 | 检查用户组织归属 |
| 数据为空 | 权限过滤过严 | 验证数据范围计算 |
| 权限验证失败 | 中间件未生效 | 检查路由配置 |
| 组织关系错误 | 数据不一致 | 检查组织架构数据 |
