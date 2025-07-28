import request from './request'

// 预警接口类型定义
export interface ExperimentAlert {
  id: number
  alert_type: 'overdue' | 'completion_rate' | 'quality_score'
  target_type: 'school' | 'teacher' | 'experiment' | 'class'
  target_id: number
  target_name: string
  alert_level: 'low' | 'medium' | 'high' | 'critical'
  alert_title: string
  alert_message: string
  alert_data?: any
  alert_value?: number
  threshold_value?: number
  is_read: boolean
  is_resolved: boolean
  resolve_note?: string
  resolved_by?: number
  resolved_at?: string
  alert_time: string
  created_at: string
  updated_at: string
  resolvedBy?: {
    id: number
    name: string
  }
  alert_type_name?: string
  target_type_name?: string
  alert_level_name?: string
  alert_level_color?: string
}

export interface ExperimentAlertConfig {
  id: number
  organization_type: 'province' | 'city' | 'county'
  organization_id: number
  organization_name: string
  alert_type: 'overdue' | 'completion_rate' | 'quality_score'
  threshold_value: number
  alert_days: number
  is_active: boolean
  alert_rules?: string
  notification_settings?: any
  created_by: number
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
  }
  alert_type_name?: string
}

export interface MonitoringDashboard {
  total_experiments: number
  overdue_experiments: number
  low_completion_experiments: number
  low_quality_experiments: number
  total_alerts: number
  unread_alerts: number
  resolved_alerts: number
  alert_trends: {
    date: string
    count: number
    type: string
  }[]
  completion_rate_trend: {
    date: string
    rate: number
  }[]
  quality_score_trend: {
    date: string
    score: number
  }[]
  recent_alerts: ExperimentAlert[]
}

export interface ExperimentProgress {
  id: number
  experiment_id: number
  experiment_name: string
  school_id: number
  school_name: string
  teacher_id: number
  teacher_name: string
  class_id: number
  class_name: string
  total_students: number
  completed_students: number
  completion_rate: number
  quality_score?: number
  start_date: string
  end_date: string
  status: 'not_started' | 'in_progress' | 'completed' | 'overdue'
  is_overdue: boolean
  days_overdue?: number
  last_activity?: string
  created_at: string
  updated_at: string
}

export interface AlertStatistics {
  total_alerts: number
  alert_by_type: {
    type: string
    count: number
    percentage: number
  }[]
  alert_by_level: {
    level: string
    count: number
    percentage: number
  }[]
  alert_trends: {
    date: string
    count: number
  }[]
  resolution_rate: number
  avg_resolution_time: number
}

export interface ExperimentRequirement {
  id: number
  experiment_id: number
  requirement_type: 'equipment' | 'material' | 'environment' | 'safety'
  requirement_name: string
  requirement_description?: string
  is_required: boolean
  quantity?: number
  unit?: string
  specifications?: string
  safety_level?: 'low' | 'medium' | 'high'
  created_at: string
  updated_at: string
}

export interface ExperimentSchedule {
  id: number
  experiment_id: number
  school_id: number
  teacher_id: number
  class_id: number
  scheduled_date: string
  scheduled_time: string
  duration_minutes: number
  location: string
  status: 'scheduled' | 'in_progress' | 'completed' | 'cancelled'
  notes?: string
  created_at: string
  updated_at: string
  experiment?: {
    id: number
    name: string
    subject: string
    grade_level: string
  }
  school?: {
    id: number
    name: string
  }
  teacher?: {
    id: number
    name: string
  }
  class?: {
    id: number
    name: string
  }
  semester: string
}

// API 函数
export const experimentMonitoringApi = {
  // 获取监控仪表板数据
  getDashboard(semester?: string) {
    const params = semester ? { semester } : undefined
    return request.get('/experiment-monitoring/dashboard', { params })
  },

  // 获取实验进度列表
  getProgress(params?: {
    page?: number
    per_page?: number
    school_id?: number
    teacher_id?: number
    class_id?: number
    status?: string
    semester?: string
    search?: string
  }) {
    return request.get('/experiment-monitoring/progress', { params })
  },

  // 获取预警列表
  getAlerts(params?: {
    page?: number
    per_page?: number
    alert_type?: string
    alert_level?: string
    is_read?: boolean
    is_resolved?: boolean
    target_type?: string
    target_id?: number
    date_from?: string
    date_to?: string
  }) {
    return request.get('/experiment-monitoring/alerts', { params })
  },

  // 标记预警为已读
  markAlertAsRead(id: number) {
    return request.put(`/experiment-monitoring/alerts/${id}/read`)
  },

  // 批量标记预警为已读
  markAlertsAsRead(ids: number[]) {
    return request.put('/experiment-monitoring/alerts/batch-read', { ids })
  },

  // 解决预警
  resolveAlert(id: number, note?: string) {
    return request.put(`/experiment-monitoring/alerts/${id}/resolve`, { note })
  },

  // 批量解决预警
  resolveAlerts(ids: number[], note?: string) {
    return request.put('/experiment-monitoring/alerts/batch-resolve', { ids, note })
  },

  // 手动触发预警检查
  triggerAlertCheck(params?: {
    alert_type?: 'overdue' | 'completion_rate' | 'quality_score'
    organization_type?: 'province' | 'city' | 'county'
    organization_id?: number
  }) {
    return request.post('/experiment-monitoring/trigger-alert-check', params)
  },

  // 获取预警统计
  getAlertStatistics(params?: {
    date_from?: string
    date_to?: string
    trend_days?: number
  }) {
    return request.get('/experiment-monitoring/alert-statistics', { params })
  }
}

// 预警配置API
export const experimentAlertConfigApi = {
  // 获取预警配置列表
  getList(params?: {
    page?: number
    per_page?: number
    organization_type?: string
    alert_type?: string
    organization_id?: number
  }) {
    return request.get('/experiment-alert-config', { params })
  },

  // 创建预警配置
  create(data: {
    organization_type: 'province' | 'city' | 'county'
    organization_id: number
    organization_name: string
    alert_type: 'overdue' | 'completion_rate' | 'quality_score'
    threshold_value: number
    alert_days: number
    alert_rules?: string
    notification_settings?: any
  }) {
    return request.post('/experiment-alert-config', data)
  },

  // 获取预警配置详情
  getDetail(id: number) {
    return request.get(`/experiment-alert-config/${id}`)
  },

  // 更新预警配置
  update(id: number, data: {
    threshold_value: number
    alert_days: number
    alert_rules?: string
    notification_settings?: any
    is_active?: boolean
  }) {
    return request.put(`/experiment-alert-config/${id}`, data)
  },

  // 删除预警配置
  delete(id: number) {
    return request.delete(`/experiment-alert-config/${id}`)
  }
}

// 预警配置扩展API
export const experimentAlertConfigExtApi = {
  // 获取有效预警配置
  getEffectiveConfig(params: {
    organization_type: 'province' | 'city' | 'county';
    organization_id: number;
    alert_type: 'overdue' | 'completion_rate' | 'quality_score';
  }) {
    return request.post('/experiment-alert-config/effective-config', params)
  },

  // 获取组织选项
  getOrganizationOptions(organizationType?: 'province' | 'city' | 'county') {
    const params = organizationType ? { organization_type: organizationType } : undefined
    return request.get('/experiment-alert-config/organization-options', { params })
  }
}

// 常量定义
export const ALERT_TYPES = [
  { value: 'overdue', label: '超期未开' },
  { value: 'completion_rate', label: '完成率低' },
  { value: 'quality_score', label: '质量评分低' }
]

export const ALERT_LEVELS = [
  { value: 'low', label: '低级' },
  { value: 'medium', label: '中级' },
  { value: 'high', label: '高级' },
  { value: 'critical', label: '严重' }
]

export const TARGET_TYPES = [
  { value: 'school', label: '学校' },
  { value: 'teacher', label: '教师' },
  { value: 'experiment', label: '实验' },
  { value: 'class', label: '班级' }
]

export const ORGANIZATION_TYPES = [
  { value: 'province', label: '省级' },
  { value: 'city', label: '市级' },
  { value: 'county', label: '区县级' }
]

// 工具函数
export const getAlertTypeName = (type: string): string => {
  const typeMap: Record<string, string> = {
    'overdue': '超期未开',
    'completion_rate': '完成率低',
    'quality_score': '质量评分低'
  }
  return typeMap[type] || type
}

export const getAlertLevelName = (level: string): string => {
  const levelMap: Record<string, string> = {
    'low': '低级',
    'medium': '中级',
    'high': '高级',
    'critical': '严重'
  }
  return levelMap[level] || level
}

export const getAlertLevelColor = (level: string): string => {
  const colorMap: Record<string, string> = {
    'low': '#67C23A',
    'medium': '#E6A23C',
    'high': '#F56C6C',
    'critical': '#F56C6C'
  }
  return colorMap[level] || '#909399'
}

export const getTargetTypeName = (type: string): string => {
  const typeMap: Record<string, string> = {
    'school': '学校',
    'teacher': '教师',
    'experiment': '实验',
    'class': '班级'
  }
  return typeMap[type] || type
}
