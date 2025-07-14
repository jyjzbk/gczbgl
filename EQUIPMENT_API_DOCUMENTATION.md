# 设备管理API文档

## 概述

设备管理API提供了完整的设备生命周期管理功能，包括设备档案管理、借用管理、维修管理和二维码管理。

## 认证

所有API请求都需要JWT认证，请在请求头中包含：

```
Authorization: Bearer {token}
```

## 响应格式

### 成功响应
```json
{
    "code": 200,
    "message": "操作成功",
    "data": {},
    "meta": {
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 100,
            "last_page": 5
        }
    }
}
```

### 错误响应
```json
{
    "code": 422,
    "message": "验证失败",
    "errors": {
        "field": ["错误信息"]
    }
}
```

## 设备档案管理API

### 1. 获取设备列表

**请求**
```
GET /api/equipments
```

**查询参数**
- `page` (integer): 页码，默认1
- `per_page` (integer): 每页数量，默认20，最大100
- `school_id` (integer): 学校ID筛选
- `category_id` (integer): 分类ID筛选
- `status` (integer): 状态筛选 (1:正常 2:借出 3:维修 4:报废)
- `condition` (integer): 状况筛选 (1:优 2:良 3:中 4:差)
- `keyword` (string): 关键词搜索（设备名称、编号、型号）
- `location` (string): 位置筛选
- `brand` (string): 品牌筛选

**响应示例**
```json
{
    "code": 200,
    "message": "获取成功",
    "data": {
        "items": [
            {
                "id": 1,
                "name": "显微镜",
                "code": "EQ001",
                "model": "XSP-2C",
                "brand": "奥林巴斯",
                "serial_number": "SN123456",
                "status": 1,
                "status_text": "正常",
                "condition": 1,
                "condition_text": "优",
                "location": "实验室A101",
                "purchase_date": "2024-01-01",
                "purchase_price": "5000.00",
                "warranty_period": 24,
                "category": {
                    "id": 1,
                    "name": "光学仪器"
                },
                "school": {
                    "id": 1,
                    "name": "示例学校"
                },
                "created_at": "2024-01-01 10:00:00",
                "updated_at": "2024-01-01 10:00:00"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 50,
            "last_page": 3
        }
    }
}
```

### 2. 获取设备详情

**请求**
```
GET /api/equipments/{id}
```

**响应示例**
```json
{
    "code": 200,
    "message": "获取成功",
    "data": {
        "id": 1,
        "name": "显微镜",
        "code": "EQ001",
        "model": "XSP-2C",
        "brand": "奥林巴斯",
        "serial_number": "SN123456",
        "status": 1,
        "condition": 1,
        "location": "实验室A101",
        "purchase_date": "2024-01-01",
        "purchase_price": "5000.00",
        "supplier": "科学仪器公司",
        "warranty_period": 24,
        "description": "高精度生物显微镜",
        "specifications": "放大倍数：40-1000倍",
        "responsible_person": "张老师",
        "contact_phone": "13800138000",
        "category": {
            "id": 1,
            "name": "光学仪器"
        },
        "school": {
            "id": 1,
            "name": "示例学校"
        },
        "photos": [
            {
                "id": 1,
                "file_url": "/storage/equipment_photos/photo1.jpg",
                "is_primary": true,
                "description": "设备正面照片"
            }
        ],
        "qrcode": {
            "id": 1,
            "qr_code_url": "/storage/qrcodes/qr_EQ001.png",
            "qr_type": "detailed",
            "created_at": "2024-01-01 10:00:00"
        }
    }
}
```

### 3. 创建设备

**请求**
```
POST /api/equipments
```

**权限要求**: `equipment.create`

**请求体**
```json
{
    "school_id": 1,
    "category_id": 1,
    "name": "显微镜",
    "code": "EQ001",
    "model": "XSP-2C",
    "brand": "奥林巴斯",
    "serial_number": "SN123456",
    "purchase_date": "2024-01-01",
    "purchase_price": 5000.00,
    "supplier": "科学仪器公司",
    "warranty_period": 24,
    "location": "实验室A101",
    "status": 1,
    "condition": 1,
    "description": "高精度生物显微镜",
    "specifications": "放大倍数：40-1000倍",
    "responsible_person": "张老师",
    "contact_phone": "13800138000"
}
```

**字段说明**
- `school_id` (required): 学校ID
- `category_id` (required): 设备分类ID
- `name` (required): 设备名称，最大255字符
- `code` (required): 设备编号，最大100字符，唯一
- `model` (required): 设备型号，最大255字符
- `brand` (required): 设备品牌，最大255字符
- `serial_number` (required): 序列号，最大255字符，唯一
- `purchase_date` (required): 采购日期，格式：YYYY-MM-DD
- `purchase_price` (required): 采购价格，数字，最小0
- `supplier` (required): 供应商，最大255字符
- `warranty_period` (required): 保修期（月），整数，最小0
- `location` (required): 存放位置，最大255字符
- `status` (required): 设备状态，1:正常 2:借出 3:维修 4:报废
- `condition` (required): 设备状况，1:优 2:良 3:中 4:差
- `description` (optional): 设备描述，最大2000字符
- `specifications` (optional): 技术规格，最大2000字符
- `responsible_person` (optional): 责任人，最大100字符
- `contact_phone` (optional): 联系电话，最大20字符

### 4. 更新设备

**请求**
```
PUT /api/equipments/{id}
```

**权限要求**: `equipment.edit`

**请求体**: 与创建设备相同，所有字段可选

### 5. 删除设备

**请求**
```
DELETE /api/equipments/{id}
```

**权限要求**: `equipment.delete`

### 6. 批量导入设备

**请求**
```
POST /api/equipments/batch-import
```

**权限要求**: `equipment.import`

**请求体** (multipart/form-data)
- `file` (required): Excel文件，支持xlsx、xls、csv格式，最大10MB

**响应示例**
```json
{
    "code": 200,
    "message": "导入成功",
    "data": {
        "imported_count": 50,
        "failed_count": 2,
        "errors": [
            {
                "row": 3,
                "errors": ["设备编号已存在"]
            },
            {
                "row": 5,
                "errors": ["采购价格格式错误"]
            }
        ]
    }
}
```

### 7. 导出设备数据

**请求**
```
GET /api/equipments/export
```

**权限要求**: `equipment.export`

**查询参数**: 与获取设备列表相同的筛选参数

**响应示例**
```json
{
    "code": 200,
    "message": "导出成功",
    "data": {
        "download_url": "/storage/exports/equipments_2024-01-01_10-00-00.xlsx",
        "filename": "equipments_2024-01-01_10-00-00.xlsx"
    }
}
```

### 8. 上传设备照片

**请求**
```
POST /api/equipments/{id}/photos
```

**权限要求**: `equipment.photo.upload`

**请求体** (multipart/form-data)
- `photo` (required): 图片文件，支持jpeg、png、jpg、gif格式，最大5MB
- `attachment_type` (optional): 附件类型，photo|manual|certificate|warranty|invoice|other
- `description` (optional): 描述，最大500字符
- `is_primary` (optional): 是否主图片，布尔值

### 9. 删除设备照片

**请求**
```
DELETE /api/equipments/{id}/photos/{photoId}
```

**权限要求**: `equipment.photo.delete`

### 10. 检查设备可用性

**请求**
```
GET /api/equipments/{id}/availability
```

**查询参数**
- `start_date` (required): 开始日期，格式：YYYY-MM-DD
- `end_date` (required): 结束日期，格式：YYYY-MM-DD

**响应示例**
```json
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "available": false,
        "reasons": [
            "2024-01-05至2024-01-10期间已被借用",
            "2024-01-12至2024-01-15期间安排维修"
        ]
    }
}
```

## 设备分类管理API

### 1. 获取设备分类列表

**请求**
```
GET /api/equipment-categories
```

### 2. 创建设备分类

**请求**
```
POST /api/equipment-categories
```

**权限要求**: `equipment.category.create`

### 3. 更新设备分类

**请求**
```
PUT /api/equipment-categories/{id}
```

**权限要求**: `equipment.category.edit`

### 4. 删除设备分类

**请求**
```
DELETE /api/equipment-categories/{id}
```

**权限要求**: `equipment.category.delete`

## 错误代码说明

- `200`: 成功
- `201`: 创建成功
- `400`: 请求参数错误
- `401`: 未认证
- `403`: 权限不足
- `404`: 资源不存在
- `422`: 验证失败
- `500`: 服务器内部错误

## 权限说明

### 设备档案权限
- `equipment.view`: 查看设备
- `equipment.create`: 创建设备
- `equipment.edit`: 编辑设备
- `equipment.delete`: 删除设备
- `equipment.import`: 批量导入设备
- `equipment.export`: 导出设备数据
- `equipment.photo.upload`: 上传设备照片
- `equipment.photo.delete`: 删除设备照片

### 角色权限分配
- **超级管理员**: 所有权限
- **管理员**: 除超级管理员权限外的所有权限
- **实验员**: 设备管理相关权限
- **教师**: 查看权限和部分操作权限
- **学生**: 仅查看权限

## 数据访问控制

- 超级管理员和管理员可以访问所有学校的数据
- 其他角色只能访问自己学校的数据
- 用户只能操作有权限的数据

## 使用示例

### JavaScript (Axios)

```javascript
// 获取设备列表
const response = await axios.get('/api/equipments', {
    headers: {
        'Authorization': `Bearer ${token}`
    },
    params: {
        page: 1,
        per_page: 20,
        keyword: '显微镜'
    }
});

// 创建设备
const equipmentData = {
    school_id: 1,
    category_id: 1,
    name: '显微镜',
    code: 'EQ001',
    // ... 其他字段
};

const createResponse = await axios.post('/api/equipments', equipmentData, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
});
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client();

// 获取设备列表
$response = $client->get('http://api.example.com/api/equipments', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token
    ],
    'query' => [
        'page' => 1,
        'per_page' => 20
    ]
]);

$data = json_decode($response->getBody(), true);
```

## 设备借用管理API

### 1. 获取借用记录列表

**请求**
```
GET /api/equipment-borrows
```

**查询参数**
- `page` (integer): 页码，默认1
- `per_page` (integer): 每页数量，默认20
- `equipment_id` (integer): 设备ID筛选
- `borrower_id` (integer): 借用人ID筛选
- `status` (integer): 状态筛选 (1:待审批 2:已批准 3:已借出 4:已归还 5:已拒绝 6:已取消)
- `borrow_date_start` (string): 借用开始日期筛选
- `borrow_date_end` (string): 借用结束日期筛选
- `overdue` (boolean): 是否逾期筛选

**响应示例**
```json
{
    "code": 200,
    "message": "获取成功",
    "data": {
        "items": [
            {
                "id": 1,
                "equipment_id": 1,
                "borrower_id": 2,
                "borrower_name": "张老师",
                "borrower_phone": "13800138000",
                "borrow_date": "2024-01-05",
                "expected_return_date": "2024-01-10",
                "actual_return_date": null,
                "purpose": "教学实验使用",
                "status": 3,
                "status_text": "已借出",
                "is_overdue": false,
                "overdue_days": 0,
                "equipment": {
                    "id": 1,
                    "name": "显微镜",
                    "code": "EQ001",
                    "model": "XSP-2C"
                },
                "borrower": {
                    "id": 2,
                    "real_name": "张老师",
                    "username": "teacher001"
                },
                "approver": {
                    "id": 1,
                    "real_name": "管理员",
                    "username": "admin"
                },
                "created_at": "2024-01-01 10:00:00"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 30,
            "last_page": 2
        }
    }
}
```

### 2. 创建借用申请

**请求**
```
POST /api/equipment-borrows
```

**权限要求**: `equipment.borrow.create`

**请求体**
```json
{
    "equipment_id": 1,
    "borrower_id": 2,
    "borrower_name": "张老师",
    "borrower_phone": "13800138000",
    "borrow_date": "2024-01-05",
    "expected_return_date": "2024-01-10",
    "purpose": "教学实验使用",
    "remark": "需要配套使用载玻片"
}
```

### 3. 审批借用申请

**请求**
```
POST /api/equipment-borrows/{id}/review
```

**权限要求**: `equipment.borrow.approve`

**请求体**
```json
{
    "status": 2,
    "remark": "审批通过，请按时归还"
}
```

### 4. 归还设备

**请求**
```
POST /api/equipment-borrows/{id}/return
```

**权限要求**: `equipment.borrow.return`

**请求体**
```json
{
    "actual_return_date": "2024-01-10",
    "remark": "设备完好归还"
}
```

### 5. 批量操作

**请求**
```
POST /api/equipment-borrows/batch-action
```

**权限要求**: `equipment.borrow.batch`

**请求体**
```json
{
    "ids": [1, 2, 3],
    "action": "approve",
    "remark": "批量审批通过"
}
```

## 设备维修管理API

### 1. 获取维修记录列表

**请求**
```
GET /api/equipment-maintenances
```

### 2. 创建维修申请

**请求**
```
POST /api/equipment-maintenances
```

**权限要求**: `equipment.maintenance.create`

### 3. 分配维修技师

**请求**
```
POST /api/equipment-maintenances/{id}/assign
```

**权限要求**: `equipment.maintenance.assign`

### 4. 完成维修

**请求**
```
POST /api/equipment-maintenances/{id}/complete
```

**权限要求**: `equipment.maintenance.complete`

### 5. 获取维修统计

**请求**
```
GET /api/equipment-maintenances/statistics
```

**权限要求**: `equipment.maintenance.statistics`

## 设备二维码管理API

### 1. 生成设备二维码

**请求**
```
POST /api/equipments/{id}/qrcode
```

**权限要求**: `equipment.qrcode.generate`

**请求体**
```json
{
    "qr_type": "detailed",
    "size": 200,
    "format": "png",
    "include_info": ["name", "code", "location", "contact"]
}
```

### 2. 获取设备二维码

**请求**
```
GET /api/equipments/{id}/qrcode
```

**权限要求**: `equipment.qrcode.view`

### 3. 批量生成二维码

**请求**
```
POST /api/equipments/batch-qrcode
```

**权限要求**: `equipment.qrcode.batch`

### 4. 扫码查询设备信息

**请求**
```
GET /api/qrcode/scan/{code}
```

**说明**: 此接口无需认证，支持公开扫码查询

## 更新日志

### v1.0.0 (2024-01-01)
- 初始版本发布
- 实现设备档案管理基础功能
- 实现设备借用管理功能
- 实现设备维修管理功能
- 实现设备二维码管理功能
- 实现权限控制和数据验证
