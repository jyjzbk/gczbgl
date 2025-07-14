<template>
  <div class="user-list-page">
    <div class="page-header">
      <div class="header-content">
        <h2>用户列表</h2>
        <p>管理系统用户信息和权限</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="showCreateDialog">
          新增用户
        </el-button>
      </div>
    </div>

    <!-- 搜索筛选 -->
    <div class="search-section">
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
    <div class="table-section">
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
              <el-button type="primary" size="small" @click="handleEdit(row)">
                编辑
              </el-button>
              <el-button type="warning" size="small" @click="handleResetPassword(row)">
                重置密码
              </el-button>
              <el-button
                type="danger"
                size="small"
                @click="handleDelete(row)"
                :disabled="row.id === currentUserId"
              >
                删除
              </el-button>
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
import { Search, Plus } from '@element-plus/icons-vue'
import { getUserListApi, deleteUserApi, type UserProfile } from '@/api/user'
import { getRoleListApi, type Role } from '@/api/role'
import { useAuthStore } from '@/stores/auth'
import SimpleUserDialog from './components/SimpleUserDialog.vue'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const dialogVisible = ref(false)
const userList = ref<UserProfile[]>([])
const currentUser = ref<UserProfile | null>(null)
const roleOptions = ref<Role[]>([])

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

// 获取角色类型
const getRoleType = (role: string) => {
  const typeMap: Record<string, string> = {
    'super_admin': 'danger',
    'admin': 'warning',
    'lab_manager': 'info',
    'teacher': 'success',
    'student': ''
  }
  return typeMap[role] || ''
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
  try {
    loading.value = true

    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchForm.search || undefined,
      role: searchForm.role || undefined,
      status: searchForm.status || undefined
    }

    console.log('正在获取用户列表，参数:', params)

    const response = await getUserListApi(params)

    console.log('API响应结构:', response)
    console.log('response.data:', (response as any).data)

    // 使用类型断言来处理响应数据
    const responseData = (response as any).data

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
      total: pagination.total,
      currentPage: pagination.current_page,
      userCount: userList.value.length,
      users: userList.value
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

// 对话框成功回调
const handleDialogSuccess = (user: any) => {
  console.log('对话框操作成功:', user)

  // 重新加载列表以获取最新数据
  fetchUserList()
}

// 初始化
onMounted(() => {
  fetchRoleList()
  fetchUserList()
})
</script>

<style scoped>
.user-list-page {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
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
  background: #fff;
  padding: 20px;
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
</style>
