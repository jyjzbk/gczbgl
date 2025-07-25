<template>
  <div class="personal-archive">
    <div class="page-card">
      <div class="page-header">
        <h2>个人实验档案</h2>
        <p>查看个人实验预约记录和完成情况</p>
      </div>

      <!-- 统计概览 -->
      <div class="stats-overview">
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="stat-card">
              <div class="stat-icon">
                <el-icon color="#409eff"><Calendar /></el-icon>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.total_reservations }}</div>
                <div class="stat-label">总预约数</div>
              </div>
            </div>
          </el-col>
          
          <el-col :span="6">
            <div class="stat-card">
              <div class="stat-icon">
                <el-icon color="#67c23a"><Check /></el-icon>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.completed_experiments }}</div>
                <div class="stat-label">已完成实验</div>
              </div>
            </div>
          </el-col>
          
          <el-col :span="6">
            <div class="stat-card">
              <div class="stat-icon">
                <el-icon color="#e6a23c"><TrendCharts /></el-icon>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.completion_rate }}%</div>
                <div class="stat-label">实验开出率</div>
              </div>
            </div>
          </el-col>
          
          <el-col :span="6">
            <div class="stat-card">
              <div class="stat-icon">
                <el-icon color="#f56c6c"><Picture /></el-icon>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.total_works }}</div>
                <div class="stat-label">实验作品</div>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>

      <!-- 筛选条件 -->
      <div class="filter-bar">
        <el-form :model="filterForm" inline>
          <el-form-item label="时间范围">
            <el-date-picker
              v-model="filterForm.date_range"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
              @change="loadReservations"
            />
          </el-form-item>

          <el-form-item label="状态">
            <el-select
              v-model="filterForm.status"
              placeholder="请选择状态"
              clearable
              @change="loadReservations"
            >
              <el-option label="待审核" :value="1" />
              <el-option label="已通过" :value="2" />
              <el-option label="已拒绝" :value="3" />
              <el-option label="已完成" :value="4" />
              <el-option label="已取消" :value="5" />
            </el-select>
          </el-form-item>

          <el-form-item label="实验类型">
            <el-select
              v-model="filterForm.experiment_type"
              placeholder="请选择类型"
              clearable
              @change="loadReservations"
            >
              <el-option label="必做" :value="1" />
              <el-option label="选做" :value="2" />
              <el-option label="演示" :value="3" />
              <el-option label="分组" :value="4" />
            </el-select>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="loadReservations">查询</el-button>
            <el-button @click="resetFilter">重置</el-button>
            <el-button type="success" @click="exportData">导出数据</el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 预约记录列表 -->
      <div class="reservations-list">
        <el-table
          :data="reservations"
          v-loading="loading"
          border
          :row-style="{ height: '60px' }"
          :cell-style="{ padding: '12px 0' }"
          @row-click="showReservationDetail"
        >
          <el-table-column prop="reservation_date" label="预约日期" width="120">
            <template #default="{ row }">
              {{ formatDate(row.reservation_date) }}
            </template>
          </el-table-column>

          <el-table-column prop="catalog.name" label="实验名称" min-width="150" />

          <el-table-column prop="laboratory.name" label="实验室" width="120" />

          <el-table-column label="时间段" width="120">
            <template #default="{ row }">
              {{ row.start_time }}-{{ row.end_time }}
            </template>
          </el-table-column>

          <el-table-column prop="class_name" label="班级" width="100" />

          <el-table-column prop="student_count" label="学生数" width="80" align="center" />

          <el-table-column label="状态" width="100" align="center">
            <template #default="{ row }">
              <el-tag :type="getStatusType(row.status)">
                {{ getStatusText(row.status) }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column label="完成情况" width="120" align="center">
            <template #default="{ row }">
              <div v-if="row.record">
                <el-progress
                  :percentage="row.record.completion_rate"
                  :stroke-width="6"
                  :show-text="false"
                />
                <div class="completion-text">{{ row.record.completion_rate }}%</div>
              </div>
              <span v-else class="no-record">未开始</span>
            </template>
          </el-table-column>

          <el-table-column label="作品数" width="100" align="center">
            <template #default="{ row }">
              <div class="work-count-cell">
                <el-badge :value="row.record?.work_count || 0" type="primary">
                  <el-icon size="18"><Picture /></el-icon>
                </el-badge>
              </div>
            </template>
          </el-table-column>

          <el-table-column label="操作" width="150" fixed="right">
            <template #default="{ row }">
              <el-button
                type="primary"
                size="small"
                @click.stop="showReservationDetail(row)"
              >
                详情
              </el-button>
              
              <el-button
                v-if="row.record"
                type="success"
                size="small"
                @click.stop="showExperimentWorks(row)"
              >
                作品
              </el-button>
            </template>
          </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div class="pagination">
          <el-pagination
            v-model:current-page="pagination.current_page"
            v-model:page-size="pagination.per_page"
            :total="pagination.total"
            :page-sizes="[10, 20, 50, 100]"
            layout="total, sizes, prev, pager, next, jumper"
            @size-change="loadReservations"
            @current-change="loadReservations"
          />
        </div>
      </div>

      <!-- 实验完成率趋势图 -->
      <div class="completion-trend">
        <h3>实验完成率趋势</h3>
        <div ref="chartContainer" class="chart-container"></div>
      </div>
    </div>

    <!-- 预约详情对话框 -->
    <el-dialog
      v-model="showDetailDialog"
      title="预约详情"
      width="800px"
    >
      <ReservationDetail
        v-if="selectedReservation"
        :reservation="selectedReservation"
        :show-actions="false"
      />
    </el-dialog>

    <!-- 实验作品对话框 -->
    <el-dialog
      v-model="showWorksDialog"
      title="实验作品"
      width="1000px"
    >
      <ExperimentWorks
        v-if="selectedRecord"
        :record-id="selectedRecord.id"
        :readonly="true"
      />
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import { Calendar, Check, TrendCharts, Picture } from '@element-plus/icons-vue'
import { experimentReservationApi } from '@/api/experimentReservation'
import { statisticsApi } from '@/api/statistics'
import { useAuthStore } from '@/stores/auth'
import ReservationDetail from './components/ReservationDetail.vue'
import ExperimentWorks from './components/ExperimentWorks.vue'
import * as echarts from 'echarts'
import dayjs from 'dayjs'

// 响应式数据
const reservations = ref([])
const stats = ref({
  total_reservations: 0,
  completed_experiments: 0,
  completion_rate: 0,
  total_works: 0
})
const selectedReservation = ref(null)
const selectedRecord = ref(null)
const showDetailDialog = ref(false)
const showWorksDialog = ref(false)
const loading = ref(false)
const chartContainer = ref(null)
let chart = null

// 筛选表单
const filterForm = reactive({
  date_range: [
    dayjs().subtract(3, 'month').format('YYYY-MM-DD'),
    dayjs().format('YYYY-MM-DD')
  ],
  status: null,
  experiment_type: null
})

// 分页
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 方法
const loadStats = async () => {
  try {
    const response = await statisticsApi.getPersonalExperimentStats()
    stats.value = response.data
  } catch (error) {
    ElMessage.error('加载统计数据失败')
  }
}

const loadReservations = async () => {
  loading.value = true
  try {
    // 获取当前用户ID
    const authStore = useAuthStore()
    const currentUserId = authStore.userInfo?.id

    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      teacher_id: currentUserId, // 使用当前用户ID
      ...filterForm,
      date_start: filterForm.date_range?.[0],
      date_end: filterForm.date_range?.[1]
    }

    const response = await experimentReservationApi.getList(params)
    // 处理分页数据结构
    reservations.value = response.data.data || response.data
    pagination.total = response.data.total || response.pagination?.total || 0
  } catch (error) {
    console.error('加载预约记录失败:', error)
    ElMessage.error('加载预约记录失败')
  } finally {
    loading.value = false
  }
}

const loadCompletionTrend = async () => {
  try {
    const authStore = useAuthStore()
    const currentUserId = authStore.userInfo?.id

    const response = await statisticsApi.getExperimentCompletionTrend({
      teacher_id: currentUserId,
      months: 12
    })

    await nextTick()
    renderChart(response.data)
  } catch (error) {
    console.error('加载趋势数据失败:', error)
    ElMessage.error('加载趋势数据失败')
  }
}

const renderChart = (data) => {
  if (!chartContainer.value) return

  if (chart) {
    chart.dispose()
  }

  chart = echarts.init(chartContainer.value)
  
  const option = {
    title: {
      text: '近12个月实验完成率',
      left: 'center'
    },
    tooltip: {
      trigger: 'axis',
      formatter: '{b}: {c}%'
    },
    xAxis: {
      type: 'category',
      data: data.map(item => item.month)
    },
    yAxis: {
      type: 'value',
      min: 0,
      max: 100,
      axisLabel: {
        formatter: '{value}%'
      }
    },
    series: [{
      data: data.map(item => item.completion_rate),
      type: 'line',
      smooth: true,
      areaStyle: {
        opacity: 0.3
      },
      itemStyle: {
        color: '#409eff'
      }
    }]
  }

  chart.setOption(option)
}

const showReservationDetail = (reservation) => {
  selectedReservation.value = reservation
  showDetailDialog.value = true
}

const showExperimentWorks = (reservation) => {
  selectedRecord.value = reservation.record
  showWorksDialog.value = true
}

const resetFilter = () => {
  filterForm.date_range = [
    dayjs().subtract(3, 'month').format('YYYY-MM-DD'),
    dayjs().format('YYYY-MM-DD')
  ]
  filterForm.status = null
  filterForm.experiment_type = null
  loadReservations()
}

const exportData = async () => {
  try {
    const authStore = useAuthStore()
    const currentUserId = authStore.userInfo?.id

    const response = await experimentReservationApi.export({
      teacher_id: currentUserId,
      ...filterForm,
      date_start: filterForm.date_range?.[0],
      date_end: filterForm.date_range?.[1]
    })

    // 处理文件下载
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `个人实验档案_${dayjs().format('YYYY-MM-DD')}.xlsx`
    link.click()
    window.URL.revokeObjectURL(url)

    ElMessage.success('导出成功')
  } catch (error) {
    console.error('导出失败:', error)
    ElMessage.error('导出失败')
  }
}

const getStatusType = (status) => {
  const types = {
    1: 'warning',
    2: 'success',
    3: 'danger',
    4: 'info',
    5: 'info'
  }
  return types[status] || 'default'
}

const getStatusText = (status) => {
  const texts = {
    1: '待审核',
    2: '已通过',
    3: '已拒绝',
    4: '已完成',
    5: '已取消'
  }
  return texts[status] || '未知'
}

const formatDate = (date) => {
  return dayjs(date).format('MM-DD')
}

// 生命周期
onMounted(() => {
  loadStats()
  loadReservations()
  loadCompletionTrend()
})
</script>

<style scoped>
.personal-archive {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 24px;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 16px;
}

.stats-overview {
  margin-bottom: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #409eff;
}

.stat-icon {
  margin-right: 16px;
  font-size: 32px;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 24px;
  font-weight: bold;
  color: #303133;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.filter-bar {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
}

.reservations-list {
  margin-bottom: 24px;
}

.completion-text {
  font-size: 12px;
  text-align: center;
  margin-top: 4px;
}

.work-count-cell {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  padding: 8px 0;
}

.no-record {
  color: #909399;
  font-size: 12px;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.completion-trend {
  margin-top: 24px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 6px;
}

.completion-trend h3 {
  margin: 0 0 16px 0;
  color: #303133;
}

.chart-container {
  height: 300px;
  width: 100%;
}
</style>
