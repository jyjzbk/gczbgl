# 系统备份与恢复指南

## 📦 完整备份清单

### 1. 核心代码文件

#### 后端关键文件
```
backend/app/Http/Controllers/Api/OrganizationController.php
backend/app/Http/Controllers/Api/EquipmentController.php
backend/app/Http/Controllers/Api/StatisticsController.php
backend/app/Services/PermissionService.php
backend/app/Http/Middleware/DataScopeMiddleware.php
backend/routes/api.php
backend/config/
backend/database/migrations/
```

#### 前端关键文件
```
frontend/src/api/organization.ts
frontend/src/api/equipment.ts
frontend/src/views/equipment/EquipmentManagement.vue
frontend/src/views/user/UserList.vue
frontend/src/views/basic/LaboratoryManagement.vue
frontend/src/components/OrganizationTree.vue
frontend/src/stores/
```

### 2. 数据库备份

#### 核心数据表
```sql
-- 导出核心表结构和数据
mysqldump -u username -p database_name \
  administrative_regions \
  schools \
  equipments \
  users \
  equipment_categories \
  laboratories \
  roles \
  permissions \
  > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### 重要测试数据
```sql
-- 河北省数据 (ID=1)
SELECT * FROM administrative_regions WHERE id = 1;

-- 石家庄市藁城区实验小学数据 (ID=1)
SELECT * FROM schools WHERE id = 1;

-- 测试用户
SELECT * FROM users WHERE username = 'province_admin_test';

-- 学校设备数据
SELECT * FROM equipments WHERE school_id = 1;
```

### 3. 配置文件
```
backend/.env
frontend/.env
backend/config/app.php
backend/config/database.php
```

## 🔄 快速恢复步骤

### 1. 环境准备
```bash
# 1. 克隆或恢复代码
git clone <repository> gczbgl
cd gczbgl

# 2. 后端环境
cd backend
composer install
cp .env.example .env
php artisan key:generate

# 3. 前端环境
cd ../frontend
npm install
```

### 2. 数据库恢复
```bash
# 1. 创建数据库
mysql -u root -p -e "CREATE DATABASE gczbgl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. 恢复数据
mysql -u username -p gczbgl < backup_YYYYMMDD_HHMMSS.sql

# 3. 运行迁移（如果需要）
cd backend
php artisan migrate
```

### 3. 关键功能验证

#### 验证组织统计API
```bash
# 测试区域统计
curl -X GET "http://localhost:8000/api/organizations/stats?organization_id=1" \
  -H "Authorization: Bearer <token>"

# 测试学校统计
curl -X GET "http://localhost:8000/api/organizations/stats?organization_id=1&organization_type=school" \
  -H "Authorization: Bearer <token>"
```

#### 验证设备列表API
```bash
# 测试学校设备列表
curl -X GET "http://localhost:8000/api/organizations/equipments?organization_id=1&organization_level=5" \
  -H "Authorization: Bearer <token>"
```

## 🛠️ 关键修复点记录

### 1. ID冲突解决方案
```php
// OrganizationController.php - getOrganizationStats方法
$organizationType = $request->get('organization_type');

if ($organizationType === 'school') {
    $school = School::find($organizationId);
    if ($school) {
        return $this->getSchoolStatsForOrganization($school, $user, $dataScope);
    }
}

// 优先检查区域，再检查学校
$region = AdministrativeRegion::find($organizationId);
if ($region) {
    return $this->getRegionStatsForOrganization($region, $user, $dataScope);
}
```

### 2. 学校设备列表修复
```php
// OrganizationController.php - getOrganizationEquipments方法
$school = School::find($organizationId);
if ($school) {
    // 验证学校权限
    $hasPermission = in_array($school->id, $schoolIds) ||
                   in_array($school->region_id, $regionIds);
    
    if ($hasPermission) {
        $schoolIds = collect([$school->id]);
    }
}
```

### 3. 前端API调用修复
```typescript
// organization.ts
export const getOrganizationStatsApi = (organizationId?: number, organizationType?: string) => {
  const params: any = {}
  if (organizationId) params.organization_id = organizationId
  if (organizationType) params.organization_type = organizationType
  
  return request({ url: '/organizations/stats', method: 'get', params })
}

// 页面组件调用
await fetchOrganizationStats(organization.id, organization.type)
```

## 📊 数据完整性检查

### 1. 统计数据验证
```sql
-- 验证河北省统计
SELECT 
  (SELECT COUNT(*) FROM schools WHERE region_id IN (
    SELECT id FROM administrative_regions WHERE id = 1 OR parent_id = 1
  )) as school_count,
  (SELECT COUNT(*) FROM equipments WHERE school_id IN (
    SELECT id FROM schools WHERE region_id IN (
      SELECT id FROM administrative_regions WHERE id = 1 OR parent_id = 1
    )
  )) as equipment_count;

-- 验证学校统计
SELECT 
  COUNT(*) as equipment_count 
FROM equipments 
WHERE school_id = 1;
```

### 2. 权限数据验证
```sql
-- 验证测试用户权限
SELECT * FROM users WHERE username = 'province_admin_test';

-- 验证角色权限
SELECT r.name, p.name 
FROM roles r 
JOIN role_permissions rp ON r.id = rp.role_id 
JOIN permissions p ON rp.permission_id = p.id;
```

## 🚨 故障排除

### 常见问题及解决方案

1. **统计数据不一致**
   - 检查 `organization_type` 参数是否正确传递
   - 验证ID冲突处理逻辑

2. **设备列表为空**
   - 检查学校节点权限验证
   - 确认 `getOrganizationEquipments` 方法的学校处理逻辑

3. **权限验证失败**
   - 检查用户数据权限范围
   - 验证 `DataScopeMiddleware` 配置

### 调试命令
```bash
# 查看Laravel日志
tail -f backend/storage/logs/laravel.log

# 清除缓存
cd backend
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 重新生成前端
cd frontend
npm run build
```

---

**重要提醒**：
1. 定期备份数据库和关键代码文件
2. 在生产环境部署前，务必在测试环境验证所有功能
3. 保留此文档的最新版本，确保团队成员都能快速恢复系统
