<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
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
      <el-form-item label="预约记录" prop="reservation_id">
        <el-select
          v-model="form.reservation_id"
          placeholder="请选择预约记录（可选）"
          filterable
          clearable
          style="width: 100%"
          @change="handleReservationChange"
        >
          <el-option
            v-for="reservation in reservations"
            :key="reservation.id"
            :label="getReservationLabel(reservation)"
            :value="reservation.id"
          />
        </el-select>
      </el-form-item>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="form.student_count"
              :min="1"
              :max="100"
              controls-position="right"
              style="width: 100%"
            />
            <span style="margin-left: 8px; color: #909399;">人</span>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="开始时间" prop="start_time">
            <el-date-picker
              v-model="form.start_time"
              type="datetime"
              placeholder="请选择开始时间"
              format="YYYY-MM-DD HH:mm"
              value-format="YYYY-MM-DD HH:mm:ss"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="备注说明" prop="remark">
        <el-input
          v-model="form.remark"
          type="textarea"
          :rows="3"
          placeholder="请输入备注说明"
          maxlength="500"
          show-word-limit
        />
      </el-form-item>
      
      <!-- 预约信息预览 -->
      <div v-if="selectedReservation" class="reservation-preview">
        <h4>预约信息</h4>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="实验名称">
            {{ selectedReservation.catalog?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验室">
            {{ selectedReservation.laboratory?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="申请教师">
            {{ selectedReservation.teacher?.real_name }}
          </el-descriptions-item>
          <el-descriptions-item label="班级">
            {{ selectedReservation.class_name }}
          </el-descriptions-item>
          <el-descriptions-item label="预约时间">
            {{ selectedReservation.reservation_date }} {{ selectedReservation.start_time }}
          </el-descriptions-item>
          <el-descriptions-item label="预约人数">
            {{ selectedReservation.student_count }}人
          </el-descriptions-item>
        </el-descriptions>
      </div>
    </el-form>
    
    <template #footer>
      <el-button @click="handleClose">取消</el-button>
      <el-button
        type="primary"
        :loading="loading"
        @click="handleSubmit"
      >
        {{ loading ? '保存中...' : '开始实验' }}
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  createExperimentRecordApi,
  getExperimentReservationsApi,
  type ExperimentRecord,
  type ExperimentReservation
} from '@/api/experiment'

interface Props {
  modelValue: boolean
  record?: ExperimentRecord | null
  mode: 'create' | 'edit'
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

// 预约列表
const reservations = ref<ExperimentReservation[]>([])

// 对话框显示状态
const visible = ref(false)

// 表单数据
const form = reactive({
  reservation_id: undefined as number | undefined,
  student_count: 1,
  start_time: '',
  remark: ''
})

// 对话框标题
const dialogTitle = computed(() => {
  return props.mode === 'create' ? '开始实验记录' : '编辑实验记录'
})

// 选中的预约
const selectedReservation = computed(() => {
  return reservations.value.find(r => r.id === form.reservation_id)
})

// 表单验证规则
const rules: FormRules = {
  student_count: [
    { required: true, message: '请输入学生人数', trigger: 'blur' }
  ],
  start_time: [
    { required: true, message: '请选择开始时间', trigger: 'change' }
  ]
}

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      initForm()
      loadReservations()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 获取预约标签
const getReservationLabel = (reservation: ExperimentReservation) => {
  return `${reservation.catalog?.name} - ${reservation.class_name} - ${reservation.reservation_date}`
}

// 处理预约变化
const handleReservationChange = () => {
  if (selectedReservation.value) {
    form.student_count = selectedReservation.value.student_count
  }
}

// 初始化表单
const initForm = () => {
  if (props.mode === 'edit' && props.record) {
    // 编辑模式，填充数据
    Object.assign(form, {
      reservation_id: props.record.reservation_id,
      student_count: props.record.student_count,
      start_time: props.record.start_time,
      remark: ''
    })
  } else {
    // 新增模式，重置表单
    resetForm()
    // 设置默认开始时间为当前时间
    form.start_time = new Date().toISOString().slice(0, 19).replace('T', ' ')
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    reservation_id: undefined,
    student_count: 1,
    start_time: '',
    remark: ''
  })
  formRef.value?.clearValidate()
}

// 加载预约列表
const loadReservations = async () => {
  try {
    // 只加载已通过的预约
    const response = await getExperimentReservationsApi({ 
      status: 1, 
      per_page: 1000 
    })
    reservations.value = response.data.data
  } catch (error) {
    console.error('加载预约列表失败:', error)
  }
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    
    loading.value = true
    
    const data = {
      reservation_id: form.reservation_id,
      student_count: form.student_count,
      start_time: form.start_time,
      remark: form.remark || undefined
    }
    
    await createExperimentRecordApi(data)
    ElMessage.success('实验记录创建成功')
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('创建实验记录失败:', error)
  } finally {
    loading.value = false
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
  resetForm()
}

onMounted(() => {
  // 组件挂载时不需要立即加载数据，等对话框打开时再加载
})
</script>

<style scoped>
.reservation-preview {
  margin-top: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e9ecef;
}

.reservation-preview h4 {
  margin: 0 0 12px;
  font-size: 14px;
  color: #303133;
}

:deep(.el-form-item) {
  margin-bottom: 20px;
}

:deep(.el-textarea__inner) {
  resize: vertical;
}
</style>
