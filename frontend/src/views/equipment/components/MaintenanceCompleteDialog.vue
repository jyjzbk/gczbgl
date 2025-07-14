<template>
  <el-dialog
    v-model="visible"
    title="完成维修"
    width="700px"
    :before-close="handleClose"
  >
    <div v-if="maintenance" class="complete-content">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="设备名称">
          {{ maintenance.equipment?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备编号">
          {{ maintenance.equipment?.code }}
        </el-descriptions-item>
        <el-descriptions-item label="故障类型">
          <el-tag :type="getFaultTypeTagType(maintenance.fault_type)">
            {{ getFaultTypeText(maintenance.fault_type) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="维修技师">
          {{ maintenance.technician_name }}
        </el-descriptions-item>
        <el-descriptions-item label="开始维修">
          {{ maintenance.repair_start_date }}
        </el-descriptions-item>
        <el-descriptions-item label="维修天数">
          {{ getRepairDays() }}天
        </el-descriptions-item>
        <el-descriptions-item label="故障描述" :span="2">
          {{ maintenance.fault_description }}
        </el-descriptions-item>
      </el-descriptions>
      
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="120px"
        style="margin-top: 20px"
      >
        <el-form-item label="完成日期" prop="repair_end_date">
          <el-date-picker
            v-model="form.repair_end_date"
            type="date"
            placeholder="请选择完成日期"
            style="width: 100%"
            value-format="YYYY-MM-DD"
            :disabled-date="disabledDate"
          />
        </el-form-item>
        
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="维修费用" prop="repair_cost">
              <el-input-number
                v-model="form.repair_cost"
                placeholder="请输入维修费用"
                :min="0"
                :precision="2"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="费用类型">
              <el-select
                v-model="costType"
                placeholder="请选择费用类型"
                style="width: 100%"
              >
                <el-option label="人工费" value="labor" />
                <el-option label="配件费" value="parts" />
                <el-option label="外包费" value="outsource" />
                <el-option label="其他费用" value="other" />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        
        <el-form-item label="使用配件">
          <el-input
            v-model="form.parts_used"
            type="textarea"
            placeholder="请输入使用的配件清单"
            :rows="3"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
        
        <el-form-item label="维修描述" prop="repair_description">
          <el-input
            v-model="form.repair_description"
            type="textarea"
            placeholder="请详细描述维修过程、解决方案、注意事项等"
            :rows="4"
            maxlength="1000"
            show-word-limit
          />
        </el-form-item>
        
        <el-form-item label="设备状态">
          <el-radio-group v-model="equipmentStatus">
            <el-radio :label="1">维修后正常</el-radio>
            <el-radio :label="3">需要继续维修</el-radio>
            <el-radio :label="4">建议报废</el-radio>
          </el-radio-group>
        </el-form-item>
        
        <!-- 费用明细 -->
        <div v-if="form.repair_cost && form.repair_cost > 0" class="cost-breakdown">
          <h4>费用明细</h4>
          <el-table :data="costItems" border size="small">
            <el-table-column prop="type" label="费用类型" width="120" />
            <el-table-column prop="description" label="描述" />
            <el-table-column prop="amount" label="金额" width="120" align="right">
              <template #default="{ row }">
                ¥{{ row.amount.toLocaleString() }}
              </template>
            </el-table-column>
            <el-table-column label="操作" width="80">
              <template #default="{ $index }">
                <el-button
                  type="danger"
                  size="small"
                  text
                  @click="removeCostItem($index)"
                >
                  删除
                </el-button>
              </template>
            </el-table-column>
          </el-table>
          
          <div class="add-cost-item">
            <el-button size="small" @click="addCostItem">
              添加费用项
            </el-button>
          </div>
        </div>
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
          完成维修
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  completeEquipmentMaintenanceApi,
  type EquipmentMaintenance
} from '@/api/equipment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  maintenance?: EquipmentMaintenance | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface CostItem {
  type: string
  description: string
  amount: number
}

const props = withDefaults(defineProps<Props>(), {
  maintenance: null
})

const emit = defineEmits<Emits>()

const formRef = ref<FormInstance>()
const submitting = ref(false)
const costType = ref('labor')
const equipmentStatus = ref(1)
const costItems = ref<CostItem[]>([])

const form = reactive({
  repair_end_date: dayjs().format('YYYY-MM-DD'),
  repair_cost: 0,
  repair_description: '',
  parts_used: ''
})

const rules: FormRules = {
  repair_end_date: [
    { required: true, message: '请选择完成日期', trigger: 'change' }
  ],
  repair_description: [
    { required: true, message: '请输入维修描述', trigger: 'blur' },
    { min: 10, max: 1000, message: '维修描述长度在 10 到 1000 个字符', trigger: 'blur' }
  ]
}

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    form.repair_end_date = dayjs().format('YYYY-MM-DD')
    form.repair_cost = 0
    form.repair_description = ''
    form.parts_used = ''
    equipmentStatus.value = 1
    costItems.value = []
  }
})

const disabledDate = (time: Date) => {
  if (!props.maintenance?.repair_start_date) return false
  const startTime = new Date(props.maintenance.repair_start_date).getTime()
  return time.getTime() < startTime || time.getTime() > Date.now()
}

// 计算维修天数
const getRepairDays = () => {
  if (!props.maintenance?.repair_start_date) return 0
  const start = dayjs(props.maintenance.repair_start_date)
  const now = dayjs()
  return now.diff(start, 'day') + 1
}

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

// 添加费用项
const addCostItem = () => {
  costItems.value.push({
    type: costType.value,
    description: '',
    amount: 0
  })
}

// 删除费用项
const removeCostItem = (index: number) => {
  costItems.value.splice(index, 1)
}

const handleSubmit = async () => {
  if (!formRef.value || !props.maintenance) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    const updateData = {
      ...form,
      status: 3 // 已完成
    }
    
    await completeEquipmentMaintenanceApi(props.maintenance.id, updateData)
    ElMessage.success('维修完成')
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('完成维修失败:', error)
    ElMessage.error('完成维修失败')
  } finally {
    submitting.value = false
  }
}

const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.complete-content {
  min-height: 300px;
}

.cost-breakdown {
  margin-top: 20px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.cost-breakdown h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
}

.add-cost-item {
  margin-top: 12px;
  text-align: center;
}

.dialog-footer {
  text-align: right;
}
</style>
