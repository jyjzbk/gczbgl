import request from './request'

// 学校数据类型
export interface School {
  id: number
  name: string
  code: string
  type: number
  type_name: string
  level: number
  level_name: string
  region_id: number
  region_name?: string
  address: string
  contact_person: string
  contact_phone: string
  student_count: number
  class_count: number
  teacher_count: number
  status: number
  created_at: string
  updated_at: string
}

// 学校列表查询参数
export interface SchoolListParams {
  page?: number
  per_page?: number
  search?: string
  type?: number
  level?: number
  region_id?: number
  status?: number
}

// 创建学校参数
export interface CreateSchoolParams {
  name: string
  code: string
  type: number
  level: number
  region_id: number
  address: string
  contact_person: string
  contact_phone: string
  student_count?: number
  class_count?: number
  teacher_count?: number
  status?: number
}

// 更新学校参数
export interface UpdateSchoolParams {
  name?: string
  code?: string
  type?: number
  level?: number
  region_id?: number
  address?: string
  contact_person?: string
  contact_phone?: string
  student_count?: number
  class_count?: number
  teacher_count?: number
  status?: number
}

// 获取学校列表
export const getSchoolListApi = (params: SchoolListParams) => {
  return request.get<{
    data: School[]
    pagination: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }>('/schools', { params })
}

// 按组织获取学校列表接口
export interface OrganizationSchoolListParams {
  organization_id?: number
  organization_level?: number
  page?: number
  per_page?: number
  search?: string
  type?: number
  status?: number
}

export const getOrganizationSchoolsApi = (params: OrganizationSchoolListParams) => {
  return request.get<{
    success: boolean
    data: {
      items: School[]
      pagination: {
        current_page: number
        last_page: number
        per_page: number
        total: number
      }
    }
  }>('/organizations/schools', { params })
}

// 获取学校详情
export const getSchoolDetailApi = (id: number) => {
  return request.get<{ data: School }>(`/schools/${id}`)
}

// 创建学校
export const createSchoolApi = (data: CreateSchoolParams) => {
  return request.post<{ data: School }>('/schools', data)
}

// 更新学校
export const updateSchoolApi = (id: number, data: UpdateSchoolParams) => {
  return request.put<{ data: School }>(`/schools/${id}`, data)
}

// 删除学校
export const deleteSchoolApi = (id: number) => {
  return request.delete(`/schools/${id}`)
}

// 获取学校选项（用于下拉框）
export const getSchoolOptionsApi = (params?: { search?: string; region_id?: number }) => {
  return request.get<{ data: Array<{ id: number; name: string; code: string }> }>('/schools/options', { params })
}

// 学校类型常量
export const SCHOOL_TYPES = {
  1: '小学',
  2: '初中', 
  3: '高中',
  4: '九年一贯制'
}

// 管理级别常量
export const SCHOOL_LEVELS = {
  1: '省直',
  2: '市直',
  3: '区县直',
  4: '学区'
}
