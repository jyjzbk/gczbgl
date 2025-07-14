<template>
  <el-dialog
    v-model="visible"
    title="维修统计分析"
    width="900px"
    :before-close="handleClose"
  >
    <div class="statistics-content">
      <!-- 统计卡片 -->
      <el-row :gutter="20" class="stats-cards">
        <el-col :span="6" v-for="stat in statsData" :key="stat.key">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon">
                <el-icon :size="32" :color="stat.color">
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
      
      <!-- 图表区域 -->
      <el-row :gutter="20" class="chart-section">
        <el-col :span="12">
          <el-card title="故障类型分布">
            <div class="chart-container">
              <div ref="faultTypeChart" style="width: 100%; height: 300px;"></div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="12">
          <el-card title="维修状态分布">
            <div class="chart-container">
              <div ref="statusChart" style="width: 100%; height: 300px;"></div>
            </div>
          </el-card>
        </el-col>
      </el-row>
      
      <el-row :gutter="20" class="chart-section">
        <el-col :span="24">
          <el-card title="维修趋势分析">
            <div class="chart-container">
              <div ref="trendChart" style="width: 100%; height: 300px;"></div>
            </div>
          </el-card>
        </el-col>
      </el-row>
      
      <!-- 详细统计表格 -->
      <el-card title="设备维修详情" class="detail-table">
        <el-table :data="detailData" border size="small">
          <el-table-column prop="equipment_name" label="设备名称" />
          <el-table-column prop="maintenance_count" label="维修次数" align="center" />
          <el-table-column prop="total_cost" label="总费用" align="right">
            <template #default="{ row }">
              ¥{{ row.total_cost.toLocaleString() }}
            </template>
          </el-table-column>
          <el-table-column prop="avg_repair_days" label="平均维修天数" align="center" />
          <el-table-column prop="last_maintenance" label="最近维修" />
        </el-table>
      </el-card>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
        <el-button type="primary" @click="exportReport">
          导出报告
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { Tools, Clock, Money, TrendCharts } from '@element-plus/icons-vue'
import * as echarts from 'echarts'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 图表引用
const faultTypeChart = ref()
const statusChart = ref()
const trendChart = ref()

// 图表实例
let faultTypeChartInstance: echarts.ECharts | null = null
let statusChartInstance: echarts.ECharts | null = null
let trendChartInstance: echarts.ECharts | null = null

// 统计数据
const statsData = ref([
  { key: 'total', label: '总维修数', value: 0, icon: Tools, color: '#409EFF' },
  { key: 'pending', label: '待处理', value: 0, icon: Clock, color: '#E6A23C' },
  { key: 'processing', label: '处理中', value: 0, icon: TrendCharts, color: '#67C23A' },
  { key: 'cost', label: '总费用', value: '¥0', icon: Money, color: '#F56C6C' }
])

const detailData = ref([
  {
    equipment_name: '显微镜-001',
    maintenance_count: 3,
    total_cost: 1500,
    avg_repair_days: 2.3,
    last_maintenance: '2024-01-10'
  },
  {
    equipment_name: '离心机-002',
    maintenance_count: 1,
    total_cost: 800,
    avg_repair_days: 1.0,
    last_maintenance: '2024-01-08'
  },
  {
    equipment_name: '天平-003',
    maintenance_count: 2,
    total_cost: 600,
    avg_repair_days: 1.5,
    last_maintenance: '2024-01-05'
  }
])

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    nextTick(() => {
      initCharts()
      loadStatistics()
    })
  }
})

// 初始化图表
const initCharts = () => {
  // 故障类型分布图
  if (faultTypeChart.value) {
    faultTypeChartInstance = echarts.init(faultTypeChart.value)
    const faultTypeOption = {
      tooltip: {
        trigger: 'item'
      },
      legend: {
        orient: 'vertical',
        left: 'left'
      },
      series: [
        {
          name: '故障类型',
          type: 'pie',
          radius: '50%',
          data: [
            { value: 35, name: '硬件故障' },
            { value: 25, name: '软件故障' },
            { value: 20, name: '使用损坏' },
            { value: 20, name: '自然老化' }
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
    faultTypeChartInstance.setOption(faultTypeOption)
  }
  
  // 维修状态分布图
  if (statusChart.value) {
    statusChartInstance = echarts.init(statusChart.value)
    const statusOption = {
      tooltip: {
        trigger: 'axis',
        axisPointer: {
          type: 'shadow'
        }
      },
      xAxis: {
        type: 'category',
        data: ['待处理', '处理中', '已完成', '已取消']
      },
      yAxis: {
        type: 'value'
      },
      series: [
        {
          name: '数量',
          type: 'bar',
          data: [8, 12, 45, 3],
          itemStyle: {
            color: function(params: any) {
              const colors = ['#E6A23C', '#409EFF', '#67C23A', '#909399']
              return colors[params.dataIndex]
            }
          }
        }
      ]
    }
    statusChartInstance.setOption(statusOption)
  }
  
  // 维修趋势图
  if (trendChart.value) {
    trendChartInstance = echarts.init(trendChart.value)
    const trendOption = {
      tooltip: {
        trigger: 'axis'
      },
      legend: {
        data: ['维修申请', '完成维修']
      },
      xAxis: {
        type: 'category',
        data: ['1月', '2月', '3月', '4月', '5月', '6月']
      },
      yAxis: {
        type: 'value'
      },
      series: [
        {
          name: '维修申请',
          type: 'line',
          data: [12, 15, 18, 22, 16, 20],
          smooth: true
        },
        {
          name: '完成维修',
          type: 'line',
          data: [10, 14, 16, 20, 15, 18],
          smooth: true
        }
      ]
    }
    trendChartInstance.setOption(trendOption)
  }
}

// 加载统计数据
const loadStatistics = async () => {
  try {
    // 模拟加载统计数据
    statsData.value = [
      { key: 'total', label: '总维修数', value: 68, icon: Tools, color: '#409EFF' },
      { key: 'pending', label: '待处理', value: 8, icon: Clock, color: '#E6A23C' },
      { key: 'processing', label: '处理中', value: 12, icon: TrendCharts, color: '#67C23A' },
      { key: 'cost', label: '总费用', value: '¥15,600', icon: Money, color: '#F56C6C' }
    ]
  } catch (error) {
    console.error('加载统计数据失败:', error)
  }
}

// 导出报告
const exportReport = () => {
  ElMessage.success('报告导出功能开发中...')
}

// 关闭对话框
const handleClose = () => {
  // 销毁图表实例
  if (faultTypeChartInstance) {
    faultTypeChartInstance.dispose()
    faultTypeChartInstance = null
  }
  if (statusChartInstance) {
    statusChartInstance.dispose()
    statusChartInstance = null
  }
  if (trendChartInstance) {
    trendChartInstance.dispose()
    trendChartInstance = null
  }
  
  emit('update:modelValue', false)
}

// 窗口大小变化时重新调整图表
const handleResize = () => {
  faultTypeChartInstance?.resize()
  statusChartInstance?.resize()
  trendChartInstance?.resize()
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  handleClose()
})
</script>

<style scoped>
.statistics-content {
  max-height: 70vh;
  overflow-y: auto;
}

.stats-cards {
  margin-bottom: 20px;
}

.stat-card {
  text-align: center;
}

.stat-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.stat-info {
  text-align: left;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
}

.chart-section {
  margin-bottom: 20px;
}

.chart-container {
  padding: 10px 0;
}

.detail-table {
  margin-top: 20px;
}

.dialog-footer {
  text-align: right;
}
</style>
