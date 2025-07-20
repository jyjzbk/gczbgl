# 数据库结构说明

## 核心表结构

### 1. 行政区域表 (administrative_regions)
```sql
CREATE TABLE administrative_regions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL COMMENT '区域名称',
    code VARCHAR(50) COMMENT '区域代码',
    level INT NOT NULL COMMENT '级别：1省 2市 3县 4镇',
    parent_id INT COMMENT '父级区域ID',
    sort_order INT DEFAULT 0 COMMENT '排序',
    status TINYINT DEFAULT 1 COMMENT '状态：1启用 0禁用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. 学校表 (schools)
```sql
CREATE TABLE schools (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL COMMENT '学校名称',
    code VARCHAR(50) COMMENT '学校代码',
    region_id INT NOT NULL COMMENT '所属区域ID',
    type VARCHAR(50) COMMENT '学校类型',
    level VARCHAR(50) COMMENT '学校级别',
    address TEXT COMMENT '地址',
    contact_person VARCHAR(100) COMMENT '联系人',
    contact_phone VARCHAR(20) COMMENT '联系电话',
    student_count INT DEFAULT 0 COMMENT '学生数量',
    class_count INT DEFAULT 0 COMMENT '班级数量',
    teacher_count INT DEFAULT 0 COMMENT '教师数量',
    status TINYINT DEFAULT 1 COMMENT '状态：1启用 0禁用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES administrative_regions(id)
);
```

### 3. 设备表 (equipments)
```sql
CREATE TABLE equipments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_id INT NOT NULL COMMENT '所属学校ID',
    category_id INT COMMENT '设备分类ID',
    laboratory_id INT COMMENT '所属实验室ID',
    name VARCHAR(255) NOT NULL COMMENT '设备名称',
    code VARCHAR(100) UNIQUE NOT NULL COMMENT '设备编号',
    model VARCHAR(100) COMMENT '型号',
    brand VARCHAR(100) COMMENT '品牌',
    serial_number VARCHAR(100) COMMENT '序列号',
    purchase_date DATE COMMENT '采购日期',
    purchase_price DECIMAL(10,2) COMMENT '采购价格',
    supplier VARCHAR(255) COMMENT '供应商',
    warranty_period INT COMMENT '保修期（月）',
    location VARCHAR(255) COMMENT '存放位置',
    status TINYINT DEFAULT 1 COMMENT '状态：1正常 2借出 3维修 4报废',
    condition TINYINT DEFAULT 1 COMMENT '状况：1良好 2一般 3较差',
    description TEXT COMMENT '描述',
    specifications TEXT COMMENT '技术规格',
    qr_code VARCHAR(255) COMMENT '二维码',
    manager_id INT COMMENT '管理员ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
);
```

### 4. 用户表 (users)
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL COMMENT '用户名',
    email VARCHAR(255) UNIQUE COMMENT '邮箱',
    password VARCHAR(255) NOT NULL COMMENT '密码',
    name VARCHAR(100) NOT NULL COMMENT '姓名',
    phone VARCHAR(20) COMMENT '电话',
    school_id INT COMMENT '所属学校ID（传统字段）',
    organization_id INT COMMENT '所属组织ID（新字段，可以是区域或学校）',
    organization_type ENUM('region', 'school') COMMENT '组织类型',
    status TINYINT DEFAULT 1 COMMENT '状态：1启用 0禁用',
    last_login_at TIMESTAMP NULL COMMENT '最后登录时间',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id)
);
```

## 重要数据关系

### ID冲突问题
- **问题**：区域表和学校表的ID可能重复（如河北省ID=1，某学校ID=1）
- **解决**：通过节点类型参数明确区分

### 组织树结构
```
河北省 (administrative_regions.id=1, type=undefined)
├── 石家庄市 (administrative_regions.id=9)
│   ├── 藁城区 (administrative_regions.id=10)
│   │   └── 石家庄市藁城区实验小学 (schools.id=1, type='school')
│   └── 其他区县...
└── 其他市...
```

### 设备归属关系
```
设备 → 学校 → 区域
equipments.school_id → schools.id
schools.region_id → administrative_regions.id
```

## 测试数据示例

### 河北省统计数据
- 区域ID：1
- 下级学校：21个
- 总设备：24个
- 总用户：49个

### 石家庄市藁城区实验小学
- 学校ID：1（与河北省ID冲突）
- 设备数量：6个
- 用户数量：4个
- 设备列表：
  - 生物显微镜XSP-2CA (BIO0010001)
  - 学生用生物显微镜 (BIO00110002)
  - 电子天平 (BAL0010001)
  - 分析天平 (BAL00110002)
  - 数字万用表 (MUL0010001)
  - 玻璃烧杯100ml (BEA0010001)
