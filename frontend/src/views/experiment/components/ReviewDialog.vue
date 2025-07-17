<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="500px"
    :before-close="handleClose"
  >
    <div v-if="booking" class="review-content">
      <!-- 预约信息摘要 -->
      <div class="booking-summary">
        <h4>预约信息</h4>
        <el-descriptions :column="1" border size="small">
          <el-descriptions-item label="实验名称">
            {{ booking.catalog?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="申请教师">
            {{ booking.teacher?.real_name }}
          </el-descriptions-item>
          <el-descriptions-item label="班级名称">
            {{ booking.class_name }}
          </el-descriptions-item>
          <el-descriptions-item label="预约时间">
            {{ booking.reservation_date }} {{ booking.start_time }} - {{ booking.end_time }}
          </el-descriptions-item>
          <el-descriptions-item label="实验室">
            {{ booking.laboratory?.name }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <!-- 审核表单 -->
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-width="80px"
        size="large"
      >
        <el-form-item label="审核结果" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="2" :disabled="action === 'reject'">
              <el-icon color="#67c23a"><Check /></el-icon>
              通过
            </el-radio>
            <el-radio :label="3" :disabled="action === 'approve'">
              <el-icon color="#f56c6c"><Close /></el-icon>
              拒绝
            </el-radio>
          </el-radio-group>
        </el-form-item>
        
        <el-form-item
          :label="form.status === 2 ? '通过意见' : '拒绝原因'"
          prop="review_remark"
        >
          <el-input
            v-model="form.review_remark"
            type="textarea"
            :rows="4"
            :placeholder="form.status === 2 ? '请输入通过意见（可选）' : '请输入拒绝原因'"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
        
        <!-- 时间冲突提醒 -->
        <div v-if="form.status === 1 && conflicts.length > 0" class="conflict-warning">
          <el-alert
            title="时间冲突提醒"
            type="warning"
            :closable="false"
            show-icon
          >
            <template #default>
              <p>检测到以下时间冲突：</p>
              <ul>
                <li v-for="conflict in conflicts" :key="conflict.id">
                  {{ conflict.class_name }} - {{ conflict.teacher?.real_name }}
                </li>
              </ul>
              <p>请确认是否继续通过此预约。</p>
            </template>
          </el-alert>
        </div>
        
        <!-- 容量检查提醒 -->
        <div v-if="form.status === 2 && capacityWarning" class="capacity-warning">
          <el-alert
            title="容量提醒"
            type="info"
            :closable="false"
            show-icon
          >
            <template #default>
              <p>
                实验室容量：{{ booking.laboratory?.capacity }}人，
                申请人数：{{ booking.student_count }}人
              </p>
              <p v-if="booking.student_count > (booking.laboratory?.capacity || 0)">
                申请人数超出实验室容量，请注意安排。
              </p>
            </template>
          </el-alert>
        </div>
      </el-form>
    </div>
    
    <template #footer>
      <el-button @click="handleClose">取消</el-button>
      <el-button
        :type="form.status === 2 ? 'success' : 'danger'"
        :loading="loading"
        @click="handleSubmit"
      >
        {{ loading ? '处理中...' : (form.status === 2 ? '确认通过' : '确认拒绝') }}
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Check, Close } from '@element-plus/icons-vue'
import { 
  reviewExperimentReservationApi,
  type ExperimentReservation 
} from '@/api/experiment'

interface Props {
  modelValue: boolean
  booking?: ExperimentReservation | null
  action: 'approve' | 'reject'
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 表单引用
const formRef = ref<FormInstance>()

// 加载状态
const loading = ref(false)

// 对话框显示状态
const visible = ref(false)

// 冲突数据
const conflicts = ref<ExperimentReservation[]>([])

// 表单数据
const form = reactive({
  status: 1,
  review_remark: ''
})

// 对话框标题
const dialogTitle = computed(() => {
  if (!props.booking) return '审核预约'
  const actionText = props.action === 'approve' ? '通过' : '拒绝'
  return `${actionText}预约 - ${props.booking.catalog?.name}`
})

// 容量警告
const capacityWarning = computed(() => {
  if (!props.booking) return false
  return props.booking.student_count > (props.booking.laboratory?.capacity || 0)
})

// 表单验证规则
const rules: FormRules = {
  status: [
    { required: true, message: '请选择审核结果', trigger: 'change' }
  ],
  review_remark: [
    {
      validator: (rule: any, value: string, callback: any) => {
        // 修正：拒绝状态是3，不是2
        if (form.status === 3 && !value.trim()) {
          callback(new Error('拒绝时必须填写拒绝原因'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      initForm()
      checkConflicts()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 初始化表单
const initForm = () => {
  // 状态值：2=已通过，3=已拒绝
  form.status = props.action === 'approve' ? 2 : 3
  form.review_remark = ''
  formRef.value?.clearValidate()
}

// 检查时间冲突
const checkConflicts = () => {
  // 这里应该调用API检查时间冲突
  // 暂时使用模拟数据
  conflicts.value = []
  
  // 模拟一些冲突数据
  if (props.booking && Math.random() > 0.7) {
    conflicts.value = [
      {
        id: 999,
        class_name: '八年级1班',
        teacher: { real_name: '张老师' }
      } as ExperimentReservation
    ]
  }
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value || !props.booking) return
  
  try {
    await formRef.value.validate()
    
    loading.value = true

    const reviewData = {
      status: form.status,
      review_remark: form.review_remark.trim() || undefined
    }

    console.log('发送审核数据:', reviewData)

    await reviewExperimentReservationApi(props.booking.id, reviewData)
    
    // 修正状态判断：2=通过，3=拒绝
    const actionText = form.status === 2 ? '通过' : '拒绝'
    ElMessage.success(`${actionText}成功`)
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('审核失败:', error)
  } finally {
    loading.value = false
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
  form.review_remark = ''
}
</script>

<style scoped>
.review-content {
  padding: 0;
}

.booking-summary {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e9ecef;
}

.booking-summary h4 {
  margin: 0 0 12px;
  font-size: 14px;
  color: #303133;
}

.conflict-warning,
.capacity-warning {
  margin-top: 16px;
}

:deep(.el-radio) {
  display: flex;
  align-items: center;
  margin-right: 20px;
  margin-bottom: 8px;
}

:deep(.el-radio__label) {
  display: flex;
  align-items: center;
  gap: 4px;
}

:deep(.el-alert__content) {
  line-height: 1.6;
}

:deep(.el-alert ul) {
  margin: 8px 0;
  padding-left: 20px;
}

:deep(.el-alert li) {
  margin-bottom: 4px;
}

:deep(.el-descriptions__body) {
  background: #fff;
}

:deep(.el-descriptions__label) {
  width: 80px;
}
</style>
