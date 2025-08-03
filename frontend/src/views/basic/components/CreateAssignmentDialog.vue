<template>
  <el-dialog
    v-model="visible"
    title="新增教材版本指定"
    width="600px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
      size="large"
    >
      <el-form-item label="目标学校" prop="school_id">
        <el-select
          v-model="form.school_id"
          placeholder="请选择学校"
          filterable
          style="width: 100%"
        >
          <el-option
            v-for="school in schools"
            :key="school.id"
            :label="school.name"
            :value="school.id"
          />
        </el-select>
      </el-form-item>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="学科" prop="subject_id">
            <el-select
              v-model="form.subject_id"
              placeholder="请选择学科"
              style="width: 100%"
            >
              <el-option
                v-for="subject in subjects"
                :key="subject.id"
                :label="subject.name"
                :value="subject.id"
              />
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="年级" prop="grade_level">
            <el-select
              v-model="form.grade_level"
              placeholder="请选择年级"
              style="width: 100%"
            >
              <el-option
                v-for="grade in gradeOptions"
                :key="grade.value"
                :label="grade.label"
                :value="grade.value"
              />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item label="教材版本" prop="textbook_version_id">
        <el-select
          v-model="form.textbook_version_id"
          placeholder="请选择教材版本"
          filterable
          style="width: 100%"
        >
          <el-option
            v-for="version in textbookVersions"
            :key="version.id"
            :label="`${version.name}${version.publisher ? ` (${version.publisher})` : ''}`"
            :value="version.id"
          />
        </el-select>
      </el-form-item>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="生效日期" prop="effective_date">
            <el-date-picker
              v-model="form.effective_date"
              type="date"
              placeholder="选择生效日期"
              style="width: 100%"
              :disabled-date="disabledDate"
            />
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="失效日期" prop="expiry_date">
            <el-date-picker
              v-model="form.expiry_date"
              type="date"
              placeholder="选择失效日期（可选）"
              style="width: 100%"
              :disabled-date="disabledExpiryDate"
            />
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item label="指定理由" prop="assignment_reason">
        <el-input
          v-model="form.assignment_reason"
          type="textarea"
          :rows="3"
          placeholder="请输入指定理由（可选）"
          maxlength="500"
          show-word-limit
        />
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="loading" @click="handleSubmit">
          确定
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { textbookAssignmentApi } from '@/api/textbookAssignment'
import type {
  School,
  CreateAssignmentParams
} from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'

interface Props {
  modelValue: boolean
  schools: School[]
  subjects: Subject[]
  textbookVersions: TextbookVersion[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const formRef = ref<FormInstance>()
const loading = ref(false)
const visible = ref(false)

// 表单数据
const form = reactive<CreateAssignmentParams>({
  school_id: 0,
  subject_id: 0,
  grade_level: '',
  textbook_version_id: 0,
  assignment_reason: '',
  effective_date: '',
  expiry_date: ''
})

// 年级选项
const gradeOptions = [
  { label: '一年级', value: '1' },
  { label: '二年级', value: '2' },
  { label: '三年级', value: '3' },
  { label: '四年级', value: '4' },
  { label: '五年级', value: '5' },
  { label: '六年级', value: '6' },
  { label: '七年级', value: '7' },
  { label: '八年级', value: '8' },
  { label: '九年级', value: '9' },
  { label: '高一', value: '10' },
  { label: '高二', value: '11' },
  { label: '高三', value: '12' }
]

// 表单验证规则
const rules: FormRules = {
  school_id: [
    { required: true, message: '请选择学校', trigger: 'change' }
  ],
  subject_id: [
    { required: true, message: '请选择学科', trigger: 'change' }
  ],
  grade_level: [
    { required: true, message: '请选择年级', trigger: 'change' }
  ],
  textbook_version_id: [
    { required: true, message: '请选择教材版本', trigger: 'change' }
  ],
  effective_date: [
    { required: true, message: '请选择生效日期', trigger: 'change' }
  ]
}

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      resetForm()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 计算属性
const disabledDate = (time: Date) => {
  return time.getTime() < Date.now() - 8.64e7 // 不能选择昨天之前的日期
}

const disabledExpiryDate = (time: Date) => {
  if (!form.effective_date) return false
  const effectiveTime = new Date(form.effective_date).getTime()
  return time.getTime() <= effectiveTime // 失效日期必须晚于生效日期
}

// 方法
const resetForm = () => {
  Object.assign(form, {
    school_id: 0,
    subject_id: 0,
    grade_level: '',
    textbook_version_id: 0,
    assignment_reason: '',
    effective_date: '',
    expiry_date: ''
  })
  formRef.value?.clearValidate()
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    
    loading.value = true
    
    // 准备提交数据
    const submitData = {
      ...form,
      effective_date: form.effective_date ? new Date(form.effective_date).toISOString().split('T')[0] : undefined,
      expiry_date: form.expiry_date ? new Date(form.expiry_date).toISOString().split('T')[0] : undefined
    }
    
    await textbookAssignmentApi.createAssignment(submitData)
    
    ElMessage.success('指定成功')
    emit('success')
    handleClose()
  } catch (error) {
    console.error('创建指定失败:', error)
    ElMessage.error('指定失败，请重试')
  } finally {
    loading.value = false
  }
}

const handleClose = () => {
  visible.value = false
  resetForm()
}
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}
</style>
