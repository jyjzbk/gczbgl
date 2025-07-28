<template>
  <div class="experiment-requirements-config">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>实验要求配置管理</span>
          <el-button 
            type="primary" 
            @click="showCreateDialog"
            v-if="canCreate"
          >
            <el-icon><Plus /></el-icon>
            新增配置
          </el-button>
        </div>
      </template>

      <!-- 筛选条件 -->
      <div class="filter-section">
        <el-form :model="filterForm" inline>
          <el-form-item label="组织类型">
            <el-select 
              v-model="filterForm.organization_type" 
              placeholder="请选择组织类型"
              clearable
              @change="handleFilterChange"
            >
              <el-option
                v-for="item in organizationTypes"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="实验类型">
            <el-select 
              v-model="filterForm.experiment_type" 
              placeholder="请选择实验类型"
              clearable
              @change="handleFilterChange"
            >
              <el-option
                v-for="item in experimentTypes"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="loadConfigs">
              <el-icon><Search /></el-icon>
              查询
            </el-button>
            <el-button @click="resetFilter">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 配置列表 -->
      <el-table 
        :data="configs" 
        v-loading="loading"
        stripe
        border
      >
        <el-table-column prop="organization_type_name" label="组织类型" width="100" />
        <el-table-column prop="organization_name" label="组织名称" width="150" />
        <el-table-column prop="experiment_type" label="实验类型" width="100" />
        
        <el-table-column label="图片要求" width="120">
          <template #default="{ row }">
            {{ row.min_images }} - {{ row.max_images }} 张
          </template>
        </el-table-column>
        
        <el-table-column label="视频要求" width="120">
          <template #default="{ row }">
            {{ row.min_videos }} - {{ row.max_videos }} 个
          </template>
        </el-table-column>

        <el-table-column label="继承设置" width="100">
          <template #default="{ row }">
            <el-tag :type="row.is_inherited ? 'success' : 'info'">
              {{ row.is_inherited ? '继承上级' : '独立配置' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="有效配置" width="120">
          <template #default="{ row }">
            <div v-if="row.effective_config">
              {{ row.effective_config.min_images }} - {{ row.effective_config.max_images }} 张<br>
              {{ row.effective_config.min_videos }} - {{ row.effective_config.max_videos }} 个
              <el-tag 
                v-if="row.effective_config.is_inherited" 
                type="warning" 
                size="small"
              >
                继承
              </el-tag>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="creator.name" label="创建人" width="100" />
        <el-table-column prop="created_at" label="创建时间" width="150" />

        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button 
              type="primary" 
              size="small" 
              @click="showEditDialog(row)"
              v-if="canEdit(row)"
            >
              编辑
            </el-button>
            <el-button 
              type="danger" 
              size="small" 
              @click="handleDelete(row)"
              v-if="canDelete(row)"
            >
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-section">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 创建/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="600px"
      @close="resetForm"
    >
      <el-form
        ref="formRef"
        :model="form"
        :rules="formRules"
        label-width="120px"
      >
        <el-form-item label="组织类型" prop="organization_type" v-if="!isEdit">
          <el-select 
            v-model="form.organization_type" 
            placeholder="请选择组织类型"
            @change="handleOrganizationTypeChange"
          >
            <el-option
              v-for="item in availableOrganizationTypes"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="组织" prop="organization_id" v-if="!isEdit">
          <el-select 
            v-model="form.organization_id" 
            placeholder="请选择组织"
            :loading="organizationOptionsLoading"
          >
            <el-option
              v-for="item in organizationOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="实验类型" prop="experiment_type" v-if="!isEdit">
          <el-select v-model="form.experiment_type" placeholder="请选择实验类型">
            <el-option
              v-for="item in experimentTypes"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="图片数量要求">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item prop="min_images">
                <el-input-number 
                  v-model="form.min_images" 
                  :min="0" 
                  :max="20"
                  placeholder="最少"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="max_images">
                <el-input-number 
                  v-model="form.max_images" 
                  :min="0" 
                  :max="20"
                  placeholder="最多"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form-item>

        <el-form-item label="视频数量要求">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item prop="min_videos">
                <el-input-number 
                  v-model="form.min_videos" 
                  :min="0" 
                  :max="10"
                  placeholder="最少"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="max_videos">
                <el-input-number 
                  v-model="form.max_videos" 
                  :min="0" 
                  :max="10"
                  placeholder="最多"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form-item>

        <el-form-item label="继承设置" prop="is_inherited">
          <el-switch
            v-model="form.is_inherited"
            active-text="继承上级配置"
            inactive-text="独立配置"
          />
        </el-form-item>

        <el-form-item label="配置说明" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="请输入配置说明"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'
import {
  experimentRequirementsConfigApi,
  ORGANIZATION_TYPES,
  EXPERIMENT_TYPES,
  validateConfigData,
  getOrganizationTypeName,
  type ExperimentRequirementsConfig,
  type CreateExperimentRequirementsConfigData,
  type UpdateExperimentRequirementsConfigData,
  type OrganizationOption
} from '@/api/experimentRequirementsConfig'

// 响应式数据
const authStore = useAuthStore()
const loading = ref(false)
const submitting = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const currentConfig = ref<ExperimentRequirementsConfig | null>(null)
const organizationOptionsLoading = ref(false)

// 配置列表
const configs = ref<ExperimentRequirementsConfig[]>([])

// 分页信息
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0,
  last_page: 1
})

// 筛选表单
const filterForm = reactive({
  organization_type: '',
  experiment_type: ''
})

// 表单数据
const form = reactive<CreateExperimentRequirementsConfigData>({
  organization_type: 'province',
  organization_id: 0,
  experiment_type: '分组实验',
  min_images: 1,
  max_images: 5,
  min_videos: 0,
  max_videos: 1,
  is_inherited: true,
  description: ''
})

// 组织选项
const organizationOptions = ref<OrganizationOption[]>([])

// 表单引用
const formRef = ref()

// 计算属性
const dialogTitle = computed(() => isEdit.value ? '编辑配置' : '新增配置')

const canCreate = computed(() => {
  // 只有省、市、区县级管理员可以创建配置
  return authStore.user?.organization_level <= 3
})

const availableOrganizationTypes = computed(() => {
  const userLevel = authStore.user?.organization_level || 5
  return ORGANIZATION_TYPES.filter(type => {
    const levelMap: Record<string, number> = {
      'province': 1,
      'city': 2,
      'county': 3
    }
    return userLevel <= levelMap[type.value]
  })
})

const organizationTypes = ORGANIZATION_TYPES
const experimentTypes = EXPERIMENT_TYPES

// 表单验证规则
const formRules = {
  organization_type: [
    { required: true, message: '请选择组织类型', trigger: 'change' }
  ],
  organization_id: [
    { required: true, message: '请选择组织', trigger: 'change' }
  ],
  experiment_type: [
    { required: true, message: '请选择实验类型', trigger: 'change' }
  ],
  min_images: [
    { required: true, message: '请输入最少图片数量', trigger: 'blur' },
    { type: 'number', min: 0, max: 20, message: '图片数量范围为0-20', trigger: 'blur' }
  ],
  max_images: [
    { required: true, message: '请输入最多图片数量', trigger: 'blur' },
    { type: 'number', min: 0, max: 20, message: '图片数量范围为0-20', trigger: 'blur' }
  ],
  min_videos: [
    { required: true, message: '请输入最少视频数量', trigger: 'blur' },
    { type: 'number', min: 0, max: 10, message: '视频数量范围为0-10', trigger: 'blur' }
  ],
  max_videos: [
    { required: true, message: '请输入最多视频数量', trigger: 'blur' },
    { type: 'number', min: 0, max: 10, message: '视频数量范围为0-10', trigger: 'blur' }
  ]
}

// 方法
const loadConfigs = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      organization_type: filterForm.organization_type || undefined,
      experiment_type: filterForm.experiment_type || undefined
    }

    const response = await experimentRequirementsConfigApi.getList(params)
    if (response.success) {
      configs.value = response.data.data.map(config => ({
        ...config,
        organization_type_name: getOrganizationTypeName(config.organization_type)
      }))

      pagination.current_page = response.data.current_page
      pagination.last_page = response.data.last_page
      pagination.per_page = response.data.per_page
      pagination.total = response.data.total
    }
  } catch (error) {
    console.error('加载配置列表失败:', error)
    ElMessage.error('加载配置列表失败')
  } finally {
    loading.value = false
  }
}

const handleFilterChange = () => {
  pagination.current_page = 1
  loadConfigs()
}

const resetFilter = () => {
  filterForm.organization_type = ''
  filterForm.experiment_type = ''
  pagination.current_page = 1
  loadConfigs()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadConfigs()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadConfigs()
}

const showCreateDialog = () => {
  isEdit.value = false
  currentConfig.value = null
  resetForm()
  dialogVisible.value = true
}

const showEditDialog = (config: ExperimentRequirementsConfig) => {
  isEdit.value = true
  currentConfig.value = config

  // 填充表单数据
  form.organization_type = config.organization_type
  form.organization_id = config.organization_id
  form.experiment_type = config.experiment_type
  form.min_images = config.min_images
  form.max_images = config.max_images
  form.min_videos = config.min_videos
  form.max_videos = config.max_videos
  form.is_inherited = config.is_inherited
  form.description = config.description || ''

  dialogVisible.value = true
}

const resetForm = () => {
  if (formRef.value) {
    formRef.value.resetFields()
  }

  form.organization_type = 'province'
  form.organization_id = 0
  form.experiment_type = '分组实验'
  form.min_images = 1
  form.max_images = 5
  form.min_videos = 0
  form.max_videos = 1
  form.is_inherited = true
  form.description = ''

  organizationOptions.value = []
}

const handleOrganizationTypeChange = async () => {
  form.organization_id = 0
  organizationOptions.value = []

  if (form.organization_type) {
    await loadOrganizationOptions()
  }
}

const loadOrganizationOptions = async () => {
  organizationOptionsLoading.value = true
  try {
    const response = await experimentRequirementsConfigApi.getOrganizationOptions(form.organization_type)
    if (response.success) {
      organizationOptions.value = response.data
    }
  } catch (error) {
    console.error('加载组织选项失败:', error)
    ElMessage.error('加载组织选项失败')
  } finally {
    organizationOptionsLoading.value = false
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()

    // 验证数据
    const errors = validateConfigData(form)
    if (errors.length > 0) {
      ElMessage.error(errors[0])
      return
    }

    submitting.value = true

    if (isEdit.value && currentConfig.value) {
      // 更新配置
      const updateData: UpdateExperimentRequirementsConfigData = {
        min_images: form.min_images,
        max_images: form.max_images,
        min_videos: form.min_videos,
        max_videos: form.max_videos,
        is_inherited: form.is_inherited,
        description: form.description
      }

      const response = await experimentRequirementsConfigApi.update(currentConfig.value.id, updateData)
      if (response.success) {
        ElMessage.success('更新配置成功')
        dialogVisible.value = false
        loadConfigs()
      }
    } else {
      // 创建配置
      const response = await experimentRequirementsConfigApi.create(form)
      if (response.success) {
        ElMessage.success('创建配置成功')
        dialogVisible.value = false
        loadConfigs()
      }
    }
  } catch (error: any) {
    console.error('提交失败:', error)
    if (error.response?.data?.message) {
      ElMessage.error(error.response.data.message)
    } else {
      ElMessage.error('操作失败')
    }
  } finally {
    submitting.value = false
  }
}

const handleDelete = async (config: ExperimentRequirementsConfig) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除 ${getOrganizationTypeName(config.organization_type)} 的 ${config.experiment_type} 配置吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    const response = await experimentRequirementsConfigApi.delete(config.id)
    if (response.success) {
      ElMessage.success('删除成功')
      loadConfigs()
    }
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('删除失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

const canEdit = (config: ExperimentRequirementsConfig): boolean => {
  const userLevel = authStore.user?.organization_level || 5
  const userOrgId = authStore.user?.organization_id || 0

  const levelMap: Record<string, number> = {
    'province': 1,
    'city': 2,
    'county': 3
  }

  const configLevel = levelMap[config.organization_type]

  // 用户级别必须小于等于配置级别
  if (userLevel > configLevel) {
    return false
  }

  // 如果是同级，必须是同一个组织
  if (userLevel === configLevel) {
    return userOrgId === config.organization_id
  }

  // 上级可以编辑下级的配置
  return true
}

const canDelete = (config: ExperimentRequirementsConfig): boolean => {
  return canEdit(config)
}

// 生命周期
onMounted(() => {
  loadConfigs()
})
</script>

<style scoped>
.experiment-requirements-config {
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-section {
  margin-bottom: 20px;
  padding: 20px;
  background-color: #f5f7fa;
  border-radius: 4px;
}

.pagination-section {
  margin-top: 20px;
  text-align: right;
}

.el-table {
  margin-bottom: 20px;
}

.el-form-item {
  margin-bottom: 20px;
}

.el-input-number {
  width: 100%;
}

.el-tag {
  margin-left: 5px;
}
</style>
