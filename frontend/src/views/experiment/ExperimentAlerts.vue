<template>
  <div class="experiment-alerts">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>实验预警管理</h2>
      <p class="page-description">管理和处理实验开出情况预警信息</p>
    </div>

    <!-- 筛选条件 -->
    <el-card class="filter-card" shadow="never">
      <el-form :model="filters" inline>
        <el-form-item label="预警类型">
          <el-select v-model="filters.alert_type" placeholder="选择预警类型" clearable style="width: 150px">
            <el-option label="超期未开" value="overdue" />
            <el-option label="完成率低" value="completion_rate" />
            <el-option label="质量评分低" value="quality_score" />
          </el-select>
        </el-form-item>
        <el-form-item label="预警级别">
          <el-select v-model="filters.alert_level" placeholder="选择预警级别" clearable style="width: 120px">
            <el-option label="低级" value="low" />
            <el-option label="中级" value="medium" />
            <el-option label="高级" value="high" />
            <el-option label="严重" value="critical" />
          </el-select>
        </el-form-item>
        <el-form-item label="处理状态">
          <el-select v-model="filters.is_resolved" placeholder="选择处理状态" clearable style="width: 120px">
            <el-option label="未解决" :value="false" />
            <el-option label="已解决" :value="true" />
          </el-select>
        </el-form-item>
        <el-form-item label="阅读状态">
          <el-select v-model="filters.is_read" placeholder="选择阅读状态" clearable style="width: 120px">
            <el-option label="未读" :value="false" />
            <el-option label="已读" :value="true" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="loadAlerts" :loading="loading">
            <el-icon><Search /></el-icon>
            查询
          </el-button>
          <el-button @click="resetFilters">
            <el-icon><Refresh /></el-icon>
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 预警统计 -->
    <div class="alert-summary">
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card class="summary-card">
            <div class="summary-content">
              <div class="summary-icon total">
                <el-icon><Warning /></el-icon>
              </div>
              <div class="summary-info">
                <div class="summary-value">{{ alertStats.total }}</div>
                <div class="summary-label">总预警数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="summary-card">
            <div class="summary-content">
              <div class="summary-icon unresolved">
                <el-icon><CircleClose /></el-icon>
              </div>
              <div class="summary-info">
                <div class="summary-value">{{ alertStats.unresolved }}</div>
                <div class="summary-label">未解决</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="summary-card">
            <div class="summary-content">
              <div class="summary-icon unread">
                <el-icon><View /></el-icon>
              </div>
              <div class="summary-info">
                <div class="summary-value">{{ alertStats.unread }}</div>
                <div class="summary-label">未读</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="summary-card">
            <div class="summary-content">
              <div class="summary-icon resolved">
                <el-icon><CircleCheck /></el-icon>
              </div>
              <div class="summary-info">
                <div class="summary-value">{{ alertStats.resolved }}</div>
                <div class="summary-label">已解决</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 预警列表 -->
    <el-card>
      <template #header>
        <div class="card-header">
          <span>预警列表</span>
          <div class="header-actions">
            <el-button type="primary" size="small" @click="batchMarkRead" :disabled="!selectedAlerts.length">
              批量标记已读
            </el-button>
            <el-button type="success" size="small" @click="batchResolve" :disabled="!selectedAlerts.length">
              批量解决
            </el-button>
          </div>
        </div>
      </template>

      <el-table 
        :data="alerts" 
        style="width: 100%"
        :loading="loading"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="alert_type" label="预警类型" width="120">
          <template #default="{ row }">
            <el-tag :type="getAlertTypeTagType(row.alert_type)">
              {{ getAlertTypeText(row.alert_type) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="alert_level" label="预警级别" width="100">
          <template #default="{ row }">
            <el-tag :type="getAlertLevelTagType(row.alert_level)">
              {{ getAlertLevelText(row.alert_level) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="预警标题" min-width="200" />
        <el-table-column prop="target_name" label="目标对象" width="150" />
        <el-table-column prop="created_at" label="创建时间" width="160">
          <template #default="{ row }">
            {{ formatDateTime(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column prop="is_read" label="阅读状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.is_read ? 'success' : 'warning'">
              {{ row.is_read ? '已读' : '未读' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="is_resolved" label="处理状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.is_resolved ? 'success' : 'danger'">
              {{ row.is_resolved ? '已解决' : '未解决' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" align="center">
          <template #default="{ row }">
            <el-button type="text" size="small" @click="viewAlert(row)">
              查看详情
            </el-button>
            <el-button 
              type="text" 
              size="small" 
              @click="markAsRead(row)" 
              v-if="!row.is_read"
            >
              标记已读
            </el-button>
            <el-button 
              type="text" 
              size="small" 
              @click="resolveAlert(row)" 
              v-if="!row.is_resolved"
            >
              解决
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 预警详情对话框 -->
    <el-dialog
      v-model="alertDetailVisible"
      title="预警详情"
      width="600px"
      :before-close="handleCloseDetail"
    >
      <div v-if="currentAlert" class="alert-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="预警类型">
            <el-tag :type="getAlertTypeTagType(currentAlert.alert_type)">
              {{ getAlertTypeText(currentAlert.alert_type) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="预警级别">
            <el-tag :type="getAlertLevelTagType(currentAlert.alert_level)">
              {{ getAlertLevelText(currentAlert.alert_level) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="预警标题" :span="2">
            {{ currentAlert.title }}
          </el-descriptions-item>
          <el-descriptions-item label="预警描述" :span="2">
            {{ currentAlert.description }}
          </el-descriptions-item>
          <el-descriptions-item label="目标对象">
            {{ currentAlert.target_name }}
          </el-descriptions-item>
          <el-descriptions-item label="创建时间">
            {{ formatDateTime(currentAlert.created_at) }}
          </el-descriptions-item>
          <el-descriptions-item label="阅读状态">
            <el-tag :type="currentAlert.is_read ? 'success' : 'warning'">
              {{ currentAlert.is_read ? '已读' : '未读' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="处理状态">
            <el-tag :type="currentAlert.is_resolved ? 'success' : 'danger'">
              {{ currentAlert.is_resolved ? '已解决' : '未解决' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="解决方案" :span="2" v-if="currentAlert.resolution">
            {{ currentAlert.resolution }}
          </el-descriptions-item>
          <el-descriptions-item label="解决时间" :span="2" v-if="currentAlert.resolved_at">
            {{ formatDateTime(currentAlert.resolved_at) }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="alertDetailVisible = false">关闭</el-button>
          <el-button 
            type="primary" 
            @click="markAsRead(currentAlert)" 
            v-if="currentAlert && !currentAlert.is_read"
          >
            标记已读
          </el-button>
          <el-button 
            type="success" 
            @click="resolveAlert(currentAlert)" 
            v-if="currentAlert && !currentAlert.is_resolved"
          >
            解决预警
          </el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 解决预警对话框 -->
    <el-dialog
      v-model="resolveDialogVisible"
      title="解决预警"
      width="500px"
    >
      <el-form :model="resolveForm" label-width="100px">
        <el-form-item label="解决方案" required>
          <el-input
            v-model="resolveForm.resolution"
            type="textarea"
            :rows="4"
            placeholder="请输入解决方案..."
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="resolveDialogVisible = false">取消</el-button>
          <el-button type="primary" @click="confirmResolve" :loading="resolving">
            确认解决
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Search, Refresh, Warning, CircleClose, View, CircleCheck 
} from '@element-plus/icons-vue'
import { experimentMonitoringApi, type ExperimentAlert } from '@/api/experimentMonitoring'

// 响应式数据
const loading = ref(false)
const resolving = ref(false)
const alerts = ref<ExperimentAlert[]>([])
const selectedAlerts = ref<ExperimentAlert[]>([])
const alertDetailVisible = ref(false)
const resolveDialogVisible = ref(false)
const currentAlert = ref<ExperimentAlert | null>(null)

// 筛选条件
const filters = reactive({
  alert_type: '',
  alert_level: '',
  is_resolved: null as boolean | null,
  is_read: null as boolean | null
})

// 分页
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 解决表单
const resolveForm = reactive({
  resolution: ''
})

// 预警统计
const alertStats = computed(() => {
  return {
    total: alerts.value.length,
    unresolved: alerts.value.filter(alert => !alert.is_resolved).length,
    unread: alerts.value.filter(alert => !alert.is_read).length,
    resolved: alerts.value.filter(alert => alert.is_resolved).length
  }
})

// 生命周期
onMounted(() => {
  loadAlerts()
})

// 方法
const loadAlerts = async () => {
  try {
    loading.value = true
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...filters
    }

    // 清理空值
    Object.keys(params).forEach(key => {
      if (params[key] === '' || params[key] === null) {
        delete params[key]
      }
    })

    const response = await experimentMonitoringApi.getAlerts(params)
    if (response.data.success) {
      alerts.value = response.data.data.data
      pagination.total = response.data.data.total
      pagination.current_page = response.data.data.current_page
    }
  } catch (error) {
    console.error('加载预警列表失败:', error)
    ElMessage.error('加载预警列表失败')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.alert_type = ''
  filters.alert_level = ''
  filters.is_resolved = null
  filters.is_read = null
  pagination.current_page = 1
  loadAlerts()
}

const handleSelectionChange = (selection: ExperimentAlert[]) => {
  selectedAlerts.value = selection
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadAlerts()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadAlerts()
}

const viewAlert = (alert: ExperimentAlert) => {
  currentAlert.value = alert
  alertDetailVisible.value = true

  // 如果是未读状态，自动标记为已读
  if (!alert.is_read) {
    markAsRead(alert, false)
  }
}

const markAsRead = async (alert: ExperimentAlert, showMessage = true) => {
  try {
    const response = await experimentMonitoringApi.markAlertAsRead([alert.id])
    if (response.data.success) {
      alert.is_read = true
      if (showMessage) {
        ElMessage.success('已标记为已读')
      }
    }
  } catch (error) {
    console.error('标记已读失败:', error)
    ElMessage.error('标记已读失败')
  }
}

const batchMarkRead = async () => {
  if (selectedAlerts.value.length === 0) {
    ElMessage.warning('请选择要标记的预警')
    return
  }

  try {
    const alertIds = selectedAlerts.value.map(alert => alert.id)
    const response = await experimentMonitoringApi.markAlertAsRead(alertIds)
    if (response.data.success) {
      selectedAlerts.value.forEach(alert => {
        alert.is_read = true
      })
      ElMessage.success('批量标记已读成功')
    }
  } catch (error) {
    console.error('批量标记已读失败:', error)
    ElMessage.error('批量标记已读失败')
  }
}

const resolveAlert = (alert: ExperimentAlert) => {
  currentAlert.value = alert
  resolveForm.resolution = ''
  resolveDialogVisible.value = true
}

const confirmResolve = async () => {
  if (!resolveForm.resolution.trim()) {
    ElMessage.warning('请输入解决方案')
    return
  }

  try {
    resolving.value = true
    const response = await experimentMonitoringApi.resolveAlert(currentAlert.value!.id, {
      resolution: resolveForm.resolution
    })
    if (response.data.success) {
      currentAlert.value!.is_resolved = true
      currentAlert.value!.resolution = resolveForm.resolution
      currentAlert.value!.resolved_at = new Date().toISOString()
      resolveDialogVisible.value = false
      ElMessage.success('预警已解决')
    }
  } catch (error) {
    console.error('解决预警失败:', error)
    ElMessage.error('解决预警失败')
  } finally {
    resolving.value = false
  }
}

const batchResolve = async () => {
  if (selectedAlerts.value.length === 0) {
    ElMessage.warning('请选择要解决的预警')
    return
  }

  try {
    const result = await ElMessageBox.prompt('请输入批量解决方案', '批量解决预警', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      inputType: 'textarea',
      inputPlaceholder: '请输入解决方案...'
    })

    if (result.value) {
      const promises = selectedAlerts.value.map(alert =>
        experimentMonitoringApi.resolveAlert(alert.id, { resolution: result.value })
      )

      await Promise.all(promises)

      selectedAlerts.value.forEach(alert => {
        alert.is_resolved = true
        alert.resolution = result.value
        alert.resolved_at = new Date().toISOString()
      })

      ElMessage.success('批量解决成功')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('批量解决失败:', error)
      ElMessage.error('批量解决失败')
    }
  }
}

const handleCloseDetail = () => {
  alertDetailVisible.value = false
  currentAlert.value = null
}

// 工具方法
const getAlertTypeText = (type: string) => {
  const typeMap = {
    'overdue': '超期未开',
    'completion_rate': '完成率低',
    'quality_score': '质量评分低'
  }
  return typeMap[type] || type
}

const getAlertTypeTagType = (type: string) => {
  const typeMap = {
    'overdue': 'danger',
    'completion_rate': 'warning',
    'quality_score': 'info'
  }
  return typeMap[type] || 'info'
}

const getAlertLevelText = (level: string) => {
  const levelMap = {
    'low': '低级',
    'medium': '中级',
    'high': '高级',
    'critical': '严重'
  }
  return levelMap[level] || level
}

const getAlertLevelTagType = (level: string) => {
  const levelMap = {
    'low': 'info',
    'medium': 'warning',
    'high': 'danger',
    'critical': 'danger'
  }
  return levelMap[level] || 'info'
}

const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}
</script>

<style scoped>
.experiment-alerts {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.filter-card {
  margin-bottom: 20px;
}

.alert-summary {
  margin-bottom: 20px;
}

.summary-card {
  height: 100px;
}

.summary-content {
  display: flex;
  align-items: center;
  height: 100%;
}

.summary-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  font-size: 24px;
  color: white;
}

.summary-icon.total {
  background: linear-gradient(135deg, #409eff 0%, #36cfc9 100%);
}

.summary-icon.unresolved {
  background: linear-gradient(135deg, #f56c6c 0%, #ff9a9e 100%);
}

.summary-icon.unread {
  background: linear-gradient(135deg, #e6a23c 0%, #ffd93d 100%);
}

.summary-icon.resolved {
  background: linear-gradient(135deg, #67c23a 0%, #95de64 100%);
}

.summary-info {
  flex: 1;
}

.summary-value {
  font-size: 28px;
  font-weight: 600;
  color: #303133;
  line-height: 1;
  margin-bottom: 4px;
}

.summary-label {
  font-size: 14px;
  color: #909399;
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

.pagination-container {
  margin-top: 20px;
  text-align: right;
}

.alert-detail {
  margin-bottom: 20px;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .experiment-alerts {
    padding: 16px;
  }

  .summary-content {
    flex-direction: column;
    text-align: center;
  }

  .summary-icon {
    margin-right: 0;
    margin-bottom: 8px;
  }

  .header-actions {
    flex-direction: column;
    gap: 4px;
  }

  .pagination-container {
    text-align: center;
  }
}
</style>
