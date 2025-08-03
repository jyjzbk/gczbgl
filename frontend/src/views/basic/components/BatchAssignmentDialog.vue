<template>
  <el-dialog
    v-model="visible"
    title="批量指定教材版本"
    width="800px"
    :before-close="handleClose"
  >
    <el-tabs v-model="activeTab" type="card">
      <!-- 手动批量指定 -->
      <el-tab-pane label="手动指定" name="manual">
        <div class="batch-form">
          <el-form
            ref="manualFormRef"
            :model="manualForm"
            :rules="manualRules"
            label-width="100px"
          >
            <el-form-item label="目标学校" prop="school_ids">
              <el-select
                v-model="manualForm.school_ids"
                placeholder="请选择学校"
                multiple
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

            <el-form-item label="指定配置">
              <div class="assignment-config">
                <div
                  v-for="(config, index) in manualForm.assignments"
                  :key="index"
                  class="config-item"
                >
                  <el-row :gutter="10" align="middle">
                    <el-col :span="6">
                      <el-select
                        v-model="config.subject_id"
                        placeholder="学科"
                        style="width: 100%"
                      >
                        <el-option
                          v-for="subject in subjects"
                          :key="subject.id"
                          :label="subject.name"
                          :value="subject.id"
                        />
                      </el-select>
                    </el-col>
                    <el-col :span="6">
                      <el-select
                        v-model="config.grade_level"
                        placeholder="年级"
                        style="width: 100%"
                      >
                        <el-option
                          v-for="grade in gradeOptions"
                          :key="grade.value"
                          :label="grade.label"
                          :value="grade.value"
                        />
                      </el-select>
                    </el-col>
                    <el-col :span="10">
                      <el-select
                        v-model="config.textbook_version_id"
                        placeholder="教材版本"
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
                    </el-col>
                    <el-col :span="2">
                      <el-button
                        type="danger"
                        size="small"
                        :icon="Delete"
                        @click="removeConfig(index)"
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
                  添加配置
                </el-button>
              </div>
            </el-form-item>

            <el-form-item label="指定理由">
              <el-input
                v-model="manualForm.assignment_reason"
                type="textarea"
                :rows="2"
                placeholder="请输入指定理由（可选）"
                maxlength="500"
              />
            </el-form-item>
          </el-form>
        </div>
      </el-tab-pane>

      <!-- 模板批量指定 -->
      <el-tab-pane label="模板指定" name="template">
        <div class="template-form">
          <el-form
            ref="templateFormRef"
            :model="templateForm"
            :rules="templateRules"
            label-width="100px"
          >
            <el-form-item label="指定模板" prop="template_id">
              <el-select
                v-model="templateForm.template_id"
                placeholder="请选择模板"
                style="width: 100%"
                @change="handleTemplateChange"
              >
                <el-option
                  v-for="template in templates"
                  :key="template.id"
                  :label="template.name"
                  :value="template.id"
                >
                  <div class="template-option">
                    <div class="template-name">{{ template.name }}</div>
                    <div class="template-desc">{{ template.description }}</div>
                  </div>
                </el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="目标学校" prop="school_ids">
              <el-select
                v-model="templateForm.school_ids"
                placeholder="请选择学校"
                multiple
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

            <!-- 模板预览 -->
            <el-form-item v-if="selectedTemplate" label="模板预览">
              <div class="template-preview">
                <div class="preview-header">
                  <span class="template-title">{{ selectedTemplate.name }}</span>
                  <el-tag type="info" size="small">
                    适用年级：{{ selectedTemplate.applicable_grades.join('、') }}
                  </el-tag>
                </div>
                <div class="preview-content">
                  <div
                    v-for="(versionId, subjectId) in selectedTemplate.assignment_config"
                    :key="subjectId"
                    class="config-preview-item"
                  >
                    <span class="subject-name">
                      {{ getSubjectName(Number(subjectId)) }}
                    </span>
                    <span class="arrow">→</span>
                    <span class="version-name">
                      {{ getVersionName(versionId) }}
                    </span>
                  </div>
                </div>
              </div>
            </el-form-item>
          </el-form>
        </div>
      </el-tab-pane>
    </el-tabs>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="loading" @click="handleSubmit">
          确定指定
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Delete } from '@element-plus/icons-vue'
import { textbookAssignmentApi } from '@/api/textbookAssignment'
import type {
  School,
  TextbookAssignmentTemplate,
  CreateAssignmentParams,
  BatchAssignmentParams,
  TemplateAssignmentParams
} from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'

interface Props {
  modelValue: boolean
  schools: School[]
  subjects: Subject[]
  textbookVersions: TextbookVersion[]
  templates: TextbookAssignmentTemplate[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const manualFormRef = ref<FormInstance>()
const templateFormRef = ref<FormInstance>()
const loading = ref(false)
const visible = ref(false)
const activeTab = ref('manual')

// 手动指定表单
const manualForm = reactive({
  school_ids: [] as number[],
  assignments: [] as Array<{
    subject_id: number
    grade_level: string
    textbook_version_id: number
  }>,
  assignment_reason: ''
})

// 模板指定表单
const templateForm = reactive<TemplateAssignmentParams>({
  template_id: 0,
  school_ids: []
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
const manualRules: FormRules = {
  school_ids: [
    { required: true, message: '请选择学校', trigger: 'change' }
  ]
}

const templateRules: FormRules = {
  template_id: [
    { required: true, message: '请选择模板', trigger: 'change' }
  ],
  school_ids: [
    { required: true, message: '请选择学校', trigger: 'change' }
  ]
}

// 计算属性
const selectedTemplate = computed(() => {
  return props.templates.find(t => t.id === templateForm.template_id)
})

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      resetForms()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 方法
const resetForms = () => {
  // 重置手动指定表单
  manualForm.school_ids = []
  manualForm.assignments = []
  manualForm.assignment_reason = ''
  
  // 重置模板指定表单
  templateForm.template_id = 0
  templateForm.school_ids = []
  
  // 清除验证
  manualFormRef.value?.clearValidate()
  templateFormRef.value?.clearValidate()
  
  // 重置选项卡
  activeTab.value = 'manual'
}

const addConfig = () => {
  manualForm.assignments.push({
    subject_id: 0,
    grade_level: '',
    textbook_version_id: 0
  })
}

const removeConfig = (index: number) => {
  manualForm.assignments.splice(index, 1)
}

const handleTemplateChange = () => {
  // 模板变更时可以做一些处理
}

const getSubjectName = (subjectId: number) => {
  const subject = props.subjects.find(s => s.id === subjectId)
  return subject?.name || '未知学科'
}

const getVersionName = (versionId: number) => {
  const version = props.textbookVersions.find(v => v.id === versionId)
  return version?.name || '未知版本'
}

const handleSubmit = async () => {
  try {
    loading.value = true
    
    if (activeTab.value === 'manual') {
      await handleManualSubmit()
    } else {
      await handleTemplateSubmit()
    }
    
    ElMessage.success('批量指定成功')
    emit('success')
    handleClose()
  } catch (error) {
    console.error('批量指定失败:', error)
    ElMessage.error('批量指定失败，请重试')
  } finally {
    loading.value = false
  }
}

const handleManualSubmit = async () => {
  if (!manualFormRef.value) return
  
  await manualFormRef.value.validate()
  
  // 构建批量指定数据
  const assignments: CreateAssignmentParams[] = []
  
  for (const schoolId of manualForm.school_ids) {
    for (const config of manualForm.assignments) {
      if (config.subject_id && config.grade_level && config.textbook_version_id) {
        assignments.push({
          school_id: schoolId,
          subject_id: config.subject_id,
          grade_level: config.grade_level,
          textbook_version_id: config.textbook_version_id,
          assignment_reason: manualForm.assignment_reason || undefined
        })
      }
    }
  }
  
  if (assignments.length === 0) {
    throw new Error('请至少配置一个有效的指定')
  }
  
  await textbookAssignmentApi.batchCreateAssignments({ assignments })
}

const handleTemplateSubmit = async () => {
  if (!templateFormRef.value) return
  
  await templateFormRef.value.validate()
  
  await textbookAssignmentApi.assignByTemplate(templateForm)
}

const handleClose = () => {
  visible.value = false
  resetForms()
}
</script>

<style scoped>
.batch-form,
.template-form {
  padding: 20px 0;
}

.assignment-config {
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

.template-option {
  display: flex;
  flex-direction: column;
}

.template-name {
  font-weight: 500;
  color: #303133;
}

.template-desc {
  font-size: 12px;
  color: #909399;
  margin-top: 2px;
}

.template-preview {
  border: 1px solid #e4e7ed;
  border-radius: 4px;
  padding: 15px;
  background-color: #f8f9fa;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e4e7ed;
}

.template-title {
  font-weight: 500;
  color: #303133;
}

.preview-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.config-preview-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  background-color: white;
  border-radius: 4px;
  border: 1px solid #e4e7ed;
}

.subject-name {
  font-weight: 500;
  color: #409eff;
}

.arrow {
  color: #909399;
}

.version-name {
  color: #67c23a;
}

.dialog-footer {
  text-align: right;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}
</style>
