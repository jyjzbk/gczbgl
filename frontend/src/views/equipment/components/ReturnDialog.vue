<template>
  <el-dialog
    v-model="visible"
    title="设备归还"
    width="600px"
    :before-close="handleClose"
  >
    <div v-if="borrow" class="return-content">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="设备名称">
          {{ borrow.equipment?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备编号">
          {{ borrow.equipment?.code }}
        </el-descriptions-item>
        <el-descriptions-item label="借用人">
          {{ borrow.borrower_name }}
        </el-descriptions-item>
        <el-descriptions-item label="借用日期">
          {{ borrow.borrow_date }}
        </el-descriptions-item>
        <el-descriptions-item label="预计归还">
          {{ borrow.expected_return_date }}
        </el-descriptions-item>
        <el-descriptions-item label="逾期天数">
          <span v-if="overdueDays > 0" class="overdue-text">
            {{ overdueDays }}天
          </span>
          <span v-else class="normal-text">无逾期</span>
        </el-descriptions-item>
      </el-descriptions>
      
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="120px"
        style="margin-top: 20px"
      >
        <el-form-item label="实际归还日期" prop="actual_return_date">
          <el-date-picker
            v-model="form.actual_return_date"
            type="date"
            placeholder="请选择实际归还日期"
            style="width: 100%"
            value-format="YYYY-MM-DD"
            :disabled-date="disabledDate"
          />
        </el-form-item>
        
        <el-form-item label="归还备注">
          <el-input
            v-model="form.remark"
            type="textarea"
            placeholder="请输入归还备注"
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
          确认归还
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  returnEquipmentApi,
  type EquipmentBorrow
} from '@/api/equipment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  borrow?: EquipmentBorrow | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = withDefaults(defineProps<Props>(), {
  borrow: null
})

const emit = defineEmits<Emits>()

const formRef = ref<FormInstance>()
const submitting = ref(false)

const form = reactive({
  actual_return_date: dayjs().format('YYYY-MM-DD'),
  remark: ''
})

const rules: FormRules = {
  actual_return_date: [
    { required: true, message: '请选择实际归还日期', trigger: 'change' }
  ]
}

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const overdueDays = computed(() => {
  if (!props.borrow?.expected_return_date) return 0
  
  const today = dayjs()
  const expectedDate = dayjs(props.borrow.expected_return_date)
  
  if (today.isAfter(expectedDate)) {
    return today.diff(expectedDate, 'day')
  }
  
  return 0
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    form.actual_return_date = dayjs().format('YYYY-MM-DD')
    form.remark = ''
  }
})

const disabledDate = (time: Date) => {
  if (!props.borrow?.borrow_date) return false
  const borrowTime = new Date(props.borrow.borrow_date).getTime()
  return time.getTime() < borrowTime || time.getTime() > Date.now()
}

const handleSubmit = async () => {
  if (!formRef.value || !props.borrow) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    await returnEquipmentApi(props.borrow.id, form)
    ElMessage.success('设备归还成功')
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('归还失败:', error)
    ElMessage.error('归还失败')
  } finally {
    submitting.value = false
  }
}

const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.return-content {
  min-height: 200px;
}

.overdue-text {
  color: #f56c6c;
  font-weight: 600;
}

.normal-text {
  color: #67c23a;
}

.dialog-footer {
  text-align: right;
}
</style>
