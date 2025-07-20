import request from './request'

// 组织相关接口

// 组织节点类型定义
export interface OrganizationNode {
  id: number
  code: string
  name: string
  level: number
  parent_id: number | null
  sort_order: number
  status: number
  type?: string // 'school' 表示学校节点
  children?: OrganizationNode[]
  user_count?: number
  school_count?: number
  equipment_count?: number
  laboratory_count?: number
}

// 组织统计信息
export interface OrganizationStats {
  total_users: number
  total_schools: number
  total_equipments: number
  total_laboratories: number
  active_users: number
  disabled_users: number
}

/**
 * 获取可管理的组织列表
 */
export const getManageableOrganizationsApi = (params: { level: number }) => {
  return request({
    url: '/organizations/manageable',
    method: 'get',
    params
  })
}

/**
 * 获取可管理的学校列表
 */
export const getManageableSchoolsApi = () => {
  return request({
    url: '/organizations/manageable-schools',
    method: 'get'
  })
}

/**
 * 获取组织下的学校列表
 */
export const getOrganizationSchoolsApi = () => {
  return request({
    url: '/organizations/schools',
    method: 'get'
  })
}

/**
 * 获取组织树结构
 */
export const getOrganizationTreeApi = () => {
  return request<{
    success: boolean
    data: OrganizationNode[]
  }>({
    url: '/organizations/tree',
    method: 'get'
  })
}

/**
 * 获取组织统计信息
 */
export const getOrganizationStatsApi = (organizationId?: number, organizationType?: string) => {
  const params: any = {}
  if (organizationId) {
    params.organization_id = organizationId
  }
  if (organizationType) {
    params.organization_type = organizationType
  }

  return request<{
    success: boolean
    data: OrganizationStats
  }>({
    url: '/organizations/stats',
    method: 'get',
    params
  })
}

/**
 * 获取组织下的用户列表（支持分页）
 */
export const getOrganizationUsersApi = (params: {
  organization_id?: number
  organization_level?: number
  page?: number
  per_page?: number
  search?: string
  role?: string
  status?: string
}) => {
  return request({
    url: '/organizations/users',
    method: 'get',
    params
  })
}

/**
 * 获取可编辑的组织列表
 */
export const getEditableOrganizationsApi = () => {
  return request({
    url: '/organizations/editable',
    method: 'get'
  })
}

/**
 * 更新组织信息
 */
export const updateOrganizationApi = (type: string, id: number, data: any) => {
  return request({
    url: `/organizations/${type}/${id}`,
    method: 'put',
    data
  })
}
