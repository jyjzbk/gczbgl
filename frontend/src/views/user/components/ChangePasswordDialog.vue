<template>
  <el-dialog
    v-model="visible"
    title="修改密码"
    width="400px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
      size="large"
    >
      <el-form-item label="当前密码" prop="current_password">
        <el-input
          v-model="form.current_password"
          type="password"
          placeholder="请输入当前密码"
          show-password
          clearable
        />
      </el-form-item>
      
      <el-form-item label="新密码" prop="password">
        <el-input
          v-model="form.password"
          type="password"
          placeholder="请输入新密码"
          show-password
          clearable
        />
      </el-form-item>
      
      <el-form-item label="确认密码" prop="password_confirmation">
        <el-input
          v-model="form.password_confirmation"
          type="password"
          placeholder="请确认新密码"
          show-password
          clearable
        />
      </el-form-item>
    </el-form>
    
    <template #footer>
      <el-button @click="handleClose">取消</el-button>
      <el-button
        type="primary"
        :loading="loading"
        @click="handleSubmit"
      >
        确认修改
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { changePasswordApi } from '@/api/auth'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 表单引用
const formRef = ref<FormInstance>()

// 加载状态
const loading = ref(false)

// 对话框显示状态
const visible = ref(false)

// 表单数据
const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// 自定义验证函数
const validatePassword = (rule: any, value: string, callback: any) => {
  if (value === '') {
    callback(new Error('请输入新密码'))
  } else if (value.length < 6) {
    callback(new Error('密码长度不能少于6位'))
  } else if (value === form.current_password) {
    callback(new Error('新密码不能与当前密码相同'))
  } else {
    if (form.password_confirmation !== '') {
      formRef.value?.validateField('password_confirmation')
    }
    callback()
  }
}

const validatePasswordConfirmation = (rule: any, value: string, callback: any) => {
  if (value === '') {
    callback(new Error('请确认新密码'))
  } else if (value !== form.password) {
    callback(new Error('两次输入密码不一致'))
  } else {
    callback()
  }
}

// 表单验证规则
const rules: FormRules = {
  current_password: [
    { required: true, message: '请输入当前密码', trigger: 'blur' }
  ],
  password: [
    { validator: validatePassword, trigger: 'blur' }
  ],
  password_confirmation: [
    { validator: validatePasswordConfirmation, trigger: 'blur' }
  ]
}

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      resetForm()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 重置表单
const resetForm = () => {
  form.current_password = ''
  form.password = ''
  form.password_confirmation = ''
  formRef.value?.clearValidate()
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    
    loading.value = true
    
    await changePasswordApi({
      current_password: form.current_password,
      password: form.password,
      password_confirmation: form.password_confirmation
    })
    
    ElMessage.success('密码修改成功')
    emit('success')
    handleClose()
  } catch (error) {
    console.error('修改密码失败:', error)
  } finally {
    loading.value = false
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
  resetForm()
}
</script>

<style scoped>
:deep(.el-form-item) {
  margin-bottom: 20px;
}
</style>
