<template>
  <div class="user-list-page">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>用户管理</h2>
        <p>按组织架构管理系统用户信息和权限</p>
      </div>
      <div class="header-actions">
        <PermissionTooltip permission="user.create">
          <el-button type="primary" :icon="Plus" @click="showCreateDialog">
            新增用户
          </el-button>
        </PermissionTooltip>
        <PermissionTooltip permission="user.export">
          <el-button type="success" :icon="Download" @click="handleExport">
            导出用户
          </el-button>
        </PermissionTooltip>
        <el-button :icon="Refresh" @click="refreshData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 主要内容区域 -->
    <div class="main-content">
      <!-- 左侧组织树 -->
      <div class="left-panel">
        <OrganizationTree
          ref="organizationTreeRef"
          :show-stats="true"
          :default-expand-level="2"
          :selected-node-id="selectedOrganizationId"
          @node-click="handleOrganizationSelect"
        />
      </div>

      <!-- 右侧用户列表 -->
      <div class="right-panel">
        <!-- 当前组织信息 -->
        <div class="current-organization" v-if="selectedOrganization">
          <div class="org-info">
            <div class="org-header">
              <el-icon :color="getOrganizationColor(selectedOrganization.level)">
                <component :is="getOrganizationIcon(selectedOrganization.level)" />
              </el-icon>
              <div class="org-details">
                <h3>{{ selectedOrganization.name }}</h3>
                <el-tag :type="getOrganizationTagType(selectedOrganization.level)" size="small">
                  {{ getOrganizationLevelName(selectedOrganization.level) }}
                </el-tag>
              </div>
            </div>
            <div class="org-stats" v-if="organizationStats">
              <div class="stat-item">
                <span class="stat-label">总用户数</span>
                <span class="stat-value">{{ organizationStats.total_users }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">正常用户</span>
                <span class="stat-value success">{{ organizationStats.active_users }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">禁用用户</span>
                <span class="stat-value danger">{{ organizationStats.disabled_users }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 搜索筛选 -->
        <div class="search-section" v-if="selectedOrganization">
          <el-form :model="searchForm" inline>
            <el-form-item label="搜索">
              <el-input
                v-model="searchForm.search"
                placeholder="用户名、姓名、邮箱"
                :prefix-icon="Search"
                clearable
                @keyup.enter="handleSearch"
              />
            </el-form-item>
            <el-form-item label="角色">
              <el-select v-model="searchForm.role" placeholder="选择角色" clearable>
                <el-option
                  v-for="role in roleOptions"
                  :key="role.id"
                  :label="role.name"
                  :value="role.code"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="状态">
              <el-select v-model="searchForm.status" placeholder="选择状态" clearable>
                <el-option label="正常" value="1" />
                <el-option label="禁用" value="0" />
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="handleSearch">搜索</el-button>
              <el-button @click="handleReset">重置</el-button>
            </el-form-item>
          </el-form>
        </div>

        <!-- 用户表格 -->
        <div class="table-section" v-if="selectedOrganization">
          <el-table
            v-loading="loading"
            :data="userList"
            stripe
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID" width="80" />
            <el-table-column label="头像" width="80">
              <template #default="{ row }">
                <el-avatar :size="40" :src="row.avatar">
                  {{ row.real_name?.charAt(0) || 'U' }}
                </el-avatar>
              </template>
            </el-table-column>
            <el-table-column label="所属组织" width="150">
              <template #default="{ row }">
                <div class="organization-info">
                  <el-tag
                    v-if="row.organization_name"
                    :type="getOrganizationTagType(row.organization_level)"
                    size="small"
                  >
                    {{ row.organization_name }}
                  </el-tag>
                  <span v-else class="no-organization">未分配</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="username" label="用户名" />
            <el-table-column prop="real_name" label="真实姓名" />
            <el-table-column prop="email" label="邮箱" />
            <el-table-column prop="phone" label="手机号" />
            <el-table-column label="角色" width="120">
              <template #default="{ row }">
                <el-tag :type="getRoleType(row.role)">
                  {{ getRoleLabel(row.role) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column label="状态" width="100">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'danger'">
                  {{ row.status === 1 ? '正常' : '禁用' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间" width="180" />
            <el-table-column label="操作" width="240" fixed="right">
              <template #default="{ row }">
                <div class="action-buttons">
                  <PermissionTooltip permission="user.edit" size="small">
                    <el-button type="primary" size="small" @click="handleEdit(row)">
                      编辑
                    </el-button>
                  </PermissionTooltip>
                  <PermissionTooltip permission="user.reset_password" size="small">
                    <el-button type="warning" size="small" @click="handleResetPassword(row)">
                      重置密码
                    </el-button>
                  </PermissionTooltip>
                  <PermissionTooltip permission="user.delete" size="small">
                    <el-button
                      type="danger"
                      size="small"
                      @click="handleDelete(row)"
                      :disabled="row.id === currentUserId"
                    >
                      删除
                    </el-button>
                  </PermissionTooltip>
                </div>
              </template>
            </el-table-column>
          </el-table>

          <!-- 分页 -->
          <div class="pagination-section">
            <el-pagination
              v-model:current-page="pagination.current_page"
              v-model:page-size="pagination.per_page"
              :total="pagination.total"
              :page-sizes="[10, 20, 50, 100]"
              layout="total, sizes, prev, pager, next, jumper"
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"
            />
          </div>
        </div>

        <!-- 空状态 -->
        <div class="empty-state" v-if="!selectedOrganization">
          <el-empty description="请选择左侧组织架构查看用户列表" />
        </div>
      </div>
    </div>

    <!-- 用户对话框 -->
    <SimpleUserDialog
      v-model="dialogVisible"
      :user="currentUser"
      @success="handleDialogSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Search,
  Plus,
  Download,
  Refresh,
  OfficeBuilding,
  Operation,
  MapLocation,
  Location,
  House
} from '@element-plus/icons-vue'
import { getOrganizationUsersApi, deleteUserApi, type UserProfile } from '@/api/user'
import { getRoleListApi, type Role } from '@/api/role'
import {
  getOrganizationStatsApi,
  type OrganizationNode,
  type OrganizationStats
} from '@/api/organization'
import { useAuthStore } from '@/stores/auth'
import SimpleUserDialog from './components/SimpleUserDialog.vue'
import OrganizationTree from '@/components/OrganizationTree.vue'
import PermissionTooltip from '@/components/PermissionTooltip.vue'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const dialogVisible = ref(false)
const userList = ref<UserProfile[]>([])
const currentUser = ref<UserProfile | null>(null)
const roleOptions = ref<Role[]>([])
const organizationTreeRef = ref()

// 组织相关数据
const selectedOrganization = ref<OrganizationNode | null>(null)
const selectedOrganizationId = ref<number | undefined>(undefined)
const organizationStats = ref<OrganizationStats | null>(null)

// 搜索表单
const searchForm = reactive({
  search: '',
  role: '',
  status: ''
})

// 分页信息
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0,
  last_page: 1
})

// 当前用户ID
const currentUserId = computed(() => authStore.userInfo?.id)

// 组织相关辅助函数
const getOrganizationIcon = (level: number) => {
  const icons = {
    1: Operation,     // 省级
    2: MapLocation,   // 市级
    3: Location,      // 区县级
    4: OfficeBuilding, // 学区级
    5: House          // 学校级
  }
  return icons[level as keyof typeof icons] || OfficeBuilding
}

const getOrganizationColor = (level: number) => {
  const colors = {
    1: '#409EFF', // 省级 - 蓝色
    2: '#67C23A', // 市级 - 绿色
    3: '#E6A23C', // 区县级 - 橙色
    4: '#F56C6C', // 学区级 - 红色
    5: '#909399'  // 学校级 - 灰色
  }
  return colors[level as keyof typeof colors] || '#909399'
}

const getOrganizationTagType = (level: number) => {
  const types = {
    1: 'primary',
    2: 'success',
    3: 'warning',
    4: 'danger',
    5: 'info'
  }
  return types[level as keyof typeof types] || 'info'
}

const getOrganizationLevelName = (level: number) => {
  const names = {
    1: '省级',
    2: '市级',
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return names[level as keyof typeof names] || '未知'
}

// 获取角色类型
const getRoleType = (role: string) => {
  const typeMap: Record<string, string> = {
    'super_admin': 'danger',
    'province_admin': 'danger',
    'city_admin': 'warning',
    'county_admin': 'warning',
    'district_admin': 'info',
    'school_admin': 'info',
    'admin': 'warning',
    'lab_manager': 'info',
    'teacher': 'success',
    'student': 'primary'
  }
  return typeMap[role] || 'info'
}

// 获取角色标签
const getRoleLabel = (role: string) => {
  // 优先从角色选项中查找
  const roleOption = roleOptions.value.find(r => r.code === role)
  if (roleOption) {
    return roleOption.name
  }

  // 后备映射
  const labelMap: Record<string, string> = {
    'super_admin': '超级管理员',
    'admin': '管理员',
    'lab_manager': '实验员',
    'teacher': '教师',
    'student': '学生'
  }
  return labelMap[role] || role
}

// 获取角色列表
const fetchRoleList = async () => {
  try {
    // 使用 all=true 参数获取所有角色，不分页
    const response = await getRoleListApi({ all: 'true' })
    console.log('角色列表API响应:', response)

    // 处理响应格式
    if (response.data) {
      // 检查 response.data 是否直接是数组（all=true的情况）
      if (Array.isArray(response.data)) {
        roleOptions.value = [...response.data]
      }
      // 检查 response.data.data 是否是数组（分页格式）
      else if (response.data.data && Array.isArray(response.data.data)) {
        roleOptions.value = [...response.data.data]
      }
      // 检查是否是分页格式的嵌套结构
      else if (response.data.data && response.data.data.data && Array.isArray(response.data.data.data)) {
        roleOptions.value = [...response.data.data.data]
      }
      else {
        roleOptions.value = []
      }
    } else {
      roleOptions.value = []
    }
  } catch (error) {
    console.error('获取角色列表失败:', error)
    // 使用默认角色选项
    roleOptions.value = [
      { id: 1, name: '超级管理员', code: 'super_admin', description: '', level: 1, status: 1, created_at: '', updated_at: '' },
      { id: 2, name: '管理员', code: 'admin', description: '', level: 2, status: 1, created_at: '', updated_at: '' },
      { id: 3, name: '实验员', code: 'lab_manager', description: '', level: 3, status: 1, created_at: '', updated_at: '' },
      { id: 4, name: '教师', code: 'teacher', description: '', level: 4, status: 1, created_at: '', updated_at: '' },
      { id: 5, name: '学生', code: 'student', description: '', level: 5, status: 1, created_at: '', updated_at: '' }
    ]
  }
}

// 获取用户列表
const fetchUserList = async () => {
  if (!selectedOrganization.value) {
    userList.value = []
    pagination.total = 0
    return
  }

  try {
    loading.value = true

    const params = {
      organization_id: selectedOrganization.value.id,
      organization_level: selectedOrganization.value.level,
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchForm.search || undefined,
      role: searchForm.role || undefined,
      status: searchForm.status || undefined
    }

    console.log('正在获取用户列表，参数:', params)

    const response = await getOrganizationUsersApi(params)

    console.log('API响应结构:', response)

    // 处理后端返回的数据结构
    const responseData = response.success ? response.data : response.data

    // 检查响应数据结构
    if (!responseData || !responseData.items) {
      console.error('API响应数据结构错误:', response)
      throw new Error('API响应数据结构错误')
    }

    userList.value = responseData.items
    pagination.current_page = responseData.pagination.current_page
    pagination.per_page = responseData.pagination.per_page
    pagination.total = responseData.pagination.total
    pagination.last_page = responseData.pagination.last_page

    console.log('用户列表加载成功:', {
      organization: selectedOrganization.value.name,
      total: pagination.total,
      currentPage: pagination.current_page,
      userCount: userList.value.length
    })

    // 真实API调用代码（暂时注释）
    /*
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchForm.search || undefined,
      role: searchForm.role || undefined,
      status: searchForm.status || undefined
    }

    const response = await getUserListApi(params)

    userList.value = response.data.items
    pagination.current_page = response.data.pagination.current_page
    pagination.per_page = response.data.pagination.per_page
    pagination.total = response.data.pagination.total
    pagination.last_page = response.data.pagination.last_page
    */

  } catch (error) {
    console.error('获取用户列表失败:', error)
    ElMessage.error('获取用户列表失败')
  } finally {
    loading.value = false
  }
}

// 组织选择处理
const handleOrganizationSelect = async (organization: OrganizationNode) => {
  console.log('选择组织:', organization)
  selectedOrganization.value = organization
  selectedOrganizationId.value = organization.id

  // 重置分页
  pagination.current_page = 1

  // 重置搜索条件
  searchForm.search = ''
  searchForm.role = ''
  searchForm.status = ''

  // 获取组织统计信息
  await fetchOrganizationStats(organization.id, organization.type)

  // 获取用户列表
  await fetchUserList()
}

// 获取组织统计信息
const fetchOrganizationStats = async (organizationId: number, organizationType?: string) => {
  try {
    const response = await getOrganizationStatsApi(organizationId, organizationType)
    if (response.success) {
      organizationStats.value = response.data
    }
  } catch (error) {
    console.error('获取组织统计信息失败:', error)
    organizationStats.value = null
  }
}

// 刷新数据
const refreshData = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.refreshTree()
  }
  if (selectedOrganization.value) {
    fetchOrganizationStats(selectedOrganization.value.id, selectedOrganization.value.type)
    fetchUserList()
  }
}

// 搜索
const handleSearch = () => {
  console.log('执行搜索:', searchForm)
  pagination.current_page = 1
  fetchUserList()
}

// 重置搜索
const handleReset = () => {
  console.log('重置搜索')
  searchForm.search = ''
  searchForm.role = ''
  searchForm.status = ''
  pagination.current_page = 1
  fetchUserList()
}

// 分页大小改变
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchUserList()
}

// 当前页改变
const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchUserList()
}

// 显示创建对话框
const showCreateDialog = () => {
  console.log('显示新增用户对话框')
  currentUser.value = null
  dialogVisible.value = true
}

// 编辑用户
const handleEdit = (user: UserProfile) => {
  console.log('编辑用户:', user)
  currentUser.value = user
  dialogVisible.value = true
}

// 重置密码
const handleResetPassword = async (user: UserProfile) => {
  try {
    await ElMessageBox.confirm(
      `确定要重置用户 "${user.real_name}" 的密码吗？`,
      '重置密码',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    console.log('重置密码:', user)
    // 模拟重置密码成功
    ElMessage.success('密码重置成功，新密码为: 123456')

  } catch (error) {
    console.log('用户取消重置密码')
  }
}

// 删除用户
const handleDelete = async (user: UserProfile) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除用户 "${user.real_name}" 吗？此操作不可恢复！`,
      '删除用户',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    console.log('删除用户:', user)

    try {
      // 调用真实的删除API
      await deleteUserApi(user.id)

      ElMessage.success('用户删除成功')
      // 重新加载列表以获取最新数据
      fetchUserList()
    } catch (error) {
      console.error('删除用户失败:', error)
      ElMessage.error('删除用户失败，请稍后重试')
    }

  } catch (error) {
    console.log('用户取消删除')
  }
}

// 导出用户
const handleExport = async () => {
  if (!selectedOrganization.value) {
    ElMessage.warning('请先选择要导出的组织')
    return
  }

  try {
    ElMessage.info('正在导出用户数据...')

    // 构建导出参数（不包含搜索条件，导出所有用户）
    const exportParams = {
      organization_id: selectedOrganization.value.id,
      organization_level: selectedOrganization.value.level,
      page: 1,
      per_page: 1000 // 获取大量数据
    }

    console.log('导出参数:', exportParams)

    // 获取所有用户数据
    const allUsers = await getOrganizationUsersApi(exportParams)

    console.log('API响应:', allUsers)
    console.log('用户数据:', allUsers.data)

    // 检查数据结构
    if (!allUsers.data || !allUsers.data.items) {
      console.error('数据结构错误:', allUsers)
      ElMessage.error('获取用户数据失败，数据格式错误')
      return
    }

    if (allUsers.data.items.length === 0) {
      ElMessage.warning('该组织下没有用户数据可导出')
      return
    }

    console.log('用户数量:', allUsers.data.items.length)

    // 准备导出数据
    const exportData = allUsers.data.items.map((user: UserProfile) => ({
      '用户ID': user.id,
      '用户名': user.username,
      '真实姓名': user.real_name,
      '邮箱': user.email,
      '手机号': user.phone || '',
      '角色': getRoleLabel(user.role),
      '所属学校': user.school_name || '',
      '状态': user.status === 1 ? '正常' : '禁用',
      '创建时间': new Date(user.created_at).toLocaleString()
    }))

    console.log('导出数据:', exportData)

    // 转换为CSV格式
    const csvContent = convertToCSV(exportData)

    console.log('CSV内容长度:', csvContent.length)

    if (!csvContent) {
      ElMessage.error('生成CSV内容失败')
      return
    }

    // 下载文件
    const organizationName = selectedOrganization.value.name.replace(/[^\w\s]/gi, '')
    downloadCSV(csvContent, `${organizationName}_用户列表_${new Date().toISOString().split('T')[0]}.csv`)

    ElMessage.success(`成功导出 ${allUsers.data.items.length} 条用户数据`)
  } catch (error) {
    console.error('导出失败:', error)
    ElMessage.error('导出失败，请稍后重试')
  }
}

// 转换为CSV格式
const convertToCSV = (data: any[]) => {
  if (data.length === 0) return ''

  const headers = Object.keys(data[0])
  const csvRows = []

  // 添加表头
  csvRows.push(headers.join(','))

  // 添加数据行
  for (const row of data) {
    const values = headers.map(header => {
      const value = row[header]
      return `"${value}"`
    })
    csvRows.push(values.join(','))
  }

  return csvRows.join('\n')
}

// 下载CSV文件
const downloadCSV = (content: string, filename: string) => {
  const blob = new Blob(['\uFEFF' + content], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)

  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.visibility = 'hidden'

  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// 对话框成功回调
const handleDialogSuccess = (user: any) => {
  console.log('对话框操作成功:', user)

  // 重新加载列表以获取最新数据
  fetchUserList()
}

// 初始化
onMounted(async () => {
  await fetchRoleList()

  // 自动选择当前用户所属的组织
  const authStore = useAuthStore()
  const currentUser = authStore.userInfo

  if (currentUser && currentUser.school_id) {
    // 学校管理员：自动选择所属学校
    console.log('当前用户学校ID:', currentUser.school_id)

    // 等待组织树加载完成后自动选择
    setTimeout(() => {
      if (organizationTreeRef.value) {
        // 触发组织树的节点选择
        const schoolNode = {
          id: currentUser.school_id as number,
          name: currentUser.school_name || '当前学校',
          code: `SCHOOL_${currentUser.school_id}`,
          parent_id: null,
          level: 5,
          type: 'school',
          sort_order: 0,
          status: 1
        }
        handleOrganizationSelect(schoolNode)
      }
    }, 1000)
  }
})
</script>

<style scoped>
.user-list-page {
  height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: #f5f5f5;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.main-content {
  flex: 1;
  display: flex;
  gap: 20px;
  min-height: 0;
}

.left-panel {
  width: 320px;
  flex-shrink: 0;
}

.right-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* 当前组织信息样式 */
.current-organization {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.org-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.org-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.org-details h3 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 18px;
  font-weight: 600;
}

.org-stats {
  display: flex;
  gap: 24px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-label {
  font-size: 12px;
  color: #909399;
}

.stat-value {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

.stat-value.success {
  color: #67c23a;
}

.stat-value.danger {
  color: #f56c6c;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.search-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.table-section {
  flex: 1;
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  min-height: 0;
}

.table-section .el-table {
  flex: 1;
}

.empty-state {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pagination-section {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
  white-space: nowrap;
}

.action-buttons .el-button {
  margin-left: 0;
}

.organization-info {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.no-organization {
  color: #909399;
  font-size: 12px;
  font-style: italic;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .main-content {
    flex-direction: column;
  }

  .left-panel {
    width: 100%;
    height: 300px;
  }

  .right-panel {
    flex: 1;
  }
}

@media (max-width: 768px) {
  .user-list-page {
    padding: 10px;
  }

  .page-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .header-actions {
    justify-content: center;
  }

  .org-stats {
    justify-content: space-around;
  }

  .search-section .el-form {
    flex-direction: column;
  }

  .search-section .el-form-item {
    margin-right: 0;
    margin-bottom: 16px;
  }

  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }

  .action-buttons .el-button {
    width: 100%;
  }
}
</style>
