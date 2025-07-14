<template>
  <div class="equipment-management">
    <div class="page-card">
      <div class="page-header">
        <h2>设备档案管理</h2>
        <p>管理实验教学设备档案，包括设备信息、技术参数和状态跟踪</p>
      </div>
      
      <!-- 搜索和筛选 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="设备分类">
            <el-select
              v-model="searchForm.category_id"
              placeholder="请选择设备分类"
              clearable
              style="width: 150px"
            >
              <el-option
                v-for="category in categories"
                :key="category.id"
                :label="category.name"
                :value="category.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="设备状态">
            <el-select
              v-model="searchForm.status"
              placeholder="请选择状态"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="status in statusOptions"
                :key="status.value"
                :label="status.label"
                :value="status.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="设备状况">
            <el-select
              v-model="searchForm.condition"
              placeholder="请选择状况"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="condition in conditionOptions"
                :key="condition.value"
                :label="condition.label"
                :value="condition.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="存放位置">
            <el-input
              v-model="searchForm.location"
              placeholder="请输入存放位置"
              clearable
              style="width: 150px"
            />
          </el-form-item>
          
          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="设备名称/编号/型号"
              clearable
              style="width: 200px"
            />
          </el-form-item>
          
          <el-form-item>
            <el-button type="primary" :icon="Search" @click="handleSearch">
              搜索
            </el-button>
            <el-button :icon="Refresh" @click="handleReset">
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      
      <!-- 工具栏 -->
      <div class="table-toolbar">
        <div class="toolbar-left">
          <el-button type="primary" :icon="Plus" @click="handleCreate">
            新增设备
          </el-button>
          <el-button 
            type="success" 
            :icon="Upload" 
            @click="handleBatchImport"
            :disabled="!hasPermission('equipment:import')"
          >
            批量导入
          </el-button>
          <el-button 
            type="warning" 
            :icon="QrCode" 
            @click="handleBatchQRCode"
            :disabled="selectedRows.length === 0"
          >
            批量生成二维码
          </el-button>
        </div>
        <div class="toolbar-right">
          <el-button :icon="Download" @click="handleExport">
            导出数据
          </el-button>
          <el-button :icon="Refresh" @click="loadData">
            刷新
          </el-button>
        </div>
      </div>
      
      <!-- 数据表格 -->
      <div class="table-container">
        <el-table
          v-loading="loading"
          :data="tableData"
          @selection-change="handleSelectionChange"
          @sort-change="handleSortChange"
          stripe
          border
          height="600"
        >
          <el-table-column type="selection" width="55" />
          
          <el-table-column
            prop="code"
            label="设备编号"
            width="120"
            sortable="custom"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="name"
            label="设备名称"
            min-width="150"
            sortable="custom"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="category.name"
            label="设备分类"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="model"
            label="型号"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="brand"
            label="品牌"
            width="100"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="location"
            label="存放位置"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="status"
            label="设备状态"
            width="100"
            align="center"
          >
            <template #default="{ row }">
              <el-tag :type="getStatusTagType(row.status)">
                {{ getStatusText(row.status) }}
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column
            prop="condition"
            label="设备状况"
            width="100"
            align="center"
          >
            <template #default="{ row }">
              <el-tag :type="getConditionTagType(row.condition)">
                {{ getConditionText(row.condition) }}
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column
            prop="purchase_date"
            label="采购日期"
            width="120"
            sortable="custom"
          />
          
          <el-table-column
            prop="purchase_price"
            label="采购价格"
            width="120"
            align="right"
            sortable="custom"
          >
            <template #default="{ row }">
              ¥{{ row.purchase_price?.toLocaleString() }}
            </template>
          </el-table-column>
          
          <el-table-column
            label="操作"
            width="200"
            fixed="right"
          >
            <template #default="{ row }">
              <el-button
                type="primary"
                size="small"
                text
                @click="handleView(row)"
              >
                查看
              </el-button>
              <el-button
                type="primary"
                size="small"
                text
                @click="handleEdit(row)"
                :disabled="!hasPermission('equipment:edit')"
              >
                编辑
              </el-button>
              <el-button
                type="warning"
                size="small"
                text
                @click="handleQRCode(row)"
              >
                二维码
              </el-button>
              <el-button
                type="danger"
                size="small"
                text
                @click="handleDelete(row)"
                :disabled="!hasPermission('equipment:delete')"
              >
                删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
      
      <!-- 分页 -->
      <div class="table-pagination">
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
    
    <!-- 设备信息对话框 -->
    <EquipmentDialog
      v-model="dialogVisible"
      :equipment="currentEquipment"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 设备详情对话框 -->
    <EquipmentDetail
      v-model="detailVisible"
      :equipment="currentEquipment"
    />
    
    <!-- 二维码对话框 -->
    <QRCodeDialog
      v-model="qrCodeVisible"
      :equipment="currentEquipment"
      :batch-mode="batchQRMode"
      :selected-equipments="selectedRows"
    />
    
    <!-- 批量导入对话框 -->
    <BatchImportDialog
      v-model="importVisible"
      import-type="equipment"
      @success="handleImportSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Search,
  Refresh,
  Plus,
  Upload,
  Download,
  QrCode
} from '@element-plus/icons-vue'
import {
  getEquipmentsApi,
  deleteEquipmentApi,
  getEquipmentCategoriesApi,
  exportEquipmentsApi,
  type Equipment,
  type EquipmentCategory
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import EquipmentDialog from './components/EquipmentDialog.vue'
import EquipmentDetail from './components/EquipmentDetail.vue'
import QRCodeDialog from './components/QRCodeDialog.vue'
import BatchImportDialog from './components/BatchImportDialog.vue'

// 权限检查
const authStore = useAuthStore()
const hasPermission = (permission: string) => {
  return authStore.hasPermission(permission)
}

// 响应式数据
const loading = ref(false)
const tableData = ref<Equipment[]>([])
const selectedRows = ref<Equipment[]>([])
const categories = ref<EquipmentCategory[]>([])

// 搜索表单
const searchForm = reactive({
  category_id: undefined,
  status: undefined,
  condition: undefined,
  location: '',
  search: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 对话框状态
const dialogVisible = ref(false)
const detailVisible = ref(false)
const qrCodeVisible = ref(false)
const importVisible = ref(false)
const currentEquipment = ref<Equipment | null>(null)
const dialogMode = ref<'create' | 'edit'>('create')
const batchQRMode = ref(false)

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

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'warning',
    3: 'danger',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '正常',
    2: '借出',
    3: '维修',
    4: '报废'
  }
  return textMap[status] || '未知'
}

// 状况标签类型
const getConditionTagType = (condition: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'success',
    3: 'warning',
    4: 'danger'
  }
  return typeMap[condition] || 'info'
}

// 状况文本
const getConditionText = (condition: number) => {
  const textMap: Record<number, string> = {
    1: '优',
    2: '良',
    3: '中',
    4: '差'
  }
  return textMap[condition] || '未知'
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

    const response = await getEquipmentsApi(params)
    tableData.value = response.data.items
    pagination.total = response.data.pagination.total
  } catch (error) {
    console.error('加载设备数据失败:', error)
    ElMessage.error('加载设备数据失败')
  } finally {
    loading.value = false
  }
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

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  loadData()
}

// 重置
const handleReset = () => {
  Object.assign(searchForm, {
    category_id: undefined,
    status: undefined,
    condition: undefined,
    location: '',
    search: ''
  })
  pagination.current_page = 1
  loadData()
}

// 新增设备
const handleCreate = () => {
  currentEquipment.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑设备
const handleEdit = (row: Equipment) => {
  currentEquipment.value = row
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看设备详情
const handleView = (row: Equipment) => {
  currentEquipment.value = row
  detailVisible.value = true
}

// 删除设备
const handleDelete = async (row: Equipment) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除设备"${row.name}"吗？此操作不可恢复。`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await deleteEquipmentApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除设备失败:', error)
      ElMessage.error('删除设备失败')
    }
  }
}

// 生成二维码
const handleQRCode = (row: Equipment) => {
  currentEquipment.value = row
  batchQRMode.value = false
  qrCodeVisible.value = true
}

// 批量生成二维码
const handleBatchQRCode = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('请先选择要生成二维码的设备')
    return
  }

  batchQRMode.value = true
  qrCodeVisible.value = true
}

// 批量导入
const handleBatchImport = () => {
  importVisible.value = true
}

// 导出数据
const handleExport = async () => {
  try {
    const response = await exportEquipmentsApi(searchForm)

    // 创建下载链接
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `设备档案_${new Date().toISOString().slice(0, 10)}.xlsx`
    link.click()
    window.URL.revokeObjectURL(url)

    ElMessage.success('导出成功')
  } catch (error) {
    console.error('导出失败:', error)
    ElMessage.error('导出失败')
  }
}

// 表格选择变化
const handleSelectionChange = (selection: Equipment[]) => {
  selectedRows.value = selection
}

// 排序变化
const handleSortChange = ({ prop, order }: { prop: string; order: string }) => {
  // 处理排序逻辑
  loadData()
}

// 页码变化
const handleCurrentChange = () => {
  loadData()
}

// 页大小变化
const handleSizeChange = () => {
  pagination.current_page = 1
  loadData()
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 导入成功回调
const handleImportSuccess = () => {
  loadData()
}

// 初始化
onMounted(() => {
  loadCategories()
  loadData()
})
</script>

<style scoped>
.equipment-management {
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

.toolbar-left,
.toolbar-right {
  display: flex;
  gap: 8px;
}

.table-container {
  margin-bottom: 16px;
}

.table-pagination {
  display: flex;
  justify-content: flex-end;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .equipment-management {
    padding: 10px;
  }

  .page-card {
    padding: 16px;
  }

  .table-toolbar {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .toolbar-left,
  .toolbar-right {
    justify-content: center;
  }
}
</style>
