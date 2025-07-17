import axios from 'axios'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

// 创建axios实例
const request = axios.create({
  baseURL: '/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// 请求拦截器
request.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    
    // 添加token到请求头
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }
    
    return config
  },
  (error) => {
    console.error('请求错误:', error)
    return Promise.reject(error)
  }
)

// 响应拦截器
request.interceptors.response.use(
  (response) => {
    const { data } = response
    
    // 如果是文件下载，直接返回
    if (response.config.responseType === 'blob') {
      return response
    }
    
    // 检查业务状态码
    // 支持两种格式：{code: 200, data: ...} 和 {success: true, data: ...}
    if (data.code !== undefined) {
      // 旧格式：code字段
      if (data.code !== 200 && data.code !== 201) {
        ElMessage.error(data.message || '请求失败')
        return Promise.reject(new Error(data.message || '请求失败'))
      }
      return data
    } else if (data.success !== undefined) {
      // 新格式：success字段
      if (!data.success) {
        ElMessage.error(data.message || '请求失败')
        return Promise.reject(new Error(data.message || '请求失败'))
      }
      return data
    }

    // 如果没有业务状态码，直接返回数据
    return data
  },
  (error) => {
    console.error('响应错误:', error)
    
    const { response } = error
    
    if (response) {
      const { status, data } = response
      
      switch (status) {
        case 401:
          ElMessage.error('登录已过期，请重新登录')
          const authStore = useAuthStore()
          authStore.logout()
          break
        case 403:
          ElMessage.error('没有权限访问该资源')
          break
        case 404:
          ElMessage.error('请求的资源不存在')
          break
        case 422:
          // 表单验证错误
          if (data.errors) {
            const firstError = Object.values(data.errors)[0] as string[]
            ElMessage.error(firstError[0])
          } else {
            ElMessage.error(data.message || '请求参数错误')
          }
          break
        case 500:
          ElMessage.error('服务器内部错误')
          break
        default:
          ElMessage.error(data?.message || '网络错误')
      }
    } else {
      ElMessage.error('网络连接失败')
    }
    
    return Promise.reject(error)
  }
)

export default request
