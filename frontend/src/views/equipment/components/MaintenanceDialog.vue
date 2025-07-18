<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="700px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
      size="large"
    >
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="设备选择" prop="equipment_id">
            <el-select
              v-model="form.equipment_id"
              placeholder="请选择设备"
              filterable
              remote
              :remote-method="searchEquipments"
              :loading="equipmentLoading"
              style="width: 100%"
              @change="handleEquipmentChange"
            >
              <el-option
                v-for="equipment in equipmentOptions"
                :key="equipment.id"
                :label="`${equipment.name} (${equipment.code})`"
                :value="equipment.id"
              >
                <div class="equipment-option">
                  <div class="equipment-name">{{ equipment.name }}</div>
                  <div class="equipment-info">
                    编号: {{ equipment.code }} | 位置: {{ equipment.location }}
                  </div>
                </div>
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="报修人" prop="reporter_name">
            <el-input
              v-model="form.reporter_name"
              placeholder="请输入报修人姓名"
              maxlength="50"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="故障类型" prop="fault_type">
            <el-select
              v-model="form.fault_type"
              placeholder="请选择故障类型"
              style="width: 100%"
            >
              <el-option
                v-for="type in faultTypeOptions"
                :key="type.value"
                :label="type.label"
                :value="type.value"
              />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="优先级" prop="priority">
            <el-select
              v-model="form.priority"
              placeholder="请选择优先级"
              style="width: 100%"
            >
              <el-option
                v-for="priority in priorityOptions"
                :key="priority.value"
                :label="priority.label"
                :value="priority.value"
              />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="故障描述" prop="fault_description">
        <el-input
          v-model="form.fault_description"
          type="textarea"
          placeholder="请详细描述设备故障现象、发生时间、影响范围等"
          :rows="4"
          maxlength="1000"
          show-word-limit
        />
      </el-form-item>
      
      <!-- 选中设备信息 -->
      <div v-if="selectedEquipment" class="selected-equipment">
        <h4>设备信息</h4>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="设备名称">
            {{ selectedEquipment.name }}
          </el-descriptions-item>
          <el-descriptions-item label="设备编号">
            {{ selectedEquipment.code }}
          </el-descriptions-item>
          <el-descriptions-item label="设备型号">
            {{ selectedEquipment.model }}
          </el-descriptions-item>
          <el-descriptions-item label="设备品牌">
            {{ selectedEquipment.brand }}
          </el-descriptions-item>
          <el-descriptions-item label="存放位置">
            {{ selectedEquipment.location }}
          </el-descriptions-item>
          <el-descriptions-item label="设备状态">
            <el-tag :type="getStatusTagType(selectedEquipment.status)">
              {{ getStatusText(selectedEquipment.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="采购日期">
            {{ selectedEquipment.purchase_date }}
          </el-descriptions-item>
          <el-descriptions-item label="保修期">
            {{ selectedEquipment.warranty_period }}个月
          </el-descriptions-item>
        </el-descriptions>
        
        <!-- 保修状态提示 -->
        <div v-if="warrantyStatus" class="warranty-status">
          <el-alert
            :title="warrantyStatus.title"
            :type="warrantyStatus.type"
            :closable="false"
            show-icon
          >
            <template #default>
              {{ warrantyStatus.message }}
            </template>
          </el-alert>
        </div>
      </div>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button 
          type="primary" 
          @click="handleSubmit" 
          :loading="submitting"
        >
          {{ mode === 'create' ? '提交申请' : '更新申请' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  createEquipmentMaintenanceApi,
  updateEquipmentMaintenanceApi,
  getEquipmentsApi,
  type EquipmentMaintenance,
  type Equipment,
  type CreateEquipmentMaintenanceParams
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  maintenance?: EquipmentMaintenance | null
  mode: 'create' | 'edit'
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface WarrantyStatus {
  title: string
  message: string
  type: 'success' | 'warning' | 'error' | 'info'
}

const props = withDefaults(defineProps<Props>(), {
  maintenance: null
})

const emit = defineEmits<Emits>()

// 权限检查
const authStore = useAuthStore()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const equipmentLoading = ref(false)
const equipmentOptions = ref<Equipment[]>([])
const selectedEquipment = ref<Equipment | null>(null)

// 表单数据
const form = reactive<CreateEquipmentMaintenanceParams>({
  equipment_id: 0,
  reporter_id: authStore.userInfo?.id || 0,
  reporter_name: authStore.userInfo?.real_name || '',
  fault_description: '',
  fault_type: 1,
  priority: 2
})

// 选项数据
const faultTypeOptions = [
  { value: 1, label: '硬件故障' },
  { value: 2, label: '软件故障' },
  { value: 3, label: '使用损坏' },
  { value: 4, label: '自然老化' }
]

const priorityOptions = [
  { value: 1, label: '低' },
  { value: 2, label: '中' },
  { value: 3, label: '高' },
  { value: 4, label: '紧急' }
]

// 表单验证规则
const rules: FormRules = {
  equipment_id: [
    { required: true, message: '请选择设备', trigger: 'change' }
  ],
  reporter_name: [
    { required: true, message: '请输入报修人姓名', trigger: 'blur' },
    { min: 2, max: 50, message: '姓名长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  fault_type: [
    { required: true, message: '请选择故障类型', trigger: 'change' }
  ],
  priority: [
    { required: true, message: '请选择优先级', trigger: 'change' }
  ],
  fault_description: [
    { required: true, message: '请输入故障描述', trigger: 'blur' },
    { min: 10, max: 1000, message: '故障描述长度在 10 到 1000 个字符', trigger: 'blur' }
  ]
}

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新增维修申请' : '编辑维修申请'
})

// 保修状态
const warrantyStatus = computed((): WarrantyStatus | null => {
  if (!selectedEquipment.value) return null

  // 检查购入日期和保修期是否有效
  if (!selectedEquipment.value.purchase_date || !selectedEquipment.value.warranty_period) {
    return {
      title: '保修信息不完整',
      message: '设备缺少购入日期或保修期信息，无法确定保修状态',
      type: 'info'
    }
  }

  const purchaseDate = dayjs(selectedEquipment.value.purchase_date)

  // 检查日期是否有效
  if (!purchaseDate.isValid()) {
    return {
      title: '保修信息无效',
      message: '设备购入日期格式无效，无法确定保修状态',
      type: 'warning'
    }
  }

  const warrantyEndDate = purchaseDate.add(selectedEquipment.value.warranty_period, 'month')
  const now = dayjs()

  if (now.isBefore(warrantyEndDate)) {
    const remainingDays = warrantyEndDate.diff(now, 'day')
    return {
      title: '设备在保修期内',
      message: `保修期还剩 ${remainingDays} 天，建议联系供应商进行免费维修`,
      type: 'success'
    }
  } else {
    const expiredDays = now.diff(warrantyEndDate, 'day')
    return {
      title: '设备已过保修期',
      message: `保修期已过期 ${expiredDays} 天，维修费用需要自行承担`,
      type: 'warning'
    }
  }
})

// 监听维修数据变化
watch(() => props.maintenance, (newVal) => {
  if (newVal && props.mode === 'edit') {
    Object.assign(form, {
      equipment_id: newVal.equipment_id,
      reporter_id: newVal.reporter_id,
      reporter_name: newVal.reporter_name,
      fault_description: newVal.fault_description,
      fault_type: newVal.fault_type,
      priority: newVal.priority
    })

    if (newVal.equipment) {
      selectedEquipment.value = newVal.equipment
      equipmentOptions.value = [newVal.equipment]
    }
  }
}, { immediate: true })

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.mode === 'create') {
    resetForm()
  }
})

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    equipment_id: 0,
    reporter_id: authStore.userInfo?.id || 0,
    reporter_name: authStore.userInfo?.real_name || '',
    fault_description: '',
    fault_type: 1,
    priority: 2
  })
  selectedEquipment.value = null
  equipmentOptions.value = []
  formRef.value?.clearValidate()
}

// 搜索设备
const searchEquipments = async (query: string) => {
  if (!query) return

  equipmentLoading.value = true
  try {
    const response = await getEquipmentsApi({
      search: query,
      per_page: 20
    })
    equipmentOptions.value = response.data.items
  } catch (error) {
    console.error('搜索设备失败:', error)
  } finally {
    equipmentLoading.value = false
  }
}

// 设备选择变化
const handleEquipmentChange = (equipmentId: number) => {
  selectedEquipment.value = equipmentOptions.value.find(eq => eq.id === equipmentId) || null
}

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'warning',
    3: 'danger',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '正常',
    2: '借出',
    3: '维修',
    4: '报废'
  }
  return textMap[status] || '未知'
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    if (props.mode === 'create') {
      await createEquipmentMaintenanceApi(form)
      ElMessage.success('维修申请提交成功')
    } else {
      await updateEquipmentMaintenanceApi(props.maintenance!.id, form)
      ElMessage.success('维修申请更新成功')
    }

    emit('success')
    handleClose()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('提交失败')
  } finally {
    submitting.value = false
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}

// 初始化
onMounted(() => {
  // 加载常用设备
  searchEquipments('')
})
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

.equipment-option {
  padding: 4px 0;
}

.equipment-name {
  font-weight: 600;
  margin-bottom: 2px;
}

.equipment-info {
  font-size: 12px;
  color: #909399;
}

.selected-equipment {
  margin-top: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.selected-equipment h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
}

.warranty-status {
  margin-top: 12px;
}
</style>
