# 后端API开发规划

## 项目概述
基于前端接口定义，开发完整的设备管理后端API系统，包括数据库设计、API实现、数据验证和权限控制。

## 技术栈
- **框架**: Laravel 12
- **数据库**: MySQL 8.0
- **认证**: JWT (tymon/jwt-auth)
- **二维码**: SimpleSoftwareIO/simple-qrcode
- **文件存储**: Laravel Storage
- **API文档**: Laravel API Resource

## 数据库表结构分析

### 现有表结构
1. **equipments** - 设备档案表 ✅
2. **equipment_categories** - 设备分类表 ✅
3. **equipment_borrows** - 设备借用表 ✅
4. **equipment_maintenances** - 设备维修表 ✅

### 需要补充的表结构
1. **equipment_qrcodes** - 设备二维码表
2. **equipment_operation_logs** - 设备操作日志表
3. **equipment_attachments** - 设备附件表

## API接口开发计划

### 1. 设备档案管理API
```
GET    /api/equipments              # 获取设备列表
POST   /api/equipments              # 创建设备
GET    /api/equipments/{id}         # 获取设备详情
PUT    /api/equipments/{id}         # 更新设备
DELETE /api/equipments/{id}         # 删除设备
POST   /api/equipments/batch-import # 批量导入设备
GET    /api/equipments/export       # 导出设备数据
POST   /api/equipments/{id}/photos  # 上传设备照片
DELETE /api/equipments/{id}/photos/{photoId} # 删除设备照片
```

### 2. 设备分类管理API
```
GET    /api/equipment-categories    # 获取设备分类列表
POST   /api/equipment-categories    # 创建设备分类
PUT    /api/equipment-categories/{id} # 更新设备分类
DELETE /api/equipment-categories/{id} # 删除设备分类
```

### 3. 设备借用管理API
```
GET    /api/equipment-borrows       # 获取借用列表
POST   /api/equipment-borrows       # 创建借用申请
GET    /api/equipment-borrows/{id}  # 获取借用详情
PUT    /api/equipment-borrows/{id}  # 更新借用申请
POST   /api/equipment-borrows/{id}/review # 审批借用申请
POST   /api/equipment-borrows/{id}/return # 归还设备
GET    /api/equipments/{id}/availability # 检查设备可用性
```

### 4. 设备维修管理API
```
GET    /api/equipment-maintenances  # 获取维修列表
POST   /api/equipment-maintenances  # 创建维修申请
GET    /api/equipment-maintenances/{id} # 获取维修详情
PUT    /api/equipment-maintenances/{id} # 更新维修申请
POST   /api/equipment-maintenances/{id}/assign # 分配技师
POST   /api/equipment-maintenances/{id}/start # 开始维修
POST   /api/equipment-maintenances/{id}/complete # 完成维修
GET    /api/equipment-maintenances/statistics # 维修统计
```

### 5. 设备二维码管理API
```
POST   /api/equipments/{id}/qrcode  # 生成设备二维码
GET    /api/equipments/{id}/qrcode  # 获取设备二维码
POST   /api/equipments/batch-qrcode # 批量生成二维码
GET    /api/qrcode/scan/{code}      # 扫码查询设备信息
DELETE /api/equipments/{id}/qrcode  # 删除设备二维码
```

## 数据验证规则

### 设备档案验证
```php
'name' => 'required|string|max:255',
'code' => 'required|string|max:100|unique:equipments,code',
'model' => 'required|string|max:255',
'brand' => 'required|string|max:255',
'serial_number' => 'required|string|max:255|unique:equipments,serial_number',
'category_id' => 'required|exists:equipment_categories,id',
'purchase_date' => 'required|date',
'purchase_price' => 'required|numeric|min:0',
'supplier' => 'required|string|max:255',
'warranty_period' => 'required|integer|min:0',
'location' => 'required|string|max:255',
'status' => 'required|in:1,2,3,4',
'condition_status' => 'required|in:1,2,3,4',
```

### 设备借用验证
```php
'equipment_id' => 'required|exists:equipments,id',
'borrower_name' => 'required|string|max:255',
'borrower_phone' => 'required|string|max:20',
'borrow_date' => 'required|date|after_or_equal:today',
'expected_return_date' => 'required|date|after:borrow_date',
'purpose' => 'required|string|max:1000',
```

### 设备维修验证
```php
'equipment_id' => 'required|exists:equipments,id',
'fault_description' => 'required|string|max:2000',
'fault_type' => 'required|in:1,2,3,4',
'priority' => 'required|in:1,2,3,4',
```

## 权限控制体系

### 权限定义
```php
// 设备档案权限
'equipment.view' => '查看设备',
'equipment.create' => '创建设备',
'equipment.edit' => '编辑设备',
'equipment.delete' => '删除设备',
'equipment.import' => '批量导入设备',
'equipment.export' => '导出设备数据',

// 设备借用权限
'equipment.borrow.view' => '查看借用记录',
'equipment.borrow.create' => '创建借用申请',
'equipment.borrow.edit' => '编辑借用申请',
'equipment.borrow.approve' => '审批借用申请',
'equipment.borrow.return' => '归还设备',

// 设备维修权限
'equipment.maintenance.view' => '查看维修记录',
'equipment.maintenance.create' => '创建维修申请',
'equipment.maintenance.edit' => '编辑维修申请',
'equipment.maintenance.assign' => '分配维修技师',
'equipment.maintenance.complete' => '完成维修',

// 设备二维码权限
'equipment.qrcode.generate' => '生成二维码',
'equipment.qrcode.view' => '查看二维码',
'equipment.qrcode.delete' => '删除二维码',
```

### 角色权限分配
```php
// 管理员：所有权限
// 教师：查看、借用相关权限
// 实验员：设备管理、维修相关权限
// 学生：查看、借用申请权限
```

## 响应格式标准

### 成功响应
```json
{
    "success": true,
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
    "success": false,
    "message": "操作失败",
    "errors": {
        "field": ["错误信息"]
    },
    "code": 422
}
```

## 开发优先级

### 第一阶段：核心功能
1. 设备档案CRUD API
2. 设备分类管理API
3. 基础权限控制

### 第二阶段：业务流程
1. 设备借用管理API
2. 设备维修管理API
3. 数据验证完善

### 第三阶段：高级功能
1. 设备二维码管理API
2. 批量操作API
3. 统计分析API

### 第四阶段：优化完善
1. API性能优化
2. 缓存机制
3. 日志记录
4. API文档完善

## 技术要求

### 代码规范
- 遵循PSR-12编码规范
- 使用Laravel Resource进行数据转换
- 统一的异常处理机制
- 完整的注释和文档

### 性能要求
- API响应时间 < 500ms
- 支持分页查询
- 数据库查询优化
- 适当的缓存策略

### 安全要求
- JWT认证机制
- 权限中间件验证
- 输入数据验证和过滤
- SQL注入防护
- XSS攻击防护

## 测试计划

### 单元测试
- 模型方法测试
- 服务类测试
- 工具类测试

### 功能测试
- API接口测试
- 权限控制测试
- 数据验证测试

### 集成测试
- 前后端联调测试
- 业务流程测试
- 性能压力测试

## 部署配置

### 环境要求
- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Nginx 1.20+

### 配置项
- 数据库连接配置
- JWT密钥配置
- 文件存储配置
- 缓存配置
- 日志配置

## 监控和维护

### 日志记录
- API访问日志
- 错误日志
- 性能日志
- 安全日志

### 监控指标
- API响应时间
- 错误率统计
- 并发用户数
- 数据库性能

### 备份策略
- 数据库定期备份
- 文件存储备份
- 配置文件备份
- 日志文件归档
