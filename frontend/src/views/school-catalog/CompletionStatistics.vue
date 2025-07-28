<template>
  <div class="completion-statistics">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>实验完成率统计分析</h2>
      <p class="page-description">基于学校选定目录的实验完成率统计和分析</p>
    </div>

    <!-- 统计条件 -->
    <el-card class="filter-card" shadow="never">
      <template #header>
        <span>🔍 统计条件</span>
      </template>
      <el-form :model="filters" inline>
        <el-form-item label="统计范围">
          <el-select v-model="filters.scope" placeholder="选择统计范围" style="width: 150px">
            <el-option label="我的学校" value="my-school" />
            <el-option label="下级学校" value="subordinate" />
            <el-option label="指定学校" value="specific" />
          </el-select>
        </el-form-item>
        <el-form-item label="学科筛选" v-if="subjects.length > 0">
          <el-select v-model="filters.subject_id" placeholder="选择学科" clearable style="width: 150px">
            <el-option 
              v-for="subject in subjects" 
              :key="subject.id" 
              :label="subject.name" 
              :value="subject.id" 
            />
          </el-select>
        </el-form-item>
        <el-form-item label="年级筛选">
          <el-select v-model="filters.grade" placeholder="选择年级" clearable style="width: 120px">
            <el-option label="一年级" value="1" />
            <el-option label="二年级" value="2" />
            <el-option label="三年级" value="3" />
            <el-option label="四年级" value="4" />
            <el-option label="五年级" value="5" />
            <el-option label="六年级" value="6" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="loadStatistics" :loading="loading">
            <el-icon><Search /></el-icon>
            生成报告
          </el-button>
          <el-button @click="resetFilters">
            重置
          </el-button>
          <el-button type="success" @click="exportData" :disabled="!statisticsData">
            导出数据
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 统计概览 -->
    <el-card v-if="overviewData" class="overview-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>📊 统计概览</span>
          <el-tag :type="getCompletionRateType(overviewData.overall_completion_rate)" size="large">
            总体完成率: {{ overviewData.overall_completion_rate }}%
          </el-tag>
        </div>
      </template>
      <el-row :gutter="20">
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ overviewData.total_schools }}</div>
            <div class="stat-label">统计学校数</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ overviewData.total_experiments }}</div>
            <div class="stat-label">总实验数</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ overviewData.total_completed }}</div>
            <div class="stat-label">已完成数</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-item">
            <div class="stat-value">{{ overviewData.overall_completion_rate }}%</div>
            <div class="stat-label">完成率</div>
          </div>
        </el-col>
      </el-row>
    </el-card>

    <!-- 图表展示 -->
    <el-row :gutter="20" v-if="statisticsData">
      <el-col :span="12">
        <el-card class="chart-card" shadow="never">
          <template #header>
            <span>📈 完成率趋势图</span>
          </template>
          <div ref="trendChartRef" style="height: 300px;"></div>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card class="chart-card" shadow="never">
          <template #header>
            <span>🥧 分类完成率</span>
          </template>
          <div ref="pieChartRef" style="height: 300px;"></div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 详细数据表 -->
    <el-card v-if="rankingData.length > 0" class="table-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>🏆 学校完成率排行</span>
          <div class="header-actions">
            <el-button size="small" @click="recalculateCompletion" :loading="recalculating">
              重新计算
            </el-button>
          </div>
        </div>
      </template>
      
      <el-table :data="rankingData" stripe>
        <el-table-column prop="rank" label="排名" width="80" />
        <el-table-column prop="school_name" label="学校名称" min-width="200" />
        <el-table-column prop="total_experiments" label="总实验数" width="120" />
        <el-table-column prop="completed_experiments" label="已完成数" width="120" />
        <el-table-column prop="completion_rate" label="完成率" width="120">
          <template #default="{ row }">
            <el-tag :type="getCompletionRateType(row.completion_rate)">
              {{ row.completion_rate }}%
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150">
          <template #default="{ row }">
            <el-button size="small" @click="viewSchoolDetail(row)">
              查看详情
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 学校详情对话框 -->
    <SchoolDetailDialog
      v-model="showDetailDialog"
      :school-id="currentSchoolId"
      :school-name="currentSchoolName"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import * as echarts from 'echarts'
import { completionStatisticsApi, type StatisticsOverview, type RankingItem, type CompletionData } from '@/api/completionStatistics'
import { useAuthStore } from '@/stores/auth'
import SchoolDetailDialog from './components/SchoolDetailDialog.vue'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const recalculating = ref(false)
const filters = ref({
  scope: 'my-school',
  subject_id: '',
  grade: ''
})
const subjects = ref<any[]>([])
const overviewData = ref<StatisticsOverview['overview']>()
const statisticsData = ref<CompletionData>()
const rankingData = ref<RankingItem[]>([])
const showDetailDialog = ref(false)
const currentSchoolId = ref<number>()
const currentSchoolName = ref<string>()

// 图表引用
const trendChartRef = ref<HTMLElement>()
const pieChartRef = ref<HTMLElement>()
let trendChart: echarts.ECharts | null = null
let pieChart: echarts.ECharts | null = null

// 方法
const loadStatistics = async () => {
  try {
    loading.value = true
    
    // 根据统计范围获取数据
    if (filters.value.scope === 'my-school') {
      await loadMySchoolStatistics()
    } else {
      await loadOverviewStatistics()
    }
    
    // 渲染图表
    await nextTick()
    renderCharts()
    
  } catch (error) {
    console.error('获取统计数据失败:', error)
    ElMessage.error('获取统计数据失败')
  } finally {
    loading.value = false
  }
}

const loadMySchoolStatistics = async () => {
  const user = authStore.user
  if (!user || user.organization_level !== 5) {
    ElMessage.error('只有学校用户可以查看本校统计')
    return
  }
  
  const schoolId = user.organization_id
  const params = {
    subject_id: filters.value.subject_id || undefined,
    grade: filters.value.grade || undefined
  }
  
  const response = await completionStatisticsApi.getSchoolStatistics(schoolId, params)
  if (response.data.success) {
    statisticsData.value = response.data.data.completion_data
    // 构造概览数据
    overviewData.value = {
      total_schools: 1,
      total_experiments: statisticsData.value.overall.total_experiments,
      total_completed: statisticsData.value.overall.completed_experiments,
      overall_completion_rate: statisticsData.value.overall.completion_rate
    }
    // 构造排行数据
    rankingData.value = [{
      rank: 1,
      school_id: schoolId,
      school_name: user.school_name || '我的学校',
      total_experiments: statisticsData.value.overall.total_experiments,
      completed_experiments: statisticsData.value.overall.completed_experiments,
      completion_rate: statisticsData.value.overall.completion_rate
    }]
  }
}

const loadOverviewStatistics = async () => {
  const params = {
    subject_id: filters.value.subject_id || undefined,
    grade: filters.value.grade || undefined
  }
  
  const [overviewResponse, rankingResponse] = await Promise.all([
    completionStatisticsApi.getStatisticsOverview(params),
    completionStatisticsApi.getCompletionRanking({ ...params, limit: 20 })
  ])
  
  if (overviewResponse.data.success) {
    overviewData.value = overviewResponse.data.data.overview
  }
  
  if (rankingResponse.data.success) {
    rankingData.value = rankingResponse.data.data.ranking
  }
}

const renderCharts = () => {
  if (!statisticsData.value) return
  
  // 渲染趋势图
  if (trendChartRef.value) {
    trendChart = echarts.init(trendChartRef.value)
    const trendOption = {
      title: { text: '完成率趋势' },
      tooltip: { trigger: 'axis' },
      xAxis: { type: 'category', data: ['1月', '2月', '3月', '4月', '5月', '6月'] },
      yAxis: { type: 'value', max: 100 },
      series: [{
        data: [65, 70, 75, 78, 80, statisticsData.value.overall.completion_rate],
        type: 'line',
        smooth: true
      }]
    }
    trendChart.setOption(trendOption)
  }
  
  // 渲染饼图
  if (pieChartRef.value) {
    pieChart = echarts.init(pieChartRef.value)
    const pieData = Object.entries(statisticsData.value.by_type).map(([type, data]) => ({
      name: type,
      value: data.rate
    }))
    const pieOption = {
      title: { text: '按类型完成率' },
      tooltip: { trigger: 'item' },
      series: [{
        type: 'pie',
        radius: '50%',
        data: pieData
      }]
    }
    pieChart.setOption(pieOption)
  }
}

const resetFilters = () => {
  filters.value = {
    scope: 'my-school',
    subject_id: '',
    grade: ''
  }
  loadStatistics()
}

const exportData = () => {
  // 导出功能实现
  ElMessage.info('导出功能开发中...')
}

const recalculateCompletion = async () => {
  try {
    recalculating.value = true
    const schoolIds = rankingData.value.map(item => item.school_id)
    const response = await completionStatisticsApi.recalculateCompletion(schoolIds)
    
    if (response.data.success) {
      ElMessage.success('重新计算完成')
      loadStatistics()
    } else {
      ElMessage.error(response.data.message || '重新计算失败')
    }
  } catch (error) {
    console.error('重新计算失败:', error)
    ElMessage.error('重新计算失败')
  } finally {
    recalculating.value = false
  }
}

const viewSchoolDetail = (row: RankingItem) => {
  currentSchoolId.value = row.school_id
  currentSchoolName.value = row.school_name
  showDetailDialog.value = true
}

const getCompletionRateType = (rate: number) => {
  if (rate >= 90) return 'success'
  if (rate >= 80) return 'primary'
  if (rate >= 70) return 'warning'
  return 'danger'
}

// 生命周期
onMounted(() => {
  loadStatistics()
})
</script>

<style scoped>
.completion-statistics {
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

.filter-card,
.overview-card,
.chart-card,
.table-card {
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
</style>
