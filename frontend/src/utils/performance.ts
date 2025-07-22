/**
 * 性能优化工具函数
 */

import { ref, computed, watch, nextTick } from 'vue'
import type { Ref } from 'vue'

/**
 * 防抖函数
 * @param fn 要防抖的函数
 * @param delay 延迟时间（毫秒）
 * @returns 防抖后的函数
 */
export function debounce<T extends (...args: any[]) => any>(
  fn: T,
  delay: number = 300
): (...args: Parameters<T>) => void {
  let timeoutId: NodeJS.Timeout | null = null
  
  return (...args: Parameters<T>) => {
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
    
    timeoutId = setTimeout(() => {
      fn.apply(null, args)
    }, delay)
  }
}

/**
 * 节流函数
 * @param fn 要节流的函数
 * @param delay 延迟时间（毫秒）
 * @returns 节流后的函数
 */
export function throttle<T extends (...args: any[]) => any>(
  fn: T,
  delay: number = 300
): (...args: Parameters<T>) => void {
  let lastCall = 0
  
  return (...args: Parameters<T>) => {
    const now = Date.now()
    
    if (now - lastCall >= delay) {
      lastCall = now
      fn.apply(null, args)
    }
  }
}

/**
 * 虚拟滚动Hook
 * 用于处理大量数据的列表渲染
 */
export function useVirtualScroll<T>(
  items: Ref<T[]>,
  itemHeight: number = 50,
  containerHeight: number = 400
) {
  const scrollTop = ref(0)
  const visibleCount = Math.ceil(containerHeight / itemHeight) + 2
  
  const visibleItems = computed(() => {
    const startIndex = Math.floor(scrollTop.value / itemHeight)
    const endIndex = Math.min(startIndex + visibleCount, items.value.length)
    
    return {
      items: items.value.slice(startIndex, endIndex),
      startIndex,
      endIndex,
      offsetY: startIndex * itemHeight,
      totalHeight: items.value.length * itemHeight
    }
  })
  
  const handleScroll = throttle((event: Event) => {
    const target = event.target as HTMLElement
    scrollTop.value = target.scrollTop
  }, 16) // 60fps
  
  return {
    visibleItems,
    handleScroll,
    scrollTop
  }
}

/**
 * 懒加载Hook
 * 用于图片或组件的懒加载
 */
export function useLazyLoad() {
  const isVisible = ref(false)
  const targetRef = ref<HTMLElement>()
  
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          isVisible.value = true
          observer.disconnect()
        }
      })
    },
    {
      threshold: 0.1
    }
  )
  
  watch(targetRef, (el) => {
    if (el) {
      observer.observe(el)
    }
  })
  
  return {
    isVisible,
    targetRef
  }
}

/**
 * 缓存Hook
 * 用于缓存API请求结果
 */
export function useCache<T>(key: string, fetcher: () => Promise<T>, ttl: number = 5 * 60 * 1000) {
  const cache = new Map<string, { data: T; timestamp: number }>()
  const loading = ref(false)
  const data = ref<T | null>(null)
  const error = ref<Error | null>(null)
  
  const fetch = async (forceRefresh = false) => {
    const cached = cache.get(key)
    const now = Date.now()
    
    // 如果有缓存且未过期，直接返回缓存数据
    if (!forceRefresh && cached && (now - cached.timestamp) < ttl) {
      data.value = cached.data
      return cached.data
    }
    
    loading.value = true
    error.value = null
    
    try {
      const result = await fetcher()
      data.value = result
      cache.set(key, { data: result, timestamp: now })
      return result
    } catch (err) {
      error.value = err as Error
      throw err
    } finally {
      loading.value = false
    }
  }
  
  const clear = () => {
    cache.delete(key)
    data.value = null
    error.value = null
  }
  
  return {
    data,
    loading,
    error,
    fetch,
    clear
  }
}

/**
 * 批量操作Hook
 * 用于批量处理操作，减少API调用次数
 */
export function useBatchOperation<T>(
  batchSize: number = 10,
  delay: number = 1000
) {
  const queue = ref<T[]>([])
  const processing = ref(false)
  
  let timeoutId: NodeJS.Timeout | null = null
  
  const add = (item: T) => {
    queue.value.push(item)
    
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
    
    timeoutId = setTimeout(() => {
      processBatch()
    }, delay)
    
    if (queue.value.length >= batchSize) {
      processBatch()
    }
  }
  
  const processBatch = async () => {
    if (queue.value.length === 0 || processing.value) {
      return
    }
    
    processing.value = true
    const batch = queue.value.splice(0, batchSize)
    
    try {
      // 这里应该调用批量处理的API
      console.log('Processing batch:', batch)
      await new Promise(resolve => setTimeout(resolve, 100)) // 模拟API调用
    } catch (error) {
      console.error('Batch processing failed:', error)
      // 失败的项目重新加入队列
      queue.value.unshift(...batch)
    } finally {
      processing.value = false
      
      // 如果还有待处理的项目，继续处理
      if (queue.value.length > 0) {
        nextTick(() => processBatch())
      }
    }
  }
  
  return {
    add,
    queue: computed(() => queue.value),
    processing: computed(() => processing.value)
  }
}

/**
 * 内存监控Hook
 * 用于监控组件的内存使用情况
 */
export function useMemoryMonitor() {
  const memoryInfo = ref<any>(null)
  
  const updateMemoryInfo = () => {
    if ('memory' in performance) {
      memoryInfo.value = {
        usedJSHeapSize: (performance as any).memory.usedJSHeapSize,
        totalJSHeapSize: (performance as any).memory.totalJSHeapSize,
        jsHeapSizeLimit: (performance as any).memory.jsHeapSizeLimit
      }
    }
  }
  
  const startMonitoring = (interval: number = 5000) => {
    updateMemoryInfo()
    const intervalId = setInterval(updateMemoryInfo, interval)
    
    return () => clearInterval(intervalId)
  }
  
  return {
    memoryInfo,
    updateMemoryInfo,
    startMonitoring
  }
}

/**
 * 性能测量工具
 */
export class PerformanceMonitor {
  private marks: Map<string, number> = new Map()
  private measures: Map<string, number> = new Map()
  
  /**
   * 开始测量
   */
  start(name: string) {
    this.marks.set(name, performance.now())
  }
  
  /**
   * 结束测量
   */
  end(name: string): number {
    const startTime = this.marks.get(name)
    if (!startTime) {
      console.warn(`Performance mark "${name}" not found`)
      return 0
    }
    
    const duration = performance.now() - startTime
    this.measures.set(name, duration)
    this.marks.delete(name)
    
    return duration
  }
  
  /**
   * 获取测量结果
   */
  getMeasure(name: string): number | undefined {
    return this.measures.get(name)
  }
  
  /**
   * 获取所有测量结果
   */
  getAllMeasures(): Record<string, number> {
    return Object.fromEntries(this.measures)
  }
  
  /**
   * 清除所有测量数据
   */
  clear() {
    this.marks.clear()
    this.measures.clear()
  }
  
  /**
   * 记录性能日志
   */
  log(name?: string) {
    if (name) {
      const measure = this.measures.get(name)
      if (measure !== undefined) {
        console.log(`Performance [${name}]: ${measure.toFixed(2)}ms`)
      }
    } else {
      console.table(this.getAllMeasures())
    }
  }
}

// 全局性能监控实例
export const performanceMonitor = new PerformanceMonitor()

/**
 * 组件性能装饰器
 */
export function withPerformanceMonitor<T extends (...args: any[]) => any>(
  fn: T,
  name: string
): T {
  return ((...args: any[]) => {
    performanceMonitor.start(name)
    const result = fn.apply(null, args)
    
    if (result instanceof Promise) {
      return result.finally(() => {
        performanceMonitor.end(name)
      })
    } else {
      performanceMonitor.end(name)
      return result
    }
  }) as T
}
