# 实验预约系统增强方案

## 📋 需求分析总结

### 现有功能状态
✅ 基础数据表已完备：
- `experiment_catalogs` - 实验目录
- `laboratories` - 实验室管理  
- `experiment_reservations` - 实验预约
- `equipment_borrows` - 设备借用
- `experiment_records` - 实验记录

### 需要增强的功能点

#### 1. 课表形式的预约界面
- **需求**：以课表形式展示实验室使用情况
- **实现**：开发日历组件，支持周/月视图
- **技术**：Vue3 + Element Plus Calendar

#### 2. 智能预约单生成
- **需求**：根据实验自动填充预约信息
- **实现**：关联实验目录，自动获取实验详情
- **数据源**：`experiment_catalogs` 表

#### 3. 器材自动配置
- **需求**：根据实验自动填充所需器材
- **实现**：新增器材需求配置表
- **关联**：实验目录 ↔ 器材需求 ↔ 设备借用

## 🗄️ 数据库结构扩展

### 1. 实验器材需求配置表
```sql
-- 已存在：experiment_equipment_requirements
-- 用于配置每个实验需要的器材清单
```

### 2. 预约模板表 (新增)
```sql
CREATE TABLE experiment_reservation_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT '模板名称',
    school_id BIGINT UNSIGNED NOT NULL COMMENT '学校ID',
    subject_id BIGINT UNSIGNED NOT NULL COMMENT '学科ID',
    grade TINYINT NOT NULL COMMENT '年级',
    semester TINYINT NOT NULL COMMENT '学期',
    template_data JSON NOT NULL COMMENT '模板数据',
    created_by BIGINT UNSIGNED NOT NULL COMMENT '创建人',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_school_subject (school_id, subject_id),
    INDEX idx_grade_semester (grade, semester)
) COMMENT='实验预约模板表';
```

### 3. 预约冲突日志表 (新增)
```sql
CREATE TABLE reservation_conflict_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id BIGINT UNSIGNED NOT NULL COMMENT '预约ID',
    conflict_type ENUM('time', 'equipment', 'capacity') NOT NULL COMMENT '冲突类型',
    conflict_details JSON NOT NULL COMMENT '冲突详情',
    resolved_at TIMESTAMP NULL COMMENT '解决时间',
    resolved_by BIGINT UNSIGNED NULL COMMENT '解决人',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reservation_id (reservation_id),
    INDEX idx_conflict_type (conflict_type)
) COMMENT='预约冲突日志表';
```

## 🔧 API接口设计

### 1. 实验室课表查询
```
GET /api/laboratories/{id}/schedule
参数：
- date_start: 开始日期
- date_end: 结束日期
- view_type: 视图类型(week/month)

返回：课表数据，包含预约信息
```

### 2. 智能预约创建
```
POST /api/experiment-reservations/smart-create
参数：
- catalog_id: 实验目录ID
- laboratory_id: 实验室ID
- reservation_date: 预约日期
- start_time: 开始时间
- class_name: 班级名称
- student_count: 学生人数

功能：
- 自动填充实验信息
- 检测时间冲突
- 生成器材清单
- 创建借用记录
```

### 3. 预约冲突检测
```
POST /api/experiment-reservations/check-conflicts
参数：
- laboratory_id: 实验室ID
- reservation_date: 预约日期
- start_time: 开始时间
- end_time: 结束时间
- equipment_ids: 所需设备ID列表

返回：冲突检测结果
```

## 🎨 前端界面设计

### 1. 课表预约界面
- **位置**：`frontend/src/views/experiment/ExperimentSchedule.vue`
- **功能**：
  - 日历视图展示实验室使用情况
  - 点击空白时段快速预约
  - 拖拽调整预约时间
  - 颜色编码显示不同状态

### 2. 智能预约表单
- **位置**：`frontend/src/views/experiment/SmartReservation.vue`
- **功能**：
  - 实验选择自动填充信息
  - 器材清单自动生成
  - 冲突实时检测提醒
  - 批量预约支持

### 3. 个人实验档案
- **位置**：`frontend/src/views/experiment/PersonalArchive.vue`
- **功能**：
  - 个人预约历史
  - 实验完成率统计
  - 实验作品展示
  - 数据导出功能

## 📱 移动端支持

### 1. 响应式设计
- 适配手机端界面
- 触摸友好的操作
- 简化的预约流程

### 2. 扫码功能
- 实验室二维码签到
- 设备二维码借用
- 快速预约入口

## 🔐 权限控制增强

### 1. 角色权限扩展
```sql
-- 在现有用户角色基础上增加
- experiment_teacher: 实验教师
- lab_manager: 实验室管理员  
- subject_leader: 备课组长
```

### 2. 数据权限规则
- 教师只能查看/预约自己学校的实验室
- 备课组长可统一管理本学科预约
- 实验室管理员可审核所有预约

## 📊 统计分析功能

### 1. 实验室使用率
- 按时间段统计使用率
- 热门时段分析
- 空闲时段推荐

### 2. 教师实验完成率
- 个人实验开出率
- 学科实验完成情况
- 年级对比分析

## 🚀 实施计划

### 阶段一：数据库扩展 (1-2天)
1. 创建新增数据表
2. 完善现有表字段
3. 数据迁移和测试

### 阶段二：后端API开发 (3-4天)
1. 课表查询接口
2. 智能预约接口
3. 冲突检测逻辑
4. 权限控制完善

### 阶段三：前端界面开发 (4-5天)
1. 课表预约界面
2. 智能预约表单
3. 个人档案页面
4. 移动端适配

### 阶段四：集成测试 (1-2天)
1. 功能测试
2. 性能优化
3. 用户体验优化

## 💡 技术要点

### 1. 冲突检测算法
```javascript
// 时间冲突检测
function checkTimeConflict(newReservation, existingReservations) {
  return existingReservations.some(existing => {
    return (newReservation.start_time < existing.end_time && 
            newReservation.end_time > existing.start_time);
  });
}
```

### 2. 器材需求计算
```javascript
// 根据学生人数计算器材需求
function calculateEquipmentNeeds(catalogId, studentCount) {
  const requirements = getEquipmentRequirements(catalogId);
  return requirements.map(req => ({
    equipment_id: req.equipment_id,
    quantity: calculateQuantity(req, studentCount)
  }));
}
```

### 3. 批量预约处理
```javascript
// 批量创建预约（整学期安排）
async function createBatchReservations(template, dateRange) {
  const reservations = generateReservationsFromTemplate(template, dateRange);
  return await Promise.all(reservations.map(createReservation));
}
```

## 🎯 预期效果

1. **提高效率**：教师预约时间从10分钟缩短到2分钟
2. **减少冲突**：智能检测避免90%的预约冲突
3. **简化管理**：管理员审核效率提升50%
4. **数据完整**：实验档案完整率达到95%以上

---

**注意**：本方案基于现有代码结构设计，确保不会影响现有用户、组织、权限等核心数据。
