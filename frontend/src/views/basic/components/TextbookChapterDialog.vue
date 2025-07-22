<template>
  <el-dialog
    :model-value="modelValue"
    :title="isEdit ? '编辑章节' : '新增章节'"
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
      <el-form-item label="学科" prop="subject_id">
        <el-select
          v-model="form.subject_id"
          placeholder="请选择学科"
          style="width: 100%"
          :disabled="isEdit"
        >
          <el-option
            v-for="subject in subjects"
            :key="subject.id"
            :label="subject.name"
            :value="subject.id"
          />
        </el-select>
      </el-form-item>
      
      <el-form-item label="教材版本" prop="textbook_version_id">
        <el-select
          v-model="form.textbook_version_id"
          placeholder="请选择教材版本"
          style="width: 100%"
          :disabled="isEdit"
        >
          <el-option
            v-for="version in textbookVersions"
            :key="version.id"
            :label="version.name"
            :value="version.id"
          />
        </el-select>
      </el-form-item>
      
      <el-row :gutter="16">
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
        <el-col :span="12">
          <el-form-item label="册次" prop="volume">
            <el-select
              v-model="form.volume"
              placeholder="请选择册次"
              style="width: 100%"
            >
              <el-option label="上册" value="上册" />
              <el-option label="下册" value="下册" />
              <el-option label="全册" value="全册" />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="16">
        <el-col :span="12">
          <el-form-item label="层级" prop="level">
            <el-input-number
              v-model="form.level"
              :min="1"
              :max="5"
              style="width: 100%"
              :disabled="!!form.parent_id"
            />
            <div class="form-tip">
              1-章，2-节，3-小节，4-子小节，5-细分节
            </div>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="排序" prop="sort_order">
            <el-input-number
              v-model="form.sort_order"
              :min="0"
              :max="999"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="章节编码" prop="code">
        <el-input
          v-model="form.code"
          placeholder="如：01、01-01、01-01-01"
          maxlength="50"
          show-word-limit
        />
        <div class="form-tip">
          建议格式：章节用01、02，小节用01-01、01-02，子小节用01-01-01
        </div>
      </el-form-item>
      
      <el-form-item label="章节名称" prop="name">
        <el-input
          v-model="form.name"
          placeholder="请输入章节名称"
          maxlength="200"
          show-word-limit
        />
      </el-form-item>
      
      <el-form-item label="状态" prop="status">
        <el-radio-group v-model="form.status">
          <el-radio :label="true">启用</el-radio>
          <el-radio :label="false">禁用</el-radio>
        </el-radio-group>
      </el-form-item>
      
      <el-form-item v-if="form.parent_id" label="父级章节">
        <el-input :value="parentChapterName" disabled />
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
import { ref, reactive, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import type { 
  CreateTextbookChapterParams,
  TextbookVersion,
  Subject 
} from '@/api/experiment'
import { 
  createTextbookChapterApi, 
  updateTextbookChapterApi,
  getTextbookChapterApi
} from '@/api/experiment'

// Props
interface Props {
  modelValue: boolean
  formData: Partial<CreateTextbookChapterParams>
  isEdit: boolean
  subjects: Subject[]
  textbookVersions: TextbookVersion[]
  gradeOptions: Array<{ label: string; value: string }>
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
const parentChapterName = ref('')

// 表单数据
const form = reactive<CreateTextbookChapterParams>({
  subject_id: 0,
  textbook_version_id: 0,
  grade_level: '',
  volume: '',
  parent_id: undefined,
  level: 1,
  code: '',
  name: '',
  sort_order: 0,
  status: true
})

// 表单验证规则
const rules: FormRules = {
  subject_id: [
    { required: true, message: '请选择学科', trigger: 'change' }
  ],
  textbook_version_id: [
    { required: true, message: '请选择教材版本', trigger: 'change' }
  ],
  grade_level: [
    { required: true, message: '请选择年级', trigger: 'change' }
  ],
  volume: [
    { required: true, message: '请选择册次', trigger: 'change' }
  ],
  level: [
    { required: true, message: '请输入层级', trigger: 'blur' },
    { type: 'number', min: 1, max: 5, message: '层级必须在 1 到 5 之间', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入章节编码', trigger: 'blur' },
    { min: 1, max: 50, message: '章节编码长度在 1 到 50 个字符', trigger: 'blur' },
    { pattern: /^[0-9\-]+$/, message: '章节编码只能包含数字和连字符', trigger: 'blur' }
  ],
  name: [
    { required: true, message: '请输入章节名称', trigger: 'blur' },
    { min: 1, max: 200, message: '章节名称长度在 1 到 200 个字符', trigger: 'blur' }
  ],
  sort_order: [
    { required: true, message: '请输入排序值', trigger: 'blur' },
    { type: 'number', min: 0, max: 999, message: '排序值必须在 0 到 999 之间', trigger: 'blur' }
  ],
  status: [
    { required: true, message: '请选择状态', trigger: 'change' }
  ]
}

// 监听表单数据变化
watch(
  () => props.formData,
  async (newData) => {
    if (newData) {
      Object.assign(form, {
        subject_id: newData.subject_id || 0,
        textbook_version_id: newData.textbook_version_id || 0,
        grade_level: newData.grade_level || '',
        volume: newData.volume || '',
        parent_id: newData.parent_id,
        level: newData.level || 1,
        code: newData.code || '',
        name: newData.name || '',
        sort_order: newData.sort_order || 0,
        status: newData.status ?? true
      })
      
      // 如果有父级章节，获取父级章节名称
      if (newData.parent_id) {
        try {
          const response = await getTextbookChapterApi(newData.parent_id)
          parentChapterName.value = response.data.name
        } catch (error) {
          console.error('获取父级章节信息失败:', error)
        }
      } else {
        parentChapterName.value = ''
      }
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
      await updateTextbookChapterApi(id, form)
      ElMessage.success('更新成功')
    } else {
      await createTextbookChapterApi(form)
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
  parentChapterName.value = ''
}
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
  line-height: 1.4;
}

:deep(.el-form-item__label) {
  font-weight: 500;
}

:deep(.el-input__count) {
  color: #909399;
  font-size: 12px;
}
</style>
