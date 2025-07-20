import request from './request'

export interface StatisticsParams {
  start_date?: string
  end_date?: string
  school_id?: number
}

export interface DashboardStats {
  total_schools: number
  total_users: number
  total_equipment: number
  total_laboratories: number
  equipment_value: number
  monthly_experiments: number
  monthly_completed: number
}

export interface ExperimentStats {
  summary: {
    total_experiments: number
    completed_experiments: number
    completion_rate: number
    avg_quality_score: number
    excellent_rate: number
  }
  quality_distribution: {
    excellent: number
    good: number
    poor: number
  }
  monthly_trend: Array<{
    month: string
    total: number
    completed: number
  }>
  school_ranking: Array<{
    id: number
    name: string
    total: number
    completed: number
    completion_rate: number
  }>
}

export interface EquipmentStats {
  summary: {
    total_equipment: number
    normal_equipment: number
    maintenance_equipment: number
    scrapped_equipment: number
    total_value: number
  }
  status_distribution: Array<{
    status: number
    count: number
    value: number
  }>
  category_distribution: Array<{
    category_name: string
    count: number
    total_value: number
  }>
  top_utilized: Array<{
    id: number
    name: string
    code: string
    borrow_count: number
    total_days: number
  }>
}

export interface UserActivityStats {
  summary: {
    total_users: number
    active_users: number
    activity_rate: number
    never_login_users: number
  }
  role_distribution: Array<{
    role: string
    count: number
  }>
  level_distribution: Array<{
    organization_level: number
    count: number
  }>
  recent_active: Array<{
    id: number
    real_name: string
    role: string
    last_login_at: string
  }>
}

export interface OrganizationPerformance {
  school_performance: Array<{
    id: number
    name: string
    type: number
    total_experiments: number
    completed_experiments: number
    completion_rate: number
    total_equipment: number
    equipment_value: number
    total_users: number
    avg_quality_score: number
  }>
  rankings: Array<{
    id: number
    name: string
    type: number
    total_experiments: number
    completed_experiments: number
    completion_rate: number
    total_equipment: number
    equipment_value: number
    total_users: number
    avg_quality_score: number
  }>
  summary: {
    total_schools: number
    avg_completion_rate: number
    avg_quality_score: number
    total_equipment_value: number
  }
}

export const statisticsApi = {
  // 获取仪表盘统计数据
  getDashboardStats() {
    return request<{
      success: boolean
      data: DashboardStats
    }>({
      url: '/statistics/dashboard',
      method: 'get'
    })
  },

  // 获取实验使用统计
  getExperimentStats(params?: StatisticsParams) {
    return request<{
      success: boolean
      data: ExperimentStats
    }>({
      url: '/statistics/experiments',
      method: 'get',
      params
    })
  },

  // 获取设备利用率统计
  getEquipmentStats(params?: StatisticsParams) {
    return request<{
      success: boolean
      data: EquipmentStats
    }>({
      url: '/statistics/equipment',
      method: 'get',
      params
    })
  },

  // 获取用户活跃度统计
  getUserActivityStats(params?: StatisticsParams) {
    return request<{
      success: boolean
      data: UserActivityStats
    }>({
      url: '/statistics/users',
      method: 'get',
      params
    })
  },

  // 获取组织绩效统计
  getOrganizationPerformance(params?: StatisticsParams) {
    return request<{
      success: boolean
      data: OrganizationPerformance
    }>({
      url: '/statistics/performance',
      method: 'get',
      params
    })
  },

  /**
   * 获取个人实验统计数据
   */
  getPersonalExperimentStats(params: { teacher_id?: string | number } = {}) {
    return request<{
      success: boolean
      data: {
        total_reservations: number
        completed_experiments: number
        completion_rate: number
        total_works: number
        pending_reservations: number
        approved_reservations: number
      }
    }>({
      url: '/personal/experiment-stats',
      method: 'get',
      params: {
        teacher_id: 'current',
        ...params
      }
    })
  },

  /**
   * 获取实验完成率趋势
   */
  getExperimentCompletionTrend(params: {
    teacher_id?: string | number
    months?: number
  } = {}) {
    return request<{
      success: boolean
      data: Array<{
        month: string
        completion_rate: number
        total_experiments: number
        completed_experiments: number
      }>
    }>({
      url: '/statistics/experiment-completion-trend',
      method: 'get',
      params: {
        teacher_id: 'current',
        months: 12,
        ...params
      }
    })
  }
}
