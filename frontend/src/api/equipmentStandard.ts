import request from './request'

// 设备配备标准数据类型
export interface EquipmentStandard {
  id: number
  name: string
  code: string
  authority_type: number
  authority_type_text: string
  stage: number
  stage_text: string
  subject_code: string
  subject_name: string
  description?: string
  equipment_list: EquipmentCategory[]
  version: string
  effective_date: string
  expiry_date?: string
  status: number
  status_text: string
  created_at: string
  updated_at: string
}

// 设备分类
export interface EquipmentCategory {
  category: string
  items: EquipmentItem[]
}

// 设备项目
export interface EquipmentItem {
  name: string
  specification: string
  quantity: number
  unit: string
}

// 学科信息
export interface Subject {
  code: string
  name: string
  stages: number[]
}

// 配备标准列表查询参数
export interface EquipmentStandardListParams {
  page?: number
  per_page?: number
  authority_type?: number
  stage?: number
  subject_code?: string
  status?: number
  effective_only?: boolean
  search?: string
}

// 配备标准表单数据
export interface EquipmentStandardForm {
  name: string
  code: string
  authority_type: number
  stage: number
  subject_code: string
  subject_name: string
  description?: string
  equipment_list: EquipmentCategory[]
  version: string
  effective_date: string
  expiry_date?: string
  status: number
}

/**
 * 获取配备标准列表
 */
export const getEquipmentStandardsApi = (params?: EquipmentStandardListParams) => {
  return request.get('/equipment-standards', { params })
}

/**
 * 获取配备标准详情
 */
export const getEquipmentStandardApi = (id: number) => {
  return request.get(`/equipment-standards/${id}`)
}

/**
 * 创建配备标准
 */
export const createEquipmentStandardApi = (data: EquipmentStandardForm) => {
  return request.post('/equipment-standards', data)
}

/**
 * 更新配备标准
 */
export const updateEquipmentStandardApi = (id: number, data: EquipmentStandardForm) => {
  return request.put(`/equipment-standards/${id}`, data)
}

/**
 * 删除配备标准
 */
export const deleteEquipmentStandardApi = (id: number) => {
  return request.delete(`/equipment-standards/${id}`)
}

/**
 * 获取学科列表
 */
export const getSubjectsApi = () => {
  return request.get('/equipment-standards-subjects')
}

/**
 * 检查设备达标情况
 */
export const checkComplianceApi = (schoolId: number, standardId: number) => {
  return request.post('/equipment-standards/check-compliance', {
    school_id: schoolId,
    standard_id: standardId
  })
}

// 制定机构常量
export const AUTHORITY_TYPES = {
  1: '教育部',
  2: '教育厅'
}

// 制定机构选项
export const AUTHORITY_TYPE_OPTIONS = [
  { label: '教育部', value: 1 },
  { label: '教育厅', value: 2 }
]

// 学段常量
export const STAGES = {
  1: '小学',
  2: '初中',
  3: '高中'
}

// 学段选项
export const STAGE_OPTIONS = [
  { label: '小学', value: 1 },
  { label: '初中', value: 2 },
  { label: '高中', value: 3 }
]

// 状态常量
export const EQUIPMENT_STANDARD_STATUS = {
  0: '禁用',
  1: '启用'
}

// 状态选项
export const EQUIPMENT_STANDARD_STATUS_OPTIONS = [
  { label: '启用', value: 1 },
  { label: '禁用', value: 0 }
]
