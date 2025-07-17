<template>
  <el-dialog
    v-model="visible"
    :title="isEdit ? '编辑用户' : '新增用户'"
    width="600px"
    :close-on-click-modal="false"
    @close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
    >
      <el-form-item label="用户名" prop="username">
        <el-input
          v-model="form.username"
          placeholder="请输入用户名"
          :disabled="isEdit"
        />
      </el-form-item>
      
      <el-form-item label="真实姓名" prop="real_name">
        <el-input
          v-model="form.real_name"
          placeholder="请输入真实姓名"
        />
      </el-form-item>
      
      <el-form-item label="邮箱" prop="email">
        <el-input
          v-model="form.email"
          placeholder="请输入邮箱"
          type="email"
        />
      </el-form-item>
      
      <el-form-item label="手机号" prop="phone">
        <el-input
          v-model="form.phone"
          placeholder="请输入手机号"
        />
      </el-form-item>
      
      <el-form-item label="角色" prop="role">
        <el-select
          v-model="form.role"
          placeholder="请选择角色"
          style="width: 100%"
          @change="handleRoleChange"
        >
          <el-option
            v-for="role in roleOptions"
            :key="role.id"
            :label="role.name"
            :value="role.code"
          />
        </el-select>
      </el-form-item>

      <!-- 组织归属选择 -->
      <el-form-item
        v-if="showOrganizationSelect"
        :label="organizationSelectLabel"
        prop="organization_id"
      >
        <el-tree-select
          v-model="form.organization_id"
          :data="organizationTreeData"
          :props="treeProps"
          :placeholder="organizationSelectPlaceholder"
          style="width: 100%"
          :loading="organizationLoading"
          check-strictly
          :render-after-expand="false"
          node-key="id"
          @change="handleOrganizationChange"
        />
      </el-form-item>

      <!-- 学校选择（仅学校级用户） -->
      <el-form-item
        v-if="showSchoolSelect"
        label="所属学校"
        prop="school_id"
      >
        <el-tree-select
          v-model="form.school_id"
          :data="schoolTreeData"
          :props="schoolTreeProps"
          placeholder="请选择学校"
          style="width: 100%"
          :loading="schoolLoading"
          check-strictly
          :render-after-expand="false"
          node-key="id"
          @change="handleSchoolChange"
        />
      </el-form-item>
      
      <el-form-item label="部门" prop="department">
        <el-input
          v-model="form.department"
          placeholder="请输入部门"
        />
      </el-form-item>
      
      <el-form-item label="职位" prop="position">
        <el-input
          v-model="form.position"
          placeholder="请输入职位"
        />
      </el-form-item>
      
      <el-form-item v-if="!isEdit" label="密码" prop="password">
        <el-input
          v-model="form.password"
          placeholder="请输入密码"
          type="password"
          show-password
        />
      </el-form-item>
      
      <el-form-item v-if="!isEdit" label="确认密码" prop="password_confirmation">
        <el-input
          v-model="form.password_confirmation"
          placeholder="请再次输入密码"
          type="password"
          show-password
        />
      </el-form-item>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="submitting" @click="handleSubmit">
          {{ isEdit ? '更新' : '创建' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import type { UserProfile } from '@/api/user'
import { createUserApi, updateUserApi } from '@/api/user'
import { getRoleListApi, type Role } from '@/api/role'

interface Props {
  modelValue: boolean
  user?: UserProfile | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success', user: any): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const roleOptions = ref<Role[]>([])
const organizationOptions = ref<any[]>([])
const organizationTreeData = ref<any[]>([])
const schoolOptions = ref<any[]>([])
const schoolTreeData = ref<any[]>([])
const organizationLoading = ref(false)
const schoolLoading = ref(false)
const selectedRoleLevel = ref<number>(5)

// 树形选择器配置
const treeProps = {
  children: 'children',
  label: 'name',
  value: 'id'
}

const schoolTreeProps = {
  children: 'children',
  label: 'name',
  value: 'id'
}

// 表单数据
const form = reactive({
  username: '',
  real_name: '',
  email: '',
  phone: '',
  role: '',
  organization_id: '',
  school_id: '',
  department: '',
  position: '',
  password: '',
  password_confirmation: ''
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.user)

// 是否显示组织选择
const showOrganizationSelect = computed(() => {
  return !isEdit.value && form.role && selectedRoleLevel.value < 5
})

// 是否显示学校选择
const showSchoolSelect = computed(() => {
  return !isEdit.value && form.role && selectedRoleLevel.value === 5
})

// 组织选择标签
const organizationSelectLabel = computed(() => {
  switch (selectedRoleLevel.value) {
    case 1: return '所属省份'
    case 2: return '所属市'
    case 3: return '所属区县'
    case 4: return '所属学区'
    default: return '所属组织'
  }
})

// 组织选择占位符
const organizationSelectPlaceholder = computed(() => {
  switch (selectedRoleLevel.value) {
    case 1: return '请选择省份'
    case 2: return '请选择市'
    case 3: return '请选择区县'
    case 4: return '请选择学区'
    default: return '请选择组织'
  }
})

// 表单验证规则
const rules: FormRules = {
  username: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { min: 3, max: 20, message: '用户名长度在 3 到 20 个字符', trigger: 'blur' }
  ],
  real_name: [
    { required: true, message: '请输入真实姓名', trigger: 'blur' }
  ],
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ],
  phone: [
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号', trigger: 'blur' }
  ],
  role: [
    { required: true, message: '请选择角色', trigger: 'change' }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, message: '密码长度不能少于6位', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, message: '请再次输入密码', trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        if (value !== form.password) {
          callback(new Error('两次输入的密码不一致'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 重置表单
const resetForm = () => {
  form.username = ''
  form.real_name = ''
  form.email = ''
  form.phone = ''
  form.role = ''
  form.organization_id = ''
  form.school_id = ''
  form.department = ''
  form.position = ''
  form.password = ''
  form.password_confirmation = ''

  // 重置选项
  organizationOptions.value = []
  schoolOptions.value = []
  selectedRoleLevel.value = 5

  if (formRef.value) {
    formRef.value.clearValidate()
  }
}

// 监听用户数据变化
watch(() => props.user, (user) => {
  if (user) {
    form.username = user.username
    form.real_name = user.real_name
    form.email = user.email
    form.phone = user.phone || ''
    form.role = user.role
    form.department = user.department || ''
    form.position = user.position || ''
  } else {
    resetForm()
  }
}, { immediate: true })

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()

    submitting.value = true

    if (isEdit.value) {
      // 更新用户
      console.log('正在更新用户:', props.user?.id, form)

      const updateData = {
        real_name: form.real_name,
        email: form.email,
        phone: form.phone || undefined,
        role: form.role,
        department: form.department || undefined,
        position: form.position || undefined
      }

      const response = await updateUserApi(props.user!.id, updateData)
      console.log('更新用户响应:', response)

      ElMessage.success('用户更新成功')
      emit('success', response.data)
    } else {
      // 创建用户
      console.log('正在创建用户:', form)

      const createData = {
        username: form.username,
        password: form.password,
        real_name: form.real_name,
        email: form.email,
        phone: form.phone || undefined,
        role: form.role,
        organization_id: form.organization_id || undefined,
        school_id: form.school_id || undefined,
        department: form.department || undefined,
        position: form.position || undefined
      }

      const response = await createUserApi(createData)
      console.log('创建用户响应:', response)

      ElMessage.success(`用户创建成功！用户名: ${createData.username}，密码: ${createData.password}`)
      emit('success', response.data)
    }

    handleClose()
    
  } catch (error) {
    console.error('提交失败:', error)
  } finally {
    submitting.value = false
  }
}

// 获取角色列表
const fetchRoleList = async () => {
  try {
    const response = await getRoleListApi({ all: 'true' })

    if (response.data) {
      if (Array.isArray(response.data)) {
        roleOptions.value = [...response.data]
      } else if (response.data.data && Array.isArray(response.data.data)) {
        roleOptions.value = [...response.data.data]
      } else if (response.data.data && response.data.data.data && Array.isArray(response.data.data.data)) {
        roleOptions.value = [...response.data.data.data]
      } else {
        roleOptions.value = []
      }
    } else {
      roleOptions.value = []
    }
  } catch (error) {
    console.error('获取角色列表失败:', error)
    // 使用默认角色选项作为后备
    roleOptions.value = [
      { id: 1, name: '省级管理员', code: 'province_admin', description: '', level: 1, status: 1, created_at: '', updated_at: '' },
      { id: 2, name: '省级教研员', code: 'province_researcher', description: '', level: 1, status: 1, created_at: '', updated_at: '' },
      { id: 3, name: '市级管理员', code: 'city_admin', description: '', level: 2, status: 1, created_at: '', updated_at: '' },
      { id: 4, name: '市级教研员', code: 'city_researcher', description: '', level: 2, status: 1, created_at: '', updated_at: '' },
      { id: 5, name: '区县管理员', code: 'county_admin', description: '', level: 3, status: 1, created_at: '', updated_at: '' },
      { id: 6, name: '区县教研员', code: 'county_researcher', description: '', level: 3, status: 1, created_at: '', updated_at: '' },
      { id: 7, name: '学区管理员', code: 'district_admin', description: '', level: 4, status: 1, created_at: '', updated_at: '' },
      { id: 8, name: '校长', code: 'school_principal', description: '', level: 5, status: 1, created_at: '', updated_at: '' },
      { id: 9, name: '教务主任', code: 'school_dean', description: '', level: 5, status: 1, created_at: '', updated_at: '' },
      { id: 10, name: '实验员', code: 'school_experimenter', description: '', level: 5, status: 1, created_at: '', updated_at: '' },
      { id: 11, name: '任课教师', code: 'school_teacher', description: '', level: 5, status: 1, created_at: '', updated_at: '' }
    ]
  }
}

// 角色变化处理
const handleRoleChange = (roleCode: string) => {
  const selectedRole = roleOptions.value.find(role => role.code === roleCode)
  if (selectedRole) {
    selectedRoleLevel.value = selectedRole.level

    // 清空之前的选择
    form.organization_id = ''
    form.school_id = ''

    // 根据角色级别加载对应的组织选项
    if (selectedRole.level < 5) {
      fetchOrganizationOptions(selectedRole.level)
    } else if (selectedRole.level === 5) {
      fetchSchoolOptions()
    }
  }
}

// 获取组织选项
const fetchOrganizationOptions = async (level: number) => {
  organizationLoading.value = true
  try {
    // 获取组织树形数据
    const { getOrganizationTreeApi } = await import('@/api/organization')
    const response = await getOrganizationTreeApi()

    if (response.data) {
      // 根据用户角色级别过滤可选择的组织
      organizationTreeData.value = filterOrganizationsByLevel(response.data, level)
    } else {
      organizationTreeData.value = []
    }
  } catch (error) {
    console.error('获取组织选项失败:', error)
    organizationTreeData.value = []
  } finally {
    organizationLoading.value = false
  }
}

// 根据角色级别过滤组织
const filterOrganizationsByLevel = (organizations: any[], targetLevel: number): any[] => {
  return organizations.filter(org => {
    // 只显示目标级别及以上的组织
    if (org.level <= targetLevel) {
      // 递归过滤子组织
      if (org.children && org.children.length > 0) {
        org.children = filterOrganizationsByLevel(org.children, targetLevel)
      }
      return true
    }
    return false
  })
}

// 处理组织选择变化
const handleOrganizationChange = (value: any) => {
  console.log('选择的组织:', value)
  // 可以在这里添加额外的逻辑，比如根据选择的组织自动填充其他字段
}

// 获取学校选项
const fetchSchoolOptions = async () => {
  schoolLoading.value = true
  try {
    // 获取组织树形数据，然后构建学校树形结构
    const { getOrganizationTreeApi } = await import('@/api/organization')
    const response = await getOrganizationTreeApi()

    if (response.data) {
      // 构建学校树形数据（按组织层级分组）
      schoolTreeData.value = buildSchoolTree(response.data)
    } else {
      schoolTreeData.value = []
    }
  } catch (error) {
    console.error('获取学校选项失败:', error)
    schoolTreeData.value = []
  } finally {
    schoolLoading.value = false
  }
}

// 构建学校树形数据
const buildSchoolTree = (organizations: any[]): any[] => {
  const result: any[] = []

  const processOrganization = (org: any) => {
    const orgNode = {
      id: `org_${org.id}`,
      name: org.name,
      level: org.level,
      disabled: true, // 组织节点不可选择
      children: []
    }

    // 添加该组织下的学校
    if (org.schools && org.schools.length > 0) {
      org.schools.forEach((school: any) => {
        orgNode.children.push({
          id: school.id,
          name: school.name,
          level: 5,
          disabled: false
        })
      })
    }

    // 递归处理子组织
    if (org.children && org.children.length > 0) {
      org.children.forEach((child: any) => {
        const childNode = processOrganization(child)
        if (childNode.children.length > 0) {
          orgNode.children.push(childNode)
        }
      })
    }

    return orgNode
  }

  organizations.forEach(org => {
    const orgNode = processOrganization(org)
    if (orgNode.children.length > 0) {
      result.push(orgNode)
    }
  })

  return result
}

// 处理学校选择变化
const handleSchoolChange = (value: any) => {
  console.log('选择的学校:', value)
  // 自动设置组织信息
  if (value) {
    // 可以根据学校ID查找对应的组织信息并自动填充
    form.organization_id = value
  }
}

// 关闭对话框
const handleClose = () => {
  visible.value = false
  resetForm()
}

// 组件挂载时获取角色列表
onMounted(() => {
  fetchRoleList()
})
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
