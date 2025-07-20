import request from './request'

export interface EquipmentRequirement {
  id?: number
  catalog_id: number
  equipment_id: number
  equipment_name?: string
  equipment_code?: string
  category_name?: string
  required_quantity: number
  min_quantity: number
  is_required: boolean
  calculation_type: 'fixed' | 'per_group' | 'per_student'
  group_size?: number
  usage_note?: string
  safety_note?: string
  sort_order: number
  is_active: boolean
  available_quantity?: number
  unit?: string
  example_quantity_30?: number
  example_quantity_40?: number
}

export interface EquipmentRecommendation {
  equipment_id: number
  equipment_name: string
  equipment_code: string
  category_name: string
  recommended_quantity: number
  calculation_type: string
  confidence: number
}

export interface RequirementTemplate {
  id?: number
  name: string
  description?: string
  subject_id?: number
  experiment_type: string
  equipment_list: EquipmentRequirement[]
  is_public: boolean
  created_by?: number
  school_id?: number
  use_count?: number
  subject?: any
  creator?: any
  school?: any
}

// 器材需求配置API
export const equipmentRequirementApi = {
  // 获取实验目录的器材需求配置
  getRequirements: (catalogId: number, params?: { active_only?: boolean }) => {
    return request.get<{ data: EquipmentRequirement[] }>(`/experiment-catalogs/${catalogId}/equipment-requirements`, { params })
  },

  // 批量配置器材需求
  batchUpdate: (catalogId: number, data: { requirements: EquipmentRequirement[], replace_all?: boolean }) => {
    return request.post(`/experiment-catalogs/${catalogId}/equipment-requirements/batch`, data)
  },

  // 获取智能推荐器材
  getRecommendations: (catalogId: number) => {
    return request.get<{ data: EquipmentRecommendation[] }>(`/experiment-catalogs/${catalogId}/equipment-requirements/recommendations`)
  },

  // 从其他实验复制配置
  copyFromCatalog: (catalogId: number, sourceCatalogId: number) => {
    return request.post(`/experiment-catalogs/${catalogId}/equipment-requirements/copy`, {
      source_catalog_id: sourceCatalogId
    })
  },

  // 更新排序
  updateSortOrder: (catalogId: number, sortData: Array<{ equipment_id: number, sort_order: number }>) => {
    return request.put(`/experiment-catalogs/${catalogId}/equipment-requirements/sort-order`, {
      sort_data: sortData
    })
  },

  // 更新单个器材需求
  updateRequirement: (catalogId: number, requirementId: number, data: Partial<EquipmentRequirement>) => {
    return request.put(`/experiment-catalogs/${catalogId}/equipment-requirements/${requirementId}`, data)
  },

  // 删除器材需求
  deleteRequirement: (catalogId: number, requirementId: number) => {
    return request.delete(`/experiment-catalogs/${catalogId}/equipment-requirements/${requirementId}`)
  }
}

// 器材配置模板API
export const equipmentTemplateApi = {
  // 获取模板列表
  getTemplates: (params?: {
    subject_id?: number
    experiment_type?: string
    is_public?: boolean
    per_page?: number
  }) => {
    return request.get<{ data: any }>('/equipment-requirement-templates', { params })
  },

  // 创建模板
  createTemplate: (data: Omit<RequirementTemplate, 'id'>) => {
    return request.post<{ data: RequirementTemplate }>('/equipment-requirement-templates', data)
  },

  // 获取模板详情
  getTemplate: (id: number) => {
    return request.get<{ data: RequirementTemplate }>(`/equipment-requirement-templates/${id}`)
  },

  // 更新模板
  updateTemplate: (id: number, data: Partial<RequirementTemplate>) => {
    return request.put<{ data: RequirementTemplate }>(`/equipment-requirement-templates/${id}`, data)
  },

  // 删除模板
  deleteTemplate: (id: number) => {
    return request.delete(`/equipment-requirement-templates/${id}`)
  },

  // 应用模板
  applyTemplate: (templateId: number, catalogId: number) => {
    return request.post(`/equipment-requirement-templates/${templateId}/apply`, {
      catalog_id: catalogId
    })
  }
}
