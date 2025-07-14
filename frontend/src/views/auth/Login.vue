<template>
  <div class="login-form">
    <!-- 表单标题 -->
    <div class="form-header">
      <h2>用户登录</h2>
      <p>欢迎回来，请输入您的账号信息</p>
    </div>
    
    <!-- 登录表单 -->
    <el-form
      ref="loginFormRef"
      :model="loginForm"
      :rules="loginRules"
      size="large"
      @submit.prevent="handleLogin"
    >
      <el-form-item prop="username">
        <el-input
          v-model="loginForm.username"
          placeholder="请输入用户名"
          :prefix-icon="User"
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="password">
        <el-input
          v-model="loginForm.password"
          type="password"
          placeholder="请输入密码"
          :prefix-icon="Lock"
          show-password
          clearable
          @keyup.enter="handleLogin"
        />
      </el-form-item>
      
      <el-form-item>
        <div class="form-options">
          <el-checkbox v-model="loginForm.remember">
            记住我
          </el-checkbox>
          <el-link type="primary" @click="goToForgotPassword">
            忘记密码？
          </el-link>
        </div>
      </el-form-item>
      
      <el-form-item>
        <el-button
          type="primary"
          size="large"
          :loading="loading"
          @click="handleLogin"
          class="login-button"
        >
          {{ loading ? '登录中...' : '登录' }}
        </el-button>
      </el-form-item>
    </el-form>
    
    <!-- 底部链接 -->
    <div class="form-footer">
      <span>还没有账号？</span>
      <el-link type="primary" @click="goToRegister">
        立即注册
      </el-link>
    </div>
    
    <!-- 演示账号 -->
    <div class="demo-accounts">
      <el-divider>演示账号</el-divider>
      <div class="demo-list">
        <el-button
          v-for="demo in demoAccounts"
          :key="demo.username"
          size="small"
          plain
          @click="fillDemoAccount(demo)"
        >
          {{ demo.label }}
        </el-button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { User, Lock } from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'
import { loginApi } from '@/api/auth'

const router = useRouter()
const authStore = useAuthStore()

// 表单引用
const loginFormRef = ref<FormInstance>()

// 加载状态
const loading = ref(false)

// 登录表单数据
const loginForm = reactive({
  username: '',
  password: '',
  remember: false
})

// 表单验证规则
const loginRules: FormRules = {
  username: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { min: 3, max: 20, message: '用户名长度在 3 到 20 个字符', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, max: 20, message: '密码长度在 6 到 20 个字符', trigger: 'blur' }
  ]
}

// 演示账号
const demoAccounts = [
  { username: 'admin', password: '123456', label: '管理员' },
  { username: 'teacher', password: '123456', label: '教师' },
  { username: 'student', password: '123456', label: '学生' }
]

// 处理登录
const handleLogin = async () => {
  if (!loginFormRef.value) return
  
  try {
    await loginFormRef.value.validate()
    
    loading.value = true
    
    const success = await authStore.login({
      username: loginForm.username,
      password: loginForm.password
    })
    
    if (success) {
      ElMessage.success('登录成功')
      
      // 跳转到首页或之前访问的页面
      const redirect = router.currentRoute.value.query.redirect as string
      router.push(redirect || '/dashboard')
    }
  } catch (error) {
    console.error('登录失败:', error)
  } finally {
    loading.value = false
  }
}

// 填充演示账号
const fillDemoAccount = (demo: { username: string; password: string }) => {
  loginForm.username = demo.username
  loginForm.password = demo.password
}

// 跳转到注册页面
const goToRegister = () => {
  router.push('/register')
}

// 跳转到忘记密码页面
const goToForgotPassword = () => {
  router.push('/forgot-password')
}
</script>

<style scoped>
.login-form {
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

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.login-button {
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

.demo-accounts {
  margin-top: 30px;
}

.demo-list {
  display: flex;
  gap: 8px;
  justify-content: center;
  flex-wrap: wrap;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}

:deep(.el-input__wrapper) {
  padding: 12px 16px;
}

:deep(.el-divider__text) {
  font-size: 12px;
  color: #909399;
}

@media (max-width: 480px) {
  .form-header h2 {
    font-size: 20px;
  }
  
  .demo-list {
    flex-direction: column;
    align-items: center;
  }
  
  .demo-list .el-button {
    width: 120px;
  }
}
</style>
