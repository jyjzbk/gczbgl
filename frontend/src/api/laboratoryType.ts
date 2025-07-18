import request from './request'

// 实验室类型数据类型
export interface LaboratoryType {
  id: number
  name: string
  code: string
  description?: string
  icon?: string
  color?: string
  sort_order: number
  status: number
  status_text: string
  created_at: string
  updated_at: string
}

// 实验室类型列表查询参数
export interface LaboratoryTypeListParams {
  page?: number
  per_page?: number
  status?: number
  search?: string
}

// 实验室类型表单数据
export interface LaboratoryTypeForm {
  name: string
  code: string
  description?: string
  icon?: string
  color?: string
  sort_order: number
  status: number
}

// 排序更新数据
export interface SortUpdateItem {
  id: number
  sort_order: number
}

/**
 * 获取实验室类型列表
 */
export const getLaboratoryTypesApi = (params?: LaboratoryTypeListParams) => {
  return request.get('/laboratory-types', { params })
}

/**
 * 获取实验室类型详情
 */
export const getLaboratoryTypeApi = (id: number) => {
  return request.get(`/laboratory-types/${id}`)
}

/**
 * 创建实验室类型
 */
export const createLaboratoryTypeApi = (data: LaboratoryTypeForm) => {
  return request.post('/laboratory-types', data)
}

/**
 * 更新实验室类型
 */
export const updateLaboratoryTypeApi = (id: number, data: LaboratoryTypeForm) => {
  return request.put(`/laboratory-types/${id}`, data)
}

/**
 * 删除实验室类型
 */
export const deleteLaboratoryTypeApi = (id: number) => {
  return request.delete(`/laboratory-types/${id}`)
}

/**
 * 批量更新排序
 */
export const updateLaboratoryTypeSortApi = (items: SortUpdateItem[]) => {
  return request.post('/laboratory-types/update-sort', { items })
}

// 实验室类型状态常量
export const LABORATORY_TYPE_STATUS = {
  0: '禁用',
  1: '启用'
}

// 实验室类型状态选项
export const LABORATORY_TYPE_STATUS_OPTIONS = [
  { label: '启用', value: 1 },
  { label: '禁用', value: 0 }
]
