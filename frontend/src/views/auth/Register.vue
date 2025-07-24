<template>
  <div class="register-form">
    <!-- 表单标题 -->
    <div class="form-header">
      <h2>用户注册</h2>
      <p>创建您的账号，开始使用实验教学管理平台</p>
    </div>
    
    <!-- 注册表单 -->
    <el-form
      ref="registerFormRef"
      :model="registerForm"
      :rules="registerRules"
      size="large"
      @submit.prevent="handleRegister"
    >
      <el-form-item prop="username">
        <el-input
          v-model="registerForm.username"
          placeholder="请输入用户名"
          :prefix-icon="User"
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="real_name">
        <el-input
          v-model="registerForm.real_name"
          placeholder="请输入真实姓名"
          :prefix-icon="UserFilled"
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="email">
        <el-input
          v-model="registerForm.email"
          placeholder="请输入邮箱地址"
          :prefix-icon="Message"
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="phone">
        <el-input
          v-model="registerForm.phone"
          placeholder="请输入手机号码（可选）"
          :prefix-icon="Phone"
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="school_id">
        <el-select
          v-model="registerForm.school_id"
          placeholder="请选择所属学校"
          filterable
          remote
          :remote-method="searchSchools"
          :loading="schoolLoading"
          clearable
          style="width: 100%"
        >
          <el-option
            v-for="school in schools"
            :key="school.id"
            :label="school.name"
            :value="school.id"
          >
            <span>{{ school.name }}</span>
            <span style="float: right; color: #8492a6; font-size: 13px">
              {{ school.region_name }}
            </span>
          </el-option>
        </el-select>
      </el-form-item>
      
      <el-form-item prop="password">
        <el-input
          v-model="registerForm.password"
          type="password"
          placeholder="请输入密码"
          :prefix-icon="Lock"
          show-password
          clearable
        />
      </el-form-item>
      
      <el-form-item prop="password_confirmation">
        <el-input
          v-model="registerForm.password_confirmation"
          type="password"
          placeholder="请确认密码"
          :prefix-icon="Lock"
          show-password
          clearable
          @keyup.enter="handleRegister"
        />
      </el-form-item>
      
      <el-form-item prop="agreement">
        <el-checkbox v-model="registerForm.agreement">
          我已阅读并同意
          <el-link type="primary" @click="showAgreement">
            《用户协议》
          </el-link>
          和
          <el-link type="primary" @click="showPrivacy">
            《隐私政策》
          </el-link>
        </el-checkbox>
      </el-form-item>
      
      <el-form-item>
        <el-button
          type="primary"
          size="large"
          :loading="loading"
          @click="handleRegister"
          class="register-button"
        >
          {{ loading ? '注册中...' : '注册' }}
        </el-button>
      </el-form-item>
    </el-form>
    
    <!-- 底部链接 -->
    <div class="form-footer">
      <span>已有账号？</span>
      <el-link type="primary" @click="goToLogin">
        立即登录
      </el-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { User, UserFilled, Message, Phone, Lock } from '@element-plus/icons-vue'
import { registerApi } from '@/api/auth'
import { getSchoolsApi, type School } from '@/api/user'

const router = useRouter()

// 表单引用
const registerFormRef = ref<FormInstance>()

// 加载状态
const loading = ref(false)
const schoolLoading = ref(false)

// 学校列表
const schools = ref<School[]>([])

// 注册表单数据
const registerForm = reactive({
  username: '',
  real_name: '',
  email: '',
  phone: '',
  school_id: undefined as number | undefined,
  password: '',
  password_confirmation: '',
  agreement: false
})

// 自定义验证函数
const validatePassword = (rule: any, value: string, callback: any) => {
  if (value === '') {
    callback(new Error('请输入密码'))
  } else if (value.length < 6) {
    callback(new Error('密码长度不能少于6位'))
  } else {
    if (registerForm.password_confirmation !== '') {
      registerFormRef.value?.validateField('password_confirmation')
    }
    callback()
  }
}

const validatePasswordConfirmation = (rule: any, value: string, callback: any) => {
  if (value === '') {
    callback(new Error('请确认密码'))
  } else if (value !== registerForm.password) {
    callback(new Error('两次输入密码不一致'))
  } else {
    callback()
  }
}

// 表单验证规则
const registerRules: FormRules = {
  username: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { min: 3, max: 20, message: '用户名长度在 3 到 20 个字符', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]+$/, message: '用户名只能包含字母、数字和下划线', trigger: 'blur' }
  ],
  real_name: [
    { required: true, message: '请输入真实姓名', trigger: 'blur' },
    { min: 2, max: 10, message: '姓名长度在 2 到 10 个字符', trigger: 'blur' }
  ],
  email: [
    { required: true, message: '请输入邮箱地址', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ],
  phone: [
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号码', trigger: 'blur' }
  ],
  password: [
    { validator: validatePassword, trigger: 'blur' }
  ],
  password_confirmation: [
    { validator: validatePasswordConfirmation, trigger: 'blur' }
  ],
  agreement: [
    { 
      validator: (rule: any, value: boolean, callback: any) => {
        if (!value) {
          callback(new Error('请阅读并同意用户协议和隐私政策'))
        } else {
          callback()
        }
      }, 
      trigger: 'change' 
    }
  ]
}

// 搜索学校
const searchSchools = async (query: string) => {
  if (query) {
    schoolLoading.value = true
    try {
      console.log('搜索学校:', query)
      const response = await getSchoolsApi({ search: query })
      console.log('搜索响应:', response)

      // 处理不同的响应格式
      let schoolData = []
      if (response.data) {
        if (response.data.data) {
          schoolData = response.data.data
        } else if (Array.isArray(response.data)) {
          schoolData = response.data
        } else {
          schoolData = response.data
        }
      }

      schools.value = Array.isArray(schoolData) ? schoolData : []
      console.log('搜索结果:', schools.value)
    } catch (error) {
      console.error('搜索学校失败:', error)
    } finally {
      schoolLoading.value = false
    }
  } else {
    // 如果没有搜索词，重新加载初始列表
    try {
      const response = await getSchoolsApi()
      let schoolData = []
      if (response.data) {
        if (response.data.data) {
          schoolData = response.data.data
        } else if (Array.isArray(response.data)) {
          schoolData = response.data
        } else {
          schoolData = response.data
        }
      }
      schools.value = Array.isArray(schoolData) ? schoolData.slice(0, 20) : []
    } catch (error) {
      console.error('重新加载学校列表失败:', error)
      schools.value = []
    }
  }
}

// 处理注册
const handleRegister = async () => {
  if (!registerFormRef.value) return
  
  try {
    await registerFormRef.value.validate()
    
    loading.value = true
    
    await registerApi({
      username: registerForm.username,
      real_name: registerForm.real_name,
      email: registerForm.email,
      phone: registerForm.phone || undefined,
      school_id: registerForm.school_id,
      password: registerForm.password,
      password_confirmation: registerForm.password_confirmation
    })
    
    ElMessage.success('注册成功！请登录您的账号')
    router.push('/login')
  } catch (error) {
    console.error('注册失败:', error)
  } finally {
    loading.value = false
  }
}

// 跳转到登录页面
const goToLogin = () => {
  router.push('/login')
}

// 显示用户协议
const showAgreement = () => {
  ElMessageBox.alert('用户协议内容...', '用户协议', {
    confirmButtonText: '确定'
  })
}

// 显示隐私政策
const showPrivacy = () => {
  ElMessageBox.alert('隐私政策内容...', '隐私政策', {
    confirmButtonText: '确定'
  })
}

// 初始化加载学校列表
onMounted(async () => {
  try {
    console.log('开始加载学校列表...')
    const response = await getSchoolsApi()
    console.log('API响应:', response)

    // 处理不同的响应格式
    let schoolData = []
    if (response.data) {
      if (response.data.data) {
        schoolData = response.data.data
      } else if (Array.isArray(response.data)) {
        schoolData = response.data
      } else {
        schoolData = response.data
      }
    }

    console.log('处理后的学校数据:', schoolData)
    schools.value = Array.isArray(schoolData) ? schoolData.slice(0, 20) : [] // 显示前20个学校
    console.log('设置的学校列表:', schools.value)
  } catch (error) {
    console.error('加载学校列表失败:', error)
  }
})
</script>

<style scoped>
.register-form {
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

.register-button {
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
  margin-bottom: 18px;
}

:deep(.el-input__wrapper) {
  padding: 12px 16px;
}

:deep(.el-select) {
  width: 100%;
}

@media (max-width: 480px) {
  .form-header h2 {
    font-size: 20px;
  }
}
</style>
