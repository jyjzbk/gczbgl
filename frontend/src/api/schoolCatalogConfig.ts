import request from './request'

// 学校实验目录配置接口类型定义
export interface SchoolExperimentCatalogConfig {
  id: number
  school_id: number
  config_type: 'selection' | 'assignment'
  source_level: number
  source_org_id: number
  source_org_name: string
  can_modify_selection: boolean
  can_delete_experiments: boolean
  configured_by: number
  configured_by_level: number
  configured_at: string
  config_reason?: string
  status: number
  effective_date?: string
  expiry_date?: string
  created_at: string
  updated_at: string
  // 关联数据
  school?: {
    id: number
    name: string
  }
  configuredBy?: {
    id: number
    name: string
  }
  config_type_name?: string
  source_level_name?: string
}

export interface ConfigureSchoolData {
  school_id: number
  config_type: 'selection' | 'assignment'
  source_level: number
  source_org_id: number
  source_org_name: string
  can_modify_selection?: boolean
  can_delete_experiments?: boolean
  config_reason?: string
  effective_date?: string
  expiry_date?: string
}

export interface BatchAssignData {
  school_ids: number[]
  source_level: number
  source_org_id: number
  source_org_name: string
  can_delete_experiments?: boolean
  config_reason?: string
}

export interface School {
  id: number
  name: string
  management_level: number
  province_id?: number
  city_id?: number
  district_id?: number
  config_status?: 'configured' | 'unconfigured'
  config_type_name?: string
  config?: SchoolExperimentCatalogConfig
}

export interface Organization {
  id: number
  name: string
  level: number
}

export interface ConfigStats {
  total_experiments: number
  by_subject: Record<string, number>
  by_grade: Record<string, number>
  by_semester: Record<string, number>
  by_type: Record<string, number>
}

export interface UserPermissions {
  user_level: number
  user_org_id: number
  can_create_province_catalog: boolean
  can_create_city_catalog: boolean
  can_create_county_catalog: boolean
  can_configure_school?: boolean
  can_assign_school?: boolean
  can_delete_experiments?: boolean
  can_view_completion_stats?: boolean
  available_catalog_levels?: number[]
  school_has_config_permission?: boolean
}

// API 函数
export const schoolCatalogConfigApi = {
  // 获取我的目录配置
  getMyConfig(schoolId?: number) {
    const params = schoolId ? { school_id: schoolId } : undefined
    return request.get('/school-catalog-config/my-config', { params })
  },

  // 配置学校目录
  configureSchool(data: ConfigureSchoolData) {
    return request.post('/school-catalog-config/configure', data)
  },

  // 获取下级学校列表
  getSubordinateSchools(params?: {
    management_level?: number
    region_id?: number
    config_status?: 'all' | 'configured' | 'unconfigured'
    page?: number
    per_page?: number
  }) {
    return request.get('/school-catalog-config/subordinate-schools', { params })
  },

  // 批量指定学校目录
  batchAssignSchools(data: BatchAssignData) {
    return request.post('/school-catalog-config/batch-assign', data)
  },

  // 获取可选择的组织列表
  getAvailableOrganizations(schoolId: number, level: number) {
    return request.get('/school-catalog-config/available-organizations', {
      params: { school_id: schoolId, level }
    })
  },

  // 获取配置历史
  getConfigHistory(schoolId: number) {
    return request.get('/school-catalog-config/config-history', {
      params: { school_id: schoolId }
    })
  }
}
