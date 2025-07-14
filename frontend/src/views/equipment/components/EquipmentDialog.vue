<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="900px"
    :before-close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
      size="large"
    >
      <el-tabs v-model="activeTab" type="border-card">
        <!-- 基本信息 -->
        <el-tab-pane label="基本信息" name="basic">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="设备分类" prop="category_id">
                <el-select
                  v-model="form.category_id"
                  placeholder="请选择设备分类"
                  filterable
                  style="width: 100%"
                >
                  <el-option
                    v-for="category in categories"
                    :key="category.id"
                    :label="category.name"
                    :value="category.id"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="设备编号" prop="code">
                <el-input
                  v-model="form.code"
                  placeholder="请输入设备编号"
                  maxlength="50"
                />
              </el-form-item>
            </el-col>
          </el-row>
          
          <el-form-item label="设备名称" prop="name">
            <el-input
              v-model="form.name"
              placeholder="请输入设备名称"
              maxlength="200"
            />
          </el-form-item>
          
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="设备型号" prop="model">
                <el-input
                  v-model="form.model"
                  placeholder="请输入设备型号"
                  maxlength="100"
                />
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="设备品牌" prop="brand">
                <el-input
                  v-model="form.brand"
                  placeholder="请输入设备品牌"
                  maxlength="100"
                />
              </el-form-item>
            </el-col>
          </el-row>
          
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="序列号" prop="serial_number">
                <el-input
                  v-model="form.serial_number"
                  placeholder="请输入序列号"
                  maxlength="100"
                />
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="存放位置" prop="location">
                <el-input
                  v-model="form.location"
                  placeholder="请输入存放位置"
                  maxlength="200"
                />
              </el-form-item>
            </el-col>
          </el-row>
          
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="设备状态" prop="status">
                <el-select
                  v-model="form.status"
                  placeholder="请选择设备状态"
                  style="width: 100%"
                >
                  <el-option
                    v-for="status in statusOptions"
                    :key="status.value"
                    :label="status.label"
                    :value="status.value"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="设备状况" prop="condition">
                <el-select
                  v-model="form.condition"
                  placeholder="请选择设备状况"
                  style="width: 100%"
                >
                  <el-option
                    v-for="condition in conditionOptions"
                    :key="condition.value"
                    :label="condition.label"
                    :value="condition.value"
                  />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </el-tab-pane>
        
        <!-- 采购信息 -->
        <el-tab-pane label="采购信息" name="purchase">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="采购日期" prop="purchase_date">
                <el-date-picker
                  v-model="form.purchase_date"
                  type="date"
                  placeholder="请选择采购日期"
                  style="width: 100%"
                  value-format="YYYY-MM-DD"
                />
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="采购价格" prop="purchase_price">
                <el-input-number
                  v-model="form.purchase_price"
                  placeholder="请输入采购价格"
                  :min="0"
                  :precision="2"
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
          </el-row>
          
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="供应商" prop="supplier">
                <el-input
                  v-model="form.supplier"
                  placeholder="请输入供应商"
                  maxlength="200"
                />
              </el-form-item>
            </el-col>
            
            <el-col :span="12">
              <el-form-item label="保修期(月)" prop="warranty_period">
                <el-input-number
                  v-model="form.warranty_period"
                  placeholder="请输入保修期"
                  :min="0"
                  :max="120"
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-tab-pane>
        
        <!-- 技术参数 -->
        <el-tab-pane label="技术参数" name="specs">
          <el-form-item label="技术规格">
            <el-input
              v-model="form.specifications"
              type="textarea"
              placeholder="请输入技术规格参数"
              :rows="6"
              maxlength="2000"
              show-word-limit
            />
          </el-form-item>
          
          <el-form-item label="设备描述">
            <el-input
              v-model="form.description"
              type="textarea"
              placeholder="请输入设备描述"
              :rows="4"
              maxlength="1000"
              show-word-limit
            />
          </el-form-item>
        </el-tab-pane>
        
        <!-- 设备照片 -->
        <el-tab-pane label="设备照片" name="photos">
          <el-form-item label="设备照片">
            <el-upload
              v-model:file-list="photoList"
              :action="uploadAction"
              :headers="uploadHeaders"
              :on-success="handlePhotoSuccess"
              :on-remove="handlePhotoRemove"
              :before-upload="beforePhotoUpload"
              list-type="picture-card"
              :limit="5"
            >
              <el-icon><Plus /></el-icon>
            </el-upload>
            <div class="upload-tip">
              支持 jpg、png 格式，单张图片不超过 2MB，最多上传 5 张
            </div>
          </el-form-item>
        </el-tab-pane>
      </el-tabs>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          {{ mode === 'create' ? '创建' : '更新' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import {
  createEquipmentApi,
  updateEquipmentApi,
  getEquipmentCategoriesApi,
  uploadEquipmentPhotoApi,
  type Equipment,
  type EquipmentCategory,
  type CreateEquipmentParams
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'

interface Props {
  modelValue: boolean
  equipment?: Equipment | null
  mode: 'create' | 'edit'
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = withDefaults(defineProps<Props>(), {
  equipment: null
})

const emit = defineEmits<Emits>()

// 权限检查
const authStore = useAuthStore()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const activeTab = ref('basic')
const categories = ref<EquipmentCategory[]>([])
const photoList = ref<any[]>([])

// 表单数据
const form = reactive<CreateEquipmentParams>({
  school_id: authStore.userInfo?.school_id || 0,
  category_id: 0,
  name: '',
  code: '',
  model: '',
  brand: '',
  serial_number: '',
  purchase_date: '',
  purchase_price: 0,
  supplier: '',
  warranty_period: 12,
  location: '',
  status: 1,
  condition: 1,
  description: '',
  specifications: '',
  photos: []
})

// 选项数据
const statusOptions = [
  { value: 1, label: '正常' },
  { value: 2, label: '借出' },
  { value: 3, label: '维修' },
  { value: 4, label: '报废' }
]

const conditionOptions = [
  { value: 1, label: '优' },
  { value: 2, label: '良' },
  { value: 3, label: '中' },
  { value: 4, label: '差' }
]

// 表单验证规则
const rules: FormRules = {
  category_id: [
    { required: true, message: '请选择设备分类', trigger: 'change' }
  ],
  name: [
    { required: true, message: '请输入设备名称', trigger: 'blur' },
    { min: 2, max: 200, message: '设备名称长度在 2 到 200 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入设备编号', trigger: 'blur' },
    { min: 2, max: 50, message: '设备编号长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  model: [
    { required: true, message: '请输入设备型号', trigger: 'blur' }
  ],
  brand: [
    { required: true, message: '请输入设备品牌', trigger: 'blur' }
  ],
  serial_number: [
    { required: true, message: '请输入序列号', trigger: 'blur' }
  ],
  purchase_date: [
    { required: true, message: '请选择采购日期', trigger: 'change' }
  ],
  purchase_price: [
    { required: true, message: '请输入采购价格', trigger: 'blur' },
    { type: 'number', min: 0, message: '采购价格必须大于等于0', trigger: 'blur' }
  ],
  supplier: [
    { required: true, message: '请输入供应商', trigger: 'blur' }
  ],
  warranty_period: [
    { required: true, message: '请输入保修期', trigger: 'blur' },
    { type: 'number', min: 0, max: 120, message: '保修期在 0 到 120 个月之间', trigger: 'blur' }
  ],
  location: [
    { required: true, message: '请输入存放位置', trigger: 'blur' }
  ],
  status: [
    { required: true, message: '请选择设备状态', trigger: 'change' }
  ],
  condition: [
    { required: true, message: '请选择设备状况', trigger: 'change' }
  ]
}

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.mode === 'create' ? '新增设备' : '编辑设备'
})

const uploadAction = computed(() => {
  return `/api/equipments/${props.equipment?.id}/photos`
})

const uploadHeaders = computed(() => {
  return {
    Authorization: `Bearer ${authStore.token}`
  }
})

// 监听设备数据变化
watch(() => props.equipment, (newVal) => {
  if (newVal && props.mode === 'edit') {
    Object.assign(form, {
      school_id: newVal.school_id,
      category_id: newVal.category_id,
      name: newVal.name,
      code: newVal.code,
      model: newVal.model,
      brand: newVal.brand,
      serial_number: newVal.serial_number,
      purchase_date: newVal.purchase_date,
      purchase_price: newVal.purchase_price,
      supplier: newVal.supplier,
      warranty_period: newVal.warranty_period,
      location: newVal.location,
      status: newVal.status,
      condition: newVal.condition,
      description: newVal.description || '',
      specifications: newVal.specifications || '',
      photos: newVal.photos || []
    })

    // 设置照片列表
    photoList.value = (newVal.photos || []).map((url, index) => ({
      uid: index,
      name: `photo-${index}`,
      status: 'success',
      url
    }))
  }
}, { immediate: true })

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    activeTab.value = 'basic'
    if (props.mode === 'create') {
      resetForm()
    }
  }
})

// 重置表单
const resetForm = () => {
  Object.assign(form, {
    school_id: authStore.userInfo?.school_id || 0,
    category_id: 0,
    name: '',
    code: '',
    model: '',
    brand: '',
    serial_number: '',
    purchase_date: '',
    purchase_price: 0,
    supplier: '',
    warranty_period: 12,
    location: '',
    status: 1,
    condition: 1,
    description: '',
    specifications: '',
    photos: []
  })
  photoList.value = []
  formRef.value?.clearValidate()
}

// 加载设备分类
const loadCategories = async () => {
  try {
    const response = await getEquipmentCategoriesApi()
    categories.value = response.data
  } catch (error) {
    console.error('加载设备分类失败:', error)
  }
}

// 照片上传成功
const handlePhotoSuccess = (response: any, file: any) => {
  if (response.code === 200) {
    form.photos.push(response.data.url)
    ElMessage.success('照片上传成功')
  } else {
    ElMessage.error(response.message || '照片上传失败')
  }
}

// 移除照片
const handlePhotoRemove = (file: any) => {
  const index = photoList.value.findIndex(item => item.uid === file.uid)
  if (index > -1 && form.photos[index]) {
    form.photos.splice(index, 1)
  }
}

// 上传前检查
const beforePhotoUpload = (file: File) => {
  const isImage = file.type.startsWith('image/')
  const isLt2M = file.size / 1024 / 1024 < 2

  if (!isImage) {
    ElMessage.error('只能上传图片文件!')
    return false
  }
  if (!isLt2M) {
    ElMessage.error('图片大小不能超过 2MB!')
    return false
  }
  return true
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    if (props.mode === 'create') {
      await createEquipmentApi(form)
      ElMessage.success('设备创建成功')
    } else {
      await updateEquipmentApi(props.equipment!.id, form)
      ElMessage.success('设备更新成功')
    }

    emit('success')
    handleClose()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('提交失败')
  } finally {
    submitting.value = false
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}

// 初始化
onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}

.upload-tip {
  margin-top: 8px;
  color: #909399;
  font-size: 12px;
}

:deep(.el-tabs__content) {
  padding: 20px 0;
}

:deep(.el-upload--picture-card) {
  width: 100px;
  height: 100px;
}

:deep(.el-upload-list--picture-card .el-upload-list__item) {
  width: 100px;
  height: 100px;
}
</style>
