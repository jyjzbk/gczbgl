<template>
  <div class="equipment-borrow">
    <div class="page-card">
      <div class="page-header">
        <h2>设备借用管理</h2>
        <p>管理设备借用申请、审批流程和归还记录</p>
      </div>
      
      <!-- 搜索和筛选 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="设备名称">
            <el-input
              v-model="searchForm.equipment_name"
              placeholder="请输入设备名称"
              clearable
              style="width: 150px"
            />
          </el-form-item>
          
          <el-form-item label="借用人">
            <el-input
              v-model="searchForm.borrower_name"
              placeholder="请输入借用人姓名"
              clearable
              style="width: 150px"
            />
          </el-form-item>
          
          <el-form-item label="借用状态">
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
          
          <el-form-item label="借用日期">
            <el-date-picker
              v-model="dateRange"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="YYYY-MM-DD"
              style="width: 240px"
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
            新增借用申请
          </el-button>
          <el-button 
            type="success" 
            :icon="Check" 
            @click="handleBatchApprove"
            :disabled="selectedRows.length === 0"
          >
            批量审批
          </el-button>
          <el-button 
            type="warning" 
            :icon="Clock" 
            @click="handleOverdueReminder"
          >
            逾期提醒
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
            prop="equipment.name"
            label="设备名称"
            min-width="150"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="equipment.code"
            label="设备编号"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="borrower_name"
            label="借用人"
            width="100"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="borrower_phone"
            label="联系电话"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="borrow_date"
            label="借用日期"
            width="120"
            sortable="custom"
          />
          
          <el-table-column
            prop="expected_return_date"
            label="预计归还"
            width="120"
            sortable="custom"
          />
          
          <el-table-column
            prop="actual_return_date"
            label="实际归还"
            width="120"
            sortable="custom"
          >
            <template #default="{ row }">
              {{ row.actual_return_date || '-' }}
            </template>
          </el-table-column>
          
          <el-table-column
            prop="status"
            label="借用状态"
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
            label="逾期天数"
            width="100"
            align="center"
          >
            <template #default="{ row }">
              <span v-if="getOverdueDays(row) > 0" class="overdue-days">
                {{ getOverdueDays(row) }}天
              </span>
              <span v-else>-</span>
            </template>
          </el-table-column>
          
          <el-table-column
            prop="purpose"
            label="借用用途"
            min-width="150"
            show-overflow-tooltip
          />
          
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
                v-if="row.status === 1"
                type="success"
                size="small"
                text
                @click="handleApprove(row)"
                :disabled="!hasPermission('equipment:borrow:approve')"
              >
                审批
              </el-button>
              <el-button
                v-if="row.status === 3"
                type="warning"
                size="small"
                text
                @click="handleReturn(row)"
                :disabled="!hasPermission('equipment:borrow:return')"
              >
                归还
              </el-button>
              <el-button
                v-if="[1, 2].includes(row.status)"
                type="primary"
                size="small"
                text
                @click="handleEdit(row)"
                :disabled="!hasPermission('equipment:borrow:edit')"
              >
                编辑
              </el-button>
              <el-button
                v-if="row.status === 1"
                type="danger"
                size="small"
                text
                @click="handleCancel(row)"
                :disabled="!hasPermission('equipment:borrow:cancel')"
              >
                取消
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
    
    <!-- 借用申请对话框 -->
    <BorrowDialog
      v-model="dialogVisible"
      :borrow="currentBorrow"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 借用详情对话框 -->
    <BorrowDetail
      v-model="detailVisible"
      :borrow="currentBorrow"
    />
    
    <!-- 审批对话框 -->
    <ApprovalDialog
      v-model="approvalVisible"
      :borrow="currentBorrow"
      :batch-mode="batchApprovalMode"
      :selected-borrows="selectedRows"
      @success="handleApprovalSuccess"
    />
    
    <!-- 归还对话框 -->
    <ReturnDialog
      v-model="returnVisible"
      :borrow="currentBorrow"
      @success="handleReturnSuccess"
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
  Check,
  Clock,
  Download
} from '@element-plus/icons-vue'
import {
  getEquipmentBorrowsApi,
  reviewEquipmentBorrowApi,
  returnEquipmentApi,
  type EquipmentBorrow
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import BorrowDialog from './components/BorrowDialog.vue'
import BorrowDetail from './components/BorrowDetail.vue'
import ApprovalDialog from './components/ApprovalDialog.vue'
import ReturnDialog from './components/ReturnDialog.vue'
import dayjs from 'dayjs'

// 权限检查
const authStore = useAuthStore()
const hasPermission = (permission: string) => {
  return authStore.hasPermission(permission)
}

// 响应式数据
const loading = ref(false)
const tableData = ref<EquipmentBorrow[]>([])
const selectedRows = ref<EquipmentBorrow[]>([])
const dateRange = ref<[string, string] | null>(null)

// 搜索表单
const searchForm = reactive({
  equipment_name: '',
  borrower_name: '',
  status: undefined,
  start_date: '',
  end_date: ''
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
const approvalVisible = ref(false)
const returnVisible = ref(false)
const currentBorrow = ref<EquipmentBorrow | null>(null)
const dialogMode = ref<'create' | 'edit'>('create')
const batchApprovalMode = ref(false)

// 选项数据
const statusOptions = [
  { value: 1, label: '申请中' },
  { value: 2, label: '已批准' },
  { value: 3, label: '已借出' },
  { value: 4, label: '已归还' },
  { value: 5, label: '已拒绝' },
  { value: 6, label: '逾期' }
]

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'warning',
    2: 'success',
    3: 'primary',
    4: 'success',
    5: 'danger',
    6: 'danger'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '申请中',
    2: '已批准',
    3: '已借出',
    4: '已归还',
    5: '已拒绝',
    6: '逾期'
  }
  return textMap[status] || '未知'
}

// 计算逾期天数
const getOverdueDays = (borrow: EquipmentBorrow) => {
  if (borrow.status === 4 || !borrow.expected_return_date) return 0

  const today = dayjs()
  const expectedDate = dayjs(borrow.expected_return_date)

  if (today.isAfter(expectedDate)) {
    return today.diff(expectedDate, 'day')
  }

  return 0
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

    const response = await getEquipmentBorrowsApi(params)
    tableData.value = response.data.items
    pagination.total = response.data.pagination.total
  } catch (error) {
    console.error('加载借用数据失败:', error)
    ElMessage.error('加载借用数据失败')
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  // 处理日期范围
  if (dateRange.value) {
    searchForm.start_date = dateRange.value[0]
    searchForm.end_date = dateRange.value[1]
  } else {
    searchForm.start_date = ''
    searchForm.end_date = ''
  }

  pagination.current_page = 1
  loadData()
}

// 重置
const handleReset = () => {
  Object.assign(searchForm, {
    equipment_name: '',
    borrower_name: '',
    status: undefined,
    start_date: '',
    end_date: ''
  })
  dateRange.value = null
  pagination.current_page = 1
  loadData()
}

// 新增借用申请
const handleCreate = () => {
  currentBorrow.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑借用申请
const handleEdit = (row: EquipmentBorrow) => {
  currentBorrow.value = row
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看借用详情
const handleView = (row: EquipmentBorrow) => {
  currentBorrow.value = row
  detailVisible.value = true
}

// 审批借用申请
const handleApprove = (row: EquipmentBorrow) => {
  currentBorrow.value = row
  batchApprovalMode.value = false
  approvalVisible.value = true
}

// 批量审批
const handleBatchApprove = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('请先选择要审批的借用申请')
    return
  }

  const pendingBorrows = selectedRows.value.filter(row => row.status === 1)
  if (pendingBorrows.length === 0) {
    ElMessage.warning('所选申请中没有待审批的记录')
    return
  }

  batchApprovalMode.value = true
  approvalVisible.value = true
}

// 归还设备
const handleReturn = (row: EquipmentBorrow) => {
  currentBorrow.value = row
  returnVisible.value = true
}

// 取消借用申请
const handleCancel = async (row: EquipmentBorrow) => {
  try {
    await ElMessageBox.confirm(
      `确定要取消"${row.equipment?.name}"的借用申请吗？`,
      '确认取消',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await reviewEquipmentBorrowApi(row.id, { status: 5, remark: '用户取消申请' })
    ElMessage.success('取消成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('取消借用申请失败:', error)
      ElMessage.error('取消借用申请失败')
    }
  }
}

// 逾期提醒
const handleOverdueReminder = () => {
  ElMessage.info('逾期提醒功能开发中...')
}

// 导出数据
const handleExport = () => {
  ElMessage.info('导出功能开发中...')
}

// 表格选择变化
const handleSelectionChange = (selection: EquipmentBorrow[]) => {
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

// 审批成功回调
const handleApprovalSuccess = () => {
  loadData()
}

// 归还成功回调
const handleReturnSuccess = () => {
  loadData()
}

// 初始化
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.equipment-borrow {
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

.overdue-days {
  color: #f56c6c;
  font-weight: 600;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .equipment-borrow {
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
