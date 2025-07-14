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
                :disabled="equipment.status !== 1"
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
          <el-form-item label="借用人" prop="borrower_name">
            <el-input
              v-model="form.borrower_name"
              placeholder="请输入借用人姓名"
              maxlength="50"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="联系电话" prop="borrower_phone">
            <el-input
              v-model="form.borrower_phone"
              placeholder="请输入联系电话"
              maxlength="20"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="借用日期" prop="borrow_date">
            <el-date-picker
              v-model="form.borrow_date"
              type="date"
              placeholder="请选择借用日期"
              style="width: 100%"
              value-format="YYYY-MM-DD"
              :disabled-date="disabledBorrowDate"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="预计归还日期" prop="expected_return_date">
            <el-date-picker
              v-model="form.expected_return_date"
              type="date"
              placeholder="请选择预计归还日期"
              style="width: 100%"
              value-format="YYYY-MM-DD"
              :disabled-date="disabledReturnDate"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="借用天数">
            <el-input
              :value="borrowDays"
              readonly
              placeholder="自动计算"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="借用用途" prop="purpose">
        <el-input
          v-model="form.purpose"
          type="textarea"
          placeholder="请输入借用用途"
          :rows="3"
          maxlength="500"
          show-word-limit
        />
      </el-form-item>
      
      <el-form-item label="备注">
        <el-input
          v-model="form.remark"
          type="textarea"
          placeholder="请输入备注信息"
          :rows="2"
          maxlength="200"
          show-word-limit
        />
      </el-form-item>
      
      <!-- 设备可用性检查结果 -->
      <div v-if="availabilityResult" class="availability-result">
        <el-alert
          :title="availabilityResult.available ? '设备可借用' : '设备不可借用'"
          :type="availabilityResult.available ? 'success' : 'error'"
          :closable="false"
          show-icon
        >
          <template #default>
            <div v-if="!availabilityResult.available">
              <p>该时间段设备不可借用，原因：</p>
              <ul>
                <li v-for="reason in availabilityResult.reasons" :key="reason">
                  {{ reason }}
                </li>
              </ul>
            </div>
            <div v-else>
              该时间段设备可正常借用
            </div>
          </template>
        </el-alert>
      </div>
      
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
        </el-descriptions>
      </div>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button 
          type="primary" 
          @click="handleSubmit" 
          :loading="submitting"
          :disabled="!canSubmit"
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
  createEquipmentBorrowApi,
  updateEquipmentBorrowApi,
  getEquipmentsApi,
  checkEquipmentAvailabilityApi,
  type EquipmentBorrow,
  type Equipment,
  type CreateEquipmentBorrowParams
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  borrow?: EquipmentBorrow | null
  mode: 'create' | 'edit'
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface AvailabilityResult {
  available: boolean
  reasons?: string[]
}

const props = withDefaults(defineProps<Props>(), {
  borrow: null
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
const availabilityResult = ref<AvailabilityResult | null>(null)

// 表单数据
const form = reactive<CreateEquipmentBorrowParams>({
  equipment_id: 0,
  borrower_id: authStore.userInfo?.id || 0,
  borrower_name: authStore.userInfo?.real_name || '',
  borrower_phone: authStore.userInfo?.phone || '',
  borrow_date: '',
  expected_return_date: '',
  purpose: '',
  remark: ''
})

// 表单验证规则
const rules: FormRules = {
  equipment_id: [
    { required: true, message: '请选择设备', trigger: 'change' }
  ],
  borrower_name: [
    { required: true, message: '请输入借用人姓名', trigger: 'blur' },
    { min: 2, max: 50, message: '姓名长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  borrower_phone: [
    { required: true, message: '请输入联系电话', trigger: 'blur' },
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号码', trigger: 'blur' }
  ],
  borrow_date: [
    { required: true, message: '请选择借用日期', trigger: 'change' }
  ],
  expected_return_date: [
    { required: true, message: '请选择预计归还日期', trigger: 'change' }
  ],
  purpose: [
    { required: true, message: '请输入借用用途', trigger: 'blur' },
    { min: 5, max: 500, message: '借用用途长度在 5 到 500 个字符', trigger: 'blur' }
  ]
}

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新增借用申请' : '编辑借用申请'
})

const borrowDays = computed(() => {
  if (form.borrow_date && form.expected_return_date) {
    const start = dayjs(form.borrow_date)
    const end = dayjs(form.expected_return_date)
    return end.diff(start, 'day') + 1
  }
  return 0
})

const canSubmit = computed(() => {
  return !availabilityResult.value || availabilityResult.value.available
})

// 监听借用数据变化
watch(() => props.borrow, (newVal) => {
  if (newVal && props.mode === 'edit') {
    Object.assign(form, {
      equipment_id: newVal.equipment_id,
      borrower_id: newVal.borrower_id,
      borrower_name: newVal.borrower_name,
      borrower_phone: newVal.borrower_phone,
      borrow_date: newVal.borrow_date,
      expected_return_date: newVal.expected_return_date,
      purpose: newVal.purpose,
      remark: newVal.remark || ''
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

// 监听日期变化，检查设备可用性
watch([() => form.equipment_id, () => form.borrow_date, () => form.expected_return_date],
  ([equipmentId, borrowDate, returnDate]) => {
    if (equipmentId && borrowDate && returnDate) {
      checkAvailability()
    } else {
      availabilityResult.value = null
    }
  }
)

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    equipment_id: 0,
    borrower_id: authStore.userInfo?.id || 0,
    borrower_name: authStore.userInfo?.real_name || '',
    borrower_phone: authStore.userInfo?.phone || '',
    borrow_date: '',
    expected_return_date: '',
    purpose: '',
    remark: ''
  })
  selectedEquipment.value = null
  equipmentOptions.value = []
  availabilityResult.value = null
  formRef.value?.clearValidate()
}

// 搜索设备
const searchEquipments = async (query: string) => {
  if (!query) return

  equipmentLoading.value = true
  try {
    const response = await getEquipmentsApi({
      search: query,
      status: 1, // 只搜索正常状态的设备
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

// 检查设备可用性
const checkAvailability = async () => {
  if (!form.equipment_id || !form.borrow_date || !form.expected_return_date) return

  try {
    const response = await checkEquipmentAvailabilityApi(
      form.equipment_id,
      form.borrow_date,
      form.expected_return_date
    )
    availabilityResult.value = response.data
  } catch (error) {
    console.error('检查设备可用性失败:', error)
    availabilityResult.value = {
      available: false,
      reasons: ['检查设备可用性失败，请稍后重试']
    }
  }
}

// 禁用借用日期
const disabledBorrowDate = (time: Date) => {
  return time.getTime() < Date.now() - 24 * 60 * 60 * 1000
}

// 禁用归还日期
const disabledReturnDate = (time: Date) => {
  if (!form.borrow_date) return true
  const borrowTime = new Date(form.borrow_date).getTime()
  return time.getTime() <= borrowTime
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

    if (!canSubmit.value) {
      ElMessage.error('设备在该时间段不可借用')
      return
    }

    submitting.value = true

    if (props.mode === 'create') {
      await createEquipmentBorrowApi(form)
      ElMessage.success('借用申请提交成功')
    } else {
      await updateEquipmentBorrowApi(props.borrow!.id, form)
      ElMessage.success('借用申请更新成功')
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

.availability-result {
  margin: 16px 0;
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

:deep(.el-select-dropdown__item.is-disabled) {
  color: #c0c4cc;
  cursor: not-allowed;
}
</style>
