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
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="学校" prop="school_id">
            <el-select
              v-model="form.school_id"
              placeholder="请选择学校"
              filterable
              style="width: 100%"
              @change="handleSchoolChange"
              :disabled="!canSelectSchool"
            >
              <el-option
                v-for="school in schools"
                :key="school.id"
                :label="school.name"
                :value="school.id"
              />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="实验室" prop="laboratory_id">
            <el-select
              v-model="form.laboratory_id"
              placeholder="请选择实验室"
              filterable
              style="width: 100%"
              :disabled="!form.school_id"
            >
              <el-option
                v-for="lab in filteredLaboratories"
                :key="lab.id"
                :label="lab.name"
                :value="lab.id"
              >
                <span>{{ lab.name }}</span>
                <span style="float: right; color: #8492a6; font-size: 13px">
                  容量: {{ lab.capacity }}人
                </span>
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="实验目录" prop="catalog_id">
        <el-select
          v-model="form.catalog_id"
          placeholder="请选择实验目录"
          filterable
          style="width: 100%"
          @change="handleCatalogChange"
        >
          <el-option
            v-for="catalog in catalogs"
            :key="catalog.id"
            :label="catalog.name"
            :value="catalog.id"
          >
            <div>
              <span>{{ catalog.name }}</span>
              <el-tag size="small" style="margin-left: 8px">
                {{ catalog.grade }}年级
              </el-tag>
            </div>
          </el-option>
        </el-select>
      </el-form-item>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="申请教师" prop="teacher_id">
            <el-select
              v-model="form.teacher_id"
              placeholder="请选择教师"
              filterable
              style="width: 100%"
            >
              <el-option
                v-for="teacher in teachers"
                :key="teacher.id"
                :label="teacher.real_name"
                :value="teacher.id"
              />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="班级名称" prop="class_name">
            <el-input
              v-model="form.class_name"
              placeholder="请输入班级名称"
              maxlength="100"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="form.student_count"
              :min="1"
              :max="selectedLaboratory?.capacity || 100"
              controls-position="right"
              style="width: 100%"
            />
            <span style="margin-left: 8px; color: #909399;">人</span>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="预约日期" prop="reservation_date">
            <el-date-picker
              v-model="form.reservation_date"
              type="date"
              placeholder="请选择预约日期"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
              style="width: 100%"
              :disabled-date="disabledDate"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="开始时间" prop="start_time">
            <el-time-picker
              v-model="form.start_time"
              placeholder="请选择开始时间"
              format="HH:mm"
              value-format="HH:mm"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="结束时间" prop="end_time">
            <el-time-picker
              v-model="form.end_time"
              placeholder="请选择结束时间"
              format="HH:mm"
              value-format="HH:mm"
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
      
      <!-- 实验信息预览 -->
      <div v-if="selectedCatalog" class="catalog-preview">
        <h4>实验信息预览</h4>
        <el-descriptions :column="2" border size="small">
          <el-descriptions-item label="实验名称">
            {{ selectedCatalog.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验编号">
            {{ selectedCatalog.code }}
          </el-descriptions-item>
          <el-descriptions-item label="实验时长">
            {{ selectedCatalog.duration }}分钟
          </el-descriptions-item>
          <el-descriptions-item label="建议人数">
            {{ selectedCatalog.student_count }}人
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
        {{ loading ? '保存中...' : '确定' }}
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  createExperimentReservationApi,
  updateExperimentReservationApi,
  getExperimentCatalogsApi,
  getLaboratoriesApi,
  type ExperimentReservation,
  type ExperimentCatalog,
  type Laboratory
} from '@/api/experiment'
import { getSchoolsApi, getUserListApi, type School } from '@/api/user'
import { useAuthStore } from '@/stores/auth'
import dayjs from 'dayjs'

interface Props {
  modelValue: boolean
  booking?: ExperimentReservation | null
  mode: 'create' | 'edit'
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 权限检查
const authStore = useAuthStore()

// 表单引用
const formRef = ref<FormInstance>()

// 加载状态
const loading = ref(false)

// 数据列表
const schools = ref<School[]>([])
const laboratories = ref<Laboratory[]>([])
const catalogs = ref<ExperimentCatalog[]>([])
const teachers = ref<any[]>([])

// 对话框显示状态
const visible = ref(false)

// 是否可以选择学校（学校级用户不能选择）
const canSelectSchool = computed(() => {
  return authStore.userInfo?.organization_level && authStore.userInfo.organization_level < 5
})

// 表单数据
const form = reactive({
  school_id: undefined as number | undefined,
  catalog_id: undefined as number | undefined,
  laboratory_id: undefined as number | undefined,
  teacher_id: undefined as number | undefined,
  class_name: '',
  student_count: 1,
  reservation_date: '',
  start_time: '',
  end_time: '',
  remark: ''
})

// 对话框标题
const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新增实验预约' : '编辑实验预约'
})

// 过滤后的实验室
const filteredLaboratories = computed(() => {
  if (!form.school_id) return []
  return laboratories.value.filter(lab => lab.school_id === form.school_id)
})

// 选中的实验室
const selectedLaboratory = computed(() => {
  return laboratories.value.find(lab => lab.id === form.laboratory_id)
})

// 选中的实验目录
const selectedCatalog = computed(() => {
  return catalogs.value.find(catalog => catalog.id === form.catalog_id)
})

// 表单验证规则
const rules: FormRules = {
  school_id: [
    { required: true, message: '请选择学校', trigger: 'change' }
  ],
  catalog_id: [
    { required: true, message: '请选择实验目录', trigger: 'change' }
  ],
  laboratory_id: [
    { required: true, message: '请选择实验室', trigger: 'change' }
  ],
  teacher_id: [
    { required: true, message: '请选择申请教师', trigger: 'change' }
  ],
  class_name: [
    { required: true, message: '请输入班级名称', trigger: 'blur' },
    { min: 2, max: 100, message: '班级名称长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  student_count: [
    { required: true, message: '请输入学生人数', trigger: 'blur' }
  ],
  reservation_date: [
    { required: true, message: '请选择预约日期', trigger: 'change' }
  ],
  start_time: [
    { required: true, message: '请选择开始时间', trigger: 'change' }
  ],
  end_time: [
    { required: true, message: '请选择结束时间', trigger: 'change' }
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

// 禁用日期
const disabledDate = (time: Date) => {
  // 禁用今天之前的日期
  return time.getTime() < Date.now() - 8.64e7
}

// 初始化表单
const initForm = () => {
  if (props.mode === 'edit' && props.booking) {
    // 编辑模式，填充数据
    Object.assign(form, {
      school_id: props.booking.school_id,
      catalog_id: props.booking.catalog_id,
      laboratory_id: props.booking.laboratory_id,
      teacher_id: props.booking.teacher_id,
      class_name: props.booking.class_name,
      student_count: props.booking.student_count,
      reservation_date: props.booking.reservation_date,
      start_time: props.booking.start_time,
      end_time: props.booking.end_time,
      remark: props.booking.remark || ''
    })
    // 加载教师列表
    loadTeachers()
  } else {
    // 新增模式，重置表单
    resetForm()
    // 如果是学校级用户，自动设置学校ID
    if (!canSelectSchool.value && authStore.userInfo?.school_id) {
      form.school_id = authStore.userInfo.school_id
      // 自动加载教师列表
      loadTeachers()
    }
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    school_id: undefined,
    catalog_id: undefined,
    laboratory_id: undefined,
    teacher_id: undefined,
    class_name: '',
    student_count: 1,
    reservation_date: '',
    start_time: '',
    end_time: '',
    remark: ''
  })
  formRef.value?.clearValidate()
}

// 处理学校变化
const handleSchoolChange = () => {
  form.laboratory_id = undefined
  form.teacher_id = undefined
  loadTeachers()
}

// 处理实验目录变化
const handleCatalogChange = () => {
  if (selectedCatalog.value) {
    form.student_count = selectedCatalog.value.student_count
  }
}

// 加载数据
const loadSchools = async () => {
  try {
    const response = await getSchoolsApi()
    // 检查响应数据结构
    if (response.data && Array.isArray(response.data.data)) {
      schools.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      schools.value = response.data
    } else {
      console.warn('学校数据格式不正确:', response.data)
      schools.value = []
    }
  } catch (error) {
    console.error('加载学校列表失败:', error)
    schools.value = []
  }
}

const loadLaboratories = async () => {
  try {
    const response = await getLaboratoriesApi()
    // 检查响应数据结构
    if (response.data && Array.isArray(response.data.data)) {
      laboratories.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      laboratories.value = response.data
    } else {
      console.warn('实验室数据格式不正确:', response.data)
      laboratories.value = []
    }
  } catch (error) {
    console.error('加载实验室列表失败:', error)
    laboratories.value = []
  }
}

const loadCatalogs = async () => {
  try {
    const response = await getExperimentCatalogsApi({ per_page: 1000 })
    if (response.data && response.data.data && Array.isArray(response.data.data)) {
      catalogs.value = response.data.data
    } else {
      console.warn('实验目录数据格式不正确:', response.data)
      catalogs.value = []
    }
  } catch (error) {
    console.error('加载实验目录失败:', error)
    catalogs.value = []
  }
}

const loadTeachers = async () => {
  if (!form.school_id) {
    teachers.value = []
    return
  }

  try {
    const response = await getUserListApi({
      school_id: form.school_id,
      role: 'teacher',
      per_page: 1000
    })

    // 处理响应数据结构
    if (response.data && response.data.items && Array.isArray(response.data.items)) {
      teachers.value = response.data.items
    } else if (response.data && Array.isArray(response.data)) {
      teachers.value = response.data
    } else {
      console.warn('教师数据格式不正确:', response.data)
      teachers.value = []
    }
  } catch (error) {
    console.error('加载教师列表失败:', error)
    teachers.value = []
  }
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    
    // 验证时间
    if (form.start_time >= form.end_time) {
      ElMessage.error('结束时间必须大于开始时间')
      return
    }
    
    loading.value = true
    
    const data = { ...form }
    
    if (props.mode === 'create') {
      await createExperimentReservationApi(data)
      ElMessage.success('创建成功')
    } else {
      await updateExperimentReservationApi(props.booking!.id, data)
      ElMessage.success('更新成功')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('保存实验预约失败:', error)
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
  loadSchools()
  loadLaboratories()
  loadCatalogs()
})
</script>

<style scoped>
.catalog-preview {
  margin-top: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e9ecef;
}

.catalog-preview h4 {
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
