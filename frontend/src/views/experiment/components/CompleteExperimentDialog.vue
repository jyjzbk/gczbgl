<template>
  <el-dialog
    v-model="visible"
    title="完成实验记录"
    width="700px"
    :before-close="handleClose"
  >
    <div v-if="record" class="complete-content">
      <!-- 实验信息摘要 -->
      <div class="experiment-summary">
        <h4>实验信息</h4>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="实验名称">
            {{ record.catalog?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="班级">
            {{ record.class_name }}
          </el-descriptions-item>
          <el-descriptions-item label="开始时间">
            {{ formatDateTime(record.start_time) }}
          </el-descriptions-item>
          <el-descriptions-item label="学生人数">
            {{ record.student_count }}人
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 完成表单 -->
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="100px"
        size="large"
      >
        <el-form-item label="结束时间" prop="end_time">
          <el-date-picker
            v-model="form.end_time"
            type="datetime"
            placeholder="请选择结束时间"
            format="YYYY-MM-DD HH:mm"
            value-format="YYYY-MM-DD HH:mm:ss"
            style="width: 100%"
          />
        </el-form-item>
        
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="完成率" prop="completion_rate">
              <el-slider
                v-model="form.completion_rate"
                :min="0"
                :max="100"
                :step="5"
                show-input
                :show-input-controls="false"
              />
            </el-form-item>
          </el-col>
          
          <el-col :span="12">
            <el-form-item label="质量评分" prop="quality_score">
              <el-rate
                v-model="rateValue"
                :max="5"
                show-text
                :texts="['很差', '较差', '一般', '良好', '优秀']"
                @change="handleRateChange"
              />
              <div class="score-display">{{ form.quality_score }}星</div>
            </el-form-item>
          </el-col>
        </el-row>
        
        <el-form-item label="实验总结" prop="summary">
          <el-input
            v-model="form.summary"
            type="textarea"
            :rows="4"
            placeholder="请输入实验总结，包括实验过程、结果等"
            maxlength="1000"
            show-word-limit
          />
        </el-form-item>
        
        <el-form-item label="遇到问题" prop="problems">
          <el-input
            v-model="form.problems"
            type="textarea"
            :rows="3"
            placeholder="请描述实验过程中遇到的问题（可选）"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
        
        <el-form-item label="改进建议" prop="suggestions">
          <el-input
            v-model="form.suggestions"
            type="textarea"
            :rows="3"
            placeholder="请提出改进建议（可选）"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
        
        <!-- 文件上传 -->
        <el-form-item label="实验照片">
          <el-upload
            ref="photoUploadRef"
            :auto-upload="false"
            :show-file-list="true"
            :on-change="handlePhotoChange"
            :before-remove="handlePhotoRemove"
            accept="image/*"
            multiple
            list-type="picture-card"
          >
            <el-icon><Plus /></el-icon>
          </el-upload>
          <div class="upload-tip">
            支持 JPG、PNG 格式，单个文件不超过 5MB
          </div>
        </el-form-item>
        
        <el-form-item label="实验视频">
          <el-upload
            ref="videoUploadRef"
            :auto-upload="false"
            :show-file-list="true"
            :on-change="handleVideoChange"
            :before-remove="handleVideoRemove"
            accept="video/*"
            multiple
          >
            <el-button type="primary">
              <el-icon><VideoPlay /></el-icon>
              选择视频
            </el-button>
          </el-upload>
          <div class="upload-tip">
            支持 MP4、AVI 格式，单个文件不超过 50MB
          </div>
        </el-form-item>
      </el-form>
    </div>
    
    <template #footer>
      <el-button @click="handleClose">取消</el-button>
      <el-button
        type="primary"
        :loading="loading"
        @click="handleSubmit"
      >
        {{ loading ? '完成中...' : '完成实验' }}
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules, type UploadFile } from 'element-plus'
import { Plus, VideoPlay } from '@element-plus/icons-vue'
import {
  completeExperimentRecordApi,
  uploadExperimentPhotoApi,
  uploadExperimentVideoApi,
  type ExperimentRecord
} from '@/api/experiment'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  record?: ExperimentRecord | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 表单引用
const formRef = ref<FormInstance>()
const photoUploadRef = ref()
const videoUploadRef = ref()

// 加载状态
const loading = ref(false)

// 对话框显示状态
const visible = ref(false)

// 评分值（1-5星）
const rateValue = ref(4)

// 文件列表
const photoFiles = ref<File[]>([])
const videoFiles = ref<File[]>([])

// 表单数据
const form = reactive({
  end_time: '',
  completion_rate: 100,
  quality_score: 80,
  summary: '',
  problems: '',
  suggestions: ''
})

// 表单验证规则
const rules: FormRules = {
  end_time: [
    { required: true, message: '请选择结束时间', trigger: 'change' }
  ],
  completion_rate: [
    { required: true, message: '请设置完成率', trigger: 'blur' }
  ],
  quality_score: [
    { required: true, message: '请进行质量评分', trigger: 'blur' }
  ],
  summary: [
    { required: true, message: '请输入实验总结', trigger: 'blur' },
    { min: 10, message: '实验总结至少10个字符', trigger: 'blur' }
  ]
}

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

// 格式化日期时间
const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm:ss')
}

// 处理评分变化
const handleRateChange = (value: number) => {
  form.quality_score = value // 直接使用星级评分(1-5)
}

// 初始化表单
const initForm = () => {
  if (props.record) {
    // 设置默认结束时间为当前时间
    form.end_time = new Date().toISOString().slice(0, 19).replace('T', ' ')
    form.completion_rate = props.record.completion_rate || 100
    form.quality_score = props.record.quality_score || 4 // 默认4星评分
    form.summary = props.record.summary || ''
    form.problems = props.record.problems || ''
    form.suggestions = props.record.suggestions || ''

    // 设置评分值（直接使用星级评分）
    rateValue.value = form.quality_score
  }
  
  // 清空文件列表
  photoFiles.value = []
  videoFiles.value = []
  
  formRef.value?.clearValidate()
}

// 处理照片变化
const handlePhotoChange = (file: UploadFile) => {
  const rawFile = file.raw
  if (!rawFile) return
  
  // 检查文件类型
  if (!rawFile.type.startsWith('image/')) {
    ElMessage.error('请选择图片文件')
    return
  }
  
  // 检查文件大小
  if (rawFile.size > 5 * 1024 * 1024) {
    ElMessage.error('图片大小不能超过 5MB')
    return
  }
  
  photoFiles.value.push(rawFile)
}

// 处理照片移除
const handlePhotoRemove = (file: UploadFile) => {
  const index = photoFiles.value.findIndex(f => f.name === file.name)
  if (index > -1) {
    photoFiles.value.splice(index, 1)
  }
  return true
}

// 处理视频变化
const handleVideoChange = (file: UploadFile) => {
  const rawFile = file.raw
  if (!rawFile) return
  
  // 检查文件类型
  if (!rawFile.type.startsWith('video/')) {
    ElMessage.error('请选择视频文件')
    return
  }
  
  // 检查文件大小
  if (rawFile.size > 50 * 1024 * 1024) {
    ElMessage.error('视频大小不能超过 50MB')
    return
  }
  
  videoFiles.value.push(rawFile)
}

// 处理视频移除
const handleVideoRemove = (file: UploadFile) => {
  const index = videoFiles.value.findIndex(f => f.name === file.name)
  if (index > -1) {
    videoFiles.value.splice(index, 1)
  }
  return true
}

// 上传文件
const uploadFiles = async (recordId: number) => {
  const uploadPromises = []

  // 批量上传照片
  if (photoFiles.value.length > 0) {
    uploadPromises.push(uploadExperimentPhotoApi(recordId, photoFiles.value))
  }

  // 批量上传视频
  if (videoFiles.value.length > 0) {
    uploadPromises.push(uploadExperimentVideoApi(recordId, videoFiles.value))
  }

  if (uploadPromises.length > 0) {
    await Promise.all(uploadPromises)
  }
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value || !props.record) return
  
  try {
    await formRef.value.validate()
    
    // 验证结束时间
    const startTime = dayjs(props.record.start_time)
    const endTime = dayjs(form.end_time)
    
    if (endTime.isBefore(startTime)) {
      ElMessage.error('结束时间不能早于开始时间')
      return
    }
    
    loading.value = true
    
    const data = {
      end_time: form.end_time,
      completion_rate: form.completion_rate,
      quality_score: form.quality_score,
      summary: form.summary,
      problems: form.problems || undefined,
      suggestions: form.suggestions || undefined
    }
    
    // 完成实验记录
    await completeExperimentRecordApi(props.record.id, data)

    // 上传文件
    if (photoFiles.value.length > 0 || videoFiles.value.length > 0) {
      await uploadFiles(props.record.id)
    }

    ElMessage.success('实验记录完成成功')

    emit('success')
    handleClose()
  } catch (error: any) {
    console.error('完成实验记录失败:', error)

    // 处理特定错误
    if (error.response?.status === 400) {
      const message = error.response.data?.message || '操作失败'
      ElMessage.error(message)
    } else if (error.response?.status === 422) {
      const errors = error.response.data?.errors
      if (errors) {
        const firstError = Object.values(errors)[0] as string[]
        ElMessage.error(firstError[0] || '数据验证失败')
      } else {
        ElMessage.error('数据验证失败')
      }
    } else {
      ElMessage.error('完成实验记录失败，请稍后重试')
    }
  } finally {
    loading.value = false
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
  photoFiles.value = []
  videoFiles.value = []
}
</script>

<style scoped>
.complete-content {
  padding: 0;
}

.experiment-summary {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e9ecef;
}

.experiment-summary h4 {
  margin: 0 0 12px;
  font-size: 14px;
  color: #303133;
}

.score-display {
  margin-top: 8px;
  font-size: 14px;
  color: #606266;
  text-align: center;
}

.upload-tip {
  margin-top: 8px;
  font-size: 12px;
  color: #909399;
  line-height: 1.4;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}

:deep(.el-textarea__inner) {
  resize: vertical;
}

:deep(.el-slider__input) {
  width: 80px;
}

:deep(.el-rate) {
  display: flex;
  align-items: center;
}

:deep(.el-rate__text) {
  margin-left: 8px;
}

:deep(.el-upload--picture-card) {
  width: 80px;
  height: 80px;
}

:deep(.el-upload-list--picture-card .el-upload-list__item) {
  width: 80px;
  height: 80px;
}

:deep(.el-descriptions__body) {
  background: #fff;
}

:deep(.el-descriptions__label) {
  width: 80px;
}
</style>
