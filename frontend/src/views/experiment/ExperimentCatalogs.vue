<template>
  <div class="experiment-catalogs">
    <div class="page-card">
      <div class="page-header">
        <h2>实验目录管理</h2>
        <p>管理实验教学目录，包括实验内容、要求和标准</p>
      </div>
      
      <!-- 搜索和筛选 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="学科">
            <el-select
              v-model="searchForm.subject_id"
              placeholder="请选择学科"
              clearable
              style="width: 150px"
            >
              <el-option
                v-for="subject in subjects"
                :key="subject.id"
                :label="subject.name"
                :value="subject.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="年级">
            <el-select
              v-model="searchForm.grade"
              placeholder="请选择年级"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="grade in gradeOptions"
                :key="grade.value"
                :label="grade.label"
                :value="grade.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="学期">
            <el-select
              v-model="searchForm.semester"
              placeholder="请选择学期"
              clearable
              style="width: 120px"
            >
              <el-option label="上学期" :value="1" />
              <el-option label="下学期" :value="2" />
            </el-select>
          </el-form-item>
          
          <el-form-item label="类型">
            <el-select
              v-model="searchForm.type"
              placeholder="请选择类型"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="type in typeOptions"
                :key="type.value"
                :label="type.label"
                :value="type.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="实验名称/编号/章节"
              clearable
              style="width: 200px"
              @keyup.enter="handleSearch"
            />
          </el-form-item>
          
          <el-form-item>
            <el-button type="primary" @click="handleSearch">
              <el-icon><Search /></el-icon>
              搜索
            </el-button>
            <el-button @click="handleReset">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      
      <!-- 工具栏 -->
      <div class="table-toolbar">
        <div class="toolbar-left">
          <el-button type="primary" @click="handleCreate">
            <el-icon><Plus /></el-icon>
            新增实验
          </el-button>
          <el-button @click="handleBatchImport">
            <el-icon><Upload /></el-icon>
            批量导入
          </el-button>
          <el-button @click="handleExport">
            <el-icon><Download /></el-icon>
            导出数据
          </el-button>
        </div>
        
        <div class="toolbar-right">
          <el-tooltip content="刷新数据">
            <el-button circle @click="loadData">
              <el-icon><Refresh /></el-icon>
            </el-button>
          </el-tooltip>
        </div>
      </div>
      
      <!-- 数据表格 -->
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        @sort-change="handleSortChange"
      >
        <el-table-column prop="code" label="实验编号" width="120" sortable="custom" />
        <el-table-column prop="name" label="实验名称" min-width="200" show-overflow-tooltip />
        <el-table-column prop="subject.name" label="学科" width="100" />
        <el-table-column prop="grade" label="年级" width="80" sortable="custom">
          <template #default="{ row }">
            {{ row.grade }}年级
          </template>
        </el-table-column>
        <el-table-column prop="semester" label="学期" width="80">
          <template #default="{ row }">
            {{ row.semester === 1 ? '上学期' : '下学期' }}
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" width="100">
          <template #default="{ row }">
            <el-tag :type="getTypeTagType(row.type)">
              {{ getTypeLabel(row.type) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="duration" label="时长" width="80">
          <template #default="{ row }">
            {{ row.duration }}分钟
          </template>
        </el-table-column>
        <el-table-column prop="student_count" label="人数" width="80" />
        <el-table-column prop="difficulty_level" label="难度" width="80">
          <template #default="{ row }">
            <el-rate
              v-model="row.difficulty_level"
              disabled
              show-score
              text-color="#ff9900"
              score-template="{value}"
            />
          </template>
        </el-table-column>
        <el-table-column prop="is_standard" label="标准实验" width="100">
          <template #default="{ row }">
            <el-tag :type="row.is_standard ? 'success' : 'info'">
              {{ row.is_standard ? '是' : '否' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.status ? 'success' : 'danger'">
              {{ row.status ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link @click="handleView(row)">
              查看
            </el-button>
            <el-button type="primary" link @click="handleEdit(row)">
              编辑
            </el-button>
            <el-button type="danger" link @click="handleDelete(row)">
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      
      <!-- 分页 -->
      <div class="table-pagination">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :total="pagination.total"
          :page-sizes="[10, 15, 20, 50]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
    
    <!-- 实验目录表单对话框 -->
    <ExperimentCatalogDialog
      v-model="dialogVisible"
      :catalog="currentCatalog"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 实验目录详情对话框 -->
    <ExperimentCatalogDetail
      v-model="detailVisible"
      :catalog="currentCatalog"
    />
    
    <!-- 批量导入对话框 -->
    <BatchImportDialog
      v-model="importVisible"
      @success="handleImportSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Plus, Upload, Download } from '@element-plus/icons-vue'
import {
  getExperimentCatalogsApi,
  deleteExperimentCatalogApi,
  getSubjectsApi,
  type ExperimentCatalog,
  type Subject
} from '@/api/experiment'
import ExperimentCatalogDialog from './components/ExperimentCatalogDialog.vue'
import ExperimentCatalogDetail from './components/ExperimentCatalogDetail.vue'
import BatchImportDialog from './components/BatchImportDialog.vue'

// 响应式数据
const loading = ref(false)
const tableData = ref<ExperimentCatalog[]>([])
const subjects = ref<Subject[]>([])

// 搜索表单
const searchForm = reactive({
  subject_id: undefined as number | undefined,
  grade: undefined as number | undefined,
  semester: undefined as number | undefined,
  type: undefined as number | undefined,
  search: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 15,
  total: 0,
  last_page: 1
})

// 排序数据
const sortData = reactive({
  sort_field: 'grade',
  sort_order: 'asc' as 'asc' | 'desc'
})

// 对话框状态
const dialogVisible = ref(false)
const detailVisible = ref(false)
const importVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const currentCatalog = ref<ExperimentCatalog | null>(null)

// 选项数据
const gradeOptions = computed(() => {
  const options = []
  for (let i = 1; i <= 12; i++) {
    options.push({ label: `${i}年级`, value: i })
  }
  return options
})

const typeOptions = [
  { label: '演示实验', value: 1 },
  { label: '分组实验', value: 2 },
  { label: '探究实验', value: 3 },
  { label: '综合实验', value: 4 }
]

// 获取类型标签类型
const getTypeTagType = (type: number) => {
  const typeMap: Record<number, string> = {
    1: 'info',
    2: 'success',
    3: 'warning',
    4: 'danger'
  }
  return typeMap[type] || 'info'
}

// 获取类型标签文本
const getTypeLabel = (type: number) => {
  const typeMap: Record<number, string> = {
    1: '演示实验',
    2: '分组实验',
    3: '探究实验',
    4: '综合实验'
  }
  return typeMap[type] || '未知'
}

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm,
      ...sortData
    }
    
    const response = await getExperimentCatalogsApi(params)
    tableData.value = response.data.data
    
    // 更新分页信息
    Object.assign(pagination, response.data)
  } catch (error) {
    console.error('加载实验目录失败:', error)
  } finally {
    loading.value = false
  }
}

// 加载学科列表
const loadSubjects = async () => {
  try {
    const response = await getSubjectsApi()
    subjects.value = response.data
  } catch (error) {
    console.error('加载学科列表失败:', error)
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
    subject_id: undefined,
    grade: undefined,
    semester: undefined,
    type: undefined,
    search: ''
  })
  handleSearch()
}

// 排序变化
const handleSortChange = ({ prop, order }: { prop: string; order: string | null }) => {
  if (order) {
    sortData.sort_field = prop
    sortData.sort_order = order === 'ascending' ? 'asc' : 'desc'
  } else {
    sortData.sort_field = 'grade'
    sortData.sort_order = 'asc'
  }
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

// 新增
const handleCreate = () => {
  currentCatalog.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑
const handleEdit = (catalog: ExperimentCatalog) => {
  currentCatalog.value = catalog
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看详情
const handleView = (catalog: ExperimentCatalog) => {
  currentCatalog.value = catalog
  detailVisible.value = true
}

// 删除
const handleDelete = async (catalog: ExperimentCatalog) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除实验"${catalog.name}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteExperimentCatalogApi(catalog.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除实验目录失败:', error)
    }
  }
}

// 批量导入
const handleBatchImport = () => {
  importVisible.value = true
}

// 导出数据
const handleExport = () => {
  ElMessage.info('导出功能开发中...')
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 导入成功回调
const handleImportSuccess = () => {
  loadData()
}

onMounted(() => {
  loadData()
  loadSubjects()
})
</script>

<style scoped>
.experiment-catalogs {
  padding: 0;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  font-size: 20px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 8px;
}

.page-header p {
  font-size: 14px;
  color: #909399;
  margin: 0;
}

.table-search {
  background: #fff;
  padding: 20px;
  margin-bottom: 16px;
  border-radius: 8px;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
}

.table-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.toolbar-left {
  display: flex;
  gap: 8px;
}

.toolbar-right {
  display: flex;
  gap: 8px;
}

.table-pagination {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

@media (max-width: 768px) {
  .table-search :deep(.el-form) {
    flex-direction: column;
  }
  
  .table-search :deep(.el-form-item) {
    margin-right: 0;
    margin-bottom: 16px;
  }
  
  .table-toolbar {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }
}
</style>
