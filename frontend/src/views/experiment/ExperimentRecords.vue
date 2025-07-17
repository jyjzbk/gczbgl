<template>
  <div class="experiment-records">
    <div class="page-card">
      <div class="page-header">
        <h2>实验记录管理</h2>
        <p>记录和管理实验教学过程，包括实验结果和评价</p>
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
          
          <el-form-item label="实验日期">
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
            开始实验
          </el-button>
          <el-button @click="handleExport">
            <el-icon><Download /></el-icon>
            导出记录
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
        <el-table-column prop="catalog.name" label="实验名称" min-width="180" show-overflow-tooltip />
        <el-table-column prop="laboratory.name" label="实验室" width="120" />
        <el-table-column prop="teacher.real_name" label="执行教师" width="100" />
        <el-table-column prop="class_name" label="班级" width="120" />
        <el-table-column prop="student_count" label="学生数" width="80" />
        <el-table-column label="实验时间" width="180">
          <template #default="{ row }">
            <div>
              <div>{{ formatDate(row.start_time) }}</div>
              <div v-if="row.end_time" class="text-secondary">
                至 {{ formatTime(row.end_time) }}
              </div>
              <el-tag v-else type="warning" size="small">进行中</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="completion_rate" label="完成率" width="100">
          <template #default="{ row }">
            <el-progress 
              :percentage="row.completion_rate" 
              :stroke-width="6"
              :show-text="false"
            />
            <span class="progress-text">{{ row.completion_rate }}%</span>
          </template>
        </el-table-column>
        <el-table-column prop="quality_score" label="质量评分" width="100">
          <template #default="{ row }">
            <el-rate
              :model-value="row.quality_score / 20"
              disabled
              show-score
              text-color="#ff9900"
              score-template="{value}星"
            />
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="getStatusTagType(row.status)">
              {{ getStatusLabel(row.status) }}
            </el-tag>
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
            >
              编辑
            </el-button>
            <el-button 
              v-if="row.status === 1" 
              type="success" 
              link 
              @click="handleComplete(row)"
            >
              完成
            </el-button>
            <el-button 
              v-if="row.photos && row.photos.length > 0" 
              type="info" 
              link 
              @click="handleViewPhotos(row)"
            >
              照片
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
    
    <!-- 实验记录表单对话框 -->
    <ExperimentRecordDialog
      v-model="dialogVisible"
      :record="currentRecord"
      :mode="dialogMode"
      @success="handleDialogSuccess"
    />
    
    <!-- 实验记录详情对话框 -->
    <ExperimentRecordDetail
      v-model="detailVisible"
      :record="currentRecord"
    />
    
    <!-- 完成实验对话框 -->
    <CompleteExperimentDialog
      v-model="completeVisible"
      :record="currentRecord"
      @success="handleCompleteSuccess"
    />
    
    <!-- 照片查看对话框 -->
    <PhotoViewDialog
      v-model="photoVisible"
      :photos="currentPhotos"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Refresh, Plus, Download } from '@element-plus/icons-vue'
import {
  getExperimentRecordsApi,
  type ExperimentRecord
} from '@/api/experiment'
import { getSchoolsApi, type School } from '@/api/user'
import { getLaboratoriesApi, type Laboratory } from '@/api/experiment'
import ExperimentRecordDialog from './components/ExperimentRecordDialog.vue'
import ExperimentRecordDetail from './components/ExperimentRecordDetail.vue'
import CompleteExperimentDialog from './components/CompleteExperimentDialog.vue'
import PhotoViewDialog from './components/PhotoViewDialog.vue'
import dayjs from 'dayjs'

// 响应式数据
const loading = ref(false)
const tableData = ref<ExperimentRecord[]>([])
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
  sort_field: 'start_time',
  sort_order: 'desc' as 'asc' | 'desc'
})

// 对话框状态
const dialogVisible = ref(false)
const detailVisible = ref(false)
const completeVisible = ref(false)
const photoVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const currentRecord = ref<ExperimentRecord | null>(null)
const currentPhotos = ref<string[]>([])

// 状态选项
const statusOptions = [
  { label: '进行中', value: 1 },
  { label: '已完成', value: 2 },
  { label: '已取消', value: 3 }
]

// 获取状态标签类型
const getStatusTagType = (status: number) => {
  const statusMap: Record<number, string> = {
    1: 'warning',
    2: 'success',
    3: 'info'
  }
  return statusMap[status] || 'info'
}

// 获取状态标签文本
const getStatusLabel = (status: number) => {
  const statusMap: Record<number, string> = {
    1: '进行中',
    2: '已完成',
    3: '已取消'
  }
  return statusMap[status] || '未知'
}

// 格式化日期
const formatDate = (datetime: string) => {
  return dayjs(datetime).format('MM-DD HH:mm')
}

// 格式化时间
const formatTime = (datetime: string) => {
  return dayjs(datetime).format('HH:mm')
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
    
    const response = await getExperimentRecordsApi(params)
    tableData.value = response.data.data
    
    // 更新分页信息
    Object.assign(pagination, response.data)
  } catch (error) {
    console.error('加载实验记录失败:', error)
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
    sortData.sort_field = 'start_time'
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

// 开始实验
const handleCreate = () => {
  currentRecord.value = null
  dialogMode.value = 'create'
  dialogVisible.value = true
}

// 编辑
const handleEdit = (record: ExperimentRecord) => {
  currentRecord.value = record
  dialogMode.value = 'edit'
  dialogVisible.value = true
}

// 查看详情
const handleView = (record: ExperimentRecord) => {
  currentRecord.value = record
  detailVisible.value = true
}

// 完成实验
const handleComplete = (record: ExperimentRecord) => {
  currentRecord.value = record
  completeVisible.value = true
}

// 查看照片
const handleViewPhotos = (record: ExperimentRecord) => {
  currentPhotos.value = record.photos || []
  photoVisible.value = true
}

// 导出数据
const handleExport = () => {
  ElMessage.info('导出功能开发中...')
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 完成成功回调
const handleCompleteSuccess = () => {
  loadData()
}

onMounted(() => {
  loadData()
  loadSchools()
  loadLaboratories()
})
</script>

<style scoped>
.experiment-records {
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

.text-secondary {
  color: #909399;
  font-size: 12px;
}

.progress-text {
  margin-left: 8px;
  font-size: 12px;
  color: #606266;
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
