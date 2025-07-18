<template>
  <div class="statistics-dashboard">
    <div class="page-header">
      <h1>统计报表</h1>
      <p>实验教学管理平台数据统计与分析</p>
    </div>

    <!-- 概览卡片 -->
    <div class="overview-cards">
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card class="overview-card">
            <div class="card-content">
              <div class="card-icon schools">
                <el-icon><School /></el-icon>
              </div>
              <div class="card-info">
                <h3>{{ dashboardData.total_schools || 0 }}</h3>
                <p>学校总数</p>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="overview-card">
            <div class="card-content">
              <div class="card-icon users">
                <el-icon><User /></el-icon>
              </div>
              <div class="card-info">
                <h3>{{ dashboardData.total_users || 0 }}</h3>
                <p>用户总数</p>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="overview-card">
            <div class="card-content">
              <div class="card-icon equipment">
                <el-icon><Monitor /></el-icon>
              </div>
              <div class="card-info">
                <h3>{{ dashboardData.total_equipment || 0 }}</h3>
                <p>设备总数</p>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="overview-card">
            <div class="card-content">
              <div class="card-icon laboratories">
                <el-icon><OfficeBuilding /></el-icon>
              </div>
              <div class="card-info">
                <h3>{{ dashboardData.total_laboratories || 0 }}</h3>
                <p>实验室总数</p>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 统计图表区域 -->
    <div class="charts-section">
      <el-row :gutter="20">
        <el-col :span="12">
          <el-card>
            <template #header>
              <div class="card-header">
                <span>实验完成情况</span>
                <el-button type="primary" size="small" @click="refreshExperimentStats">刷新</el-button>
              </div>
            </template>
            <div class="chart-container" ref="experimentChart"></div>
          </el-card>
        </el-col>
        <el-col :span="12">
          <el-card>
            <template #header>
              <div class="card-header">
                <span>设备状态分布</span>
                <el-button type="primary" size="small" @click="refreshEquipmentStats">刷新</el-button>
              </div>
            </template>
            <div class="chart-container" ref="equipmentChart"></div>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 详细统计表格 -->
    <div class="tables-section">
      <el-row :gutter="20">
        <el-col :span="24">
          <el-card>
            <template #header>
              <div class="card-header">
                <span>学校绩效排名</span>
                <div class="header-actions">
                  <el-date-picker
                    v-model="dateRange"
                    type="daterange"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                    size="small"
                    @change="onDateRangeChange"
                  />
                  <el-button type="primary" size="small" @click="refreshPerformanceStats">刷新</el-button>
                </div>
              </div>
            </template>
            <el-table :data="performanceData" v-loading="performanceLoading">
              <el-table-column prop="name" label="学校名称" width="200" />
              <el-table-column prop="total_experiments" label="总实验数" width="120" />
              <el-table-column prop="completed_experiments" label="已完成" width="120" />
              <el-table-column prop="completion_rate" label="完成率" width="120">
                <template #default="scope">
                  <el-progress 
                    :percentage="scope.row.completion_rate" 
                    :color="getProgressColor(scope.row.completion_rate)"
                    :show-text="false"
                  />
                  <span class="progress-text">{{ scope.row.completion_rate }}%</span>
                </template>
              </el-table-column>
              <el-table-column prop="total_equipment" label="设备数量" width="120" />
              <el-table-column prop="equipment_value" label="设备价值" width="150">
                <template #default="scope">
                  ¥{{ formatCurrency(scope.row.equipment_value) }}
                </template>
              </el-table-column>
              <el-table-column prop="avg_quality_score" label="平均质量评分" width="140">
                <template #default="scope">
                  <el-rate 
                    v-model="scope.row.avg_quality_score" 
                    disabled 
                    show-score 
                    text-color="#ff9900"
                  />
                </template>
              </el-table-column>
            </el-table>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import { School, User, Monitor, OfficeBuilding } from '@element-plus/icons-vue'
import * as echarts from 'echarts'
import { statisticsApi } from '@/api/statistics'

// 响应式数据
const dashboardData = ref<any>({})
const experimentData = ref<any>({})
const equipmentData = ref<any>({})
const performanceData = ref<any[]>([])
const performanceLoading = ref(false)
const dateRange = ref<[Date, Date]>([
  new Date(Date.now() - 30 * 24 * 60 * 60 * 1000), // 30天前
  new Date()
])

// 图表引用
const experimentChart = ref<HTMLElement>()
const equipmentChart = ref<HTMLElement>()

// 图表实例
let experimentChartInstance: echarts.ECharts | null = null
let equipmentChartInstance: echarts.ECharts | null = null

// 加载仪表盘数据
const loadDashboardData = async () => {
  try {
    const response = await statisticsApi.getDashboardStats()
    if (response.success) {
      dashboardData.value = response.data
    }
  } catch (error) {
    console.error('加载仪表盘数据失败:', error)
    ElMessage.error('加载仪表盘数据失败')
  }
}

// 加载实验统计数据
const loadExperimentStats = async () => {
  try {
    const params = {
      start_date: formatDate(dateRange.value[0]),
      end_date: formatDate(dateRange.value[1])
    }
    const response = await statisticsApi.getExperimentStats(params)
    if (response.success) {
      experimentData.value = response.data
      await nextTick()
      renderExperimentChart()
    }
  } catch (error) {
    console.error('加载实验统计数据失败:', error)
    ElMessage.error('加载实验统计数据失败')
  }
}

// 加载设备统计数据
const loadEquipmentStats = async () => {
  try {
    const params = {
      start_date: formatDate(dateRange.value[0]),
      end_date: formatDate(dateRange.value[1])
    }
    const response = await statisticsApi.getEquipmentStats(params)
    if (response.success) {
      equipmentData.value = response.data
      await nextTick()
      renderEquipmentChart()
    }
  } catch (error) {
    console.error('加载设备统计数据失败:', error)
    ElMessage.error('加载设备统计数据失败')
  }
}

// 加载绩效统计数据
const loadPerformanceStats = async () => {
  performanceLoading.value = true
  try {
    const params = {
      start_date: formatDate(dateRange.value[0]),
      end_date: formatDate(dateRange.value[1])
    }
    const response = await statisticsApi.getOrganizationPerformance(params)
    if (response.success) {
      performanceData.value = response.data.rankings || []
    }
  } catch (error) {
    console.error('加载绩效统计数据失败:', error)
    ElMessage.error('加载绩效统计数据失败')
  } finally {
    performanceLoading.value = false
  }
}

// 渲染实验统计图表
const renderExperimentChart = () => {
  if (!experimentChart.value) return
  
  if (experimentChartInstance) {
    experimentChartInstance.dispose()
  }
  
  experimentChartInstance = echarts.init(experimentChart.value)
  
  const option = {
    title: {
      text: '实验完成情况',
      left: 'center'
    },
    tooltip: {
      trigger: 'item'
    },
    legend: {
      orient: 'vertical',
      left: 'left'
    },
    series: [
      {
        name: '实验状态',
        type: 'pie',
        radius: '50%',
        data: [
          { value: experimentData.value.summary?.completed_experiments || 0, name: '已完成' },
          { value: (experimentData.value.summary?.total_experiments || 0) - (experimentData.value.summary?.completed_experiments || 0), name: '未完成' }
        ],
        emphasis: {
          itemStyle: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
          }
        }
      }
    ]
  }
  
  experimentChartInstance.setOption(option)
}

// 渲染设备统计图表
const renderEquipmentChart = () => {
  if (!equipmentChart.value) return
  
  if (equipmentChartInstance) {
    equipmentChartInstance.dispose()
  }
  
  equipmentChartInstance = echarts.init(equipmentChart.value)
  
  const statusData = equipmentData.value.status_distribution || []
  const statusNames = ['正常', '借出', '维修', '报废']
  
  const option = {
    title: {
      text: '设备状态分布',
      left: 'center'
    },
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'shadow'
      }
    },
    xAxis: {
      type: 'category',
      data: statusNames
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name: '设备数量',
        type: 'bar',
        data: statusNames.map((_, index) => {
          const item = statusData.find((d: any) => d.status === index + 1)
          return item ? item.count : 0
        }),
        itemStyle: {
          color: function(params: any) {
            const colors = ['#5470c6', '#91cc75', '#fac858', '#ee6666']
            return colors[params.dataIndex]
          }
        }
      }
    ]
  }
  
  equipmentChartInstance.setOption(option)
}

// 工具函数
const formatDate = (date: Date) => {
  return date.toISOString().split('T')[0]
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
const onDateRangeChange = () => {
  loadExperimentStats()
  loadEquipmentStats()
  loadPerformanceStats()
}

const refreshExperimentStats = () => {
  loadExperimentStats()
}

const refreshEquipmentStats = () => {
  loadEquipmentStats()
}

const refreshPerformanceStats = () => {
  loadPerformanceStats()
}

// 生命周期
onMounted(() => {
  loadDashboardData()
  loadExperimentStats()
  loadEquipmentStats()
  loadPerformanceStats()
})
</script>

<style scoped>
.statistics-dashboard {
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

.overview-cards {
  margin-bottom: 20px;
}

.overview-card {
  height: 120px;
}

.card-content {
  display: flex;
  align-items: center;
  height: 100%;
}

.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  font-size: 24px;
  color: white;
}

.card-icon.schools {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-icon.users {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-icon.equipment {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.card-icon.laboratories {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.card-info h3 {
  margin: 0 0 4px 0;
  font-size: 28px;
  font-weight: bold;
  color: #303133;
}

.card-info p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.charts-section {
  margin-bottom: 20px;
}

.tables-section {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 10px;
  align-items: center;
}

.chart-container {
  height: 300px;
}

.progress-text {
  margin-left: 8px;
  font-size: 12px;
  color: #606266;
}
</style>
