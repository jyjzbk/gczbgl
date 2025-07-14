import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAppStore = defineStore('app', () => {
  // 侧边栏状态
  const sidebarCollapsed = ref(false)
  const sidebarMobileOpen = ref(false)
  
  // 设备类型
  const isMobile = ref(false)
  
  // 页面加载状态
  const pageLoading = ref(false)
  
  // 未读消息数量
  const unreadCount = ref(0)
  
  // 面包屑导航
  const breadcrumbs = ref<Array<{ title: string; path?: string }>>([])
  
  // 计算属性
  const sidebarWidth = computed(() => {
    if (isMobile.value) {
      return sidebarMobileOpen.value ? '200px' : '0px'
    }
    return sidebarCollapsed.value ? '64px' : '200px'
  })
  
  // 切换侧边栏
  const toggleSidebar = () => {
    if (isMobile.value) {
      sidebarMobileOpen.value = !sidebarMobileOpen.value
    } else {
      sidebarCollapsed.value = !sidebarCollapsed.value
    }
  }
  
  // 关闭移动端侧边栏
  const closeMobileSidebar = () => {
    if (isMobile.value) {
      sidebarMobileOpen.value = false
    }
  }
  
  // 设置设备类型
  const setDevice = (device: 'mobile' | 'desktop') => {
    isMobile.value = device === 'mobile'
    if (device === 'desktop') {
      sidebarMobileOpen.value = false
    }
  }
  
  // 设置页面加载状态
  const setPageLoading = (loading: boolean) => {
    pageLoading.value = loading
  }
  
  // 设置未读消息数量
  const setUnreadCount = (count: number) => {
    unreadCount.value = count
  }
  
  // 设置面包屑导航
  const setBreadcrumbs = (crumbs: Array<{ title: string; path?: string }>) => {
    breadcrumbs.value = crumbs
  }
  
  // 添加面包屑
  const addBreadcrumb = (crumb: { title: string; path?: string }) => {
    breadcrumbs.value.push(crumb)
  }
  
  // 清空面包屑
  const clearBreadcrumbs = () => {
    breadcrumbs.value = []
  }
  
  return {
    // 状态
    sidebarCollapsed,
    sidebarMobileOpen,
    isMobile,
    pageLoading,
    unreadCount,
    breadcrumbs,
    
    // 计算属性
    sidebarWidth,
    
    // 方法
    toggleSidebar,
    closeMobileSidebar,
    setDevice,
    setPageLoading,
    setUnreadCount,
    setBreadcrumbs,
    addBreadcrumb,
    clearBreadcrumbs
  }
})
