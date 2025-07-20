<template>
  <div class="quick-reservation-form">
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
    >
      <el-form-item label="实验名称" prop="catalog_id">
        <el-select
          v-model="form.catalog_id"
          placeholder="请选择实验"
          filterable
          style="width: 100%"
          @change="handleExperimentChange"
        >
          <el-option
            v-for="catalog in experimentCatalogs"
            :key="catalog.id"
            :label="catalog.name"
            :value="catalog.id"
          />
        </el-select>
      </el-form-item>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="预约日期" prop="reservation_date">
            <el-date-picker
              v-model="form.reservation_date"
              type="date"
              placeholder="选择日期"
              style="width: 100%"
              :disabled-date="disabledDate"
            />
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="班级名称" prop="class_name">
            <el-input
              v-model="form.class_name"
              placeholder="请输入班级名称"
            />
          </el-form-item>
        </el-col>
      </el-row>

      <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="开始时间" prop="start_time">
            <el-time-picker
              v-model="form.start_time"
              placeholder="开始时间"
              style="width: 100%"
              format="HH:mm"
              value-format="HH:mm"
            />
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="结束时间" prop="end_time">
            <el-time-picker
              v-model="form.end_time"
              placeholder="结束时间"
              style="width: 100%"
              format="HH:mm"
              value-format="HH:mm"
            />
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="form.student_count"
              :min="1"
              :max="100"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item label="优先级">
        <el-radio-group v-model="form.priority">
          <el-radio label="normal">普通</el-radio>
          <el-radio label="high">高</el-radio>
          <el-radio label="urgent">紧急</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="备注">
        <el-input
          v-model="form.preparation_notes"
          type="textarea"
          :rows="2"
          placeholder="请输入备注信息"
        />
      </el-form-item>

      <!-- 冲突提醒 -->
      <div v-if="conflicts.length > 0" class="conflict-warnings">
        <el-alert
          v-for="(conflict, index) in conflicts"
          :key="index"
          :title="conflict.message"
          type="warning"
          :closable="false"
          show-icon
          class="conflict-item"
        />
      </div>
    </el-form>

    <div class="form-actions">
      <el-button @click="$emit('cancel')">取消</el-button>
      <el-button type="primary" @click="checkConflicts" :loading="checking">
        检测冲突
      </el-button>
      <el-button
        type="success"
        @click="submitForm"
        :loading="submitting"
        :disabled="hasBlockingConflicts"
      >
        提交预约
      </el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { ElMessage } from 'element-plus'
import type { FormInstance, FormRules } from 'element-plus'
import { smartReservationApi } from '@/api/smartReservation'
import { experimentCatalogApi } from '@/api/experiment'

// Props
const props = defineProps<{
  laboratoryId?: number
  initialDate?: string
  initialTime?: string
}>()

// Emits
const emit = defineEmits<{
  success: []
  cancel: []
}>()

// 响应式数据
const formRef = ref<FormInstance>()
const experimentCatalogs = ref([])
const conflicts = ref([])
const checking = ref(false)
const submitting = ref(false)

// 表单数据
const form = reactive({
  catalog_id: null,
  laboratory_id: props.laboratoryId,
  reservation_date: props.initialDate || '',
  start_time: props.initialTime || '',
  end_time: '',
  class_name: '',
  student_count: 30,
  priority: 'normal',
  preparation_notes: ''
})

// 表单验证规则
const rules: FormRules = {
  catalog_id: [{ required: true, message: '请选择实验', trigger: 'change' }],
  reservation_date: [{ required: true, message: '请选择预约日期', trigger: 'change' }],
  start_time: [{ required: true, message: '请选择开始时间', trigger: 'change' }],
  end_time: [{ required: true, message: '请选择结束时间', trigger: 'change' }],
  class_name: [{ required: true, message: '请输入班级名称', trigger: 'blur' }],
  student_count: [{ required: true, message: '请输入学生人数', trigger: 'blur' }]
}

// 计算属性
const hasBlockingConflicts = computed(() => {
  return conflicts.value.some(conflict => 
    ['laboratory_time', 'teacher_time', 'capacity'].includes(conflict.type)
  )
})

// 方法
const loadExperimentCatalogs = async () => {
  try {
    const response = await experimentCatalogApi.getList({ per_page: 100 })
    experimentCatalogs.value = response.data
  } catch (error) {
    ElMessage.error('加载实验目录失败')
  }
}

const handleExperimentChange = (catalogId: number) => {
  if (!catalogId) return

  // 根据实验设置建议的结束时间
  const experiment = experimentCatalogs.value.find(item => item.id === catalogId)
  if (experiment && form.start_time) {
    const startTime = new Date(`2000-01-01 ${form.start_time}`)
    const endTime = new Date(startTime.getTime() + experiment.duration * 60000)
    form.end_time = endTime.toTimeString().slice(0, 5)
  }

  // 设置建议的学生人数
  if (experiment && experiment.student_count) {
    form.student_count = experiment.student_count
  }
}

const checkConflicts = async () => {
  if (!form.laboratory_id || !form.reservation_date || 
      !form.start_time || !form.end_time) {
    ElMessage.warning('请先填写完整的预约信息')
    return
  }

  checking.value = true
  try {
    const response = await smartReservationApi.checkConflicts({
      laboratory_id: form.laboratory_id,
      reservation_date: form.reservation_date,
      start_time: form.start_time,
      end_time: form.end_time,
      student_count: form.student_count
    })

    conflicts.value = response.data.conflicts
    
    if (conflicts.value.length === 0) {
      ElMessage.success('未检测到冲突，可以提交预约')
    } else {
      ElMessage.warning(`检测到 ${conflicts.value.length} 个冲突`)
    }
  } catch (error) {
    ElMessage.error('冲突检测失败')
  } finally {
    checking.value = false
  }
}

const submitForm = async () => {
  if (!formRef.value) return

  const valid = await formRef.value.validate()
  if (!valid) return

  if (hasBlockingConflicts.value) {
    ElMessage.warning('存在阻塞性冲突，无法提交预约')
    return
  }

  submitting.value = true
  try {
    await smartReservationApi.create({
      ...form,
      auto_borrow_equipment: true
    })
    
    ElMessage.success('预约提交成功')
    emit('success')
  } catch (error) {
    ElMessage.error('预约提交失败')
  } finally {
    submitting.value = false
  }
}

const disabledDate = (time: Date) => {
  return time.getTime() < Date.now() - 8.64e7 // 不能选择今天之前的日期
}

// 监听器
watch(() => form.start_time, (newTime) => {
  if (newTime && form.catalog_id) {
    handleExperimentChange(form.catalog_id)
  }
})

watch(() => [form.reservation_date, form.start_time, form.end_time], () => {
  conflicts.value = [] // 清空之前的冲突检测结果
})

// 生命周期
onMounted(() => {
  loadExperimentCatalogs()
})
</script>

<style scoped>
.quick-reservation-form {
  padding: 20px 0;
}

.conflict-warnings {
  margin: 16px 0;
}

.conflict-item {
  margin-bottom: 8px;
}

.form-actions {
  text-align: right;
  padding-top: 16px;
  border-top: 1px solid #ebeef5;
}

.form-actions .el-button {
  margin-left: 12px;
}
</style>
