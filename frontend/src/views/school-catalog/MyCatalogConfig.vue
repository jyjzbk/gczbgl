<template>
  <div class="my-catalog-config">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>我的目录配置</h2>
      <p class="page-description">查看和管理学校实验目录配置</p>
    </div>

    <!-- 学校信息卡片 -->
    <el-card class="school-info-card" shadow="never" v-if="schoolInfo">
      <template #header>
        <div class="card-header">
          <span>🏫 学校信息</span>
        </div>
      </template>
      <el-descriptions :column="3" border>
        <el-descriptions-item label="学校名称">
          {{ schoolInfo.name }}
        </el-descriptions-item>
        <el-descriptions-item label="管理级别">
          <el-tag :type="getManagementLevelType(schoolInfo.management_level)">
            {{ getManagementLevelText(schoolInfo.management_level) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="配置状态">
          <el-tag :type="configData ? 'success' : 'warning'">
            {{ configData ? '✅ 已配置' : '❌ 未配置' }}
          </el-tag>
        </el-descriptions-item>
      </el-descriptions>
    </el-card>

    <!-- 当前配置信息 -->
    <el-card class="config-info-card" shadow="never" v-if="configData">
      <template #header>
        <div class="card-header">
          <span>⚙️ 当前配置信息</span>
          <div class="header-actions">
            <el-button
              v-if="catalogPermissions.canConfig && dataPermissions.canConfigureSchool(schoolId)"
              type="primary"
              size="small"
              @click="showConfigDialog = true"
            >
              修改配置
            </el-button>
            <el-button size="small" @click="loadConfigHistory">
              查看历史
            </el-button>
          </div>
        </div>
      </template>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="配置类型">
          <el-tag :type="configData.config_type === 'selection' ? 'success' : 'info'">
            {{ configData.config_type_name }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="目录来源">
          {{ configData.source_org_name }} ({{ configData.source_level_name }})
        </el-descriptions-item>
        <el-descriptions-item label="删除权限">
          <el-tag :type="configData.can_delete_experiments ? 'success' : 'danger'">
            {{ configData.can_delete_experiments ? '✅ 允许删除实验项目' : '❌ 不允许删除' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="配置时间">
          {{ formatDateTime(configData.configured_at) }}
        </el-descriptions-item>
        <el-descriptions-item label="配置人员">
          {{ configData.configuredBy?.name || '未知' }}
        </el-descriptions-item>
        <el-descriptions-item label="生效时间">
          {{ configData.effective_date || '立即生效' }}
        </el-descriptions-item>
        <el-descriptions-item label="配置理由" :span="2">
          {{ configData.config_reason || '无' }}
        </el-descriptions-item>
      </el-descriptions>
    </el-card>

    <!-- 无配置状态 -->
    <el-card class="no-config-card" shadow="never" v-else>
      <el-empty description="尚未配置实验目录">
        <el-button
          v-if="catalogPermissions.canConfig && dataPermissions.canConfigureSchool(schoolId)"
          type="primary"
          @click="showConfigDialog = true"
        >
          立即配置
        </el-button>
        <el-alert 
          v-else 
          title="该学校的实验目录由上级管理员统一指定" 
          type="info" 
          :closable="false"
        />
      </el-empty>
    </el-card>

    <!-- 目录统计概览 -->
    <el-card v-if="configData && configStats" class="stats-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>📊 目录统计概览</span>
          <el-button size="small" @click="loadConfigData" :loading="loading">
            <el-icon><Refresh /></el-icon>
            刷新
          </el-button>
        </div>
      </template>
      <el-row :gutter="20">
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ configStats.total_experiments }}</div>
            <div class="stat-label">总实验数</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ Object.keys(configStats.by_subject).length }}</div>
            <div class="stat-label">涉及学科</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ Object.keys(configStats.by_grade).length }}</div>
            <div class="stat-label">涉及年级</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ Object.keys(configStats.by_type).length }}</div>
            <div class="stat-label">实验类型</div>
          </div>
        </el-col>
      </el-row>
      
      <el-divider />
      
      <el-row :gutter="20">
        <el-col :span="12">
          <h4>按学科分布</h4>
          <div v-for="(count, subjectId) in configStats.by_subject" :key="subjectId" class="distribution-item">
            <span>学科 {{ subjectId }}:</span>
            <el-tag size="small">{{ count }} 个实验</el-tag>
          </div>
        </el-col>
        <el-col :span="12">
          <h4>按年级分布</h4>
          <div v-for="(count, grade) in configStats.by_grade" :key="grade" class="distribution-item">
            <span>{{ grade }}年级:</span>
            <el-tag size="small">{{ count }} 个实验</el-tag>
          </div>
        </el-col>
      </el-row>
    </el-card>

    <!-- 配置对话框 -->
    <ConfigDialog
      v-model="showConfigDialog"
      :school-id="schoolId"
      :current-config="configData"
      :permissions="permissions"
      @success="handleConfigSuccess"
    />

    <!-- 配置历史对话框 -->
    <ConfigHistoryDialog
      v-model="showHistoryDialog"
      :school-id="schoolId"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { Refresh } from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'
import { useSchoolCatalogPermissions } from '@/composables/useSchoolCatalogPermissions'
import { schoolCatalogConfigApi, type SchoolExperimentCatalogConfig, type ConfigStats, type UserPermissions } from '@/api/schoolCatalogConfig'
import ConfigDialog from './components/ConfigDialog.vue'
import ConfigHistoryDialog from './components/ConfigHistoryDialog.vue'

const authStore = useAuthStore()
const { permissions: catalogPermissions, dataPermissions } = useSchoolCatalogPermissions()

// 响应式数据
const loading = ref(false)
const schoolId = ref<number>()
const schoolInfo = ref<any>()
const configData = ref<SchoolExperimentCatalogConfig>()
const configStats = ref<ConfigStats>()
const permissions = ref<UserPermissions>({} as UserPermissions)
const showConfigDialog = ref(false)
const showHistoryDialog = ref(false)

// 计算属性
const currentUser = computed(() => authStore.user)

// 方法
const loadConfigData = async () => {
  try {
    loading.value = true
    const response = await schoolCatalogConfigApi.getMyConfig(schoolId.value)
    
    if (response.data.success) {
      schoolInfo.value = response.data.data.school
      configData.value = response.data.data.config
      configStats.value = response.data.data.stats
      permissions.value = response.data.data.permissions
      
      // 如果没有指定学校ID，使用当前用户的学校
      if (!schoolId.value && schoolInfo.value) {
        schoolId.value = schoolInfo.value.id
      }
    } else {
      ElMessage.error(response.data.message || '获取配置失败')
    }
  } catch (error) {
    console.error('获取配置失败:', error)
    ElMessage.error('获取配置失败')
  } finally {
    loading.value = false
  }
}

const loadConfigHistory = () => {
  showHistoryDialog.value = true
}

const handleConfigSuccess = () => {
  showConfigDialog.value = false
  loadConfigData()
  ElMessage.success('配置保存成功')
}

const getManagementLevelType = (level: number) => {
  const types = { 1: 'danger', 2: 'warning', 3: 'info', 4: 'success', 5: '' }
  return types[level as keyof typeof types] || ''
}

const getManagementLevelText = (level: number) => {
  const texts = { 1: '省直学校', 2: '市直学校', 3: '区县直管', 4: '学区学校', 5: '普通学校' }
  return texts[level as keyof typeof texts] || '未知'
}

const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}

// 生命周期
onMounted(() => {
  // 如果用户是学校级别，使用用户的学校ID
  if (currentUser.value?.organization_level === 5) {
    schoolId.value = currentUser.value.organization_id
  }
  loadConfigData()
})
</script>

<style scoped>
.my-catalog-config {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.school-info-card,
.config-info-card,
.no-config-card,
.stats-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.stat-item {
  text-align: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #409eff;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.distribution-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.distribution-item:last-child {
  border-bottom: none;
}
</style>
