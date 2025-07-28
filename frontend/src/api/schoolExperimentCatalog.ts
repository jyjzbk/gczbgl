import request from './request'

// 学校实验目录选择接口类型定义
export interface SchoolExperimentCatalogSelection {
  id: number
  school_id: number
  selected_level: 'province' | 'city' | 'county'
  selected_org_id: number
  selected_org_name: string
  can_delete_experiments: boolean
  selection_reason?: string
  selected_by: number
  selected_at: string
  created_at: string
  updated_at: string
  school?: {
    id: number
    name: string
  }
  selectedBy?: {
    id: number
    name: string
  }
  selected_level_name?: string
}

export interface SetSchoolSelectionData {
  school_id: number
  selected_level: 'province' | 'city' | 'county'
  selected_org_id: number
  selected_org_name: string
  can_delete_experiments?: boolean
  selection_reason?: string
}

export interface AvailableStandard {
  level: 'province' | 'city' | 'county'
  level_name: string
  org_id: number
  org_name: string
  catalog_count: number
  description: string
}

export interface ExperimentCatalogWithDeleteStatus {
  id: number
  name: string
  code: string
  type: number
  grade: number
  semester: number
  chapter?: string
  objective?: string
  materials?: string
  procedure?: string
  safety_notes?: string
  difficulty_level: number
  is_standard: boolean
  status: number
  is_deleted_by_school: boolean
  created_at: string
  updated_at: string
}

export interface DeleteCatalogData {
  school_id: number
  catalog_id: number
  delete_reason: string
}

export interface RestoreCatalogData {
  school_id: number
  catalog_id: number
}

export interface DeletedCatalogRecord {
  id: number
  catalog_id: number
  deleted_by_org_type: string
  deleted_by_org_id: number
  deleted_by_user_id: number
  delete_reason: string
  deleted_at: string
  restored_at?: string
  restored_by?: number
  catalog?: {
    id: number
    name: string
    code: string
    type: number
    grade: number
    semester: number
  }
  deletedByUser?: {
    id: number
    name: string
  }
}

// API 函数
export const schoolExperimentCatalogApi = {
  // 获取学校实验目录选择
  getSchoolSelection(schoolId?: number) {
    const params = schoolId ? { school_id: schoolId } : undefined
    return request.get('/school-experiment-catalog/selection', { params })
  },

  // 设置学校实验目录选择
  setSchoolSelection(data: SetSchoolSelectionData) {
    return request.post('/school-experiment-catalog/selection', data)
  },

  // 获取可选择的标准组织列表
  getAvailableStandards(schoolId?: number) {
    const params = schoolId ? { school_id: schoolId } : undefined
    return request.get('/school-experiment-catalog/available-standards', { params })
  },

  // 获取学校可用的实验目录
  getAvailableCatalogs(schoolId?: number) {
    const params = schoolId ? { school_id: schoolId } : undefined
    return request.get('/school-experiment-catalog/available-catalogs', { params })
  },

  // 删除实验目录
  deleteExperimentCatalog(data: DeleteCatalogData) {
    return request.post('/school-experiment-catalog/delete-catalog', data)
  },

  // 恢复实验目录
  restoreExperimentCatalog(data: RestoreCatalogData) {
    return request.post('/school-experiment-catalog/restore-catalog', data)
  },

  // 获取删除的实验目录列表
  getDeletedCatalogs(schoolId?: number, page?: number, perPage?: number) {
    const params = {
      school_id: schoolId,
      page,
      per_page: perPage
    }
    return request.get('/school-experiment-catalog/deleted-catalogs', { params })
  }
}

// 删除权限配置接口类型定义
export interface ExperimentCatalogDeletePermission {
  id: number
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  organization_name: string
  allow_school_delete: boolean
  require_delete_reason: boolean
  max_delete_percentage: number
  delete_rules?: string
  created_by: number
  is_active: boolean
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
  }
  organization_type_name?: string
}

export interface CreateDeletePermissionData {
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  organization_name: string
  allow_school_delete?: boolean
  require_delete_reason?: boolean
  max_delete_percentage: number
  delete_rules?: string
}

export interface UpdateDeletePermissionData {
  allow_school_delete?: boolean
  require_delete_reason?: boolean
  max_delete_percentage: number
  delete_rules?: string
}

export interface EffectivePermissionResult {
  allow_school_delete: boolean
  require_delete_reason: boolean
  max_delete_percentage: number
  delete_rules?: string
  source: {
    organization_type: string
    organization_id: number
    organization_name: string
    is_inherited: boolean
  }
}

export interface SchoolDeleteStatistic {
  school_id: number
  school_name: string
  total_catalogs: number
  deleted_catalogs: number
  delete_percentage: number
  selected_level: string
  can_delete_experiments: boolean
}

// 删除权限配置API
export const experimentCatalogDeletePermissionApi = {
  // 获取删除权限配置列表
  getList(params?: {
    page?: number
    per_page?: number
    organization_type?: string
    organization_id?: number
  }) {
    return request<{
      success: boolean
      data: {
        data: ExperimentCatalogDeletePermission[]
        current_page: number
        last_page: number
        per_page: number
        total: number
      }
    }>({
      url: '/experiment-catalog-delete-permission',
      method: 'get',
      params
    })
  },

  // 创建删除权限配置
  create(data: CreateDeletePermissionData) {
    return request<{
      success: boolean
      message: string
      data: ExperimentCatalogDeletePermission
    }>({
      url: '/experiment-catalog-delete-permission',
      method: 'post',
      data
    })
  },

  // 获取删除权限配置详情
  getDetail(id: number) {
    return request<{
      success: boolean
      data: ExperimentCatalogDeletePermission
    }>({
      url: `/experiment-catalog-delete-permission/${id}`,
      method: 'get'
    })
  },

  // 更新删除权限配置
  update(id: number, data: UpdateDeletePermissionData) {
    return request<{
      success: boolean
      message: string
      data: ExperimentCatalogDeletePermission
    }>({
      url: `/experiment-catalog-delete-permission/${id}`,
      method: 'put',
      data
    })
  },

  // 删除权限配置
  delete(id: number) {
    return request<{
      success: boolean
      message: string
    }>({
      url: `/experiment-catalog-delete-permission/${id}`,
      method: 'delete'
    })
  },

  // 获取有效权限配置
  getEffectivePermission(params: {
    organization_type: 'province' | 'city' | 'county'
    organization_id: number
  }) {
    return request<{
      success: boolean
      data: EffectivePermissionResult
    }>({
      url: '/experiment-catalog-delete-permission/effective-permission',
      method: 'post',
      data: params
    })
  },

  // 获取学校删除统计
  getSchoolDeleteStatistics(params: {
    organization_type: 'province' | 'city' | 'county'
    organization_id: number
  }) {
    return request<{
      success: boolean
      data: SchoolDeleteStatistic[]
    }>({
      url: '/experiment-catalog-delete-permission/school-delete-statistics',
      method: 'post',
      data: params
    })
  }
}

// 常量定义
export const SELECTION_LEVELS = [
  { value: 'province', label: '省级' },
  { value: 'city', label: '市级' },
  { value: 'county', label: '区县级' }
]

// 工具函数
export const getSelectionLevelName = (level: string): string => {
  const levelMap: Record<string, string> = {
    'province': '省级',
    'city': '市级',
    'county': '区县级'
  }
  return levelMap[level] || level
}

export const getExperimentTypeName = (type: number): string => {
  const typeMap: Record<number, string> = {
    1: '必做',
    2: '选做',
    3: '演示',
    4: '分组'
  }
  return typeMap[type] || '未知'
}
