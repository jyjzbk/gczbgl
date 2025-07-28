<template>
  <div class="school-experiment-catalog">
    <!-- 页面标题 -->
    <div class="page-header">
      <h2>学校实验目录管理</h2>
      <p class="page-description">管理学校实验目录标准选择和删除权限配置</p>
    </div>

    <!-- 当前选择状态 -->
    <el-card class="selection-status" shadow="never" v-if="currentSelection">
      <template #header>
        <div class="card-header">
          <span>当前选择状态</span>
          <el-button type="primary" size="small" @click="showSelectionDialog = true">
            更改选择
          </el-button>
        </div>
      </template>
      <el-descriptions :column="2" border>
        <el-descriptions-item label="选择的标准">
          {{ currentSelection.standard_name }}
        </el-descriptions-item>
        <el-descriptions-item label="标准级别">
          <el-tag :type="getStandardLevelType(currentSelection.standard_level)">
            {{ getStandardLevelText(currentSelection.standard_level) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="选择时间">
          {{ formatDateTime(currentSelection.selected_at) }}
        </el-descriptions-item>
        <el-descriptions-item label="删除权限">
          <el-tag :type="currentSelection.can_delete ? 'success' : 'danger'">
            {{ currentSelection.can_delete ? '允许删除' : '不允许删除' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="选择理由" :span="2">
          {{ currentSelection.selection_reason || '无' }}
        </el-descriptions-item>
      </el-descriptions>
    </el-card>

    <!-- 无选择状态 -->
    <el-card class="no-selection" shadow="never" v-else>
      <el-empty description="尚未选择实验目录标准">
        <el-button type="primary" @click="showSelectionDialog = true">
          选择标准
        </el-button>
      </el-empty>
    </el-card>

    <!-- 可用实验目录 -->
    <el-card v-if="currentSelection">
      <template #header>
        <div class="card-header">
          <span>可用实验目录</span>
          <div class="header-actions">
            <el-button type="danger" size="small" @click="showDeletedCatalogs" v-if="currentSelection.can_delete">
              查看删除记录
            </el-button>
            <el-button type="primary" size="small" @click="loadCatalogs">
              <el-icon><Refresh /></el-icon>
              刷新
            </el-button>
          </div>
        </div>
      </template>

      <!-- 筛选条件 -->
      <div class="filter-section">
        <el-form :model="filters" inline>
          <el-form-item label="学科">
            <el-select v-model="filters.subject_id" placeholder="选择学科" clearable style="width: 150px">
              <el-option 
                v-for="subject in subjects" 
                :key="subject.id" 
                :label="subject.name" 
                :value="subject.id" 
              />
            </el-select>
          </el-form-item>
          <el-form-item label="年级">
            <el-select v-model="filters.grade" placeholder="选择年级" clearable style="width: 120px">
              <el-option label="一年级" value="1" />
              <el-option label="二年级" value="2" />
              <el-option label="三年级" value="3" />
              <el-option label="四年级" value="4" />
              <el-option label="五年级" value="5" />
              <el-option label="六年级" value="6" />
            </el-select>
          </el-form-item>
          <el-form-item label="学期">
            <el-select v-model="filters.semester" placeholder="选择学期" clearable style="width: 120px">
              <el-option label="上学期" value="1" />
              <el-option label="下学期" value="2" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="loadCatalogs" :loading="loading">
              <el-icon><Search /></el-icon>
              查询
            </el-button>
            <el-button @click="resetFilters">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 实验目录表格 -->
      <el-table :data="catalogs" style="width: 100%" :loading="loading">
        <el-table-column prop="code" label="实验编号" width="120" />
        <el-table-column prop="name" label="实验名称" min-width="200" />
        <el-table-column prop="subject_name" label="学科" width="100" />
        <el-table-column prop="grade" label="年级" width="80">
          <template #default="{ row }">
            {{ row.grade }}年级
          </template>
        </el-table-column>
        <el-table-column prop="semester" label="学期" width="80">
          <template #default="{ row }">
            {{ row.semester === 1 ? '上学期' : '下学期' }}
          </template>
        </el-table-column>
        <el-table-column prop="experiment_type" label="实验类型" width="100">
          <template #default="{ row }">
            <el-tag :type="getExperimentTypeTagType(row.experiment_type)">
              {{ row.experiment_type }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="management_level" label="管理级别" width="100">
          <template #default="{ row }">
            <el-tag :type="getManagementLevelType(row.management_level)">
              {{ getManagementLevelText(row.management_level) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" align="center">
          <template #default="{ row }">
            <el-button type="text" size="small" @click="viewCatalogDetail(row)">
              查看详情
            </el-button>
            <el-button 
              type="text" 
              size="small" 
              @click="deleteCatalog(row)" 
              v-if="currentSelection.can_delete && !row.is_deleted"
              style="color: #f56c6c"
            >
              删除
            </el-button>
            <el-button 
              type="text" 
              size="small" 
              @click="restoreCatalog(row)" 
              v-if="currentSelection.can_delete && row.is_deleted"
              style="color: #67c23a"
            >
              恢复
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-container">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>

    <!-- 选择标准对话框 -->
    <el-dialog
      v-model="showSelectionDialog"
      title="选择实验目录标准"
      width="600px"
    >
      <el-form :model="selectionForm" label-width="120px">
        <el-form-item label="可选标准" required>
          <el-select v-model="selectionForm.standard_id" placeholder="请选择标准" style="width: 100%">
            <el-option 
              v-for="standard in availableStandards" 
              :key="standard.id" 
              :label="standard.name" 
              :value="standard.id"
            >
              <div style="display: flex; justify-content: space-between;">
                <span>{{ standard.name }}</span>
                <el-tag :type="getStandardLevelType(standard.level)" size="small">
                  {{ getStandardLevelText(standard.level) }}
                </el-tag>
              </div>
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="申请删除权限">
          <el-switch v-model="selectionForm.request_delete_permission" />
          <div class="form-tip">
            开启后可以删除不适合的实验目录
          </div>
        </el-form-item>
        <el-form-item label="选择理由" required>
          <el-input
            v-model="selectionForm.selection_reason"
            type="textarea"
            :rows="3"
            placeholder="请说明选择此标准的理由..."
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showSelectionDialog = false">取消</el-button>
          <el-button type="primary" @click="confirmSelection" :loading="selecting">
            确认选择
          </el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 删除实验对话框 -->
    <el-dialog
      v-model="showDeleteDialog"
      title="删除实验目录"
      width="500px"
    >
      <div class="delete-warning">
        <el-icon class="warning-icon"><Warning /></el-icon>
        <p>确定要删除实验目录 <strong>{{ currentCatalog?.name }}</strong> 吗？</p>
        <p class="warning-text">删除后该实验将不会出现在您的实验计划中，但不会影响上级的统计数据。</p>
      </div>
      
      <el-form :model="deleteForm" label-width="100px">
        <el-form-item label="删除理由" required>
          <el-input
            v-model="deleteForm.delete_reason"
            type="textarea"
            :rows="3"
            placeholder="请说明删除此实验的理由..."
          />
        </el-form-item>
      </el-form>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showDeleteDialog = false">取消</el-button>
          <el-button type="danger" @click="confirmDelete" :loading="deleting">
            确认删除
          </el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 实验详情对话框 -->
    <el-dialog
      v-model="showDetailDialog"
      title="实验详情"
      width="800px"
    >
      <div v-if="currentCatalog" class="catalog-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="实验编号">
            {{ currentCatalog.code }}
          </el-descriptions-item>
          <el-descriptions-item label="实验名称">
            {{ currentCatalog.name }}
          </el-descriptions-item>
          <el-descriptions-item label="学科">
            {{ currentCatalog.subject_name }}
          </el-descriptions-item>
          <el-descriptions-item label="年级学期">
            {{ currentCatalog.grade }}年级{{ currentCatalog.semester === 1 ? '上学期' : '下学期' }}
          </el-descriptions-item>
          <el-descriptions-item label="实验类型">
            <el-tag :type="getExperimentTypeTagType(currentCatalog.experiment_type)">
              {{ currentCatalog.experiment_type }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="管理级别">
            <el-tag :type="getManagementLevelType(currentCatalog.management_level)">
              {{ getManagementLevelText(currentCatalog.management_level) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="实验目标" :span="2">
            {{ currentCatalog.objective || '无' }}
          </el-descriptions-item>
          <el-descriptions-item label="实验内容" :span="2">
            {{ currentCatalog.content || '无' }}
          </el-descriptions-item>
          <el-descriptions-item label="注意事项" :span="2">
            {{ currentCatalog.safety_notes || '无' }}
          </el-descriptions-item>
        </el-descriptions>
      </div>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="showDetailDialog = false">关闭</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Refresh, Warning } from '@element-plus/icons-vue'

// 响应式数据
const loading = ref(false)
const selecting = ref(false)
const deleting = ref(false)
const currentSelection = ref(null)
const availableStandards = ref([])
const catalogs = ref([])
const subjects = ref([])
const currentCatalog = ref(null)

// 对话框状态
const showSelectionDialog = ref(false)
const showDeleteDialog = ref(false)
const showDetailDialog = ref(false)

// 筛选条件
const filters = reactive({
  subject_id: '',
  grade: '',
  semester: ''
})

// 分页
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 表单数据
const selectionForm = reactive({
  standard_id: '',
  request_delete_permission: false,
  selection_reason: ''
})

const deleteForm = reactive({
  delete_reason: ''
})

// 生命周期
onMounted(() => {
  loadCurrentSelection()
  loadAvailableStandards()
  loadSubjects()
})

// 方法
const loadCurrentSelection = async () => {
  try {
    // 模拟API调用
    console.log('加载当前选择状态')
    // const response = await schoolExperimentCatalogApi.getCurrentSelection()
    // currentSelection.value = response.data.data
  } catch (error) {
    console.error('加载当前选择失败:', error)
  }
}

const loadAvailableStandards = async () => {
  try {
    // 模拟API调用
    console.log('加载可选标准')
    // const response = await schoolExperimentCatalogApi.getAvailableStandards()
    // availableStandards.value = response.data.data
  } catch (error) {
    console.error('加载可选标准失败:', error)
  }
}

const loadSubjects = async () => {
  try {
    // 模拟API调用
    console.log('加载学科列表')
    // const response = await subjectApi.getSubjects()
    // subjects.value = response.data.data
  } catch (error) {
    console.error('加载学科列表失败:', error)
  }
}

const loadCatalogs = async () => {
  if (!currentSelection.value) return

  try {
    loading.value = true
    // 模拟API调用
    console.log('加载实验目录', filters)
    // const response = await schoolExperimentCatalogApi.getAvailableCatalogs({
    //   ...filters,
    //   page: pagination.current_page,
    //   per_page: pagination.per_page
    // })
    // catalogs.value = response.data.data.data
    // pagination.total = response.data.data.total
  } catch (error) {
    console.error('加载实验目录失败:', error)
    ElMessage.error('加载实验目录失败')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  filters.subject_id = ''
  filters.grade = ''
  filters.semester = ''
  pagination.current_page = 1
  loadCatalogs()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadCatalogs()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadCatalogs()
}

const confirmSelection = async () => {
  if (!selectionForm.standard_id || !selectionForm.selection_reason.trim()) {
    ElMessage.warning('请填写完整信息')
    return
  }

  try {
    selecting.value = true
    // 模拟API调用
    console.log('确认选择标准', selectionForm)
    // const response = await schoolExperimentCatalogApi.setSelection(selectionForm)
    // if (response.data.success) {
    //   currentSelection.value = response.data.data
    //   showSelectionDialog.value = false
    //   ElMessage.success('选择成功')
    //   loadCatalogs()
    // }
    ElMessage.success('选择成功')
    showSelectionDialog.value = false
  } catch (error) {
    console.error('选择标准失败:', error)
    ElMessage.error('选择标准失败')
  } finally {
    selecting.value = false
  }
}

const viewCatalogDetail = (catalog: any) => {
  currentCatalog.value = catalog
  showDetailDialog.value = true
}

const deleteCatalog = (catalog: any) => {
  currentCatalog.value = catalog
  deleteForm.delete_reason = ''
  showDeleteDialog.value = true
}

const confirmDelete = async () => {
  if (!deleteForm.delete_reason.trim()) {
    ElMessage.warning('请填写删除理由')
    return
  }

  try {
    deleting.value = true
    // 模拟API调用
    console.log('删除实验目录', currentCatalog.value, deleteForm)
    // const response = await schoolExperimentCatalogApi.deleteCatalog({
    //   catalog_id: currentCatalog.value.id,
    //   delete_reason: deleteForm.delete_reason
    // })
    // if (response.data.success) {
    //   showDeleteDialog.value = false
    //   ElMessage.success('删除成功')
    //   loadCatalogs()
    // }
    ElMessage.success('删除成功')
    showDeleteDialog.value = false
    loadCatalogs()
  } catch (error) {
    console.error('删除实验目录失败:', error)
    ElMessage.error('删除实验目录失败')
  } finally {
    deleting.value = false
  }
}

const restoreCatalog = async (catalog: any) => {
  try {
    await ElMessageBox.confirm('确定要恢复此实验目录吗？', '确认恢复', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    // 模拟API调用
    console.log('恢复实验目录', catalog)
    // const response = await schoolExperimentCatalogApi.restoreCatalog(catalog.id)
    // if (response.data.success) {
    //   ElMessage.success('恢复成功')
    //   loadCatalogs()
    // }
    ElMessage.success('恢复成功')
    loadCatalogs()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('恢复实验目录失败:', error)
      ElMessage.error('恢复实验目录失败')
    }
  }
}

const showDeletedCatalogs = () => {
  ElMessage.info('查看删除记录功能开发中...')
}

// 工具方法
const getStandardLevelText = (level: number) => {
  const levelMap = {
    1: '省级',
    2: '市级',
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return levelMap[level] || '未知'
}

const getStandardLevelType = (level: number) => {
  const typeMap = {
    1: 'danger',
    2: 'warning',
    3: 'primary',
    4: 'info',
    5: 'success'
  }
  return typeMap[level] || 'info'
}

const getManagementLevelText = (level: number) => {
  return getStandardLevelText(level)
}

const getManagementLevelType = (level: number) => {
  return getStandardLevelType(level)
}

const getExperimentTypeTagType = (type: string) => {
  const typeMap = {
    '必做': 'danger',
    '选做': 'warning',
    '演示': 'info',
    '分组': 'success'
  }
  return typeMap[type] || 'info'
}

const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}
</script>

<style scoped>
.school-experiment-catalog {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.selection-status {
  margin-bottom: 20px;
}

.no-selection {
  margin-bottom: 20px;
  text-align: center;
  padding: 40px 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.filter-section {
  margin-bottom: 20px;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 6px;
}

.pagination-container {
  margin-top: 20px;
  text-align: right;
}

.delete-warning {
  text-align: center;
  margin-bottom: 20px;
}

.warning-icon {
  font-size: 48px;
  color: #f56c6c;
  margin-bottom: 16px;
}

.warning-text {
  color: #909399;
  font-size: 14px;
  margin-top: 8px;
}

.catalog-detail {
  margin-bottom: 20px;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .school-experiment-catalog {
    padding: 16px;
  }

  .card-header {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .header-actions {
    justify-content: center;
  }

  .filter-section .el-form {
    flex-direction: column;
  }

  .filter-section .el-form-item {
    margin-right: 0;
    margin-bottom: 12px;
  }

  .pagination-container {
    text-align: center;
  }
}
</style>
