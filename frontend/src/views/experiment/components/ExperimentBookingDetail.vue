<template>
  <el-dialog
    v-model="visible"
    title="实验预约详情"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="booking" class="booking-detail">
      <!-- 基本信息 -->
      <div class="detail-section">
        <h3 class="section-title">预约信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="实验名称">
            {{ booking.catalog?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验编号">
            {{ booking.catalog?.code }}
          </el-descriptions-item>
          <el-descriptions-item label="实验室">
            {{ booking.laboratory?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验室容量">
            {{ booking.laboratory?.capacity }}人
          </el-descriptions-item>
          <el-descriptions-item label="申请教师">
            {{ booking.teacher?.real_name }}
          </el-descriptions-item>
          <el-descriptions-item label="班级名称">
            {{ booking.class_name }}
          </el-descriptions-item>
          <el-descriptions-item label="学生人数">
            {{ booking.student_count }}人
          </el-descriptions-item>
          <el-descriptions-item label="预约日期">
            {{ booking.reservation_date }}
          </el-descriptions-item>
          <el-descriptions-item label="预约时间">
            {{ booking.start_time }} - {{ booking.end_time }}
          </el-descriptions-item>
          <el-descriptions-item label="预约状态">
            <el-tag :type="getStatusTagType(booking.status)">
              {{ getStatusLabel(booking.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="申请时间">
            {{ formatDateTime(booking.created_at) }}
          </el-descriptions-item>
          <el-descriptions-item label="更新时间">
            {{ formatDateTime(booking.updated_at) }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 备注说明 -->
      <div v-if="booking.remark" class="detail-section">
        <h3 class="section-title">备注说明</h3>
        <div class="content-box">
          {{ booking.remark }}
        </div>
      </div>
      
      <!-- 审核信息 -->
      <div v-if="booking.reviewer_id" class="detail-section">
        <h3 class="section-title">审核信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="审核人">
            {{ booking.reviewer?.real_name }}
          </el-descriptions-item>
          <el-descriptions-item label="审核时间">
            {{ formatDateTime(booking.reviewed_at) }}
          </el-descriptions-item>
          <el-descriptions-item label="审核结果" :span="2">
            <el-tag :type="getStatusTagType(booking.status)">
              {{ getStatusLabel(booking.status) }}
            </el-tag>
          </el-descriptions-item>
        </el-descriptions>
        
        <div v-if="booking.review_remark" class="review-remark">
          <h4>审核意见</h4>
          <div class="content-box">
            {{ booking.review_remark }}
          </div>
        </div>
      </div>
      
      <!-- 实验信息 -->
      <div v-if="booking.catalog" class="detail-section">
        <h3 class="section-title">实验信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="学科">
            {{ booking.catalog.subject?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="年级学期">
            {{ booking.catalog.grade }}年级 {{ booking.catalog.semester === 1 ? '上学期' : '下学期' }}
          </el-descriptions-item>
          <el-descriptions-item label="实验类型">
            <el-tag :type="getTypeTagType(booking.catalog.type)">
              {{ getTypeLabel(booking.catalog.type) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="实验时长">
            {{ booking.catalog.duration }}分钟
          </el-descriptions-item>
          <el-descriptions-item label="建议人数">
            {{ booking.catalog.student_count }}人
          </el-descriptions-item>
          <el-descriptions-item label="难度等级">
            <el-rate
              :model-value="booking.catalog.difficulty_level"
              disabled
              show-score
              text-color="#ff9900"
              score-template="{value}星"
            />
          </el-descriptions-item>
        </el-descriptions>
        
        <!-- 实验目标 -->
        <div v-if="booking.catalog.objective" class="experiment-info">
          <h4>实验目标</h4>
          <div class="content-box">
            {{ booking.catalog.objective }}
          </div>
        </div>
        
        <!-- 实验器材 -->
        <div v-if="booking.catalog.materials" class="experiment-info">
          <h4>实验器材</h4>
          <div class="content-box">
            {{ booking.catalog.materials }}
          </div>
        </div>
        
        <!-- 安全注意事项 -->
        <div v-if="booking.catalog.safety_notes" class="experiment-info">
          <h4>安全注意事项</h4>
          <div class="content-box safety-notes">
            <el-alert
              :title="booking.catalog.safety_notes"
              type="warning"
              :closable="false"
              show-icon
            />
          </div>
        </div>
      </div>
      
      <!-- 时间冲突检查 -->
      <div v-if="conflicts.length > 0" class="detail-section">
        <h3 class="section-title">时间冲突提醒</h3>
        <el-alert
          title="检测到时间冲突"
          type="warning"
          :closable="false"
          show-icon
        >
          <template #default>
            <p>以下预约与当前时间段存在冲突：</p>
            <ul>
              <li v-for="conflict in conflicts" :key="conflict.id">
                {{ conflict.class_name }} - {{ conflict.teacher?.real_name }} 
                ({{ conflict.start_time }} - {{ conflict.end_time }})
              </li>
            </ul>
          </template>
        </el-alert>
      </div>
    </div>
    
    <template #footer>
      <el-button @click="handleClose">关闭</el-button>
      <el-button 
        v-if="booking && booking.status === 0" 
        type="primary" 
        @click="handleEdit"
      >
        编辑
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { type ExperimentReservation } from '@/api/experiment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  booking?: ExperimentReservation | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'edit', booking: ExperimentReservation): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 对话框显示状态
const visible = ref(false)

// 模拟冲突数据
const conflicts = ref<ExperimentReservation[]>([])

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal && props.booking) {
      checkConflicts()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 获取状态标签类型
const getStatusTagType = (status: number) => {
  const statusMap: Record<number, string> = {
    0: 'warning',
    1: 'success',
    2: 'danger',
    3: 'info'
  }
  return statusMap[status] || 'info'
}

// 获取状态标签文本
const getStatusLabel = (status: number) => {
  const statusMap: Record<number, string> = {
    0: '待审核',
    1: '已通过',
    2: '已拒绝',
    3: '已取消'
  }
  return statusMap[status] || '未知'
}

// 获取类型标签类型
const getTypeTagType = (type: number) => {
  const typeMap: Record<number, string> = {
    1: 'info',
    2: 'success',
    3: 'warning',
    4: 'danger'
  }
  return typeMap[type] || 'info'
}

// 获取类型标签文本
const getTypeLabel = (type: number) => {
  const typeMap: Record<number, string> = {
    1: '演示实验',
    2: '分组实验',
    3: '探究实验',
    4: '综合实验'
  }
  return typeMap[type] || '未知'
}

// 格式化日期时间
const formatDateTime = (datetime?: string) => {
  return datetime ? dayjs(datetime).format('YYYY-MM-DD HH:mm:ss') : '-'
}

// 检查时间冲突
const checkConflicts = () => {
  // 这里应该调用API检查时间冲突
  // 暂时使用模拟数据
  conflicts.value = []
}

// 处理编辑
const handleEdit = () => {
  if (props.booking) {
    emit('edit', props.booking)
    handleClose()
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
}
</script>

<style scoped>
.booking-detail {
  max-height: 600px;
  overflow-y: auto;
}

.detail-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e4e7ed;
}

.content-box {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 16px;
  line-height: 1.6;
  color: #495057;
}

.review-remark {
  margin-top: 16px;
}

.review-remark h4 {
  font-size: 14px;
  font-weight: 500;
  color: #303133;
  margin: 0 0 8px;
}

.experiment-info {
  margin-top: 16px;
}

.experiment-info h4 {
  font-size: 14px;
  font-weight: 500;
  color: #303133;
  margin: 0 0 8px;
}

.safety-notes {
  padding: 0;
  background: transparent;
  border: none;
}

:deep(.el-descriptions__body) {
  background: #fff;
}

:deep(.el-descriptions__label) {
  width: 120px;
}

:deep(.el-alert) {
  margin: 0;
}

:deep(.el-alert__content) {
  line-height: 1.6;
}

/* 滚动条样式 */
.booking-detail::-webkit-scrollbar {
  width: 6px;
}

.booking-detail::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.booking-detail::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.booking-detail::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
