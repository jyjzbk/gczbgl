# 第三阶段高级功能开发文档

**文档版本：** v1.0  
**创建时间：** 2025-07-27  
**更新时间：** 2025-07-27  
**开发状态：** 已完成

## 📋 概述

第三阶段开发完成了实验教学管理平台的三个核心高级功能模块，进一步完善了系统的管理能力和智能化水平。本阶段开发的功能包括：

1. **实验要求配置管理系统**
2. **标准实验目录管理功能完善**
3. **实验开出情况监控预警系统**

## 🎯 功能模块详述

### 1. 实验要求配置管理系统

#### 功能概述
为不同级别的教育管理部门提供灵活的实验要求配置功能，支持对实验过程中图片、视频等资料的数量要求进行分级管理。

#### 核心特性
- **分级配置管理**：支持省、市、区县三级组织的独立配置
- **实验类型区分**：分别配置分组实验和演示实验的要求
- **继承机制**：下级可选择继承上级配置或制定独立标准
- **智能计算**：自动计算并显示实际生效的配置
- **权限控制**：基于组织层级的严格权限控制

#### 技术实现
- **数据库表**：`experiment_requirements_config`
- **核心模型**：`ExperimentRequirementsConfig`
- **控制器**：`ExperimentRequirementsConfigController`
- **前端API**：`experimentRequirementsConfig.ts`

#### API接口列表
| 方法 | 路径 | 功能 |
|------|------|------|
| GET | `/api/experiment-requirements-config` | 获取配置列表 |
| POST | `/api/experiment-requirements-config` | 创建新配置 |
| GET | `/api/experiment-requirements-config/{id}` | 获取配置详情 |
| PUT | `/api/experiment-requirements-config/{id}` | 更新配置 |
| DELETE | `/api/experiment-requirements-config/{id}` | 删除配置 |
| POST | `/api/experiment-requirements-config/effective-config` | 获取有效配置 |
| GET | `/api/experiment-requirements-config/organization-options/{type}` | 获取组织选项 |

### 2. 标准实验目录管理功能完善

#### 功能概述
完善标准实验目录管理系统，实现学校对实验目录标准的互斥选择机制，以及删除权限的精细化控制。

#### 核心特性
- **学校互斥选择**：每个学校只能选择一个实验目录标准
- **删除权限控制**：可配置学校是否允许删除不适合的实验
- **删除理由记录**：完整记录删除操作的时间、操作人、理由
- **智能权限继承**：删除权限配置支持上下级继承
- **删除统计监控**：提供删除比例统计和超限预警

#### 技术实现
- **数据库表**：
  - `school_experiment_catalog_selections`（学校选择记录）
  - `experiment_catalog_delete_permissions`（删除权限配置）
- **核心模型**：
  - `SchoolExperimentCatalogSelection`
  - `ExperimentCatalogDeletePermission`
- **控制器**：
  - `SchoolExperimentCatalogController`
  - `ExperimentCatalogDeletePermissionController`

#### API接口列表
| 方法 | 路径 | 功能 |
|------|------|------|
| GET | `/api/school-experiment-catalog/selection` | 获取学校选择 |
| POST | `/api/school-experiment-catalog/selection` | 设置学校选择 |
| GET | `/api/school-experiment-catalog/available-standards` | 获取可选标准 |
| GET | `/api/school-experiment-catalog/available-catalogs` | 获取可用目录 |
| POST | `/api/school-experiment-catalog/delete-catalog` | 删除实验目录 |
| POST | `/api/school-experiment-catalog/restore-catalog` | 恢复实验目录 |
| GET | `/api/school-experiment-catalog/deleted-catalogs` | 获取删除记录 |

### 3. 实验开出情况监控预警系统

#### 功能概述
为区县、省、市管理员提供辖区内实验开出情况的实时监控界面，实现超期未开实验的自动预警提示功能。

#### 核心特性
- **多维度监控**：按学校、教师、学科、时间等维度监控
- **智能预警系统**：超期未开、完成率低、质量评分低三类预警
- **分级预警配置**：支持省市区县级预警阈值配置
- **实时监控仪表板**：直观展示监控数据和预警信息
- **预警处理跟踪**：完整的预警生命周期管理
- **统计分析**：多维度统计分析和趋势预测

#### 技术实现
- **数据库表**：
  - `experiment_alert_config`（预警配置）
  - `experiment_alerts`（预警记录）
  - `experiment_monitoring_statistics`（监控统计）
- **核心模型**：
  - `ExperimentAlertConfig`
  - `ExperimentAlert`
  - `ExperimentMonitoringStatistics`
- **控制器**：
  - `ExperimentMonitoringController`
  - `ExperimentAlertConfigController`

#### API接口列表
| 方法 | 路径 | 功能 |
|------|------|------|
| GET | `/api/experiment-monitoring/dashboard` | 获取监控仪表板 |
| GET | `/api/experiment-monitoring/alerts` | 获取预警列表 |
| POST | `/api/experiment-monitoring/alerts/mark-read` | 标记预警已读 |
| POST | `/api/experiment-monitoring/alerts/resolve` | 解决预警 |
| GET | `/api/experiment-monitoring/school-monitoring` | 获取学校监控详情 |
| POST | `/api/experiment-monitoring/trigger-alert-check` | 手动触发预警检查 |
| GET | `/api/experiment-monitoring/alert-statistics` | 获取预警统计 |

## 🗄️ 数据库设计

### 新增数据表

#### 1. experiment_requirements_config（实验要求配置表）
```sql
CREATE TABLE experiment_requirements_config (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county'),
    organization_id BIGINT UNSIGNED,
    experiment_type ENUM('分组实验', '演示实验'),
    min_images INT DEFAULT 0,
    max_images INT DEFAULT 10,
    min_videos INT DEFAULT 0,
    max_videos INT DEFAULT 3,
    is_inherited BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 2. school_experiment_catalog_selections（学校实验目录选择表）
```sql
CREATE TABLE school_experiment_catalog_selections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED,
    selected_level ENUM('province', 'city', 'county'),
    selected_org_id BIGINT UNSIGNED,
    selected_org_name VARCHAR(100),
    can_delete_experiments BOOLEAN DEFAULT FALSE,
    selection_reason TEXT,
    selected_by BIGINT UNSIGNED,
    selected_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 3. experiment_catalog_delete_permissions（实验目录删除权限表）
```sql
CREATE TABLE experiment_catalog_delete_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county'),
    organization_id BIGINT UNSIGNED,
    organization_name VARCHAR(100),
    allow_school_delete BOOLEAN DEFAULT TRUE,
    require_delete_reason BOOLEAN DEFAULT TRUE,
    max_delete_percentage INT DEFAULT 20,
    delete_rules TEXT,
    created_by BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 4. experiment_alert_config（预警配置表）
```sql
CREATE TABLE experiment_alert_config (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_type ENUM('province', 'city', 'county'),
    organization_id BIGINT UNSIGNED,
    organization_name VARCHAR(100),
    alert_type ENUM('overdue', 'completion_rate', 'quality_score'),
    threshold_value DECIMAL(5,2),
    alert_days INT DEFAULT 7,
    is_active BOOLEAN DEFAULT TRUE,
    alert_rules TEXT,
    notification_settings JSON,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 5. experiment_alerts（预警记录表）
```sql
CREATE TABLE experiment_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_type ENUM('overdue', 'completion_rate', 'quality_score'),
    target_type ENUM('school', 'teacher', 'experiment', 'class'),
    target_id BIGINT UNSIGNED,
    target_name VARCHAR(200),
    alert_level ENUM('low', 'medium', 'high', 'critical'),
    alert_title VARCHAR(200),
    alert_message TEXT,
    alert_data JSON,
    alert_value DECIMAL(8,2),
    threshold_value DECIMAL(8,2),
    is_read BOOLEAN DEFAULT FALSE,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolve_note TEXT,
    resolved_by BIGINT UNSIGNED,
    resolved_at TIMESTAMP,
    alert_time TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 6. experiment_monitoring_statistics（监控统计表）
```sql
CREATE TABLE experiment_monitoring_statistics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    target_type ENUM('school', 'teacher', 'subject', 'grade'),
    target_id BIGINT UNSIGNED,
    target_name VARCHAR(200),
    semester VARCHAR(20),
    statistics_date DATE,
    total_planned_experiments INT DEFAULT 0,
    completed_experiments INT DEFAULT 0,
    overdue_experiments INT DEFAULT 0,
    pending_experiments INT DEFAULT 0,
    completion_rate DECIMAL(5,2) DEFAULT 0,
    overdue_rate DECIMAL(5,2) DEFAULT 0,
    quality_score DECIMAL(5,2) DEFAULT 0,
    avg_completion_days DECIMAL(8,2) DEFAULT 0,
    max_overdue_days INT DEFAULT 0,
    subject_statistics JSON,
    grade_statistics JSON,
    monthly_statistics JSON,
    calculated_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔧 使用说明

### 实验要求配置管理
1. 省级管理员登录系统
2. 进入"实验要求配置管理"页面
3. 创建省级配置，设置图片视频数量要求
4. 市级、区县级管理员可继承或覆盖上级配置
5. 系统自动计算各学校的有效配置

### 学校实验目录选择
1. 学校管理员查看可选择的标准（省、市、区县级）
2. 选择适合的标准并填写选择理由
3. 决定是否申请删除权限
4. 根据选择的标准查看可用的实验目录
5. 如有删除权限，可删除不适合的实验并记录理由

### 监控预警系统
1. 管理员进入监控仪表板查看辖区情况
2. 系统自动检查超期实验、完成率、质量评分
3. 根据预警配置自动生成预警
4. 管理员处理预警并记录解决方案
5. 查看统计报表和趋势分析

## 📊 测试数据

系统已创建完整的测试数据，包括：
- 省、市级预警配置
- 学校实验目录选择示例
- 监控统计数据
- 测试预警记录

## 🎯 下一步计划

1. **前后端集成测试**：验证所有新功能的前后端交互
2. **用户界面完善**：优化前端页面和用户体验
3. **性能优化**：数据库查询优化和缓存策略
4. **安全加固**：输入验证和权限控制加强
5. **部署准备**：生产环境配置和部署脚本

---

**文档维护者**：开发团队  
**联系方式**：项目仓库 Issues  
**最后更新**：2025-07-27
