<template>
  <el-button
    v-if="hasPermission"
    v-bind="$attrs"
    :disabled="disabled || loading"
    :loading="loading"
    @click="handleClick"
  >
    <slot />
  </el-button>
  <el-tooltip
    v-else-if="showTooltip"
    :content="tooltipContent || '您没有权限执行此操作'"
    placement="top"
  >
    <el-button
      v-bind="$attrs"
      disabled
      class="permission-disabled"
    >
      <slot />
    </el-button>
  </el-tooltip>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { 
  PermissionController, 
  PermissionType, 
  ManagementLevel 
} from '@/utils/permission'

// Props
interface Props {
  // 权限类型
  permission?: PermissionType | PermissionType[]
  // 资源对象
  resource?: any
  // 管理级别要求
  requireLevel?: ManagementLevel
  // 角色要求
  requireRoles?: string[]
  // 是否显示工具提示
  showTooltip?: boolean
  // 工具提示内容
  tooltipContent?: string
  // 自定义权限检查函数
  customCheck?: () => boolean
  // 按钮是否禁用
  disabled?: boolean
  // 按钮是否加载中
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showTooltip: true,
  disabled: false,
  loading: false
})

// Emits
const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

// 权限控制器
const permissionController = new PermissionController()

// 计算是否有权限
const hasPermission = computed(() => {
  // 如果有自定义检查函数，优先使用
  if (props.customCheck) {
    return props.customCheck()
  }
  
  // 检查管理级别要求
  if (props.requireLevel !== undefined) {
    const userLevel = permissionController.getCurrentUserLevel()
    if (userLevel > props.requireLevel) {
      return false
    }
  }
  
  // 检查角色要求
  if (props.requireRoles && props.requireRoles.length > 0) {
    const userRoles = permissionController.getCurrentUserRoles()
    const hasRequiredRole = props.requireRoles.some(role => userRoles.includes(role))
    if (!hasRequiredRole) {
      return false
    }
  }
  
  // 检查具体权限
  if (props.permission && props.resource) {
    const permissions = Array.isArray(props.permission) ? props.permission : [props.permission]
    
    for (const perm of permissions) {
      const resourcePermissions = permissionController.getExperimentCatalogPermissions(props.resource)
      
      switch (perm) {
        case PermissionType.VIEW:
          if (!resourcePermissions.canView) return false
          break
        case PermissionType.CREATE:
          if (!resourcePermissions.canCreate) return false
          break
        case PermissionType.EDIT:
          if (!resourcePermissions.canEdit) return false
          break
        case PermissionType.DELETE:
          if (!resourcePermissions.canDelete) return false
          break
        case PermissionType.COPY:
          if (!resourcePermissions.canCopy) return false
          break
        case PermissionType.INHERIT:
          if (!resourcePermissions.canInherit) return false
          break
        case PermissionType.MANAGE_EQUIPMENT:
          if (!resourcePermissions.canManageEquipment) return false
          break
        default:
          return false
      }
    }
  }
  
  return true
})

// 处理点击事件
const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading && hasPermission.value) {
    emit('click', event)
  }
}
</script>

<style scoped>
.permission-disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

:deep(.permission-disabled:hover) {
  opacity: 0.5;
}
</style>
