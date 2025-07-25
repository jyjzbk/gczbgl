# API 接口文档

## 实验记录相关接口

### 上传实验照片

**接口地址**：`POST /api/experiment-records/{id}/upload-photos`

**请求方式**：`multipart/form-data`

**请求参数**：
- `photos[]`: 文件数组，最多10张照片
- 支持格式：jpeg, png, jpg, gif
- 单张照片最大5MB

**请求头**：
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**响应数据**：
```json
{
  "code": 200,
  "message": "照片上传成功",
  "data": {
    "photos": [
      "http://localhost:8000/storage/experiment_photos/1753432611_6883422344144.jpg",
      "http://localhost:8000/storage/experiment_photos/1753432611_68834223e259d.jpg"
    ]
  }
}
```

### 获取个人实验统计

**接口地址**：`GET /api/personal/experiment-stats`

**请求参数**：
- `teacher_id`: 可选，默认为当前用户

**响应数据**：
```json
{
  "success": true,
  "data": {
    "total_reservations": 3,
    "completed_experiments": 3,
    "completion_rate": 100.00,
    "total_works": 3,
    "pending_reservations": 0,
    "approved_reservations": 0
  }
}
```

**字段说明**：
- `total_works`: 基于照片数量统计的实验作品总数
- 其他字段保持原有含义

---

## 认证相关接口

### 用户注册

**接口地址**：`POST /api/register`

**请求参数**：
```json
{
  "username": "string",
  "password": "string",
  "password_confirmation": "string",
  "real_name": "string",
  "email": "string",
  "phone": "string",
  "school_id": "integer"
}
```

**参数说明**：
- `school_id`: 必填，用户所属学校ID，需要在schools表中存在
- 注册成功后自动分配任课教师角色和相应权限

**响应数据**：
```json
{
  "success": true,
  "message": "注册成功",
  "data": {
    "token": "jwt_token_string",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "username": "test_user",
      "real_name": "测试用户",
      "email": "test@example.com",
      "phone": "13800138000",
      "status": 1
    }
  }
}
```

### 获取学校列表（公开接口）

**接口地址**：`GET /api/public/schools`

**请求参数**：
```json
{
  "search": "string"  // 可选，搜索关键词
}
```

**响应数据**：
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "石家庄市藁城区实验小学",
      "code": "ZY001",
      "region_name": "藁城区"
    }
  ]
}
```

## 组织管理相关接口

### 1. 获取组织统计信息

**接口地址：** `GET /api/organizations/stats`

**请求参数：**
```typescript
{
  organization_id?: number,     // 组织ID（区域ID或学校ID）
  organization_type?: string   // 节点类型：'school' 表示学校节点，不传或其他值表示区域节点
}
```

**响应数据：**
```typescript
{
  success: boolean,
  data: {
    total_users: number,        // 用户总数
    active_users: number,       // 活跃用户数
    disabled_users: number,     // 禁用用户数
    total_schools: number,      // 学校总数
    total_equipments: number,   // 设备总数
    total_laboratories: number  // 实验室总数
  }
}
```

**重要说明：**
- 当 `organization_type='school'` 时，直接查询指定学校的统计数据
- 当不传 `organization_type` 或传其他值时，优先按区域处理
- 解决了区域ID和学校ID冲突的问题

### 2. 获取组织设备列表

**接口地址：** `GET /api/organizations/equipments`

**请求参数：**
```typescript
{
  organization_id?: number,     // 组织ID
  organization_level?: number,  // 组织级别
  page?: number,               // 页码
  per_page?: number,           // 每页数量
  search?: string,             // 搜索关键词
  category_id?: number,        // 设备分类ID
  status?: number,             // 设备状态
  condition?: number,          // 设备状况
  location?: string            // 存放位置
}
```

**响应数据：**
```typescript
{
  success: boolean,
  data: {
    items: Equipment[],         // 设备列表
    pagination: {
      current_page: number,
      last_page: number,
      per_page: number,
      total: number
    }
  }
}
```

**处理逻辑：**
- 首先检查 `organization_id` 是否为学校ID
- 如果是学校：直接查询该学校的设备
- 如果是区域：查询该区域下所有学校的设备

### 3. 获取组织树

**接口地址：** `GET /api/organizations/tree`

**响应数据：**
```typescript
{
  success: boolean,
  data: OrganizationNode[]
}

interface OrganizationNode {
  id: number,
  code: string,
  name: string,
  level: number,
  parent_id: number | null,
  sort_order: number,
  status: number,
  type?: string,              // 'school' 表示学校节点
  children?: OrganizationNode[],
  user_count?: number,
  school_count?: number,
  equipment_count?: number,
  laboratory_count?: number
}
```

**重要字段：**
- `type: 'school'`：标识学校节点
- 区域节点没有 `type` 字段或 `type` 不等于 `'school'`

## 前端API调用示例

### 统计API调用
```typescript
// 区域统计
await getOrganizationStatsApi(regionId)

// 学校统计（新方式）
await getOrganizationStatsApi(schoolId, 'school')

// 组织选择处理
const handleOrganizationSelect = async (organization: OrganizationNode) => {
  await fetchOrganizationStats(organization.id, organization.type)
}
```

### 设备列表API调用
```typescript
const loadData = async () => {
  const params = {
    organization_id: selectedOrganization.value.id,
    organization_level: selectedOrganization.value.level,
    page: pagination.current_page,
    per_page: pagination.per_page,
    ...searchForm
  }
  
  const response = await getOrganizationEquipmentsApi(params)
  // 处理响应...
}
```

## 权限验证

### 数据权限范围
```typescript
interface DataScope {
  type: 'all' | 'region' | 'school',
  region_ids?: number[],      // 可访问的区域ID列表
  school_ids?: number[]       // 可访问的学校ID列表
}
```

### 权限检查逻辑
1. **超级管理员** (`type: 'all'`)：可访问所有数据
2. **区域管理员** (`type: 'region'`)：可访问指定区域及下级数据
3. **学校管理员** (`type: 'school'`)：只能访问指定学校数据

### 学校节点权限验证
```php
// 检查是否有直接的学校权限或者区域权限
$hasPermission = in_array($school->id, $schoolIds) ||
               in_array($school->region_id, $regionIds);
```
