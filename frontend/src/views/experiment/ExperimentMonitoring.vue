<template>
  <div class="experiment-monitoring">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>实验开出情况监控预警</h2>
      <p class="page-description">实时监控辖区内实验开出情况，智能预警超期未开实验</p>
    </div>

    <!-- 筛选条件 -->
    <el-card class="filter-card" shadow="never">
      <div class="filter-row">
        <el-form :model="filters" inline>
          <el-form-item label="学期">
            <el-select v-model="filters.semester" placeholder="选择学期" style="width: 150px">
              <el-option label="2024-2025上" value="2024-2025-1" />
              <el-option label="2024-2025下" value="2024-2025-2" />
              <el-option label="2025-2026上" value="2025-2026-1" />
            </el-select>
          </el-form-item>
          <el-form-item label="组织类型">
            <el-select v-model="filters.organizationType" placeholder="选择组织类型" style="width: 120px">
              <el-option label="省级" value="province" />
              <el-option label="市级" value="city" />
              <el-option label="区县级" value="district" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="loadDashboard" :loading="loading">
              <el-icon><Search /></el-icon>
              查询
            </el-button>
            <el-button @click="triggerAlertCheck" :loading="alertCheckLoading">
              <el-icon><Warning /></el-icon>
              手动检查预警
            </el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>

    <!-- 统计概览 -->
    <div class="stats-overview" v-if="dashboardData">
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon schools">
                <el-icon><School /></el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ dashboardData.region_summary.total_schools }}</div>
                <div class="stat-label">管辖学校数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon experiments">
                <el-icon><Document /></el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ dashboardData.region_summary.total_planned_experiments }}</div>
                <div class="stat-label">计划实验数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon completed">
                <el-icon><CircleCheck /></el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ dashboardData.region_summary.total_completed_experiments }}</div>
                <div class="stat-label">已完成实验</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon overdue">
                <el-icon><Warning /></el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ dashboardData.region_summary.total_overdue_experiments }}</div>
                <div class="stat-label">超期实验数</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 关键指标 -->
    <div class="key-metrics" v-if="dashboardData">
      <el-row :gutter="20">
        <el-col :span="8">
          <el-card class="metric-card">
            <div class="metric-header">
              <h3>平均完成率</h3>
            </div>
            <div class="metric-value">
              <span class="percentage">{{ dashboardData.region_summary.avg_completion_rate.toFixed(1) }}%</span>
              <el-progress 
                :percentage="dashboardData.region_summary.avg_completion_rate" 
                :color="getProgressColor(dashboardData.region_summary.avg_completion_rate)"
                :stroke-width="8"
              />
            </div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="metric-card">
            <div class="metric-header">
              <h3>平均超期率</h3>
            </div>
            <div class="metric-value">
              <span class="percentage">{{ dashboardData.region_summary.avg_overdue_rate.toFixed(1) }}%</span>
              <el-progress 
                :percentage="dashboardData.region_summary.avg_overdue_rate" 
                color="#f56c6c"
                :stroke-width="8"
              />
            </div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="metric-card">
            <div class="metric-header">
              <h3>平均质量评分</h3>
            </div>
            <div class="metric-value">
              <span class="percentage">{{ dashboardData.region_summary.avg_quality_score.toFixed(1) }}分</span>
              <el-progress 
                :percentage="dashboardData.region_summary.avg_quality_score" 
                :color="getScoreColor(dashboardData.region_summary.avg_quality_score)"
                :stroke-width="8"
              />
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 预警统计 -->
    <div class="alert-stats" v-if="dashboardData">
      <el-card>
        <template #header>
          <div class="card-header">
            <span>预警统计</span>
            <el-button type="text" @click="$router.push('/experiment-alerts')">
              查看详细预警 <el-icon><ArrowRight /></el-icon>
            </el-button>
          </div>
        </template>
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="alert-stat-item">
              <div class="alert-number total">{{ dashboardData.alert_statistics.total }}</div>
              <div class="alert-label">总预警数</div>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="alert-stat-item">
              <div class="alert-number unresolved">{{ dashboardData.alert_statistics.unresolved }}</div>
              <div class="alert-label">未解决</div>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="alert-stat-item">
              <div class="alert-number unread">{{ dashboardData.alert_statistics.unread }}</div>
              <div class="alert-label">未读</div>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="alert-stat-item">
              <div class="alert-number resolved">{{ dashboardData.alert_statistics.resolved }}</div>
              <div class="alert-label">已解决</div>
            </div>
          </el-col>
        </el-row>
      </el-card>
    </div>

    <!-- 学校监控详情表格 -->
    <div class="school-monitoring" v-if="dashboardData">
      <el-card>
        <template #header>
          <div class="card-header">
            <span>学校监控详情</span>
            <el-button type="primary" size="small" @click="exportData">
              <el-icon><Download /></el-icon>
              导出数据
            </el-button>
          </div>
        </template>
        <el-table 
          :data="dashboardData.region_summary.school_statistics" 
          style="width: 100%"
          :loading="loading"
        >
          <el-table-column prop="school_name" label="学校名称" min-width="200" />
          <el-table-column prop="completion_rate" label="完成率" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="getCompletionRateType(row.completion_rate)">
                {{ row.completion_rate.toFixed(1) }}%
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="overdue_rate" label="超期率" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="getOverdueRateType(row.overdue_rate)">
                {{ row.overdue_rate.toFixed(1) }}%
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="quality_score" label="质量评分" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="getQualityScoreType(row.quality_score)">
                {{ row.quality_score.toFixed(1) }}分
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="has_alerts" label="预警状态" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="row.has_alerts ? 'danger' : 'success'">
                {{ row.has_alerts ? '有预警' : '正常' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="150" align="center">
            <template #default="{ row }">
              <el-button type="text" size="small" @click="viewSchoolDetail(row)">
                查看详情
              </el-button>
              <el-button type="text" size="small" @click="viewSchoolAlerts(row)" v-if="row.has_alerts">
                查看预警
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>

    <!-- 加载状态 -->
    <div v-if="loading && !dashboardData" class="loading-container">
      <el-skeleton :rows="8" animated />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Search, Warning, School, Document, CircleCheck, 
  ArrowRight, Download 
} from '@element-plus/icons-vue'
import { experimentMonitoringApi, type DashboardData } from '@/api/experimentMonitoring'

// 响应式数据
const loading = ref(false)
const alertCheckLoading = ref(false)
const dashboardData = ref<DashboardData | null>(null)

// 筛选条件
const filters = reactive({
  semester: '2024-2025-2',
  organizationType: 'province'
})

// 生命周期
onMounted(() => {
  loadDashboard()
})

// 方法
const loadDashboard = async () => {
  try {
    loading.value = true
    const response = await experimentMonitoringApi.getDashboard(filters.semester)
    if (response.data.success) {
      dashboardData.value = response.data.data
    }
  } catch (error) {
    console.error('加载监控数据失败:', error)
    ElMessage.error('加载监控数据失败')
  } finally {
    loading.value = false
  }
}

const triggerAlertCheck = async () => {
  try {
    alertCheckLoading.value = true
    const response = await experimentMonitoringApi.triggerAlertCheck({
      semester: filters.semester,
      organization_type: filters.organizationType
    })
    if (response.data.success) {
      ElMessage.success(response.data.message)
      // 重新加载数据
      await loadDashboard()
    }
  } catch (error) {
    console.error('触发预警检查失败:', error)
    ElMessage.error('触发预警检查失败')
  } finally {
    alertCheckLoading.value = false
  }
}

// 工具方法
const getProgressColor = (percentage: number) => {
  if (percentage >= 90) return '#67c23a'
  if (percentage >= 80) return '#e6a23c'
  if (percentage >= 60) return '#f56c6c'
  return '#f56c6c'
}

const getScoreColor = (score: number) => {
  if (score >= 90) return '#67c23a'
  if (score >= 80) return '#e6a23c'
  if (score >= 60) return '#f56c6c'
  return '#f56c6c'
}

const getCompletionRateType = (rate: number) => {
  if (rate >= 90) return 'success'
  if (rate >= 80) return 'warning'
  return 'danger'
}

const getOverdueRateType = (rate: number) => {
  if (rate <= 5) return 'success'
  if (rate <= 15) return 'warning'
  return 'danger'
}

const getQualityScoreType = (score: number) => {
  if (score >= 90) return 'success'
  if (score >= 80) return 'warning'
  return 'danger'
}

const viewSchoolDetail = (school: any) => {
  // 跳转到学校详情页面
  console.log('查看学校详情:', school)
}

const viewSchoolAlerts = (school: any) => {
  // 跳转到学校预警页面
  console.log('查看学校预警:', school)
}

const exportData = () => {
  ElMessage.info('导出功能开发中...')
}
</script>

<style scoped>
.experiment-monitoring {
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

.filter-row {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stats-overview {
  margin-bottom: 20px;
}

.stat-card {
  height: 100px;
}

.stat-content {
  display: flex;
  align-items: center;
  height: 100%;
}

.stat-icon {
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

.stat-icon.schools {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.experiments {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-icon.completed {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon.overdue {
  background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 28px;
  font-weight: 600;
  color: #303133;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.key-metrics {
  margin-bottom: 20px;
}

.metric-card {
  height: 120px;
}

.metric-header {
  margin-bottom: 16px;
}

.metric-header h3 {
  margin: 0;
  font-size: 16px;
  color: #303133;
}

.metric-value {
  text-align: center;
}

.percentage {
  display: block;
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  margin-bottom: 8px;
}

.alert-stats {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.alert-stat-item {
  text-align: center;
  padding: 16px;
}

.alert-number {
  font-size: 32px;
  font-weight: 600;
  margin-bottom: 8px;
}

.alert-number.total {
  color: #409eff;
}

.alert-number.unresolved {
  color: #f56c6c;
}

.alert-number.unread {
  color: #e6a23c;
}

.alert-number.resolved {
  color: #67c23a;
}

.alert-label {
  font-size: 14px;
  color: #909399;
}

.school-monitoring {
  margin-bottom: 20px;
}

.loading-container {
  padding: 40px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .experiment-monitoring {
    padding: 16px;
  }

  .filter-row {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .stat-content {
    flex-direction: column;
    text-align: center;
  }

  .stat-icon {
    margin-right: 0;
    margin-bottom: 8px;
  }
}
</style>
