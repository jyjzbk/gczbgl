-- 设备管理相关数据库表结构设计
-- 创建时间: 2024-01-15
-- 说明: 包含设备档案、借用记录、维修记录等核心表

-- 1. 设备分类表
CREATE TABLE equipment_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT '分类名称',
    code VARCHAR(50) NOT NULL UNIQUE COMMENT '分类编码',
    parent_id BIGINT UNSIGNED NULL COMMENT '父分类ID',
    description TEXT NULL COMMENT '分类描述',
    sort_order INT DEFAULT 0 COMMENT '排序',
    status TINYINT DEFAULT 1 COMMENT '状态: 1-启用 0-禁用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_parent_id (parent_id),
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order),
    FOREIGN KEY (parent_id) REFERENCES equipment_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备分类表';

-- 2. 设备档案表
CREATE TABLE equipments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL COMMENT '学校ID',
    category_id BIGINT UNSIGNED NOT NULL COMMENT '设备分类ID',
    name VARCHAR(200) NOT NULL COMMENT '设备名称',
    code VARCHAR(100) NOT NULL COMMENT '设备编号',
    model VARCHAR(100) NOT NULL COMMENT '设备型号',
    brand VARCHAR(100) NOT NULL COMMENT '设备品牌',
    serial_number VARCHAR(100) NOT NULL COMMENT '序列号',
    purchase_date DATE NOT NULL COMMENT '采购日期',
    purchase_price DECIMAL(12,2) NOT NULL COMMENT '采购价格',
    supplier VARCHAR(200) NOT NULL COMMENT '供应商',
    warranty_period INT NOT NULL COMMENT '保修期(月)',
    location VARCHAR(200) NOT NULL COMMENT '存放位置',
    status TINYINT DEFAULT 1 COMMENT '设备状态: 1-正常 2-借出 3-维修 4-报废',
    condition_status TINYINT DEFAULT 1 COMMENT '设备状况: 1-优 2-良 3-中 4-差',
    description TEXT NULL COMMENT '设备描述',
    specifications TEXT NULL COMMENT '技术规格',
    photos JSON NULL COMMENT '设备照片',
    qr_code VARCHAR(255) NULL COMMENT '二维码',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT '软删除时间',
    UNIQUE KEY uk_school_code (school_id, code),
    UNIQUE KEY uk_serial_number (serial_number),
    INDEX idx_school_id (school_id),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_condition (condition_status),
    INDEX idx_location (location),
    INDEX idx_brand (brand),
    INDEX idx_purchase_date (purchase_date),
    INDEX idx_deleted_at (deleted_at),
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES equipment_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备档案表';

-- 3. 设备借用记录表
CREATE TABLE equipment_borrows (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_id BIGINT UNSIGNED NOT NULL COMMENT '设备ID',
    borrower_id BIGINT UNSIGNED NOT NULL COMMENT '借用人ID',
    borrower_name VARCHAR(100) NOT NULL COMMENT '借用人姓名',
    borrower_phone VARCHAR(20) NOT NULL COMMENT '借用人电话',
    borrow_date DATE NOT NULL COMMENT '借用日期',
    expected_return_date DATE NOT NULL COMMENT '预计归还日期',
    actual_return_date DATE NULL COMMENT '实际归还日期',
    purpose TEXT NOT NULL COMMENT '借用用途',
    status TINYINT DEFAULT 1 COMMENT '状态: 1-申请中 2-已批准 3-已借出 4-已归还 5-已拒绝 6-逾期',
    remark TEXT NULL COMMENT '备注',
    approved_by BIGINT UNSIGNED NULL COMMENT '审批人ID',
    approved_at TIMESTAMP NULL COMMENT '审批时间',
    approval_remark TEXT NULL COMMENT '审批备注',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_borrower_id (borrower_id),
    INDEX idx_status (status),
    INDEX idx_borrow_date (borrow_date),
    INDEX idx_expected_return_date (expected_return_date),
    INDEX idx_actual_return_date (actual_return_date),
    INDEX idx_approved_by (approved_by),
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
    FOREIGN KEY (borrower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备借用记录表';

-- 4. 设备维修记录表
CREATE TABLE equipment_maintenances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_id BIGINT UNSIGNED NOT NULL COMMENT '设备ID',
    reporter_id BIGINT UNSIGNED NOT NULL COMMENT '报修人ID',
    reporter_name VARCHAR(100) NOT NULL COMMENT '报修人姓名',
    fault_description TEXT NOT NULL COMMENT '故障描述',
    fault_type TINYINT NOT NULL COMMENT '故障类型: 1-硬件故障 2-软件故障 3-使用损坏 4-自然老化',
    priority TINYINT NOT NULL COMMENT '优先级: 1-低 2-中 3-高 4-紧急',
    status TINYINT DEFAULT 1 COMMENT '状态: 1-待处理 2-处理中 3-已完成 4-已取消',
    repair_start_date DATE NULL COMMENT '开始维修日期',
    repair_end_date DATE NULL COMMENT '完成维修日期',
    repair_cost DECIMAL(10,2) NULL COMMENT '维修费用',
    repair_description TEXT NULL COMMENT '维修描述',
    parts_used TEXT NULL COMMENT '使用配件',
    technician_id BIGINT UNSIGNED NULL COMMENT '维修技师ID',
    technician_name VARCHAR(100) NULL COMMENT '维修技师姓名',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_reporter_id (reporter_id),
    INDEX idx_technician_id (technician_id),
    INDEX idx_status (status),
    INDEX idx_fault_type (fault_type),
    INDEX idx_priority (priority),
    INDEX idx_repair_start_date (repair_start_date),
    INDEX idx_repair_end_date (repair_end_date),
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备维修记录表';

-- 5. 设备操作日志表
CREATE TABLE equipment_operation_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_id BIGINT UNSIGNED NOT NULL COMMENT '设备ID',
    user_id BIGINT UNSIGNED NOT NULL COMMENT '操作用户ID',
    operation_type VARCHAR(50) NOT NULL COMMENT '操作类型',
    operation_description TEXT NOT NULL COMMENT '操作描述',
    old_data JSON NULL COMMENT '操作前数据',
    new_data JSON NULL COMMENT '操作后数据',
    ip_address VARCHAR(45) NULL COMMENT 'IP地址',
    user_agent TEXT NULL COMMENT '用户代理',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_user_id (user_id),
    INDEX idx_operation_type (operation_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备操作日志表';

-- 6. 设备二维码表
CREATE TABLE equipment_qrcodes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_id BIGINT UNSIGNED NOT NULL COMMENT '设备ID',
    qr_code_url VARCHAR(500) NOT NULL COMMENT '二维码URL',
    qr_code_content TEXT NOT NULL COMMENT '二维码内容',
    qr_type VARCHAR(50) DEFAULT 'basic' COMMENT '二维码类型',
    size INT DEFAULT 200 COMMENT '二维码尺寸',
    generated_by BIGINT UNSIGNED NOT NULL COMMENT '生成人ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_generated_by (generated_by),
    INDEX idx_qr_type (qr_type),
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备二维码表';

-- 7. 设备文件附件表
CREATE TABLE equipment_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_id BIGINT UNSIGNED NOT NULL COMMENT '设备ID',
    file_name VARCHAR(255) NOT NULL COMMENT '文件名',
    file_path VARCHAR(500) NOT NULL COMMENT '文件路径',
    file_size BIGINT NOT NULL COMMENT '文件大小(字节)',
    file_type VARCHAR(100) NOT NULL COMMENT '文件类型',
    mime_type VARCHAR(100) NOT NULL COMMENT 'MIME类型',
    uploaded_by BIGINT UNSIGNED NOT NULL COMMENT '上传人ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_file_type (file_type),
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设备文件附件表';

-- 插入默认设备分类数据
INSERT INTO equipment_categories (name, code, description, sort_order) VALUES
('实验仪器', 'INSTRUMENT', '各类实验用仪器设备', 1),
('计算机设备', 'COMPUTER', '计算机及相关设备', 2),
('音视频设备', 'MULTIMEDIA', '音响、投影等多媒体设备', 3),
('办公设备', 'OFFICE', '打印机、复印机等办公设备', 4),
('其他设备', 'OTHER', '其他类型设备', 99);

-- 插入子分类
INSERT INTO equipment_categories (name, code, parent_id, description, sort_order) VALUES
('显微镜', 'MICROSCOPE', 1, '各类显微镜设备', 1),
('天平', 'BALANCE', 1, '电子天平、分析天平等', 2),
('离心机', 'CENTRIFUGE', 1, '各类离心机设备', 3),
('台式机', 'DESKTOP', 2, '台式计算机', 1),
('笔记本', 'LAPTOP', 2, '笔记本电脑', 2),
('投影仪', 'PROJECTOR', 3, '投影设备', 1),
('音响', 'SPEAKER', 3, '音响设备', 2);
