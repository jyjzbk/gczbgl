import request from './request'

// 学校教师接口
export interface SchoolTeacher {
  id: number
  school_id: number
  user_id: number
  employee_number?: string
  subject?: string
  teaching_grades?: number[]
  title?: string
  education?: string
  join_date?: string
  status: number
  created_at: string
  updated_at: string
  school?: {
    id: number
    name: string
  }
  user?: {
    id: number
    username: string
    real_name: string
    email: string
    phone?: string
  }
  status_name?: string
  teaching_grade_names?: string[]
}

// 获取教师列表接口
export interface SchoolTeacherListParams {
  page?: number
  per_page?: number
  search?: string
  school_id?: number
  subject?: string
  status?: number
  all?: string
}

export interface SchoolTeacherListResponse {
  success: boolean
  data: {
    data: SchoolTeacher[]
    current_page: number
    per_page: number
    total: number
    last_page: number
  } | SchoolTeacher[]
}

export const getSchoolTeacherListApi = (params?: SchoolTeacherListParams) => {
  return request.get<SchoolTeacherListResponse>('/school-teachers', { params })
}

// 创建教师接口
export interface CreateSchoolTeacherParams {
  school_id: number
  user_id: number
  employee_number?: string
  subject?: string
  teaching_grades?: number[]
  title?: string
  education?: string
  join_date?: string
}

export const createSchoolTeacherApi = (data: CreateSchoolTeacherParams) => {
  return request.post('/school-teachers', data)
}

// 更新教师接口
export interface UpdateSchoolTeacherParams {
  employee_number?: string
  subject?: string
  teaching_grades?: number[]
  title?: string
  education?: string
  join_date?: string
  status?: number
}

export const updateSchoolTeacherApi = (id: number, data: UpdateSchoolTeacherParams) => {
  return request.put(`/school-teachers/${id}`, data)
}

// 删除教师接口
export const deleteSchoolTeacherApi = (id: number) => {
  return request.delete(`/school-teachers/${id}`)
}

// 获取单个教师详情
export const getSchoolTeacherDetailApi = (id: number) => {
  return request.get(`/school-teachers/${id}`)
}

// 获取可添加的用户列表
export interface AvailableUser {
  id: number
  username: string
  real_name: string
  email: string
}

export const getAvailableUsersApi = (params: { school_id: number; search?: string }) => {
  return request.get<{ success: boolean; data: AvailableUser[] }>('/school-teachers/available-users', { params })
}

// 批量导入教师接口
export interface BatchImportTeacherParams {
  school_id: number
  teachers: {
    user_id: number
    employee_number?: string
    subject?: string
    teaching_grades?: number[]
    title?: string
    education?: string
    join_date?: string
  }[]
}

export const batchImportSchoolTeacherApi = (data: BatchImportTeacherParams) => {
  return request.post('/school-teachers/batch-import', data)
}
