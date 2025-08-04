<template>
  <el-dialog
    v-model="visible"
    :title="props.mode === 'create' ? '新增设备' : '编辑设备'"
    width="600px"
    :close-on-click-modal="false"
    @close="handleClose"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
      label-position="right"
    >
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
      
      <el-form-item label="设备名称" prop="name">
        <el-input
          v-model="form.name"
          placeholder="请输入设备名称"
          maxlength="200"
        />
      </el-form-item>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="设备编号" prop="code">
            <el-input
              v-model="form.code"
              placeholder="请输入设备编号"
              maxlength="100"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="设备型号" prop="model">
            <el-input
              v-model="form.model"
              placeholder="请输入设备型号"
              maxlength="100"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="设备品牌" prop="brand">
            <el-input
              v-model="form.brand"
              placeholder="请输入设备品牌"
              maxlength="100"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="供应商" prop="supplier">
            <el-input
              v-model="form.supplier"
              placeholder="请输入供应商"
              maxlength="200"
            />
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="数量" prop="quantity">
            <el-input-number
              v-model="form.quantity"
              :min="1"
              :max="9999"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="8">
          <el-form-item label="单位" prop="unit">
            <el-select v-model="form.unit" style="width: 100%">
              <el-option label="台" value="台" />
              <el-option label="个" value="个" />
              <el-option label="套" value="套" />
              <el-option label="支" value="支" />
              <el-option label="只" value="只" />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="8">
          <el-form-item label="设备状态" prop="status">
            <el-select v-model="form.status" style="width: 100%">
              <el-option label="正常" :value="1" />
              <el-option label="借出" :value="2" />
              <el-option label="维修" :value="3" />
              <el-option label="报废" :value="4" />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="存放位置" prop="storage_location">
        <el-input
          v-model="form.storage_location"
          placeholder="请输入存放位置"
          maxlength="200"
        />
      </el-form-item>
      
      <el-form-item label="备注" prop="remark">
        <el-input
          v-model="form.remark"
          type="textarea"
          :rows="3"
          placeholder="请输入备注信息"
          maxlength="2000"
        />
      </el-form-item>
    </el-form>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" :loading="submitting" @click="handleSubmit">
          {{ props.mode === 'create' ? '创建' : '更新' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import {
  createEquipmentApi,
  updateEquipmentApi,
  getEquipmentCategoriesApi,
  type Equipment,
  type EquipmentCategory,
  type CreateEquipmentParams
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'

// Props
interface Props {
  modelValue: boolean
  mode: 'create' | 'edit'
  equipment?: Equipment
}

const props = withDefaults(defineProps<Props>(), {
  mode: 'create'
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  success: []
}>()

// Store
const authStore = useAuthStore()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const categories = ref<EquipmentCategory[]>([])

// 表单数据
const form = reactive<CreateEquipmentParams>({
  school_id: authStore.userInfo?.school_id || 0,
  category_id: 0,
  name: '',
  code: '',
  model: '',
  brand: '',
  supplier: '',
  quantity: 1,
  unit: '台',
  status: 1,
  storage_location: '',
  remark: ''
})

// 表单验证规则
const rules: FormRules = {
  category_id: [
    { required: true, message: '请选择设备分类', trigger: 'change' }
  ],
  name: [
    { required: true, message: '请输入设备名称', trigger: 'blur' },
    { min: 2, max: 200, message: '设备名称长度在 2 到 200 个字符', trigger: 'blur' }
  ],
  quantity: [
    { required: true, message: '请输入数量', trigger: 'blur' }
  ],
  unit: [
    { required: true, message: '请选择单位', trigger: 'change' }
  ],
  status: [
    { required: true, message: '请选择设备状态', trigger: 'change' }
  ]
}

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 监听设备数据变化
watch(() => props.equipment, (newVal) => {
  if (newVal && props.mode === 'edit') {
    Object.assign(form, {
      school_id: newVal.school_id,
      category_id: newVal.category_id,
      name: newVal.name,
      code: newVal.code || '',
      model: newVal.model || '',
      brand: newVal.brand || '',
      supplier: newVal.supplier || '',
      quantity: newVal.quantity,
      unit: newVal.unit,
      status: newVal.status,
      storage_location: newVal.storage_location || '',
      remark: newVal.remark || ''
    })
  }
}, { immediate: true })

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    loadCategories()
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
    supplier: '',
    quantity: 1,
    unit: '台',
    status: 1,
    storage_location: '',
    remark: ''
  })
  formRef.value?.clearValidate()
}

// 加载设备分类
const loadCategories = async () => {
  try {
    const response = await getEquipmentCategoriesApi({ all: true, status: 1 })
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('加载设备分类失败:', error)
    categories.value = []
  }
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
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
