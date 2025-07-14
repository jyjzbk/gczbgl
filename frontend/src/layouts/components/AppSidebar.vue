<template>
  <div class="sidebar-container">
    <!-- Logo区域 -->
    <div class="sidebar-logo" :class="{ collapsed: appStore.sidebarCollapsed }">
      <div class="logo-img">
        <el-icon :size="32" color="#fff">
          <Operation />
        </el-icon>
      </div>
      <span v-if="!appStore.sidebarCollapsed" class="logo-text">实验管理</span>
    </div>
    
    <!-- 菜单 -->
    <el-menu
      :default-active="activeMenu"
      class="sidebar-menu"
      :collapse="appStore.sidebarCollapsed"
      :unique-opened="true"
      router
      @select="handleMenuSelect"
    >
      <!-- 仪表盘 -->
      <el-menu-item index="/dashboard">
        <el-icon><Odometer /></el-icon>
        <template #title>仪表盘</template>
      </el-menu-item>
      
      <!-- 用户管理 -->
      <el-sub-menu v-if="hasUserPermission" index="user">
        <template #title>
          <el-icon><User /></el-icon>
          <span>用户管理</span>
        </template>
        <el-menu-item v-if="authStore.hasAnyPermission(['user', 'user.list'])" index="/users">用户列表</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['role', 'role.list'])" index="/roles">角色管理</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['role', 'role.list'])" index="/permissions">权限管理</el-menu-item>
      </el-sub-menu>
      
      <!-- 基础数据 -->
      <el-sub-menu v-if="hasBasicDataPermission" index="basic">
        <template #title>
          <el-icon><Setting /></el-icon>
          <span>基础数据</span>
        </template>
        <el-menu-item v-if="authStore.hasAnyPermission(['user', 'user.list', 'user.create'])" index="/schools">学校管理</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['user', 'user.list', 'user.create'])" index="/laboratories">实验室管理</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['user', 'user.list', 'user.create'])" index="/subjects">学科管理</el-menu-item>
      </el-sub-menu>
      
      <!-- 实验管理 -->
      <el-sub-menu v-if="hasExperimentPermission" index="experiment">
        <template #title>
          <el-icon><Operation /></el-icon>
          <span>实验管理</span>
        </template>
        <el-menu-item v-if="authStore.hasAnyPermission(['experiment', 'experiment.catalog'])" index="/experiment-catalogs">实验目录</el-menu-item>
        <el-menu-item v-if="authStore.hasPermission('experiment.booking')" index="/experiment-bookings">实验预约</el-menu-item>
        <el-menu-item v-if="authStore.hasPermission('experiment.record')" index="/experiment-records">实验记录</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record'])" index="/experiment-statistics">实验统计</el-menu-item>
      </el-sub-menu>

      <!-- 设备管理 -->
      <el-sub-menu v-if="hasEquipmentPermission" index="equipment">
        <template #title>
          <el-icon><Box /></el-icon>
          <span>设备管理</span>
        </template>
        <el-menu-item v-if="authStore.hasAnyPermission(['equipment', 'equipment.list'])" index="/equipment-management">设备档案</el-menu-item>
        <el-menu-item v-if="authStore.hasPermission('equipment.borrow')" index="/equipment-borrow">设备借用</el-menu-item>
        <el-menu-item v-if="authStore.hasPermission('equipment.maintenance')" index="/equipment-maintenance">设备维修</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['equipment', 'equipment.list'])" index="/equipment-qrcode">二维码管理</el-menu-item>
      </el-sub-menu>
      
      <!-- 统计报表 -->
      <el-sub-menu v-if="hasStatisticsPermission" index="statistics">
        <template #title>
          <el-icon><DataAnalysis /></el-icon>
          <span>统计报表</span>
        </template>
        <el-menu-item v-if="authStore.hasAnyPermission(['experiment', 'experiment.catalog'])" index="/statistics/experiment">实验统计</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['equipment', 'equipment.list'])" index="/statistics/equipment">设备统计</el-menu-item>
        <el-menu-item v-if="authStore.hasAnyPermission(['user', 'user.list'])" index="/statistics/region">区域分析</el-menu-item>
      </el-sub-menu>
      
      <!-- 系统管理 -->
      <el-sub-menu index="system" v-if="hasSystemPermission">
        <template #title>
          <el-icon><Tools /></el-icon>
          <span>系统管理</span>
        </template>
        <el-menu-item index="/system/logs">系统日志</el-menu-item>
        <el-menu-item index="/system/config">系统配置</el-menu-item>
        <el-menu-item index="/system/backup">数据备份</el-menu-item>
      </el-sub-menu>
    </el-menu>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import {
  Odometer,
  User,
  Setting,
  Operation,
  Box,
  DataAnalysis,
  Tools
} from '@element-plus/icons-vue'
import { useAppStore } from '@/stores/app'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const appStore = useAppStore()
const authStore = useAuthStore()

// 当前激活的菜单
const activeMenu = computed(() => {
  return route.path
})

// 权限检查
const hasUserPermission = computed(() => {
  const result = authStore.hasAnyPermission(['user', 'user.list', 'role', 'role.list'])
  console.log('hasUserPermission:', result, 'permissions:', authStore.permissions)
  return result
})

const hasSystemPermission = computed(() => {
  return authStore.hasAnyPermission(['system', 'system.read', 'log', 'log.read'])
})

const hasEquipmentPermission = computed(() => {
  return authStore.hasAnyPermission(['equipment', 'equipment.list'])
})

const hasExperimentPermission = computed(() => {
  return authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'])
})

const hasBasicDataPermission = computed(() => {
  return authStore.hasAnyPermission(['user', 'user.list', 'user.create'])
})

const hasStatisticsPermission = computed(() => {
  // 统计报表权限：有实验或设备权限的用户都可以查看对应的统计
  return authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']) ||
         authStore.hasAnyPermission(['equipment', 'equipment.list']) ||
         authStore.hasAnyPermission(['user', 'user.list'])
})

// 处理菜单选择
const handleMenuSelect = (index: string) => {
  // 移动端点击菜单后关闭侧边栏
  if (appStore.isMobile) {
    appStore.closeMobileSidebar()
  }
}
</script>

<style scoped>
.sidebar-container {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  padding: 0 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s;
}

.sidebar-logo.collapsed {
  padding: 0 8px;
}

.logo-img {
  width: 32px;
  height: 32px;
  margin-right: 8px;
}

.sidebar-logo.collapsed .logo-img {
  margin-right: 0;
}

.logo-text {
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  white-space: nowrap;
}

.sidebar-menu {
  flex: 1;
  border: none;
  background-color: transparent;
}

.sidebar-menu .el-menu-item {
  color: rgba(255, 255, 255, 0.65);
  border-radius: 0;
}

.sidebar-menu .el-menu-item:hover {
  background-color: rgba(255, 255, 255, 0.08) !important;
  color: #fff;
}

.sidebar-menu .el-menu-item.is-active {
  background-color: #1890ff !important;
  color: #fff;
}

.sidebar-menu .el-sub-menu .el-sub-menu__title {
  color: rgba(255, 255, 255, 0.65);
}

.sidebar-menu .el-sub-menu .el-sub-menu__title:hover {
  background-color: rgba(255, 255, 255, 0.08) !important;
  color: #fff;
}

.sidebar-menu .el-sub-menu .el-menu-item {
  background-color: rgba(0, 0, 0, 0.2) !important;
  color: rgba(255, 255, 255, 0.8) !important;
  margin: 2px 8px;
  border-radius: 4px;
}

.sidebar-menu .el-sub-menu .el-menu-item:hover {
  background-color: rgba(255, 255, 255, 0.15) !important;
  color: #fff !important;
}

.sidebar-menu .el-sub-menu .el-menu-item.is-active {
  background-color: #1890ff !important;
  color: #fff !important;
}

/* 折叠状态下的样式 */
.sidebar-menu.el-menu--collapse {
  width: 64px;
}

.sidebar-menu.el-menu--collapse .el-sub-menu .el-sub-menu__title {
  padding: 0 20px;
}

.sidebar-menu.el-menu--collapse .el-menu-item {
  padding: 0 20px;
}
</style>
