<template>
  <div class="equipment-maintenance">
    <div class="page-card">
      <div class="page-header">
        <h2>设备维修管理</h2>
        <p>管理设备故障报修、维修进度跟踪和成本记录</p>
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
          
          <el-form-item label="报修人">
            <el-input
              v-model="searchForm.reporter_name"
              placeholder="请输入报修人姓名"
              clearable
              style="width: 150px"
            />
          </el-form-item>
          
          <el-form-item label="维修状态">
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
          
          <el-form-item label="故障类型">
            <el-select
              v-model="searchForm.fault_type"
              placeholder="请选择类型"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="type in faultTypeOptions"
                :key="type.value"
                :label="type.label"
                :value="type.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="优先级">
            <el-select
              v-model="searchForm.priority"
              placeholder="请选择优先级"
              clearable
              style="width: 120px"
            >
              <el-option
                v-for="priority in priorityOptions"
                :key="priority.value"
                :label="priority.label"
                :value="priority.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="报修日期">
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
            新增维修申请
          </el-button>
          <el-button 
            type="success" 
            :icon="Tools" 
            @click="handleBatchAssign"
            :disabled="selectedRows.length === 0"
          >
            批量分配技师
          </el-button>
          <el-button 
            type="warning" 
            :icon="Clock" 
            @click="handleUrgentReminder"
          >
            紧急提醒
          </el-button>
        </div>
        <div class="toolbar-right">
          <el-button :icon="PieChart" @click="handleStatistics">
            维修统计
          </el-button>
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
            prop="reporter_name"
            label="报修人"
            width="100"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="fault_type"
            label="故障类型"
            width="100"
            align="center"
          >
            <template #default="{ row }">
              <el-tag :type="getFaultTypeTagType(row.fault_type)">
                {{ getFaultTypeText(row.fault_type) }}
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column
            prop="priority"
            label="优先级"
            width="80"
            align="center"
          >
            <template #default="{ row }">
              <el-tag :type="getPriorityTagType(row.priority)">
                {{ getPriorityText(row.priority) }}
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column
            prop="status"
            label="维修状态"
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
            prop="technician_name"
            label="维修技师"
            width="100"
            show-overflow-tooltip
          >
            <template #default="{ row }">
              {{ row.technician_name || '未分配' }}
            </template>
          </el-table-column>
          
          <el-table-column
            prop="repair_start_date"
            label="开始维修"
            width="120"
            sortable="custom"
          >
            <template #default="{ row }">
              {{ row.repair_start_date || '-' }}
            </template>
          </el-table-column>
          
          <el-table-column
            prop="repair_end_date"
            label="完成维修"
            width="120"
            sortable="custom"
          >
            <template #default="{ row }">
              {{ row.repair_end_date || '-' }}
            </template>
          </el-table-column>
          
          <el-table-column
            prop="repair_cost"
            label="维修费用"
            width="100"
            align="right"
            sortable="custom"
          >
            <template #default="{ row }">
              {{ row.repair_cost ? `¥${row.repair_cost.toLocaleString()}` : '-' }}
            </template>
          </el-table-column>
          
          <el-table-column
            label="维修天数"
            width="100"
            align="center"
          >
            <template #default="{ row }">
              {{ getRepairDays(row) }}
            </template>
          </el-table-column>
          
          <el-table-column
            prop="created_at"
            label="报修时间"
            width="120"
            sortable="custom"
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
                @click="handleAssign(row)"
                :disabled="!hasPermission('equipment:maintenance:assign')"
              >
                分配
              </el-button>
              <el-button
                v-if="row.status === 2"
                type="warning"
                size="small"
                text
                @click="handleStart(row)"
                :disabled="!hasPermission('equipment:maintenance:start')"
              >
                开始
              </el-button>
              <el-button
                v-if="row.status === 2"
                type="success"
                size="small"
                text
                @click="handleComplete(row)"
                :disabled="!hasPermission('equipment:maintenance:complete')"
              >
                完成
              </el-button>
              <el-button
                v-if="[1, 2].includes(row.status)"
                type="primary"
                size="small"
                text
                @click="handleEdit(row)"
                :disabled="!hasPermission('equipment:maintenance:edit')"
              >
                编辑
              </el-button>
              <el-button
                v-if="row.status === 1"
                type="danger"
                size="small"
                text
                @click="handleCancel(row)"
                :disabled="!hasPermission('equipment:maintenance:cancel')"
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
    
    <!-- 维修申请对话框 -->
    <MaintenanceDialog
      v-model="dialogVisible"
      :maintenance="currentMaintenance"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 维修详情对话框 -->
    <MaintenanceDetail
      v-model="detailVisible"
      :maintenance="currentMaintenance"
    />
    
    <!-- 技师分配对话框 -->
    <TechnicianAssignDialog
      v-model="assignVisible"
      :maintenance="currentMaintenance"
      :batch-mode="batchAssignMode"
      :selected-maintenances="selectedRows"
      @success="handleAssignSuccess"
    />
    
    <!-- 维修完成对话框 -->
    <MaintenanceCompleteDialog
      v-model="completeVisible"
      :maintenance="currentMaintenance"
      @success="handleCompleteSuccess"
    />
    
    <!-- 维修统计对话框 -->
    <MaintenanceStatistics
      v-model="statisticsVisible"
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
  Tools,
  Clock,
  PieChart,
  Download
} from '@element-plus/icons-vue'
import {
  getEquipmentMaintenancesApi,
  updateEquipmentMaintenanceApi,
  type EquipmentMaintenance
} from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import MaintenanceDialog from './components/MaintenanceDialog.vue'
import MaintenanceDetail from './components/MaintenanceDetail.vue'
import TechnicianAssignDialog from './components/TechnicianAssignDialog.vue'
import MaintenanceCompleteDialog from './components/MaintenanceCompleteDialog.vue'
import MaintenanceStatistics from './components/MaintenanceStatistics.vue'
import dayjs from 'dayjs'

// 权限检查
const authStore = useAuthStore()
const hasPermission = (permission: string) => {
  return authStore.hasPermission(permission)
}

// 响应式数据
const loading = ref(false)
const tableData = ref<EquipmentMaintenance[]>([])
const selectedRows = ref<EquipmentMaintenance[]>([])
const dateRange = ref<[string, string] | null>(null)

// 搜索表单
const searchForm = reactive({
  equipment_name: '',
  reporter_name: '',
  status: undefined,
  fault_type: undefined,
  priority: undefined,
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
const assignVisible = ref(false)
const completeVisible = ref(false)
const statisticsVisible = ref(false)
const currentMaintenance = ref<EquipmentMaintenance | null>(null)
const dialogMode = ref<'create' | 'edit'>('create')
const batchAssignMode = ref(false)

// 选项数据
const statusOptions = [
  { value: 1, label: '待处理' },
  { value: 2, label: '处理中' },
  { value: 3, label: '已完成' },
  { value: 4, label: '已取消' }
]

const faultTypeOptions = [
  { value: 1, label: '硬件故障' },
  { value: 2, label: '软件故障' },
  { value: 3, label: '使用损坏' },
  { value: 4, label: '自然老化' }
]

const priorityOptions = [
  { value: 1, label: '低' },
  { value: 2, label: '中' },
  { value: 3, label: '高' },
  { value: 4, label: '紧急' }
]

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'warning',
    2: 'primary',
    3: 'success',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '待处理',
    2: '处理中',
    3: '已完成',
    4: '已取消'
  }
  return textMap[status] || '未知'
}

// 故障类型标签类型
const getFaultTypeTagType = (faultType: number) => {
  const typeMap: Record<number, string> = {
    1: 'danger',
    2: 'warning',
    3: 'primary',
    4: 'info'
  }
  return typeMap[faultType] || 'info'
}

// 故障类型文本
const getFaultTypeText = (faultType: number) => {
  const textMap: Record<number, string> = {
    1: '硬件故障',
    2: '软件故障',
    3: '使用损坏',
    4: '自然老化'
  }
  return textMap[faultType] || '未知'
}

// 优先级标签类型
const getPriorityTagType = (priority: number) => {
  const typeMap: Record<number, string> = {
    1: 'info',
    2: 'warning',
    3: 'danger',
    4: 'danger'
  }
  return typeMap[priority] || 'info'
}

// 优先级文本
const getPriorityText = (priority: number) => {
  const textMap: Record<number, string> = {
    1: '低',
    2: '中',
    3: '高',
    4: '紧急'
  }
  return textMap[priority] || '未知'
}

// 计算维修天数
const getRepairDays = (maintenance: EquipmentMaintenance) => {
  if (maintenance.repair_end_date && maintenance.repair_start_date) {
    const start = dayjs(maintenance.repair_start_date)
    const end = dayjs(maintenance.repair_end_date)
    return `${end.diff(start, 'day') + 1}天`
  } else if (maintenance.repair_start_date) {
    const start = dayjs(maintenance.repair_start_date)
    const now = dayjs()
    return `${now.diff(start, 'day') + 1}天`
  }
  return '-'
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

    const response = await getEquipmentMaintenancesApi(params)
    tableData.value = response.data.items
    pagination.total = response.data.pagination.total
  } catch (error) {
    console.error('加载维修数据失败:', error)
    ElMessage.error('加载维修数据失败')
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
    reporter_name: '',
    status: undefined,
    fault_type: undefined,
    priority: undefined,
    start_date: '',
    end_date: ''
  })
  dateRange.value = null
  pagination.current_page = 1
  loadData()
}

// 新增维修申请
const handleCreate = () => {
  currentMaintenance.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑维修申请
const handleEdit = (row: EquipmentMaintenance) => {
  currentMaintenance.value = row
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看维修详情
const handleView = (row: EquipmentMaintenance) => {
  currentMaintenance.value = row
  detailVisible.value = true
}

// 分配技师
const handleAssign = (row: EquipmentMaintenance) => {
  currentMaintenance.value = row
  batchAssignMode.value = false
  assignVisible.value = true
}

// 批量分配技师
const handleBatchAssign = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('请先选择要分配技师的维修申请')
    return
  }

  const pendingMaintenances = selectedRows.value.filter(row => row.status === 1)
  if (pendingMaintenances.length === 0) {
    ElMessage.warning('所选申请中没有待处理的记录')
    return
  }

  batchAssignMode.value = true
  assignVisible.value = true
}

// 开始维修
const handleStart = async (row: EquipmentMaintenance) => {
  try {
    await ElMessageBox.confirm(
      `确定开始维修"${row.equipment?.name}"吗？`,
      '确认开始维修',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await updateEquipmentMaintenanceApi(row.id, {
      status: 2,
      repair_start_date: dayjs().format('YYYY-MM-DD')
    })
    ElMessage.success('维修已开始')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('开始维修失败:', error)
      ElMessage.error('开始维修失败')
    }
  }
}

// 完成维修
const handleComplete = (row: EquipmentMaintenance) => {
  currentMaintenance.value = row
  completeVisible.value = true
}

// 取消维修申请
const handleCancel = async (row: EquipmentMaintenance) => {
  try {
    await ElMessageBox.confirm(
      `确定要取消"${row.equipment?.name}"的维修申请吗？`,
      '确认取消',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await updateEquipmentMaintenanceApi(row.id, { status: 4 })
    ElMessage.success('取消成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('取消维修申请失败:', error)
      ElMessage.error('取消维修申请失败')
    }
  }
}

// 紧急提醒
const handleUrgentReminder = () => {
  ElMessage.info('紧急提醒功能开发中...')
}

// 维修统计
const handleStatistics = () => {
  statisticsVisible.value = true
}

// 导出数据
const handleExport = () => {
  ElMessage.info('导出功能开发中...')
}

// 表格选择变化
const handleSelectionChange = (selection: EquipmentMaintenance[]) => {
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

// 分配成功回调
const handleAssignSuccess = () => {
  loadData()
}

// 完成成功回调
const handleCompleteSuccess = () => {
  loadData()
}

// 初始化
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.equipment-maintenance {
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
  .equipment-maintenance {
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
