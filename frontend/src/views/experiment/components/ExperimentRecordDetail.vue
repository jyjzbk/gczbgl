<template>
  <el-dialog
    v-model="visible"
    title="实验记录详情"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="record" class="record-detail">
      <!-- 基本信息 -->
      <div class="detail-section">
        <h3 class="section-title">实验信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="实验名称">
            {{ record.catalog?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验编号">
            {{ record.catalog?.code }}
          </el-descriptions-item>
          <el-descriptions-item label="实验室">
            {{ record.laboratory?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="执行教师">
            {{ record.teacher?.real_name }}
          </el-descriptions-item>
          <el-descriptions-item label="班级名称">
            {{ record.class_name }}
          </el-descriptions-item>
          <el-descriptions-item label="学生人数">
            {{ record.student_count }}人
          </el-descriptions-item>
          <el-descriptions-item label="开始时间">
            {{ formatDateTime(record.start_time) }}
          </el-descriptions-item>
          <el-descriptions-item label="结束时间">
            {{ record.end_time ? formatDateTime(record.end_time) : '进行中' }}
          </el-descriptions-item>
          <el-descriptions-item label="实验状态">
            <el-tag :type="getStatusTagType(record.status)">
              {{ getStatusLabel(record.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="完成率">
            <el-progress 
              :percentage="record.completion_rate" 
              :stroke-width="6"
              :show-text="false"
            />
            <span style="margin-left: 8px;">{{ record.completion_rate }}%</span>
          </el-descriptions-item>
          <el-descriptions-item label="质量评分">
            <el-rate
              :model-value="record.quality_score / 20"
              disabled
              show-score
              text-color="#ff9900"
              score-template="{value}星"
            />
            <span style="margin-left: 8px;">{{ record.quality_score }}分</span>
          </el-descriptions-item>
          <el-descriptions-item label="创建时间">
            {{ formatDateTime(record.created_at) }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 关联预约信息 -->
      <div v-if="record.reservation" class="detail-section">
        <h3 class="section-title">关联预约</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="预约日期">
            {{ record.reservation.reservation_date }}
          </el-descriptions-item>
          <el-descriptions-item label="预约时间">
            {{ record.reservation.start_time }} - {{ record.reservation.end_time }}
          </el-descriptions-item>
          <el-descriptions-item label="预约状态">
            <el-tag :type="getReservationStatusType(record.reservation.status)">
              {{ getReservationStatusLabel(record.reservation.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="预约备注">
            {{ record.reservation.remark || '-' }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 实验总结 -->
      <div v-if="record.summary" class="detail-section">
        <h3 class="section-title">实验总结</h3>
        <div class="content-box">
          {{ record.summary }}
        </div>
      </div>
      
      <!-- 遇到问题 -->
      <div v-if="record.problems" class="detail-section">
        <h3 class="section-title">遇到问题</h3>
        <div class="content-box problems">
          <el-alert
            :title="record.problems"
            type="warning"
            :closable="false"
            show-icon
          />
        </div>
      </div>
      
      <!-- 改进建议 -->
      <div v-if="record.suggestions" class="detail-section">
        <h3 class="section-title">改进建议</h3>
        <div class="content-box">
          {{ record.suggestions }}
        </div>
      </div>
      
      <!-- 实验照片 -->
      <div v-if="record.photos && record.photos.length > 0" class="detail-section">
        <h3 class="section-title">实验照片</h3>
        <div class="photo-gallery">
          <div 
            v-for="(photo, index) in record.photos" 
            :key="index"
            class="photo-item"
            @click="handlePreviewPhoto(index)"
          >
            <el-image
              :src="photo"
              fit="cover"
              :preview-src-list="record.photos"
              :initial-index="index"
              class="photo-image"
            />
          </div>
        </div>
      </div>
      
      <!-- 实验视频 -->
      <div v-if="record.videos && record.videos.length > 0" class="detail-section">
        <h3 class="section-title">实验视频</h3>
        <div class="video-list">
          <div 
            v-for="(video, index) in record.videos" 
            :key="index"
            class="video-item"
          >
            <video 
              :src="video" 
              controls 
              class="video-player"
              preload="metadata"
            >
              您的浏览器不支持视频播放
            </video>
          </div>
        </div>
      </div>
      
      <!-- 实验数据统计 -->
      <div class="detail-section">
        <h3 class="section-title">实验数据</h3>
        <el-row :gutter="20">
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ getDuration() }}</div>
              <div class="stat-label">实验时长</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ record.student_count }}</div>
              <div class="stat-label">参与学生</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ (record.photos?.length || 0) + (record.videos?.length || 0) }}</div>
              <div class="stat-label">媒体文件</div>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>
    
    <template #footer>
      <el-button @click="handleClose">关闭</el-button>
      <el-button 
        v-if="record && record.status === 1" 
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
import { type ExperimentRecord } from '@/api/experiment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  record?: ExperimentRecord | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'edit', record: ExperimentRecord): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 对话框显示状态
const visible = ref(false)

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
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
    1: 'warning',
    2: 'success',
    3: 'info'
  }
  return statusMap[status] || 'info'
}

// 获取状态标签文本
const getStatusLabel = (status: number) => {
  const statusMap: Record<number, string> = {
    1: '进行中',
    2: '已完成',
    3: '已取消'
  }
  return statusMap[status] || '未知'
}

// 获取预约状态类型
const getReservationStatusType = (status: number) => {
  const statusMap: Record<number, string> = {
    0: 'warning',
    1: 'success',
    2: 'danger',
    3: 'info'
  }
  return statusMap[status] || 'info'
}

// 获取预约状态标签
const getReservationStatusLabel = (status: number) => {
  const statusMap: Record<number, string> = {
    0: '待审核',
    1: '已通过',
    2: '已拒绝',
    3: '已取消'
  }
  return statusMap[status] || '未知'
}

// 格式化日期时间
const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm:ss')
}

// 计算实验时长
const getDuration = () => {
  if (!props.record) return '-'
  
  const start = dayjs(props.record.start_time)
  const end = props.record.end_time ? dayjs(props.record.end_time) : dayjs()
  
  const duration = end.diff(start, 'minute')
  
  if (duration < 60) {
    return `${duration}分钟`
  } else {
    const hours = Math.floor(duration / 60)
    const minutes = duration % 60
    return `${hours}小时${minutes}分钟`
  }
}

// 预览照片
const handlePreviewPhoto = (index: number) => {
  // 照片预览由 el-image 组件自动处理
}

// 处理编辑
const handleEdit = () => {
  if (props.record) {
    emit('edit', props.record)
    handleClose()
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
}
</script>

<style scoped>
.record-detail {
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

.problems {
  padding: 0;
  background: transparent;
  border: none;
}

.photo-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 12px;
}

.photo-item {
  cursor: pointer;
  border-radius: 6px;
  overflow: hidden;
  transition: transform 0.3s;
}

.photo-item:hover {
  transform: scale(1.05);
}

.photo-image {
  width: 100%;
  height: 120px;
}

.video-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 16px;
}

.video-player {
  width: 100%;
  height: 200px;
  border-radius: 6px;
}

.stat-card {
  text-align: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #409eff;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
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
.record-detail::-webkit-scrollbar {
  width: 6px;
}

.record-detail::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.record-detail::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.record-detail::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
