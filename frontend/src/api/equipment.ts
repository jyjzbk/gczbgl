import request from './request'

// 设备档案相关接口
export interface Equipment {
  id: number
  school_id: number
  laboratory_id?: number
  category_id: number
  name: string
  code?: string
  model?: string
  brand?: string
  supplier?: string
  supplier_phone?: string
  purchase_date?: string
  purchase_price?: number
  quantity: number
  unit: string
  warranty_period?: number
  service_life?: number
  funding_source?: string
  storage_location?: string
  manager_id?: number
  status: number // 1:正常 2:借出 3:维修 4:报废
  qr_code?: string
  remark?: string
  created_at: string
  updated_at: string
  category?: {
    id: number
    name: string
    code: string
  }
  school?: {
    id: number
    name: string
  }
}

export interface EquipmentListParams {
  page?: number
  per_page?: number
  school_id?: number
  category_id?: number
  status?: number
  condition?: number
  location?: string
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateEquipmentParams {
  school_id: number
  laboratory_id?: number
  category_id: number
  name: string
  code?: string
  model?: string
  brand?: string
  supplier?: string
  supplier_phone?: string
  purchase_date?: string
  purchase_price?: number
  quantity: number
  unit: string
  warranty_period?: number
  service_life?: number
  funding_source?: string
  storage_location?: string
  manager_id?: number
  status: number
  remark?: string
}

// 获取设备列表
export const getEquipmentsApi = (params: EquipmentListParams) => {
  return request.get('/equipments', { params })
}

// 按组织获取设备列表接口
export interface OrganizationEquipmentListParams {
  organization_id?: number
  organization_level?: number
  page?: number
  per_page?: number
  search?: string
  category_id?: number
  status?: number
  condition?: number
  location?: string
}

export const getOrganizationEquipmentsApi = (params: OrganizationEquipmentListParams) => {
  return request.get<{
    success: boolean
    data: {
      items: Equipment[]
      pagination: {
        current_page: number
        last_page: number
        per_page: number
        total: number
      }
    }
  }>('/organizations/equipments', { params })
}

// 创建设备
export const createEquipmentApi = (data: CreateEquipmentParams) => {
  return request.post('/equipments', data)
}

// 获取设备详情
export const getEquipmentApi = (id: number) => {
  return request.get(`/equipments/${id}`)
}

// 更新设备
export const updateEquipmentApi = (id: number, data: Partial<CreateEquipmentParams>) => {
  return request.put(`/equipments/${id}`, data)
}

// 删除设备
export const deleteEquipmentApi = (id: number) => {
  return request.delete(`/equipments/${id}`)
}

// 批量导入设备
export const batchImportEquipmentsApi = (data: { equipments: CreateEquipmentParams[] }) => {
  return request.post('/equipments/batch-import', data)
}

// 生成设备二维码
export const generateEquipmentQRCodeApi = (id: number) => {
  return request.post(`/equipments/${id}/qrcode`)
}

// 批量生成二维码
export const batchGenerateQRCodesApi = (ids: number[]) => {
  return request.post('/equipments/batch-qrcode', { ids })
}

// 设备借用相关接口
export interface EquipmentBorrow {
  id: number
  equipment_id: number
  borrower_id: number
  borrower_name: string
  borrower_phone: string
  borrow_date: string
  expected_return_date: string
  actual_return_date?: string
  purpose: string
  status: number // 1:申请中 2:已批准 3:已借出 4:已归还 5:已拒绝 6:逾期
  remark?: string
  created_at: string
  updated_at: string
  equipment?: Equipment
  borrower?: {
    id: number
    real_name: string
    phone: string
  }
}

export interface EquipmentBorrowListParams {
  page?: number
  per_page?: number
  equipment_id?: number
  borrower_id?: number
  status?: number
  start_date?: string
  end_date?: string
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateEquipmentBorrowParams {
  equipment_id: number
  borrower_id: number
  borrower_name: string
  borrower_phone: string
  borrow_date: string
  expected_return_date: string
  purpose: string
  remark?: string
}

// 批量设备借用参数
export interface CreateBatchEquipmentBorrowParams {
  equipment_ids: number[]
  quantities: number[]
  borrower_id: number
  borrow_date: string
  expected_return_date: string
  purpose: string
  remark?: string
}

// 获取设备借用列表
export const getEquipmentBorrowsApi = (params: EquipmentBorrowListParams) => {
  return request.get('/equipment-borrows', { params })
}

// 创建设备借用申请
export const createEquipmentBorrowApi = (data: CreateEquipmentBorrowParams) => {
  return request.post('/equipment-borrows', data)
}

// 批量创建设备借用申请
export const createBatchEquipmentBorrowApi = (data: CreateBatchEquipmentBorrowParams) => {
  return request.post('/equipment-borrows', data)
}

// 获取设备借用详情
export const getEquipmentBorrowApi = (id: number) => {
  return request.get(`/equipment-borrows/${id}`)
}

// 更新设备借用
export const updateEquipmentBorrowApi = (id: number, data: Partial<CreateEquipmentBorrowParams>) => {
  return request.put(`/equipment-borrows/${id}`, data)
}

// 审批设备借用
export const reviewEquipmentBorrowApi = (id: number, data: { status: number; remark?: string }) => {
  return request.post(`/equipment-borrows/${id}/review`, data)
}

// 归还设备
export const returnEquipmentApi = (id: number, data: { actual_return_date: string; remark?: string }) => {
  return request.post(`/equipment-borrows/${id}/return`, data)
}

// 检查设备可用性
export const checkEquipmentAvailabilityApi = (equipment_id: number, start_date: string, end_date: string) => {
  return request.get(`/equipments/${equipment_id}/availability`, {
    params: { start_date, end_date }
  })
}

// 设备维修相关接口
export interface EquipmentMaintenance {
  id: number
  equipment_id: number
  reporter_id: number
  reporter_name: string
  fault_description: string
  fault_type: number // 1:硬件故障 2:软件故障 3:使用损坏 4:自然老化
  priority: number // 1:低 2:中 3:高 4:紧急
  status: number // 1:待处理 2:处理中 3:已完成 4:已取消
  repair_start_date?: string
  repair_end_date?: string
  repair_cost?: number
  repair_description?: string
  parts_used?: string
  technician_id?: number
  technician_name?: string
  created_at: string
  updated_at: string
  equipment?: Equipment
  reporter?: {
    id: number
    real_name: string
    phone: string
  }
}

export interface EquipmentMaintenanceListParams {
  page?: number
  per_page?: number
  equipment_id?: number
  reporter_id?: number
  technician_id?: number
  status?: number
  fault_type?: number
  priority?: number
  start_date?: string
  end_date?: string
  search?: string
  sort_field?: string
  sort_order?: 'asc' | 'desc'
}

export interface CreateEquipmentMaintenanceParams {
  equipment_id: number
  reporter_id: number
  reporter_name: string
  fault_description: string
  fault_type: number
  priority: number
}

export interface UpdateEquipmentMaintenanceParams {
  status?: number
  repair_start_date?: string
  repair_end_date?: string
  repair_cost?: number
  repair_description?: string
  parts_used?: string
  technician_id?: number
  technician_name?: string
}

// 获取设备维修列表
export const getEquipmentMaintenancesApi = (params: EquipmentMaintenanceListParams) => {
  return request.get('/equipment-maintenances', { params })
}

// 创建设备维修申请
export const createEquipmentMaintenanceApi = (data: CreateEquipmentMaintenanceParams) => {
  return request.post('/equipment-maintenances', data)
}

// 获取设备维修详情
export const getEquipmentMaintenanceApi = (id: number) => {
  return request.get(`/equipment-maintenances/${id}`)
}

// 更新设备维修
export const updateEquipmentMaintenanceApi = (id: number, data: UpdateEquipmentMaintenanceParams) => {
  return request.put(`/equipment-maintenances/${id}`, data)
}

// 完成设备维修
export const completeEquipmentMaintenanceApi = (id: number, data: UpdateEquipmentMaintenanceParams) => {
  return request.post(`/equipment-maintenances/${id}/complete`, data)
}

// 设备分类相关接口
export interface EquipmentCategory {
  id: number
  name: string
  code: string
  parent_id?: number
  description?: string
  sort_order: number
  status: number
  created_at: string
  updated_at: string
  children?: EquipmentCategory[]
}

// 获取设备分类列表
export const getEquipmentCategoriesApi = (params?: { search?: string; status?: number; all?: boolean }) => {
  return request.get<{ data: EquipmentCategory[] | { items: EquipmentCategory[]; pagination: any } }>('/equipment-categories', { params })
}

// 创建设备分类
export const createEquipmentCategoryApi = (data: {
  name: string
  code: string
  parent_id?: number
  description?: string
  sort_order?: number
}) => {
  return request.post('/equipment-categories', data)
}

// 更新设备分类
export const updateEquipmentCategoryApi = (id: number, data: {
  name?: string
  code?: string
  parent_id?: number
  description?: string
  sort_order?: number
  status?: number
}) => {
  return request.put(`/equipment-categories/${id}`, data)
}

// 删除设备分类
export const deleteEquipmentCategoryApi = (id: number) => {
  return request.delete(`/equipment-categories/${id}`)
}

// 上传设备照片
export const uploadEquipmentPhotoApi = (id: number, file: File) => {
  const formData = new FormData()
  formData.append('photo', file)

  return request.post(`/equipments/${id}/photos`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 删除设备照片
export const deleteEquipmentPhotoApi = (id: number, photoUrl: string) => {
  return request.delete(`/equipments/${id}/photos`, {
    data: { photo_url: photoUrl }
  })
}

// 获取设备统计数据
export const getEquipmentStatsApi = (params?: {
  school_id?: number
  start_date?: string
  end_date?: string
}) => {
  return request.get('/equipments/stats', { params })
}

// 导出设备数据
export const exportEquipmentsApi = (params: EquipmentListParams) => {
  return request.get('/equipments/export', {
    params,
    responseType: 'blob'
  })
}
