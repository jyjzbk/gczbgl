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
              :check-strictly="true"
            >
              <template #default="{ node, data }">
                <div class="permission-node">
                  <div class="node-content">
                    <span class="node-label">{{ data.name }}</span>
                    <span class="node-code">{{ data.code }}</span>
                  </div>
                  <div class="node-actions">
                    <el-tag v-if="data.type" :type="getPermissionType(data.type)" size="small">
                      {{ data.type }}
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

      // 只设置叶子节点，避免父子节点级联问题
      const leafPermissions = rolePermissions.value.filter(permission => {
        // 检查是否为叶子节点（没有子权限依赖它）
        return !rolePermissions.value.some(p => p.startsWith(permission + '.'))
      })

      console.log('原始权限:', rolePermissions.value)
      console.log('叶子权限:', leafPermissions)

      permissionTreeRef.value.setCheckedKeys(leafPermissions, false) // false表示不级联选择
      console.log('树形组件选中状态已更新')

      // 验证实际选中的权限
      setTimeout(() => {
        if (permissionTreeRef.value) {
          const actualChecked = permissionTreeRef.value.getCheckedKeys()
          console.log('实际选中的权限:', actualChecked)
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
    // 由于设置了 check-strictly="true"，不需要 halfCheckedKeys
    const allPermissions = [...checkedKeys]

    console.log('保存权限配置:', {
      roleId: selectedRole.value.id,
      roleName: selectedRole.value.name,
      checkedKeys,
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

// 调试：打印权限树结构
const debugPermissionTree = () => {
  console.log('权限树结构:', permissionTree.value)
  if (permissionTreeRef.value) {
    console.log('当前选中的权限:', permissionTreeRef.value.getCheckedKeys())
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
          { id: 'user.update', name: '编辑用户', code: 'user.update', type: 'write' },
          { id: 'user.delete', name: '删除用户', code: 'user.delete', type: 'delete' }
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
          { id: 'experiment.catalog', name: '实验目录', code: 'experiment.catalog', type: 'read' },
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
    'delete': 'danger'
  }
  return typeMap[type] || ''
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
