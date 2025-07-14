import request from './request'

// 角色接口
export interface Role {
  id: number
  name: string
  code: string
  description: string
  level: number
  status: number
  user_count?: number
  permission_count?: number
  permissions?: string[]
  created_at: string
  updated_at: string
}

// 获取角色列表接口
export interface RoleListParams {
  page?: number
  per_page?: number
  search?: string
  level?: number
  status?: number
  all?: string  // 添加all参数，用于获取所有数据不分页
}

export interface RoleListResponse {
  success: boolean
  data: {
    data: Role[]
    current_page: number
    per_page: number
    total: number
    last_page: number
  }
}

export const getRoleListApi = (params?: RoleListParams) => {
  return request.get<RoleListResponse>('/roles', { params })
}

// 创建角色接口
export interface CreateRoleParams {
  name: string
  code: string
  description: string
  level: number
  permissions?: string[]
}

export const createRoleApi = (data: CreateRoleParams) => {
  return request.post('/roles', data)
}

// 更新角色接口
export interface UpdateRoleParams {
  name?: string
  description?: string
  level?: number
  status?: number
  permissions?: string[]
}

export const updateRoleApi = (id: number, data: UpdateRoleParams) => {
  return request.put(`/roles/${id}`, data)
}

// 删除角色接口
export const deleteRoleApi = (id: number) => {
  return request.delete(`/roles/${id}`)
}

// 获取单个角色详情
export const getRoleDetailApi = (id: number) => {
  return request.get(`/roles/${id}`)
}

// 权限接口
export interface Permission {
  id: string
  name: string
  code: string
  type?: string
  module?: string
  children?: Permission[]
}

// 获取权限列表接口
export interface PermissionListResponse {
  success: boolean
  data: Permission[]
}

export const getPermissionListApi = () => {
  return request.get<PermissionListResponse>('/permissions')
}

// 获取权限树接口
export const getPermissionTreeApi = () => {
  return request.get<PermissionListResponse>('/permissions/tree')
}

// 角色权限管理接口
export interface RolePermissionParams {
  permissions: string[]
}

export const updateRolePermissionsApi = (roleId: number, data: RolePermissionParams) => {
  return request.post(`/roles/${roleId}/permissions`, data)
}

export const getRolePermissionsApi = (roleId: number) => {
  return request.get(`/roles/${roleId}/permissions`)
}

// 分配角色权限
export const assignRolePermissionsApi = (roleId: number, permissions: any[]) => {
  return request.post(`/roles/${roleId}/permissions`, { permissions })
}
