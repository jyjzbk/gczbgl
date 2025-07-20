import request from './request'

export interface ExperimentWorkParams {
  record_id?: number
  student_id?: number
  type?: 'photo' | 'video' | 'document' | 'other'
  is_featured?: boolean
  is_public?: boolean
  page?: number
  per_page?: number
}

export interface CreateWorkData {
  record_id: number
  student_id?: number
  title: string
  description?: string
  is_public?: boolean
}

export interface UpdateWorkData {
  title?: string
  description?: string
  quality_score?: number
  teacher_comment?: string
  is_featured?: boolean
  is_public?: boolean
}

export const experimentWorkApi = {
  /**
   * 获取实验作品列表
   */
  getList(params: ExperimentWorkParams = {}) {
    return request({
      url: '/experiment-works',
      method: 'get',
      params
    })
  },

  /**
   * 获取作品详情
   */
  getDetail(id: number) {
    return request({
      url: `/experiment-works/${id}`,
      method: 'get'
    })
  },

  /**
   * 上传实验作品
   */
  upload(formData: FormData) {
    return request({
      url: '/experiment-works',
      method: 'post',
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },

  /**
   * 更新作品信息
   */
  update(id: number, data: UpdateWorkData) {
    return request({
      url: `/experiment-works/${id}`,
      method: 'put',
      data
    })
  },

  /**
   * 删除作品
   */
  delete(id: number) {
    return request({
      url: `/experiment-works/${id}`,
      method: 'delete'
    })
  },

  /**
   * 获取精选作品
   */
  getFeatured(params: { per_page?: number } = {}) {
    return request({
      url: '/experiment-works',
      method: 'get',
      params: {
        is_featured: true,
        is_public: true,
        ...params
      }
    })
  },

  /**
   * 设置精选作品
   */
  setFeatured(id: number, featured: boolean) {
    return request({
      url: `/experiment-works/${id}`,
      method: 'put',
      data: {
        is_featured: featured
      }
    })
  },

  /**
   * 批量删除作品
   */
  batchDelete(ids: number[]) {
    return request({
      url: '/experiment-works/batch-delete',
      method: 'post',
      data: { ids }
    })
  },

  /**
   * 获取作品统计信息
   */
  getStats(params: { record_id?: number; student_id?: number } = {}) {
    return request({
      url: '/experiment-works/stats',
      method: 'get',
      params
    })
  }
}

export default experimentWorkApi
