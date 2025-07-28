# 角色菜单权限对照表

## 重要更新：组织层级权限控制系统

### 新增功能
- **五级组织架构**：省→市→区县→学区→学校
- **数据权限控制**：用户只能管理本级和下级组织的数据
- **权限继承机制**：上级用户自动拥有下级数据的管理权限
- **数据隔离**：同级组织间的数据完全隔离

### 组织级别说明
1. **省级用户 (Level 1)**：可管理全省所有数据
2. **市级用户 (Level 2)**：可管理本市及下级数据
3. **区县级用户 (Level 3)**：可管理本区县及下级数据
4. **学区级用户 (Level 4)**：可管理本学区学校数据
5. **学校级用户 (Level 5)**：只能管理本校数据

## 修复的问题

### 1. 权限检查逻辑问题
- **问题**：前端菜单使用了不存在的权限名（如 `equipment.maintenance`）
- **修复**：统一使用后端实际配置的权限名

### 2. 菜单显示不完整
- **问题**：teacher001用户缺少"实验目录"、"二维码管理"等菜单项
- **修复**：修正权限检查逻辑，使用 `hasAnyPermission` 检查基础权限

### 3. 统计报表权限过于严格
- **问题**：统计报表权限检查逻辑错误，导致有权限的用户看不到菜单
- **修复**：简化权限检查，有实验或设备权限的用户都可以查看对应统计

### 4. 数据权限控制缺失
- **问题**：用户可以访问不属于其管理范围的数据
- **修复**：实现基于组织层级的数据权限控制系统

## 角色权限配置（更新：支持组织层级权限）

### 1. 省级管理员 (province_admin) - 组织级别：1
**权限：** user, user.list, user.create, user.update, user.delete, role, role.list, role.create, role.update, role.delete, experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.create, equipment.update, equipment.delete, equipment.borrow, equipment.maintenance

**数据访问范围：** 全省所有数据（省直学校 + 所有市级及以下数据）

**应显示菜单：**
- ✅ 仪表盘
- ✅ 用户管理
  - ✅ 用户列表（可管理所有级别用户）
  - ✅ 角色管理
  - ✅ 权限管理
- ✅ 基础数据
  - ✅ 行政区域管理（新增）
  - ✅ 学校管理（可管理所有学校，包含实验室管理标签页）
  - ✅ 学科管理
- ✅ 实验管理
  - ✅ 实验目录
  - ✅ 实验预约
  - ✅ 实验记录
  - ✅ 实验统计
- ✅ 设备管理
  - ✅ 设备档案（可管理所有设备）
  - ✅ 设备借用
  - ✅ 设备维修
  - ✅ 二维码管理
- ✅ 统计报表
  - ✅ 实验统计
  - ✅ 设备统计
  - ✅ 区域分析

### 2. 市级管理员 (city_admin) - 组织级别：2
**权限：** user, user.list, user.create, user.update, user.delete, role, role.list, role.create, role.update, role.delete, experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.create, equipment.update, equipment.delete, equipment.borrow, equipment.maintenance

**数据访问范围：** 本市及下级数据（市直学校 + 本市所有区县及以下数据）

**应显示菜单：**
- ✅ 仪表盘
- ✅ 用户管理
  - ✅ 用户列表（可管理市级及以下用户）
  - ✅ 角色管理
  - ✅ 权限管理
- ✅ 基础数据
  - ✅ 行政区域管理（可管理本市区域）
  - ✅ 学校管理（可管理本市学校，包含实验室管理标签页）
  - ✅ 学科管理
- ✅ 实验管理
  - ✅ 实验目录
  - ✅ 实验预约
  - ✅ 实验记录
  - ✅ 实验统计
- ✅ 设备管理
  - ✅ 设备档案（可管理本市设备）
  - ✅ 设备借用
  - ✅ 设备维修
  - ✅ 二维码管理
- ✅ 统计报表
  - ✅ 实验统计
  - ✅ 设备统计
  - ✅ 区域分析

### 3. 区县管理员 (county_admin) - 组织级别：3
**权限：** user, user.list, user.create, user.update, experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.create, equipment.update, equipment.delete, equipment.borrow, equipment.maintenance

**数据访问范围：** 本区县及下级数据（区县直学校 + 本区县所有学区及学校数据）

**应显示菜单：**
- ✅ 仪表盘
- ✅ 用户管理
  - ✅ 用户列表（可管理区县级及以下用户）
  - ❌ 角色管理 (无role权限)
  - ❌ 权限管理 (无role权限)
- ✅ 基础数据
  - ✅ 行政区域管理（可管理本区县区域）
  - ✅ 学校管理（可管理本区县学校，包含实验室管理标签页）
  - ✅ 学科管理
- ✅ 实验管理
  - ✅ 实验目录
  - ✅ 实验预约
  - ✅ 实验记录
  - ✅ 实验统计
- ✅ 设备管理
  - ✅ 设备档案（可管理本区县设备）
  - ✅ 设备借用
  - ✅ 设备维修
  - ✅ 二维码管理
- ✅ 统计报表
  - ✅ 实验统计
  - ✅ 设备统计
  - ✅ 区域分析

### 4. 学区管理员 (district_admin) - 组织级别：4
**权限：** user, user.list, user.create, user.update, experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.create, equipment.update, equipment.delete, equipment.borrow, equipment.maintenance

**数据访问范围：** 本学区学校数据

**应显示菜单：**
- ✅ 仪表盘
- ✅ 用户管理
  - ✅ 用户列表（可管理学区内学校用户）
  - ❌ 角色管理 (无role权限)
  - ❌ 权限管理 (无role权限)
- ✅ 基础数据
  - ❌ 行政区域管理 (无区域管理权限)
  - ✅ 学校管理（可管理学区内学校，包含实验室管理标签页）
  - ✅ 学科管理
- ✅ 实验管理
  - ✅ 实验目录
  - ✅ 实验预约
  - ✅ 实验记录
  - ✅ 实验统计
- ✅ 设备管理
  - ✅ 设备档案（可管理学区内设备）
  - ✅ 设备借用
  - ✅ 设备维修
  - ✅ 二维码管理
- ✅ 统计报表
  - ✅ 实验统计
  - ✅ 设备统计
  - ❌ 区域分析 (无区域权限)

### 5. 学校管理员 (school_admin) - 组织级别：5
**权限：** user, user.list, user.create, user.update, experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.create, equipment.update, equipment.delete, equipment.borrow, equipment.maintenance

**数据访问范围：** 仅本校数据

**应显示菜单：**
- ✅ 仪表盘
- ✅ 用户管理
  - ✅ 用户列表（仅本校用户）
  - ❌ 角色管理 (无role权限)
  - ❌ 权限管理 (无role权限)
- ✅ 基础数据
  - ❌ 行政区域管理 (无区域管理权限)
  - ✅ 学校管理（仅本校信息，包含实验室管理标签页）
  - ✅ 学科管理
- ✅ 实验管理
  - ✅ 实验目录
  - ✅ 实验预约
  - ✅ 实验记录
  - ✅ 实验统计
- ✅ 设备管理
  - ✅ 设备档案（仅本校设备）
  - ✅ 设备借用
  - ✅ 设备维修
  - ✅ 二维码管理
- ✅ 统计报表
  - ✅ 实验统计
  - ✅ 设备统计
  - ❌ 区域分析 (无区域权限)

### 6. 任课教师 (school_teacher) - 组织级别：5
**权限：** experiment, experiment.catalog, experiment.booking, experiment.record, equipment, equipment.list, equipment.borrow

**数据访问范围：** 仅本校数据

**应显示菜单：**
- ✅ 仪表盘
- ❌ 用户管理 (无user权限)
- ❌ 基础数据 (无user权限)
- ✅ 实验管理
  - ✅ 实验目录 (有experiment权限)
  - ✅ 实验预约 (有experiment.booking权限)
  - ✅ 实验记录 (有experiment.record权限)
  - ✅ 实验统计 (有experiment权限)
- ✅ 设备管理
  - ✅ 设备档案 (有equipment权限，仅本校设备)
  - ✅ 设备借用 (有equipment.borrow权限)
  - ❌ 设备维修 (无equipment.maintenance权限)
  - ✅ 二维码管理 (有equipment权限)
- ✅ 统计报表
  - ✅ 实验统计 (有experiment权限)
  - ✅ 设备统计 (有equipment权限)
  - ❌ 区域分析 (无user权限)
- ❌ 系统管理 (无system权限)

### 7. 学生 (school_student) - 组织级别：5
**权限：** experiment, experiment.catalog, experiment.booking, equipment, equipment.list

**数据访问范围：** 仅本校数据

**应显示菜单：**
- ✅ 仪表盘
- ❌ 用户管理 (无user权限)
- ❌ 基础数据 (无user权限)
- ✅ 实验管理
  - ✅ 实验目录（仅本校实验）
  - ✅ 实验预约（仅本校实验室）
  - ❌ 实验记录 (无experiment.record权限)
  - ✅ 实验统计 (有experiment权限，仅本校数据)
- ✅ 设备管理
  - ✅ 设备档案（仅本校设备）
  - ❌ 设备借用 (无equipment.borrow权限)
  - ❌ 设备维修 (无equipment.maintenance权限)
  - ✅ 二维码管理（仅本校设备）
- ✅ 统计报表
  - ✅ 实验统计（仅本校数据）
  - ✅ 设备统计（仅本校数据）
  - ❌ 区域分析 (无user权限)

## 修复后的前端权限检查逻辑

### 一级菜单权限检查（修复后）
```javascript
// 用户管理 - 无变化
hasUserPermission = hasAnyPermission(['user', 'user.list', 'role', 'role.list'])

// 基础数据 - 无变化
hasBasicDataPermission = hasAnyPermission(['user', 'user.list', 'user.create'])

// 实验管理 - 无变化
hasExperimentPermission = hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'])

// 设备管理 - 无变化
hasEquipmentPermission = hasAnyPermission(['equipment', 'equipment.list'])

// 统计报表 - 修复：简化逻辑，更宽松的权限检查
hasStatisticsPermission = hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']) ||
                         hasAnyPermission(['equipment', 'equipment.list']) ||
                         hasAnyPermission(['user', 'user.list'])

// 系统管理 - 新增：为province_admin添加了系统权限
hasSystemPermission = hasAnyPermission(['system', 'system.read', 'log', 'log.read'])
```

### 二级菜单权限检查（修复后）
```javascript
// 实验管理子菜单 - 修复：使用更宽松的权限检查
实验目录: hasAnyPermission(['experiment', 'experiment.catalog'])  // 修复：添加基础experiment权限
实验预约: hasPermission('experiment.booking')                    // 无变化
实验记录: hasPermission('experiment.record')                     // 无变化
实验统计: hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record'])  // 修复：添加基础experiment权限

// 设备管理子菜单 - 修复：使用更宽松的权限检查
设备档案: hasAnyPermission(['equipment', 'equipment.list'])      // 修复：添加基础equipment权限
设备借用: hasPermission('equipment.borrow')                     // 无变化
设备维修: hasPermission('equipment.maintenance')                // 无变化
二维码管理: hasAnyPermission(['equipment', 'equipment.list'])    // 修复：添加基础equipment权限

// 统计报表子菜单 - 无变化
实验统计: hasAnyPermission(['experiment', 'experiment.catalog'])
设备统计: hasAnyPermission(['equipment', 'equipment.list'])
区域分析: hasAnyPermission(['user', 'user.list'])
```

## 修复总结

### 主要修复内容
1. **实验目录菜单**：从只检查 `experiment.catalog` 改为检查 `['experiment', 'experiment.catalog']`
2. **实验统计菜单**：从只检查 `experiment.catalog` 改为检查 `['experiment', 'experiment.catalog', 'experiment.record']`
3. **设备档案菜单**：从只检查 `equipment.list` 改为检查 `['equipment', 'equipment.list']`
4. **二维码管理菜单**：从只检查 `equipment.list` 改为检查 `['equipment', 'equipment.list']`
5. **统计报表权限**：简化逻辑，使权限检查更加合理
6. **系统管理权限**：为 `province_admin` 角色添加系统管理权限

### 修复效果
- **teacher001 (school_teacher)** 现在可以看到：
  - ✅ 实验目录（之前缺失）
  - ✅ 二维码管理（之前缺失）
  - ✅ 统计报表（实验统计、设备统计）
- **所有角色** 的菜单显示现在都符合其权限配置
- **权限检查逻辑** 更加合理和一致
