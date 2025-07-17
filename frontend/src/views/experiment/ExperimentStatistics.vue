<template>
  <div class="experiment-statistics-page">
    <div class="page-header">
      <div class="header-content">
        <h2>实验统计</h2>
        <p>实验开出率、质量分析和趋势统计</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Download" @click="handleExport">
          导出报告
        </el-button>
        <el-button :icon="Refresh" @click="handleRefresh">
          刷新数据
        </el-button>
      </div>
    </div>

    <!-- 筛选条件 -->
    <div class="filter-section">
      <el-form :model="filterForm" inline>
        <el-form-item label="学校">
          <el-select
            v-model="filterForm.school_id"
            placeholder="请选择学校"
            clearable
            style="width: 200px"
          >
            <el-option
              v-for="school in schools"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学科">
          <el-select
            v-model="filterForm.subject_id"
            placeholder="请选择学科"
            clearable
            style="width: 150px"
          >
            <el-option
              v-for="subject in subjects"
              :key="subject.id"
              :label="subject.name"
              :value="subject.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="年级">
          <el-select
            v-model="filterForm.grade"
            placeholder="请选择年级"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="grade in grades"
              :key="grade"
              :label="`${grade}年级`"
              :value="grade"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="时间范围">
          <el-date-picker
            v-model="dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            style="width: 240px"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 统计卡片 -->
    <div class="stats-section">
      <el-row :gutter="20">
        <el-col :span="6" v-for="stat in statsCards" :key="stat.key">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon" :style="{ color: stat.color }">
                <el-icon :size="32">
                  <component :is="stat.icon" />
                </el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 图表区域 -->
    <div class="charts-section">
      <el-row :gutter="20">
        <!-- 开出率趋势图 -->
        <el-col :span="12">
          <el-card title="开出率趋势">
            <div ref="trendChart" style="height: 300px"></div>
          </el-card>
        </el-col>
        <!-- 实验类型分布 -->
        <el-col :span="12">
          <el-card title="实验类型分布">
            <div ref="typeChart" style="height: 300px"></div>
          </el-card>
        </el-col>
      </el-row>
      
      <el-row :gutter="20" style="margin-top: 20px">
        <!-- 年级统计 -->
        <el-col :span="12">
          <el-card title="年级统计">
            <div ref="gradeChart" style="height: 300px"></div>
          </el-card>
        </el-col>
        <!-- 学科统计 -->
        <el-col :span="12">
          <el-card title="学科统计">
            <div ref="subjectChart" style="height: 300px"></div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 详细数据表格 -->
    <div class="table-section">
      <el-card title="学科详细统计">
        <el-table
          v-loading="loading"
          :data="subjectTableData"
          stripe
          style="width: 100%"
        >
          <el-table-column prop="subject_name" label="学科名称" />
          <el-table-column prop="subject_code" label="学科代码" />
          <el-table-column prop="total_experiments" label="实验总数" />
          <el-table-column prop="avg_completion_rate" label="平均开出率">
            <template #default="{ row }">
              <el-progress
                :percentage="row.avg_completion_rate"
                :color="getProgressColor(row.avg_completion_rate)"
                :show-text="false"
                style="width: 100px"
              />
              <span style="margin-left: 10px">{{ row.avg_completion_rate }}%</span>
            </template>
          </el-table-column>
          <el-table-column prop="avg_quality_score" label="平均质量分">
            <template #default="{ row }">
              <el-rate
                v-model="row.avg_quality_score"
                disabled
                show-score
                text-color="#ff9900"
              />
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, nextTick, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { Download, Refresh, TrendCharts, PieChart, Histogram, DataAnalysis } from '@element-plus/icons-vue'
import * as echarts from 'echarts'
import dayjs from 'dayjs'
import {
  getExperimentCompletionRateApi,
  getExperimentTrendsApi,
  getSubjectStatisticsApi,
  getSubjectsApi,
  type ExperimentStatistics,
  type TrendData,
  type SubjectStatistics
} from '@/api/experiment'
import { getSchoolsApi, type School } from '@/api/user'

// 响应式数据
const loading = ref(false)
const schools = ref<School[]>([])
const subjects = ref([])
const dateRange = ref<[string, string]>([
  dayjs().subtract(1, 'month').format('YYYY-MM-DD'),
  dayjs().format('YYYY-MM-DD')
])

// 年级选项
const grades = ref([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])

// 筛选表单
const filterForm = reactive({
  school_id: undefined as number | undefined,
  subject_id: undefined as number | undefined,
  grade: undefined as number | undefined
})

// 统计卡片数据
const statsCards = ref([
  { key: 'total', label: '实验总数', value: '0', icon: DataAnalysis, color: '#409EFF' },
  { key: 'completed', label: '已完成', value: '0', icon: TrendCharts, color: '#67C23A' },
  { key: 'rate', label: '开出率', value: '0%', icon: PieChart, color: '#E6A23C' },
  { key: 'quality', label: '平均质量分', value: '0', icon: Histogram, color: '#F56C6C' }
])

// 图表引用
const trendChart = ref()
const typeChart = ref()
const gradeChart = ref()
const subjectChart = ref()

// 图表实例
let trendChartInstance: echarts.ECharts | null = null
let typeChartInstance: echarts.ECharts | null = null
let gradeChartInstance: echarts.ECharts | null = null
let subjectChartInstance: echarts.ECharts | null = null

// 表格数据
const subjectTableData = ref([])

// 获取进度条颜色
const getProgressColor = (percentage: number) => {
  if (percentage >= 90) return '#67C23A'
  if (percentage >= 70) return '#E6A23C'
  return '#F56C6C'
}

// 加载基础数据
const loadBaseData = async () => {
  try {
    // 加载学校列表
    const schoolsResponse = await getSchoolsApi()
    // 检查响应数据结构
    if (schoolsResponse.data && Array.isArray(schoolsResponse.data.data)) {
      schools.value = schoolsResponse.data.data
    } else if (schoolsResponse.data && Array.isArray(schoolsResponse.data)) {
      schools.value = schoolsResponse.data
    } else {
      console.warn('学校数据格式不正确:', schoolsResponse.data)
      schools.value = []
    }

    // 加载学科列表
    const subjectsResponse = await getSubjectsApi()
    // 检查响应数据结构
    if (subjectsResponse.data && Array.isArray(subjectsResponse.data.data)) {
      subjects.value = subjectsResponse.data.data
    } else if (subjectsResponse.data && Array.isArray(subjectsResponse.data)) {
      subjects.value = subjectsResponse.data
    } else {
      console.warn('学科数据格式不正确:', subjectsResponse.data)
      subjects.value = []
    }
  } catch (error) {
    console.error('加载基础数据失败:', error)
    schools.value = []
    subjects.value = []
  }
}

// 加载统计数据
const loadStatistics = async () => {
  loading.value = true
  try {
    const params = {
      ...filterForm,
      start_date: dateRange.value?.[0],
      end_date: dateRange.value?.[1]
    }

    // 加载开出率统计
    const completionResponse = await getExperimentCompletionRateApi(params)
    updateStatsCards(completionResponse.data)

    // 加载趋势数据
    const trendsResponse = await getExperimentTrendsApi(params)
    updateTrendChart(trendsResponse.data)

    // 加载学科统计
    const subjectResponse = await getSubjectStatisticsApi(params)
    updateSubjectData(subjectResponse.data)

  } catch (error) {
    console.error('加载统计数据失败:', error)
    ElMessage.error('加载统计数据失败')
  } finally {
    loading.value = false
  }
}

// 更新统计卡片
const updateStatsCards = (data: ExperimentStatistics) => {
  statsCards.value[0].value = data.total_experiments.toString()
  statsCards.value[1].value = data.completed_experiments.toString()
  statsCards.value[2].value = `${data.completion_rate}%`
  statsCards.value[3].value = data.avg_quality_score.toFixed(1)
}

// 更新趋势图表
const updateTrendChart = (data: TrendData) => {
  if (!trendChartInstance) return

  const option = {
    tooltip: {
      trigger: 'axis'
    },
    legend: {
      data: ['实验数量', '开出率']
    },
    xAxis: {
      type: 'category',
      data: data.trends.map(item => item.period)
    },
    yAxis: [
      {
        type: 'value',
        name: '实验数量'
      },
      {
        type: 'value',
        name: '开出率(%)',
        max: 100
      }
    ],
    series: [
      {
        name: '实验数量',
        type: 'bar',
        data: data.trends.map(item => item.total_count)
      },
      {
        name: '开出率',
        type: 'line',
        yAxisIndex: 1,
        data: data.trends.map(item => item.avg_completion_rate)
      }
    ]
  }

  trendChartInstance.setOption(option)
}

// 更新学科数据
const updateSubjectData = (data: SubjectStatistics) => {
  subjectTableData.value = data.statistics
}

// 初始化图表
const initCharts = () => {
  nextTick(() => {
    if (trendChart.value) {
      trendChartInstance = echarts.init(trendChart.value)
    }
    if (typeChart.value) {
      typeChartInstance = echarts.init(typeChart.value)
    }
    if (gradeChart.value) {
      gradeChartInstance = echarts.init(gradeChart.value)
    }
    if (subjectChart.value) {
      subjectChartInstance = echarts.init(subjectChart.value)
    }
  })
}

// 搜索
const handleSearch = () => {
  loadStatistics()
}

// 重置
const handleReset = () => {
  Object.assign(filterForm, {
    school_id: undefined,
    subject_id: undefined,
    grade: undefined
  })
  dateRange.value = [
    dayjs().subtract(1, 'month').format('YYYY-MM-DD'),
    dayjs().format('YYYY-MM-DD')
  ]
  loadStatistics()
}

// 刷新
const handleRefresh = () => {
  loadStatistics()
}

// 导出
const handleExport = () => {
  ElMessage.info('导出功能开发中...')
}

// 初始化
onMounted(() => {
  loadBaseData()
  initCharts()
  loadStatistics()
})
</script>

<style scoped>
.experiment-statistics-page {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.filter-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.stats-section {
  margin-bottom: 20px;
}

.stat-card {
  height: 120px;
}

.stat-content {
  display: flex;
  align-items: center;
  height: 100%;
}

.stat-icon {
  margin-right: 20px;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #303133;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.charts-section {
  margin-bottom: 20px;
}

.table-section {
  margin-bottom: 20px;
}
</style>
