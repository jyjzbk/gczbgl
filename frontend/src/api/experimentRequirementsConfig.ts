import request from './request'

// 实验要求配置接口类型定义
export interface ExperimentRequirementsConfig {
  id: number
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  experiment_type: '分组实验' | '演示实验'
  min_images: number
  max_images: number
  min_videos: number
  max_videos: number
  is_inherited: boolean
  created_by: number
  description?: string
  is_active: boolean
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
  }
  effective_config?: {
    min_images: number
    max_images: number
    min_videos: number
    max_videos: number
    is_inherited: boolean
  }
  organization_type_name?: string
}

export interface CreateExperimentRequirementsConfigData {
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  experiment_type: '分组实验' | '演示实验'
  min_images: number
  max_images: number
  min_videos: number
  max_videos: number
  is_inherited?: boolean
  description?: string
}

export interface UpdateExperimentRequirementsConfigData {
  min_images: number
  max_images: number
  min_videos: number
  max_videos: number
  is_inherited?: boolean
  description?: string
}

export interface ExperimentRequirementsConfigListParams {
  page?: number
  per_page?: number
  organization_type?: string
  organization_id?: number
  experiment_type?: string
}

export interface EffectiveConfigParams {
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  experiment_type: '分组实验' | '演示实验'
}

export interface EffectiveConfigResult {
  min_images: number
  max_images: number
  min_videos: number
  max_videos: number
  source: {
    organization_type: string
    organization_id: number
    is_inherited: boolean
  }
}

export interface OrganizationOption {
  value: number
  label: string
}

// API 函数
export const experimentRequirementsConfigApi = {
  // 获取配置列表
  getList(params?: ExperimentRequirementsConfigListParams) {
    return request.get('/experiment-requirements-config', { params })
  },

  // 创建配置
  create(data: CreateExperimentRequirementsConfigData) {
    return request.post('/experiment-requirements-config', data)
  },

  // 获取配置详情
  getDetail(id: number) {
    return request.get(`/experiment-requirements-config/${id}`)
  },

  // 更新配置
  update(id: number, data: UpdateExperimentRequirementsConfigData) {
    return request.put(`/experiment-requirements-config/${id}`, data)
  },

  // 删除配置
  delete(id: number) {
    return request.delete(`/experiment-requirements-config/${id}`)
  },

  // 获取有效配置
  getEffectiveConfig(params: EffectiveConfigParams) {
    return request.post('/experiment-requirements-config/effective-config', params)
  },

  // 获取组织选项
  getOrganizationOptions(organizationType: 'province' | 'city' | 'county') {
    return request.get(`/experiment-requirements-config/organization-options/${organizationType}`)
  }
}

// 常量定义
export const ORGANIZATION_TYPES = [
  { value: 'province', label: '省级' },
  { value: 'city', label: '市级' },
  { value: 'county', label: '区县级' }
]

export const EXPERIMENT_TYPES = [
  { value: '分组实验', label: '分组实验' },
  { value: '演示实验', label: '演示实验' }
]

// 工具函数
export const getOrganizationTypeName = (type: string): string => {
  const typeMap: Record<string, string> = {
    'province': '省级',
    'city': '市级',
    'county': '区县级'
  }
  return typeMap[type] || type
}

export const getExperimentTypeName = (type: string): string => {
  return type
}

// 验证函数
export const validateConfigData = (data: CreateExperimentRequirementsConfigData | UpdateExperimentRequirementsConfigData): string[] => {
  const errors: string[] = []

  if (data.min_images < 0) {
    errors.push('最少图片数量不能小于0')
  }

  if (data.max_images < data.min_images) {
    errors.push('最多图片数量不能小于最少图片数量')
  }

  if (data.min_videos < 0) {
    errors.push('最少视频数量不能小于0')
  }

  if (data.max_videos < data.min_videos) {
    errors.push('最多视频数量不能小于最少视频数量')
  }

  if (data.max_images > 20) {
    errors.push('最多图片数量不能超过20')
  }

  if (data.max_videos > 10) {
    errors.push('最多视频数量不能超过10')
  }

  return errors
}
