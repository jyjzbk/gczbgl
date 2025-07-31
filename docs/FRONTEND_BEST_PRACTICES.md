# 前端开发最佳实践

## 📋 组织架构处理规范

### 1. 组织工具函数使用

**必须使用的工具函数：**
```typescript
import { parseSchoolId, isSchoolNode, getOrganizationType } from '@/utils/organization'
```

**核心函数说明：**
- `parseSchoolId(node)`: 从学校节点提取正确的数字ID
- `isSchoolNode(node)`: 判断节点是否为学校节点
- `getOrganizationType(node)`: 获取组织类型
- `getOrganizationRealId(node)`: 获取组织的真实ID（推荐使用）

### 2. 标准组织选择处理模式

**✅ 推荐方式：**
```typescript
const handleOrganizationSelect = async (organization: OrganizationNode) => {
  console.log('选择组织:', organization)
  selectedOrganization.value = organization
  selectedOrganizationId.value = organization.id

  // 重置分页和搜索条件
  pagination.current_page = 1
  resetSearchForm()

  // 获取组织统计信息 - 使用工具函数
  const orgId = isSchoolNode(organization) ? parseSchoolId(organization) : organization.id
  const orgType = getOrganizationType(organization)
  await fetchOrganizationStats(orgId, orgType)

  // 获取数据列表
  await fetchDataList()
}
```

**❌ 不推荐方式：**
```typescript
// 直接使用organization.id可能导致'school_15'格式问题
await fetchOrganizationStats(organization.id, organization.type)
```

### 3. API参数构建规范

**✅ 推荐方式：**
```typescript
const loadData = async () => {
  const params = {
    organization_id: isSchoolNode(selectedOrganization.value) 
      ? parseSchoolId(selectedOrganization.value) 
      : selectedOrganization.value.id,
    organization_level: selectedOrganization.value.level,
    page: pagination.current_page,
    per_page: pagination.per_page,
    ...searchForm
  }
  
  const response = await getOrganizationDataApi(params)
  // 处理响应...
}
```

### 4. 函数类型定义规范

**✅ 推荐类型定义：**
```typescript
// 支持混合类型，兼容学校ID解析
const fetchOrganizationStats = async (organizationId: number | string, organizationType?: string) => {
  const response = await getOrganizationStatsApi(organizationId, organizationType)
  // 处理响应...
}
```

### 5. 刷新数据处理规范

**✅ 推荐方式：**
```typescript
const refreshData = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.refreshTree()
  }
  if (selectedOrganization.value) {
    const orgId = isSchoolNode(selectedOrganization.value) 
      ? parseSchoolId(selectedOrganization.value) 
      : selectedOrganization.value.id
    const orgType = getOrganizationType(selectedOrganization.value)
    fetchOrganizationStats(orgId, orgType)
    fetchDataList()
  }
}
```

## 🔧 常见问题和解决方案

### 问题1：403 Forbidden错误
**症状：** 点击学校节点时出现403错误
**原因：** 传递了`'school_15'`格式的字符串ID给后端
**解决：** 使用`parseSchoolId()`函数提取数字ID

### 问题2：数据不一致
**症状：** 不同角色看到的统计数据不同
**原因：** ID解析不正确导致权限检查失败
**解决：** 统一使用组织工具函数处理ID

### 问题3：类型错误
**症状：** TypeScript类型检查失败
**原因：** 函数参数类型定义不支持混合类型
**解决：** 更新函数签名支持`number | string`

## 📝 代码检查清单

在开发涉及组织架构的页面时，请检查：

- [ ] 是否导入了组织工具函数
- [ ] `handleOrganizationSelect`是否使用了工具函数
- [ ] API参数构建是否使用了工具函数
- [ ] 函数类型定义是否支持`number | string`
- [ ] `refreshData`函数是否正确处理ID
- [ ] 导出功能是否正确处理ID
- [ ] 所有涉及`selectedOrganization.value.id`的地方是否已修复

## 🎯 适用页面

此规范适用于所有包含组织架构选择的页面：
- 用户管理页面
- 班级管理组件
- 教师管理组件
- 实验室管理页面
- 设备管理页面
- 学校管理页面
- 其他涉及组织数据的页面

## 📚 参考文档

- [API文档](./API_DOCUMENTATION.md) - 组织工具函数详细说明
- [更新日志](./CHANGELOG.md) - 修复历史记录
- [项目状态](./PROJECT_STATUS.md) - 当前开发状态
