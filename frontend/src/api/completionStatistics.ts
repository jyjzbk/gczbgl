import request from './request'

// 完成率统计接口类型定义
export interface CompletionBaseline {
  id: number
  school_id: number
  config_id: number
  subject_id: number
  grade: number
  semester: number
  total_experiments: number
  required_experiments: number
  optional_experiments: number
  demo_experiments: number
  group_experiments: number
  completed_experiments: number
  completion_rate: number
  last_calculated_at?: string
  calculated_by?: number
  // 关联数据
  subject?: {
    id: number
    name: string
  }
  config?: {
    id: number
    source_org_name: string
  }
  semester_name?: string
  grade_name?: string
  completion_level?: string
  completion_level_name?: string
}

export interface CompletionData {
  overall: {
    total_experiments: number
    completed_experiments: number
    completion_rate: number
  }
  by_subject: Record<string, {
    total: number
    completed: number
    rate: number
    dimension_name: string
  }>
  by_grade: Record<string, {
    total: number
    completed: number
    rate: number
    dimension_name: string
  }>
  by_semester: Record<string, {
    total: number
    completed: number
    rate: number
    dimension_name: string
  }>
  by_type: Record<string, {
    total: number
    completed: number
    rate: number
    dimension_name: string
  }>
  detailed_list: ExperimentDetail[]
}

export interface ExperimentDetail {
  catalog_id: number
  name: string
  code: string
  subject_id: number
  grade: number
  semester: number
  experiment_type: string
  is_completed: boolean
  difficulty_level: number
  duration: number
}

export interface TrendData {
  date: string
  avg_completion_rate: number
  total_experiments: number
  completed_experiments: number
}

export interface RankingItem {
  rank: number
  school_id: number
  school_name: string
  total_experiments: number
  completed_experiments: number
  completion_rate: number
}

export interface StatisticsOverview {
  overview: {
    total_schools: number
    total_experiments: number
    total_completed: number
    overall_completion_rate: number
  }
  school_stats: Record<number, {
    total_experiments: number
    completed_experiments: number
    completion_rate: number
  }>
  top_ranking: RankingItem[]
}

export interface DimensionStats {
  dimension: string
  stats: Array<{
    [key: string]: any
    total_experiments: number
    completed_experiments: number
    completion_rate: number
    dimension_name: string
  }>
}

// API 函数
export const completionStatisticsApi = {
  // 获取学校完成率统计
  getSchoolStatistics(schoolId: number, params?: {
    subject_id?: number
    grade?: number
    semester?: number
    start_date?: string
    end_date?: string
    force_recalculate?: boolean
  }) {
    return request.get(`/completion-statistics/school/${schoolId}`, { params })
  },

  // 获取完成率排行榜
  getCompletionRanking(params?: {
    school_ids?: number[]
    subject_id?: number
    grade?: number
    semester?: number
    limit?: number
  }) {
    return request.get('/completion-statistics/ranking', { params })
  },

  // 获取按维度统计的完成率
  getCompletionByDimension(schoolId: number, dimension: 'subject_id' | 'grade' | 'semester') {
    return request.get(`/completion-statistics/school/${schoolId}/by-dimension`, {
      params: { dimension }
    })
  },

  // 重新计算完成率
  recalculateCompletion(schoolIds: number[]) {
    return request.post('/completion-statistics/recalculate', {
      school_ids: schoolIds
    })
  },

  // 获取统计概览
  getStatisticsOverview(params?: {
    school_ids?: number[]
  }) {
    return request.get('/completion-statistics/overview', { params })
  }
}
