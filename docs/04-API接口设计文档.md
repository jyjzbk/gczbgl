# 实验教学管理平台API接口设计文档

## 一、权限控制系统

### 1.1 组织层级权限控制
本系统实现了基于五级组织架构的数据权限控制：

**组织层级：**
1. **省级 (Level 1)**：可管理全省所有数据
2. **市级 (Level 2)**：可管理本市及下级数据
3. **区县级 (Level 3)**：可管理本区县及下级数据
4. **学区级 (Level 4)**：可管理本学区学校数据
5. **学校级 (Level 5)**：只能管理本校数据

**权限原则：**
- **向下管理**：用户只能管理本级和下级组织的数据
- **数据隔离**：同级组织间的数据完全隔离
- **权限继承**：上级权限自动包含下级权限

### 1.2 API权限验证
所有需要权限控制的API都会自动应用数据权限过滤：

```http
# 请求头必须包含认证信息
Authorization: Bearer {jwt_token}

# 响应会根据用户权限自动过滤数据
GET /api/v1/equipments
# 省级用户：返回全省设备
# 市级用户：返回本市设备
# 学校用户：返回本校设备
```

**权限错误响应：**
```json
{
    "code": 403,
    "message": "无权访问该资源",
    "data": null
}
```

## 二、接口设计规范

### 2.1 RESTful API设计原则
- 使用HTTP动词表示操作：GET(查询)、POST(创建)、PUT(更新)、DELETE(删除)
- URL路径使用名词复数形式，表示资源集合
- 使用HTTP状态码表示请求结果
- 统一的请求/响应数据格式
- 版本控制：/api/v1/

### 2.2 接口URL规范
```
基础URL: https://domain.com/api/v1/

资源操作规范:
GET    /api/v1/users          # 获取用户列表
GET    /api/v1/users/{id}     # 获取指定用户
POST   /api/v1/users          # 创建用户
PUT    /api/v1/users/{id}     # 更新用户
DELETE /api/v1/users/{id}     # 删除用户

嵌套资源:
GET    /api/v1/schools/{id}/laboratories  # 获取学校的实验室列表
POST   /api/v1/schools/{id}/laboratories  # 为学校创建实验室
```

### 2.3 统一响应格式
```json
{
    "code": 200,
    "message": "操作成功",
    "data": {
        // 具体数据
    },
    "meta": {
        "timestamp": "2025-01-13T10:30:00Z",
        "request_id": "req_123456789"
    }
}
```

### 2.4 分页响应格式
```json
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "items": [...],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 100,
            "last_page": 5,
            "from": 1,
            "to": 20
        }
    }
}
```

### 1.5 错误响应格式
```json
{
    "code": 400,
    "message": "请求参数错误",
    "errors": {
        "email": ["邮箱格式不正确"],
        "password": ["密码长度至少6位"]
    },
    "meta": {
        "timestamp": "2025-01-13T10:30:00Z",
        "request_id": "req_123456789"
    }
}
```

## 二、认证授权接口

### 2.1 用户登录
```
POST /api/v1/auth/login
Content-Type: application/json

Request Body:
{
    "username": "admin",
    "password": "password123",
    "captcha": "1234",
    "remember": true
}

Response:
{
    "code": 200,
    "message": "登录成功",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 7200,
        "user": {
            "id": 1,
            "username": "admin",
            "real_name": "系统管理员",
            "email": "admin@example.com",
            "avatar": "avatar.jpg",
            "roles": [
                {
                    "id": 1,
                    "name": "省级管理员",
                    "level": 1,
                    "scope": {
                        "type": "region",
                        "id": 1,
                        "name": "河北省"
                    }
                }
            ],
            "permissions": ["user.view", "user.create", "experiment.view"]
        }
    }
}
```

### 2.2 用户登出
```
POST /api/v1/auth/logout
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "登出成功"
}
```

### 2.3 刷新Token
```
POST /api/v1/auth/refresh
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "Token刷新成功",
    "data": {
        "token": "new_token_here",
        "expires_in": 7200
    }
}
```

### 2.4 获取当前用户信息
```
GET /api/v1/auth/me
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "获取成功",
    "data": {
        "id": 1,
        "username": "admin",
        "real_name": "系统管理员",
        "email": "admin@example.com",
        "roles": [...],
        "permissions": [...]
    }
}
```

## 三、用户管理接口

### 3.1 获取用户列表
```
GET /api/v1/users?page=1&per_page=20&search=张三&role_id=1&status=1
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "items": [
            {
                "id": 1,
                "username": "zhangsan",
                "real_name": "张三",
                "email": "zhangsan@example.com",
                "phone": "13800138000",
                "status": 1,
                "last_login_at": "2025-01-13T09:30:00Z",
                "created_at": "2025-01-01T00:00:00Z",
                "roles": [...]
            }
        ],
        "pagination": {...}
    }
}
```

### 3.2 创建用户
```
POST /api/v1/users
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "username": "lisi",
    "email": "lisi@example.com",
    "phone": "13900139000",
    "password": "password123",
    "real_name": "李四",
    "gender": 1,
    "role_ids": [2],
    "scope_type": "school",
    "scope_id": 1
}

Response:
{
    "code": 201,
    "message": "用户创建成功",
    "data": {
        "id": 2,
        "username": "lisi",
        "real_name": "李四",
        "email": "lisi@example.com",
        "status": 1,
        "created_at": "2025-01-13T10:30:00Z"
    }
}
```

### 3.3 更新用户
```
PUT /api/v1/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "real_name": "李四四",
    "email": "lisi_new@example.com",
    "phone": "13900139001",
    "status": 1
}

Response:
{
    "code": 200,
    "message": "用户更新成功",
    "data": {
        "id": 2,
        "username": "lisi",
        "real_name": "李四四",
        "email": "lisi_new@example.com",
        "updated_at": "2025-01-13T10:35:00Z"
    }
}
```

## 四、实验管理接口

### 4.1 获取实验目录
```
GET /api/v1/experiment-catalogs?subject_id=1&grade=1&semester=1&type=1
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "items": [
            {
                "id": 1,
                "subject_id": 1,
                "subject_name": "物理",
                "grade": 1,
                "semester": 1,
                "chapter": "第一章 运动的描述",
                "name": "测量长度和时间",
                "type": 1,
                "type_name": "必做实验",
                "difficulty": 2,
                "duration": 45,
                "student_count": 2,
                "description": "学习使用刻度尺和秒表测量长度和时间",
                "objectives": "掌握基本测量工具的使用方法",
                "procedures": "1. 准备器材...",
                "safety_notes": "注意安全使用器材"
            }
        ],
        "pagination": {...}
    }
}
```

### 4.2 创建实验预约
```
POST /api/v1/experiment-reservations
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "catalog_id": 1,
    "laboratory_id": 1,
    "class_name": "高一(1)班",
    "student_count": 45,
    "reservation_date": "2025-01-15",
    "start_time": "08:00",
    "end_time": "09:40",
    "remark": "物理实验：测量重力加速度"
}

Response:
{
    "code": 201,
    "message": "预约创建成功",
    "data": {
        "id": 1,
        "catalog_id": 1,
        "catalog_name": "测量长度和时间",
        "laboratory_id": 1,
        "laboratory_name": "物理实验室1",
        "teacher_id": 1,
        "teacher_name": "张老师",
        "class_name": "高一(1)班",
        "student_count": 45,
        "reservation_date": "2025-01-15",
        "start_time": "08:00",
        "end_time": "09:40",
        "status": 1,
        "status_name": "待审核",
        "created_at": "2025-01-13T10:30:00Z"
    }
}
```

### 4.3 获取实验预约列表
```
GET /api/v1/experiment-reservations?date=2025-01-15&laboratory_id=1&status=2
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "items": [
            {
                "id": 1,
                "catalog_name": "测量长度和时间",
                "laboratory_name": "物理实验室1",
                "teacher_name": "张老师",
                "class_name": "高一(1)班",
                "student_count": 45,
                "reservation_date": "2025-01-15",
                "start_time": "08:00",
                "end_time": "09:40",
                "status": 2,
                "status_name": "已通过"
            }
        ]
    }
}
```

### 4.4 创建实验记录
```
POST /api/v1/experiment-records
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "reservation_id": 1,
    "start_time": "2025-01-15T08:05:00Z",
    "end_time": "2025-01-15T09:35:00Z",
    "student_count": 43,
    "completion_rate": 95.5,
    "quality_score": 4,
    "photos": ["photo1.jpg", "photo2.jpg"],
    "videos": ["video1.mp4"],
    "summary": "实验进行顺利，学生掌握良好",
    "problems": "部分器材精度不够",
    "suggestions": "建议更新测量设备"
}

Response:
{
    "code": 201,
    "message": "实验记录创建成功",
    "data": {
        "id": 1,
        "reservation_id": 1,
        "start_time": "2025-01-15T08:05:00Z",
        "end_time": "2025-01-15T09:35:00Z",
        "completion_rate": 95.5,
        "quality_score": 4,
        "status": 2,
        "status_name": "已完成",
        "created_at": "2025-01-15T09:35:00Z"
    }
}
```

## 五、设备管理接口

### 5.1 获取设备列表
```
GET /api/v1/equipments?school_id=1&category_id=1&status=1&search=显微镜
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "items": [
            {
                "id": 1,
                "name": "生物显微镜",
                "code": "SWJ001",
                "model": "XSP-2CA",
                "brand": "奥林巴斯",
                "category_name": "光学仪器",
                "quantity": 30,
                "unit": "台",
                "purchase_price": 1500.00,
                "purchase_date": "2024-09-01",
                "status": 1,
                "status_name": "正常",
                "laboratory_name": "生物实验室1",
                "manager_name": "李老师"
            }
        ],
        "pagination": {...}
    }
}
```

### 5.2 创建设备借用记录
```
POST /api/v1/equipment-borrows
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "equipment_ids": [1, 2, 3],
    "reservation_id": 1,
    "borrow_date": "2025-01-15",
    "expected_return_date": "2025-01-15",
    "purpose": "物理实验使用",
    "remark": "小心轻放"
}

Response:
{
    "code": 201,
    "message": "借用记录创建成功",
    "data": {
        "id": 1,
        "equipment_count": 3,
        "borrow_date": "2025-01-15",
        "expected_return_date": "2025-01-15",
        "status": 1,
        "status_name": "已借出",
        "created_at": "2025-01-15T08:00:00Z"
    }
}
```

## 六、统计分析接口

### 6.1 获取实验开出率统计
```
GET /api/v1/statistics/experiment-completion?scope_type=school&scope_id=1&date_range=2024-09-01,2025-01-31
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "total_experiments": 120,
        "completed_experiments": 108,
        "completion_rate": 90.0,
        "group_experiments": 80,
        "group_completion_rate": 92.5,
        "demo_experiments": 40,
        "demo_completion_rate": 85.0,
        "by_subject": [
            {
                "subject_id": 1,
                "subject_name": "物理",
                "total": 40,
                "completed": 38,
                "rate": 95.0
            }
        ]
    }
}
```

### 6.2 获取设备配备率统计
```
GET /api/v1/statistics/equipment-standard?school_id=1&standard_id=1
Authorization: Bearer {token}

Response:
{
    "code": 200,
    "message": "查询成功",
    "data": {
        "overall_rate": 85.5,
        "total_value": 1250000.00,
        "gap_value": 180000.00,
        "by_category": [
            {
                "category_id": 1,
                "category_name": "光学仪器",
                "required": 50,
                "current": 45,
                "gap": 5,
                "compliance_rate": 90.0,
                "required_value": 75000.00,
                "current_value": 67500.00,
                "gap_value": 7500.00
            }
        ]
    }
}
```

## 七、文件管理接口

### 7.1 文件上传
```
POST /api/v1/files/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

Request Body:
file: (binary)
type: image  // image, video, document
module: experiment  // experiment, equipment, user

Response:
{
    "code": 200,
    "message": "上传成功",
    "data": {
        "id": 1,
        "filename": "experiment_photo_001.jpg",
        "original_name": "实验照片.jpg",
        "url": "/storage/uploads/experiment/2025/01/13/experiment_photo_001.jpg",
        "size": 1024000,
        "mime_type": "image/jpeg",
        "created_at": "2025-01-13T10:30:00Z"
    }
}
```

### 7.2 批量导入
```
POST /api/v1/equipments/import
Authorization: Bearer {token}
Content-Type: multipart/form-data

Request Body:
file: (Excel文件)
school_id: 1

Response:
{
    "code": 200,
    "message": "导入成功",
    "data": {
        "total": 100,
        "success": 95,
        "failed": 5,
        "errors": [
            {
                "row": 10,
                "message": "设备名称不能为空"
            }
        ]
    }
}
```

## 八、🆕 智能实验预约系统接口

### 8.1 智能预约创建
```
POST /api/smart-reservations/create
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "catalog_id": 1,
    "laboratory_id": 1,
    "reservation_date": "2025-01-15",
    "start_time": "08:00",
    "end_time": "09:40",
    "class_name": "高一(1)班",
    "student_count": 45,
    "priority": "normal",
    "auto_borrow_equipment": true,
    "preparation_notes": "需要提前准备天平和砝码"
}

Response:
{
    "success": true,
    "message": "预约创建成功",
    "data": {
        "reservation": {
            "id": 123,
            "experiment_name": "测量重力加速度",
            "laboratory_name": "物理实验室1",
            "reservation_date": "2025-01-15",
            "time_slot": "08:00-09:40",
            "status": 1,
            "status_text": "待审核",
            "equipment_requirements": [
                {
                    "equipment_id": 1,
                    "equipment_name": "天平",
                    "equipment_code": "EQ001",
                    "required_quantity": 15,
                    "available_quantity": 20,
                    "shortage": 0,
                    "is_required": true
                }
            ]
        },
        "conflicts": [],
        "has_conflicts": false
    }
}
```

### 8.2 实验室课表查询
```
GET /api/smart-reservations/laboratories/{laboratory_id}/schedule
Authorization: Bearer {token}

Query Parameters:
- date_start: 2025-01-15 (必填)
- date_end: 2025-01-21 (必填)
- view_type: week|month (可选，默认week)

Response:
{
    "success": true,
    "data": {
        "laboratory": {
            "id": 1,
            "name": "物理实验室1",
            "capacity": 50,
            "location": "教学楼3楼"
        },
        "schedule": [
            {
                "date": "2025-01-15",
                "day_name": "星期三",
                "reservations": [
                    {
                        "id": 123,
                        "experiment_name": "测量重力加速度",
                        "teacher_name": "张老师",
                        "class_name": "高一(1)班",
                        "student_count": 45,
                        "start_time": "08:00",
                        "end_time": "09:40",
                        "status": 2,
                        "status_text": "已通过",
                        "status_color": "success",
                        "priority": "normal",
                        "priority_name": "普通",
                        "priority_color": "primary"
                    }
                ]
            }
        ],
        "date_range": {
            "start": "2025-01-15",
            "end": "2025-01-21"
        }
    }
}
```

### 8.3 预约冲突检测
```
POST /api/smart-reservations/check-conflicts
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "laboratory_id": 1,
    "reservation_date": "2025-01-15",
    "start_time": "08:00",
    "end_time": "09:40",
    "teacher_id": 123,
    "student_count": 45,
    "equipment_ids": [1, 2, 3],
    "exclude_reservation_id": 122
}

Response:
{
    "success": true,
    "data": {
        "has_conflicts": true,
        "conflicts": [
            {
                "type": "laboratory_time",
                "message": "实验室时间冲突",
                "existing_reservation": {
                    "id": 122,
                    "experiment_name": "光的折射实验",
                    "teacher_name": "李老师",
                    "time_slot": "08:00-09:40"
                }
            },
            {
                "type": "equipment_borrowed",
                "message": "设备已被借用",
                "equipment_name": "天平",
                "borrower_name": "王老师",
                "borrow_date": "2025-01-15",
                "expected_return_date": "2025-01-15"
            }
        ]
    }
}
```

### 8.4 个人实验统计
```
GET /api/personal/experiment-stats
Authorization: Bearer {token}

Query Parameters:
- teacher_id: current|{user_id} (可选，默认current)

Response:
{
    "success": true,
    "data": {
        "total_reservations": 25,
        "completed_experiments": 20,
        "completion_rate": 80.0,
        "total_works": 156,
        "pending_reservations": 3,
        "approved_reservations": 2
    }
}
```

## 九、🆕 实验作品管理接口

### 9.1 作品上传
```
POST /api/experiment-works
Authorization: Bearer {token}
Content-Type: multipart/form-data

Request Body:
record_id: 1
student_id: 123 (可选)
title: "重力加速度测量结果"
description: "通过单摆实验测量重力加速度的实验结果"
is_public: true
file: [binary data]

Response:
{
    "success": true,
    "message": "作品上传成功",
    "data": {
        "id": 456,
        "title": "重力加速度测量结果",
        "type": "photo",
        "type_name": "图片",
        "file_url": "/storage/experiment-works/2025/01/uuid.jpg",
        "thumbnail_url": "/storage/experiment-works/2025/01/thumbnails/uuid_thumb.jpg",
        "file_size": 2621440,
        "formatted_file_size": "2.5 MB",
        "created_at": "2025-01-15T10:30:00Z"
    }
}
```

### 9.2 作品列表查询
```
GET /api/experiment-works
Authorization: Bearer {token}

Query Parameters:
- record_id: 1 (可选)
- student_id: 123 (可选)
- type: photo|video|document|other (可选)
- is_featured: true|false (可选)
- is_public: true|false (可选)
- page: 1 (可选)
- per_page: 15 (可选)

Response:
{
    "success": true,
    "data": [
        {
            "id": 456,
            "title": "重力加速度测量结果",
            "description": "实验结果分析",
            "type": "photo",
            "type_name": "图片",
            "file_url": "/storage/experiment-works/2025/01/uuid.jpg",
            "thumbnail_url": "/storage/experiment-works/2025/01/thumbnails/uuid_thumb.jpg",
            "file_size": 2621440,
            "formatted_file_size": "2.5 MB",
            "quality_score": 4,
            "teacher_comment": "实验数据准确，分析到位",
            "is_featured": true,
            "is_public": true,
            "student": {
                "id": 123,
                "name": "张三"
            },
            "uploader": {
                "id": 456,
                "name": "李老师"
            },
            "created_at": "2025-01-15T10:30:00Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 68
    }
}
```

### 9.3 作品详情
```
GET /api/experiment-works/{id}
Authorization: Bearer {token}

Response:
{
    "success": true,
    "data": {
        "id": 456,
        "title": "重力加速度测量结果",
        "description": "通过单摆实验测量重力加速度",
        "type": "photo",
        "file_url": "/storage/experiment-works/2025/01/uuid.jpg",
        "thumbnail_url": "/storage/experiment-works/2025/01/thumbnails/uuid_thumb.jpg",
        "file_name": "gravity_experiment.jpg",
        "file_size": 2621440,
        "mime_type": "image/jpeg",
        "metadata": {
            "width": 1920,
            "height": 1080,
            "original_name": "重力实验照片.jpg"
        },
        "quality_score": 4,
        "teacher_comment": "实验数据准确，分析到位",
        "is_featured": true,
        "is_public": true,
        "experiment_record": {
            "id": 1,
            "catalog": {
                "id": 1,
                "name": "测量重力加速度"
            },
            "laboratory": {
                "id": 1,
                "name": "物理实验室1"
            }
        },
        "student": {
            "id": 123,
            "name": "张三"
        },
        "uploader": {
            "id": 456,
            "name": "李老师"
        },
        "created_at": "2025-01-15T10:30:00Z"
    }
}
```

### 9.4 作品更新
```
PUT /api/experiment-works/{id}
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "title": "重力加速度测量结果（修订版）",
    "description": "更新后的实验结果分析",
    "quality_score": 5,
    "teacher_comment": "优秀的实验报告",
    "is_featured": true,
    "is_public": true
}

Response:
{
    "success": true,
    "message": "作品更新成功",
    "data": {
        "id": 456,
        "title": "重力加速度测量结果（修订版）",
        "quality_score": 5,
        "teacher_comment": "优秀的实验报告",
        "is_featured": true,
        "is_public": true,
        "updated_at": "2025-01-15T14:30:00Z"
    }
}
```

### 9.5 作品删除
```
DELETE /api/experiment-works/{id}
Authorization: Bearer {token}

Response:
{
    "success": true,
    "message": "作品删除成功"
}
```

---

**文档版本**: v2.0
**创建日期**: 2025-01-13
**更新日期**: 2025-07-19
**文档状态**: 已更新智能预约系统接口
