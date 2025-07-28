<template>
  <div class="subordinate-assignment">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>下级目录指定</h2>
      <p class="page-description">为下级学校指定实验目录配置</p>
    </div>

    <!-- 筛选条件 -->
    <el-card class="filter-card" shadow="never">
      <template #header>
        <span>🔍 学校筛选</span>
      </template>
      <el-form :model="filters" inline>
        <el-form-item label="管理级别">
          <el-select v-model="filters.management_level" placeholder="选择管理级别" clearable style="width: 150px">
            <el-option label="省直学校" value="1" />
            <el-option label="市直学校" value="2" />
            <el-option label="区县直管" value="3" />
            <el-option label="学区学校" value="4" />
          </el-select>
        </el-form-item>
        <el-form-item label="配置状态">
          <el-select v-model="filters.config_status" placeholder="选择配置状态" clearable style="width: 150px">
            <el-option label="全部" value="all" />
            <el-option label="已配置" value="configured" />
            <el-option label="未配置" value="unconfigured" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="loadSchools" :loading="loading">
            <el-icon><Search /></el-icon>
            查询
          </el-button>
          <el-button @click="resetFilters">
            重置
          </el-button>
          <el-button 
            type="success" 
            @click="showBatchAssignDialog = true"
            :disabled="selectedSchools.length === 0"
          >
            批量指定
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- 学校列表 -->
    <el-card class="schools-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>📋 学校列表</span>
          <div class="header-actions">
            <span class="stats-text">
              共 {{ pagination.total }} 所学校，已配置 {{ configuredCount }} 所，未配置 {{ unconfiguredCount }} 所
            </span>
            <el-button size="small" @click="loadSchools" :loading="loading">
              <el-icon><Refresh /></el-icon>
              刷新
            </el-button>
          </div>
        </div>
      </template>

      <el-table 
        :data="schools" 
        v-loading="loading"
        @selection-change="handleSelectionChange"
        stripe
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="name" label="学校名称" min-width="200" />
        <el-table-column prop="management_level" label="管理级别" width="120">
          <template #default="{ row }">
            <el-tag :type="getManagementLevelType(row.management_level)">
              {{ getManagementLevelText(row.management_level) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="config_status" label="配置状态" width="120">
          <template #default="{ row }">
            <el-tag :type="row.config_status === 'configured' ? 'success' : 'warning'">
              {{ row.config_status === 'configured' ? '✅ 已配置' : '❌ 未配置' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="config_type_name" label="配置类型" width="120">
          <template #default="{ row }">
            <el-tag v-if="row.config_type_name" :type="row.config?.config_type === 'selection' ? 'success' : 'info'">
              {{ row.config_type_name }}
            </el-tag>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column label="目录来源" min-width="200">
          <template #default="{ row }">
            <span v-if="row.config">
              {{ row.config.source_org_name }} ({{ row.config.source_level_name }})
            </span>
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button 
              v-if="row.config_status === 'configured'" 
              size="small" 
              @click="viewConfig(row)"
            >
              查看配置
            </el-button>
            <el-button 
              size="small" 
              type="primary" 
              @click="assignConfig(row)"
            >
              {{ row.config_status === 'configured' ? '修改配置' : '立即配置' }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-wrapper">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 批量指定对话框 -->
    <BatchAssignDialog
      v-model="showBatchAssignDialog"
      :selected-schools="selectedSchools"
      @success="handleBatchAssignSuccess"
    />

    <!-- 配置对话框 -->
    <ConfigDialog
      v-model="showConfigDialog"
      :school-id="currentSchoolId"
      :current-config="currentConfig"
      :is-assignment="true"
      @success="handleConfigSuccess"
    />

    <!-- 配置详情对话框 -->
    <ConfigDetailDialog
      v-model="showDetailDialog"
      :config="currentConfig"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Refresh } from '@element-plus/icons-vue'
import { schoolCatalogConfigApi, type School, type SchoolExperimentCatalogConfig } from '@/api/schoolCatalogConfig'
import BatchAssignDialog from './components/BatchAssignDialog.vue'
import ConfigDialog from './components/ConfigDialog.vue'
import ConfigDetailDialog from './components/ConfigDetailDialog.vue'

// 响应式数据
const loading = ref(false)
const schools = ref<School[]>([])
const selectedSchools = ref<School[]>([])
const filters = ref({
  management_level: '',
  config_status: 'all'
})
const pagination = ref({
  current_page: 1,
  per_page: 20,
  total: 0
})

const showBatchAssignDialog = ref(false)
const showConfigDialog = ref(false)
const showDetailDialog = ref(false)
const currentSchoolId = ref<number>()
const currentConfig = ref<SchoolExperimentCatalogConfig>()

// 计算属性
const configuredCount = computed(() => 
  schools.value.filter(school => school.config_status === 'configured').length
)

const unconfiguredCount = computed(() => 
  schools.value.filter(school => school.config_status === 'unconfigured').length
)

// 方法
const loadSchools = async () => {
  try {
    loading.value = true
    const params = {
      ...filters.value,
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    }
    
    const response = await schoolCatalogConfigApi.getSubordinateSchools(params)
    
    if (response.data.success) {
      schools.value = response.data.data.data
      pagination.value.total = response.data.data.total
      pagination.value.current_page = response.data.data.current_page
    } else {
      ElMessage.error(response.data.message || '获取学校列表失败')
    }
  } catch (error) {
    console.error('获取学校列表失败:', error)
    ElMessage.error('获取学校列表失败')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.value = {
    management_level: '',
    config_status: 'all'
  }
  pagination.value.current_page = 1
  loadSchools()
}

const handleSelectionChange = (selection: School[]) => {
  selectedSchools.value = selection
}

const handleSizeChange = (size: number) => {
  pagination.value.per_page = size
  pagination.value.current_page = 1
  loadSchools()
}

const handleCurrentChange = (page: number) => {
  pagination.value.current_page = page
  loadSchools()
}

const viewConfig = (school: School) => {
  currentConfig.value = school.config
  showDetailDialog.value = true
}

const assignConfig = (school: School) => {
  currentSchoolId.value = school.id
  currentConfig.value = school.config
  showConfigDialog.value = true
}

const handleBatchAssignSuccess = () => {
  showBatchAssignDialog.value = false
  selectedSchools.value = []
  loadSchools()
  ElMessage.success('批量指定成功')
}

const handleConfigSuccess = () => {
  showConfigDialog.value = false
  loadSchools()
  ElMessage.success('配置保存成功')
}

const getManagementLevelType = (level: number) => {
  const types = { 1: 'danger', 2: 'warning', 3: 'info', 4: 'success' }
  return types[level as keyof typeof types] || ''
}

const getManagementLevelText = (level: number) => {
  const texts = { 1: '省直学校', 2: '市直学校', 3: '区县直管', 4: '学区学校' }
  return texts[level as keyof typeof texts] || '未知'
}

// 生命周期
onMounted(() => {
  loadSchools()
})
</script>

<style scoped>
.subordinate-assignment {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.filter-card,
.schools-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stats-text {
  font-size: 14px;
  color: #909399;
}

.pagination-wrapper {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}
</style>
