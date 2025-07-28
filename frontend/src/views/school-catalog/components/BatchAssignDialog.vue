<template>
  <el-dialog
    v-model="visible"
    title="批量指定学校目录"
    width="600px"
    :close-on-click-modal="false"
  >
    <div class="selected-schools">
      <h4>目标学校 (已选择 {{ selectedSchools.length }} 所学校)</h4>
      <div class="school-tags">
        <el-tag 
          v-for="school in selectedSchools" 
          :key="school.id"
          type="info"
          size="small"
          style="margin: 2px;"
        >
          {{ school.name }}
        </el-tag>
      </div>
    </div>

    <el-divider />

    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
    >
      <el-form-item label="目录级别" prop="source_level">
        <el-select 
          v-model="form.source_level" 
          placeholder="选择目录级别"
          @change="handleLevelChange"
          style="width: 100%"
        >
          <el-option label="省级" value="1" />
          <el-option label="市级" value="2" />
          <el-option label="区县级" value="3" />
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
        <el-checkbox v-model="form.can_delete_experiments">
          允许学校删除实验项目
        </el-checkbox>
      </el-form-item>

      <el-form-item label="指定理由">
        <el-input
          v-model="form.config_reason"
          type="textarea"
          :rows="3"
          placeholder="请输入指定理由"
          maxlength="1000"
          show-word-limit
        />
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleCancel">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确认指定
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
import { schoolCatalogConfigApi, type BatchAssignData, type School, type Organization } from '@/api/schoolCatalogConfig'

interface Props {
  modelValue: boolean
  selectedSchools: School[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const formRef = ref<FormInstance>()
const submitting = ref(false)
const loadingOrgs = ref(false)
const availableOrgs = ref<Organization[]>([])

const form = ref<BatchAssignData>({
  school_ids: [],
  source_level: 1,
  source_org_id: 0,
  source_org_name: '',
  can_delete_experiments: false,
  config_reason: ''
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
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
  form.value = {
    school_ids: props.selectedSchools.map(school => school.id),
    source_level: 1,
    source_org_id: 0,
    source_org_name: '',
    can_delete_experiments: false,
    config_reason: ''
  }
  
  // 加载可用组织
  loadAvailableOrgs()
}

const loadAvailableOrgs = async () => {
  if (props.selectedSchools.length === 0 || !form.value.source_level) return
  
  try {
    loadingOrgs.value = true
    // 使用第一个学校的ID来获取可用组织（假设同批次学校的可用组织相同）
    const response = await schoolCatalogConfigApi.getAvailableOrganizations(
      props.selectedSchools[0].id,
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
    
    const response = await schoolCatalogConfigApi.batchAssignSchools(form.value)
    
    if (response.data.success) {
      const summary = response.data.data.summary
      ElMessage.success(`批量指定完成，成功 ${summary.success} 个，失败 ${summary.failed} 个`)
      emit('success')
    } else {
      ElMessage.error(response.data.message || '批量指定失败')
    }
  } catch (error) {
    console.error('批量指定失败:', error)
    ElMessage.error('批量指定失败')
  } finally {
    submitting.value = false
  }
}

const handleCancel = () => {
  visible.value = false
}

// 监听器
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    initForm()
  }
})
</script>

<style scoped>
.selected-schools {
  margin-bottom: 20px;
}

.selected-schools h4 {
  margin: 0 0 10px 0;
  color: #303133;
}

.school-tags {
  max-height: 100px;
  overflow-y: auto;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 4px;
}

.dialog-footer {
  text-align: right;
}
</style>
