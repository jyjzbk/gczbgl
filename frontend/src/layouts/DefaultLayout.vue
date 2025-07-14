<template>
  <div class="app-container">
    <!-- 顶部导航 -->
    <el-header class="app-header" height="60px">
      <AppHeader />
    </el-header>
    
    <el-container>
      <!-- 侧边栏 -->
      <el-aside 
        :width="appStore.sidebarWidth" 
        class="app-sidebar"
        :class="{ 
          collapsed: appStore.sidebarCollapsed,
          'mobile-open': appStore.sidebarMobileOpen 
        }"
      >
        <AppSidebar />
      </el-aside>
      
      <!-- 主内容区 -->
      <el-main class="app-main">
        <!-- 面包屑导航 -->
        <AppBreadcrumb />
        
        <!-- 页面内容 -->
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </el-main>
    </el-container>
    
    <!-- 移动端遮罩层 -->
    <div
      v-if="appStore.isMobile && appStore.sidebarMobileOpen"
      class="mobile-mask"
      @click="appStore.closeMobileSidebar"
    />

    <!-- 权限调试组件 -->
    <DebugPermissions v-if="isDevelopment" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, computed } from 'vue'
import { useAppStore } from '@/stores/app'
import AppHeader from './components/AppHeader.vue'
import AppSidebar from './components/AppSidebar.vue'
import AppBreadcrumb from './components/AppBreadcrumb.vue'
import DebugPermissions from '@/components/DebugPermissions.vue'

const appStore = useAppStore()

// 开发环境检查
const isDevelopment = computed(() => {
  return import.meta.env.DEV
})

// 检测设备类型
const checkDevice = () => {
  const isMobile = window.innerWidth <= 768
  appStore.setDevice(isMobile ? 'mobile' : 'desktop')
}

// 窗口大小变化监听
const handleResize = () => {
  checkDevice()
}

onMounted(() => {
  checkDevice()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.mobile-mask {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  z-index: 999;
}
</style>
