import { ref, onMounted, onUnmounted } from 'vue'

/**
 * 性能优化组合函数
 */
export function usePerformanceOptimization() {
  
  /**
   * 防抖函数
   */
  function debounce<T extends (...args: any[]) => any>(
    func: T,
    wait: number
  ): (...args: Parameters<T>) => void {
    let timeout: NodeJS.Timeout | null = null
    
    return (...args: Parameters<T>) => {
      if (timeout) {
        clearTimeout(timeout)
      }
      timeout = setTimeout(() => func(...args), wait)
    }
  }

  /**
   * 节流函数
   */
  function throttle<T extends (...args: any[]) => any>(
    func: T,
    wait: number
  ): (...args: Parameters<T>) => void {
    let inThrottle = false
    
    return (...args: Parameters<T>) => {
      if (!inThrottle) {
        func(...args)
        inThrottle = true
        setTimeout(() => inThrottle = false, wait)
      }
    }
  }

  /**
   * 虚拟滚动优化
   */
  function useVirtualScroll(itemHeight: number, containerHeight: number) {
    const scrollTop = ref(0)
    const visibleStart = ref(0)
    const visibleEnd = ref(0)
    const visibleCount = Math.ceil(containerHeight / itemHeight) + 2

    const updateVisibleRange = (scrollPosition: number) => {
      scrollTop.value = scrollPosition
      visibleStart.value = Math.floor(scrollPosition / itemHeight)
      visibleEnd.value = visibleStart.value + visibleCount
    }

    const getVisibleItems = <T>(items: T[]) => {
      return items.slice(visibleStart.value, visibleEnd.value)
    }

    const getOffsetY = () => {
      return visibleStart.value * itemHeight
    }

    return {
      scrollTop,
      visibleStart,
      visibleEnd,
      updateVisibleRange,
      getVisibleItems,
      getOffsetY
    }
  }

  /**
   * 图片懒加载
   */
  function useLazyLoad() {
    const observer = ref<IntersectionObserver | null>(null)
    const lazyImages = ref<Set<HTMLImageElement>>(new Set())

    const initLazyLoad = () => {
      if ('IntersectionObserver' in window) {
        observer.value = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const img = entry.target as HTMLImageElement
              const src = img.dataset.src
              if (src) {
                img.src = src
                img.removeAttribute('data-src')
                observer.value?.unobserve(img)
                lazyImages.value.delete(img)
              }
            }
          })
        })
      }
    }

    const addLazyImage = (img: HTMLImageElement) => {
      if (observer.value) {
        observer.value.observe(img)
        lazyImages.value.add(img)
      }
    }

    const cleanup = () => {
      if (observer.value) {
        lazyImages.value.forEach(img => {
          observer.value?.unobserve(img)
        })
        observer.value.disconnect()
        lazyImages.value.clear()
      }
    }

    onMounted(initLazyLoad)
    onUnmounted(cleanup)

    return {
      addLazyImage,
      cleanup
    }
  }

  /**
   * 内存泄漏防护
   */
  function useMemoryGuard() {
    const timers = ref<Set<NodeJS.Timeout>>(new Set())
    const intervals = ref<Set<NodeJS.Timeout>>(new Set())
    const eventListeners = ref<Array<{
      element: EventTarget
      event: string
      handler: EventListener
    }>>([])

    const safeSetTimeout = (callback: () => void, delay: number) => {
      const timer = setTimeout(() => {
        callback()
        timers.value.delete(timer)
      }, delay)
      timers.value.add(timer)
      return timer
    }

    const safeSetInterval = (callback: () => void, delay: number) => {
      const interval = setInterval(callback, delay)
      intervals.value.add(interval)
      return interval
    }

    const safeAddEventListener = (
      element: EventTarget,
      event: string,
      handler: EventListener,
      options?: boolean | AddEventListenerOptions
    ) => {
      element.addEventListener(event, handler, options)
      eventListeners.value.push({ element, event, handler })
    }

    const cleanup = () => {
      // 清理定时器
      timers.value.forEach(timer => clearTimeout(timer))
      timers.value.clear()

      // 清理间隔器
      intervals.value.forEach(interval => clearInterval(interval))
      intervals.value.clear()

      // 清理事件监听器
      eventListeners.value.forEach(({ element, event, handler }) => {
        element.removeEventListener(event, handler)
      })
      eventListeners.value = []
    }

    onUnmounted(cleanup)

    return {
      safeSetTimeout,
      safeSetInterval,
      safeAddEventListener,
      cleanup
    }
  }

  /**
   * 数据缓存
   */
  function useDataCache<T>(key: string, ttl: number = 300000) { // 默认5分钟
    const cache = new Map<string, { data: T; timestamp: number }>()

    const get = (cacheKey: string): T | null => {
      const item = cache.get(cacheKey)
      if (item && Date.now() - item.timestamp < ttl) {
        return item.data
      }
      cache.delete(cacheKey)
      return null
    }

    const set = (cacheKey: string, data: T) => {
      cache.set(cacheKey, { data, timestamp: Date.now() })
    }

    const clear = () => {
      cache.clear()
    }

    const has = (cacheKey: string): boolean => {
      const item = cache.get(cacheKey)
      if (item && Date.now() - item.timestamp < ttl) {
        return true
      }
      cache.delete(cacheKey)
      return false
    }

    return {
      get,
      set,
      clear,
      has
    }
  }

  /**
   * 组件渲染优化
   */
  function useRenderOptimization() {
    const renderCount = ref(0)
    const lastRenderTime = ref(0)

    const trackRender = () => {
      renderCount.value++
      lastRenderTime.value = Date.now()
    }

    const shouldUpdate = (newProps: any, oldProps: any): boolean => {
      // 浅比较优化
      if (typeof newProps !== 'object' || typeof oldProps !== 'object') {
        return newProps !== oldProps
      }

      const newKeys = Object.keys(newProps)
      const oldKeys = Object.keys(oldProps)

      if (newKeys.length !== oldKeys.length) {
        return true
      }

      return newKeys.some(key => newProps[key] !== oldProps[key])
    }

    return {
      renderCount,
      lastRenderTime,
      trackRender,
      shouldUpdate
    }
  }

  /**
   * 网络请求优化
   */
  function useRequestOptimization() {
    const pendingRequests = ref<Map<string, Promise<any>>>(new Map())

    const deduplicateRequest = async <T>(
      key: string,
      requestFn: () => Promise<T>
    ): Promise<T> => {
      if (pendingRequests.value.has(key)) {
        return pendingRequests.value.get(key) as Promise<T>
      }

      const promise = requestFn().finally(() => {
        pendingRequests.value.delete(key)
      })

      pendingRequests.value.set(key, promise)
      return promise
    }

    const batchRequests = <T>(
      requests: Array<() => Promise<T>>,
      batchSize: number = 5
    ): Promise<T[]> => {
      const batches: Array<Array<() => Promise<T>>> = []
      
      for (let i = 0; i < requests.length; i += batchSize) {
        batches.push(requests.slice(i, i + batchSize))
      }

      return batches.reduce(async (acc, batch) => {
        const results = await acc
        const batchResults = await Promise.all(batch.map(fn => fn()))
        return [...results, ...batchResults]
      }, Promise.resolve([] as T[]))
    }

    return {
      deduplicateRequest,
      batchRequests
    }
  }

  return {
    debounce,
    throttle,
    useVirtualScroll,
    useLazyLoad,
    useMemoryGuard,
    useDataCache,
    useRenderOptimization,
    useRequestOptimization
  }
}
