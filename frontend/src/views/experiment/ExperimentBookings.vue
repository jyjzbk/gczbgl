<template>
  <div class="experiment-bookings">
    <div class="page-card">
      <div class="page-header">
        <h2>实验预约管理</h2>
        <p>管理实验室预约申请，包括预约审核和时间安排</p>
      </div>
      
      <!-- 搜索和筛选 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="学校">
            <el-select
              v-model="searchForm.school_id"
              placeholder="请选择学校"
              clearable
              style="width: 150px"
            >
              <el-option
                v-for="school in schools"
                :key="school.id"
                :label="school.name"
                :value="school.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="实验室">
            <el-select
              v-model="searchForm.laboratory_id"
              placeholder="请选择实验室"
              clearable
              style="width: 150px"
            >
              <el-option
                v-for="lab in laboratories"
                :key="lab.id"
                :label="lab.name"
                :value="lab.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="状态">
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
          
          <el-form-item label="预约日期">
            <el-date-picker
              v-model="dateRange"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              format="YYYY-MM-DD"
              value-format="YYYY-MM-DD"
              style="width: 240px"
              @change="handleDateChange"
            />
          </el-form-item>
          
          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="班级名称/教师姓名"
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
            新增预约
          </el-button>
          <el-button type="success" @click="handleBatchApprove">
            <el-icon><Check /></el-icon>
            批量通过
          </el-button>
          <el-button type="warning" @click="handleBatchReject">
            <el-icon><Close /></el-icon>
            批量拒绝
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
        @selection-change="handleSelectionChange"
        @sort-change="handleSortChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="catalog.name" label="实验名称" min-width="180" show-overflow-tooltip />
        <el-table-column prop="laboratory.name" label="实验室" width="120" />
        <el-table-column label="所属学校" width="140" show-overflow-tooltip>
          <template #default="{ row }">
            {{ row.laboratory?.school?.name || '未知学校' }}
          </template>
        </el-table-column>
        <el-table-column prop="teacher.real_name" label="申请教师" width="100" />
        <el-table-column prop="class_name" label="班级" width="120" />
        <el-table-column prop="student_count" label="学生数" width="80" />
        <el-table-column prop="reservation_date" label="预约日期" width="120" sortable="custom" />
        <el-table-column label="预约时间" width="160">
          <template #default="{ row }">
            {{ row.start_time }} - {{ row.end_time }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusTagType(row.status)">
              {{ getStatusLabel(row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="申请时间" width="160" sortable="custom">
          <template #default="{ row }">
            {{ formatDateTime(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link @click="handleView(row)">
              查看
            </el-button>
            <el-button
              v-if="row.status === 1"
              type="primary"
              link
              @click="handleEdit(row)"
              :title="canEdit(row) ? '编辑预约' : '只有待审核状态的预约可以编辑'"
            >
              编辑
            </el-button>
            <el-button
              v-if="row.status === 1"
              type="success"
              link
              @click="handleApprove(row)"
            >
              通过
            </el-button>
            <el-button
              v-if="row.status === 1"
              type="danger"
              link
              @click="handleReject(row)"
            >
              拒绝
            </el-button>
            <el-button
              v-if="[1, 2].includes(row.status)"
              type="danger"
              link
              @click="handleCancel(row)"
              :title="canCancel(row) ? '取消预约' : '只有待审核或已通过的预约可以取消'"
            >
              取消
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
    
    <!-- 预约表单对话框 -->
    <ExperimentBookingDialog
      v-model="dialogVisible"
      :booking="currentBooking"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 预约详情对话框 -->
    <ExperimentBookingDetail
      v-model="detailVisible"
      :booking="currentBooking"
    />
    
    <!-- 审核对话框 -->
    <ReviewDialog
      v-model="reviewVisible"
      :booking="currentBooking"
      :action="reviewAction"
      @success="handleReviewSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Plus, Check, Close } from '@element-plus/icons-vue'
import {
  getExperimentReservationsApi,
  cancelExperimentReservationApi,
  reviewExperimentReservationApi,
  type ExperimentReservation
} from '@/api/experiment'
import { getSchoolsApi, type School } from '@/api/user'
import { getLaboratoriesApi, type Laboratory } from '@/api/experiment'
import ExperimentBookingDialog from './components/ExperimentBookingDialog.vue'
import ExperimentBookingDetail from './components/ExperimentBookingDetail.vue'
import ReviewDialog from './components/ReviewDialog.vue'
import dayjs from 'dayjs'

// 响应式数据
const loading = ref(false)
const tableData = ref<ExperimentReservation[]>([])
const selectedRows = ref<ExperimentReservation[]>([])
const schools = ref<School[]>([])
const laboratories = ref<Laboratory[]>([])

// 日期范围
const dateRange = ref<[string, string] | null>(null)

// 搜索表单
const searchForm = reactive({
  school_id: undefined as number | undefined,
  laboratory_id: undefined as number | undefined,
  status: undefined as number | undefined,
  start_date: '',
  end_date: '',
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
  sort_field: 'created_at',
  sort_order: 'desc' as 'asc' | 'desc'
})

// 对话框状态
const dialogVisible = ref(false)
const detailVisible = ref(false)
const reviewVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const currentBooking = ref<ExperimentReservation | null>(null)
const reviewAction = ref<'approve' | 'reject'>('approve')

// 状态选项
const statusOptions = [
  { label: '待审核', value: 0 },
  { label: '已通过', value: 1 },
  { label: '已拒绝', value: 2 },
  { label: '已取消', value: 3 }
]

// 获取状态标签类型
const getStatusTagType = (status: number) => {
  const statusMap: Record<number, string> = {
    1: 'warning',  // 待审核
    2: 'success',  // 已通过
    3: 'danger',   // 已拒绝
    4: 'info',     // 已完成
    5: 'info'      // 已取消
  }
  return statusMap[status] || 'info'
}

// 获取状态标签文本
const getStatusLabel = (status: number) => {
  const statusMap: Record<number, string> = {
    1: '待审核',
    2: '已通过',
    3: '已拒绝',
    4: '已完成',
    5: '已取消'
  }
  return statusMap[status] || '未知'
}

// 检查是否可以编辑
const canEdit = (booking: ExperimentReservation) => {
  return booking.status === 1 && new Date(booking.reservation_date) >= new Date()
}

// 检查是否可以取消
const canCancel = (booking: ExperimentReservation) => {
  return [1, 2].includes(booking.status) && new Date(booking.reservation_date) >= new Date()
}

// 格式化日期时间
const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm')
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
    
    const response = await getExperimentReservationsApi(params)
    tableData.value = response.data.data
    
    // 更新分页信息
    Object.assign(pagination, response.data)
  } catch (error) {
    console.error('加载实验预约失败:', error)
  } finally {
    loading.value = false
  }
}

// 加载学校列表
const loadSchools = async () => {
  try {
    const response = await getSchoolsApi()
    // 检查响应数据结构
    if (response.data && Array.isArray(response.data.data)) {
      schools.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      schools.value = response.data
    } else {
      console.warn('学校数据格式不正确:', response.data)
      schools.value = []
    }
  } catch (error) {
    console.error('加载学校列表失败:', error)
    schools.value = []
  }
}

// 加载实验室列表
const loadLaboratories = async () => {
  try {
    const response = await getLaboratoriesApi()
    // 检查响应数据结构
    if (response.data && Array.isArray(response.data.data)) {
      laboratories.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      laboratories.value = response.data
    } else {
      console.warn('实验室数据格式不正确:', response.data)
      laboratories.value = []
    }
  } catch (error) {
    console.error('加载实验室列表失败:', error)
    laboratories.value = []
  }
}

// 处理日期范围变化
const handleDateChange = (dates: [string, string] | null) => {
  if (dates) {
    searchForm.start_date = dates[0]
    searchForm.end_date = dates[1]
  } else {
    searchForm.start_date = ''
    searchForm.end_date = ''
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
    school_id: undefined,
    laboratory_id: undefined,
    status: undefined,
    start_date: '',
    end_date: '',
    search: ''
  })
  dateRange.value = null
  handleSearch()
}

// 排序变化
const handleSortChange = ({ prop, order }: { prop: string; order: string | null }) => {
  if (order) {
    sortData.sort_field = prop
    sortData.sort_order = order === 'ascending' ? 'asc' : 'desc'
  } else {
    sortData.sort_field = 'created_at'
    sortData.sort_order = 'desc'
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

// 选择变化
const handleSelectionChange = (selection: ExperimentReservation[]) => {
  selectedRows.value = selection
}

// 新增
const handleCreate = () => {
  currentBooking.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑
const handleEdit = (booking: ExperimentReservation) => {
  currentBooking.value = booking
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看详情
const handleView = (booking: ExperimentReservation) => {
  currentBooking.value = booking
  detailVisible.value = true
}

// 通过
const handleApprove = (booking: ExperimentReservation) => {
  currentBooking.value = booking
  reviewAction.value = 'approve'
  reviewVisible.value = true
}

// 拒绝
const handleReject = (booking: ExperimentReservation) => {
  currentBooking.value = booking
  reviewAction.value = 'reject'
  reviewVisible.value = true
}

// 取消预约
const handleCancel = async (booking: ExperimentReservation) => {
  try {
    await ElMessageBox.confirm(
      `确定要取消"${booking.catalog?.name}"的预约吗？`,
      '确认取消',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await cancelExperimentReservationApi(booking.id)
    ElMessage.success('取消成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('取消预约失败:', error)
    }
  }
}

// 批量通过
const handleBatchApprove = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('请选择要操作的记录')
    return
  }
  
  const pendingRows = selectedRows.value.filter(row => row.status === 0)
  if (pendingRows.length === 0) {
    ElMessage.warning('所选记录中没有待审核的预约')
    return
  }
  
  ElMessage.info('批量审核功能开发中...')
}

// 批量拒绝
const handleBatchReject = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('请选择要操作的记录')
    return
  }
  
  const pendingRows = selectedRows.value.filter(row => row.status === 0)
  if (pendingRows.length === 0) {
    ElMessage.warning('所选记录中没有待审核的预约')
    return
  }
  
  ElMessage.info('批量审核功能开发中...')
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 审核成功回调
const handleReviewSuccess = () => {
  loadData()
}

onMounted(() => {
  loadData()
  loadSchools()
  loadLaboratories()
})
</script>

<style scoped>
.experiment-bookings {
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
