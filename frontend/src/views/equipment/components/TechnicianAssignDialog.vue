<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="600px"
    :before-close="handleClose"
  >
    <div class="assign-content">
      <div v-if="!batchMode && maintenance" class="single-assign">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="设备名称">
            {{ maintenance.equipment?.name }}
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
          <el-descriptions-item label="报修人">
            {{ maintenance.reporter_name }}
          </el-descriptions-item>
          <el-descriptions-item label="故障描述" :span="2">
            {{ maintenance.fault_description }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <div v-else-if="batchMode" class="batch-assign">
        <p>共选择了 {{ selectedMaintenances.length }} 个维修申请</p>
        <el-table :data="selectedMaintenances" border size="small" max-height="300">
          <el-table-column prop="equipment.name" label="设备名称" />
          <el-table-column prop="fault_type" label="故障类型">
            <template #default="{ row }">
              {{ getFaultTypeText(row.fault_type) }}
            </template>
          </el-table-column>
          <el-table-column prop="priority" label="优先级">
            <template #default="{ row }">
              {{ getPriorityText(row.priority) }}
            </template>
          </el-table-column>
        </el-table>
      </div>
      
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="100px"
        style="margin-top: 20px"
      >
        <el-form-item label="维修技师" prop="technician_id">
          <el-select
            v-model="form.technician_id"
            placeholder="请选择维修技师"
            filterable
            style="width: 100%"
            @change="handleTechnicianChange"
          >
            <el-option
              v-for="technician in technicians"
              :key="technician.id"
              :label="technician.name"
              :value="technician.id"
            >
              <div class="technician-option">
                <div class="technician-name">{{ technician.name }}</div>
                <div class="technician-info">
                  专业: {{ technician.specialty }} | 当前任务: {{ technician.current_tasks }}个
                </div>
              </div>
            </el-option>
          </el-select>
        </el-form-item>
        
        <el-form-item label="分配说明">
          <el-input
            v-model="form.remark"
            type="textarea"
            placeholder="请输入分配说明或特殊要求"
            :rows="3"
            maxlength="200"
            show-word-limit
          />
        </el-form-item>
      </el-form>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button 
          type="primary" 
          @click="handleSubmit" 
          :loading="submitting"
        >
          确认分配
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  updateEquipmentMaintenanceApi,
  type EquipmentMaintenance
} from '@/api/equipment'

interface Props {
  modelValue: boolean
  maintenance?: EquipmentMaintenance | null
  batchMode?: boolean
  selectedMaintenances?: EquipmentMaintenance[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface Technician {
  id: number
  name: string
  specialty: string
  current_tasks: number
}

const props = withDefaults(defineProps<Props>(), {
  maintenance: null,
  batchMode: false,
  selectedMaintenances: () => []
})

const emit = defineEmits<Emits>()

const formRef = ref<FormInstance>()
const submitting = ref(false)
const technicians = ref<Technician[]>([])

const form = reactive({
  technician_id: 0,
  technician_name: '',
  remark: ''
})

const rules: FormRules = {
  technician_id: [
    { required: true, message: '请选择维修技师', trigger: 'change' }
  ]
}

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.batchMode ? '批量分配技师' : '分配维修技师'
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

// 技师选择变化
const handleTechnicianChange = (technicianId: number) => {
  const technician = technicians.value.find(t => t.id === technicianId)
  if (technician) {
    form.technician_name = technician.name
  }
}

// 加载技师列表
const loadTechnicians = async () => {
  // 模拟技师数据
  technicians.value = [
    { id: 1, name: '张师傅', specialty: '电子设备', current_tasks: 3 },
    { id: 2, name: '李师傅', specialty: '机械设备', current_tasks: 1 },
    { id: 3, name: '王师傅', specialty: '光学仪器', current_tasks: 2 },
    { id: 4, name: '赵师傅', specialty: '计算机设备', current_tasks: 0 }
  ]
}

const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    const updateData = {
      technician_id: form.technician_id,
      technician_name: form.technician_name,
      status: 2 // 更新为处理中状态
    }
    
    if (props.batchMode) {
      // 批量分配
      const promises = props.selectedMaintenances
        .filter(maintenance => maintenance.status === 1)
        .map(maintenance => updateEquipmentMaintenanceApi(maintenance.id, updateData))
      
      await Promise.all(promises)
      ElMessage.success('批量分配完成')
    } else if (props.maintenance) {
      // 单个分配
      await updateEquipmentMaintenanceApi(props.maintenance.id, updateData)
      ElMessage.success('分配完成')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('分配失败:', error)
    ElMessage.error('分配失败')
  } finally {
    submitting.value = false
  }
}

const handleClose = () => {
  form.technician_id = 0
  form.technician_name = ''
  form.remark = ''
  emit('update:modelValue', false)
}

onMounted(() => {
  loadTechnicians()
})
</script>

<style scoped>
.assign-content {
  min-height: 200px;
}

.technician-option {
  padding: 4px 0;
}

.technician-name {
  font-weight: 600;
  margin-bottom: 2px;
}

.technician-info {
  font-size: 12px;
  color: #909399;
}

.dialog-footer {
  text-align: right;
}
</style>
