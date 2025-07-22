/**
 * 用户体验优化工具函数
 */

import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'
import type { LoadingInstance } from 'element-plus/es/components/loading/src/loading'

/**
 * 统一的错误处理
 */
export class ErrorHandler {
  private static instance: ErrorHandler
  
  static getInstance(): ErrorHandler {
    if (!ErrorHandler.instance) {
      ErrorHandler.instance = new ErrorHandler()
    }
    return ErrorHandler.instance
  }
  
  /**
   * 处理API错误
   */
  handleApiError(error: any, context?: string) {
    console.error(`API Error${context ? ` in ${context}` : ''}:`, error)
    
    let message = '操作失败，请稍后重试'
    
    if (error.response) {
      const { status, data } = error.response
      
      switch (status) {
        case 400:
          message = data.message || '请求参数错误'
          break
        case 401:
          message = '登录已过期，请重新登录'
          // 可以在这里触发登录跳转
          break
        case 403:
          message = '没有权限执行此操作'
          break
        case 404:
          message = '请求的资源不存在'
          break
        case 422:
          message = data.message || '数据验证失败'
          break
        case 500:
          message = '服务器内部错误'
          break
        default:
          message = data.message || `请求失败 (${status})`
      }
    } else if (error.code === 'NETWORK_ERROR') {
      message = '网络连接失败，请检查网络设置'
    } else if (error.code === 'TIMEOUT') {
      message = '请求超时，请稍后重试'
    }
    
    ElMessage.error(message)
    return message
  }
  
  /**
   * 处理表单验证错误
   */
  handleValidationError(errors: Record<string, string[]>) {
    const firstError = Object.values(errors)[0]?.[0]
    if (firstError) {
      ElMessage.error(firstError)
    }
  }
  
  /**
   * 处理权限错误
   */
  handlePermissionError(action: string) {
    ElMessage.warning(`您没有权限执行"${action}"操作`)
  }
}

/**
 * 加载状态管理
 */
export class LoadingManager {
  private loadingInstances: Map<string, LoadingInstance> = new Map()
  private loadingCounts: Map<string, number> = new Map()
  
  /**
   * 显示加载状态
   */
  show(key: string = 'default', options?: any): LoadingInstance {
    const count = this.loadingCounts.get(key) || 0
    this.loadingCounts.set(key, count + 1)
    
    if (!this.loadingInstances.has(key)) {
      const loading = ElLoading.service({
        text: '加载中...',
        background: 'rgba(0, 0, 0, 0.7)',
        ...options
      })
      this.loadingInstances.set(key, loading)
    }
    
    return this.loadingInstances.get(key)!
  }
  
  /**
   * 隐藏加载状态
   */
  hide(key: string = 'default') {
    const count = this.loadingCounts.get(key) || 0
    if (count <= 1) {
      const loading = this.loadingInstances.get(key)
      if (loading) {
        loading.close()
        this.loadingInstances.delete(key)
      }
      this.loadingCounts.delete(key)
    } else {
      this.loadingCounts.set(key, count - 1)
    }
  }
  
  /**
   * 强制隐藏所有加载状态
   */
  hideAll() {
    this.loadingInstances.forEach(loading => loading.close())
    this.loadingInstances.clear()
    this.loadingCounts.clear()
  }
}

/**
 * 确认对话框工具
 */
export class ConfirmDialog {
  /**
   * 删除确认
   */
  static async confirmDelete(itemName: string, itemType: string = '项目'): Promise<boolean> {
    try {
      await ElMessageBox.confirm(
        `确定要删除${itemType}"${itemName}"吗？此操作不可撤销。`,
        '确认删除',
        {
          confirmButtonText: '确定删除',
          cancelButtonText: '取消',
          type: 'warning',
          confirmButtonClass: 'el-button--danger'
        }
      )
      return true
    } catch {
      return false
    }
  }
  
  /**
   * 批量删除确认
   */
  static async confirmBatchDelete(count: number, itemType: string = '项目'): Promise<boolean> {
    try {
      await ElMessageBox.confirm(
        `确定要删除选中的 ${count} 个${itemType}吗？此操作不可撤销。`,
        '确认批量删除',
        {
          confirmButtonText: '确定删除',
          cancelButtonText: '取消',
          type: 'warning',
          confirmButtonClass: 'el-button--danger'
        }
      )
      return true
    } catch {
      return false
    }
  }
  
  /**
   * 保存确认
   */
  static async confirmSave(hasChanges: boolean = true): Promise<boolean> {
    if (!hasChanges) {
      ElMessage.info('没有需要保存的更改')
      return false
    }
    
    try {
      await ElMessageBox.confirm(
        '确定要保存当前更改吗？',
        '确认保存',
        {
          confirmButtonText: '保存',
          cancelButtonText: '取消',
          type: 'info'
        }
      )
      return true
    } catch {
      return false
    }
  }
  
  /**
   * 离开页面确认
   */
  static async confirmLeave(hasUnsavedChanges: boolean = true): Promise<boolean> {
    if (!hasUnsavedChanges) {
      return true
    }
    
    try {
      await ElMessageBox.confirm(
        '您有未保存的更改，确定要离开此页面吗？',
        '确认离开',
        {
          confirmButtonText: '离开',
          cancelButtonText: '取消',
          type: 'warning'
        }
      )
      return true
    } catch {
      return false
    }
  }
}

/**
 * 成功消息工具
 */
export class SuccessMessage {
  /**
   * 创建成功
   */
  static created(itemName: string, itemType: string = '项目') {
    ElMessage.success(`${itemType}"${itemName}"创建成功`)
  }
  
  /**
   * 更新成功
   */
  static updated(itemName: string, itemType: string = '项目') {
    ElMessage.success(`${itemType}"${itemName}"更新成功`)
  }
  
  /**
   * 删除成功
   */
  static deleted(itemName: string, itemType: string = '项目') {
    ElMessage.success(`${itemType}"${itemName}"删除成功`)
  }
  
  /**
   * 批量操作成功
   */
  static batchOperation(count: number, operation: string, itemType: string = '项目') {
    ElMessage.success(`成功${operation} ${count} 个${itemType}`)
  }
  
  /**
   * 导入成功
   */
  static imported(count: number, itemType: string = '项目') {
    ElMessage.success(`成功导入 ${count} 个${itemType}`)
  }
  
  /**
   * 导出成功
   */
  static exported(itemType: string = '数据') {
    ElMessage.success(`${itemType}导出成功`)
  }
}

/**
 * 表单验证增强
 */
export class FormValidator {
  /**
   * 验证必填字段
   */
  static required(message: string = '此字段为必填项') {
    return { required: true, message, trigger: 'blur' }
  }
  
  /**
   * 验证长度
   */
  static length(min: number, max: number, message?: string) {
    return {
      min,
      max,
      message: message || `长度在 ${min} 到 ${max} 个字符`,
      trigger: 'blur'
    }
  }
  
  /**
   * 验证邮箱
   */
  static email(message: string = '请输入正确的邮箱地址') {
    return {
      type: 'email' as const,
      message,
      trigger: 'blur'
    }
  }
  
  /**
   * 验证手机号
   */
  static phone(message: string = '请输入正确的手机号码') {
    return {
      pattern: /^1[3-9]\d{9}$/,
      message,
      trigger: 'blur'
    }
  }
  
  /**
   * 验证数字范围
   */
  static numberRange(min: number, max: number, message?: string) {
    return {
      type: 'number' as const,
      min,
      max,
      message: message || `数值必须在 ${min} 到 ${max} 之间`,
      trigger: 'blur'
    }
  }
  
  /**
   * 自定义验证器
   */
  static custom(validator: (rule: any, value: any, callback: any) => void) {
    return { validator, trigger: 'blur' }
  }
}

/**
 * 键盘快捷键管理
 */
export class KeyboardShortcuts {
  private shortcuts: Map<string, () => void> = new Map()
  
  /**
   * 注册快捷键
   */
  register(key: string, callback: () => void) {
    this.shortcuts.set(key.toLowerCase(), callback)
  }
  
  /**
   * 注销快捷键
   */
  unregister(key: string) {
    this.shortcuts.delete(key.toLowerCase())
  }
  
  /**
   * 处理键盘事件
   */
  handleKeydown = (event: KeyboardEvent) => {
    const key = this.getKeyString(event)
    const callback = this.shortcuts.get(key)
    
    if (callback) {
      event.preventDefault()
      callback()
    }
  }
  
  /**
   * 获取按键字符串
   */
  private getKeyString(event: KeyboardEvent): string {
    const parts: string[] = []
    
    if (event.ctrlKey) parts.push('ctrl')
    if (event.altKey) parts.push('alt')
    if (event.shiftKey) parts.push('shift')
    if (event.metaKey) parts.push('meta')
    
    parts.push(event.key.toLowerCase())
    
    return parts.join('+')
  }
  
  /**
   * 启用快捷键监听
   */
  enable() {
    document.addEventListener('keydown', this.handleKeydown)
  }
  
  /**
   * 禁用快捷键监听
   */
  disable() {
    document.removeEventListener('keydown', this.handleKeydown)
  }
}

// 全局实例
export const errorHandler = ErrorHandler.getInstance()
export const loadingManager = new LoadingManager()
export const keyboardShortcuts = new KeyboardShortcuts()

/**
 * 通用的异步操作包装器
 */
export async function withLoading<T>(
  operation: () => Promise<T>,
  loadingText: string = '处理中...',
  loadingKey: string = 'default'
): Promise<T> {
  loadingManager.show(loadingKey, { text: loadingText })
  
  try {
    const result = await operation()
    return result
  } catch (error) {
    errorHandler.handleApiError(error)
    throw error
  } finally {
    loadingManager.hide(loadingKey)
  }
}
