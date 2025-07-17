<template>
  <div class="organization-scope">
    <!-- 权限范围卡片 -->
    <el-card class="scope-card" shadow="hover">
      <template #header>
        <div class="card-header">
          <el-icon><OfficeBuilding /></el-icon>
          <span>数据权限范围</span>
        </div>
      </template>
      
      <div class="scope-content">
        <!-- 当前用户组织信息 -->
        <div class="current-org">
          <div class="org-info">
            <el-tag :type="getRoleType(authStore.userInfo?.role)" size="large">
              {{ getRoleLabel(authStore.userInfo?.role) }}
            </el-tag>
            <div class="org-details">
              <h4>{{ authStore.userInfo?.organization_name || authStore.userInfo?.school_name || '未设置' }}</h4>
              <p class="org-level">{{ getOrganizationLevelText(authStore.userInfo?.organization_level) }}</p>
            </div>
          </div>
        </div>

        <!-- 权限范围说明 -->
        <div class="scope-description">
          <el-divider content-position="left">
            <el-icon><Key /></el-icon>
            权限说明
          </el-divider>
          <div class="scope-text">
            <el-icon><View /></el-icon>
            <span>{{ getDataScopeText(authStore.userInfo?.organization_level) }}</span>
          </div>
          
          <!-- 具体权限列表 -->
          <div class="permission-details" v-if="showDetails">
            <el-divider content-position="left">具体权限</el-divider>
            <div class="permission-grid">
              <div 
                v-for="permission in displayPermissions" 
                :key="permission.code"
                class="permission-item"
              >
                <el-icon :class="permission.icon"></el-icon>
                <span>{{ permission.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 操作按钮 -->
        <div class="scope-actions">
          <el-button 
            type="primary" 
            size="small" 
            @click="showDetails = !showDetails"
            :icon="showDetails ? 'ArrowUp' : 'ArrowDown'"
          >
            {{ showDetails ? '收起详情' : '查看详情' }}
          </el-button>
          <el-button 
            type="info" 
            size="small" 
            @click="refreshScope"
            :icon="Refresh"
            :loading="refreshing"
          >
            刷新权限
          </el-button>
        </div>
      </div>
    </el-card>

    <!-- 数据统计卡片 -->
    <el-card class="stats-card" shadow="hover" v-if="showStats">
      <template #header>
        <div class="card-header">
          <el-icon><DataAnalysis /></el-icon>
          <span>数据统计</span>
        </div>
      </template>
      
      <div class="stats-content">
        <div class="stat-item" v-for="stat in dataStats" :key="stat.key">
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-label">{{ stat.label }}</div>
          <el-icon :class="stat.icon"></el-icon>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { 
  OfficeBuilding, 
  Key, 
  View, 
  ArrowUp, 
  ArrowDown, 
  Refresh,
  DataAnalysis,
  User,
  School,
  Monitor,
  Experiment
} from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import { useAuthStore } from '@/stores/auth'
import request from '@/api/request'

interface Props {
  showStats?: boolean
  compact?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showStats: true,
  compact: false
})

const authStore = useAuthStore()
const showDetails = ref(false)
const refreshing = ref(false)
const dataStats = ref([
  { key: 'users', label: '用户数量', value: '-', icon: 'User' },
  { key: 'schools', label: '学校数量', value: '-', icon: 'School' },
  { key: 'equipment', label: '设备数量', value: '-', icon: 'Monitor' },
  { key: 'experiments', label: '实验数量', value: '-', icon: 'Experiment' }
])

// 获取角色类型样式
const getRoleType = (role?: string) => {
  const roleMap: Record<string, string> = {
    'super_admin': 'danger',
    'province_admin': 'danger',
    'city_admin': 'warning',
    'county_admin': 'warning',
    'district_admin': 'info',
    'school_admin': 'info',
    'teacher': 'success',
    'student': ''
  }
  return roleMap[role || ''] || 'info'
}

// 获取角色标签
const getRoleLabel = (role?: string) => {
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
  return roleMap[role || ''] || '用户'
}

// 获取组织级别文本
const getOrganizationLevelText = (level?: number) => {
  if (!level) return '未知级别'
  const levelMap: Record<number, string> = {
    1: '省级',
    2: '市级',
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return levelMap[level] || '未知级别'
}

// 获取数据权限范围文本
const getDataScopeText = (level?: number) => {
  if (!level) return '未设置权限范围'
  
  const scopeMap: Record<number, string> = {
    1: '可查看全省所有数据',
    2: '可查看本市及下级数据',
    3: '可查看本区县及下级数据',
    4: '可查看本学区学校数据',
    5: '仅可查看本校数据'
  }
  return scopeMap[level] || '未知权限范围'
}

// 显示的权限列表
const displayPermissions = computed(() => {
  const permissions = authStore.permissions || []
  return permissions.slice(0, 8).map(permission => ({
    code: permission,
    name: getPermissionName(permission),
    icon: getPermissionIcon(permission)
  }))
})

// 获取权限名称
const getPermissionName = (permission: string) => {
  const nameMap: Record<string, string> = {
    'user.list': '用户列表',
    'user.create': '创建用户',
    'user.edit': '编辑用户',
    'user.delete': '删除用户',
    'school.list': '学校列表',
    'school.create': '创建学校',
    'equipment.list': '设备列表',
    'equipment.create': '创建设备',
    'experiment.list': '实验列表',
    'experiment.create': '创建实验'
  }
  return nameMap[permission] || permission
}

// 获取权限图标
const getPermissionIcon = (permission: string) => {
  if (permission.includes('user')) return 'User'
  if (permission.includes('school')) return 'School'
  if (permission.includes('equipment')) return 'Monitor'
  if (permission.includes('experiment')) return 'Experiment'
  return 'Key'
}

// 刷新权限信息
const refreshScope = async () => {
  refreshing.value = true
  try {
    await authStore.getUserInfo()
    await loadDataStats()
    ElMessage.success('权限信息已刷新')
  } catch (error) {
    ElMessage.error('刷新权限信息失败')
  } finally {
    refreshing.value = false
  }
}

// 加载数据统计
const loadDataStats = async () => {
  try {
    const [usersRes, schoolsRes, equipmentRes, experimentsRes] = await Promise.allSettled([
      request.get('/users'),
      request.get('/schools'),
      request.get('/equipments'),
      request.get('/experiment-catalogs')
    ])

    if (usersRes.status === 'fulfilled') {
      dataStats.value[0].value = usersRes.value.data.data?.length || 0
    }
    if (schoolsRes.status === 'fulfilled') {
      dataStats.value[1].value = schoolsRes.value.data.data?.length || 0
    }
    if (equipmentRes.status === 'fulfilled') {
      dataStats.value[2].value = equipmentRes.value.data.data?.items?.length || 0
    }
    if (experimentsRes.status === 'fulfilled') {
      dataStats.value[3].value = experimentsRes.value.data.data?.length || 0
    }
  } catch (error) {
    console.error('加载数据统计失败:', error)
  }
}

onMounted(() => {
  if (props.showStats) {
    loadDataStats()
  }
})
</script>

<style scoped>
.organization-scope {
  display: flex;
  gap: 16px;
  margin-bottom: 20px;
}

.scope-card {
  flex: 1;
  min-width: 300px;
}

.stats-card {
  width: 280px;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}

.scope-content {
  padding: 0;
}

.current-org {
  margin-bottom: 16px;
}

.org-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.org-details h4 {
  margin: 0 0 4px 0;
  font-size: 16px;
  color: #303133;
}

.org-level {
  margin: 0;
  font-size: 12px;
  color: #909399;
}

.scope-description {
  margin-bottom: 16px;
}

.scope-text {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background-color: #f0f9ff;
  border-radius: 6px;
  color: #1d4ed8;
  font-size: 14px;
}

.permission-details {
  margin-top: 12px;
}

.permission-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 8px;
}

.permission-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 8px;
  background-color: #f8f9fa;
  border-radius: 4px;
  font-size: 12px;
  color: #606266;
}

.scope-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.stats-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.stat-item {
  position: relative;
  text-align: center;
  padding: 16px 8px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #409eff;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  color: #909399;
}

.stat-item .el-icon {
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 16px;
  color: #c0c4cc;
}

@media (max-width: 768px) {
  .organization-scope {
    flex-direction: column;
  }
  
  .stats-card {
    width: 100%;
  }
  
  .stats-content {
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>
