<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="800px"
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
          <el-form-item label="学科" prop="subject_id">
            <el-select
              v-model="form.subject_id"
              placeholder="请选择学科"
              filterable
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
          <el-form-item label="实验编号" prop="code">
            <el-input
              v-model="form.code"
              placeholder="请输入实验编号"
              maxlength="50"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="实验名称" prop="name">
        <el-input
          v-model="form.name"
          placeholder="请输入实验名称"
          maxlength="200"
        />
      </el-form-item>
      
      <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="年级" prop="grade">
            <el-select
              v-model="form.grade"
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
        
        <el-col :span="8">
          <el-form-item label="学期" prop="semester">
            <el-select
              v-model="form.semester"
              placeholder="请选择学期"
              style="width: 100%"
            >
              <el-option label="上学期" :value="1" />
              <el-option label="下学期" :value="2" />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="8">
          <el-form-item label="实验类型" prop="type">
            <el-select
              v-model="form.type"
              placeholder="请选择类型"
              style="width: 100%"
            >
              <el-option
                v-for="type in typeOptions"
                :key="type.value"
                :label="type.label"
                :value="type.value"
              />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="章节" prop="chapter">
        <el-input
          v-model="form.chapter"
          placeholder="请输入所属章节"
          maxlength="100"
        />
      </el-form-item>
      
      <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="实验时长" prop="duration">
            <el-input-number
              v-model="form.duration"
              :min="1"
              :max="300"
              controls-position="right"
              style="width: 100%"
            >
              <template #append>分钟</template>
            </el-input-number>
          </el-form-item>
        </el-col>
        
        <el-col :span="8">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="form.student_count"
              :min="1"
              :max="100"
              controls-position="right"
              style="width: 100%"
            >
              <template #append>人</template>
            </el-input-number>
          </el-form-item>
        </el-col>
        
        <el-col :span="8">
          <el-form-item label="难度等级" prop="difficulty_level">
            <el-rate
              v-model="form.difficulty_level"
              :max="5"
              show-text
              :texts="['很简单', '简单', '一般', '困难', '很困难']"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="标准实验">
            <el-switch
              v-model="form.is_standard"
              active-text="是"
              inactive-text="否"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="状态">
            <el-switch
              v-model="form.status"
              active-text="启用"
              inactive-text="禁用"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="实验目标" prop="objective">
        <el-input
          v-model="form.objective"
          type="textarea"
          :rows="3"
          placeholder="请输入实验目标和要求"
        />
      </el-form-item>
      
      <el-form-item label="实验器材" prop="materials">
        <el-input
          v-model="form.materials"
          type="textarea"
          :rows="3"
          placeholder="请输入所需实验器材和材料"
        />
      </el-form-item>
      
      <el-form-item label="实验步骤" prop="procedure">
        <el-input
          v-model="form.procedure"
          type="textarea"
          :rows="4"
          placeholder="请输入详细的实验步骤"
        />
      </el-form-item>
      
      <el-form-item label="安全注意" prop="safety_notes">
        <el-input
          v-model="form.safety_notes"
          type="textarea"
          :rows="3"
          placeholder="请输入安全注意事项"
        />
      </el-form-item>
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
  createExperimentCatalogApi,
  updateExperimentCatalogApi,
  getSubjectsApi,
  type ExperimentCatalog,
  type Subject
} from '@/api/experiment'

interface Props {
  modelValue: boolean
  catalog?: ExperimentCatalog | null
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

// 学科列表
const subjects = ref<Subject[]>([])

// 对话框显示状态
const visible = ref(false)

// 表单数据
const form = reactive({
  subject_id: undefined as number | undefined,
  name: '',
  code: '',
  type: undefined as number | undefined,
  grade: undefined as number | undefined,
  semester: undefined as number | undefined,
  chapter: '',
  duration: 45,
  student_count: 2,
  objective: '',
  materials: '',
  procedure: '',
  safety_notes: '',
  difficulty_level: 1,
  is_standard: false,
  status: true
})

// 对话框标题
const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新增实验目录' : '编辑实验目录'
})

// 年级选项
const gradeOptions = computed(() => {
  const options = []
  for (let i = 1; i <= 12; i++) {
    options.push({ label: `${i}年级`, value: i })
  }
  return options
})

// 类型选项
const typeOptions = [
  { label: '演示实验', value: 1 },
  { label: '分组实验', value: 2 },
  { label: '探究实验', value: 3 },
  { label: '综合实验', value: 4 }
]

// 表单验证规则
const rules: FormRules = {
  subject_id: [
    { required: true, message: '请选择学科', trigger: 'change' }
  ],
  name: [
    { required: true, message: '请输入实验名称', trigger: 'blur' },
    { min: 2, max: 200, message: '实验名称长度在 2 到 200 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入实验编号', trigger: 'blur' },
    { min: 2, max: 50, message: '实验编号长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  type: [
    { required: true, message: '请选择实验类型', trigger: 'change' }
  ],
  grade: [
    { required: true, message: '请选择年级', trigger: 'change' }
  ],
  semester: [
    { required: true, message: '请选择学期', trigger: 'change' }
  ],
  duration: [
    { required: true, message: '请输入实验时长', trigger: 'blur' }
  ],
  student_count: [
    { required: true, message: '请输入学生人数', trigger: 'blur' }
  ],
  difficulty_level: [
    { required: true, message: '请选择难度等级', trigger: 'change' }
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

// 初始化表单
const initForm = () => {
  if (props.mode === 'edit' && props.catalog) {
    // 编辑模式，填充数据
    Object.assign(form, {
      subject_id: props.catalog.subject_id,
      name: props.catalog.name,
      code: props.catalog.code,
      type: props.catalog.type,
      grade: props.catalog.grade,
      semester: props.catalog.semester,
      chapter: props.catalog.chapter || '',
      duration: props.catalog.duration,
      student_count: props.catalog.student_count,
      objective: props.catalog.objective || '',
      materials: props.catalog.materials || '',
      procedure: props.catalog.procedure || '',
      safety_notes: props.catalog.safety_notes || '',
      difficulty_level: props.catalog.difficulty_level,
      is_standard: Boolean(props.catalog.is_standard),
      status: Boolean(props.catalog.status)
    })
  } else {
    // 新增模式，重置表单
    resetForm()
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    subject_id: undefined,
    name: '',
    code: '',
    type: undefined,
    grade: undefined,
    semester: undefined,
    chapter: '',
    duration: 45,
    student_count: 2,
    objective: '',
    materials: '',
    procedure: '',
    safety_notes: '',
    difficulty_level: 1,
    is_standard: false,
    status: true
  })
  formRef.value?.clearValidate()
}

// 加载学科列表
const loadSubjects = async () => {
  try {
    const response = await getSubjectsApi()
    // 检查响应数据结构
    if (response.data && Array.isArray(response.data.data)) {
      subjects.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      subjects.value = response.data
    } else {
      console.warn('学科数据格式不正确:', response.data)
      subjects.value = []
    }
  } catch (error) {
    console.error('加载学科列表失败:', error)
    subjects.value = []
  }
}

// 处理提交
const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    
    loading.value = true
    
    const data = {
      ...form,
      is_standard: form.is_standard,
      status: form.status
    }
    
    if (props.mode === 'create') {
      await createExperimentCatalogApi(data)
      ElMessage.success('创建成功')
    } else {
      await updateExperimentCatalogApi(props.catalog!.id, data)
      ElMessage.success('更新成功')
    }
    
    emit('success')
    handleClose()
  } catch (error) {
    console.error('保存实验目录失败:', error)
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
  loadSubjects()
})
</script>

<style scoped>
:deep(.el-form-item) {
  margin-bottom: 20px;
}

:deep(.el-textarea__inner) {
  resize: vertical;
}
</style>
