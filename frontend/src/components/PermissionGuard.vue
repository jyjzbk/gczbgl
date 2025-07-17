<template>
  <div class="permission-guard">
    <!-- 有权限时显示内容 -->
    <div v-if="hasPermission" class="permission-content">
      <slot></slot>
    </div>
    
    <!-- 无权限时显示提示 -->
    <div v-else class="permission-denied">
      <el-empty 
        :image-size="120"
        :description="deniedMessage"
      >
        <template #image>
          <el-icon class="permission-icon"><Lock /></el-icon>
        </template>
        
        <template #description>
          <div class="denied-content">
            <h3>{{ deniedTitle }}</h3>
            <p>{{ deniedMessage }}</p>
            
            <!-- 权限要求说明 -->
            <div class="permission-requirement" v-if="showRequirement">
              <el-divider content-position="center">权限要求</el-divider>
              <div class="requirement-list">
                <div 
                  v-for="req in permissionRequirements" 
                  :key="req.type"
                  class="requirement-item"
                >
                  <el-icon :class="req.icon"></el-icon>
                  <span>{{ req.text }}</span>
                </div>
              </div>
            </div>
            
            <!-- 建议操作 -->
            <div class="suggested-actions" v-if="showActions">
              <el-button 
                type="primary" 
                size="small"
                @click="contactAdmin"
                :icon="Message"
              >
                联系管理员
              </el-button>
              <el-button 
                type="info" 
                size="small"
                @click="goBack"
                :icon="ArrowLeft"
              >
                返回上页
              </el-button>
            </div>
          </div>
        </template>
      </el-empty>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { 
  Lock, 
  Message, 
  ArrowLeft,
  User,
  Key,
  OfficeBuilding
} from '@element-plus/icons-vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useAuthStore } from '@/stores/auth'

interface Props {
  permission?: string | string[]
  role?: string | string[]
  organizationLevel?: number | number[]
  fallbackMessage?: string
  showRequirement?: boolean
  showActions?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showRequirement: true,
  showActions: true,
  fallbackMessage: '您没有权限访问此功能'
})

const authStore = useAuthStore()
const router = useRouter()

// 检查权限
const hasPermission = computed(() => {
  const userInfo = authStore.userInfo
  if (!userInfo) return false

  // 检查具体权限
  if (props.permission) {
    const permissions = Array.isArray(props.permission) ? props.permission : [props.permission]
    const hasRequiredPermission = permissions.some(perm => 
      authStore.permissions.includes(perm)
    )
    if (!hasRequiredPermission) return false
  }

  // 检查角色
  if (props.role) {
    const roles = Array.isArray(props.role) ? props.role : [props.role]
    if (!roles.includes(userInfo.role)) return false
  }

  // 检查组织级别
  if (props.organizationLevel !== undefined) {
    const levels = Array.isArray(props.organizationLevel) ? props.organizationLevel : [props.organizationLevel]
    if (!userInfo.organization_level || !levels.includes(userInfo.organization_level)) return false
  }

  return true
})

// 拒绝访问的标题
const deniedTitle = computed(() => {
  if (props.permission) return '权限不足'
  if (props.role) return '角色权限不足'
  if (props.organizationLevel !== undefined) return '组织级别权限不足'
  return '访问受限'
})

// 拒绝访问的消息
const deniedMessage = computed(() => {
  if (props.permission) {
    return '您当前的权限不足以访问此功能，请联系管理员申请相应权限。'
  }
  if (props.role) {
    return '此功能仅限特定角色访问，您当前的角色无法使用此功能。'
  }
  if (props.organizationLevel !== undefined) {
    return '此功能仅限特定组织级别访问，您当前的组织级别无法使用此功能。'
  }
  return props.fallbackMessage
})

// 权限要求说明
const permissionRequirements = computed(() => {
  const requirements = []

  if (props.permission) {
    const permissions = Array.isArray(props.permission) ? props.permission : [props.permission]
    requirements.push({
      type: 'permission',
      icon: 'Key',
      text: `需要权限: ${permissions.join(', ')}`
    })
  }

  if (props.role) {
    const roles = Array.isArray(props.role) ? props.role : [props.role]
    const roleNames = roles.map(role => getRoleLabel(role)).join(', ')
    requirements.push({
      type: 'role',
      icon: 'User',
      text: `需要角色: ${roleNames}`
    })
  }

  if (props.organizationLevel !== undefined) {
    const levels = Array.isArray(props.organizationLevel) ? props.organizationLevel : [props.organizationLevel]
    const levelNames = levels.map(level => getOrganizationLevelText(level)).join(', ')
    requirements.push({
      type: 'level',
      icon: 'OfficeBuilding',
      text: `需要组织级别: ${levelNames}`
    })
  }

  return requirements
})

// 获取角色标签
const getRoleLabel = (role: string) => {
  const roleMap: Record<string, string> = {
    'super_admin': '超级管理员',
    'province_admin': '省级管理员',
    'city_admin': '市级管理员',
    'county_admin': '区县管理员',
    'district_admin': '学区管理员',
    'school_admin': '学校管理员',
    'teacher': '教师',
    'student': '学生'
  }
  return roleMap[role] || role
}

// 获取组织级别文本
const getOrganizationLevelText = (level: number) => {
  const levelMap: Record<number, string> = {
    1: '省级',
    2: '市级',
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return levelMap[level] || `级别${level}`
}

// 联系管理员
const contactAdmin = async () => {
  try {
    await ElMessageBox.confirm(
      '是否要发送权限申请邮件给管理员？',
      '申请权限',
      {
        confirmButtonText: '发送申请',
        cancelButtonText: '取消',
        type: 'info'
      }
    )
    
    // 这里可以调用API发送权限申请
    ElMessage.success('权限申请已发送，请等待管理员审核')
  } catch {
    // 用户取消
  }
}

// 返回上页
const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push('/')
  }
}
</script>

<style scoped>
.permission-guard {
  width: 100%;
}

.permission-content {
  width: 100%;
}

.permission-denied {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  padding: 40px 20px;
}

.permission-icon {
  font-size: 120px;
  color: #e6a23c;
}

.denied-content {
  text-align: center;
  max-width: 500px;
}

.denied-content h3 {
  margin: 0 0 12px 0;
  font-size: 20px;
  color: #303133;
}

.denied-content p {
  margin: 0 0 20px 0;
  color: #606266;
  line-height: 1.6;
}

.permission-requirement {
  margin: 20px 0;
  padding: 16px;
  background-color: #fdf6ec;
  border-radius: 8px;
  border: 1px solid #faecd8;
}

.requirement-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.requirement-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #e6a23c;
}

.suggested-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .permission-denied {
    min-height: 300px;
    padding: 20px 10px;
  }
  
  .denied-content {
    max-width: 100%;
  }
  
  .suggested-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .suggested-actions .el-button {
    width: 200px;
  }
}
</style>
