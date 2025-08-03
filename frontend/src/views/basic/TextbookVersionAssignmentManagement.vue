<template>
  <div class="textbook-assignment-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">教材版本指定管理</h1>
        <p class="page-description">统一管理下属学校的教材版本选择，确保实验教学标准化</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="showCreateDialog">
          新增指定
        </el-button>
        <el-button type="success" :icon="Document" @click="showBatchDialog">
          批量指定
        </el-button>
        <el-button type="info" :icon="Collection" @click="showTemplateDialog">
          模板管理
        </el-button>
      </div>
    </div>

    <!-- 统计卡片 -->
    <div class="statistics-cards">
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-number">{{ statistics.total_schools }}</div>
              <div class="stat-label">管辖学校总数</div>
            </div>
            <el-icon class="stat-icon" color="#409EFF"><School /></el-icon>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-number">{{ statistics.assigned_schools }}</div>
              <div class="stat-label">已指定学校</div>
            </div>
            <el-icon class="stat-icon" color="#67C23A"><Check /></el-icon>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-number">{{ statistics.unassigned_schools }}</div>
              <div class="stat-label">未指定学校</div>
            </div>
            <el-icon class="stat-icon" color="#F56C6C"><Warning /></el-icon>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-number">{{ statistics.active_assignments }}</div>
              <div class="stat-label">生效指定数</div>
            </div>
            <el-icon class="stat-icon" color="#E6A23C"><Star /></el-icon>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <!-- 筛选区域 -->
    <el-card class="filter-card">
      <el-form :model="searchForm" inline>
        <el-form-item label="学校">
          <el-select
            v-model="searchForm.school_id"
            placeholder="请选择学校"
            filterable
            clearable
            style="width: 200px"
            @change="handleSearch"
          >
            <el-option
              v-for="school in schools"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学科">
          <el-select
            v-model="searchForm.subject_id"
            placeholder="请选择学科"
            clearable
            style="width: 150px"
            @change="handleSearch"
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
            v-model="searchForm.grade_level"
            placeholder="请选择年级"
            clearable
            style="width: 120px"
            @change="handleSearch"
          >
            <el-option
              v-for="grade in gradeOptions"
              :key="grade.value"
              :label="grade.label"
              :value="grade.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="状态">
          <el-select
            v-model="searchForm.status"
            placeholder="请选择状态"
            clearable
            style="width: 120px"
            @change="handleSearch"
          >
            <el-option label="生效中" value="active" />
            <el-option label="已失效" value="0" />
          </el-select>
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
    </el-card>

    <!-- 数据表格 -->
    <el-card class="table-card">
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        style="width: 100%"
      >
        <el-table-column prop="school_name" label="学校名称" width="200" />
        <el-table-column prop="subject_name" label="学科" width="100" />
        <el-table-column prop="grade_level" label="年级" width="80" />
        <el-table-column prop="textbook_version_name" label="教材版本" width="150" />
        <el-table-column prop="publisher" label="出版社" width="120" />
        <el-table-column prop="assigner_name" label="指定人" width="100" />
        <el-table-column prop="assigner_user.name" label="指定人" width="100" />
        <el-table-column prop="effective_date" label="生效日期" width="120">
          <template #default="{ row }">
            {{ formatDate(row.effective_date) }}
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status_name }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.status === 1"
              type="warning"
              size="small"
              @click="handleRevoke(row)"
            >
              撤销
            </el-button>
            <el-button
              type="primary"
              size="small"
              @click="handleViewHistory(row)"
            >
              历史
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-wrapper">
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
    </el-card>

    <!-- 新增指定对话框 -->
    <CreateAssignmentDialog
      v-model="createDialogVisible"
      :schools="schools"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
      @success="handleCreateSuccess"
    />

    <!-- 批量指定对话框 -->
    <BatchAssignmentDialog
      v-model="batchDialogVisible"
      :schools="schools"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
      :templates="templates"
      @success="handleBatchSuccess"
    />

    <!-- 模板管理对话框 -->
    <TemplateManagementDialog
      v-model="templateDialogVisible"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
      @success="handleTemplateSuccess"
    />

    <!-- 撤销确认对话框 -->
    <RevokeAssignmentDialog
      v-model="revokeDialogVisible"
      :assignment="currentAssignment"
      @success="handleRevokeSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage } from 'element-plus'
import {
  Plus, Document, Collection, Search, Refresh, School,
  Check, Warning, Star
} from '@element-plus/icons-vue'
import { textbookAssignmentApi, textbookAssignmentTemplateApi } from '@/api/textbookAssignment'
import { getSubjectsApi, getTextbookVersionOptionsApi } from '@/api/experiment'
import type {
  TextbookVersionAssignment,
  TextbookAssignmentTemplate,
  School as SchoolType,
  AssignmentListParams
} from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'
import CreateAssignmentDialog from './components/CreateAssignmentDialog.vue'
import BatchAssignmentDialog from './components/BatchAssignmentDialog.vue'
import TemplateManagementDialog from './components/TemplateManagementDialog.vue'
import RevokeAssignmentDialog from './components/RevokeAssignmentDialog.vue'
import { formatDate } from '@/utils/date'

// 响应式数据
const loading = ref(false)
const schools = ref<SchoolType[]>([])
const subjects = ref<Subject[]>([])
const textbookVersions = ref<TextbookVersion[]>([])
const templates = ref<TextbookAssignmentTemplate[]>([])
const tableData = ref<TextbookVersionAssignment[]>([])
const currentAssignment = ref<TextbookVersionAssignment | null>(null)

// 统计数据
const statistics = ref({
  total_schools: 0,
  assigned_schools: 0,
  unassigned_schools: 0,
  total_assignments: 0,
  active_assignments: 0
})

// 搜索表单
const searchForm = reactive<AssignmentListParams>({
  school_id: 0,
  subject_id: undefined,
  grade_level: undefined,
  status: undefined
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 对话框显示状态
const createDialogVisible = ref(false)
const batchDialogVisible = ref(false)
const templateDialogVisible = ref(false)
const revokeDialogVisible = ref(false)

// 年级选项
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

// 生命周期
onMounted(() => {
  loadInitialData()
})

// 方法
const loadInitialData = async () => {
  await Promise.all([
    loadSchools(),
    loadSubjects(),
    loadTextbookVersions(),
    loadTemplates(),
    loadStatistics(),
    loadAssignments() // 直接加载所有可管理学校的指定记录
  ])
}

const loadSchools = async () => {
  try {
    const response = await textbookAssignmentApi.getManageableSchools()
    schools.value = response.data.data || response.data
  } catch (error) {
    console.error('加载学校列表失败:', error)
    ElMessage.error('加载学校列表失败')
  }
}

const loadSubjects = async () => {
  try {
    const response = await getSubjectsApi()
    subjects.value = response.data.data || response.data
  } catch (error) {
    console.error('加载学科列表失败:', error)
  }
}

const loadTextbookVersions = async () => {
  try {
    const response = await getTextbookVersionOptionsApi()
    textbookVersions.value = response.data.data || response.data
  } catch (error) {
    console.error('加载教材版本列表失败:', error)
  }
}

const loadTemplates = async () => {
  try {
    const response = await textbookAssignmentTemplateApi.getOptions()
    templates.value = response.data.data || response.data
  } catch (error) {
    console.error('加载模板列表失败:', error)
  }
}

const loadStatistics = async () => {
  try {
    const response = await textbookAssignmentApi.getStatistics()
    statistics.value = response.data.data || response.data
  } catch (error) {
    console.error('加载统计数据失败:', error)
  }
}

const loadAssignments = async () => {
  loading.value = true
  try {
    const params = {
      // 不传入school_id，获取所有可管理学校的指定记录
      subject_id: searchForm.subject_id,
      grade_level: searchForm.grade_level,
      status: searchForm.status,
      page: pagination.current_page,
      per_page: pagination.per_page
    }

    const response = await textbookAssignmentApi.getAssignments(params)
    const data = response.data.data || response.data

    tableData.value = data.items || data
    pagination.total = data.pagination?.total || 0
  } catch (error) {
    console.error('加载指定记录失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.current_page = 1
  loadAssignments()
}

const handleReset = () => {
  Object.assign(searchForm, {
    school_id: 0, // 保留字段但不使用
    subject_id: undefined,
    grade_level: undefined,
    status: undefined
  })
  pagination.current_page = 1
  loadAssignments()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadAssignments()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadAssignments()
}

// 对话框相关方法
const showCreateDialog = () => {
  createDialogVisible.value = true
}

const showBatchDialog = () => {
  batchDialogVisible.value = true
}

const showTemplateDialog = () => {
  templateDialogVisible.value = true
}

const handleCreateSuccess = () => {
  loadAssignments()
  loadStatistics()
}

const handleBatchSuccess = () => {
  loadAssignments()
  loadStatistics()
}

const handleTemplateSuccess = () => {
  loadTemplates()
}

const handleRevoke = (assignment: TextbookVersionAssignment) => {
  currentAssignment.value = assignment
  revokeDialogVisible.value = true
}

const handleRevokeSuccess = () => {
  loadAssignments()
  loadStatistics()
}

const handleViewHistory = (assignment: TextbookVersionAssignment) => {
  // TODO: 实现查看历史记录功能
  ElMessage.info('历史记录功能开发中')
}
</script>

<style scoped>
.textbook-assignment-management {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  margin: 0 0 8px 0;
}

.page-description {
  color: #606266;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.statistics-cards {
  margin-bottom: 20px;
}

.stat-card {
  position: relative;
  overflow: hidden;
}

.stat-card :deep(.el-card__body) {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: 600;
  color: #303133;
  line-height: 1;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.stat-icon {
  font-size: 32px;
  opacity: 0.8;
}

.filter-card {
  margin-bottom: 20px;
}

.table-card {
  margin-bottom: 20px;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
</style>
