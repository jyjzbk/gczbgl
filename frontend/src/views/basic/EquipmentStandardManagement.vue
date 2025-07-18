<template>
  <div class="equipment-standard-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>教学仪器配备标准</h2>
        <p>管理教育部和教育厅制定的各学段学科仪器配备标准</p>
      </div>
      <div class="header-actions">
        <PermissionTooltip permission="equipment_standard.create">
          <el-button type="primary" :icon="Plus" @click="handleCreate">
            新增标准
          </el-button>
        </PermissionTooltip>
        <el-button :icon="Refresh" @click="loadData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 搜索区域 -->
    <div class="search-section">
      <el-form :model="searchForm" inline>
        <el-form-item label="制定机构">
          <el-select
            v-model="searchForm.authority_type"
            placeholder="请选择制定机构"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="option in authorityOptions"
              :key="option.value"
              :label="option.label"
              :value="option.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学段">
          <el-select
            v-model="searchForm.stage"
            placeholder="请选择学段"
            clearable
            style="width: 100px"
          >
            <el-option
              v-for="option in stageOptions"
              :key="option.value"
              :label="option.label"
              :value="option.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学科">
          <el-select
            v-model="searchForm.subject_code"
            placeholder="请选择学科"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="subject in subjects"
              :key="subject.code"
              :label="subject.name"
              :value="subject.code"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="标准名称">
          <el-input
            v-model="searchForm.search"
            placeholder="请输入标准名称"
            clearable
            style="width: 200px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">
            搜索
          </el-button>
          <el-button :icon="RefreshLeft" @click="handleReset">
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 数据表格 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        style="width: 100%"
      >
        <el-table-column type="index" label="序号" width="60" align="center" />
        
        <el-table-column prop="name" label="标准名称" min-width="200" show-overflow-tooltip />
        
        <el-table-column prop="authority_type_text" label="制定机构" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.authority_type === 1 ? 'danger' : 'warning'">
              {{ row.authority_type_text }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column prop="stage_text" label="学段" width="80" align="center" />
        
        <el-table-column prop="subject_name" label="学科" width="100" align="center" />
        
        <el-table-column prop="version" label="版本" width="80" align="center" />
        
        <el-table-column prop="effective_date" label="生效日期" width="120" align="center" />
        
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status_text }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column label="操作" width="250" align="center" fixed="right">
          <template #default="{ row }">
            <el-button
              type="info"
              size="small"
              :icon="View"
              @click="handleView(row)"
            >
              查看
            </el-button>
            <PermissionTooltip permission="equipment_standard.update">
              <el-button
                type="primary"
                size="small"
                :icon="Edit"
                @click="handleEdit(row)"
              >
                编辑
              </el-button>
            </PermissionTooltip>
            <PermissionTooltip permission="equipment_standard.delete">
              <el-button
                type="danger"
                size="small"
                :icon="Delete"
                @click="handleDelete(row)"
              >
                删除
              </el-button>
            </PermissionTooltip>
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
    </div>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="detailVisible"
      title="配备标准详情"
      width="80%"
      destroy-on-close
    >
      <div v-if="currentStandard" class="standard-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="标准名称">{{ currentStandard.name }}</el-descriptions-item>
          <el-descriptions-item label="标准代码">{{ currentStandard.code }}</el-descriptions-item>
          <el-descriptions-item label="制定机构">{{ currentStandard.authority_type_text }}</el-descriptions-item>
          <el-descriptions-item label="学段">{{ currentStandard.stage_text }}</el-descriptions-item>
          <el-descriptions-item label="学科">{{ currentStandard.subject_name }}</el-descriptions-item>
          <el-descriptions-item label="版本">{{ currentStandard.version }}</el-descriptions-item>
          <el-descriptions-item label="生效日期">{{ currentStandard.effective_date }}</el-descriptions-item>
          <el-descriptions-item label="失效日期">{{ currentStandard.expiry_date || '无' }}</el-descriptions-item>
          <el-descriptions-item label="描述" :span="2">{{ currentStandard.description || '无' }}</el-descriptions-item>
        </el-descriptions>
        
        <div class="equipment-list-section">
          <h3>设备清单</h3>
          <div v-for="(category, index) in currentStandard.equipment_list" :key="index" class="category-section">
            <h4>{{ category.category }}</h4>
            <el-table :data="category.items" border>
              <el-table-column prop="name" label="设备名称" />
              <el-table-column prop="specification" label="规格型号" />
              <el-table-column prop="quantity" label="数量" width="80" align="center" />
              <el-table-column prop="unit" label="单位" width="80" align="center" />
            </el-table>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, RefreshLeft, Refresh, View, Edit, Delete } from '@element-plus/icons-vue'
import {
  getEquipmentStandardsApi,
  deleteEquipmentStandardApi,
  getSubjectsApi,
  type EquipmentStandard,
  type Subject,
  AUTHORITY_TYPE_OPTIONS,
  STAGE_OPTIONS
} from '@/api/equipmentStandard'
import PermissionTooltip from '@/components/PermissionTooltip.vue'

// 响应式数据
const loading = ref(false)
const tableData = ref<EquipmentStandard[]>([])
const detailVisible = ref(false)
const currentStandard = ref<EquipmentStandard | null>(null)
const subjects = ref<Subject[]>([])

// 搜索表单
const searchForm = reactive({
  authority_type: undefined as number | undefined,
  stage: undefined as number | undefined,
  subject_code: '',
  search: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 15,
  total: 0
})

// 选项数据
const authorityOptions = AUTHORITY_TYPE_OPTIONS
const stageOptions = STAGE_OPTIONS

// 加载学科数据
const loadSubjects = async () => {
  try {
    const response = await getSubjectsApi()
    subjects.value = response.data
  } catch (error) {
    console.error('加载学科失败:', error)
  }
}

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm
    }
    
    const response = await getEquipmentStandardsApi(params)
    tableData.value = response.data.data
    Object.assign(pagination, response.data)
  } catch (error) {
    console.error('加载配备标准失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  loadData()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    authority_type: undefined,
    stage: undefined,
    subject_code: '',
    search: ''
  })
  pagination.current_page = 1
  loadData()
}

// 分页变化
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadData()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadData()
}

// 查看详情
const handleView = (row: EquipmentStandard) => {
  currentStandard.value = row
  detailVisible.value = true
}

// 新增
const handleCreate = () => {
  ElMessage.info('新增功能开发中...')
}

// 编辑
const handleEdit = (row: EquipmentStandard) => {
  ElMessage.info('编辑功能开发中...')
}

// 删除
const handleDelete = async (row: EquipmentStandard) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除配备标准"${row.name}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteEquipmentStandardApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('删除失败:', error)
      ElMessage.error(error.response?.data?.message || '删除失败')
    }
  }
}

// 初始化
onMounted(() => {
  loadSubjects()
  loadData()
})
</script>

<style scoped>
.equipment-standard-management {
  padding: 20px;
  background: #f5f7fa;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #606266;
  font-size: 14px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.search-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-section {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.pagination-section {
  padding: 20px;
  display: flex;
  justify-content: center;
}

.standard-detail {
  padding: 20px 0;
}

.equipment-list-section {
  margin-top: 30px;
}

.equipment-list-section h3 {
  margin: 0 0 20px 0;
  color: #303133;
  font-size: 18px;
  font-weight: 600;
  border-bottom: 2px solid #409EFF;
  padding-bottom: 8px;
}

.category-section {
  margin-bottom: 30px;
}

.category-section h4 {
  margin: 0 0 15px 0;
  color: #606266;
  font-size: 16px;
  font-weight: 500;
}
</style>
