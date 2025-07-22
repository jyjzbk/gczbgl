<template>
  <div class="textbook-versions">
    <div class="page-card">
      <div class="page-header">
        <h2>教材版本管理</h2>
        <p>管理教材版本信息，为实验目录提供版本分类支持</p>
      </div>
      
      <!-- 搜索和筛选 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="版本名称/代码/出版社"
              clearable
              style="width: 200px"
              @keyup.enter="handleSearch"
            />
          </el-form-item>
          
          <el-form-item label="状态">
            <el-select
              v-model="searchForm.status"
              placeholder="请选择状态"
              clearable
              style="width: 120px"
            >
              <el-option label="启用" :value="1" />
              <el-option label="禁用" :value="0" />
            </el-select>
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
            新增版本
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
        <el-table-column prop="sort_order" label="排序" width="80" sortable="custom" />
        <el-table-column prop="name" label="版本名称" min-width="150" sortable="custom" />
        <el-table-column prop="code" label="版本代码" width="120" sortable="custom" />
        <el-table-column prop="publisher" label="出版社" min-width="200" show-overflow-tooltip />
        <el-table-column prop="description" label="描述" min-width="200" show-overflow-tooltip />
        <el-table-column prop="status" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="创建时间" width="160" sortable="custom">
          <template #default="{ row }">
            {{ formatDateTime(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="160" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link @click="handleEdit(row)">
              <el-icon><Edit /></el-icon>
              编辑
            </el-button>
            <el-button type="danger" link @click="handleDelete(row)">
              <el-icon><Delete /></el-icon>
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      
      <!-- 分页 -->
      <div class="table-pagination">
        <el-pagination
          v-model:current-page="pagination.page"
          v-model:page-size="pagination.per_page"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
    
    <!-- 新增/编辑对话框 -->
    <TextbookVersionDialog
      v-model="dialogVisible"
      :form-data="formData"
      :is-edit="isEdit"
      @success="handleDialogSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Plus, Edit, Delete } from '@element-plus/icons-vue'
import { debounce, performanceMonitor } from '@/utils/performance'
import type { 
  TextbookVersion, 
  TextbookVersionListParams,
  CreateTextbookVersionParams 
} from '@/api/experiment'
import { 
  getTextbookVersionsApi, 
  deleteTextbookVersionApi 
} from '@/api/experiment'
import TextbookVersionDialog from './components/TextbookVersionDialog.vue'
import { formatDateTime } from '@/utils/date'

// 响应式数据
const loading = ref(false)
const tableData = ref<TextbookVersion[]>([])
const dialogVisible = ref(false)
const isEdit = ref(false)
const formData = ref<Partial<CreateTextbookVersionParams>>({})

// 搜索表单
const searchForm = reactive<TextbookVersionListParams>({
  search: '',
  status: undefined
})

// 分页数据
const pagination = reactive({
  page: 1,
  per_page: 20,
  total: 0
})

// 排序数据
const sortData = reactive({
  sort_field: 'sort_order',
  sort_order: 'asc' as 'asc' | 'desc'
})

// 加载数据（添加性能监控）
const loadData = async () => {
  performanceMonitor.start('loadTextbookVersions')
  loading.value = true
  try {
    const params = {
      ...searchForm,
      ...pagination,
      ...sortData
    }

    const response = await getTextbookVersionsApi(params)

    // 处理响应数据
    const responseData = response.data

    // 检查是否直接包含 items 和 pagination（axios拦截器已处理过的格式）
    if (responseData && responseData.items && Array.isArray(responseData.items)) {
      tableData.value = responseData.items
      pagination.total = responseData.pagination?.total || responseData.items.length
    }
    // 检查标准的 success + data 结构
    else if (responseData && responseData.success && responseData.data && responseData.data.items) {
      tableData.value = responseData.data.items
      pagination.total = responseData.data.pagination.total
    }
    // 检查简单数组结构
    else if (responseData && Array.isArray(responseData)) {
      tableData.value = responseData
      pagination.total = tableData.value.length
    } else {
      tableData.value = []
      pagination.total = 0
    }
  } catch (error) {
    console.error('加载教材版本数据失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
    performanceMonitor.end('loadTextbookVersions')
  }
}

// 搜索（添加防抖优化）
const handleSearch = debounce(() => {
  pagination.page = 1
  loadData()
}, 300)

// 重置
const handleReset = () => {
  Object.assign(searchForm, {
    search: '',
    status: undefined
  })
  pagination.page = 1
  loadData()
}

// 排序变化
const handleSortChange = ({ prop, order }: { prop: string; order: string | null }) => {
  if (order) {
    sortData.sort_field = prop
    sortData.sort_order = order === 'ascending' ? 'asc' : 'desc'
  } else {
    sortData.sort_field = 'sort_order'
    sortData.sort_order = 'asc'
  }
  loadData()
}

// 分页变化
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.page = 1
  loadData()
}

const handleCurrentChange = (page: number) => {
  pagination.page = page
  loadData()
}

// 新增
const handleCreate = () => {
  formData.value = {
    name: '',
    code: '',
    publisher: '',
    description: '',
    status: true,
    sort_order: 0
  }
  isEdit.value = false
  dialogVisible.value = true
}

// 编辑
const handleEdit = (row: TextbookVersion) => {
  formData.value = {
    id: row.id, // 添加ID字段
    name: row.name,
    code: row.code,
    publisher: row.publisher,
    description: row.description,
    status: row.status === 1,
    sort_order: row.sort_order
  }
  isEdit.value = true
  dialogVisible.value = true
}

// 删除
const handleDelete = async (row: TextbookVersion) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除教材版本"${row.name}"吗？`,
      '确认删除',
      {
        type: 'warning'
      }
    )
    
    await deleteTextbookVersionApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除教材版本失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 初始化
onMounted(() => {
  console.log('Component mounted, loading data...')
  loadData()
})

// 添加监听器来调试数据变化
watch(tableData, (newValue) => {
  console.log('tableData changed:', newValue)
  console.log('tableData length:', newValue.length)
}, { deep: true })
</script>

<style scoped>
.textbook-versions {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 24px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.table-search {
  margin-bottom: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.table-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.table-pagination {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
</style>
