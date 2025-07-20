import request from './request'

// 实验室数据类型
export interface Laboratory {
  id: number
  school_id: number
  name: string
  code: string
  type: number
  type_id?: number
  type_name: string
  laboratory_type?: {
    id: number
    name: string
    code: string
    icon?: string
    color?: string
  }
  location: string
  area: number
  capacity: number
  manager_id?: number
  manager_name?: string
  equipment_list?: string
  safety_rules?: string
  status: number
  created_at: string
  updated_at: string
  school?: {
    id: number
    name: string
  }
  manager?: {
    id: number
    real_name: string
  }
}

// 实验室列表查询参数
export interface LaboratoryListParams {
  page?: number
  per_page?: number
  school_id?: number
  type?: number
  status?: number
  search?: string
}

// 创建实验室参数
export interface CreateLaboratoryParams {
  school_id: number
  name: string
  code: string
  type?: number // 兼容旧数据
  type_id?: number // 新的类型关联
  location?: string
  area?: number
  capacity: number
  manager_id?: number
  equipment_list?: string
  safety_rules?: string
  status?: number
}

// 更新实验室参数
export interface UpdateLaboratoryParams {
  school_id?: number
  name?: string
  code?: string
  type?: number // 兼容旧数据
  type_id?: number // 新的类型关联
  location?: string
  area?: number
  capacity?: number
  manager_id?: number
  equipment_list?: string
  safety_rules?: string
  status?: number
}

// 获取实验室列表
export const getLaboratoryListApi = (params: LaboratoryListParams) => {
  return request.get<{
    data: Laboratory[]
    pagination: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }>('/laboratories', { params })
}

// 按组织获取实验室列表接口
export interface OrganizationLaboratoryListParams {
  organization_id?: number
  organization_level?: number
  page?: number
  per_page?: number
  search?: string
  type?: number
  status?: number
}

export const getOrganizationLaboratoriesApi = (params: OrganizationLaboratoryListParams) => {
  return request.get<{
    success: boolean
    data: {
      items: Laboratory[]
      pagination: {
        current_page: number
        last_page: number
        per_page: number
        total: number
      }
    }
  }>('/organizations/laboratories', { params })
}

// 获取实验室详情
export const getLaboratoryDetailApi = (id: number) => {
  return request.get<{ data: Laboratory }>(`/laboratories/${id}`)
}

// 创建实验室
export const createLaboratoryApi = (data: CreateLaboratoryParams) => {
  return request.post<{ data: Laboratory }>('/laboratories', data)
}

// 更新实验室
export const updateLaboratoryApi = (id: number, data: UpdateLaboratoryParams) => {
  return request.put<{ data: Laboratory }>(`/laboratories/${id}`, data)
}

// 删除实验室
export const deleteLaboratoryApi = (id: number) => {
  return request.delete(`/laboratories/${id}`)
}

// 获取实验室选项（用于下拉框）
export const getLaboratoryOptionsApi = (params?: { school_id?: number; type?: number }) => {
  return request.get<{ data: Array<{ id: number; name: string; code: string; type: number; capacity: number }> }>('/laboratories-options', { params })
}

// 实验室类型常量
export const LABORATORY_TYPES = {
  1: '物理实验室',
  2: '化学实验室',
  3: '生物实验室',
  4: '综合实验室'
}

// 实验室状态常量
export const LABORATORY_STATUS = {
  0: '维护中',
  1: '正常'
}

// 实验室API对象（为了兼容组件中的使用方式）
export const laboratoryApi = {
  getList: getLaboratoryListApi,
  create: createLaboratoryApi,
  get: getLaboratoryDetailApi,
  update: updateLaboratoryApi,
  delete: deleteLaboratoryApi,
  getOptions: getLaboratoryOptionsApi
}
