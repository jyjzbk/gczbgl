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

## 🆕 第三阶段新增数据表

### 实验要求配置表 (experiment_requirements_config)
```sql
CREATE TABLE experiment_requirements_config (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county') COMMENT '组织类型',
    organization_id BIGINT UNSIGNED COMMENT '组织ID',
    experiment_type ENUM('分组实验', '演示实验') COMMENT '实验类型',
    min_images INT DEFAULT 0 COMMENT '最少图片数量',
    max_images INT DEFAULT 10 COMMENT '最多图片数量',
    min_videos INT DEFAULT 0 COMMENT '最少视频数量',
    max_videos INT DEFAULT 3 COMMENT '最多视频数量',
    is_inherited BOOLEAN DEFAULT TRUE COMMENT '是否继承上级配置',
    created_by BIGINT UNSIGNED COMMENT '创建人',
    description TEXT COMMENT '配置说明',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_org_experiment_type (organization_type, organization_id, experiment_type),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 学校实验目录选择表 (school_experiment_catalog_selections)
```sql
CREATE TABLE school_experiment_catalog_selections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED COMMENT '学校ID',
    selected_level ENUM('province', 'city', 'county') COMMENT '选择的标准级别',
    selected_org_id BIGINT UNSIGNED COMMENT '选择的组织ID',
    selected_org_name VARCHAR(100) COMMENT '选择的组织名称',
    can_delete_experiments BOOLEAN DEFAULT FALSE COMMENT '是否允许删除实验',
    selection_reason TEXT COMMENT '选择理由',
    selected_by BIGINT UNSIGNED COMMENT '选择操作人',
    selected_at TIMESTAMP COMMENT '选择时间',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_school_selection (school_id),
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_by) REFERENCES users(id)
);
```

### 实验目录删除权限表 (experiment_catalog_delete_permissions)
```sql
CREATE TABLE experiment_catalog_delete_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county') COMMENT '组织类型',
    organization_id BIGINT UNSIGNED COMMENT '组织ID',
    organization_name VARCHAR(100) COMMENT '组织名称',
    allow_school_delete BOOLEAN DEFAULT TRUE COMMENT '是否允许学校删除实验',
    require_delete_reason BOOLEAN DEFAULT TRUE COMMENT '是否要求填写删除理由',
    max_delete_percentage INT DEFAULT 20 COMMENT '最大删除比例(%)',
    delete_rules TEXT COMMENT '删除规则说明',
    created_by BIGINT UNSIGNED COMMENT '创建人',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_org_permission (organization_type, organization_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 预警配置表 (experiment_alert_config)
```sql
CREATE TABLE experiment_alert_config (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county') COMMENT '组织类型',
    organization_id BIGINT UNSIGNED COMMENT '组织ID',
    organization_name VARCHAR(100) COMMENT '组织名称',
    alert_type ENUM('overdue', 'completion_rate', 'quality_score') COMMENT '预警类型',
    threshold_value DECIMAL(5,2) COMMENT '预警阈值',
    alert_days INT DEFAULT 7 COMMENT '预警提前天数',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    alert_rules TEXT COMMENT '预警规则说明',
    notification_settings JSON COMMENT '通知设置',
    created_by BIGINT UNSIGNED COMMENT '创建人',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_org_alert_type (organization_type, organization_id, alert_type),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 预警记录表 (experiment_alerts)
```sql
CREATE TABLE experiment_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_type ENUM('overdue', 'completion_rate', 'quality_score') COMMENT '预警类型',
    target_type ENUM('school', 'teacher', 'experiment', 'class') COMMENT '预警对象类型',
    target_id BIGINT UNSIGNED COMMENT '预警对象ID',
    target_name VARCHAR(200) COMMENT '预警对象名称',
    alert_level ENUM('low', 'medium', 'high', 'critical') COMMENT '预警级别',
    alert_title VARCHAR(200) COMMENT '预警标题',
    alert_message TEXT COMMENT '预警消息',
    alert_data JSON COMMENT '预警相关数据',
    alert_value DECIMAL(8,2) COMMENT '预警数值',
    threshold_value DECIMAL(8,2) COMMENT '阈值',
    is_read BOOLEAN DEFAULT FALSE COMMENT '是否已读',
    is_resolved BOOLEAN DEFAULT FALSE COMMENT '是否已解决',
    resolve_note TEXT COMMENT '解决说明',
    resolved_by BIGINT UNSIGNED COMMENT '解决人',
    resolved_at TIMESTAMP COMMENT '解决时间',
    alert_time TIMESTAMP COMMENT '预警时间',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_alert_type (alert_type),
    INDEX idx_target (target_type, target_id),
    INDEX idx_alert_level (alert_level),
    FOREIGN KEY (resolved_by) REFERENCES users(id)
);
```

### 监控统计表 (experiment_monitoring_statistics)
```sql
CREATE TABLE experiment_monitoring_statistics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    target_type ENUM('school', 'teacher', 'subject', 'grade') COMMENT '统计对象类型',
    target_id BIGINT UNSIGNED COMMENT '统计对象ID',
    target_name VARCHAR(200) COMMENT '统计对象名称',
    semester VARCHAR(20) COMMENT '学期',
    statistics_date DATE COMMENT '统计日期',
    total_planned_experiments INT DEFAULT 0 COMMENT '计划实验总数',
    completed_experiments INT DEFAULT 0 COMMENT '已完成实验数',
    overdue_experiments INT DEFAULT 0 COMMENT '超期实验数',
    pending_experiments INT DEFAULT 0 COMMENT '待开实验数',
    completion_rate DECIMAL(5,2) DEFAULT 0 COMMENT '完成率(%)',
    overdue_rate DECIMAL(5,2) DEFAULT 0 COMMENT '超期率(%)',
    quality_score DECIMAL(5,2) DEFAULT 0 COMMENT '质量评分',
    avg_completion_days DECIMAL(8,2) DEFAULT 0 COMMENT '平均完成天数',
    max_overdue_days INT DEFAULT 0 COMMENT '最大超期天数',
    subject_statistics JSON COMMENT '学科统计',
    grade_statistics JSON COMMENT '年级统计',
    monthly_statistics JSON COMMENT '月度统计',
    calculated_at TIMESTAMP COMMENT '计算时间',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_target_semester_date (target_type, target_id, semester, statistics_date),
    INDEX idx_target (target_type, target_id),
    INDEX idx_semester (semester),
    INDEX idx_statistics_date (statistics_date)
);
```

## 总结

本数据库设计支持完整的实验教学管理功能，包括用户权限管理、组织架构管理、实验目录管理、设备管理、实验执行管理、实验要求配置、监控预警等核心功能。通过合理的表结构设计和关联关系，确保了数据的完整性和系统的可扩展性。

### 数据库统计
- **总表数**：30+ 张表
- **核心业务表**：25 张
- **关联关系**：50+ 个外键约束
- **索引优化**：100+ 个索引
- **数据完整性**：完整的约束和验证规则

### 第三阶段新增功能
- **实验要求配置管理**：支持分级配置和继承机制
- **学校目录选择管理**：互斥选择和删除权限控制
- **监控预警系统**：智能预警和统计分析

## 🆕 第三阶段新增数据表

### 实验要求配置表 (experiment_requirements_config)
```sql
CREATE TABLE experiment_requirements_config (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county') COMMENT '组织类型',
    organization_id BIGINT UNSIGNED COMMENT '组织ID',
    experiment_type ENUM('分组实验', '演示实验') COMMENT '实验类型',
    min_images INT DEFAULT 0 COMMENT '最少图片数量',
    max_images INT DEFAULT 10 COMMENT '最多图片数量',
    min_videos INT DEFAULT 0 COMMENT '最少视频数量',
    max_videos INT DEFAULT 3 COMMENT '最多视频数量',
    is_inherited BOOLEAN DEFAULT TRUE COMMENT '是否继承上级配置',
    created_by BIGINT UNSIGNED COMMENT '创建人',
    description TEXT COMMENT '配置说明',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_org_experiment_type (organization_type, organization_id, experiment_type),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 学校实验目录选择表 (school_experiment_catalog_selections)
```sql
CREATE TABLE school_experiment_catalog_selections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED COMMENT '学校ID',
    selected_level ENUM('province', 'city', 'county') COMMENT '选择的标准级别',
    selected_org_id BIGINT UNSIGNED COMMENT '选择的组织ID',
    selected_org_name VARCHAR(100) COMMENT '选择的组织名称',
    can_delete_experiments BOOLEAN DEFAULT FALSE COMMENT '是否允许删除实验',
    selection_reason TEXT COMMENT '选择理由',
    selected_by BIGINT UNSIGNED COMMENT '选择操作人',
    selected_at TIMESTAMP COMMENT '选择时间',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_school_selection (school_id),
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_by) REFERENCES users(id)
);
```

### 实验目录删除权限表 (experiment_catalog_delete_permissions)
```sql
CREATE TABLE experiment_catalog_delete_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county') COMMENT '组织类型',
    organization_id BIGINT UNSIGNED COMMENT '组织ID',
    organization_name VARCHAR(100) COMMENT '组织名称',
    allow_school_delete BOOLEAN DEFAULT TRUE COMMENT '是否允许学校删除实验',
    require_delete_reason BOOLEAN DEFAULT TRUE COMMENT '是否要求填写删除理由',
    max_delete_percentage INT DEFAULT 20 COMMENT '最大删除比例(%)',
    delete_rules TEXT COMMENT '删除规则说明',
    created_by BIGINT UNSIGNED COMMENT '创建人',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_org_permission (organization_type, organization_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```
