<template>
  <div class="permission-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>权限管理</h2>
        <p>管理系统权限和访问控制</p>
      </div>
    </div>

    <el-row :gutter="20">
      <!-- 左侧：角色列表 -->
      <el-col :span="8">
        <el-card>
          <template #header>
            <div class="card-header">
              <span>角色列表</span>
              <el-button type="primary" size="small" @click="fetchRoleList">刷新</el-button>
            </div>
          </template>

          <div v-loading="roleLoading" class="role-list">
            <div
              v-for="role in roleList"
              :key="role.id"
              class="role-item"
              :class="{ active: selectedRole?.id === role.id }"
              @click="selectRole(role)"
            >
              <div class="role-info">
                <h4>{{ role.name }}</h4>
                <p>{{ role.description }}</p>
                <el-tag :type="getLevelType(role.level)" size="small">
                  级别 {{ role.level }}
                </el-tag>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- 右侧：权限配置 -->
      <el-col :span="16">
        <el-card>
          <template #header>
            <div class="card-header">
              <span>
                {{ selectedRole ? `${selectedRole.name} - 权限配置` : '请选择角色' }}
              </span>
              <div v-if="selectedRole">
                <el-button type="success" size="small" @click="savePermissions" :loading="saving">
                  保存权限
                </el-button>
                <el-button type="warning" size="small" @click="loadDefaultPermissions" :loading="loading">
                  恢复默认
                </el-button>
                <el-button size="small" @click="expandAllPermissions">
                  展开全部
                </el-button>
                <el-button size="small" @click="collapseAllPermissions">
                  折叠全部
                </el-button>
                <el-button type="primary" size="small" @click="refreshCurrentRolePermissions">
                  刷新权限
                </el-button>
                <el-button type="info" size="small" @click="debugPermissionTree">
                  调试
                </el-button>
              </div>
            </div>
          </template>

          <div v-if="selectedRole">
            <el-tree
              ref="permissionTreeRef"
              v-loading="loading"
              :data="permissionTree"
              :props="treeProps"
              show-checkbox
              node-key="id"
              :default-expanded-keys="expandedKeys"
              :check-strictly="false"
              @check="handlePermissionCheck"
            >
              <template #default="{ node, data }">
                <div class="permission-node">
                  <div class="node-content">
                    <span class="node-label">{{ data.name }}</span>
                    <span class="node-code">{{ data.code }}</span>
                  </div>
                  <div class="node-actions">
                    <el-tag v-if="data.type" :type="getPermissionType(data.type)" size="small">
                      {{ getPermissionTypeLabel(data.type) }}
                    </el-tag>
                    <el-tag v-if="data.level === 'high'" type="danger" size="small">
                      高级
                    </el-tag>
                  </div>
                </div>
              </template>
            </el-tree>
          </div>

          <div v-else class="no-role-selected">
            <el-empty description="请从左侧选择一个角色来配置权限" />
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { ElMessage, ElTree } from 'element-plus'
import { getPermissionTreeApi, getRoleListApi, getRolePermissionsApi, assignRolePermissionsApi, type Permission } from '@/api/role'

// 角色相关数据
const roleList = ref([])
const selectedRole = ref(null)
const roleLoading = ref(false)

// 权限树数据
const permissionTree = ref<Permission[]>([])
const rolePermissions = ref([])
const loading = ref(false)
const saving = ref(false)

// 树形组件引用
const permissionTreeRef = ref<InstanceType<typeof ElTree>>()

const treeProps = {
  children: 'children',
  label: 'name'
}

const expandedKeys = ref(['user', 'experiment', 'equipment'])

// 获取角色列表
const fetchRoleList = async () => {
  roleLoading.value = true
  try {
    // 使用 all=true 参数获取所有角色，不分页
    const response = await getRoleListApi({ all: 'true' })

    // 处理响应格式
    if (response.data) {
      // 检查 response.data 是否直接是数组（all=true的情况）
      if (Array.isArray(response.data)) {
        roleList.value = [...response.data]
      }
      // 检查 response.data.data 是否是数组（分页格式）
      else if (response.data.data && Array.isArray(response.data.data)) {
        roleList.value = [...response.data.data]
      }
      // 检查是否是分页格式的嵌套结构
      else if (response.data.data && response.data.data.data && Array.isArray(response.data.data.data)) {
        roleList.value = [...response.data.data.data]
      }
      else {
        roleList.value = []
      }
    } else {
      roleList.value = []
    }
  } catch (error) {
    console.error('获取角色列表失败:', error)
    ElMessage.error('获取角色列表失败')
    roleList.value = []
  } finally {
    roleLoading.value = false
  }
}

// 选择角色
const selectRole = async (role: any) => {
  selectedRole.value = role
  await fetchRolePermissions(role.id)
}

// 获取角色权限
const fetchRolePermissions = async (roleId: number) => {
  try {
    console.log('获取角色权限，角色ID:', roleId)
    const response = await getRolePermissionsApi(roleId)
    console.log('角色权限API响应:', response)

    // 处理响应数据
    let permissions = []
    if (response.data && response.data.data) {
      permissions = response.data.data
    } else if (response.data) {
      permissions = response.data
    }

    rolePermissions.value = permissions || []
    console.log('设置的角色权限:', rolePermissions.value)

    // 设置树形组件的选中状态
    if (permissionTreeRef.value) {
      // 清除所有选中状态
      permissionTreeRef.value.setCheckedKeys([])
      // 等待DOM更新后再设置新的选中状态
      await nextTick()

      // 设置权限，不启用级联选择以避免自动勾选父节点
      console.log('设置权限:', rolePermissions.value)
      permissionTreeRef.value.setCheckedKeys(rolePermissions.value, false) // false表示不级联选择
      console.log('树形组件选中状态已更新')

      // 验证实际选中的权限
      setTimeout(() => {
        if (permissionTreeRef.value) {
          const actualChecked = permissionTreeRef.value.getCheckedKeys()
          const halfChecked = permissionTreeRef.value.getHalfCheckedKeys()
          console.log('实际选中的权限:', actualChecked)
          console.log('半选中的权限:', halfChecked)
        }
      }, 100)
    }
  } catch (error) {
    console.error('获取角色权限失败:', error)
    ElMessage.error('获取角色权限失败')
    rolePermissions.value = []
  }
}

// 保存权限配置
const savePermissions = async () => {
  if (!selectedRole.value || !permissionTreeRef.value) {
    console.log('保存权限失败：缺少必要参数', { selectedRole: selectedRole.value, permissionTreeRef: permissionTreeRef.value })
    return
  }

  saving.value = true
  try {
    const checkedKeys = permissionTreeRef.value.getCheckedKeys()
    const halfCheckedKeys = permissionTreeRef.value.getHalfCheckedKeys()

    // 只保存完全选中的权限，不包含半选中的父节点
    // 这样可以避免保存不应该保存的父节点权限
    const allPermissions = [...checkedKeys]

    console.log('保存权限配置:', {
      roleId: selectedRole.value.id,
      roleName: selectedRole.value.name,
      checkedKeys,
      halfCheckedKeys,
      allPermissions
    })

    const response = await assignRolePermissionsApi(selectedRole.value.id, allPermissions)
    console.log('权限保存响应:', response)

    ElMessage.success('权限配置保存成功')

    // 重新获取权限以确认保存成功
    await fetchRolePermissions(selectedRole.value.id)
  } catch (error) {
    console.error('保存权限配置失败:', error)
    ElMessage.error('保存权限配置失败')
  } finally {
    saving.value = false
  }
}

// 刷新当前角色权限
const refreshCurrentRolePermissions = async () => {
  if (selectedRole.value) {
    await fetchRolePermissions(selectedRole.value.id)
    ElMessage.success('权限已刷新')
  }
}

// 加载默认权限配置
const loadDefaultPermissions = async () => {
  if (!selectedRole.value) return

  try {
    loading.value = true
    const response = await fetch(`http://localhost:8000/api/roles/${selectedRole.value.id}/default-permissions`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('获取默认权限失败')
    }

    const data = await response.json()

    if (data.success) {
      // 设置默认权限
      rolePermissions.value = data.data

      // 更新树形组件选中状态
      if (permissionTreeRef.value) {
        permissionTreeRef.value.setCheckedKeys([])
        await nextTick()
        permissionTreeRef.value.setCheckedKeys(data.data, false)
      }

      ElMessage.success('已恢复默认权限配置')
    } else {
      throw new Error(data.message || '获取默认权限失败')
    }
  } catch (error) {
    console.error('加载默认权限失败:', error)
    ElMessage.error('加载默认权限失败')
  } finally {
    loading.value = false
  }
}

// 展开全部权限
const expandAllPermissions = () => {
  if (permissionTreeRef.value) {
    const allKeys: string[] = []
    const collectKeys = (nodes: any[]) => {
      nodes.forEach(node => {
        allKeys.push(node.id)
        if (node.children && node.children.length > 0) {
          collectKeys(node.children)
        }
      })
    }
    collectKeys(permissionTree.value)
    permissionTreeRef.value.setExpandedKeys(allKeys)
  }
}

// 折叠全部权限
const collapseAllPermissions = () => {
  if (permissionTreeRef.value) {
    permissionTreeRef.value.setExpandedKeys([])
  }
}

// 处理权限选中事件
const handlePermissionCheck = (data: any, checked: any) => {
  console.log('权限选中事件:', { data, checked })
  // Element Plus 的树形组件在 check-strictly="false" 时会自动处理父子联动
  // 这里可以添加额外的业务逻辑，比如记录操作日志等
}

// 调试：打印权限树结构
const debugPermissionTree = () => {
  console.log('权限树结构:', permissionTree.value)
  if (permissionTreeRef.value) {
    console.log('当前选中的权限:', permissionTreeRef.value.getCheckedKeys())
    console.log('半选中的权限:', permissionTreeRef.value.getHalfCheckedKeys())
  }
}

// 获取权限树
const fetchPermissionTree = async () => {
  try {
    loading.value = true
    console.log('正在获取权限树...')

    const response = await getPermissionTreeApi()
    console.log('权限树API响应:', response)

    if (response.data) {
      permissionTree.value = response.data
    } else {
      permissionTree.value = []
    }

    console.log('权限树加载成功:', permissionTree.value)
  } catch (error) {
    console.error('获取权限树失败:', error)
    ElMessage.error('获取权限树失败')

    // 使用默认的权限树作为后备
    permissionTree.value = [
      {
        id: 'user',
        name: '用户管理',
        code: 'user',
        children: [
          { id: 'user.list', name: '用户列表', code: 'user.list', type: 'read' },
          { id: 'user.create', name: '创建用户', code: 'user.create', type: 'write' },
          { id: 'user.update', name: '更新用户', code: 'user.update', type: 'write' },
          { id: 'user.edit', name: '编辑用户', code: 'user.edit', type: 'write' },
          { id: 'user.delete', name: '删除用户', code: 'user.delete', type: 'delete' },
          { id: 'user.export', name: '导出用户', code: 'user.export', type: 'advanced', level: 'high' },
          { id: 'user.reset_password', name: '重置密码', code: 'user.reset_password', type: 'advanced', level: 'high' }
        ]
      },
      {
        id: 'role',
        name: '角色管理',
        code: 'role',
        children: [
          { id: 'role.list', name: '角色列表', code: 'role.list', type: 'read' },
          { id: 'role.create', name: '创建角色', code: 'role.create', type: 'write' },
          { id: 'role.update', name: '编辑角色', code: 'role.update', type: 'write' },
          { id: 'role.delete', name: '删除角色', code: 'role.delete', type: 'delete' }
        ]
      },
      {
        id: 'experiment',
        name: '实验管理',
        code: 'experiment',
        children: [
          {
            id: 'experiment.catalog',
            name: '实验目录',
            code: 'experiment.catalog',
            type: 'read',
            children: [
              { id: 'experiment.catalog.view', name: '查看实验目录', code: 'experiment.catalog.view', type: 'read' },
              { id: 'experiment.catalog.create', name: '创建实验目录', code: 'experiment.catalog.create', type: 'write' },
              { id: 'experiment.catalog.edit', name: '编辑实验目录', code: 'experiment.catalog.edit', type: 'write' },
              { id: 'experiment.catalog.delete', name: '删除实验目录', code: 'experiment.catalog.delete', type: 'delete' },
              { id: 'experiment.catalog.copy', name: '复制实验目录', code: 'experiment.catalog.copy', type: 'write' },
              { id: 'experiment.catalog.manage_level', name: '管理级别权限', code: 'experiment.catalog.manage_level', type: 'advanced', level: 'high' }
            ]
          },
          { id: 'experiment.booking', name: '实验预约', code: 'experiment.booking', type: 'write' },
          { id: 'experiment.record', name: '实验记录', code: 'experiment.record', type: 'write' }
        ]
      },
      {
        id: 'equipment',
        name: '设备管理',
        code: 'equipment',
        children: [
          { id: 'equipment.list', name: '设备列表', code: 'equipment.list', type: 'read' },
          { id: 'equipment.create', name: '添加设备', code: 'equipment.create', type: 'write' },
          { id: 'equipment.update', name: '编辑设备', code: 'equipment.update', type: 'write' },
          { id: 'equipment.delete', name: '删除设备', code: 'equipment.delete', type: 'delete' },
          { id: 'equipment.borrow', name: '设备借用', code: 'equipment.borrow', type: 'write' },
          { id: 'equipment.maintenance', name: '设备维修', code: 'equipment.maintenance', type: 'write' }
        ]
      },
      {
        id: 'basic',
        name: '基础数据',
        code: 'basic',
        children: [
          {
            id: 'basic.subject',
            name: '学科管理',
            code: 'basic.subject',
            type: 'read',
            children: [
              { id: 'basic.subject.view', name: '查看学科', code: 'basic.subject.view', type: 'read' },
              { id: 'basic.subject.create', name: '创建学科', code: 'basic.subject.create', type: 'write' },
              { id: 'basic.subject.edit', name: '编辑学科', code: 'basic.subject.edit', type: 'write' },
              { id: 'basic.subject.delete', name: '删除学科', code: 'basic.subject.delete', type: 'delete' }
            ]
          },
          {
            id: 'basic.equipment_standard',
            name: '教学仪器配备标准',
            code: 'basic.equipment_standard',
            type: 'read',
            children: [
              { id: 'basic.equipment_standard.view', name: '查看配备标准', code: 'basic.equipment_standard.view', type: 'read' },
              { id: 'basic.equipment_standard.create', name: '创建配备标准', code: 'basic.equipment_standard.create', type: 'write' },
              { id: 'basic.equipment_standard.edit', name: '编辑配备标准', code: 'basic.equipment_standard.edit', type: 'write' },
              { id: 'basic.equipment_standard.delete', name: '删除配备标准', code: 'basic.equipment_standard.delete', type: 'delete' },
              { id: 'basic.equipment_standard.check_compliance', name: '合规性检查', code: 'basic.equipment_standard.check_compliance', type: 'advanced', level: 'high' }
            ]
          },
          {
            id: 'basic.textbook_version',
            name: '📚 教材版本管理',
            code: 'basic.textbook_version',
            type: 'read',
            children: [
              { id: 'basic.textbook_version.view', name: '查看教材版本', code: 'basic.textbook_version.view', type: 'read' },
              { id: 'basic.textbook_version.create', name: '创建教材版本', code: 'basic.textbook_version.create', type: 'write' },
              { id: 'basic.textbook_version.edit', name: '编辑教材版本', code: 'basic.textbook_version.edit', type: 'write' },
              { id: 'basic.textbook_version.delete', name: '删除教材版本', code: 'basic.textbook_version.delete', type: 'delete' },
              { id: 'basic.textbook_version.batch_status', name: '批量状态更新', code: 'basic.textbook_version.batch_status', type: 'write' },
              { id: 'basic.textbook_version.sort', name: '排序管理', code: 'basic.textbook_version.sort', type: 'write' }
            ]
          },
          {
            id: 'basic.textbook_chapter',
            name: '📖 章节结构管理',
            code: 'basic.textbook_chapter',
            type: 'read',
            children: [
              { id: 'basic.textbook_chapter.view', name: '查看章节结构', code: 'basic.textbook_chapter.view', type: 'read' },
              { id: 'basic.textbook_chapter.tree', name: '章节树形结构', code: 'basic.textbook_chapter.tree', type: 'read' },
              { id: 'basic.textbook_chapter.create', name: '创建章节', code: 'basic.textbook_chapter.create', type: 'write' },
              { id: 'basic.textbook_chapter.edit', name: '编辑章节', code: 'basic.textbook_chapter.edit', type: 'write' },
              { id: 'basic.textbook_chapter.delete', name: '删除章节', code: 'basic.textbook_chapter.delete', type: 'delete' }
            ]
          }
        ]
      },
      {
        id: 'system',
        name: '系统管理',
        code: 'system',
        children: [
          { id: 'system.read', name: '系统信息', code: 'system.read', type: 'advanced', level: 'high' },
          { id: 'log.read', name: '日志查看', code: 'log.read', type: 'advanced', level: 'high' }
        ]
      }
    ]
  } finally {
    loading.value = false
  }
}

const getPermissionType = (type: string) => {
  const typeMap: Record<string, string> = {
    'read': 'info',
    'write': 'success',
    'delete': 'danger',
    'advanced': 'warning'
  }
  return typeMap[type] || ''
}

const getPermissionTypeLabel = (type: string) => {
  const labelMap: Record<string, string> = {
    'read': '查看',
    'write': '操作',
    'delete': '删除',
    'advanced': '高级'
  }
  return labelMap[type] || type
}

// 获取角色级别对应的标签类型
const getLevelType = (level: number) => {
  if (level <= 2) return 'danger'
  if (level <= 4) return 'warning'
  return 'success'
}

onMounted(() => {
  fetchRoleList()
  fetchPermissionTree()
})
</script>

<style scoped>
.permission-management-page {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.page-header p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.permission-tree-section {
  background: #fff;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.role-list {
  max-height: 600px;
  overflow-y: auto;
}

.role-item {
  padding: 15px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.3s;
}

.role-item:hover {
  border-color: #409eff;
  background-color: #f0f9ff;
}

.role-item.active {
  border-color: #409eff;
  background-color: #ecf5ff;
}

.role-info h4 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 16px;
}

.role-info p {
  margin: 0 0 10px 0;
  color: #606266;
  font-size: 14px;
}

.permission-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding-right: 20px;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.node-label {
  font-weight: 500;
}

.node-code {
  color: #909399;
  font-size: 12px;
  background: #f5f7fa;
  padding: 2px 6px;
  border-radius: 4px;
}

.node-actions {
  display: flex;
  gap: 5px;
}

.no-role-selected {
  padding: 40px;
  text-align: center;
}
</style>
