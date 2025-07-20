import request from './request'

export interface SmartReservationParams {
  catalog_id: number
  laboratory_id: number
  reservation_date: string
  start_time: string
  end_time: string
  class_name: string
  student_count: number
  priority?: string
  auto_borrow_equipment?: boolean
  preparation_notes?: string
}

export interface ConflictCheckParams {
  laboratory_id: number
  reservation_date: string
  start_time: string
  end_time: string
  teacher_id?: number
  student_count?: number
  equipment_ids?: number[]
  exclude_reservation_id?: number
}

export interface LaboratoryScheduleParams {
  date_start: string
  date_end: string
  view_type?: 'week' | 'month'
}

export const smartReservationApi = {
  /**
   * 获取实验室课表
   */
  getLaboratorySchedule(laboratoryId: number, params: LaboratoryScheduleParams) {
    return request({
      url: `/smart-reservations/laboratories/${laboratoryId}/schedule`,
      method: 'get',
      params
    })
  },

  /**
   * 智能创建预约
   */
  create(data: SmartReservationParams) {
    return request({
      url: '/smart-reservations/create',
      method: 'post',
      data
    })
  },

  /**
   * 检测预约冲突
   */
  checkConflicts(data: ConflictCheckParams) {
    return request({
      url: '/smart-reservations/check-conflicts',
      method: 'post',
      data
    })
  },

  /**
   * 获取推荐时间段
   */
  getRecommendedTimeSlots(params: {
    laboratory_id: number
    date: string
    duration: number
    student_count: number
  }) {
    return request({
      url: '/smart-reservations/recommended-time-slots',
      method: 'get',
      params
    })
  },

  /**
   * 批量预约
   */
  batchCreate(data: {
    template_id?: number
    reservations: SmartReservationParams[]
  }) {
    return request({
      url: '/smart-reservations/batch-create',
      method: 'post',
      data
    })
  }
}

export default smartReservationApi
