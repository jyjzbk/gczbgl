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
      label-width="100px"
      size="large"
    >
      <el-form-item label="模板名称" prop="name">
        <el-input
          v-model="form.name"
          placeholder="请输入模板名称"
          maxlength="100"
          show-word-limit
        />
      </el-form-item>

      <el-form-item label="模板描述" prop="description">
        <el-input
          v-model="form.description"
          type="textarea"
          :rows="3"
          placeholder="请输入模板描述（可选）"
          maxlength="500"
          show-word-limit
        />
      </el-form-item>

      <el-form-item label="适用年级" prop="applicable_grades">
        <el-checkbox-group v-model="form.applicable_grades">
          <el-checkbox
            v-for="grade in gradeOptions"
            :key="grade.value"
            :label="grade.value"
          >
            {{ grade.label }}
          </el-checkbox>
        </el-checkbox-group>
      </el-form-item>

      <el-form-item label="学科配置" prop="assignment_config">
        <div class="config-section">
          <div
            v-for="(versionId, subjectId, index) in form.assignment_config"
            :key="subjectId"
            class="config-item"
          >
            <el-row :gutter="10" align="middle">
              <el-col :span="8">
                <el-select
                  :model-value="Number(subjectId)"
                  placeholder="选择学科"
                  style="width: 100%"
                  @change="(value) => handleSubjectChange(subjectId, value)"
                >
                  <el-option
                    v-for="subject in subjects"
                    :key="subject.id"
                    :label="subject.name"
                    :value="subject.id"
                  />
                </el-select>
              </el-col>
              <el-col :span="12">
                <el-select
                  :model-value="versionId"
                  placeholder="选择教材版本"
                  filterable
                  style="width: 100%"
                  @change="(value) => handleVersionChange(subjectId, value)"
                >
                  <el-option
                    v-for="version in textbookVersions"
                    :key="version.id"
                    :label="`${version.name}${version.publisher ? ` (${version.publisher})` : ''}`"
                    :value="version.id"
                  />
                </el-select>
              </el-col>
              <el-col :span="4">
                <el-button
                  type="danger"
                  size="small"
                  :icon="Delete"
                  @click="removeConfig(subjectId)"
                />
              </el-col>
            </el-row>
          </div>
          <el-button
            type="primary"
            :icon="Plus"
            @click="addConfig"
            style="margin-top: 10px"
          >
            添加学科配置
          </el-button>
        </div>
      </el-form-item>

      <el-form-item label="设为默认">
        <el-switch
          v-model="form.is_default"
          active-text="是"
          inactive-text="否"
        />
        <div class="form-tip">
          设为默认模板后，将取消其他默认模板
        </div>
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="loading" @click="handleSubmit">
          {{ mode === 'create' ? '创建' : '更新' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Delete } from '@element-plus/icons-vue'
import { textbookAssignmentTemplateApi } from '@/api/textbookAssignment'
import type {
  TextbookAssignmentTemplate,
  CreateTemplateParams
} from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'

interface Props {
  modelValue: boolean
  mode: 'create' | 'edit'
  template: TextbookAssignmentTemplate | null
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
const form = reactive<CreateTemplateParams>({
  name: '',
  description: '',
  assignment_config: {},
  applicable_grades: [],
  applicable_school_types: [],
  is_default: false
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
  name: [
    { required: true, message: '请输入模板名称', trigger: 'blur' },
    { min: 2, max: 100, message: '模板名称长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  applicable_grades: [
    { required: true, message: '请选择适用年级', trigger: 'change' }
  ],
  assignment_config: [
    {
      validator: (rule, value, callback) => {
        if (Object.keys(value).length === 0) {
          callback(new Error('请至少添加一个学科配置'))
        } else {
          callback()
        }
      },
      trigger: 'change'
    }
  ]
}

// 计算属性
const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新建模板' : '编辑模板'
})

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      initForm()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 方法
const initForm = () => {
  if (props.mode === 'edit' && props.template) {
    // 编辑模式，填充数据
    Object.assign(form, {
      name: props.template.name,
      description: props.template.description || '',
      assignment_config: { ...props.template.assignment_config },
      applicable_grades: [...props.template.applicable_grades],
      applicable_school_types: props.template.applicable_school_types ? [...props.template.applicable_school_types] : [],
      is_default: Boolean(props.template.is_default)
    })
  } else {
    // 新建模式，重置表单
    resetForm()
  }
}

const resetForm = () => {
  Object.assign(form, {
    name: '',
    description: '',
    assignment_config: {},
    applicable_grades: [],
    applicable_school_types: [],
    is_default: false
  })
  formRef.value?.clearValidate()
}

const addConfig = () => {
  // 添加一个新的配置项
  const newKey = `new_${Date.now()}`
  form.assignment_config[newKey] = 0
}

const removeConfig = (subjectId: string) => {
  delete form.assignment_config[subjectId]
}

const handleSubjectChange = (oldSubjectId: string, newSubjectId: number) => {
  const versionId = form.assignment_config[oldSubjectId]
  delete form.assignment_config[oldSubjectId]
  form.assignment_config[newSubjectId] = versionId
}

const handleVersionChange = (subjectId: string, versionId: number) => {
  form.assignment_config[subjectId] = versionId
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    
    loading.value = true
    
    // 清理配置中的无效项
    const cleanConfig: Record<string, number> = {}
    Object.entries(form.assignment_config).forEach(([subjectId, versionId]) => {
      if (subjectId && versionId && !subjectId.startsWith('new_')) {
        cleanConfig[subjectId] = versionId
      }
    })
    
    const submitData = {
      ...form,
      assignment_config: cleanConfig
    }
    
    if (props.mode === 'create') {
      await textbookAssignmentTemplateApi.createTemplate(submitData)
      ElMessage.success('创建成功')
    } else {
      await textbookAssignmentTemplateApi.updateTemplate(props.template!.id, submitData)
      ElMessage.success('更新成功')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('保存模板失败:', error)
    ElMessage.error('保存失败，请重试')
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
.config-section {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  padding: 15px;
  background-color: #fafafa;
}

.config-item {
  margin-bottom: 10px;
}

.config-item:last-child {
  margin-bottom: 0;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.dialog-footer {
  text-align: right;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}
</style>
