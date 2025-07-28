<template>
  <el-dialog
    v-model="visible"
    :title="isAssignment ? '指定学校目录' : '配置学校目录'"
    width="600px"
    :close-on-click-modal="false"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
    >
      <el-form-item label="配置类型">
        <el-radio-group v-model="form.config_type" :disabled="isAssignment">
          <el-radio value="selection">学校选择</el-radio>
          <el-radio value="assignment">上级指定</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="目录级别" prop="source_level">
        <el-select 
          v-model="form.source_level" 
          placeholder="选择目录级别"
          @change="handleLevelChange"
          style="width: 100%"
        >
          <el-option 
            v-for="level in availableLevels" 
            :key="level" 
            :label="getLevelName(level)" 
            :value="level" 
          />
        </el-select>
      </el-form-item>

      <el-form-item label="目录来源" prop="source_org_id">
        <el-select 
          v-model="form.source_org_id" 
          placeholder="选择目录来源组织"
          @change="handleOrgChange"
          style="width: 100%"
          :loading="loadingOrgs"
        >
          <el-option 
            v-for="org in availableOrgs" 
            :key="org.id" 
            :label="org.name" 
            :value="org.id" 
          />
        </el-select>
      </el-form-item>

      <el-form-item label="权限设置">
        <el-checkbox v-model="form.can_modify_selection" :disabled="isAssignment">
          允许修改选择
        </el-checkbox>
        <br>
        <el-checkbox v-model="form.can_delete_experiments">
          允许删除实验项目
        </el-checkbox>
      </el-form-item>

      <el-form-item label="生效日期">
        <el-date-picker
          v-model="form.effective_date"
          type="date"
          placeholder="选择生效日期"
          style="width: 100%"
          :disabled-date="disabledDate"
        />
      </el-form-item>

      <el-form-item label="失效日期">
        <el-date-picker
          v-model="form.expiry_date"
          type="date"
          placeholder="选择失效日期（可选）"
          style="width: 100%"
          :disabled-date="disabledExpiryDate"
        />
      </el-form-item>

      <el-form-item label="配置理由">
        <el-input
          v-model="form.config_reason"
          type="textarea"
          :rows="3"
          placeholder="请输入配置理由"
          maxlength="1000"
          show-word-limit
        />
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleCancel">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确定
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { schoolCatalogConfigApi, type ConfigureSchoolData, type SchoolExperimentCatalogConfig, type Organization, type UserPermissions } from '@/api/schoolCatalogConfig'

interface Props {
  modelValue: boolean
  schoolId?: number
  currentConfig?: SchoolExperimentCatalogConfig
  permissions?: UserPermissions
  isAssignment?: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = withDefaults(defineProps<Props>(), {
  isAssignment: false
})

const emit = defineEmits<Emits>()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const loadingOrgs = ref(false)
const availableOrgs = ref<Organization[]>([])

const form = ref<ConfigureSchoolData>({
  school_id: 0,
  config_type: 'selection',
  source_level: 1,
  source_org_id: 0,
  source_org_name: '',
  can_modify_selection: true,
  can_delete_experiments: false,
  config_reason: '',
  effective_date: '',
  expiry_date: ''
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const availableLevels = computed(() => {
  return props.permissions?.available_catalog_levels || [1, 2, 3]
})

// 表单验证规则
const rules: FormRules = {
  source_level: [
    { required: true, message: '请选择目录级别', trigger: 'change' }
  ],
  source_org_id: [
    { required: true, message: '请选择目录来源组织', trigger: 'change' }
  ]
}

// 方法
const initForm = () => {
  if (props.currentConfig) {
    // 编辑模式
    form.value = {
      school_id: props.currentConfig.school_id,
      config_type: props.currentConfig.config_type,
      source_level: props.currentConfig.source_level,
      source_org_id: props.currentConfig.source_org_id,
      source_org_name: props.currentConfig.source_org_name,
      can_modify_selection: props.currentConfig.can_modify_selection,
      can_delete_experiments: props.currentConfig.can_delete_experiments,
      config_reason: props.currentConfig.config_reason || '',
      effective_date: props.currentConfig.effective_date || '',
      expiry_date: props.currentConfig.expiry_date || ''
    }
  } else {
    // 新建模式
    form.value = {
      school_id: props.schoolId || 0,
      config_type: props.isAssignment ? 'assignment' : 'selection',
      source_level: availableLevels.value[0] || 1,
      source_org_id: 0,
      source_org_name: '',
      can_modify_selection: !props.isAssignment,
      can_delete_experiments: false,
      config_reason: '',
      effective_date: '',
      expiry_date: ''
    }
  }
  
  // 加载可用组织
  if (form.value.source_level) {
    loadAvailableOrgs()
  }
}

const loadAvailableOrgs = async () => {
  if (!props.schoolId || !form.value.source_level) return
  
  try {
    loadingOrgs.value = true
    const response = await schoolCatalogConfigApi.getAvailableOrganizations(
      props.schoolId,
      form.value.source_level
    )
    
    if (response.data.success) {
      availableOrgs.value = response.data.data
    }
  } catch (error) {
    console.error('获取可用组织失败:', error)
    ElMessage.error('获取可用组织失败')
  } finally {
    loadingOrgs.value = false
  }
}

const handleLevelChange = () => {
  form.value.source_org_id = 0
  form.value.source_org_name = ''
  loadAvailableOrgs()
}

const handleOrgChange = () => {
  const selectedOrg = availableOrgs.value.find(org => org.id === form.value.source_org_id)
  if (selectedOrg) {
    form.value.source_org_name = selectedOrg.name
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    const response = await schoolCatalogConfigApi.configureSchool(form.value)
    
    if (response.data.success) {
      ElMessage.success('配置保存成功')
      emit('success')
    } else {
      ElMessage.error(response.data.message || '配置保存失败')
    }
  } catch (error) {
    console.error('配置保存失败:', error)
    ElMessage.error('配置保存失败')
  } finally {
    submitting.value = false
  }
}

const handleCancel = () => {
  visible.value = false
}

const getLevelName = (level: number) => {
  const names = { 1: '省级', 2: '市级', 3: '区县级' }
  return names[level as keyof typeof names] || '未知级别'
}

const disabledDate = (time: Date) => {
  return time.getTime() < Date.now() - 24 * 60 * 60 * 1000
}

const disabledExpiryDate = (time: Date) => {
  if (!form.value.effective_date) return false
  const effectiveTime = new Date(form.value.effective_date).getTime()
  return time.getTime() <= effectiveTime
}

// 监听器
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    initForm()
  }
})

watch(() => props.schoolId, () => {
  if (props.modelValue) {
    initForm()
  }
})
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
