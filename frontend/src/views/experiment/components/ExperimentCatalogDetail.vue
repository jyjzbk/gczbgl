<template>
  <el-dialog
    v-model="visible"
    title="实验目录详情"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="catalog" class="catalog-detail">
      <!-- 基本信息 -->
      <div class="detail-section">
        <h3 class="section-title">基本信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="实验编号">
            {{ catalog.code }}
          </el-descriptions-item>
          <el-descriptions-item label="实验名称">
            {{ catalog.name }}
          </el-descriptions-item>
          <el-descriptions-item label="所属学科">
            {{ catalog.subject?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="年级">
            {{ getGradeLabel(catalog.grade_level || catalog.grade) }}
          </el-descriptions-item>
          <el-descriptions-item label="册次">
            {{ getVolumeLabel(catalog.volume) || (catalog.semester === 1 ? '上学期' : '下学期') }}
          </el-descriptions-item>
          <el-descriptions-item label="实验类型">
            <el-tag :type="getTypeTagType(catalog.type)">
              {{ getTypeLabel(catalog.type) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="所属章节">
            {{ catalog.chapter || '-' }}
          </el-descriptions-item>
          <el-descriptions-item label="实验时长">
            {{ catalog.duration }}分钟
          </el-descriptions-item>
          <el-descriptions-item label="学生人数">
            {{ catalog.student_count }}人
          </el-descriptions-item>
          <el-descriptions-item label="难度等级">
            <el-rate
              :model-value="catalog.difficulty_level"
              disabled
              show-score
              text-color="#ff9900"
              score-template="{value}星"
            />
          </el-descriptions-item>
          <el-descriptions-item label="标准实验">
            <el-tag :type="catalog.is_standard ? 'success' : 'info'">
              {{ catalog.is_standard ? '是' : '否' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="catalog.status ? 'success' : 'danger'">
              {{ catalog.status ? '启用' : '禁用' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="创建时间">
            {{ formatDate(catalog.created_at) }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 实验目标 -->
      <div v-if="catalog.objective" class="detail-section">
        <h3 class="section-title">实验目标</h3>
        <div class="content-box">
          {{ catalog.objective }}
        </div>
      </div>
      
      <!-- 实验器材 -->
      <div v-if="catalog.materials" class="detail-section">
        <h3 class="section-title">实验器材</h3>
        <div class="content-box">
          {{ catalog.materials }}
        </div>
      </div>
      
      <!-- 实验步骤 -->
      <div v-if="catalog.procedure" class="detail-section">
        <h3 class="section-title">实验步骤</h3>
        <div class="content-box procedure-content">
          <pre>{{ catalog.procedure }}</pre>
        </div>
      </div>
      
      <!-- 安全注意事项 -->
      <div v-if="catalog.safety_notes" class="detail-section">
        <h3 class="section-title">安全注意事项</h3>
        <div class="content-box safety-notes">
          <el-alert
            :title="catalog.safety_notes"
            type="warning"
            :closable="false"
            show-icon
          />
        </div>
      </div>
      
      <!-- 统计信息 -->
      <div class="detail-section">
        <h3 class="section-title">统计信息</h3>
        <el-row :gutter="20">
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ stats.reservationCount }}</div>
              <div class="stat-label">预约次数</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ stats.recordCount }}</div>
              <div class="stat-label">实验记录</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-card">
              <div class="stat-value">{{ stats.avgScore }}分</div>
              <div class="stat-label">平均评分</div>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>
    
    <template #footer>
      <el-button @click="handleClose">关闭</el-button>
      <el-button type="primary" @click="handleEdit">
        编辑
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { type ExperimentCatalog } from '@/api/experiment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  catalog?: ExperimentCatalog | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'edit', catalog: ExperimentCatalog): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 对话框显示状态
const visible = ref(false)

// 模拟统计数据
const stats = ref({
  reservationCount: 0,
  recordCount: 0,
  avgScore: 0
})

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal && props.catalog) {
      loadStats()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

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

// 获取年级标签
const getGradeLabel = (grade: string | number) => {
  if (!grade) return '-'

  const gradeMap: Record<string, string> = {
    '1': '一年级',
    '2': '二年级',
    '3': '三年级',
    '4': '四年级',
    '5': '五年级',
    '6': '六年级',
    '7': '七年级',
    '8': '八年级',
    '9': '九年级',
    '10': '高一',
    '11': '高二',
    '12': '高三'
  }

  return gradeMap[String(grade)] || `${grade}年级`
}

// 获取册次标签
const getVolumeLabel = (volume: string) => {
  if (!volume) return '-'

  const volumeMap: Record<string, string> = {
    '上册': '上册',
    '下册': '下册',
    '全册': '全册',
    '第一册': '第一册',
    '第二册': '第二册',
    '第三册': '第三册',
    '第四册': '第四册'
  }

  return volumeMap[volume] || volume
}

// 格式化日期
const formatDate = (date: string) => {
  return dayjs(date).format('YYYY-MM-DD HH:mm:ss')
}

// 加载统计数据
const loadStats = () => {
  // 这里应该调用API获取统计数据
  // 暂时使用模拟数据
  stats.value = {
    reservationCount: Math.floor(Math.random() * 50),
    recordCount: Math.floor(Math.random() * 30),
    avgScore: Math.floor(Math.random() * 20) + 80
  }
}

// 处理编辑
const handleEdit = () => {
  if (props.catalog) {
    emit('edit', props.catalog)
    handleClose()
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
}
</script>

<style scoped>
.catalog-detail {
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

.procedure-content pre {
  margin: 0;
  font-family: inherit;
  white-space: pre-wrap;
  word-wrap: break-word;
}

.safety-notes {
  padding: 0;
  background: transparent;
  border: none;
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
.catalog-detail::-webkit-scrollbar {
  width: 6px;
}

.catalog-detail::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.catalog-detail::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.catalog-detail::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
