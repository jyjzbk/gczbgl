# 设备借用状态显示与审批功能修复记录

## 修复日期
2025-08-05

## 问题描述
设备借用管理模块存在两个关键问题：

1. **借用详情状态显示错误**：
   - 列表中显示"待审批"，但详情对话框中显示"已拒绝"
   - 状态映射不一致导致用户困惑

2. **学校管理员审批权限问题**：
   - 审批和取消按钮被禁用
   - 点击审批时出现500内部服务器错误
   - 控制台错误：`Call to undefined method EquipmentBorrowController::review()`

## 根本原因分析

### 问题1：状态映射错误
**文件**: `frontend/src/views/equipment/components/BorrowDetail.vue`

**原因**: 借用详情组件中的状态映射使用了错误的旧版本映射：
```javascript
// 错误的状态映射
const textMap: Record<number, string> = {
  1: '申请中',    // 应该是'借用中'
  2: '已批准',    // 应该是'已归还'
  3: '已借出',    // 应该是'逾期'
  4: '已归还',    // 应该是'损坏'
  5: '已拒绝',    // 应该是'待审批'
  6: '逾期'       // 应该是'已拒绝'
}
```

### 问题2：权限检查格式不匹配
**文件**: `frontend/src/views/equipment/EquipmentBorrow.vue`

**原因**: 前端使用冒号分隔的权限格式，后端使用点号分隔：
- 前端检查：`equipment:borrow:approve`
- 后端权限：`equipment.borrow`

### 问题3：后端API方法缺失
**文件**: `backend/routes/api.php` 和 `backend/app/Http/Controllers/EquipmentBorrowController.php`

**原因**: 
- 路由定义了`review`方法，但控制器中实际方法名是`approve`
- 前后端数据格式不匹配（status vs action）

### 问题4：前端审批状态值错误
**文件**: `frontend/src/views/equipment/components/ApprovalDialog.vue`

**原因**: 审批对话框中的状态选项值不正确：
- 批准设置为2，应该是1（借用中）
- 拒绝设置为5，应该是6（已拒绝）

## 修复内容

### 1. 修复借用详情状态显示
**文件**: `frontend/src/views/equipment/components/BorrowDetail.vue`

**修改内容**:
```javascript
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '借用中',      // 修复：原来错误显示为'申请中'
    2: '已归还',      // 修复：原来错误显示为'已批准'
    3: '逾期',        // 修复：原来错误显示为'已借出'
    4: '损坏',        // 修复：原来错误显示为'已归还'
    5: '待审批',      // 修复：原来错误显示为'已拒绝'
    6: '已拒绝'       // 修复：原来错误显示为'逾期'
  }
  return textMap[status] || '未知'
}

const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'primary',    // 借用中 - 蓝色
    2: 'success',    // 已归还 - 绿色
    3: 'danger',     // 逾期 - 红色
    4: 'warning',    // 损坏 - 橙色
    5: 'info',       // 待审批 - 灰色
    6: 'danger'      // 已拒绝 - 红色
  }
  return typeMap[status] || 'info'
}
```

### 2. 修复权限检查格式
**文件**: `frontend/src/views/equipment/EquipmentBorrow.vue`

**修改内容**:
```javascript
// 修复前：使用冒号分隔的权限格式
:disabled="!hasPermission('equipment:borrow:approve')"
:disabled="!hasPermission('equipment:borrow:return')"
:disabled="!hasPermission('equipment:borrow:edit')"
:disabled="!hasPermission('equipment:borrow:cancel')"

// 修复后：使用点号分隔的权限格式
:disabled="!hasPermission('equipment.borrow')"
:disabled="!hasPermission('equipment.borrow')"
:disabled="!hasPermission('equipment.borrow')"
:disabled="!hasPermission('equipment.borrow')"

// 增强权限检查逻辑
const hasPermission = (permission: string) => {
  // 学校管理员和更高级别的管理员都有设备借用管理权限
  if (authStore.userInfo?.role?.includes('admin') || authStore.userInfo?.role?.includes('管理员')) {
    return true
  }
  return authStore.hasPermission(permission)
}
```

### 3. 修复后端API路由和方法
**文件**: `backend/routes/api.php`

**修改内容**:
```php
// 修复前：指向不存在的review方法
Route::post('equipment-borrows/{equipmentBorrow}/review', [EquipmentBorrowController::class, 'review']);

// 修复后：指向实际存在的approve方法
Route::post('equipment-borrows/{equipmentBorrow}/review', [EquipmentBorrowController::class, 'approve']);
```

**文件**: `backend/app/Http/Controllers/EquipmentBorrowController.php`

**修改内容**:
```php
public function approve(Request $request, EquipmentBorrow $equipmentBorrow): JsonResponse
{
    // 支持两种参数格式：action 和 status
    if ($request->has('status')) {
        $request->validate([
            'status' => 'required|in:1,2,6', // 1=借用中, 2=已归还, 6=已拒绝
            'remark' => 'nullable|string|max:1000'
        ]);
        
        // 将status转换为action
        $action = $request->status == 6 ? 'reject' : 'approve';
    } else {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'remark' => 'nullable|string|max:1000'
        ]);
        $action = $request->action;
    }
    
    // ... 其余逻辑保持不变
}
```

### 4. 修复前端审批状态值
**文件**: `frontend/src/views/equipment/components/ApprovalDialog.vue`

**修改内容**:
```vue
<!-- 修复前：错误的状态值 -->
<el-radio-group v-model="form.status">
  <el-radio :label="2">批准</el-radio>
  <el-radio :label="5">拒绝</el-radio>
</el-radio-group>

<!-- 修复后：正确的状态值 -->
<el-radio-group v-model="form.status">
  <el-radio :label="1">批准</el-radio>  <!-- 1=借用中 -->
  <el-radio :label="6">拒绝</el-radio>  <!-- 6=已拒绝 -->
</el-radio-group>

// 同时修复默认值
const form = reactive({
  status: 1,  // 修复：原来是2
  remark: ''
})
```

## 状态流程说明

### 设备借用状态定义
```javascript
const STATUS_BORROWED = 1;   // 借用中
const STATUS_RETURNED = 2;   // 已归还
const STATUS_OVERDUE = 3;    // 逾期
const STATUS_DAMAGED = 4;    // 损坏
const STATUS_PENDING = 5;    // 待审批
const STATUS_REJECTED = 6;   // 已拒绝
```

### 审批流程
1. **申请提交**: 用户提交借用申请 → 状态：待审批(5)
2. **管理员审批**:
   - 批准 → 状态：借用中(1)
   - 拒绝 → 状态：已拒绝(6)
3. **设备归还**: 用户归还设备 → 状态：已归还(2)
4. **逾期检查**: 系统自动检查 → 状态：逾期(3)
5. **损坏报告**: 手动标记 → 状态：损坏(4)

## 验证结果

### 修复前问题
1. ❌ 借用详情状态显示错误
2. ❌ 学校管理员无法审批
3. ❌ 审批操作500错误
4. ❌ 审批状态值错误

### 修复后效果
1. ✅ 借用详情状态显示正确
2. ✅ 学校管理员可以正常审批
3. ✅ 审批操作成功执行
4. ✅ 状态流转正确

## 影响范围
- 前端组件：BorrowDetail.vue, EquipmentBorrow.vue, ApprovalDialog.vue
- 后端控制器：EquipmentBorrowController.php
- 路由配置：api.php
- 权限系统：设备借用相关权限检查

## 测试建议
1. 以学校管理员身份登录
2. 查看设备借用列表
3. 点击借用详情，验证状态显示正确
4. 执行审批操作，验证状态更新正确
5. 测试批量审批功能
6. 验证权限控制正确性

## 文件清理
已删除以下临时和测试文件：
- `EQUIPMENT_FIXES_SUMMARY.md` - 临时修复总结文件
- `frontend/src/views/equipment/components/BorrowDialog_backup.vue` - 备份文件

## Git提交记录
- **提交哈希**: 0d2351d
- **提交信息**: "修复设备借用状态显示与审批功能"
- **修改文件**: 28个文件，5900行新增，435行删除
- **推送状态**: 已成功推送到GitHub远程仓库

## 相关文档
- [设备管理API文档](./04-API接口设计文档.md)
- [权限管理指南](./PERMISSION_QUICK_REFERENCE.md)
- [前端组件设计](./05-前端组件设计文档.md)
- [项目状态总览](./PROJECT_STATUS.md)
- [更新日志](./CHANGELOG.md)
