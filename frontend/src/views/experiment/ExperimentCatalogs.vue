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
          <el-form-item label="管理级别">
            <el-select
              v-model="searchForm.management_level"
              placeholder="请选择级别"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="level in visibleManagementLevels"
                :key="level.value"
                :label="level.label"
                :value="level.value"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="学科">
            <el-select
              v-model="searchForm.subject_id"
              placeholder="请选择学科"
              clearable
              style="width: 150px"
              @change="handleSubjectChange"
            >
              <el-option
                v-for="subject in subjects"
                :key="subject.id"
                :label="subject.name"
                :value="subject.id"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="教材版本">
            <el-select
              v-model="searchForm.textbook_version_id"
              placeholder="请选择版本"
              clearable
              style="width: 150px"
              @change="handleVersionChange"
            >
              <el-option
                v-for="version in textbookVersions"
                :key="version.id"
                :label="version.name"
                :value="version.id"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="年级">
            <el-select
              v-model="searchForm.grade_level"
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

          <el-form-item label="册次">
            <el-select
              v-model="searchForm.volume"
              placeholder="请选择册次"
              clearable
              style="width: 120px"
            >
              <el-option label="上册" value="上册" />
              <el-option label="下册" value="下册" />
              <el-option label="全册" value="全册" />
            </el-select>
          </el-form-item>

          <el-form-item label="章节">
            <el-select
              v-model="searchForm.chapter_id"
              placeholder="请选择章节"
              clearable
              style="width: 200px"
              filterable
            >
              <el-option
                v-for="chapter in chapters"
                :key="chapter.id"
                :label="`${chapter.code} ${chapter.name}`"
                :value="chapter.id"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="实验类型">
            <el-select
              v-model="searchForm.experiment_type"
              placeholder="请选择类型"
              clearable
              style="width: 120px"
            >
              <el-option label="必做" value="必做" />
              <el-option label="选做" value="选做" />
              <el-option label="演示" value="演示" />
              <el-option label="分组" value="分组" />
            </el-select>
          </el-form-item>

          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="实验名称/编号"
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
          <el-button
            v-if="canCreateCatalog()"
            type="primary"
            @click="handleCreate"
          >
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
        <el-table-column prop="management_level" label="级别" width="80" sortable="custom">
          <template #default="{ row }">
            <el-tag :type="getManagementLevelTagType(row.management_level)" size="small">
              {{ getManagementLevelLabel(row.management_level) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="code" label="实验编号" width="120" sortable="custom" />
        <el-table-column prop="name" label="实验名称" min-width="200" show-overflow-tooltip />
        <el-table-column prop="subject.name" label="学科" width="100" />
        <el-table-column prop="textbook_version.name" label="教材版本" width="120" show-overflow-tooltip />
        <el-table-column prop="grade_level" label="年级" width="80" sortable="custom" />
        <el-table-column prop="volume" label="册次" width="80" />
        <el-table-column prop="chapter_info.name" label="章节" width="150" show-overflow-tooltip>
          <template #default="{ row }">
            <span v-if="row.chapter_info">
              {{ row.chapter_info.code }} {{ row.chapter_info.name }}
            </span>
            <span v-else class="text-gray">-</span>
          </template>
        </el-table-column>
        <el-table-column prop="experiment_type" label="实验类型" width="100">
          <template #default="{ row }">
            <el-tag :type="getExperimentTypeTagType(row.experiment_type)" size="small">
              {{ row.experiment_type }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="version" label="版本" width="80" sortable="custom">
          <template #default="{ row }">
            v{{ row.version }}
          </template>
        </el-table-column>
        <el-table-column prop="is_deleted_by_lower" label="删除状态" width="100">
          <template #default="{ row }">
            <el-tag v-if="row.is_deleted_by_lower" type="warning" size="small">
              已删除
            </el-tag>
            <el-tag v-else type="success" size="small">
              正常
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.status ? 'success' : 'danger'" size="small">
              {{ row.status ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="240" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link @click="handleView(row)">
              详情
            </el-button>
            <el-button
              v-if="canEditCatalog(row)"
              type="primary"
              link
              @click="handleEdit(row)"
            >
              编辑
            </el-button>
            <el-button
              v-if="canCopyCatalog(row)"
              type="success"
              link
              @click="handleCopy(row)"
            >
              复制
            </el-button>
            <el-button
              v-if="canDeleteCatalog(row)"
              type="danger"
              link
              @click="handleDelete(row)"
            >
              {{ getDeleteButtonText(row) }}
            </el-button>
            <el-button
              v-if="canRestoreCatalog(row)"
              type="warning"
              link
              @click="handleRestore(row)"
            >
              恢复
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
  getTextbookVersionsApi,
  getTextbookVersionOptionsApi,
  getTextbookChaptersApi,
  type ExperimentCatalog,
  type Subject,
  type TextbookVersion,
  type TextbookChapter
} from '@/api/experiment'

import ExperimentCatalogDialog from './components/ExperimentCatalogDialog.vue'
import ExperimentCatalogDetail from './components/ExperimentCatalogDetail.vue'
import BatchImportDialog from './components/BatchImportDialog.vue'
import { useExperimentPermission } from '@/composables/useExperimentPermission'

// 权限控制
const {
  canCreateCatalog,
  canEditCatalog,
  canDeleteCatalog,
  canCopyCatalog,
  canRestoreCatalog,
  visibleManagementLevels,
  getManagementLevelLabel,
  getManagementLevelTagType,
  getDeleteConfirmText,
  getDeleteButtonText,
  getCatalogStatusTag,
  filterCatalogsByPermission
} = useExperimentPermission()



// 响应式数据
const loading = ref(false)
const tableData = ref<ExperimentCatalog[]>([])
const subjects = ref<Subject[]>([])
const textbookVersions = ref<TextbookVersion[]>([])
const chapters = ref<TextbookChapter[]>([])

// 搜索表单
const searchForm = reactive({
  management_level: undefined as number | undefined,
  subject_id: undefined as number | undefined,
  textbook_version_id: undefined as number | undefined,
  chapter_id: undefined as number | undefined,
  grade_level: undefined as string | undefined,
  volume: undefined as string | undefined,
  experiment_type: undefined as string | undefined,
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
const gradeOptions = [
  { label: '一年级', value: '1' },
  { label: '二年级', value: '2' },
  { label: '三年级', value: '3' },
  { label: '四年级', value: '4' },
  { label: '五年级', value: '5' },
  { label: '六年级', value: '6' },
  { label: '七年级', value: '7' },
  { label: '八年级', value: '8' },
  { label: '九年级', value: '9' },
  { label: '高一', value: '10' },
  { label: '高二', value: '11' },
  { label: '高三', value: '12' }
]

// 获取实验类型标签类型
const getExperimentTypeTagType = (type: string) => {
  const typeMap: Record<string, string> = {
    '必做': 'danger',
    '选做': 'warning',
    '演示': 'primary',
    '分组': 'success'
  }
  return typeMap[type] || 'info'
}

// 加载基础数据
const loadBaseData = async () => {
  try {
    const [subjectsRes, versionsRes] = await Promise.all([
      getSubjectsApi(),
      getTextbookVersionOptionsApi()
    ])

    subjects.value = Array.isArray(subjectsRes.data.data) ? subjectsRes.data.data :
                     Array.isArray(subjectsRes.data) ? subjectsRes.data : []
    textbookVersions.value = Array.isArray(versionsRes.data.data) ? versionsRes.data.data :
                             Array.isArray(versionsRes.data) ? versionsRes.data : []
  } catch (error) {
    console.error('加载基础数据失败:', error)
    ElMessage.error('加载基础数据失败')
  }
}

// 学科变化时加载章节
const handleSubjectChange = () => {
  searchForm.textbook_version_id = undefined
  searchForm.chapter_id = undefined
  chapters.value = []
  loadChapters()
}

// 版本变化时加载章节
const handleVersionChange = () => {
  searchForm.chapter_id = undefined
  loadChapters()
}

// 加载章节数据
const loadChapters = async () => {
  if (!searchForm.subject_id || !searchForm.textbook_version_id) {
    chapters.value = []
    return
  }

  try {
    const params = {
      subject_id: searchForm.subject_id,
      textbook_version_id: searchForm.textbook_version_id,
      grade_level: searchForm.grade_level,
      volume: searchForm.volume
    }

    const response = await getTextbookChaptersApi(params)
    chapters.value = Array.isArray(response.data.data) ? response.data.data :
                     Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('加载章节数据失败:', error)
  }
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

    // 处理不同的响应格式
    if (response.data.data && response.data.data.data) {
      // 新格式：{code, message, data: {current_page, data: [...]}}
      tableData.value = response.data.data.data
      Object.assign(pagination, response.data.data)
    } else if (response.data.data) {
      // 旧格式：{success, data: [...]}
      tableData.value = response.data.data
      Object.assign(pagination, response.data)
    } else {
      tableData.value = []
    }
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
    const confirmText = getDeleteConfirmText(catalog)

    await ElMessageBox.confirm(confirmText, '确认删除', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    await deleteExperimentCatalogApi(catalog.id)
    ElMessage.success('操作成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除实验目录失败:', error)
      ElMessage.error('操作失败')
    }
  }
}

// 复制实验目录
const handleCopy = async (catalog: ExperimentCatalog) => {
  try {
    // 这里应该打开复制对话框，预填充数据
    currentCatalog.value = { ...catalog }
    dialogMode.value = 'create'
    dialogVisible.value = true
    ElMessage.info('已复制实验目录，请修改相关信息后保存')
  } catch (error) {
    console.error('复制实验目录失败:', error)
    ElMessage.error('复制失败')
  }
}

// 恢复被删除的实验目录
const handleRestore = async (catalog: ExperimentCatalog) => {
  try {
    await ElMessageBox.confirm(
      `确定要恢复实验"${catalog.name}"吗？`,
      '确认恢复',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'success'
      }
    )

    // 这里应该调用恢复API
    ElMessage.success('恢复成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('恢复实验目录失败:', error)
      ElMessage.error('恢复失败')
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
  loadBaseData()
  loadData()
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

.text-gray {
  color: #909399;
}

:deep(.el-table .el-table__cell) {
  padding: 8px 0;
}

:deep(.el-tag--small) {
  height: 20px;
  line-height: 18px;
  font-size: 11px;
}
</style>
