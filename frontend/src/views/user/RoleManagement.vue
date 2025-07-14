<template>
  <div class="role-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>角色管理</h2>
        <p>管理系统角色和权限配置</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="handleAddRole">
          新增角色
        </el-button>
      </div>
    </div>

    <!-- 角色列表 -->
    <div v-loading="loading" class="role-grid">
      <div v-for="role in roles" :key="role.id" class="role-card">
        <div class="role-header">
          <div class="role-info">
            <h3>{{ role.name }}</h3>
            <p>{{ role.description }}</p>
          </div>
          <div class="role-actions">
            <el-dropdown trigger="click">
              <el-button type="primary" size="small" :icon="MoreFilled" circle />
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item @click="handleEditRole(role)">
                    <el-icon><Edit /></el-icon>
                    编辑
                  </el-dropdown-item>
                  <el-dropdown-item @click="handlePermissionConfig(role)">
                    <el-icon><Setting /></el-icon>
                    权限配置
                  </el-dropdown-item>
                  <el-dropdown-item divided @click="handleDeleteRole(role)">
                    <el-icon><Delete /></el-icon>
                    删除
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </div>
        </div>
        
        <div class="role-stats">
          <div class="stat-item">
            <span class="stat-label">用户数量</span>
            <span class="stat-value">{{ role.user_count || 0 }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">权限数量</span>
            <span class="stat-value">{{ role.permission_count || 0 }}</span>
          </div>
        </div>
        
        <div class="role-footer">
          <div class="role-level">
            <el-tag :type="getLevelType(role.level)">
              级别 {{ role.level }}
            </el-tag>
          </div>
          <div class="role-quick-actions">
            <el-button size="small" type="primary" plain @click="handleEditRole(role)">
              编辑
            </el-button>
            <el-button size="small" type="success" plain @click="handlePermissionConfig(role)">
              权限
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- 角色编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑角色' : '新增角色'"
      width="600px"
      @close="resetForm"
    >
      <el-form
        ref="formRef"
        :model="roleForm"
        :rules="formRules"
        label-width="80px"
      >
        <el-form-item label="角色名称" prop="name">
          <el-input v-model="roleForm.name" placeholder="请输入角色名称" />
        </el-form-item>

        <el-form-item label="角色代码" prop="code">
          <el-input
            v-model="roleForm.code"
            placeholder="请输入角色代码（英文）"
            :readonly="isEdit"
            :class="{ 'readonly-input': isEdit }"
          />
          <div v-if="isEdit" class="form-tip">
            角色代码在编辑时不可修改
          </div>
        </el-form-item>

        <el-form-item label="角色级别" prop="level">
          <el-select v-model="roleForm.level" placeholder="请选择角色级别">
            <el-option label="省级 (1)" :value="1" />
            <el-option label="市级 (2)" :value="2" />
            <el-option label="区县级 (3)" :value="3" />
            <el-option label="学区级 (4)" :value="4" />
            <el-option label="学校级 (5)" :value="5" />
          </el-select>
        </el-form-item>

        <el-form-item label="角色描述" prop="description">
          <el-input
            v-model="roleForm.description"
            type="textarea"
            :rows="3"
            placeholder="请输入角色描述"
          />
        </el-form-item>

        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="roleForm.status">
            <el-radio :label="1">启用</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>

      <template #footer>
        <div class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSubmit" :loading="submitting">
            {{ isEdit ? '更新' : '创建' }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Plus, MoreFilled, Edit, Setting, Delete } from '@element-plus/icons-vue'
import { getRoleListApi, deleteRoleApi, createRoleApi, updateRoleApi, type Role } from '@/api/role'
import { useRouter } from 'vue-router'

const router = useRouter()

// 响应式数据
const loading = ref(false)
const roles = ref<Role[]>([])
const dialogVisible = ref(false)
const isEdit = ref(false)
const submitting = ref(false)
const formRef = ref<FormInstance>()

// 表单数据
const roleForm = reactive({
  id: 0,
  name: '',
  code: '',
  level: 5,
  description: '',
  status: 1
})

// 表单验证规则
const formRules: FormRules = {
  name: [
    { required: true, message: '请输入角色名称', trigger: 'blur' },
    { min: 2, max: 50, message: '角色名称长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入角色代码', trigger: 'blur' },
    { pattern: /^[a-zA-Z_][a-zA-Z0-9_]*$/, message: '角色代码只能包含字母、数字和下划线，且以字母或下划线开头', trigger: 'blur' }
  ],
  level: [
    { required: true, message: '请选择角色级别', trigger: 'change' }
  ],
  description: [
    { max: 500, message: '描述不能超过 500 个字符', trigger: 'blur' }
  ]
}

// 获取角色列表
const fetchRoleList = async () => {
  try {
    loading.value = true
    console.log('正在获取角色列表...')

    const response = await getRoleListApi()
    console.log('角色列表API响应:', response)

    // 处理响应数据
    if (response.data && response.data.data) {
      roles.value = response.data.data
    } else {
      roles.value = []
    }

    console.log('角色列表加载成功:', roles.value)
  } catch (error) {
    console.error('获取角色列表失败:', error)
    ElMessage.error('获取角色列表失败')
    roles.value = []
  } finally {
    loading.value = false
  }
}

// 删除角色
const handleDeleteRole = async (role: Role) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除角色 "${role.name}" 吗？此操作不可恢复！`,
      '删除角色',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    console.log('删除角色:', role)

    await deleteRoleApi(role.id)
    ElMessage.success('角色删除成功')

    // 重新加载列表
    fetchRoleList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除角色失败:', error)
      ElMessage.error('删除角色失败')
    }
  }
}

// 新增角色
const handleAddRole = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 编辑角色
const handleEditRole = (role: Role) => {
  isEdit.value = true
  roleForm.id = role.id
  roleForm.name = role.name
  roleForm.code = role.code
  roleForm.level = role.level
  roleForm.description = role.description || ''
  roleForm.status = role.status
  dialogVisible.value = true
}

// 权限配置
const handlePermissionConfig = (role: Role) => {
  router.push('/permissions')
}

// 重置表单
const resetForm = () => {
  roleForm.id = 0
  roleForm.name = ''
  roleForm.code = ''
  roleForm.level = 5
  roleForm.description = ''
  roleForm.status = 1
  formRef.value?.clearValidate()
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    if (isEdit.value) {
      // 更新角色
      await updateRoleApi(roleForm.id, {
        name: roleForm.name,
        code: roleForm.code,
        description: roleForm.description,
        level: roleForm.level,
        status: roleForm.status
      })
      ElMessage.success('角色更新成功')
    } else {
      // 创建角色
      await createRoleApi({
        name: roleForm.name,
        code: roleForm.code,
        description: roleForm.description,
        level: roleForm.level
      })
      ElMessage.success('角色创建成功')
    }

    dialogVisible.value = false
    fetchRoleList()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '角色更新失败' : '角色创建失败')
  } finally {
    submitting.value = false
  }
}

const getLevelType = (level: number) => {
  if (level <= 2) return 'danger'
  if (level <= 3) return 'warning'
  if (level <= 4) return 'info'
  return 'success'
}

onMounted(() => {
  fetchRoleList()
})
</script>

<style scoped>
.role-management-page {
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

.role-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.role-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.role-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.role-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.role-info h3 {
  margin: 0 0 5px 0;
  color: #303133;
  font-size: 18px;
  font-weight: 600;
}

.role-info p {
  margin: 0;
  color: #606266;
  font-size: 14px;
  line-height: 1.5;
}

.role-actions {
  opacity: 0.7;
  transition: opacity 0.3s ease;
}

.role-card:hover .role-actions {
  opacity: 1;
}

.role-actions .el-dropdown {
  cursor: pointer;
}

.role-stats {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  padding: 15px 0;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #f0f0f0;
}

.stat-item {
  text-align: center;
}

.stat-label {
  display: block;
  color: #909399;
  font-size: 12px;
  margin-bottom: 5px;
}

.stat-value {
  display: block;
  color: #303133;
  font-size: 20px;
  font-weight: 600;
}

.role-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

.role-level {
  flex: 1;
}

.role-quick-actions {
  display: flex;
  gap: 8px;
}

.role-quick-actions .el-button {
  padding: 4px 8px;
  font-size: 12px;
}

.readonly-input :deep(.el-input__inner) {
  background-color: #f5f7fa;
  color: #909399;
  cursor: not-allowed;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
</style>
