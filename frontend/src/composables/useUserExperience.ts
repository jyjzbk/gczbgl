import { ref, nextTick } from 'vue'
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'

/**
 * 用户体验优化组合函数
 */
export function useUserExperience() {

  /**
   * 加载状态管理
   */
  function useLoading() {
    const loading = ref(false)
    const loadingText = ref('加载中...')
    let loadingInstance: any = null

    const showLoading = (text: string = '加载中...') => {
      loading.value = true
      loadingText.value = text
      loadingInstance = ElLoading.service({
        lock: true,
        text,
        background: 'rgba(0, 0, 0, 0.7)'
      })
    }

    const hideLoading = () => {
      loading.value = false
      if (loadingInstance) {
        loadingInstance.close()
        loadingInstance = null
      }
    }

    const withLoading = async <T>(
      asyncFn: () => Promise<T>,
      text: string = '处理中...'
    ): Promise<T> => {
      try {
        showLoading(text)
        return await asyncFn()
      } finally {
        hideLoading()
      }
    }

    return {
      loading,
      loadingText,
      showLoading,
      hideLoading,
      withLoading
    }
  }

  /**
   * 消息提示优化
   */
  function useMessage() {
    const showSuccess = (message: string, duration: number = 3000) => {
      ElMessage.success({
        message,
        duration,
        showClose: true
      })
    }

    const showError = (message: string, duration: number = 5000) => {
      ElMessage.error({
        message,
        duration,
        showClose: true
      })
    }

    const showWarning = (message: string, duration: number = 4000) => {
      ElMessage.warning({
        message,
        duration,
        showClose: true
      })
    }

    const showInfo = (message: string, duration: number = 3000) => {
      ElMessage.info({
        message,
        duration,
        showClose: true
      })
    }

    const confirm = async (
      message: string,
      title: string = '确认操作',
      options: any = {}
    ): Promise<boolean> => {
      try {
        await ElMessageBox.confirm(message, title, {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
          ...options
        })
        return true
      } catch {
        return false
      }
    }

    const prompt = async (
      message: string,
      title: string = '输入信息',
      options: any = {}
    ): Promise<string | null> => {
      try {
        const { value } = await ElMessageBox.prompt(message, title, {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          ...options
        })
        return value
      } catch {
        return null
      }
    }

    return {
      showSuccess,
      showError,
      showWarning,
      showInfo,
      confirm,
      prompt
    }
  }

  /**
   * 表单验证优化
   */
  function useFormValidation() {
    const validateField = async (formRef: any, field: string): Promise<boolean> => {
      try {
        await formRef.validateField(field)
        return true
      } catch {
        return false
      }
    }

    const validateForm = async (formRef: any): Promise<boolean> => {
      try {
        await formRef.validate()
        return true
      } catch {
        return false
      }
    }

    const clearValidation = (formRef: any, fields?: string[]) => {
      if (fields) {
        formRef.clearValidate(fields)
      } else {
        formRef.clearValidate()
      }
    }

    const scrollToError = async (formRef: any) => {
      await nextTick()
      const errorField = formRef.$el.querySelector('.is-error')
      if (errorField) {
        errorField.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        })
      }
    }

    return {
      validateField,
      validateForm,
      clearValidation,
      scrollToError
    }
  }

  /**
   * 数据导出优化
   */
  function useDataExport() {
    const exportToCSV = (data: any[], filename: string = 'export.csv') => {
      if (!data.length) {
        ElMessage.warning('没有数据可导出')
        return
      }

      const headers = Object.keys(data[0])
      const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
      ].join('\n')

      const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.download = filename
      link.click()
      URL.revokeObjectURL(link.href)
    }

    const exportToExcel = async (data: any[], filename: string = 'export.xlsx') => {
      try {
        // 动态导入 xlsx 库
        const XLSX = await import('xlsx')
        
        if (!data.length) {
          ElMessage.warning('没有数据可导出')
          return
        }

        const worksheet = XLSX.utils.json_to_sheet(data)
        const workbook = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1')
        XLSX.writeFile(workbook, filename)
      } catch (error) {
        ElMessage.error('导出失败，请检查数据格式')
      }
    }

    const exportToJSON = (data: any[], filename: string = 'export.json') => {
      if (!data.length) {
        ElMessage.warning('没有数据可导出')
        return
      }

      const jsonContent = JSON.stringify(data, null, 2)
      const blob = new Blob([jsonContent], { type: 'application/json' })
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.download = filename
      link.click()
      URL.revokeObjectURL(link.href)
    }

    return {
      exportToCSV,
      exportToExcel,
      exportToJSON
    }
  }

  /**
   * 页面状态管理
   */
  function usePageState() {
    const pageLoading = ref(false)
    const pageError = ref<string | null>(null)
    const pageEmpty = ref(false)

    const setLoading = (loading: boolean) => {
      pageLoading.value = loading
      if (loading) {
        pageError.value = null
        pageEmpty.value = false
      }
    }

    const setError = (error: string) => {
      pageError.value = error
      pageLoading.value = false
      pageEmpty.value = false
    }

    const setEmpty = (empty: boolean) => {
      pageEmpty.value = empty
      pageLoading.value = false
      pageError.value = null
    }

    const reset = () => {
      pageLoading.value = false
      pageError.value = null
      pageEmpty.value = false
    }

    return {
      pageLoading,
      pageError,
      pageEmpty,
      setLoading,
      setError,
      setEmpty,
      reset
    }
  }

  /**
   * 键盘快捷键
   */
  function useKeyboardShortcuts() {
    const shortcuts = ref<Map<string, () => void>>(new Map())

    const addShortcut = (key: string, callback: () => void) => {
      shortcuts.value.set(key, callback)
    }

    const removeShortcut = (key: string) => {
      shortcuts.value.delete(key)
    }

    const handleKeydown = (event: KeyboardEvent) => {
      const key = [
        event.ctrlKey && 'ctrl',
        event.altKey && 'alt',
        event.shiftKey && 'shift',
        event.key.toLowerCase()
      ].filter(Boolean).join('+')

      const callback = shortcuts.value.get(key)
      if (callback) {
        event.preventDefault()
        callback()
      }
    }

    const initShortcuts = () => {
      document.addEventListener('keydown', handleKeydown)
    }

    const destroyShortcuts = () => {
      document.removeEventListener('keydown', handleKeydown)
      shortcuts.value.clear()
    }

    return {
      addShortcut,
      removeShortcut,
      initShortcuts,
      destroyShortcuts
    }
  }

  /**
   * 响应式设计辅助
   */
  function useResponsive() {
    const isMobile = ref(false)
    const isTablet = ref(false)
    const isDesktop = ref(false)

    const updateScreenSize = () => {
      const width = window.innerWidth
      isMobile.value = width < 768
      isTablet.value = width >= 768 && width < 1024
      isDesktop.value = width >= 1024
    }

    const initResponsive = () => {
      updateScreenSize()
      window.addEventListener('resize', updateScreenSize)
    }

    const destroyResponsive = () => {
      window.removeEventListener('resize', updateScreenSize)
    }

    return {
      isMobile,
      isTablet,
      isDesktop,
      initResponsive,
      destroyResponsive
    }
  }

  return {
    useLoading,
    useMessage,
    useFormValidation,
    useDataExport,
    usePageState,
    useKeyboardShortcuts,
    useResponsive
  }
}
