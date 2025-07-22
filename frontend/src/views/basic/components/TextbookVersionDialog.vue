<template>
  <el-dialog
    :model-value="modelValue"
    :title="isEdit ? '编辑教材版本' : '新增教材版本'"
    width="600px"
    @update:model-value="$emit('update:modelValue', $event)"
    @close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
    >
      <el-form-item label="版本名称" prop="name">
        <el-input
          v-model="form.name"
          placeholder="请输入版本名称，如：人教版"
          maxlength="50"
          show-word-limit
        />
      </el-form-item>
      
      <el-form-item label="版本代码" prop="code">
        <el-input
          v-model="form.code"
          placeholder="请输入版本代码，如：PEP"
          maxlength="20"
          show-word-limit
        />
      </el-form-item>
      
      <el-form-item label="出版社" prop="publisher">
        <el-input
          v-model="form.publisher"
          placeholder="请输入出版社名称"
          maxlength="100"
          show-word-limit
        />
      </el-form-item>
      
      <el-form-item label="排序" prop="sort_order">
        <el-input-number
          v-model="form.sort_order"
          :min="0"
          :max="999"
          placeholder="排序值，数字越小越靠前"
          style="width: 200px"
        />
      </el-form-item>
      
      <el-form-item label="状态" prop="status">
        <el-radio-group v-model="form.status">
          <el-radio :label="true">启用</el-radio>
          <el-radio :label="false">禁用</el-radio>
        </el-radio-group>
      </el-form-item>
      
      <el-form-item label="描述" prop="description">
        <el-input
          v-model="form.description"
          type="textarea"
          :rows="3"
          placeholder="请输入版本描述信息"
          maxlength="500"
          show-word-limit
        />
      </el-form-item>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="loading" @click="handleSubmit">
          {{ isEdit ? '更新' : '创建' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import type { CreateTextbookVersionParams } from '@/api/experiment'
import { 
  createTextbookVersionApi, 
  updateTextbookVersionApi 
} from '@/api/experiment'

// Props
interface Props {
  modelValue: boolean
  formData: Partial<CreateTextbookVersionParams>
  isEdit: boolean
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  success: []
}>()

// 响应式数据
const loading = ref(false)
const formRef = ref<FormInstance>()

// 表单数据
const form = reactive<CreateTextbookVersionParams>({
  name: '',
  code: '',
  publisher: '',
  description: '',
  status: true,
  sort_order: 0
})

// 表单验证规则
const rules: FormRules = {
  name: [
    { required: true, message: '请输入版本名称', trigger: 'blur' },
    { min: 2, max: 50, message: '版本名称长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入版本代码', trigger: 'blur' },
    { min: 2, max: 20, message: '版本代码长度在 2 到 20 个字符', trigger: 'blur' },
    { pattern: /^[A-Z0-9_]+$/, message: '版本代码只能包含大写字母、数字和下划线', trigger: 'blur' }
  ],
  publisher: [
    { max: 100, message: '出版社名称不能超过 100 个字符', trigger: 'blur' }
  ],
  sort_order: [
    { required: true, message: '请输入排序值', trigger: 'blur' },
    { type: 'number', min: 0, max: 999, message: '排序值必须在 0 到 999 之间', trigger: 'blur' }
  ],
  status: [
    { required: true, message: '请选择状态', trigger: 'change' }
  ],
  description: [
    { max: 500, message: '描述不能超过 500 个字符', trigger: 'blur' }
  ]
}

// 监听表单数据变化
watch(
  () => props.formData,
  (newData) => {
    if (newData) {
      Object.assign(form, {
        name: newData.name || '',
        code: newData.code || '',
        publisher: newData.publisher || '',
        description: newData.description || '',
        status: newData.status ?? true,
        sort_order: newData.sort_order || 0
      })
    }
  },
  { immediate: true, deep: true }
)

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    loading.value = true
    
    if (props.isEdit) {
      // 编辑模式需要传递ID，这里假设从formData中获取
      const id = (props.formData as any).id
      await updateTextbookVersionApi(id, form)
      ElMessage.success('更新成功')
    } else {
      await createTextbookVersionApi(form)
      ElMessage.success('创建成功')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('操作失败')
  } finally {
    loading.value = false
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
  // 重置表单
  if (formRef.value) {
    formRef.value.resetFields()
  }
}
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

:deep(.el-form-item__label) {
  font-weight: 500;
}

:deep(.el-input__count) {
  color: #909399;
  font-size: 12px;
}
</style>
