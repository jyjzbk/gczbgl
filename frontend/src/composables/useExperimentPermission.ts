/**
 * 实验目录权限控制组合式函数
 */

import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { permissionController, type ExperimentCatalogPermission } from '@/utils/permission'

export function useExperimentPermission() {
  const authStore = useAuthStore()

  // 当前用户信息
  const currentUser = computed(() => authStore.userInfo)
  const currentUserLevel = computed(() => permissionController.getCurrentUserLevel())
  const currentUserOrgId = computed(() => permissionController.getCurrentUserOrgId())
  const currentUserOrgType = computed(() => permissionController.getCurrentUserOrgType())

  // 权限检查方法
  const canViewCatalog = (catalog: any): boolean => {
    return permissionController.canViewCatalog(catalog)
  }

  const canCreateCatalog = (): boolean => {
    return permissionController.canCreateCatalog()
  }

  const canEditCatalog = (catalog: any): boolean => {
    return permissionController.canEditCatalog(catalog)
  }

  const canDeleteCatalog = (catalog: any): boolean => {
    return permissionController.canDeleteCatalog(catalog)
  }

  const canCopyCatalog = (catalog: any): boolean => {
    return permissionController.canCopyCatalog(catalog)
  }

  const canInheritCatalog = (catalog: any): boolean => {
    return permissionController.canInheritCatalog(catalog)
  }

  const canManageEquipment = (catalog: any): boolean => {
    return permissionController.canManageEquipment(catalog)
  }

  const canRestoreCatalog = (catalog: any): boolean => {
    return permissionController.canRestoreCatalog(catalog)
  }

  const getCatalogPermissions = (catalog: any): ExperimentCatalogPermission => {
    return permissionController.getCatalogPermissions(catalog)
  }

  // 管理权限
  const canManageTextbookVersions = computed(() => {
    return permissionController.canManageTextbookVersions()
  })

  const canManageChapters = computed(() => {
    return permissionController.canManageChapters()
  })

  const canMakeReservation = computed(() => {
    return permissionController.canMakeReservation()
  })

  // 获取可见的管理级别选项
  const visibleManagementLevels = computed(() => {
    return permissionController.getVisibleManagementLevels()
  })

  // 工具方法
  const getManagementLevelLabel = (level: number): string => {
    return permissionController.getManagementLevelLabel(level)
  }

  const getManagementLevelTagType = (level: number): string => {
    return permissionController.getManagementLevelTagType(level)
  }

  // 过滤实验目录列表（只显示有权限查看的）
  const filterCatalogsByPermission = (catalogs: any[]): any[] => {
    return catalogs.filter(catalog => canViewCatalog(catalog))
  }

  // 检查是否为自己创建的实验目录
  const isOwnCatalog = (catalog: any): boolean => {
    return catalog.created_by_level === currentUserLevel.value && 
           catalog.created_by_org_id === currentUserOrgId.value
  }

  // 检查是否为上级创建的实验目录
  const isUpperLevelCatalog = (catalog: any): boolean => {
    return catalog.management_level < currentUserLevel.value
  }

  // 检查是否为下级创建的实验目录
  const isLowerLevelCatalog = (catalog: any): boolean => {
    return catalog.management_level > currentUserLevel.value
  }

  // 获取操作提示文本
  const getOperationTip = (catalog: any, operation: string): string => {
    switch (operation) {
      case 'edit':
        if (!canEditCatalog(catalog)) {
          return isUpperLevelCatalog(catalog) 
            ? '上级创建的实验目录不能直接编辑，可以复制后修改'
            : '只能编辑自己创建的实验目录'
        }
        break
      case 'delete':
        if (!canDeleteCatalog(catalog)) {
          return '只能删除自己创建的实验目录或标记删除上级的实验目录'
        }
        if (isUpperLevelCatalog(catalog)) {
          return '此操作将标记删除上级实验目录，不会影响上级统计'
        }
        break
      case 'copy':
        if (!canCopyCatalog(catalog)) {
          return '只能复制上级的实验目录'
        }
        break
      case 'inherit':
        if (!canInheritCatalog(catalog)) {
          return '只能继承有上级配置的实验目录'
        }
        break
    }
    return ''
  }

  // 获取删除操作的确认文本
  const getDeleteConfirmText = (catalog: any): string => {
    if (isOwnCatalog(catalog)) {
      return `确定要删除实验目录"${catalog.name}"吗？此操作不可恢复。`
    } else if (isUpperLevelCatalog(catalog)) {
      return `确定要标记删除实验目录"${catalog.name}"吗？此操作不会影响上级统计，可以恢复。`
    }
    return `确定要删除实验目录"${catalog.name}"吗？`
  }

  // 获取删除操作的按钮文本
  const getDeleteButtonText = (catalog: any): string => {
    if (isOwnCatalog(catalog)) {
      return '删除'
    } else if (isUpperLevelCatalog(catalog)) {
      return '标记删除'
    }
    return '删除'
  }

  // 获取实验目录的状态标签
  const getCatalogStatusTag = (catalog: any) => {
    if (catalog.is_deleted_by_lower) {
      return {
        text: '已删除',
        type: 'warning'
      }
    }
    if (!catalog.status) {
      return {
        text: '禁用',
        type: 'danger'
      }
    }
    return {
      text: '正常',
      type: 'success'
    }
  }

  // 检查是否可以显示某个功能模块
  const canAccessModule = (module: string): boolean => {
    switch (module) {
      case 'textbook-versions':
        return canManageTextbookVersions.value
      case 'textbook-chapters':
        return canManageChapters.value
      case 'experiment-catalogs':
        return true // 所有用户都可以访问实验目录
      case 'smart-reservation':
        return canMakeReservation.value
      default:
        return true
    }
  }

  return {
    // 用户信息
    currentUser,
    currentUserLevel,
    currentUserOrgId,
    currentUserOrgType,

    // 权限检查
    canViewCatalog,
    canCreateCatalog,
    canEditCatalog,
    canDeleteCatalog,
    canCopyCatalog,
    canInheritCatalog,
    canManageEquipment,
    canRestoreCatalog,
    getCatalogPermissions,

    // 管理权限
    canManageTextbookVersions,
    canManageChapters,
    canMakeReservation,

    // 工具方法
    visibleManagementLevels,
    getManagementLevelLabel,
    getManagementLevelTagType,
    filterCatalogsByPermission,
    isOwnCatalog,
    isUpperLevelCatalog,
    isLowerLevelCatalog,
    getOperationTip,
    getDeleteConfirmText,
    getDeleteButtonText,
    getCatalogStatusTag,
    canAccessModule
  }
}
