/**
 * 日期时间工具函数
 */

import { format, parseISO, isValid, formatDistanceToNow } from 'date-fns'
import { zhCN } from 'date-fns/locale'

/**
 * 格式化日期时间
 * @param date 日期字符串或Date对象
 * @param formatStr 格式字符串，默认为 'yyyy-MM-dd HH:mm:ss'
 * @returns 格式化后的日期字符串
 */
export function formatDateTime(
  date: string | Date | null | undefined,
  formatStr: string = 'yyyy-MM-dd HH:mm:ss'
): string {
  if (!date) return '-'
  
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    
    if (!isValid(dateObj)) {
      return '-'
    }
    
    return format(dateObj, formatStr, { locale: zhCN })
  } catch (error) {
    console.warn('日期格式化失败:', error)
    return '-'
  }
}

/**
 * 格式化日期（不包含时间）
 * @param date 日期字符串或Date对象
 * @returns 格式化后的日期字符串 (yyyy-MM-dd)
 */
export function formatDate(date: string | Date | null | undefined): string {
  return formatDateTime(date, 'yyyy-MM-dd')
}

/**
 * 格式化时间（不包含日期）
 * @param date 日期字符串或Date对象
 * @returns 格式化后的时间字符串 (HH:mm:ss)
 */
export function formatTime(date: string | Date | null | undefined): string {
  return formatDateTime(date, 'HH:mm:ss')
}

/**
 * 格式化为相对时间
 * @param date 日期字符串或Date对象
 * @returns 相对时间字符串，如 "3分钟前"
 */
export function formatRelativeTime(date: string | Date | null | undefined): string {
  if (!date) return '-'
  
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    
    if (!isValid(dateObj)) {
      return '-'
    }
    
    return formatDistanceToNow(dateObj, { 
      addSuffix: true, 
      locale: zhCN 
    })
  } catch (error) {
    console.warn('相对时间格式化失败:', error)
    return '-'
  }
}

/**
 * 格式化为友好的日期时间显示
 * @param date 日期字符串或Date对象
 * @returns 友好的日期时间字符串
 */
export function formatFriendlyDateTime(date: string | Date | null | undefined): string {
  if (!date) return '-'
  
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    
    if (!isValid(dateObj)) {
      return '-'
    }
    
    const now = new Date()
    const diffInHours = (now.getTime() - dateObj.getTime()) / (1000 * 60 * 60)
    
    // 如果是今天，显示相对时间
    if (diffInHours < 24 && now.getDate() === dateObj.getDate()) {
      return formatRelativeTime(dateObj)
    }
    
    // 如果是一周内，显示星期几
    if (diffInHours < 24 * 7) {
      return format(dateObj, 'EEEE HH:mm', { locale: zhCN })
    }
    
    // 如果是今年，不显示年份
    if (now.getFullYear() === dateObj.getFullYear()) {
      return format(dateObj, 'MM-dd HH:mm', { locale: zhCN })
    }
    
    // 其他情况显示完整日期
    return format(dateObj, 'yyyy-MM-dd HH:mm', { locale: zhCN })
  } catch (error) {
    console.warn('友好时间格式化失败:', error)
    return '-'
  }
}

/**
 * 获取当前时间戳
 * @returns 当前时间戳（毫秒）
 */
export function getCurrentTimestamp(): number {
  return Date.now()
}

/**
 * 获取当前日期时间字符串
 * @param formatStr 格式字符串
 * @returns 当前日期时间字符串
 */
export function getCurrentDateTime(formatStr: string = 'yyyy-MM-dd HH:mm:ss'): string {
  return format(new Date(), formatStr, { locale: zhCN })
}

/**
 * 获取今天的开始时间
 * @returns 今天00:00:00的Date对象
 */
export function getStartOfToday(): Date {
  const now = new Date()
  return new Date(now.getFullYear(), now.getMonth(), now.getDate())
}

/**
 * 获取今天的结束时间
 * @returns 今天23:59:59的Date对象
 */
export function getEndOfToday(): Date {
  const now = new Date()
  return new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999)
}

/**
 * 检查日期是否为今天
 * @param date 要检查的日期
 * @returns 是否为今天
 */
export function isToday(date: string | Date): boolean {
  if (!date) return false
  
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    const today = new Date()
    
    return dateObj.getDate() === today.getDate() &&
           dateObj.getMonth() === today.getMonth() &&
           dateObj.getFullYear() === today.getFullYear()
  } catch (error) {
    return false
  }
}

/**
 * 检查日期是否为昨天
 * @param date 要检查的日期
 * @returns 是否为昨天
 */
export function isYesterday(date: string | Date): boolean {
  if (!date) return false
  
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    const yesterday = new Date()
    yesterday.setDate(yesterday.getDate() - 1)
    
    return dateObj.getDate() === yesterday.getDate() &&
           dateObj.getMonth() === yesterday.getMonth() &&
           dateObj.getFullYear() === yesterday.getFullYear()
  } catch (error) {
    return false
  }
}

/**
 * 计算两个日期之间的天数差
 * @param startDate 开始日期
 * @param endDate 结束日期
 * @returns 天数差
 */
export function getDaysDifference(
  startDate: string | Date,
  endDate: string | Date
): number {
  try {
    const start = typeof startDate === 'string' ? parseISO(startDate) : startDate
    const end = typeof endDate === 'string' ? parseISO(endDate) : endDate
    
    const diffInMs = end.getTime() - start.getTime()
    return Math.floor(diffInMs / (1000 * 60 * 60 * 24))
  } catch (error) {
    return 0
  }
}

/**
 * 解析日期字符串
 * @param dateString 日期字符串
 * @returns Date对象或null
 */
export function parseDate(dateString: string): Date | null {
  if (!dateString) return null
  
  try {
    const date = parseISO(dateString)
    return isValid(date) ? date : null
  } catch (error) {
    return null
  }
}

/**
 * 验证日期字符串是否有效
 * @param dateString 日期字符串
 * @returns 是否有效
 */
export function isValidDateString(dateString: string): boolean {
  if (!dateString) return false
  
  try {
    const date = parseISO(dateString)
    return isValid(date)
  } catch (error) {
    return false
  }
}

// 导出常用的日期格式常量
export const DATE_FORMATS = {
  DATE: 'yyyy-MM-dd',
  TIME: 'HH:mm:ss',
  DATETIME: 'yyyy-MM-dd HH:mm:ss',
  DATETIME_SHORT: 'MM-dd HH:mm',
  MONTH_DAY: 'MM-dd',
  YEAR_MONTH: 'yyyy-MM',
  ISO: "yyyy-MM-dd'T'HH:mm:ss.SSSxxx"
} as const

// 导出默认格式化函数（向后兼容）
export { formatDateTime as default }
