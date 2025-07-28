<template>
  <div class="app-breadcrumb" v-if="breadcrumbs.length > 0">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item 
        v-for="(item, index) in breadcrumbs" 
        :key="index"
        :to="item.path"
      >
        {{ item.title }}
      </el-breadcrumb-item>
    </el-breadcrumb>
  </div>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '@/stores/app'

const route = useRoute()
const appStore = useAppStore()

// 路由标题映射
const routeTitleMap: Record<string, string> = {
  '/dashboard': '仪表盘',
  '/users': '用户列表',
  '/roles': '角色管理',
  '/permissions': '权限管理',
  '/schools': '学校管理',
  '/laboratories': '实验室管理',
  '/laboratory-types': '实验室类型管理',
  '/subjects': '学科管理',
  '/equipment-standards': '教学仪器配备标准',
  '/experiment-catalogs': '实验目录',
  '/experiment-bookings': '实验预约',
  '/smart-reservation': '智能预约',
  '/laboratory-schedule': '实验室课表',
  '/experiment-records': '实验记录',
  '/personal-archive': '个人实验档案',
  '/experiment-statistics': '实验统计',
  '/experiment-requirements-config': '实验要求配置管理',
  '/experiment-monitoring': '实验开出情况监控',
  '/experiment-alerts': '实验预警管理',
  '/school-experiment-catalog': '学校实验目录管理',
  '/school-catalog-config/my-config': '我的目录配置',
  '/school-catalog-config/subordinate-assignment': '下级目录指定',
  '/school-catalog-config/completion-statistics': '完成率统计',
  '/equipment-management': '设备档案管理',
  '/equipment-borrow': '设备借用管理',
  '/equipment-maintenance': '设备维修管理',
  '/equipment-qrcode': '设备二维码管理',
  '/statistics/dashboard': '统计仪表盘',
  '/statistics/reports': '详细报表',
  '/statistics/experiment': '实验统计',
  '/statistics/equipment': '设备统计',
  '/statistics/region': '区域分析',
  '/system/logs': '系统日志',
  '/system/config': '系统配置',
  '/system/backup': '数据备份',
  '/profile': '个人资料',
  '/settings': '系统设置',
  '/change-password': '修改密码'
}

// 生成面包屑
const generateBreadcrumbs = (path: string) => {
  const breadcrumbs = []
  
  // 首页
  if (path !== '/dashboard') {
    breadcrumbs.push({ title: '首页', path: '/dashboard' })
  }
  
  // 解析路径
  const pathSegments = path.split('/').filter(segment => segment)
  let currentPath = ''
  
  for (const segment of pathSegments) {
    currentPath += `/${segment}`
    const title = routeTitleMap[currentPath] || segment
    
    // 最后一个路径不添加链接
    const isLast = currentPath === path
    breadcrumbs.push({
      title,
      path: isLast ? undefined : currentPath
    })
  }
  
  return breadcrumbs
}

// 计算面包屑
const breadcrumbs = computed(() => {
  // 如果store中有自定义面包屑，使用自定义的
  if (appStore.breadcrumbs.length > 0) {
    return appStore.breadcrumbs
  }
  
  // 否则根据路由自动生成
  return generateBreadcrumbs(route.path)
})

// 监听路由变化，自动更新面包屑
watch(
  () => route.path,
  (newPath) => {
    // 清空自定义面包屑，使用自动生成的
    appStore.clearBreadcrumbs()
  },
  { immediate: true }
)
</script>

<style scoped>
.app-breadcrumb {
  margin-bottom: 16px;
}

.app-breadcrumb :deep(.el-breadcrumb__item) {
  .el-breadcrumb__inner {
    color: #606266;
    font-weight: normal;
    cursor: pointer;
  }
  
  .el-breadcrumb__inner:hover {
    color: #409eff;
  }
  
  &:last-child .el-breadcrumb__inner {
    color: #303133;
    font-weight: 500;
    cursor: default;
  }
  
  &:last-child .el-breadcrumb__inner:hover {
    color: #303133;
  }
}

@media (max-width: 768px) {
  .app-breadcrumb {
    margin-bottom: 12px;
  }
  
  .app-breadcrumb :deep(.el-breadcrumb__item) {
    .el-breadcrumb__inner {
      font-size: 12px;
    }
  }
}
</style>
