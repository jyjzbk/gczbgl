# 组织层级权限控制系统使用指南

## 概述

本系统实现了基于五级组织架构的数据权限控制，确保用户只能管理本级和下级组织的数据，实现了"向下管理"的权限继承机制。

## 组织架构设计

### 五级组织结构

#### 完整组织架构图

```
🏛️ 河北省教育厅 (Level 1)
├─ 🎓 省直属中小学
│    ├─ 🏫 石家庄精英中学
│    ├─ 🏫 衡水中学
│    ├─ 🏫 保定七中
│    └─ 🏫 邢台一中
└─ 🏢 下属市教育局
     ├─ 🏢 石家庄市教育局 (Level 2)
     │    ├─ 🎓 市直中小学
     │    │    ├─ 🏫 石家庄市第一中学
     │    │    ├─ 🏫 石家庄市第二中学
     │    │    ├─ 🏫 石家庄外国语学校
     │    │    └─ 🏫 石家庄实验中学
     │    └─ 🌐 区县 (Level 3)
     │         ├─ 🏙️ 藁城区
     │         │    ├─ 🎓 学区 (Level 4)
     │         │    │    ├─ 📍 廉州学区
     │         │    │    │    ├─ 🏫 廉州东城小学
     │         │    │    │    ├─ 🏫 廉州北街小学
     │         │    │    │    ├─ 🏫 廉州第四中学
     │         │    │    │    └─ 🏫 廉州第一中学
     │         │    │    ├─ 📍 南董学区
     │         │    │    ├─ 📍 南营学区
     │         │    │    └─ 📍 兴安学区
     │         │    └─ 🎓 区县直中小学
     │         │         ├─ 🏫 通安小学
     │         │         ├─ 🏫 实验小学
     │         │         └─ 🏫 石家庄第八中学
     │         ├─ 🏙️ 栾城区
     │         ├─ 🏙️ 长安区
     │         ├─ 🏙️ 井陉矿区
     │         └─ 🏙️ 鹿泉区
     ├─ 🏢 唐山市教育局 (Level 2)
     ├─ 🏢 沧州市教育局 (Level 2)
     └─ 🏢 衡水市教育局 (Level 2)
```

#### 层级结构说明

```
🏛️ 省级 (Level 1) - 河北省教育厅
├─ 🏢 市级 (Level 2) - 各市教育局
│   ├─ 🏙️ 区县级 (Level 3) - 各区县教育局
│   │   ├─ 📍 学区级 (Level 4) - 各学区
│   │   │   └─ 🏫 学校级 (Level 5) - 学区学校
│   │   └─ 🏫 区县直学校 (Level 5)
│   └─ 🏫 市直学校 (Level 5)
└─ 🏫 省直学校 (Level 5)
```

### 权限继承原则

- **向下管理**：上级用户可以管理下级组织的所有数据
- **数据隔离**：同级组织间的数据完全隔离
- **权限边界**：用户无法访问上级组织的数据

## 用户权限配置

### 用户组织归属字段

在 `users` 表中，每个用户都有以下组织归属字段：

```sql
-- 主要组织归属
organization_id     -- 组织ID（区域或学校）
organization_type   -- 组织类型：'region' 或 'school'
organization_level  -- 组织级别：1-5

-- 兼容字段
school_id          -- 学校ID（兼容旧数据）
```

### 权限级别说明

#### 1. 省级用户 (Level 1)
- **管理范围**：全省所有数据
- **可访问**：
  - 省直学校
  - 所有市级及以下组织
  - 所有学校的设备、实验室等数据

#### 2. 市级用户 (Level 2)
- **管理范围**：本市及下级数据
- **可访问**：
  - 市直学校
  - 本市所有区县及以下组织
  - 本市所有学校的数据

#### 3. 区县级用户 (Level 3)
- **管理范围**：本区县及下级数据
- **可访问**：
  - 区县直学校
  - 本区县所有学区及学校
  - 本区县所有学校的数据

#### 4. 学区级用户 (Level 4)
- **管理范围**：本学区学校数据
- **可访问**：
  - 本学区所有学校
  - 本学区学校的设备、实验室等数据

#### 5. 学校级用户 (Level 5)
- **管理范围**：本校数据
- **可访问**：
  - 仅本校数据
  - 本校的设备、实验室、用户等

## 技术实现

### 数据权限中间件

系统使用 `DataScopeMiddleware` 中间件自动处理数据权限过滤：

```php
// 在路由中应用中间件
Route::middleware(['data.scope'])->group(function () {
    Route::apiResource('equipments', EquipmentController::class);
    Route::apiResource('schools', SchoolController::class);
    // ... 其他需要权限控制的路由
});
```

### 权限服务类

`PermissionService` 类提供权限计算和验证功能：

```php
// 获取用户数据访问范围
$dataScope = $permissionService->getUserDataScope($user);

// 检查是否可以访问指定学校
$canAccess = $permissionService->canAccessSchool($user, $schoolId);

// 获取可管理的学校ID列表
$schoolIds = $permissionService->getManageableSchoolIds($user);
```

### 控制器中的权限验证

在控制器中使用中间件提供的方法进行权限验证：

```php
// 应用数据范围过滤
DataScopeMiddleware::applyDataScope($query, $request, 'equipments');

// 验证访问权限
if (!DataScopeMiddleware::canAccess($request, 'school', $schoolId)) {
    return response()->json(['code' => 403, 'message' => '无权访问'], 403);
}

// 验证创建权限
if (!DataScopeMiddleware::canCreate($request, $data)) {
    return response()->json(['code' => 403, 'message' => '无权创建'], 403);
}

// 验证更新权限
if (!DataScopeMiddleware::canUpdate($request, $model, $data)) {
    return response()->json(['code' => 403, 'message' => '无权更新'], 403);
}
```

## 用户配置示例

### 创建省级管理员

```php
$user = User::create([
    'username' => 'province_admin',
    'email' => 'province@example.com',
    'password' => Hash::make('password'),
    'real_name' => '省级管理员',
    'organization_id' => 1,        // 河北省ID
    'organization_type' => 'region',
    'organization_level' => 1,     // 省级
    'status' => 1
]);

// 分配角色
UserRole::create([
    'user_id' => $user->id,
    'role_id' => $provinceAdminRole->id,
    'scope_type' => 'region',
    'scope_id' => 1
]);
```

### 创建学校管理员

```php
$user = User::create([
    'username' => 'school_admin',
    'email' => 'school@example.com',
    'password' => Hash::make('password'),
    'real_name' => '学校管理员',
    'organization_id' => 10,       // 学校ID
    'organization_type' => 'school',
    'organization_level' => 5,     // 学校级
    'school_id' => 10,            // 兼容字段
    'status' => 1
]);

// 分配角色
UserRole::create([
    'user_id' => $user->id,
    'role_id' => $schoolAdminRole->id,
    'scope_type' => 'school',
    'scope_id' => 10
]);
```

## 数据库迁移

运行以下命令应用权限相关的数据库迁移：

```bash
# 添加用户组织归属字段
php artisan migrate

# 创建测试数据
php artisan db:seed --class=OrganizationPermissionSeeder
php artisan db:seed --class=TestEquipmentSeeder
```

## 测试验证

### 测试用户

系统已创建以下测试用户（密码均为：password）：

| 用户名 | 角色 | 组织级别 | 管理范围 |
|--------|------|----------|----------|
| province_admin_test | 省级管理员 | 1 | 全省数据 |
| city_admin_test | 市级管理员 | 2 | 石家庄市及下级 |
| county_admin_test | 区县管理员 | 3 | 藁城区及下级 |
| district_admin_test | 学区管理员 | 4 | 廉州学区学校 |
| school_admin_test | 学校管理员 | 5 | 廉州东城小学 |
| province_school_admin | 学校管理员 | 5 | 石家庄精英中学 |
| city_school_admin | 学校管理员 | 5 | 石家庄市第一中学 |
| county_school_admin | 学校管理员 | 5 | 通安小学 |

### 权限测试脚本

运行权限测试脚本验证系统功能：

```bash
# 测试权限控制逻辑
php test_organization_permissions.php

# 测试API权限控制
php test_organization_api.php
```

### 测试结果摘要

- ✅ 省级管理员：可访问15所学校，20台设备
- ✅ 市级管理员：可访问11所学校，15台设备
- ✅ 区县管理员：可访问7所学校，10台设备
- ✅ 学区管理员：可访问4所学校，5台设备
- ✅ 学校管理员：可访问1所学校，5台设备

## 常见问题

### Q: 如何修改用户的管理范围？

A: 更新用户的 `organization_id`、`organization_type` 和 `organization_level` 字段，系统会自动重新计算权限范围。

### Q: 如何添加新的组织层级？

A: 需要修改 `PermissionService` 类中的权限计算逻辑，并更新相关的验证规则。

### Q: 如何处理跨区域的数据访问需求？

A: 可以为用户分配多个角色，或者在特殊情况下使用超级管理员权限。

### Q: 如何确保数据安全？

A: 系统在多个层面进行权限验证：
1. 中间件层面的自动过滤
2. 控制器层面的权限检查
3. 服务层面的业务逻辑验证

## 注意事项

1. **数据一致性**：确保用户的组织归属信息准确无误
2. **权限测试**：在生产环境部署前充分测试权限控制功能
3. **日志记录**：建议记录权限相关的操作日志
4. **定期审计**：定期检查用户权限配置的合理性

## 更新日志

- 2025-07-15：初始版本，实现五级组织架构权限控制
- 支持向下管理的权限继承机制
- 集成数据权限过滤中间件
- 完善API层面的权限验证
