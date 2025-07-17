<template>
  <div class="app-header-content">
    <!-- 左侧 -->
    <div class="header-left">
      <el-button 
        :icon="Fold" 
        text 
        size="large"
        @click="appStore.toggleSidebar"
      />
      <span class="app-title">实验教学管理平台</span>
    </div>
    
    <!-- 右侧 -->
    <div class="header-right">
      <!-- 全屏切换 -->
      <el-tooltip content="全屏" placement="bottom">
        <el-button 
          :icon="FullScreen" 
          text 
          size="large"
          @click="toggleFullscreen"
        />
      </el-tooltip>
      
      <!-- 消息通知 -->
      <el-badge :value="appStore.unreadCount" :hidden="appStore.unreadCount === 0" class="header-item">
        <el-button 
          :icon="Bell" 
          text 
          size="large"
          @click="showNotifications"
        />
      </el-badge>
      
      <!-- 用户菜单 -->
      <el-dropdown class="header-item" @command="handleUserCommand">
        <div class="user-info">
          <el-avatar
            :src="authStore.userInfo?.avatar"
            :size="32"
          >
            {{ authStore.userName.charAt(0) }}
          </el-avatar>
          <div class="user-details">
            <span class="username">{{ authStore.userName }}</span>
            <span class="user-org" v-if="userOrganization">{{ userOrganization }}</span>
          </div>
          <el-icon><ArrowDown /></el-icon>
        </div>
        <template #dropdown>
          <el-dropdown-menu>
            <!-- 用户信息头部 -->
            <div class="dropdown-header">
              <div class="user-role">
                <el-tag :type="getRoleType(authStore.userInfo?.role)" size="small">
                  {{ getRoleLabel(authStore.userInfo?.role) }}
                </el-tag>
              </div>
              <div class="user-level" v-if="authStore.userInfo?.organization_level">
                <span class="level-text">{{ getOrganizationLevelText(authStore.userInfo.organization_level) }}</span>
              </div>
            </div>
            <el-divider style="margin: 8px 0;" />

            <el-dropdown-item command="profile">
              <el-icon><User /></el-icon>
              个人资料
            </el-dropdown-item>
            <el-dropdown-item command="settings">
              <el-icon><Setting /></el-icon>
              系统设置
            </el-dropdown-item>
            <el-dropdown-item command="password">
              <el-icon><Lock /></el-icon>
              修改密码
            </el-dropdown-item>
            <el-dropdown-item divided command="logout">
              <el-icon><SwitchButton /></el-icon>
              退出登录
            </el-dropdown-item>
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Fold,
  FullScreen,
  Bell,
  ArrowDown,
  User,
  Setting,
  Lock,
  SwitchButton
} from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useAppStore } from '@/stores/app'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const appStore = useAppStore()
const authStore = useAuthStore()
const router = useRouter()

// 计算用户组织信息
const userOrganization = computed(() => {
  const userInfo = authStore.userInfo
  if (!userInfo) return ''

  // 根据组织类型和级别显示组织信息
  if (userInfo.organization_type === 'school' && userInfo.school_name) {
    return userInfo.school_name
  } else if (userInfo.organization_type === 'region') {
    // 这里可以根据实际需要显示区域名称
    return getOrganizationLevelText(userInfo.organization_level || 5)
  }

  return userInfo.school_name || ''
})

// 获取角色类型样式
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
  return roleMap[role || ''] || '用户'
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

// 全屏切换
const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}

// 显示通知
const showNotifications = () => {
  ElMessage.info('通知功能开发中...')
}

// 处理用户菜单命令
const handleUserCommand = async (command: string) => {
  switch (command) {
    case 'profile':
      router.push('/profile')
      break
    case 'settings':
      router.push('/settings')
      break
    case 'password':
      router.push('/change-password')
      break
    case 'logout':
      await handleLogout()
      break
  }
}

// 处理退出登录
const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(
      '确定要退出登录吗？',
      '提示',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await authStore.logout()
  } catch (error) {
    // 用户取消操作
  }
}
</script>

<style scoped>
.app-header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
  padding: 0 20px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.app-title {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-item {
  cursor: pointer;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.user-info:hover {
  background-color: #f5f7fa;
}

.user-details {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  max-width: 120px;
}

.username {
  font-size: 14px;
  color: #606266;
  font-weight: 500;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 100%;
}

.user-org {
  font-size: 12px;
  color: #909399;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 100%;
}

.dropdown-header {
  padding: 8px 16px;
  background-color: #f8f9fa;
  margin: -8px -16px 0;
}

.user-role {
  margin-bottom: 4px;
}

.level-text {
  font-size: 12px;
  color: #909399;
}

@media (max-width: 768px) {
  .app-header-content {
    padding: 0 10px;
  }
  
  .app-title {
    font-size: 16px;
  }
  
  .username {
    display: none;
  }
}
</style>
