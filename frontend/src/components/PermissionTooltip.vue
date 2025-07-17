<template>
  <div class="permission-tooltip">
    <!-- 有权限时显示内容 -->
    <div v-if="hasPermission" class="permission-content">
      <slot></slot>
    </div>
    
    <!-- 无权限时显示小型提示 -->
    <el-tooltip 
      v-else
      :content="tooltipContent"
      placement="top"
      :show-after="300"
    >
      <div class="permission-denied-mini">
        <div class="denied-placeholder">
          <el-icon class="denied-icon" :size="size === 'small' ? 14 : 16">
            <Lock />
          </el-icon>
          <span class="denied-text" v-if="showText">权限不足</span>
        </div>
      </div>
    </el-tooltip>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Lock } from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'

interface Props {
  permission?: string | string[]
  role?: string | string[]
  organizationLevel?: number | number[]
  size?: 'small' | 'default'
  showText?: boolean
  customMessage?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'default',
  showText: false,
  customMessage: ''
})

const authStore = useAuthStore()

// 权限检查
const hasPermission = computed(() => {
  // 权限检查
  if (props.permission) {
    const permissions = Array.isArray(props.permission) ? props.permission : [props.permission]
    const hasAnyPermission = permissions.some(p => authStore.hasPermission(p))
    if (!hasAnyPermission) return false
  }

  // 角色检查
  if (props.role) {
    const roles = Array.isArray(props.role) ? props.role : [props.role]
    const hasAnyRole = roles.some(r => authStore.userInfo?.role === r)
    if (!hasAnyRole) return false
  }

  // 组织级别检查
  if (props.organizationLevel !== undefined) {
    const levels = Array.isArray(props.organizationLevel) ? props.organizationLevel : [props.organizationLevel]
    const userLevel = authStore.userInfo?.organization_level
    if (userLevel === undefined || !levels.includes(userLevel)) return false
  }

  return true
})

// 提示内容
const tooltipContent = computed(() => {
  if (props.customMessage) {
    return props.customMessage
  }

  let message = '您当前的权限不足以访问此功能'
  
  if (props.permission) {
    const permissions = Array.isArray(props.permission) ? props.permission : [props.permission]
    message += `\n需要权限: ${permissions.join(', ')}`
  }
  
  if (props.role) {
    const roles = Array.isArray(props.role) ? props.role : [props.role]
    message += `\n需要角色: ${roles.join(', ')}`
  }
  
  if (props.organizationLevel !== undefined) {
    const levels = Array.isArray(props.organizationLevel) ? props.organizationLevel : [props.organizationLevel]
    message += `\n需要组织级别: ${levels.join(', ')}`
  }
  
  return message
})
</script>

<style scoped>
.permission-tooltip {
  display: inline-block;
}

.permission-content {
  display: contents;
}

.permission-denied-mini {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: not-allowed;
}

.denied-placeholder {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 6px;
  border-radius: 4px;
  background-color: #f5f7fa;
  border: 1px solid #e4e7ed;
  color: #909399;
  font-size: 12px;
  transition: all 0.2s ease;
}

.denied-placeholder:hover {
  background-color: #ecf5ff;
  border-color: #b3d8ff;
  color: #409eff;
}

.denied-icon {
  opacity: 0.6;
}

.denied-text {
  font-size: 11px;
  white-space: nowrap;
}

/* 小尺寸样式 */
.permission-tooltip[data-size="small"] .denied-placeholder {
  padding: 1px 4px;
  font-size: 11px;
}

.permission-tooltip[data-size="small"] .denied-text {
  font-size: 10px;
}
</style>
