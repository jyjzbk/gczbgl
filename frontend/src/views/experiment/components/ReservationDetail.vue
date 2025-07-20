<template>
  <div class="reservation-detail">
    <!-- 基本信息 -->
    <el-descriptions title="预约信息" :column="2" border>
      <el-descriptions-item label="预约编号">
        {{ reservation.id }}
      </el-descriptions-item>
      <el-descriptions-item label="状态">
        <el-tag :type="getStatusType(reservation.status)">
          {{ getStatusText(reservation.status) }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="实验名称">
        {{ reservation.catalog?.name }}
      </el-descriptions-item>
      <el-descriptions-item label="实验室">
        {{ reservation.laboratory?.name }}
      </el-descriptions-item>
      <el-descriptions-item label="预约日期">
        {{ formatDate(reservation.reservation_date) }}
      </el-descriptions-item>
      <el-descriptions-item label="时间段">
        {{ reservation.start_time }} - {{ reservation.end_time }}
      </el-descriptions-item>
      <el-descriptions-item label="授课教师">
        {{ reservation.teacher?.name }}
      </el-descriptions-item>
      <el-descriptions-item label="班级">
        {{ reservation.class_name }}
      </el-descriptions-item>
      <el-descriptions-item label="学生人数">
        {{ reservation.student_count }}人
      </el-descriptions-item>
      <el-descriptions-item label="优先级">
        <el-tag :type="getPriorityType(reservation.priority)" size="small">
          {{ getPriorityText(reservation.priority) }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="创建时间" :span="2">
        {{ formatDateTime(reservation.created_at) }}
      </el-descriptions-item>
    </el-descriptions>

    <!-- 实验详情 -->
    <div v-if="reservation.catalog" class="experiment-details">
      <h3>实验详情</h3>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="实验类型">
          <el-tag :type="getExperimentTypeColor(reservation.catalog.type)">
            {{ getExperimentTypeName(reservation.catalog.type) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="建议时长">
          {{ reservation.catalog.duration }}分钟
        </el-descriptions-item>
        <el-descriptions-item label="实验目的" :span="2">
          {{ reservation.catalog.objective }}
        </el-descriptions-item>
        <el-descriptions-item label="安全注意事项" :span="2">
          {{ reservation.catalog.safety_notes }}
        </el-descriptions-item>
      </el-descriptions>
    </div>

    <!-- 器材清单 -->
    <div v-if="reservation.equipment_requirements?.length > 0" class="equipment-list">
      <h3>器材清单</h3>
      <el-table :data="reservation.equipment_requirements" border size="small">
        <el-table-column prop="equipment_name" label="器材名称" />
        <el-table-column prop="equipment_code" label="器材编号" width="120" />
        <el-table-column prop="required_quantity" label="需要数量" width="100" align="center" />
        <el-table-column label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.shortage === 0" type="success" size="small">充足</el-tag>
            <el-tag v-else type="danger" size="small">缺{{ row.shortage }}</el-tag>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <!-- 审核信息 -->
    <div v-if="reservation.reviewer_id" class="review-info">
      <h3>审核信息</h3>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="审核人">
          {{ reservation.reviewer?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="审核时间">
          {{ formatDateTime(reservation.reviewed_at) }}
        </el-descriptions-item>
        <el-descriptions-item label="审核备注" :span="2">
          {{ reservation.review_remark || '无' }}
        </el-descriptions-item>
      </el-descriptions>
    </div>

    <!-- 实验记录 -->
    <div v-if="reservation.record" class="experiment-record">
      <h3>实验记录</h3>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="实际开始时间">
          {{ formatDateTime(reservation.record.start_time) }}
        </el-descriptions-item>
        <el-descriptions-item label="实际结束时间">
          {{ formatDateTime(reservation.record.end_time) }}
        </el-descriptions-item>
        <el-descriptions-item label="完成率">
          <el-progress
            :percentage="reservation.record.completion_rate"
            :stroke-width="6"
            :show-text="true"
          />
        </el-descriptions-item>
        <el-descriptions-item label="质量评分">
          <el-rate
            v-model="reservation.record.quality_score"
            disabled
            show-score
            text-color="#ff9900"
          />
        </el-descriptions-item>
        <el-descriptions-item label="实验总结" :span="2">
          {{ reservation.record.summary || '无' }}
        </el-descriptions-item>
        <el-descriptions-item label="存在问题" :span="2">
          {{ reservation.record.problems || '无' }}
        </el-descriptions-item>
      </el-descriptions>
    </div>

    <!-- 操作按钮 -->
    <div v-if="showActions" class="actions">
      <el-button
        v-if="canEdit"
        type="primary"
        @click="editReservation"
      >
        修改预约
      </el-button>
      
      <el-button
        v-if="canCancel"
        type="danger"
        @click="cancelReservation"
      >
        取消预约
      </el-button>
      
      <el-button
        v-if="canStartExperiment"
        type="success"
        @click="startExperiment"
      >
        开始实验
      </el-button>
      
      <el-button
        v-if="reservation.record"
        type="info"
        @click="viewWorks"
      >
        查看作品
      </el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { experimentReservationApi } from '@/api/experimentReservation'
import dayjs from 'dayjs'

// Props
const props = defineProps<{
  reservation: any
  showActions?: boolean
}>()

// Emits
const emit = defineEmits<{
  updated: []
  edit: [reservation: any]
  startExperiment: [reservation: any]
  viewWorks: [reservation: any]
}>()

// 计算属性
const canEdit = computed(() => {
  return props.reservation.status === 1 && // 待审核状态
         dayjs(props.reservation.reservation_date).isAfter(dayjs(), 'day')
})

const canCancel = computed(() => {
  return [1, 2].includes(props.reservation.status) && // 待审核或已通过
         dayjs(props.reservation.reservation_date).isAfter(dayjs(), 'day')
})

const canStartExperiment = computed(() => {
  return props.reservation.status === 2 && // 已通过
         dayjs(props.reservation.reservation_date).isSameOrBefore(dayjs(), 'day') &&
         !props.reservation.record
})

// 方法
const getStatusType = (status: number) => {
  const types = {
    1: 'warning',
    2: 'success',
    3: 'danger',
    4: 'info',
    5: 'info'
  }
  return types[status] || 'default'
}

const getStatusText = (status: number) => {
  const texts = {
    1: '待审核',
    2: '已通过',
    3: '已拒绝',
    4: '已完成',
    5: '已取消'
  }
  return texts[status] || '未知'
}

const getPriorityType = (priority: string) => {
  const types = {
    'low': 'info',
    'normal': 'primary',
    'high': 'warning',
    'urgent': 'danger'
  }
  return types[priority] || 'primary'
}

const getPriorityText = (priority: string) => {
  const texts = {
    'low': '低',
    'normal': '普通',
    'high': '高',
    'urgent': '紧急'
  }
  return texts[priority] || '普通'
}

const getExperimentTypeName = (type: number) => {
  const types = { 1: '必做', 2: '选做', 3: '演示', 4: '分组' }
  return types[type] || '未知'
}

const getExperimentTypeColor = (type: number) => {
  const colors = { 1: 'danger', 2: 'warning', 3: 'info', 4: 'success' }
  return colors[type] || 'default'
}

const formatDate = (date: string) => {
  return dayjs(date).format('YYYY-MM-DD')
}

const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm:ss')
}

const editReservation = () => {
  emit('edit', props.reservation)
}

const cancelReservation = async () => {
  try {
    await ElMessageBox.confirm(
      '确定要取消这个预约吗？取消后无法恢复。',
      '确认取消',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await experimentReservationApi.cancel(props.reservation.id)
    ElMessage.success('预约已取消')
    emit('updated')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('取消预约失败')
    }
  }
}

const startExperiment = () => {
  emit('startExperiment', props.reservation)
}

const viewWorks = () => {
  emit('viewWorks', props.reservation)
}
</script>

<style scoped>
.reservation-detail {
  padding: 20px 0;
}

.experiment-details,
.equipment-list,
.review-info,
.experiment-record {
  margin-top: 24px;
}

.experiment-details h3,
.equipment-list h3,
.review-info h3,
.experiment-record h3 {
  margin: 0 0 16px 0;
  color: #303133;
  font-size: 16px;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 8px;
}

.actions {
  margin-top: 24px;
  text-align: right;
  padding-top: 16px;
  border-top: 1px solid #ebeef5;
}

.actions .el-button {
  margin-left: 12px;
}
</style>
