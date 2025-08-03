import request from './request'

// 教材版本指定相关接口类型定义
export interface TextbookVersionAssignment {
  id: number
  assigner_level: number
  assigner_org_id: number
  assigner_org_type: string
  assigner_user_id: number
  school_id: number
  subject_id: number
  grade_level: string
  textbook_version_id: number
  status: number
  assignment_reason?: string
  effective_date: string
  expiry_date?: string
  replaced_assignment_id?: number
  change_reason?: string
  created_at: string
  updated_at: string
  // 关联数据
  school?: {
    id: number
    name: string
  }
  subject?: {
    id: number
    name: string
  }
  textbook_version?: {
    id: number
    name: string
    publisher?: string
  }
  assigner_user?: {
    id: number
    name: string
  }
  assigner_level_name?: string
  status_name?: string
}

export interface TextbookAssignmentTemplate {
  id: number
  name: string
  description?: string
  creator_level: number
  creator_org_id: number
  creator_org_type: string
  creator_user_id: number
  assignment_config: Record<string, number> // subject_id -> textbook_version_id
  applicable_grades: string[]
  applicable_school_types?: number[]
  usage_count: number
  last_used_at?: string
  status: number
  is_default: number
  created_at: string
  updated_at: string
  // 关联数据
  creator_user?: {
    id: number
    name: string
  }
  creator_level_name?: string
  status_name?: string
}

export interface School {
  id: number
  name: string
  code: string
  type: number
  management_level: number
  province_id?: number
  city_id?: number
  county_id?: number
  district_id?: number
  administrative_region?: {
    id: number
    name: string
    level: number
  }
}

// 请求参数类型
export interface AssignmentListParams {
  school_id?: number // 可选，不传则获取所有可管理学校的指定记录
  subject_id?: number
  grade_level?: string
  status?: 'active' | '0' | '1'
  page?: number
  per_page?: number
}

export interface CreateAssignmentParams {
  school_id: number
  subject_id: number
  grade_level: string
  textbook_version_id: number
  assignment_reason?: string
  effective_date?: string
  expiry_date?: string
}

export interface BatchAssignmentParams {
  assignments: CreateAssignmentParams[]
}

export interface TemplateAssignmentParams {
  template_id: number
  school_ids: number[]
}

export interface CreateTemplateParams {
  name: string
  description?: string
  assignment_config: Record<string, number>
  applicable_grades: string[]
  applicable_school_types?: number[]
  is_default?: boolean
}

export interface TemplateListParams {
  search?: string
  status?: 0 | 1
  creator_level?: number
  page?: number
  per_page?: number
}

export interface AssignedVersionParams {
  school_id: number
  subject_id: number
  grade_level: string
}

export interface RevokeAssignmentParams {
  reason: string
}

// 教材版本指定管理API
export const textbookAssignmentApi = {
  // 获取可管理的学校列表
  getManageableSchools() {
    return request.get<{ data: School[] }>('/textbook-version-assignments/manageable-schools')
  },

  // 获取指定统计信息
  getStatistics() {
    return request.get('/textbook-version-assignments/statistics')
  },

  // 获取学校指定的教材版本
  getAssignedVersion(params: AssignedVersionParams) {
    return request.get('/textbook-version-assignments/assigned-version', { params })
  },

  // 获取学校的教材版本指定列表
  getAssignments(params: AssignmentListParams) {
    return request.get<{ data: { items: TextbookVersionAssignment[], pagination: any } }>('/textbook-version-assignments', { params })
  },

  // 创建教材版本指定
  createAssignment(data: CreateAssignmentParams) {
    return request.post<{ data: TextbookVersionAssignment }>('/textbook-version-assignments', data)
  },

  // 批量指定教材版本
  batchCreateAssignments(data: BatchAssignmentParams) {
    return request.post('/textbook-version-assignments/batch', data)
  },

  // 使用模板批量指定
  assignByTemplate(data: TemplateAssignmentParams) {
    return request.post('/textbook-version-assignments/assign-by-template', data)
  },

  // 撤销指定
  revokeAssignment(id: number, data: RevokeAssignmentParams) {
    return request.put(`/textbook-version-assignments/${id}/revoke`, data)
  }
}

// 教材版本指定模板API
export const textbookAssignmentTemplateApi = {
  // 获取模板选项（用于下拉菜单）
  getOptions() {
    return request.get<{ data: TextbookAssignmentTemplate[] }>('/textbook-assignment-templates/options')
  },

  // 获取模板列表
  getTemplates(params?: TemplateListParams) {
    return request.get<{ data: { items: TextbookAssignmentTemplate[], pagination: any } }>('/textbook-assignment-templates', { params })
  },

  // 创建模板
  createTemplate(data: CreateTemplateParams) {
    return request.post<{ data: TextbookAssignmentTemplate }>('/textbook-assignment-templates', data)
  },

  // 获取模板详情
  getTemplate(id: number) {
    return request.get<{ data: TextbookAssignmentTemplate }>(`/textbook-assignment-templates/${id}`)
  },

  // 更新模板
  updateTemplate(id: number, data: Partial<CreateTemplateParams>) {
    return request.put<{ data: TextbookAssignmentTemplate }>(`/textbook-assignment-templates/${id}`, data)
  },

  // 删除模板
  deleteTemplate(id: number) {
    return request.delete(`/textbook-assignment-templates/${id}`)
  }
}

// 导出所有API
export default {
  ...textbookAssignmentApi,
  template: textbookAssignmentTemplateApi
}
