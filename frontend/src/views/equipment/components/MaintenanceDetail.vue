<template>
  <el-dialog
    v-model="visible"
    title="维修详情"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="maintenance" class="maintenance-detail">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="设备名称">
          {{ maintenance.equipment?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备编号">
          {{ maintenance.equipment?.code }}
        </el-descriptions-item>
        <el-descriptions-item label="报修人">
          {{ maintenance.reporter_name }}
        </el-descriptions-item>
        <el-descriptions-item label="故障类型">
          <el-tag :type="getFaultTypeTagType(maintenance.fault_type)">
            {{ getFaultTypeText(maintenance.fault_type) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="优先级">
          <el-tag :type="getPriorityTagType(maintenance.priority)">
            {{ getPriorityText(maintenance.priority) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="维修状态">
          <el-tag :type="getStatusTagType(maintenance.status)">
            {{ getStatusText(maintenance.status) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="维修技师">
          {{ maintenance.technician_name || '未分配' }}
        </el-descriptions-item>
        <el-descriptions-item label="报修时间">
          {{ maintenance.created_at }}
        </el-descriptions-item>
        <el-descriptions-item label="开始维修">
          {{ maintenance.repair_start_date || '未开始' }}
        </el-descriptions-item>
        <el-descriptions-item label="完成维修">
          {{ maintenance.repair_end_date || '未完成' }}
        </el-descriptions-item>
        <el-descriptions-item label="维修费用">
          {{ maintenance.repair_cost ? `¥${maintenance.repair_cost.toLocaleString()}` : '未填写' }}
        </el-descriptions-item>
        <el-descriptions-item label="维修天数">
          {{ getRepairDays() }}
        </el-descriptions-item>
      </el-descriptions>
      
      <!-- 故障描述 -->
      <div class="detail-section">
        <h4>故障描述</h4>
        <div class="content-text">
          {{ maintenance.fault_description }}
        </div>
      </div>
      
      <!-- 维修描述 -->
      <div v-if="maintenance.repair_description" class="detail-section">
        <h4>维修描述</h4>
        <div class="content-text">
          {{ maintenance.repair_description }}
        </div>
      </div>
      
      <!-- 使用配件 -->
      <div v-if="maintenance.parts_used" class="detail-section">
        <h4>使用配件</h4>
        <div class="content-text">
          {{ maintenance.parts_used }}
        </div>
      </div>
      
      <!-- 设备信息 -->
      <div v-if="maintenance.equipment" class="detail-section">
        <h4>设备信息</h4>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="设备型号">
            {{ maintenance.equipment.model }}
          </el-descriptions-item>
          <el-descriptions-item label="设备品牌">
            {{ maintenance.equipment.brand }}
          </el-descriptions-item>
          <el-descriptions-item label="存放位置">
            {{ maintenance.equipment.location }}
          </el-descriptions-item>
          <el-descriptions-item label="采购日期">
            {{ maintenance.equipment.purchase_date }}
          </el-descriptions-item>
          <el-descriptions-item label="保修期">
            {{ maintenance.equipment.warranty_period }}个月
          </el-descriptions-item>
          <el-descriptions-item label="供应商">
            {{ maintenance.equipment.supplier }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 维修进度时间线 -->
      <div class="detail-section">
        <h4>维修进度</h4>
        <el-timeline>
          <el-timeline-item
            timestamp="报修时间"
            :time="maintenance.created_at"
            type="primary"
          >
            {{ maintenance.reporter_name }} 提交维修申请
            <div class="timeline-content">
              故障类型：{{ getFaultTypeText(maintenance.fault_type) }}
            </div>
          </el-timeline-item>
          
          <el-timeline-item
            v-if="maintenance.technician_name"
            timestamp="分配技师"
            type="success"
          >
            分配给技师：{{ maintenance.technician_name }}
          </el-timeline-item>
          
          <el-timeline-item
            v-if="maintenance.repair_start_date"
            timestamp="开始维修"
            :time="maintenance.repair_start_date"
            type="warning"
          >
            开始维修设备
          </el-timeline-item>
          
          <el-timeline-item
            v-if="maintenance.repair_end_date"
            timestamp="完成维修"
            :time="maintenance.repair_end_date"
            type="success"
          >
            维修完成
            <div v-if="maintenance.repair_cost" class="timeline-content">
              维修费用：¥{{ maintenance.repair_cost.toLocaleString() }}
            </div>
          </el-timeline-item>
          
          <el-timeline-item
            v-if="maintenance.status === 4"
            timestamp="取消维修"
            type="danger"
          >
            维修申请已取消
          </el-timeline-item>
        </el-timeline>
      </div>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
        <el-button 
          v-if="canEdit"
          type="primary" 
          @click="handleEdit"
        >
          编辑维修
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { EquipmentMaintenance } from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  maintenance?: EquipmentMaintenance | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'edit', maintenance: EquipmentMaintenance): void
}

const props = withDefaults(defineProps<Props>(), {
  maintenance: null
})

const emit = defineEmits<Emits>()

const authStore = useAuthStore()

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const canEdit = computed(() => {
  return props.maintenance && 
         [1, 2].includes(props.maintenance.status) &&
         authStore.hasPermission('equipment:maintenance:edit')
})

// 故障类型标签类型
const getFaultTypeTagType = (faultType: number) => {
  const typeMap: Record<number, string> = {
    1: 'danger',
    2: 'warning',
    3: 'primary',
    4: 'info'
  }
  return typeMap[faultType] || 'info'
}

// 故障类型文本
const getFaultTypeText = (faultType: number) => {
  const textMap: Record<number, string> = {
    1: '硬件故障',
    2: '软件故障',
    3: '使用损坏',
    4: '自然老化'
  }
  return textMap[faultType] || '未知'
}

// 优先级标签类型
const getPriorityTagType = (priority: number) => {
  const typeMap: Record<number, string> = {
    1: 'info',
    2: 'warning',
    3: 'danger',
    4: 'danger'
  }
  return typeMap[priority] || 'info'
}

// 优先级文本
const getPriorityText = (priority: number) => {
  const textMap: Record<number, string> = {
    1: '低',
    2: '中',
    3: '高',
    4: '紧急'
  }
  return textMap[priority] || '未知'
}

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'warning',
    2: 'primary',
    3: 'success',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '待处理',
    2: '处理中',
    3: '已完成',
    4: '已取消'
  }
  return textMap[status] || '未知'
}

// 计算维修天数
const getRepairDays = () => {
  if (!props.maintenance) return '-'
  
  if (props.maintenance.repair_end_date && props.maintenance.repair_start_date) {
    const start = dayjs(props.maintenance.repair_start_date)
    const end = dayjs(props.maintenance.repair_end_date)
    return `${end.diff(start, 'day') + 1}天`
  } else if (props.maintenance.repair_start_date) {
    const start = dayjs(props.maintenance.repair_start_date)
    const now = dayjs()
    return `${now.diff(start, 'day') + 1}天`
  }
  return '-'
}

// 编辑维修
const handleEdit = () => {
  if (props.maintenance) {
    emit('edit', props.maintenance)
    handleClose()
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.maintenance-detail {
  max-height: 600px;
  overflow-y: auto;
}

.detail-section {
  margin-top: 24px;
}

.detail-section h4 {
  margin: 0 0 12px 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.content-text {
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
  line-height: 1.6;
  white-space: pre-wrap;
}

.timeline-content {
  margin-top: 4px;
  font-size: 12px;
  color: #909399;
}

.dialog-footer {
  text-align: right;
}
</style>
