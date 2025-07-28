import request from './request'

// 班级接口
export interface SchoolClass {
  id: number
  school_id: number
  name: string
  code: string
  grade: number
  class_number: number
  student_count: number
  head_teacher_id?: number
  classroom_location?: string
  status: number
  created_at: string
  updated_at: string
  school?: {
    id: number
    name: string
  }
  head_teacher?: {
    id: number
    real_name: string
  }
  grade_name?: string
  full_name?: string
}

// 获取班级列表接口
export interface SchoolClassListParams {
  page?: number
  per_page?: number
  search?: string
  school_id?: number
  grade?: number
  status?: number
  all?: string
}

export interface SchoolClassListResponse {
  success: boolean
  data: {
    data: SchoolClass[]
    current_page: number
    per_page: number
    total: number
    last_page: number
  } | SchoolClass[]
}

export const getSchoolClassListApi = (params?: SchoolClassListParams) => {
  return request.get<SchoolClassListResponse>('/school-classes', { params })
}

// 创建班级接口
export interface CreateSchoolClassParams {
  school_id: number
  grade: number
  class_number: number
  student_count?: number
  head_teacher_id?: number
  classroom_location?: string
}

export const createSchoolClassApi = (data: CreateSchoolClassParams) => {
  return request.post('/school-classes', data)
}

// 更新班级接口
export interface UpdateSchoolClassParams {
  grade?: number
  class_number?: number
  student_count?: number
  head_teacher_id?: number
  classroom_location?: string
  status?: number
}

export const updateSchoolClassApi = (id: number, data: UpdateSchoolClassParams) => {
  return request.put(`/school-classes/${id}`, data)
}

// 删除班级接口
export const deleteSchoolClassApi = (id: number) => {
  return request.delete(`/school-classes/${id}`)
}

// 获取单个班级详情
export const getSchoolClassDetailApi = (id: number) => {
  return request.get(`/school-classes/${id}`)
}

// 批量创建班级接口
export interface BatchCreateClassParams {
  school_id: number
  grades: {
    grade: number
    class_count: number
  }[]
}

export const batchCreateSchoolClassApi = (data: BatchCreateClassParams) => {
  return request.post('/school-classes/batch-create', data)
}
