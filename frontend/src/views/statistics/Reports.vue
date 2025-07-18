<template>
  <div class="statistics-reports">
    <div class="page-header">
      <h1>详细报表</h1>
      <p>实验教学管理平台详细统计报表</p>
    </div>

    <!-- 筛选条件 -->
    <el-card class="filter-card">
      <el-form :model="filterForm" inline>
        <el-form-item label="统计类型">
          <el-select v-model="filterForm.reportType" @change="onReportTypeChange">
            <el-option label="实验使用统计" value="experiment" />
            <el-option label="设备利用率统计" value="equipment" />
            <el-option label="用户活跃度统计" value="user" />
            <el-option label="组织绩效统计" value="performance" />
          </el-select>
        </el-form-item>
        <el-form-item label="时间范围">
          <el-date-picker
            v-model="filterForm.dateRange"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            @change="onDateRangeChange"
          />
        </el-form-item>
        <el-form-item label="学校">
          <el-select 
            v-model="filterForm.schoolId" 
            placeholder="选择学校"
            clearable
            @change="onSchoolChange"
          >
            <el-option
              v-for="school in schools"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="loadReportData">查询</el-button>
          <el-button @click="exportReport">导出</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 实验统计报表 -->
    <el-card v-if="filterForm.reportType === 'experiment'" class="report-card">
      <template #header>
        <span>实验使用统计报表</span>
      </template>
      
      <!-- 汇总信息 -->
      <div class="summary-section">
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ experimentData.summary?.total_experiments || 0 }}</h3>
              <p>总实验数</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ experimentData.summary?.completed_experiments || 0 }}</h3>
              <p>已完成实验</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ experimentData.summary?.completion_rate || 0 }}%</h3>
              <p>完成率</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ experimentData.summary?.avg_quality_score || 0 }}</h3>
              <p>平均质量评分</p>
            </div>
          </el-col>
        </el-row>
      </div>

      <!-- 月度趋势图表 -->
      <div class="chart-section">
        <h4>月度实验趋势</h4>
        <div class="chart-container" ref="experimentTrendChart"></div>
      </div>

      <!-- 学校排名表格 -->
      <div class="table-section">
        <h4>学校实验完成排名</h4>
        <el-table :data="experimentData.school_ranking" v-loading="loading">
          <el-table-column prop="name" label="学校名称" />
          <el-table-column prop="total" label="总实验数" />
          <el-table-column prop="completed" label="已完成" />
          <el-table-column prop="completion_rate" label="完成率">
            <template #default="scope">
              <el-progress 
                :percentage="scope.row.completion_rate" 
                :color="getProgressColor(scope.row.completion_rate)"
              />
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>

    <!-- 设备统计报表 -->
    <el-card v-if="filterForm.reportType === 'equipment'" class="report-card">
      <template #header>
        <span>设备利用率统计报表</span>
      </template>
      
      <!-- 汇总信息 -->
      <div class="summary-section">
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ equipmentData.summary?.total_equipment || 0 }}</h3>
              <p>设备总数</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ equipmentData.summary?.normal_equipment || 0 }}</h3>
              <p>正常设备</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ equipmentData.summary?.maintenance_equipment || 0 }}</h3>
              <p>维修中设备</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>¥{{ formatCurrency(equipmentData.summary?.total_value || 0) }}</h3>
              <p>设备总价值</p>
            </div>
          </el-col>
        </el-row>
      </div>

      <!-- 分类分布图表 -->
      <div class="chart-section">
        <h4>设备分类分布</h4>
        <div class="chart-container" ref="equipmentCategoryChart"></div>
      </div>

      <!-- 使用频率排名 -->
      <div class="table-section">
        <h4>设备使用频率排名</h4>
        <el-table :data="equipmentData.top_utilized" v-loading="loading">
          <el-table-column prop="name" label="设备名称" />
          <el-table-column prop="code" label="设备编号" />
          <el-table-column prop="borrow_count" label="借用次数" />
          <el-table-column prop="total_days" label="累计使用天数" />
        </el-table>
      </div>
    </el-card>

    <!-- 用户活跃度报表 -->
    <el-card v-if="filterForm.reportType === 'user'" class="report-card">
      <template #header>
        <span>用户活跃度统计报表</span>
      </template>



      <!-- 汇总信息 -->
      <div class="summary-section">
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ userActivityData.summary?.total_users || 0 }}</h3>
              <p>用户总数</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ userActivityData.summary?.active_users || 0 }}</h3>
              <p>活跃用户</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ userActivityData.summary?.activity_rate || 0 }}%</h3>
              <p>活跃率</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ userActivityData.summary?.never_login_users || 0 }}</h3>
              <p>从未登录用户</p>
            </div>
          </el-col>
        </el-row>
      </div>

      <!-- 角色分布图表 -->
      <div class="chart-section">
        <h4>用户角色分布</h4>
        <div class="chart-container" ref="userRoleChart"></div>
      </div>

      <!-- 最近活跃用户 -->
      <div class="table-section">
        <h4>最近活跃用户</h4>
        <el-table :data="userActivityData.recent_active" v-loading="loading">
          <el-table-column prop="real_name" label="用户姓名" />
          <el-table-column prop="role" label="角色" />
          <el-table-column prop="last_login_at" label="最后登录时间">
            <template #default="scope">
              {{ formatDateTime(scope.row.last_login_at) }}
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>

    <!-- 组织绩效报表 -->
    <el-card v-if="filterForm.reportType === 'performance'" class="report-card">
      <template #header>
        <span>组织绩效统计报表</span>
      </template>
      
      <!-- 汇总信息 -->
      <div class="summary-section">
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ performanceData.summary?.total_schools || 0 }}</h3>
              <p>学校总数</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ performanceData.summary?.avg_completion_rate || 0 }}%</h3>
              <p>平均完成率</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>{{ performanceData.summary?.avg_quality_score || 0 }}</h3>
              <p>平均质量评分</p>
            </div>
          </el-col>
          <el-col :span="6">
            <div class="summary-item">
              <h3>¥{{ formatCurrency(performanceData.summary?.total_equipment_value || 0) }}</h3>
              <p>设备总价值</p>
            </div>
          </el-col>
        </el-row>
      </div>

      <!-- 绩效排名表格 -->
      <div class="table-section">
        <h4>学校绩效排名</h4>
        <el-table :data="performanceData.rankings" v-loading="loading">
          <el-table-column type="index" label="排名" width="80" />
          <el-table-column prop="name" label="学校名称" />
          <el-table-column prop="total_experiments" label="实验数" />
          <el-table-column prop="completion_rate" label="完成率">
            <template #default="scope">
              {{ scope.row.completion_rate }}%
            </template>
          </el-table-column>
          <el-table-column prop="avg_quality_score" label="质量评分" />
          <el-table-column prop="total_equipment" label="设备数" />
          <el-table-column prop="equipment_value" label="设备价值">
            <template #default="scope">
              ¥{{ formatCurrency(scope.row.equipment_value) }}
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import * as echarts from 'echarts'
import { statisticsApi } from '@/api/statistics'
import { getSchoolOptionsApi } from '@/api/school'
import { useAuthStore } from '@/stores/auth'
import type { ExperimentStats, EquipmentStats, UserActivityStats, OrganizationPerformance } from '@/api/statistics'

// 响应式数据
const loading = ref(false)
const schools = ref<any[]>([])
const filterForm = ref({
  reportType: 'experiment',
  dateRange: [
    new Date(Date.now() - 90 * 24 * 60 * 60 * 1000), // 90天前
    new Date()
  ] as [Date, Date],
  schoolId: undefined as number | undefined
})

const experimentData = ref<ExperimentStats>({
  summary: {
    total_experiments: 0,
    completed_experiments: 0,
    completion_rate: 0,
    avg_quality_score: 0,
    excellent_rate: 0
  },
  quality_distribution: {
    excellent: 0,
    good: 0,
    poor: 0
  },
  monthly_trend: [],
  school_ranking: []
})

const equipmentData = ref<EquipmentStats>({
  summary: {
    total_equipment: 0,
    normal_equipment: 0,
    maintenance_equipment: 0,
    scrapped_equipment: 0,
    total_value: 0
  },
  status_distribution: [],
  category_distribution: [],
  top_utilized: []
})

const userActivityData = ref<UserActivityStats>({
  summary: {
    total_users: 0,
    active_users: 0,
    activity_rate: 0,
    never_login_users: 0
  },
  role_distribution: [],
  level_distribution: [],
  recent_active: []
})

const performanceData = ref<OrganizationPerformance>({
  school_performance: [],
  rankings: [],
  summary: {
    total_schools: 0,
    avg_completion_rate: 0,
    avg_quality_score: 0,
    total_equipment_value: 0
  }
})

// 图表引用
const experimentTrendChart = ref<HTMLElement>()
const equipmentCategoryChart = ref<HTMLElement>()
const userRoleChart = ref<HTMLElement>()

// 图表实例
let experimentTrendChartInstance: echarts.ECharts | null = null
let equipmentCategoryChartInstance: echarts.ECharts | null = null
let userRoleChartInstance: echarts.ECharts | null = null

// 加载报表数据
const loadReportData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: formatDate(filterForm.value.dateRange[0]),
      end_date: formatDate(filterForm.value.dateRange[1]),
      school_id: filterForm.value.schoolId
    }

    switch (filterForm.value.reportType) {
      case 'experiment':
        await loadExperimentData(params)
        break
      case 'equipment':
        await loadEquipmentData(params)
        break
      case 'user':
        await loadUserActivityData(params)
        break
      case 'performance':
        await loadPerformanceData(params)
        break
    }
  } catch (error) {
    console.error('加载报表数据失败:', error)
    ElMessage.error('加载报表数据失败')
  } finally {
    loading.value = false
  }
}

// 加载实验数据
const loadExperimentData = async (params: any) => {
  try {
    const response = await statisticsApi.getExperimentStats(params)
    if (response && response.success) {
      experimentData.value = response.data
      await nextTick()
      renderExperimentTrendChart()
    }
  } catch (error) {
    console.error('Error loading experiment data:', error)
    ElMessage.error('加载实验统计数据失败')
  }
}

// 加载设备数据
const loadEquipmentData = async (params: any) => {
  try {
    const response = await statisticsApi.getEquipmentStats(params)
    if (response && response.success) {
      equipmentData.value = response.data
      await nextTick()
      renderEquipmentCategoryChart()
    }
  } catch (error) {
    console.error('Error loading equipment data:', error)
    ElMessage.error('加载设备统计数据失败')
  }
}

// 加载用户活跃度数据
const loadUserActivityData = async (params: any) => {
  try {
    const response = await statisticsApi.getUserActivityStats(params)
    if (response && response.success) {
      userActivityData.value = response.data
      await nextTick()
      renderUserRoleChart()
    }
  } catch (error) {
    console.error('Error loading user activity data:', error)
    ElMessage.error('加载用户活跃度数据失败')
  }
}

// 加载绩效数据
const loadPerformanceData = async (params: any) => {
  try {
    const response = await statisticsApi.getOrganizationPerformance(params)
    if (response && response.success) {
      performanceData.value = response.data
    }
  } catch (error) {
    console.error('Error loading performance data:', error)
    ElMessage.error('加载组织绩效数据失败')
  }
}

// 渲染实验趋势图表
const renderExperimentTrendChart = () => {
  if (!experimentTrendChart.value) return
  
  if (experimentTrendChartInstance) {
    experimentTrendChartInstance.dispose()
  }
  
  experimentTrendChartInstance = echarts.init(experimentTrendChart.value)
  
  const monthlyData = experimentData.value.monthly_trend || []
  
  const option = {
    tooltip: {
      trigger: 'axis'
    },
    legend: {
      data: ['总实验数', '已完成实验']
    },
    xAxis: {
      type: 'category',
      data: monthlyData.map(item => item.month)
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name: '总实验数',
        type: 'line',
        data: monthlyData.map(item => item.total)
      },
      {
        name: '已完成实验',
        type: 'line',
        data: monthlyData.map(item => item.completed)
      }
    ]
  }
  
  experimentTrendChartInstance.setOption(option)
}

// 渲染设备分类图表
const renderEquipmentCategoryChart = () => {
  if (!equipmentCategoryChart.value) return
  
  if (equipmentCategoryChartInstance) {
    equipmentCategoryChartInstance.dispose()
  }
  
  equipmentCategoryChartInstance = echarts.init(equipmentCategoryChart.value)
  
  const categoryData = equipmentData.value.category_distribution || []
  
  const option = {
    tooltip: {
      trigger: 'item'
    },
    legend: {
      orient: 'vertical',
      left: 'left'
    },
    series: [
      {
        name: '设备分类',
        type: 'pie',
        radius: '50%',
        data: categoryData.map(item => ({
          value: item.count,
          name: item.category_name
        }))
      }
    ]
  }
  
  equipmentCategoryChartInstance.setOption(option)
}

// 渲染用户角色图表
const renderUserRoleChart = () => {
  if (!userRoleChart.value) return
  
  if (userRoleChartInstance) {
    userRoleChartInstance.dispose()
  }
  
  userRoleChartInstance = echarts.init(userRoleChart.value)
  
  const roleData = userActivityData.value.role_distribution || []
  
  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'shadow'
      }
    },
    xAxis: {
      type: 'category',
      data: roleData.map(item => item.role)
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name: '用户数量',
        type: 'bar',
        data: roleData.map(item => item.count)
      }
    ]
  }
  
  userRoleChartInstance.setOption(option)
}

// 工具函数
const formatDate = (date: Date) => {
  return date.toISOString().split('T')[0]
}

const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('zh-CN').format(value || 0)
}

const getProgressColor = (percentage: number) => {
  if (percentage >= 80) return '#67c23a'
  if (percentage >= 60) return '#e6a23c'
  return '#f56c6c'
}

// 事件处理
const onReportTypeChange = () => {
  loadReportData()
}

const onDateRangeChange = () => {
  loadReportData()
}

const onSchoolChange = () => {
  loadReportData()
}

const exportReport = () => {
  ElMessage.info('导出功能开发中...')
}

// 加载学校选项
const loadSchoolOptions = async () => {
  try {
    const response = await getSchoolOptionsApi()

    if (response && response.code === 200 && response.data) {
      // 处理后端返回的格式 {code: 200, message: '获取成功', data: [...]}
      schools.value = response.data || []
    } else if (response && response.data) {
      // 兼容其他可能的格式
      schools.value = response.data || []
    } else {
      schools.value = []
    }
  } catch (error) {
    console.error('加载学校选项失败:', error)
    ElMessage.error('加载学校选项失败')
    schools.value = []
  }
}

// 生命周期
onMounted(() => {
  loadSchoolOptions()
  loadReportData()
})
</script>

<style scoped>
.statistics-reports {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h1 {
  margin: 0 0 8px 0;
  font-size: 24px;
  color: #303133;
}

.page-header p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.filter-card {
  margin-bottom: 20px;
}

.report-card {
  margin-bottom: 20px;
}

.summary-section {
  margin-bottom: 30px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.summary-item {
  text-align: center;
}

.summary-item h3 {
  margin: 0 0 8px 0;
  font-size: 32px;
  font-weight: bold;
  color: #409eff;
}

.summary-item p {
  margin: 0;
  color: #606266;
  font-size: 14px;
}

.chart-section {
  margin-bottom: 30px;
}

.chart-section h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  color: #303133;
}

.chart-container {
  height: 300px;
}

.table-section h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  color: #303133;
}
</style>
