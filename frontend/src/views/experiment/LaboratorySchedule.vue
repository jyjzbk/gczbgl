<template>
  <div class="laboratory-schedule">
    <div class="page-card">
      <div class="page-header">
        <h2>实验室课表</h2>
        <p>查看实验室使用情况，支持快速预约</p>
      </div>

      <!-- 筛选条件 -->
      <div class="filter-bar">
        <el-form :model="filterForm" inline>
          <el-form-item label="实验室">
            <el-select
              v-model="filterForm.laboratory_id"
              placeholder="请选择实验室"
              clearable
              style="width: 200px"
              @change="handleLaboratoryChange"
            >
              <el-option
                v-for="lab in laboratories"
                :key="lab.id"
                :label="lab.name"
                :value="lab.id"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="视图类型">
            <el-radio-group v-model="filterForm.view_type" @change="loadSchedule">
              <el-radio-button label="week">周视图</el-radio-button>
              <el-radio-button label="month">月视图</el-radio-button>
            </el-radio-group>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="loadSchedule" :loading="loading">
              查询
            </el-button>
            <el-button @click="goToday">今天</el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 日期导航 -->
      <div class="date-navigation">
        <el-button-group>
          <el-button @click="previousPeriod" :icon="ArrowLeft">
            {{ filterForm.view_type === 'week' ? '上周' : '上月' }}
          </el-button>
          <el-button @click="nextPeriod" :icon="ArrowRight">
            {{ filterForm.view_type === 'week' ? '下周' : '下月' }}
          </el-button>
        </el-button-group>
        
        <div class="current-period">
          <h3>{{ currentPeriodText }}</h3>
        </div>

        <el-button type="primary" @click="showQuickReservation = true">
          快速预约
        </el-button>
      </div>

      <!-- 课表内容 -->
      <div class="schedule-content" v-loading="loading">
        <!-- 周视图 -->
        <div v-if="filterForm.view_type === 'week'" class="week-view">
          <div class="time-header">
            <div class="time-slot-header">时间</div>
            <div
              v-for="day in weekDays"
              :key="day.date"
              class="day-header"
              :class="{ today: isToday(day.date) }"
            >
              <div class="day-name">{{ day.day_name }}</div>
              <div class="day-date">{{ formatDate(day.date) }}</div>
            </div>
          </div>

          <div class="time-slots">
            <div
              v-for="timeSlot in timeSlots"
              :key="timeSlot.time"
              class="time-row"
            >
              <div class="time-label">{{ timeSlot.time }}</div>
              <div
                v-for="day in weekDays"
                :key="`${day.date}-${timeSlot.time}`"
                class="time-cell"
                @click="handleCellClick(day.date, timeSlot.time)"
              >
                <div
                  v-for="reservation in getReservationsForTimeSlot(day.date, timeSlot.time)"
                  :key="reservation.id"
                  class="reservation-block"
                  :class="getReservationClass(reservation)"
                  @click.stop="showReservationDetail(reservation)"
                >
                  <div class="reservation-title">{{ reservation.experiment_name }}</div>
                  <div class="reservation-info">
                    {{ reservation.teacher_name }} | {{ reservation.class_name }}
                  </div>
                  <div class="reservation-time">
                    {{ reservation.start_time }}-{{ reservation.end_time }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 月视图 -->
        <div v-else class="month-view">
          <el-calendar v-model="currentDate" @panel-change="handlePanelChange">
            <template #date-cell="{ data }">
              <div class="calendar-day">
                <div class="day-number">{{ data.day.split('-').pop() }}</div>
                <div class="day-reservations">
                  <div
                    v-for="reservation in getDayReservations(data.day)"
                    :key="reservation.id"
                    class="mini-reservation"
                    :class="getReservationClass(reservation)"
                    @click="showReservationDetail(reservation)"
                  >
                    <div class="mini-title">{{ reservation.experiment_name }}</div>
                    <div class="mini-time">{{ reservation.start_time }}</div>
                  </div>
                </div>
              </div>
            </template>
          </el-calendar>
        </div>
      </div>

      <!-- 实验室信息 -->
      <div v-if="selectedLaboratory" class="laboratory-info">
        <el-descriptions title="实验室信息" :column="4" border>
          <el-descriptions-item label="名称">
            {{ selectedLaboratory.name }}
          </el-descriptions-item>
          <el-descriptions-item label="容量">
            {{ selectedLaboratory.capacity }}人
          </el-descriptions-item>
          <el-descriptions-item label="位置">
            {{ selectedLaboratory.location }}
          </el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="selectedLaboratory.status === 1 ? 'success' : 'danger'">
              {{ selectedLaboratory.status === 1 ? '正常' : '维修中' }}
            </el-tag>
          </el-descriptions-item>
        </el-descriptions>
      </div>
    </div>

    <!-- 快速预约对话框 -->
    <el-dialog
      v-model="showQuickReservation"
      title="快速预约"
      width="600px"
      :close-on-click-modal="false"
    >
      <QuickReservationForm
        :laboratory-id="filterForm.laboratory_id"
        :initial-date="selectedDate"
        :initial-time="selectedTime"
        @success="handleReservationSuccess"
        @cancel="showQuickReservation = false"
      />
    </el-dialog>

    <!-- 预约详情对话框 -->
    <el-dialog
      v-model="showReservationDetailDialog"
      title="预约详情"
      width="500px"
    >
      <ReservationDetail
        v-if="selectedReservation"
        :reservation="selectedReservation"
        @updated="loadSchedule"
      />
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { ArrowLeft, ArrowRight } from '@element-plus/icons-vue'
import { smartReservationApi } from '@/api/smartReservation'
import { laboratoryApi } from '@/api/laboratory'
import QuickReservationForm from './components/QuickReservationForm.vue'
import ReservationDetail from './components/ReservationDetail.vue'
import dayjs from 'dayjs'
import isBetween from 'dayjs/plugin/isBetween'

// 扩展dayjs插件
dayjs.extend(isBetween)

// 响应式数据
const laboratories = ref([])
const scheduleData = ref([])
const selectedLaboratory = ref(null)
const selectedReservation = ref(null)
const showQuickReservation = ref(false)
const showReservationDetailDialog = ref(false)
const selectedDate = ref('')
const selectedTime = ref('')
const loading = ref(false)
const currentDate = ref(new Date())

// 筛选表单
const filterForm = reactive({
  laboratory_id: null,
  view_type: 'week'
})

// 时间段配置
const timeSlots = [
  { time: '08:00', label: '第1节' },
  { time: '08:50', label: '第2节' },
  { time: '09:50', label: '第3节' },
  { time: '10:40', label: '第4节' },
  { time: '11:30', label: '第5节' },
  { time: '14:00', label: '第6节' },
  { time: '14:50', label: '第7节' },
  { time: '15:40', label: '第8节' },
  { time: '16:30', label: '第9节' }
]

// 计算属性
const currentPeriodText = computed(() => {
  if (filterForm.view_type === 'week') {
    const start = dayjs(currentDate.value).startOf('week')
    const end = dayjs(currentDate.value).endOf('week')
    return `${start.format('YYYY年MM月DD日')} - ${end.format('MM月DD日')}`
  } else {
    return dayjs(currentDate.value).format('YYYY年MM月')
  }
})

const weekDays = computed(() => {
  const start = dayjs(currentDate.value).startOf('week')
  const days = []
  for (let i = 0; i < 7; i++) {
    const date = start.add(i, 'day')
    days.push({
      date: date.format('YYYY-MM-DD'),
      day_name: date.format('dddd')
    })
  }
  return days
})

const dateRange = computed(() => {
  if (filterForm.view_type === 'week') {
    return {
      start: dayjs(currentDate.value).startOf('week').format('YYYY-MM-DD'),
      end: dayjs(currentDate.value).endOf('week').format('YYYY-MM-DD')
    }
  } else {
    return {
      start: dayjs(currentDate.value).startOf('month').format('YYYY-MM-DD'),
      end: dayjs(currentDate.value).endOf('month').format('YYYY-MM-DD')
    }
  }
})

// 方法
const loadLaboratories = async () => {
  try {
    const response = await laboratoryApi.getOptions()
    laboratories.value = response.data

    if (laboratories.value.length > 0 && !filterForm.laboratory_id) {
      filterForm.laboratory_id = laboratories.value[0].id
    }
  } catch (error) {
    console.error('加载实验室列表失败:', error)
    ElMessage.error('加载实验室列表失败')
  }
}

const loadSchedule = async () => {
  if (!filterForm.laboratory_id) return

  loading.value = true
  try {
    const response = await smartReservationApi.getLaboratorySchedule(
      filterForm.laboratory_id,
      {
        date_start: dateRange.value.start,
        date_end: dateRange.value.end,
        view_type: filterForm.view_type
      }
    )

    scheduleData.value = response.data.schedule
    selectedLaboratory.value = response.data.laboratory
  } catch (error) {
    ElMessage.error('加载课表失败')
  } finally {
    loading.value = false
  }
}

const handleLaboratoryChange = () => {
  loadSchedule()
}

const handlePanelChange = (date: Date) => {
  currentDate.value = date
  loadSchedule()
}

const previousPeriod = () => {
  if (filterForm.view_type === 'week') {
    currentDate.value = dayjs(currentDate.value).subtract(1, 'week').toDate()
  } else {
    currentDate.value = dayjs(currentDate.value).subtract(1, 'month').toDate()
  }
  loadSchedule()
}

const nextPeriod = () => {
  if (filterForm.view_type === 'week') {
    currentDate.value = dayjs(currentDate.value).add(1, 'week').toDate()
  } else {
    currentDate.value = dayjs(currentDate.value).add(1, 'month').toDate()
  }
  loadSchedule()
}

const goToday = () => {
  currentDate.value = new Date()
  loadSchedule()
}

const handleCellClick = (date: string, time: string) => {
  selectedDate.value = date
  selectedTime.value = time
  showQuickReservation.value = true
}

const showReservationDetail = (reservation: any) => {
  selectedReservation.value = reservation
  showReservationDetailDialog.value = true
}

const handleReservationSuccess = () => {
  showQuickReservation.value = false
  loadSchedule()
  ElMessage.success('预约成功')
}

const getReservationsForTimeSlot = (date: string, time: string) => {
  const dayData = scheduleData.value.find(day => day.date === date)
  if (!dayData) return []

  return dayData.reservations.filter(reservation => {
    try {
      // 确保时间格式正确
      if (!reservation.start_time || !reservation.end_time || !time) {
        console.warn('Invalid time data:', { reservation, time })
        return false
      }

      // 确保时间格式统一为 HH:mm
      const formatTime = (timeStr: string) => {
        if (timeStr.includes(':')) {
          return timeStr.substring(0, 5) // 取前5位 HH:mm
        }
        return timeStr
      }

      const startTime = dayjs(`${date} ${formatTime(reservation.start_time)}`)
      const endTime = dayjs(`${date} ${formatTime(reservation.end_time)}`)
      const slotTime = dayjs(`${date} ${formatTime(time)}`)

      // 验证dayjs对象是否有效
      if (!startTime.isValid() || !endTime.isValid() || !slotTime.isValid()) {
        console.warn('Invalid dayjs objects:', {
          startTime: startTime.isValid(),
          endTime: endTime.isValid(),
          slotTime: slotTime.isValid(),
          startTimeStr: `${date} ${reservation.start_time}`,
          endTimeStr: `${date} ${reservation.end_time}`,
          slotTimeStr: `${date} ${time}`
        })
        return false
      }

      return slotTime.isBetween(startTime, endTime, null, '[)')
    } catch (error) {
      console.error('Error in getReservationsForTimeSlot:', error, { date, time, reservation })
      return false
    }
  })
}

const getDayReservations = (date: string) => {
  const dayData = scheduleData.value.find(day => day.date === date)
  return dayData ? dayData.reservations : []
}

const getReservationClass = (reservation: any) => {
  const statusClasses = {
    1: 'pending',
    2: 'approved',
    3: 'rejected',
    4: 'completed',
    5: 'cancelled'
  }
  
  const priorityClasses = {
    'low': 'priority-low',
    'normal': 'priority-normal',
    'high': 'priority-high',
    'urgent': 'priority-urgent'
  }

  return [
    statusClasses[reservation.status] || 'pending',
    priorityClasses[reservation.priority] || 'priority-normal'
  ]
}

const isToday = (date: string) => {
  return dayjs(date).isSame(dayjs(), 'day')
}

const formatDate = (date: string) => {
  return dayjs(date).format('MM/DD')
}

// 监听器
watch(() => filterForm.laboratory_id, () => {
  if (filterForm.laboratory_id) {
    loadSchedule()
  }
})

// 生命周期
onMounted(() => {
  loadLaboratories()
})
</script>

<style scoped>
.laboratory-schedule {
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

.filter-bar {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
}

.date-navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 16px;
  background: #f0f2f5;
  border-radius: 6px;
}

.current-period h3 {
  margin: 0;
  color: #303133;
}

/* 周视图样式 */
.week-view {
  border: 1px solid #ebeef5;
  border-radius: 6px;
  overflow: hidden;
}

.time-header {
  display: flex;
  background: #f5f7fa;
  border-bottom: 1px solid #ebeef5;
}

.time-slot-header {
  width: 80px;
  padding: 12px 8px;
  text-align: center;
  font-weight: bold;
  border-right: 1px solid #ebeef5;
}

.day-header {
  flex: 1;
  padding: 12px 8px;
  text-align: center;
  border-right: 1px solid #ebeef5;
}

.day-header.today {
  background: #e6f7ff;
  color: #1890ff;
}

.day-name {
  font-weight: bold;
  margin-bottom: 4px;
}

.day-date {
  font-size: 12px;
  color: #909399;
}

.time-row {
  display: flex;
  border-bottom: 1px solid #ebeef5;
  min-height: 60px;
}

.time-label {
  width: 80px;
  padding: 8px;
  text-align: center;
  background: #fafafa;
  border-right: 1px solid #ebeef5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}

.time-cell {
  flex: 1;
  padding: 4px;
  border-right: 1px solid #ebeef5;
  cursor: pointer;
  position: relative;
}

.time-cell:hover {
  background: #f0f9ff;
}

.reservation-block {
  background: #e6f7ff;
  border: 1px solid #91d5ff;
  border-radius: 4px;
  padding: 4px 6px;
  margin-bottom: 2px;
  font-size: 11px;
  cursor: pointer;
}

.reservation-block.approved {
  background: #f6ffed;
  border-color: #b7eb8f;
}

.reservation-block.completed {
  background: #f0f0f0;
  border-color: #d9d9d9;
}

.reservation-block.priority-high {
  border-left: 3px solid #faad14;
}

.reservation-block.priority-urgent {
  border-left: 3px solid #ff4d4f;
}

.reservation-title {
  font-weight: bold;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.reservation-info,
.reservation-time {
  color: #666;
  font-size: 10px;
}

/* 月视图样式 */
.month-view {
  margin-top: 20px;
}

.calendar-day {
  height: 100px;
  padding: 4px;
}

.day-number {
  font-weight: bold;
  margin-bottom: 4px;
}

.day-reservations {
  max-height: 70px;
  overflow-y: auto;
}

.mini-reservation {
  background: #e6f7ff;
  border-radius: 2px;
  padding: 2px 4px;
  margin-bottom: 2px;
  font-size: 10px;
  cursor: pointer;
}

.mini-title {
  font-weight: bold;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mini-time {
  color: #666;
}

.laboratory-info {
  margin-top: 24px;
}
</style>
