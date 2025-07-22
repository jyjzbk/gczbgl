<template>
  <div v-if="hasPermission" class="permission-wrapper">
    <slot />
  </div>
  <div v-else-if="showFallback" class="permission-fallback">
    <slot name="fallback">
      <el-empty 
        :description="fallbackMessage || '您没有权限访问此内容'"
        :image-size="60"
      >
        <template #image>
          <el-icon size="60" color="#ddd"><Lock /></el-icon>
        </template>
      </el-empty>
    </slot>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Lock } from '@element-plus/icons-vue'
import { 
  PermissionController, 
  PermissionType, 
  ManagementLevel,
  type ExperimentCatalogPermission 
} from '@/utils/permission'

// Props
interface Props {
  // 权限类型
  permission?: PermissionType | PermissionType[]
  // 资源对象（用于检查具体权限）
  resource?: any
  // 管理级别要求
  requireLevel?: ManagementLevel
  // 角色要求
  requireRoles?: string[]
  // 是否显示无权限时的占位内容
  showFallback?: boolean
  // 无权限时的提示信息
  fallbackMessage?: string
  // 自定义权限检查函数
  customCheck?: () => boolean
}

const props = withDefaults(defineProps<Props>(), {
  showFallback: false
})

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
</script>

<style scoped>
.permission-wrapper {
  width: 100%;
}

.permission-fallback {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
  padding: 20px;
}

:deep(.el-empty__description) {
  color: #909399;
  font-size: 14px;
}
</style>
