import request from './request'

// 实验目录相关接口
export interface ExperimentCatalog {
  id: number
  subject_id: number
  name: string
  code: string
  type: number
  grade: number
  semester: number
  chapter?: string
  duration: number
  student_count: number
  objective?: string
  materials?: string
  procedure?: string
  safety_notes?: string
  difficulty_level: number
  is_standard: number
  status: number
  created_at: string
  updated_at: string
  subject?: {
    id: number
    name: string
    code: string
  }
}

export interface ExperimentCatalogListParams {
  page?: number
  per_page?: number
  subject_id?: number
  grade?: number
  semester?: number
  type?: number
  is_standard?: number
  status?: number
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateExperimentCatalogParams {
  subject_id: number
  name: string
  code: string
  type: number
  grade: number
  semester: number
  chapter?: string
  duration: number
  student_count: number
  objective?: string
  materials?: string
  procedure?: string
  safety_notes?: string
  difficulty_level: number
  is_standard: boolean
  status: boolean
}

// 获取实验目录列表
export const getExperimentCatalogsApi = (params: ExperimentCatalogListParams) => {
  return request.get('/experiment-catalogs', { params })
}

// 创建实验目录
export const createExperimentCatalogApi = (data: CreateExperimentCatalogParams) => {
  return request.post('/experiment-catalogs', data)
}

// 获取实验目录详情
export const getExperimentCatalogApi = (id: number) => {
  return request.get(`/experiment-catalogs/${id}`)
}

// 更新实验目录
export const updateExperimentCatalogApi = (id: number, data: Partial<CreateExperimentCatalogParams>) => {
  return request.put(`/experiment-catalogs/${id}`, data)
}

// 删除实验目录
export const deleteExperimentCatalogApi = (id: number) => {
  return request.delete(`/experiment-catalogs/${id}`)
}

// 批量导入实验目录
export const batchImportCatalogsApi = (data: { catalogs: CreateExperimentCatalogParams[] }) => {
  return request.post('/experiment-catalogs/batch-import', data)
}

// 实验预约相关接口
export interface ExperimentReservation {
  id: number
  school_id: number
  catalog_id: number
  laboratory_id: number
  teacher_id: number
  class_name: string
  student_count: number
  reservation_date: string
  start_time: string
  end_time: string
  status: number
  reviewer_id?: number
  reviewed_at?: string
  review_remark?: string
  remark?: string
  created_at: string
  updated_at: string
  catalog?: ExperimentCatalog
  laboratory?: {
    id: number
    name: string
    code: string
    capacity: number
  }
  teacher?: {
    id: number
    real_name: string
    username: string
  }
  reviewer?: {
    id: number
    real_name: string
    username: string
  }
}

export interface ExperimentReservationListParams {
  page?: number
  per_page?: number
  school_id?: number
  teacher_id?: number
  laboratory_id?: number
  status?: number
  start_date?: string
  end_date?: string
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateExperimentReservationParams {
  school_id: number
  catalog_id: number
  laboratory_id: number
  teacher_id: number
  class_name: string
  student_count: number
  reservation_date: string
  start_time: string
  end_time: string
  remark?: string
}

// 获取实验预约列表
export const getExperimentReservationsApi = (params: ExperimentReservationListParams) => {
  return request.get('/experiment-reservations', { params })
}

// 创建实验预约
export const createExperimentReservationApi = (data: CreateExperimentReservationParams) => {
  return request.post('/experiment-reservations', data)
}

// 获取实验预约详情
export const getExperimentReservationApi = (id: number) => {
  return request.get(`/experiment-reservations/${id}`)
}

// 更新实验预约
export const updateExperimentReservationApi = (id: number, data: Partial<CreateExperimentReservationParams>) => {
  return request.put(`/experiment-reservations/${id}`, data)
}

// 取消实验预约
export const cancelExperimentReservationApi = (id: number) => {
  return request.post(`/experiment-reservations/${id}/cancel`)
}

// 审核实验预约
export const reviewExperimentReservationApi = (id: number, data: { status: number; review_remark?: string }) => {
  return request.post(`/experiment-reservations/${id}/review`, data)
}

// 实验记录相关接口
export interface ExperimentRecord {
  id: number
  reservation_id?: number
  school_id: number
  catalog_id: number
  laboratory_id: number
  teacher_id: number
  class_name: string
  student_count: number
  start_time: string
  end_time?: string
  completion_rate: number
  quality_score: number
  photos?: string[]
  videos?: string[]
  summary?: string
  problems?: string
  suggestions?: string
  status: number
  created_at: string
  updated_at: string
  catalog?: ExperimentCatalog
  laboratory?: {
    id: number
    name: string
    code: string
  }
  teacher?: {
    id: number
    real_name: string
    username: string
  }
  reservation?: ExperimentReservation
}

export interface ExperimentRecordListParams {
  page?: number
  per_page?: number
  school_id?: number
  teacher_id?: number
  laboratory_id?: number
  status?: number
  start_date?: string
  end_date?: string
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateExperimentRecordParams {
  reservation_id?: number
  student_count: number
  start_time?: string
  remark?: string
}

export interface UpdateExperimentRecordParams {
  end_time?: string
  completion_rate?: number
  quality_score?: number
  photos?: string[]
  videos?: string[]
  summary?: string
  problems?: string
  suggestions?: string
}

// 获取实验记录列表
export const getExperimentRecordsApi = (params: ExperimentRecordListParams) => {
  return request.get('/experiment-records', { params })
}

// 开始实验记录
export const createExperimentRecordApi = (data: CreateExperimentRecordParams) => {
  return request.post('/experiment-records', data)
}

// 获取实验记录详情
export const getExperimentRecordApi = (id: number) => {
  return request.get(`/experiment-records/${id}`)
}

// 更新实验记录
export const updateExperimentRecordApi = (id: number, data: UpdateExperimentRecordParams) => {
  return request.put(`/experiment-records/${id}`, data)
}

// 完成实验记录
export const completeExperimentRecordApi = (id: number, data: UpdateExperimentRecordParams) => {
  return request.post(`/experiment-records/${id}/complete`, data)
}

// 上传实验照片
export const uploadExperimentPhotoApi = (id: number, file: File) => {
  const formData = new FormData()
  formData.append('photo', file)
  
  return request.post(`/experiment-records/${id}/photos`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 上传实验视频
export const uploadExperimentVideoApi = (id: number, file: File) => {
  const formData = new FormData()
  formData.append('video', file)
  
  return request.post(`/experiment-records/${id}/videos`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 获取学科列表（用于实验目录）
export interface Subject {
  id: number
  name: string
  code: string
  category: string
}

export const getSubjectsApi = (params?: { search?: string }) => {
  return request.get<{ data: Subject[] }>('/subjects', { params })
}

// 获取实验室列表（用于预约）
export interface Laboratory {
  id: number
  school_id: number
  name: string
  code: string
  type: string
  capacity: number
  location: string
  status: number
}

export const getLaboratoriesApi = (params?: { school_id?: number; search?: string }) => {
  return request.get<{ data: Laboratory[] }>('/laboratories', { params })
}
