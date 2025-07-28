import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

/**
 * 学校实验目录配置权限控制组合函数
 */
export function useSchoolCatalogPermissions() {
  const authStore = useAuthStore()

  // 基础权限检查
  const hasPermission = (permission: string) => {
    return authStore.hasPermission(permission)
  }

  const hasAnyPermission = (permissions: string[]) => {
    return authStore.hasAnyPermission(permissions)
  }

  // 学校目录配置相关权限
  const permissions = computed(() => ({
    // 查看权限
    canView: hasPermission('school_experiment_catalog.view'),
    
    // 配置权限
    canConfig: hasPermission('school_experiment_catalog.config'),
    
    // 指定权限
    canAssign: hasPermission('school_experiment_catalog.assign'),
    
    // 修改权限
    canModify: hasPermission('school_experiment_catalog.modify'),
    
    // 删除实验权限
    canDeleteExperiments: hasPermission('school_experiment_catalog.delete_experiments'),
    
    // 查看完成率统计权限
    canViewStats: hasPermission('school_experiment_catalog.completion_stats'),
    
    // 管理基准权限
    canManageBaseline: hasPermission('school_experiment_catalog.baseline_manage'),
    
    // 实验目录制定权限
    canCreateProvinceCatalog: hasPermission('experiment_catalog.create_province'),
    canCreateCityCatalog: hasPermission('experiment_catalog.create_city'),
    canCreateCountyCatalog: hasPermission('experiment_catalog.create_county'),
    canApproveBaseline: hasPermission('experiment_catalog.approve_baseline')
  }))

  // 菜单显示权限
  const menuPermissions = computed(() => ({
    // 学校目录配置主菜单
    showSchoolCatalogConfig: hasAnyPermission([
      'school_experiment_catalog.view',
      'school_experiment_catalog.config',
      'school_experiment_catalog.assign'
    ]),
    
    // 我的目录配置菜单
    showMyCatalogConfig: hasAnyPermission([
      'school_experiment_catalog.view',
      'school_experiment_catalog.config'
    ]),
    
    // 下级目录指定菜单
    showSubordinateAssignment: hasPermission('school_experiment_catalog.assign'),
    
    // 完成率统计菜单
    showCompletionStatistics: hasPermission('school_experiment_catalog.completion_stats')
  }))

  // 按钮显示权限
  const buttonPermissions = computed(() => ({
    // 配置相关按钮
    showConfigButton: permissions.value.canConfig,
    showModifyButton: permissions.value.canModify,
    showAssignButton: permissions.value.canAssign,
    
    // 统计相关按钮
    showStatsButton: permissions.value.canViewStats,
    showRecalculateButton: permissions.value.canManageBaseline,
    showExportButton: permissions.value.canViewStats,
    
    // 批量操作按钮
    showBatchAssignButton: permissions.value.canAssign,
    showBatchConfigButton: permissions.value.canConfig
  }))

  // 用户级别权限检查
  const userLevelPermissions = computed(() => {
    const user = authStore.user
    const userLevel = user?.organization_level || 5
    
    return {
      // 用户级别
      userLevel,
      
      // 是否为省级用户
      isProvinceLevel: userLevel === 1,
      
      // 是否为市级用户
      isCityLevel: userLevel === 2,
      
      // 是否为区县级用户
      isCountyLevel: userLevel === 3,
      
      // 是否为学区级用户
      isDistrictLevel: userLevel === 4,
      
      // 是否为学校级用户
      isSchoolLevel: userLevel === 5,
      
      // 是否为管理员级别（省市区县）
      isAdminLevel: userLevel <= 3,
      
      // 可管理的级别
      canManageLevels: userLevel <= 3 ? Array.from({ length: 5 - userLevel }, (_, i) => userLevel + i + 1) : []
    }
  })

  // 数据权限检查
  const dataPermissions = {
    // 检查是否可以查看指定学校的数据
    canViewSchoolData(schoolId: number): boolean {
      const user = authStore.user
      if (!user) return false
      
      const userLevel = user.organization_level || 5
      const userOrgId = user.organization_id || user.school_id
      
      // 学校级用户只能查看自己学校的数据
      if (userLevel === 5) {
        return userOrgId === schoolId
      }
      
      // 上级管理员可以查看下级学校数据（具体逻辑需要根据学校的管理层级判断）
      return userLevel <= 3
    },
    
    // 检查是否可以配置指定学校
    canConfigureSchool(schoolId: number): boolean {
      const user = authStore.user
      if (!user) return false
      
      const userLevel = user.organization_level || 5
      const userOrgId = user.organization_id || user.school_id
      
      // 学校级用户只能配置自己的学校
      if (userLevel === 5) {
        return userOrgId === schoolId && permissions.value.canConfig
      }
      
      // 上级管理员可以指定下级学校
      return userLevel <= 3 && permissions.value.canAssign
    },
    
    // 检查是否可以指定学校目录
    canAssignSchoolCatalog(schoolId: number): boolean {
      const user = authStore.user
      if (!user) return false
      
      const userLevel = user.organization_level || 5
      
      // 只有上级管理员可以指定
      return userLevel <= 3 && permissions.value.canAssign
    }
  }

  // 功能权限检查
  const featurePermissions = {
    // 检查是否可以使用批量操作
    canUseBatchOperations(): boolean {
      return permissions.value.canAssign
    },
    
    // 检查是否可以导出数据
    canExportData(): boolean {
      return permissions.value.canViewStats
    },
    
    // 检查是否可以重新计算完成率
    canRecalculateCompletion(): boolean {
      return permissions.value.canManageBaseline
    },
    
    // 检查是否可以查看配置历史
    canViewConfigHistory(): boolean {
      return hasAnyPermission([
        'school_experiment_catalog.view',
        'school_experiment_catalog.config',
        'school_experiment_catalog.assign'
      ])
    }
  }

  return {
    // 基础权限
    hasPermission,
    hasAnyPermission,
    
    // 权限对象
    permissions,
    menuPermissions,
    buttonPermissions,
    userLevelPermissions,
    
    // 权限检查函数
    dataPermissions,
    featurePermissions
  }
}
