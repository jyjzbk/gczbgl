/**
 * 权限控制工具类
 * 用于实验目录分级管理的权限控制
 */

import { useAuthStore } from '@/stores/auth'

// 管理级别枚举
export enum ManagementLevel {
  PROVINCE = 1,  // 省级
  CITY = 2,      // 市级
  COUNTY = 3,    // 区县级
  DISTRICT = 4,  // 学区级
  SCHOOL = 5     // 学校级
}

// 权限类型枚举
export enum PermissionType {
  VIEW = 'view',
  CREATE = 'create',
  EDIT = 'edit',
  DELETE = 'delete',
  COPY = 'copy',
  INHERIT = 'inherit',
  MANAGE_EQUIPMENT = 'manage_equipment'
}

// 实验目录权限接口
export interface ExperimentCatalogPermission {
  canView: boolean
  canCreate: boolean
  canEdit: boolean
  canDelete: boolean
  canCopy: boolean
  canInherit: boolean
  canManageEquipment: boolean
  canRestore: boolean
}

/**
 * 权限控制类
 */
export class PermissionController {
  private authStore = useAuthStore()

  /**
   * 获取当前用户的管理级别
   */
  getCurrentUserLevel(): ManagementLevel {
    const user = this.authStore.userInfo
    if (!user) return ManagementLevel.SCHOOL

    // 根据用户的organization_level字段确定管理级别
    if (user.organization_level) {
      return user.organization_level as ManagementLevel
    }

    // 兼容旧的organization_type字段判断
    if (user.organization_type === 'province') return ManagementLevel.PROVINCE
    if (user.organization_type === 'city') return ManagementLevel.CITY
    if (user.organization_type === 'county') return ManagementLevel.COUNTY
    if (user.organization_type === 'district') return ManagementLevel.DISTRICT
    return ManagementLevel.SCHOOL
  }

  /**
   * 获取当前用户的组织ID
   */
  getCurrentUserOrgId(): number {
    const user = this.authStore.userInfo
    return user?.organization_id || 0
  }

  /**
   * 获取当前用户的组织类型
   */
  getCurrentUserOrgType(): string {
    const user = this.authStore.userInfo
    return user?.organization_type || 'school'
  }

  /**
   * 检查是否可以查看实验目录
   */
  canViewCatalog(catalog: any): boolean {
    // 所有用户都能查看各级管理员创建的实验目录
    return true
  }

  /**
   * 检查是否可以创建实验目录
   */
  canCreateCatalog(): boolean {
    // 所有级别都可以创建实验目录
    return true
  }

  /**
   * 检查是否可以编辑实验目录
   */
  canEditCatalog(catalog: any): boolean {
    const userLevel = this.getCurrentUserLevel()
    const userOrgId = this.getCurrentUserOrgId()

    // 各级管理员只能编辑本级的实验目录
    if (catalog.management_level === userLevel) {
      // 如果是本级的实验目录，检查是否是同一组织创建的
      if (catalog.created_by_org_id === userOrgId) {
        return true // 编辑本级同组织的实验目录
      }
    }

    // 上级管理员可以编辑下级实验目录
    if (catalog.management_level > userLevel) {
      return true // 上级可以编辑下级的实验目录
    }

    return false
  }

  /**
   * 检查是否可以删除实验目录
   */
  canDeleteCatalog(catalog: any): boolean {
    const userLevel = this.getCurrentUserLevel()
    const userOrgId = this.getCurrentUserOrgId()

    // 各级管理员只能删除本级的实验目录
    if (catalog.management_level === userLevel) {
      // 如果是本级的实验目录，检查是否是同一组织创建的
      if (catalog.created_by_org_id === userOrgId) {
        return true // 删除本级同组织的实验目录
      }
    }

    // 上级管理员可以删除下级实验目录
    if (catalog.management_level > userLevel) {
      return true // 上级可以删除下级的实验目录
    }

    return false
  }

  /**
   * 检查是否可以复制实验目录
   */
  canCopyCatalog(catalog: any): boolean {
    // 首先检查权限代码
    if (this.authStore.hasPermission('experiment.catalog.copy')) {
      // 如果有权限代码，则按照权限代码执行
      return this.canViewCatalog(catalog)
    }

    // 兼容旧的逻辑：用户可以复制所有可见的实验目录
    return this.canViewCatalog(catalog)
  }

  /**
   * 基于权限代码检查是否可以查看实验目录
   */
  canViewCatalogByPermission(): boolean {
    return this.authStore.hasPermission('experiment.catalog.view') ||
           this.authStore.hasPermission('experiment.catalog')
  }

  /**
   * 基于权限代码检查是否可以创建实验目录
   */
  canCreateCatalogByPermission(): boolean {
    return this.authStore.hasPermission('experiment.catalog.create')
  }

  /**
   * 基于权限代码检查是否可以编辑实验目录
   */
  canEditCatalogByPermission(): boolean {
    return this.authStore.hasPermission('experiment.catalog.edit')
  }

  /**
   * 基于权限代码检查是否可以删除实验目录
   */
  canDeleteCatalogByPermission(): boolean {
    return this.authStore.hasPermission('experiment.catalog.delete')
  }

  /**
   * 基于权限代码检查是否可以复制实验目录
   */
  canCopyCatalogByPermission(): boolean {
    return this.authStore.hasPermission('experiment.catalog.copy')
  }

  /**
   * 基于权限代码检查是否可以管理级别权限
   */
  canManageLevelPermissions(): boolean {
    return this.authStore.hasPermission('experiment.catalog.manage_level')
  }

  /**
   * 检查是否可以继承实验目录配置
   */
  canInheritCatalog(catalog: any): boolean {
    const userLevel = this.getCurrentUserLevel()
    
    // 可以继承上级的实验目录配置
    return catalog.parent_catalog_id && catalog.management_level < userLevel
  }

  /**
   * 检查是否可以管理器材配置
   */
  canManageEquipment(catalog: any): boolean {
    return this.canEditCatalog(catalog)
  }

  /**
   * 检查是否可以恢复被删除的实验目录
   */
  canRestoreCatalog(catalog: any): boolean {
    const userLevel = this.getCurrentUserLevel()
    const userOrgId = this.getCurrentUserOrgId()
    
    // 可以恢复被下级删除的自己创建的实验目录
    return catalog.is_deleted_by_lower && 
           catalog.created_by_level === userLevel && 
           catalog.created_by_org_id === userOrgId
  }

  /**
   * 获取实验目录的完整权限信息
   */
  getCatalogPermissions(catalog: any): ExperimentCatalogPermission {
    return {
      canView: this.canViewCatalog(catalog),
      canCreate: this.canCreateCatalog(),
      canEdit: this.canEditCatalog(catalog),
      canDelete: this.canDeleteCatalog(catalog),
      canCopy: this.canCopyCatalog(catalog),
      canInherit: this.canInheritCatalog(catalog),
      canManageEquipment: this.canManageEquipment(catalog),
      canRestore: this.canRestoreCatalog(catalog)
    }
  }

  /**
   * 检查是否可以访问教材版本管理
   */
  canManageTextbookVersions(): boolean {
    const userLevel = this.getCurrentUserLevel()
    // 只有省级和市级可以管理教材版本
    return userLevel <= ManagementLevel.CITY
  }

  /**
   * 检查是否可以访问章节结构管理
   */
  canManageChapters(): boolean {
    const userLevel = this.getCurrentUserLevel()
    // 省级、市级、区县级可以管理章节结构
    return userLevel <= ManagementLevel.COUNTY
  }

  /**
   * 检查是否可以进行智能预约
   */
  canMakeReservation(): boolean {
    // 所有用户都可以进行预约
    return true
  }

  /**
   * 获取可见的管理级别选项
   */
  getVisibleManagementLevels(): Array<{ label: string; value: number }> {
    const userLevel = this.getCurrentUserLevel()
    const levels = [
      { label: '省级', value: ManagementLevel.PROVINCE },
      { label: '市级', value: ManagementLevel.CITY },
      { label: '区县级', value: ManagementLevel.COUNTY },
      { label: '学区级', value: ManagementLevel.DISTRICT },
      { label: '学校级', value: ManagementLevel.SCHOOL }
    ]

    // 所有用户都能看到所有级别的选项（用于筛选查看）
    return levels
  }

  /**
   * 获取管理级别标签
   */
  getManagementLevelLabel(level: number): string {
    const labels = {
      [ManagementLevel.PROVINCE]: '省级',
      [ManagementLevel.CITY]: '市级',
      [ManagementLevel.COUNTY]: '区县级',
      [ManagementLevel.DISTRICT]: '学区级',
      [ManagementLevel.SCHOOL]: '学校级'
    }
    return labels[level] || '未知'
  }

  /**
   * 获取管理级别标签类型（用于el-tag的type属性）
   */
  getManagementLevelTagType(level: number): string {
    const types = {
      [ManagementLevel.PROVINCE]: 'danger',
      [ManagementLevel.CITY]: 'warning',
      [ManagementLevel.COUNTY]: 'primary',
      [ManagementLevel.DISTRICT]: 'success',
      [ManagementLevel.SCHOOL]: 'info'
    }
    return types[level] || 'info'
  }
}

// 创建全局权限控制实例
export const permissionController = new PermissionController()

// 导出便捷方法
export const usePermission = () => {
  return permissionController
}
