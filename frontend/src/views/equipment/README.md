# 设备管理模块

## 概述

设备管理模块是实验教学管理平台的核心功能之一，提供完整的设备生命周期管理，包括设备档案管理、借用管理、维修管理和二维码管理等功能。

## 功能模块

### 1. 设备档案管理 (EquipmentManagement.vue)

**功能特点：**
- 设备基本信息录入和管理
- 技术参数和规格管理
- 设备照片上传和管理
- 设备状态和状况跟踪
- 批量导入和导出功能
- 二维码生成和打印

**主要组件：**
- `SimpleEquipmentDialog.vue` - 设备信息编辑对话框
- `EquipmentDetail.vue` - 设备详情查看
- `QRCodeDialog.vue` - 二维码管理
- `BatchImportDialog.vue` - 批量导入

### 2. 设备借用管理 (EquipmentBorrow.vue)

**功能特点：**
- 借用申请流程管理
- 设备可用性检查
- 审批流程和状态跟踪
- 逾期提醒和归还管理
- 借用历史记录

**主要组件：**
- `BorrowDialog.vue` - 借用申请对话框
- `BorrowDetail.vue` - 借用详情查看
- `ApprovalDialog.vue` - 审批对话框
- `ReturnDialog.vue` - 归还对话框

### 3. 设备维修管理 (EquipmentMaintenance.vue)

**功能特点：**
- 故障报修申请
- 维修进度跟踪
- 技师分配和管理
- 维修成本记录
- 维修统计分析

**主要组件：**
- `MaintenanceDialog.vue` - 维修申请对话框
- `MaintenanceDetail.vue` - 维修详情查看
- `TechnicianAssignDialog.vue` - 技师分配
- `MaintenanceCompleteDialog.vue` - 维修完成
- `MaintenanceStatistics.vue` - 维修统计

### 4. 设备二维码管理 (EquipmentQRCode.vue)

**功能特点：**
- 单个和批量二维码生成
- 多种模板和格式支持
- 扫码查询设备信息
- 二维码打印和分享
- 移动端扫码支持

**主要组件：**
- `EquipmentSelector.vue` - 设备选择器
- `BatchPreviewDialog.vue` - 批量预览

## API 接口

### 设备档案接口
```typescript
// 获取设备列表
getEquipmentsApi(params: EquipmentListParams)

// 创建设备
createEquipmentApi(data: CreateEquipmentParams)

// 更新设备
updateEquipmentApi(id: number, data: Partial<CreateEquipmentParams>)

// 删除设备
deleteEquipmentApi(id: number)

// 批量导入
batchImportEquipmentsApi(data: { equipments: CreateEquipmentParams[] })
```

### 设备借用接口
```typescript
// 获取借用列表
getEquipmentBorrowsApi(params: EquipmentBorrowListParams)

// 创建借用申请
createEquipmentBorrowApi(data: CreateEquipmentBorrowParams)

// 审批借用
reviewEquipmentBorrowApi(id: number, data: { status: number; remark?: string })

// 归还设备
returnEquipmentApi(id: number, data: { actual_return_date: string; remark?: string })
```

### 设备维修接口
```typescript
// 获取维修列表
getEquipmentMaintenancesApi(params: EquipmentMaintenanceListParams)

// 创建维修申请
createEquipmentMaintenanceApi(data: CreateEquipmentMaintenanceParams)

// 完成维修
completeEquipmentMaintenanceApi(id: number, data: UpdateEquipmentMaintenanceParams)
```

## 路由配置

```typescript
// 设备档案管理
{
  path: '/equipment-management',
  component: () => import('@/views/equipment/EquipmentManagement.vue'),
  meta: { title: '设备档案管理' }
}

// 设备借用管理
{
  path: '/equipment-borrow',
  component: () => import('@/views/equipment/EquipmentBorrow.vue'),
  meta: { title: '设备借用管理' }
}

// 设备维修管理
{
  path: '/equipment-maintenance',
  component: () => import('@/views/equipment/EquipmentMaintenance.vue'),
  meta: { title: '设备维修管理' }
}

// 设备二维码管理
{
  path: '/equipment-qrcode',
  component: () => import('@/views/equipment/EquipmentQRCode.vue'),
  meta: { title: '设备二维码管理' }
}
```

## 权限控制

设备管理模块支持基于角色的权限控制：

- `equipment:view` - 查看设备信息
- `equipment:create` - 创建设备
- `equipment:edit` - 编辑设备
- `equipment:delete` - 删除设备
- `equipment:import` - 批量导入
- `equipment:borrow:view` - 查看借用记录
- `equipment:borrow:create` - 创建借用申请
- `equipment:borrow:approve` - 审批借用申请
- `equipment:borrow:return` - 归还设备
- `equipment:maintenance:view` - 查看维修记录
- `equipment:maintenance:create` - 创建维修申请
- `equipment:maintenance:assign` - 分配维修技师
- `equipment:maintenance:complete` - 完成维修

## 技术特点

### 1. 组件化设计
- 采用Vue 3 Composition API
- 组件高度复用和模块化
- 统一的设计规范和交互模式

### 2. 响应式设计
- 支持PC端和移动端
- 自适应布局和交互
- 优化的用户体验

### 3. 数据验证
- 完整的前端表单验证
- 实时数据校验和提示
- 防止无效数据提交

### 4. 性能优化
- 懒加载和按需加载
- 虚拟滚动和分页
- 图片压缩和缓存

## 使用说明

### 1. 设备档案管理
1. 点击"新增设备"创建设备档案
2. 填写设备基本信息、采购信息和技术参数
3. 上传设备照片（可选）
4. 保存后可生成设备二维码

### 2. 设备借用管理
1. 点击"新增借用申请"
2. 选择要借用的设备
3. 填写借用信息和用途
4. 提交申请等待审批
5. 审批通过后可借用设备
6. 使用完毕后及时归还

### 3. 设备维修管理
1. 发现设备故障时点击"新增维修申请"
2. 选择故障设备并描述故障现象
3. 系统自动分配维修技师
4. 技师开始维修并记录进度
5. 维修完成后填写维修报告

### 4. 二维码管理
1. 在"二维码生成"标签页选择设备
2. 设置二维码参数和包含信息
3. 生成二维码并可下载或打印
4. 使用"扫码查询"功能查看设备信息

## 注意事项

1. **数据安全**：所有操作都有权限控制，确保数据安全
2. **操作记录**：系统自动记录所有操作日志，便于追溯
3. **备份恢复**：定期备份设备数据，防止数据丢失
4. **移动支持**：二维码扫描功能需要摄像头权限
5. **浏览器兼容**：建议使用Chrome、Firefox等现代浏览器

## 开发规范

1. **代码规范**：遵循Vue 3和TypeScript最佳实践
2. **组件命名**：使用PascalCase命名组件
3. **API调用**：统一使用async/await处理异步操作
4. **错误处理**：完善的错误捕获和用户提示
5. **类型定义**：完整的TypeScript类型定义

## 后续开发

1. **移动端应用**：开发专门的移动端应用
2. **高级统计**：更丰富的数据分析和报表
3. **智能推荐**：基于使用历史的智能推荐
4. **集成扩展**：与其他系统的集成接口
5. **性能优化**：进一步的性能优化和用户体验提升
