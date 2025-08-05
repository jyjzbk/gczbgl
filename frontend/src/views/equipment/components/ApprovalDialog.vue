<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="600px"
    :before-close="handleClose"
  >
    <div class="approval-content">
      <div v-if="!batchMode && borrow" class="single-approval">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="设备名称">
            {{ borrow.equipment?.name }}
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
          <el-descriptions-item label="借用用途" :span="2">
            {{ borrow.purpose }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <div v-else-if="batchMode" class="batch-approval">
        <p>共选择了 {{ selectedBorrows.length }} 个借用申请</p>
        <el-table :data="selectedBorrows" border size="small" max-height="300">
          <el-table-column prop="equipment.name" label="设备名称" />
          <el-table-column prop="borrower_name" label="借用人" />
          <el-table-column prop="borrow_date" label="借用日期" />
        </el-table>
      </div>
      
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="100px"
        style="margin-top: 20px"
      >
        <el-form-item label="审批结果" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">批准</el-radio>
            <el-radio :label="6">拒绝</el-radio>
          </el-radio-group>
        </el-form-item>
        
        <el-form-item label="审批意见" prop="remark">
          <el-input
            v-model="form.remark"
            type="textarea"
            placeholder="请输入审批意见"
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
          确认审批
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  reviewEquipmentBorrowApi,
  type EquipmentBorrow
} from '@/api/equipment'

interface Props {
  modelValue: boolean
  borrow?: EquipmentBorrow | null
  batchMode?: boolean
  selectedBorrows?: EquipmentBorrow[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = withDefaults(defineProps<Props>(), {
  borrow: null,
  batchMode: false,
  selectedBorrows: () => []
})

const emit = defineEmits<Emits>()

const formRef = ref<FormInstance>()
const submitting = ref(false)

const form = reactive({
  status: 1,
  remark: ''
})

const rules: FormRules = {
  status: [
    { required: true, message: '请选择审批结果', trigger: 'change' }
  ],
  remark: [
    { required: true, message: '请输入审批意见', trigger: 'blur' }
  ]
}

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.batchMode ? '批量审批' : '审批借用申请'
})

const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    if (props.batchMode) {
      // 批量审批
      const promises = props.selectedBorrows
        .filter(borrow => borrow.status === 1)
        .map(borrow => reviewEquipmentBorrowApi(borrow.id, form))
      
      await Promise.all(promises)
      ElMessage.success('批量审批完成')
    } else if (props.borrow) {
      // 单个审批
      await reviewEquipmentBorrowApi(props.borrow.id, form)
      ElMessage.success('审批完成')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('审批失败:', error)
    ElMessage.error('审批失败')
  } finally {
    submitting.value = false
  }
}

const handleClose = () => {
  form.status = 2
  form.remark = ''
  emit('update:modelValue', false)
}
</script>

<style scoped>
.approval-content {
  min-height: 200px;
}

.dialog-footer {
  text-align: right;
}
</style>
