<template>
  <div class="profile-page">
    <div class="page-card">
      <div class="profile-header">
        <h2>个人资料</h2>
        <p>管理您的个人信息和账户设置</p>
      </div>
      
      <el-row :gutter="20">
        <!-- 左侧头像区域 -->
        <el-col :span="8">
          <div class="avatar-section">
            <div class="avatar-container">
              <el-avatar :size="120" :src="userInfo?.avatar">
                {{ userInfo?.real_name?.charAt(0) || 'U' }}
              </el-avatar>
              <div class="avatar-overlay" @click="showAvatarUpload">
                <el-icon><Camera /></el-icon>
                <span>更换头像</span>
              </div>
            </div>
            
            <div class="user-basic-info">
              <h3>{{ userInfo?.real_name }}</h3>
              <p class="username">@{{ userInfo?.username }}</p>
              <el-tag :type="getRoleType(userInfo?.role)">
                {{ getRoleLabel(userInfo?.role) }}
              </el-tag>
            </div>
          </div>
        </el-col>
        
        <!-- 右侧表单区域 -->
        <el-col :span="16">
          <el-tabs v-model="activeTab" class="profile-tabs">
            <!-- 基本信息 -->
            <el-tab-pane label="基本信息" name="basic">
              <el-form
                ref="profileFormRef"
                :model="profileForm"
                :rules="profileRules"
                label-width="100px"
                size="large"
              >
                <el-form-item label="真实姓名" prop="real_name">
                  <el-input
                    v-model="profileForm.real_name"
                    placeholder="请输入真实姓名"
                  />
                </el-form-item>
                
                <el-form-item label="邮箱地址" prop="email">
                  <el-input
                    v-model="profileForm.email"
                    placeholder="请输入邮箱地址"
                  />
                </el-form-item>
                
                <el-form-item label="手机号码" prop="phone">
                  <el-input
                    v-model="profileForm.phone"
                    placeholder="请输入手机号码"
                  />
                </el-form-item>
                
                <el-form-item label="部门" prop="department">
                  <el-input
                    v-model="profileForm.department"
                    placeholder="请输入所在部门"
                  />
                </el-form-item>
                
                <el-form-item label="职位" prop="position">
                  <el-input
                    v-model="profileForm.position"
                    placeholder="请输入职位"
                  />
                </el-form-item>
                
                <el-form-item label="个人简介" prop="bio">
                  <el-input
                    v-model="profileForm.bio"
                    type="textarea"
                    :rows="4"
                    placeholder="请输入个人简介"
                    maxlength="200"
                    show-word-limit
                  />
                </el-form-item>
                
                <el-form-item>
                  <el-button
                    type="primary"
                    :loading="updating"
                    @click="updateProfile"
                  >
                    保存修改
                  </el-button>
                  <el-button @click="resetForm">
                    重置
                  </el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
            
            <!-- 安全设置 -->
            <el-tab-pane label="安全设置" name="security">
              <div class="security-section">
                <div class="security-item">
                  <div class="security-info">
                    <h4>登录密码</h4>
                    <p>定期更换密码可以提高账户安全性</p>
                  </div>
                  <el-button type="primary" @click="showChangePassword">
                    修改密码
                  </el-button>
                </div>
                
                <el-divider />
                
                <div class="security-item">
                  <div class="security-info">
                    <h4>邮箱验证</h4>
                    <p>已验证的邮箱：{{ userInfo?.email }}</p>
                  </div>
                  <el-button>
                    重新验证
                  </el-button>
                </div>
                
                <el-divider />
                
                <div class="security-item">
                  <div class="security-info">
                    <h4>登录记录</h4>
                    <p>查看最近的登录记录和设备信息</p>
                  </div>
                  <el-button>
                    查看记录
                  </el-button>
                </div>
              </div>
            </el-tab-pane>
            
            <!-- 账户信息 -->
            <el-tab-pane label="账户信息" name="account">
              <div class="account-info">
                <el-descriptions :column="1" border>
                  <el-descriptions-item label="用户ID">
                    {{ userInfo?.id }}
                  </el-descriptions-item>
                  <el-descriptions-item label="用户名">
                    {{ userInfo?.username }}
                  </el-descriptions-item>
                  <el-descriptions-item label="角色">
                    <el-tag :type="getRoleType(userInfo?.role)">
                      {{ getRoleLabel(userInfo?.role) }}
                    </el-tag>
                  </el-descriptions-item>
                  <el-descriptions-item label="组织级别" v-if="userInfo?.organization_level">
                    <el-tag type="info">
                      {{ getOrganizationLevelText(userInfo.organization_level) }}
                    </el-tag>
                  </el-descriptions-item>
                  <el-descriptions-item label="组织类型" v-if="userInfo?.organization_type">
                    {{ userInfo.organization_type === 'school' ? '学校' : '行政区域' }}
                  </el-descriptions-item>
                  <el-descriptions-item label="所属组织">
                    {{ userInfo?.organization_name || userInfo?.school_name || '未设置' }}
                  </el-descriptions-item>
                  <el-descriptions-item label="数据权限范围">
                    {{ getDataScopeText(userInfo?.organization_level) }}
                  </el-descriptions-item>
                  <el-descriptions-item label="注册时间">
                    {{ formatDate(userInfo?.created_at) }}
                  </el-descriptions-item>
                  <el-descriptions-item label="最后更新">
                    {{ formatDate(userInfo?.updated_at) }}
                  </el-descriptions-item>
                </el-descriptions>
              </div>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </div>
    
    <!-- 头像上传对话框 -->
    <el-dialog
      v-model="avatarDialogVisible"
      title="更换头像"
      width="400px"
      :before-close="handleAvatarDialogClose"
    >
      <div class="avatar-upload">
        <el-upload
          ref="uploadRef"
          :auto-upload="false"
          :show-file-list="false"
          :on-change="handleAvatarChange"
          accept="image/*"
          drag
        >
          <div class="upload-area">
            <el-icon class="upload-icon"><Plus /></el-icon>
            <div class="upload-text">
              <p>点击或拖拽图片到此处</p>
              <p class="upload-tip">支持 JPG、PNG 格式，文件大小不超过 2MB</p>
            </div>
          </div>
        </el-upload>
        
        <div v-if="avatarPreview" class="avatar-preview">
          <img :src="avatarPreview" alt="头像预览" />
        </div>
      </div>
      
      <template #footer>
        <el-button @click="avatarDialogVisible = false">取消</el-button>
        <el-button
          type="primary"
          :loading="uploadingAvatar"
          :disabled="!avatarFile"
          @click="uploadAvatar"
        >
          上传头像
        </el-button>
      </template>
    </el-dialog>
    
    <!-- 修改密码对话框 -->
    <ChangePasswordDialog
      v-model="changePasswordVisible"
      @success="handlePasswordChanged"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules, type UploadFile } from 'element-plus'
import { Camera, Plus } from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'
import { updateProfileApi, uploadAvatarApi } from '@/api/user'
import ChangePasswordDialog from './components/ChangePasswordDialog.vue'
import dayjs from 'dayjs'

const authStore = useAuthStore()

// 表单引用
const profileFormRef = ref<FormInstance>()
const uploadRef = ref()

// 当前标签页
const activeTab = ref('basic')

// 加载状态
const updating = ref(false)
const uploadingAvatar = ref(false)

// 对话框状态
const avatarDialogVisible = ref(false)
const changePasswordVisible = ref(false)

// 头像相关
const avatarFile = ref<File | null>(null)
const avatarPreview = ref('')

// 用户信息
const userInfo = computed(() => authStore.userInfo)

// 个人资料表单
const profileForm = reactive({
  real_name: '',
  email: '',
  phone: '',
  department: '',
  position: '',
  bio: ''
})

// 表单验证规则
const profileRules: FormRules = {
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
  ]
}

// 获取角色类型
const getRoleType = (role?: string) => {
  const roleMap: Record<string, string> = {
    'super_admin': 'danger',
    'province_admin': 'danger',
    'city_admin': 'warning',
    'county_admin': 'warning',
    'district_admin': 'info',
    'school_admin': 'info',
    'teacher': 'success',
    'student': ''
  }
  return roleMap[role || ''] || 'info'
}

// 获取角色标签
const getRoleLabel = (role?: string) => {
  const roleMap: Record<string, string> = {
    'super_admin': '超级管理员',
    'province_admin': '省级管理员',
    'city_admin': '市级管理员',
    'county_admin': '区县管理员',
    'district_admin': '学区管理员',
    'school_admin': '学校管理员',
    'teacher': '教师',
    'student': '学生'
  }
  return roleMap[role || ''] || '未知'
}

// 获取组织级别文本
const getOrganizationLevelText = (level: number) => {
  const levelMap: Record<number, string> = {
    1: '省级',
    2: '市级',
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return levelMap[level] || '未知级别'
}

// 获取数据权限范围文本
const getDataScopeText = (level?: number) => {
  if (!level) return '未设置'

  const scopeMap: Record<number, string> = {
    1: '全省数据',
    2: '本市及下级数据',
    3: '本区县及下级数据',
    4: '本学区学校数据',
    5: '仅本校数据'
  }
  return scopeMap[level] || '未知范围'
}

// 格式化日期
const formatDate = (date?: string) => {
  return date ? dayjs(date).format('YYYY-MM-DD HH:mm:ss') : '-'
}

// 初始化表单数据
const initFormData = () => {
  if (userInfo.value) {
    profileForm.real_name = userInfo.value.real_name || ''
    profileForm.email = userInfo.value.email || ''
    profileForm.phone = userInfo.value.phone || ''
    profileForm.department = userInfo.value.department || ''
    profileForm.position = userInfo.value.position || ''
    profileForm.bio = userInfo.value.bio || ''
  }
}

// 更新个人资料
const updateProfile = async () => {
  if (!profileFormRef.value) return
  
  try {
    await profileFormRef.value.validate()
    
    updating.value = true
    
    await updateProfileApi(profileForm)
    
    // 更新本地用户信息
    await authStore.fetchUserInfo()
    
    ElMessage.success('个人资料更新成功')
  } catch (error) {
    console.error('更新个人资料失败:', error)
  } finally {
    updating.value = false
  }
}

// 重置表单
const resetForm = () => {
  initFormData()
}

// 显示头像上传对话框
const showAvatarUpload = () => {
  avatarDialogVisible.value = true
  avatarFile.value = null
  avatarPreview.value = ''
}

// 处理头像文件变化
const handleAvatarChange = (file: UploadFile) => {
  const rawFile = file.raw
  if (!rawFile) return
  
  // 检查文件类型
  if (!rawFile.type.startsWith('image/')) {
    ElMessage.error('请选择图片文件')
    return
  }
  
  // 检查文件大小
  if (rawFile.size > 2 * 1024 * 1024) {
    ElMessage.error('图片大小不能超过 2MB')
    return
  }
  
  avatarFile.value = rawFile
  
  // 生成预览
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(rawFile)
}

// 上传头像
const uploadAvatar = async () => {
  if (!avatarFile.value) return
  
  try {
    uploadingAvatar.value = true
    
    await uploadAvatarApi(avatarFile.value)
    
    // 更新本地用户信息
    await authStore.fetchUserInfo()
    
    ElMessage.success('头像更新成功')
    avatarDialogVisible.value = false
  } catch (error) {
    console.error('上传头像失败:', error)
  } finally {
    uploadingAvatar.value = false
  }
}

// 关闭头像对话框
const handleAvatarDialogClose = () => {
  avatarFile.value = null
  avatarPreview.value = ''
}

// 显示修改密码对话框
const showChangePassword = () => {
  changePasswordVisible.value = true
}

// 密码修改成功回调
const handlePasswordChanged = () => {
  ElMessage.success('密码修改成功')
}

onMounted(() => {
  initFormData()
})
</script>

<style scoped>
.profile-page {
  padding: 0;
}

.profile-header {
  margin-bottom: 30px;
}

.profile-header h2 {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 8px;
}

.profile-header p {
  font-size: 14px;
  color: #909399;
  margin: 0;
}

.avatar-section {
  text-align: center;
}

.avatar-container {
  position: relative;
  display: inline-block;
  margin-bottom: 20px;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
  cursor: pointer;
  color: white;
  font-size: 12px;
}

.avatar-container:hover .avatar-overlay {
  opacity: 1;
}

.user-basic-info h3 {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 8px;
}

.username {
  font-size: 14px;
  color: #909399;
  margin: 0 0 12px;
}

.profile-tabs {
  margin-top: 20px;
}

.security-section {
  max-width: 600px;
}

.security-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
}

.security-info h4 {
  font-size: 16px;
  font-weight: 500;
  color: #303133;
  margin: 0 0 4px;
}

.security-info p {
  font-size: 14px;
  color: #909399;
  margin: 0;
}

.account-info {
  max-width: 600px;
}

.avatar-upload {
  text-align: center;
}

.upload-area {
  padding: 40px 20px;
  border: 2px dashed #dcdfe6;
  border-radius: 8px;
  transition: border-color 0.3s;
}

.upload-area:hover {
  border-color: #409eff;
}

.upload-icon {
  font-size: 40px;
  color: #c0c4cc;
  margin-bottom: 16px;
}

.upload-text p {
  margin: 0;
  color: #606266;
}

.upload-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 8px;
}

.avatar-preview {
  margin-top: 20px;
}

.avatar-preview img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
}

@media (max-width: 768px) {
  .profile-header h2 {
    font-size: 20px;
  }
  
  .security-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>
