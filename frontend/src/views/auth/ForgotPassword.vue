<template>
  <div class="forgot-password-form">
    <!-- 表单标题 -->
    <div class="form-header">
      <h2>重置密码</h2>
      <p>请输入您的邮箱地址，我们将发送验证码到您的邮箱</p>
    </div>
    
    <!-- 步骤指示器 -->
    <el-steps :active="currentStep" align-center class="steps">
      <el-step title="验证邮箱" />
      <el-step title="重置密码" />
      <el-step title="完成" />
    </el-steps>
    
    <!-- 第一步：发送验证码 -->
    <div v-if="currentStep === 0" class="step-content">
      <el-form
        ref="emailFormRef"
        :model="emailForm"
        :rules="emailRules"
        size="large"
      >
        <el-form-item prop="email">
          <el-input
            v-model="emailForm.email"
            placeholder="请输入邮箱地址"
            :prefix-icon="Message"
            clearable
          />
        </el-form-item>
        
        <el-form-item>
          <el-button
            type="primary"
            size="large"
            :loading="sendingCode"
            @click="sendVerificationCode"
            class="action-button"
          >
            {{ sendingCode ? '发送中...' : '发送验证码' }}
          </el-button>
        </el-form-item>
      </el-form>
    </div>
    
    <!-- 第二步：验证码和新密码 -->
    <div v-if="currentStep === 1" class="step-content">
      <el-form
        ref="resetFormRef"
        :model="resetForm"
        :rules="resetRules"
        size="large"
      >
        <el-form-item prop="code">
          <el-input
            v-model="resetForm.code"
            placeholder="请输入验证码"
            :prefix-icon="Key"
            clearable
          >
            <template #append>
              <el-button
                :disabled="countdown > 0"
                @click="sendVerificationCode"
                style="border: none"
              >
                {{ countdown > 0 ? `${countdown}s` : '重新发送' }}
              </el-button>
            </template>
          </el-input>
        </el-form-item>
        
        <el-form-item prop="password">
          <el-input
            v-model="resetForm.password"
            type="password"
            placeholder="请输入新密码"
            :prefix-icon="Lock"
            show-password
            clearable
          />
        </el-form-item>
        
        <el-form-item prop="password_confirmation">
          <el-input
            v-model="resetForm.password_confirmation"
            type="password"
            placeholder="请确认新密码"
            :prefix-icon="Lock"
            show-password
            clearable
          />
        </el-form-item>
        
        <el-form-item>
          <el-button
            type="primary"
            size="large"
            :loading="resetting"
            @click="resetPassword"
            class="action-button"
          >
            {{ resetting ? '重置中...' : '重置密码' }}
          </el-button>
        </el-form-item>
      </el-form>
    </div>
    
    <!-- 第三步：完成 -->
    <div v-if="currentStep === 2" class="step-content success-content">
      <el-result
        icon="success"
        title="密码重置成功"
        sub-title="您的密码已成功重置，请使用新密码登录"
      >
        <template #extra>
          <el-button type="primary" size="large" @click="goToLogin">
            立即登录
          </el-button>
        </template>
      </el-result>
    </div>
    
    <!-- 底部链接 -->
    <div v-if="currentStep < 2" class="form-footer">
      <el-link type="primary" @click="goToLogin">
        返回登录
      </el-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Message, Key, Lock } from '@element-plus/icons-vue'
import { sendVerificationCodeApi, resetPasswordApi } from '@/api/auth'

const router = useRouter()

// 表单引用
const emailFormRef = ref<FormInstance>()
const resetFormRef = ref<FormInstance>()

// 当前步骤
const currentStep = ref(0)

// 加载状态
const sendingCode = ref(false)
const resetting = ref(false)

// 倒计时
const countdown = ref(0)
let countdownTimer: NodeJS.Timeout | null = null

// 邮箱表单
const emailForm = reactive({
  email: ''
})

// 重置表单
const resetForm = reactive({
  code: '',
  password: '',
  password_confirmation: ''
})

// 验证规则
const emailRules: FormRules = {
  email: [
    { required: true, message: '请输入邮箱地址', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ]
}

const resetRules: FormRules = {
  code: [
    { required: true, message: '请输入验证码', trigger: 'blur' },
    { len: 6, message: '验证码为6位数字', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入新密码', trigger: 'blur' },
    { min: 6, max: 20, message: '密码长度在 6 到 20 个字符', trigger: 'blur' }
  ],
  password_confirmation: [
    { required: true, message: '请确认新密码', trigger: 'blur' },
    {
      validator: (rule: any, value: string, callback: any) => {
        if (value !== resetForm.password) {
          callback(new Error('两次输入密码不一致'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 发送验证码
const sendVerificationCode = async () => {
  if (!emailFormRef.value) return
  
  try {
    await emailFormRef.value.validate()
    
    sendingCode.value = true
    
    await sendVerificationCodeApi({
      email: emailForm.email,
      type: 'reset_password'
    })
    
    ElMessage.success('验证码已发送到您的邮箱')
    
    // 进入下一步
    currentStep.value = 1
    resetForm.code = ''
    resetForm.password = ''
    resetForm.password_confirmation = ''
    
    // 开始倒计时
    startCountdown()
  } catch (error) {
    console.error('发送验证码失败:', error)
  } finally {
    sendingCode.value = false
  }
}

// 重置密码
const resetPassword = async () => {
  if (!resetFormRef.value) return
  
  try {
    await resetFormRef.value.validate()
    
    resetting.value = true
    
    await resetPasswordApi({
      email: emailForm.email,
      code: resetForm.code,
      password: resetForm.password,
      password_confirmation: resetForm.password_confirmation
    })
    
    ElMessage.success('密码重置成功')
    currentStep.value = 2
  } catch (error) {
    console.error('重置密码失败:', error)
  } finally {
    resetting.value = false
  }
}

// 开始倒计时
const startCountdown = () => {
  countdown.value = 60
  countdownTimer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(countdownTimer!)
      countdownTimer = null
    }
  }, 1000)
}

// 跳转到登录页面
const goToLogin = () => {
  router.push('/login')
}

// 组件卸载时清理定时器
onUnmounted(() => {
  if (countdownTimer) {
    clearInterval(countdownTimer)
  }
})
</script>

<style scoped>
.forgot-password-form {
  width: 100%;
}

.form-header {
  text-align: center;
  margin-bottom: 30px;
}

.form-header h2 {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 8px;
}

.form-header p {
  font-size: 14px;
  color: #909399;
  margin: 0;
}

.steps {
  margin-bottom: 40px;
}

.step-content {
  margin-bottom: 30px;
}

.success-content {
  text-align: center;
}

.action-button {
  width: 100%;
  height: 44px;
  font-size: 16px;
  font-weight: 500;
}

.form-footer {
  text-align: center;
  margin-top: 20px;
  font-size: 14px;
  color: #606266;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}

:deep(.el-input__wrapper) {
  padding: 12px 16px;
}

:deep(.el-input-group__append) {
  padding: 0;
}

:deep(.el-input-group__append .el-button) {
  padding: 0 15px;
  margin: 0;
}

@media (max-width: 480px) {
  .form-header h2 {
    font-size: 20px;
  }
  
  .steps {
    margin-bottom: 30px;
  }
}
</style>
