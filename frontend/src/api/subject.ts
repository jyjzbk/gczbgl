import request from './request'

// 学科数据类型
export interface Subject {
  id: number
  name: string
  code: string
  type: number
  type_name: string
  stage: number
  stage_name: string
  sort_order: number
  status: number
  created_at: string
  updated_at: string
}

// 学科列表查询参数
export interface SubjectListParams {
  page?: number
  per_page?: number
  type?: number
  stage?: number
  status?: number
  search?: string
  all?: boolean
}

// 创建学科参数
export interface CreateSubjectParams {
  name: string
  code: string
  type: number
  stage: number
  sort_order?: number
  status?: number
}

// 更新学科参数
export interface UpdateSubjectParams {
  name?: string
  code?: string
  type?: number
  stage?: number
  sort_order?: number
  status?: number
}

// 获取学科列表
export const getSubjectListApi = (params: SubjectListParams) => {
  return request.get<{
    data: Subject[]
    pagination?: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }>('/subjects', { params })
}

// 获取学科详情
export const getSubjectDetailApi = (id: number) => {
  return request.get<{ data: Subject }>(`/subjects/${id}`)
}

// 创建学科
export const createSubjectApi = (data: CreateSubjectParams) => {
  return request.post<{ data: Subject }>('/subjects', data)
}

// 更新学科
export const updateSubjectApi = (id: number, data: UpdateSubjectParams) => {
  return request.put<{ data: Subject }>(`/subjects/${id}`, data)
}

// 删除学科
export const deleteSubjectApi = (id: number) => {
  return request.delete(`/subjects/${id}`)
}

// 获取学科选项（用于下拉框）
export const getSubjectOptionsApi = (params?: { stage?: number }) => {
  return request.get<{ data: Array<{ id: number; name: string; code: string; type: number; stage: number }> }>('/subjects-options', { params })
}

// 学科类型常量
export const SUBJECT_TYPES = {
  1: '理科',
  2: '文科',
  3: '综合'
}

// 学段常量
export const SUBJECT_STAGES = {
  1: '小学',
  2: '初中',
  3: '高中'
}
