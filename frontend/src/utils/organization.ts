/**
 * 组织架构相关工具函数
 */

import type { OrganizationNode } from '@/api/organization'

/**
 * 解析学校ID
 * 如果是学校节点且ID格式为 'school_数字'，则提取数字部分
 * 否则返回原始ID
 */
export function parseSchoolId(node: OrganizationNode): number | string {
  // 如果是学校级别且ID是字符串格式
  if (node.level === 5 && typeof node.id === 'string' && node.id.startsWith('school_')) {
    const schoolId = node.id.replace('school_', '')
    const numericId = parseInt(schoolId, 10)
    return isNaN(numericId) ? node.id : numericId
  }
  
  // 如果节点有school_id字段，优先使用
  if (node.school_id !== undefined) {
    return node.school_id
  }
  
  return node.id
}

/**
 * 获取组织的真实ID
 * 用于API调用时传递正确的ID参数
 */
export function getOrganizationRealId(node: OrganizationNode): number | string {
  // 对于学校节点，使用解析后的学校ID
  if (node.level === 5 || node.type === 'school') {
    return parseSchoolId(node)
  }
  
  // 对于其他组织节点，直接使用ID
  return node.id
}

/**
 * 获取组织的类型
 * 用于API调用时传递正确的类型参数
 */
export function getOrganizationType(node: OrganizationNode): string {
  if (node.level === 5 || node.type === 'school') {
    return 'school'
  }
  
  return node.type || 'organization'
}

/**
 * 检查节点是否为学校节点
 */
export function isSchoolNode(node: OrganizationNode): boolean {
  return node.level === 5 || node.type === 'school'
}

/**
 * 获取学校搜索参数
 * 根据选中的组织节点生成正确的搜索参数
 */
export function getSchoolSearchParams(node: OrganizationNode) {
  if (isSchoolNode(node)) {
    return {
      school_id: parseSchoolId(node)
    }
  }
  
  return {
    organization_id: node.id,
    organization_level: node.level
  }
}
