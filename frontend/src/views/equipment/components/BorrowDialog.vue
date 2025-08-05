<template>
  <el-dialog
    v-model="visible"
    title="设备借用申请"
    width="800px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
    >
      <!-- 设备选择部分 -->
      <el-form-item label="选择设备" prop="selectedEquipments">
        <div class="equipment-selector">
          <div class="search-bar">
            <el-input
              v-model="equipmentSearchQuery"
              placeholder="请搜索并选择借用的设备"
              clearable
              @input="handleEquipmentSearch"
            >
              <template #append>
                <el-button @click="addEquipment" :disabled="!currentEquipmentId">
                  添加设备
                </el-button>
              </template>
            </el-input>
          </div>

          <!-- 设备搜索结果 -->
          <div v-if="equipmentOptions.length > 0" class="equipment-options">
            <el-select
              v-model="currentEquipmentId"
              placeholder="请选择设备"
              style="width: 100%"
              filterable
            >
              <el-option
                v-for="equipment in equipmentOptions"
                :key="equipment.id"
                :label="`${equipment.name} (${equipment.code}) - 可借用: ${equipment.available_quantity || 0}`"
                :value="equipment.id"
                :disabled="equipment.available_quantity <= 0"
              />
            </el-select>
          </div>

          <!-- 已选设备列表 -->
          <div v-if="selectedEquipments.length > 0" class="selected-equipments">
            <h4>已选设备 ({{ selectedEquipments.length }})</h4>
            <el-table :data="selectedEquipments" border size="small">
              <el-table-column prop="name" label="设备名称" min-width="150" />
              <el-table-column prop="code" label="设备编号" width="120" />
              <el-table-column label="借用数量" width="120" align="center">
                <template #default="{ row, $index }">
                  <el-input-number
                    v-model="row.quantity"
                    :min="1"
                    :max="row.available_quantity || 1"
                    size="small"
                    @change="updateQuantity($index, $event)"
                  />
                </template>
              </el-table-column>
              <el-table-column prop="unit" label="单位" width="80" />
              <el-table-column label="操作" width="80">
                <template #default="{ $index }">
                  <el-button
                    type="danger"
                    size="small"
                    link
                    @click="removeEquipment($index)"
                  >
                    移除
                  </el-button>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </el-form-item>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="借用人" prop="borrower_name">
            <el-input
              v-model="form.borrower_name"
              placeholder="请输入借用人姓名"
            />
          </el-form-item>
        </el-col>

        <el-col :span="12">
          <el-form-item label="联系电话" prop="borrower_phone">
            <el-input
              v-model="form.borrower_phone"
              placeholder="请输入联系电话"
            />
          </el-form-item>
        </el-col>
      </el-row>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="借用日期" prop="borrow_date">
            <el-date-picker
              v-model="form.borrow_date"
              type="date"
              placeholder="选择借用日期"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>

        <el-col :span="12">
          <el-form-item label="预计归还日期" prop="expected_return_date">
            <el-date-picker
              v-model="form.expected_return_date"
              type="date"
              placeholder="选择归还日期"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item label="借用用途" prop="purpose">
        <el-input
          v-model="form.purpose"
          type="textarea"
          :rows="3"
          placeholder="请输入借用用途"
        />
      </el-form-item>

      <el-form-item label="备注">
        <el-input
          v-model="form.remark"
          type="textarea"
          :rows="2"
          placeholder="请输入备注信息"
        />
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          提交申请
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { useAuthStore } from '@/stores/auth'
import { getEquipmentsApi } from '@/api/equipment'
import { createBatchEquipmentBorrowApi, type CreateBatchEquipmentBorrowParams } from '@/api/equipment'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface Equipment {
  id: number
  name: string
  code: string
  quantity: number
  available_quantity: number
  unit: string
}

interface SelectedEquipment extends Equipment {
  quantity: number
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const authStore = useAuthStore()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)

// 设备搜索相关
const equipmentSearchQuery = ref('')
const equipmentOptions = ref<Equipment[]>([])
const currentEquipmentId = ref<number | null>(null)
const selectedEquipments = ref<SelectedEquipment[]>([])

// 表单数据
const form = reactive({
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
  selectedEquipments: [
    {
      validator: (rule, value, callback) => {
        if (selectedEquipments.value.length === 0) {
          callback(new Error('请至少选择一个设备'))
        } else {
          callback()
        }
      },
      trigger: 'change'
    }
  ],
  borrower_name: [
    { required: true, message: '请输入借用人姓名', trigger: 'blur' }
  ],
  borrower_phone: [
    { required: true, message: '请输入联系电话', trigger: 'blur' }
  ],
  borrow_date: [
    { required: true, message: '请选择借用日期', trigger: 'change' }
  ],
  expected_return_date: [
    { required: true, message: '请选择归还日期', trigger: 'change' }
  ],
  purpose: [
    { required: true, message: '请输入借用用途', trigger: 'blur' }
  ]
}

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 搜索设备
const handleEquipmentSearch = async () => {
  if (!equipmentSearchQuery.value.trim()) {
    equipmentOptions.value = []
    return
  }

  try {
    const response = await getEquipmentsApi({
      search: equipmentSearchQuery.value,
      status: 1, // 只搜索正常状态的设备
      per_page: 20
    })
    equipmentOptions.value = response.data.items || []
  } catch (error) {
    console.error('搜索设备失败:', error)
    ElMessage.error('搜索设备失败')
  }
}

// 添加设备
const addEquipment = () => {
  if (!currentEquipmentId.value) return

  const equipment = equipmentOptions.value.find(eq => eq.id === currentEquipmentId.value)
  if (!equipment) return

  // 检查是否已经添加过
  const exists = selectedEquipments.value.find(eq => eq.id === equipment.id)
  if (exists) {
    ElMessage.warning('该设备已经添加过了')
    return
  }

  // 添加到已选列表
  selectedEquipments.value.push({
    ...equipment,
    quantity: 1
  })

  // 清空选择
  currentEquipmentId.value = null
  equipmentSearchQuery.value = ''
  equipmentOptions.value = []
}

// 更新设备数量
const updateQuantity = (index: number, quantity: number) => {
  if (selectedEquipments.value[index]) {
    selectedEquipments.value[index].quantity = quantity
  }
}

// 移除设备
const removeEquipment = (index: number) => {
  selectedEquipments.value.splice(index, 1)
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()

    if (selectedEquipments.value.length === 0) {
      ElMessage.error('请至少选择一个设备')
      return
    }

    submitting.value = true

    // 构建提交数据
    const submitData: CreateBatchEquipmentBorrowParams = {
      equipment_ids: selectedEquipments.value.map(item => item.id),
      quantities: selectedEquipments.value.map(item => item.quantity),
      borrower_id: form.borrower_id,
      borrow_date: typeof form.borrow_date === 'string' ? form.borrow_date : form.borrow_date.toISOString().split('T')[0],
      expected_return_date: typeof form.expected_return_date === 'string' ? form.expected_return_date : form.expected_return_date.toISOString().split('T')[0],
      purpose: form.purpose,
      remark: form.remark
    }

    console.log('提交数据:', submitData)

    // 调用API提交表单
    await createBatchEquipmentBorrowApi(submitData)

    ElMessage.success('借用申请提交成功')
    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('提交失败:', error)

    // 如果是422错误，显示具体的验证错误信息
    if (error.response && error.response.status === 422) {
      const data = error.response.data
      if (data.errors) {
        // Laravel验证错误格式
        const firstError = Object.values(data.errors)[0] as string[]
        ElMessage.error(firstError[0])
      } else if (data.message) {
        // 自定义错误信息
        ElMessage.error(data.message)
      } else {
        ElMessage.error('请求参数错误')
      }
    } else if (error.response && error.response.data && error.response.data.message) {
      // 其他HTTP错误，显示后端返回的错误信息
      ElMessage.error(error.response.data.message)
    } else {
      // 网络错误或其他未知错误
      ElMessage.error('提交失败，请重试')
    }
  } finally {
    submitting.value = false
  }
}

// 关闭对话框
const handleClose = () => {
  // 重置表单
  formRef.value?.resetFields()

  // 重置设备选择
  selectedEquipments.value = []
  equipmentOptions.value = []
  currentEquipmentId.value = null
  equipmentSearchQuery.value = ''

  emit('update:modelValue', false)
}
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

.equipment-selector {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  padding: 16px;
  background-color: #fafafa;
}

.search-bar {
  margin-bottom: 12px;
}

.equipment-options {
  margin-bottom: 12px;
}

.selected-equipments h4 {
  color: #303133;
  margin-bottom: 12px;
  font-size: 14px;
}

:deep(.el-table) {
  margin-top: 0;
}

:deep(.el-table th) {
  background-color: #f5f7fa;
}
</style>
