import request from './request'

export interface ExperimentReservationParams {
  page?: number
  per_page?: number
  school_id?: number
  catalog_id?: number
  laboratory_id?: number
  teacher_id?: string | number
  status?: number
  date_start?: string
  date_end?: string
  class_name?: string
}

export interface CreateReservationData {
  catalog_id: number
  laboratory_id: number
  reservation_date: string
  start_time: string
  end_time: string
  class_name: string
  student_count: number
  remark?: string
  priority?: string
  preparation_notes?: string
}

export interface UpdateReservationData {
  catalog_id?: number
  laboratory_id?: number
  reservation_date?: string
  start_time?: string
  end_time?: string
  class_name?: string
  student_count?: number
  remark?: string
  priority?: string
  preparation_notes?: string
}

export interface ReviewData {
  status: number
  review_remark?: string
}

export const experimentReservationApi = {
  /**
   * 获取实验预约列表
   */
  getList(params: ExperimentReservationParams = {}) {
    return request({
      url: '/experiment-reservations',
      method: 'get',
      params
    })
  },

  /**
   * 获取预约详情
   */
  getDetail(id: number) {
    return request({
      url: `/experiment-reservations/${id}`,
      method: 'get'
    })
  },

  /**
   * 创建预约
   */
  create(data: CreateReservationData) {
    return request({
      url: '/experiment-reservations',
      method: 'post',
      data
    })
  },

  /**
   * 更新预约
   */
  update(id: number, data: UpdateReservationData) {
    return request({
      url: `/experiment-reservations/${id}`,
      method: 'put',
      data
    })
  },

  /**
   * 删除预约
   */
  delete(id: number) {
    return request({
      url: `/experiment-reservations/${id}`,
      method: 'delete'
    })
  },

  /**
   * 取消预约
   */
  cancel(id: number) {
    return request({
      url: `/experiment-reservations/${id}/cancel`,
      method: 'post'
    })
  },

  /**
   * 审核预约
   */
  review(id: number, data: ReviewData) {
    return request({
      url: `/experiment-reservations/${id}/review`,
      method: 'post',
      data
    })
  },

  /**
   * 批量审核
   */
  batchReview(data: {
    ids: number[]
    status: number
    review_remark?: string
  }) {
    return request({
      url: '/experiment-reservations/batch-review',
      method: 'post',
      data
    })
  },

  /**
   * 获取待审核预约数量
   */
  getPendingCount() {
    return request({
      url: '/experiment-reservations/pending-count',
      method: 'get'
    })
  },

  /**
   * 导出预约数据
   */
  export(params: ExperimentReservationParams & {
    format?: 'excel' | 'pdf'
  } = {}) {
    return request({
      url: '/experiment-reservations/export',
      method: 'get',
      params: {
        format: 'excel',
        ...params
      },
      responseType: 'blob'
    })
  },

  /**
   * 获取预约统计
   */
  getStats(params: {
    school_id?: number
    teacher_id?: string | number
    date_start?: string
    date_end?: string
  } = {}) {
    return request({
      url: '/experiment-reservations/stats',
      method: 'get',
      params
    })
  },

  /**
   * 获取可预约时间段
   */
  getAvailableTimeSlots(params: {
    laboratory_id: number
    date: string
    duration?: number
  }) {
    return request({
      url: '/experiment-reservations/available-time-slots',
      method: 'get',
      params
    })
  },

  /**
   * 检查预约冲突
   */
  checkConflicts(data: {
    laboratory_id: number
    reservation_date: string
    start_time: string
    end_time: string
    exclude_id?: number
  }) {
    return request({
      url: '/experiment-reservations/check-conflicts',
      method: 'post',
      data
    })
  },

  /**
   * 复制预约
   */
  duplicate(id: number, data: {
    reservation_date: string
    start_time?: string
    end_time?: string
  }) {
    return request({
      url: `/experiment-reservations/${id}/duplicate`,
      method: 'post',
      data
    })
  },

  /**
   * 获取预约历史
   */
  getHistory(id: number) {
    return request({
      url: `/experiment-reservations/${id}/history`,
      method: 'get'
    })
  }
}

export default experimentReservationApi
