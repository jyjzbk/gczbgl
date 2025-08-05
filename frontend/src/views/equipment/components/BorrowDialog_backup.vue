<template>
  <el-dialog
    v-model="visible"
    title="设备借用申请"
    width="700px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
    >
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
import { ref, reactive, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { useAuthStore } from '@/stores/auth'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const authStore = useAuthStore()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)

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

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true
    
    // TODO: 调用API提交表单
    ElMessage.success('借用申请提交成功')
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
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
