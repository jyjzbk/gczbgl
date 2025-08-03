<template>
  <el-dialog
    v-model="visible"
    title="撤销教材版本指定"
    width="500px"
    :before-close="handleClose"
  >
    <div v-if="assignment" class="revoke-content">
      <div class="assignment-info">
        <h4>指定信息</h4>
        <div class="info-item">
          <span class="label">学校：</span>
          <span class="value">{{ assignment.school?.name }}</span>
        </div>
        <div class="info-item">
          <span class="label">学科：</span>
          <span class="value">{{ assignment.subject?.name }}</span>
        </div>
        <div class="info-item">
          <span class="label">年级：</span>
          <span class="value">{{ assignment.grade_level }}年级</span>
        </div>
        <div class="info-item">
          <span class="label">教材版本：</span>
          <span class="value">
            {{ assignment.textbook_version?.name }}
            <span v-if="assignment.textbook_version?.publisher" class="publisher">
              ({{ assignment.textbook_version.publisher }})
            </span>
          </span>
        </div>
        <div class="info-item">
          <span class="label">指定人：</span>
          <span class="value">{{ assignment.assigner_user?.name }}</span>
        </div>
        <div class="info-item">
          <span class="label">生效日期：</span>
          <span class="value">{{ formatDate(assignment.effective_date) }}</span>
        </div>
      </div>

      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="80px"
        style="margin-top: 20px"
      >
        <el-form-item label="撤销理由" prop="reason">
          <el-input
            v-model="form.reason"
            type="textarea"
            :rows="4"
            placeholder="请输入撤销理由"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
      </el-form>

      <div class="warning-notice">
        <el-alert
          title="撤销提醒"
          type="warning"
          :closable="false"
          show-icon
        >
          <template #default>
            <p>撤销后该指定将立即失效，相关的实验目录筛选和统计计算将受到影响。</p>
            <p>请确认是否继续撤销操作？</p>
          </template>
        </el-alert>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="danger" :loading="loading" @click="handleSubmit">
          确认撤销
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { textbookAssignmentApi } from '@/api/textbookAssignment'
import type { TextbookVersionAssignment } from '@/api/textbookAssignment'
import { formatDate } from '@/utils/date'

interface Props {
  modelValue: boolean
  assignment: TextbookVersionAssignment | null
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
const form = reactive({
  reason: ''
})

// 表单验证规则
const rules: FormRules = {
  reason: [
    { required: true, message: '请输入撤销理由', trigger: 'blur' },
    { min: 5, max: 500, message: '撤销理由长度在 5 到 500 个字符', trigger: 'blur' }
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

// 方法
const resetForm = () => {
  form.reason = ''
  formRef.value?.clearValidate()
}

const handleSubmit = async () => {
  if (!formRef.value || !props.assignment) return

  try {
    await formRef.value.validate()
    
    loading.value = true
    
    await textbookAssignmentApi.revokeAssignment(props.assignment.id, {
      reason: form.reason
    })
    
    ElMessage.success('撤销成功')
    emit('success')
    handleClose()
  } catch (error) {
    console.error('撤销指定失败:', error)
    ElMessage.error('撤销失败，请重试')
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
.revoke-content {
  padding: 10px 0;
}

.assignment-info {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 16px;
  margin-bottom: 20px;
}

.assignment-info h4 {
  margin: 0 0 12px 0;
  color: #303133;
  font-size: 16px;
  font-weight: 600;
}

.info-item {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  line-height: 1.5;
}

.info-item:last-child {
  margin-bottom: 0;
}

.label {
  font-weight: 500;
  color: #606266;
  min-width: 80px;
  flex-shrink: 0;
}

.value {
  color: #303133;
  flex: 1;
}

.publisher {
  color: #909399;
  font-size: 12px;
}

.warning-notice {
  margin-top: 20px;
}

.warning-notice :deep(.el-alert__content) {
  line-height: 1.6;
}

.warning-notice p {
  margin: 0 0 8px 0;
}

.warning-notice p:last-child {
  margin-bottom: 0;
}

.dialog-footer {
  text-align: right;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}
</style>
