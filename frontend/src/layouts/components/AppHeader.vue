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
          <span class="username">{{ authStore.userName }}</span>
          <el-icon><ArrowDown /></el-icon>
        </div>
        <template #dropdown>
          <el-dropdown-menu>
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

.username {
  font-size: 14px;
  color: #606266;
  max-width: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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
