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
        <el-select v-model="form.role" placeholder="请选择角色">
          <el-option
            v-for="role in roleOptions"
            :key="role.id"
            :label="role.name"
            :value="role.code"
          />
        </el-select>
      </el-form-item>
      
      <el-form-item label="状态" prop="status">
        <el-radio-group v-model="form.status">
          <el-radio :label="1">正常</el-radio>
          <el-radio :label="0">禁用</el-radio>
        </el-radio-group>
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
import { getRoleListApi, type Role } from '@/api/role'

interface Props {
  modelValue: boolean
  user?: UserProfile | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const roleOptions = ref<Role[]>([])

// 表单数据
const form = reactive({
  username: '',
  real_name: '',
  email: '',
  phone: '',
  role: '',
  status: 1,
  password: '',
  password_confirmation: ''
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.user)

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
  form.status = 1
  form.password = ''
  form.password_confirmation = ''

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
    form.status = 1 // 假设从API获取的状态
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
    
    // TODO: 调用创建或更新用户API
    if (isEdit.value) {
      // 更新用户
      console.log('更新用户:', form)
      ElMessage.success('用户更新成功')
    } else {
      // 创建用户
      console.log('创建用户:', form)
      ElMessage.success('用户创建成功')
    }
    
    emit('success')
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
    roleOptions.value = []
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
