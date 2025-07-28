<template>
  <div class="school-class-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>班级管理</h2>
        <p>按组织架构管理学校班级信息和班级配置</p>
      </div>
      <div class="header-actions">
        <el-button @click="refreshData" :loading="loading">
          <el-icon><Refresh /></el-icon>
          刷新
        </el-button>
      </div>
    </div>

    <!-- 主要内容区域 -->
    <div class="main-content">
      <!-- 左侧组织架构 -->
      <div class="sidebar">
        <div class="sidebar-header">
          <h3>
            <el-icon><OfficeBuilding /></el-icon>
            组织架构
          </h3>
          <div class="sidebar-actions">
            <el-button size="small" circle @click="refreshOrganization">
              <el-icon><Refresh /></el-icon>
            </el-button>
            <el-button size="small" circle @click="expandAll">
              <el-icon><Plus /></el-icon>
            </el-button>
            <el-button size="small" circle @click="collapseAll">
              <el-icon><Minus /></el-icon>
            </el-button>
          </div>
        </div>

        <div class="search-box">
          <el-input
            v-model="organizationSearchText"
            placeholder="搜索组织"
            clearable
            size="small"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
        </div>

        <div class="organization-tree">
          <OrganizationTree
            :selected-node="selectedOrganization"
            :search-text="organizationSearchText"
            @node-click="handleOrganizationSelect"
          />
        </div>
      </div>

      <!-- 右侧内容区域 -->
      <div class="content-area">
        <!-- 选中组织信息 -->
        <div v-if="selectedOrganization" class="selected-info">
          <div class="info-card">
            <div class="card-header">
              <h3>{{ selectedOrganization.name }}</h3>
              <el-tag :type="getOrganizationTagType(selectedOrganization.level)">
                {{ selectedOrganization.level_name }}
              </el-tag>
            </div>
            <div class="stats-row">
              <div class="stat-item">
                <span class="stat-label">班级总数</span>
                <span class="stat-value">{{ pagination.total }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">学生总数</span>
                <span class="stat-value">{{ totalStudents }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">平均班级规模</span>
                <span class="stat-value">{{ averageClassSize }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 操作栏 -->
        <div class="toolbar">
          <div class="toolbar-left">
            <el-form :model="searchParams" inline>
              <el-form-item label="年级">
                <el-select v-model="searchParams.grade" placeholder="请选择年级" clearable style="width: 150px">
                  <el-option
                    v-for="option in gradeOptions"
                    :key="option.value"
                    :label="option.label"
                    :value="option.value"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="状态">
                <el-select v-model="searchParams.status" placeholder="请选择状态" clearable style="width: 120px">
                  <el-option label="正常" :value="1" />
                  <el-option label="停用" :value="0" />
                </el-select>
              </el-form-item>
              <el-form-item>
                <el-input
                  v-model="searchParams.search"
                  placeholder="搜索班级名称"
                  style="width: 200px"
                  clearable
                  @keyup.enter="fetchClassList"
                >
                  <template #prefix>
                    <el-icon><Search /></el-icon>
                  </template>
                </el-input>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="fetchClassList">搜索</el-button>
                <el-button @click="resetSearch">重置</el-button>
              </el-form-item>
            </el-form>
          </div>

          <div class="toolbar-right">
            <el-button type="primary" @click="handleCreate">
              <el-icon><Plus /></el-icon>
              新增班级
            </el-button>
            <el-button type="success" @click="handleBatchCreate">
              <el-icon><Plus /></el-icon>
              批量创建
            </el-button>
          </div>
        </div>

        <!-- 班级列表 -->
        <div class="data-table">
          <el-table
            v-loading="loading"
            :data="classList"
            stripe
            style="width: 100%"
          >
            <el-table-column prop="name" label="班级名称" width="150" />
            <el-table-column prop="code" label="班级代码" width="120" />
            <el-table-column label="学校" width="200">
              <template #default="{ row }">
                {{ row.school?.name }}
              </template>
            </el-table-column>
            <el-table-column prop="grade" label="年级" width="80">
              <template #default="{ row }">
                {{ getGradeName(row.grade) }}
              </template>
            </el-table-column>
            <el-table-column prop="student_count" label="学生人数" width="100" />
            <el-table-column prop="classroom_location" label="教室位置" width="120" />
            <el-table-column label="班主任" width="120">
              <template #default="{ row }">
                {{ row.head_teacher?.real_name || '-' }}
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="80">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'danger'">
                  {{ row.status === 1 ? '正常' : '停用' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column label="操作" width="200" fixed="right">
              <template #default="{ row }">
                <el-button type="primary" size="small" @click="handleEdit(row)">
                  编辑
                </el-button>
                <el-button type="danger" size="small" @click="handleDelete(row)">
                  删除
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
        </div>
      </div>
    </div>

    <!-- 新增/编辑班级对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="isEdit ? '编辑班级' : '新增班级'"
      width="500px"
    >
      <el-form
        ref="formRef"
        :model="classForm"
        :rules="formRules"
        label-width="100px"
      >
        <el-form-item label="学校" prop="school_id">
          <el-select
            v-model="classForm.school_id"
            placeholder="请选择学校"
            style="width: 100%"
            :disabled="isEdit"
          >
            <el-option
              v-for="school in schools"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="年级" prop="grade">
          <el-select
            v-model="classForm.grade"
            placeholder="请选择年级"
            style="width: 100%"
          >
            <el-option
              v-for="grade in gradeOptions"
              :key="grade.value"
              :label="grade.label"
              :value="grade.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="班级序号" prop="class_number">
          <el-input-number
            v-model="classForm.class_number"
            :min="1"
            :max="20"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="学生人数" prop="student_count">
          <el-input-number
            v-model="classForm.student_count"
            :min="0"
            :max="100"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="教室位置" prop="classroom_location">
          <el-input
            v-model="classForm.classroom_location"
            placeholder="请输入教室位置"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>

    <!-- 批量创建对话框 -->
    <el-dialog
      v-model="batchCreateDialogVisible"
      title="批量创建班级"
      width="600px"
    >
      <el-form
        ref="batchFormRef"
        :model="batchForm"
        :rules="batchFormRules"
        label-width="100px"
      >
        <el-form-item label="学校" prop="school_id">
          <el-select
            v-model="batchForm.school_id"
            placeholder="请选择学校"
            style="width: 100%"
          >
            <el-option
              v-for="school in schools"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="年级配置" prop="grades">
          <div v-for="(grade, index) in batchForm.grades" :key="index" class="grade-config">
            <el-select
              v-model="grade.grade"
              placeholder="选择年级"
              style="width: 120px; margin-right: 10px"
            >
              <el-option
                v-for="gradeOption in gradeOptions"
                :key="gradeOption.value"
                :label="gradeOption.label"
                :value="gradeOption.value"
              />
            </el-select>
            <el-input-number
              v-model="grade.class_count"
              :min="1"
              :max="10"
              placeholder="班级数"
              style="width: 120px; margin-right: 10px"
            />
            <el-button
              type="danger"
              size="small"
              @click="removeGradeConfig(index)"
              v-if="batchForm.grades.length > 1"
            >
              删除
            </el-button>
          </div>
          <el-button type="primary" size="small" @click="addGradeConfig">
            添加年级
          </el-button>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="batchCreateDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitBatchForm" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Refresh, Search, OfficeBuilding, Minus } from '@element-plus/icons-vue'
import {
  getSchoolClassListApi,
  createSchoolClassApi,
  updateSchoolClassApi,
  deleteSchoolClassApi,
  batchCreateSchoolClassApi,
  type SchoolClass,
  type CreateSchoolClassParams,
  type UpdateSchoolClassParams,
  type BatchCreateClassParams
} from '@/api/schoolClass'
import { getSchoolOptionsApi, type School } from '@/api/school'
import OrganizationTree from '@/components/OrganizationTree.vue'
import type { OrganizationNode } from '@/types/organization'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const editDialogVisible = ref(false)
const batchCreateDialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()
const batchFormRef = ref<FormInstance>()

// 列表数据
const classList = ref<SchoolClass[]>([])
const schools = ref<School[]>([])

// 组织架构相关
const selectedOrganization = ref<OrganizationNode | null>(null)
const organizationSearchText = ref('')

// 搜索参数
const searchParams = reactive({
  school_id: undefined as number | undefined,
  grade: undefined as number | undefined,
  search: '',
  status: undefined as number | undefined,
  page: 1,
  per_page: 20
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0,
  last_page: 1
})

// 表单数据
const classForm = reactive<CreateSchoolClassParams>({
  school_id: 0,
  grade: 1,
  class_number: 1,
  student_count: 0,
  classroom_location: ''
})

// 批量创建表单
const batchForm = reactive<BatchCreateClassParams>({
  school_id: 0,
  grades: [{ grade: 1, class_count: 1 }]
})

// 年级选项
const gradeOptions = [
  { value: 1, label: '一年级' },
  { value: 2, label: '二年级' },
  { value: 3, label: '三年级' },
  { value: 4, label: '四年级' },
  { value: 5, label: '五年级' },
  { value: 6, label: '六年级' },
  { value: 7, label: '七年级' },
  { value: 8, label: '八年级' },
  { value: 9, label: '九年级' }
]

// 表单验证规则
const formRules: FormRules = {
  school_id: [{ required: true, message: '请选择学校', trigger: 'change' }],
  grade: [{ required: true, message: '请选择年级', trigger: 'change' }],
  class_number: [{ required: true, message: '请输入班级序号', trigger: 'blur' }]
}

const batchFormRules: FormRules = {
  school_id: [{ required: true, message: '请选择学校', trigger: 'change' }],
  grades: [{ required: true, message: '请配置年级信息', trigger: 'change' }]
}

// 计算属性
const totalStudents = computed(() => {
  return classList.value.reduce((total, cls) => total + (cls.student_count || 0), 0)
})

const averageClassSize = computed(() => {
  if (classList.value.length === 0) return 0
  return Math.round(totalStudents.value / classList.value.length)
})

// 获取组织标签类型
const getOrganizationTagType = (level: number) => {
  const types = ['', 'danger', 'warning', 'info', 'success', 'primary']
  return types[level] || 'info'
}

// 方法
const getGradeName = (grade: number) => {
  const gradeOption = gradeOptions.find(option => option.value === grade)
  return gradeOption ? gradeOption.label : `${grade}年级`
}

const fetchSchools = async () => {
  try {
    const response = await getSchoolOptionsApi()
    if (response.data) {
      schools.value = response.data
    }
  } catch (error) {
    console.error('获取学校列表失败:', error)
  }
}

const fetchClassList = async () => {
  loading.value = true
  try {
    const response = await getSchoolClassListApi({
      ...searchParams,
      page: pagination.current_page,
      per_page: pagination.per_page
    })
    
    if (response.data) {
      if (Array.isArray(response.data)) {
        classList.value = response.data
      } else {
        classList.value = response.data.data
        pagination.current_page = response.data.current_page
        pagination.per_page = response.data.per_page
        pagination.total = response.data.total
        pagination.last_page = response.data.last_page
      }
    }
  } catch (error) {
    console.error('获取班级列表失败:', error)
    ElMessage.error('获取班级列表失败')
  } finally {
    loading.value = false
  }
}

const handleSchoolChange = () => {
  pagination.current_page = 1
  fetchClassList()
}

const handleSearch = () => {
  pagination.current_page = 1
  fetchClassList()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchClassList()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchClassList()
}

// 组织架构相关方法
const handleOrganizationSelect = (node: OrganizationNode) => {
  selectedOrganization.value = node

  // 根据选中的组织节点设置搜索条件
  if (node.level === 5) { // 学校级别
    searchParams.school_id = node.id
  } else {
    searchParams.school_id = undefined
  }

  // 重新加载数据
  pagination.current_page = 1
  fetchClassList()
}

const refreshOrganization = () => {
  // 刷新组织架构树
  organizationSearchText.value = ''
}

const expandAll = () => {
  // 展开所有节点的逻辑
}

const collapseAll = () => {
  // 收起所有节点的逻辑
}

const resetSearch = () => {
  Object.assign(searchParams, {
    grade: undefined,
    search: '',
    status: undefined
  })
  pagination.current_page = 1
  fetchClassList()
}

const handleCreate = () => {
  isEdit.value = false
  resetForm()
  editDialogVisible.value = true
}

const handleEdit = (row: SchoolClass) => {
  isEdit.value = true
  Object.assign(classForm, {
    school_id: row.school_id,
    grade: row.grade,
    class_number: row.class_number,
    student_count: row.student_count,
    classroom_location: row.classroom_location
  })
  editDialogVisible.value = true
}

const handleDelete = async (row: SchoolClass) => {
  try {
    await ElMessageBox.confirm('确定要删除这个班级吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    await deleteSchoolClassApi(row.id)
    ElMessage.success('删除成功')
    fetchClassList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除班级失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

const resetForm = () => {
  Object.assign(classForm, {
    school_id: 0,
    grade: 1,
    class_number: 1,
    student_count: 0,
    classroom_location: ''
  })
  formRef.value?.clearValidate()
}

const submitForm = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    if (isEdit.value) {
      // 编辑逻辑需要获取当前编辑的班级ID
      // 这里需要在handleEdit中保存当前编辑的班级
      ElMessage.warning('编辑功能待完善')
    } else {
      await createSchoolClassApi(classForm)
      ElMessage.success('创建成功')
      editDialogVisible.value = false
      fetchClassList()
    }
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('提交失败')
  } finally {
    submitting.value = false
  }
}

const handleBatchCreate = () => {
  batchForm.school_id = 0
  batchForm.grades = [{ grade: 1, class_count: 1 }]
  batchCreateDialogVisible.value = true
}

const addGradeConfig = () => {
  batchForm.grades.push({ grade: 1, class_count: 1 })
}

const removeGradeConfig = (index: number) => {
  batchForm.grades.splice(index, 1)
}

const submitBatchForm = async () => {
  if (!batchFormRef.value) return
  
  try {
    await batchFormRef.value.validate()
    submitting.value = true
    
    await batchCreateSchoolClassApi(batchForm)
    ElMessage.success('批量创建成功')
    batchCreateDialogVisible.value = false
    fetchClassList()
  } catch (error) {
    console.error('批量创建失败:', error)
    ElMessage.error('批量创建失败')
  } finally {
    submitting.value = false
  }
}

const refreshData = () => {
  fetchClassList()
}

// 生命周期
onMounted(() => {
  fetchSchools()
  fetchClassList()
})
</script>

<style scoped>
.school-class-management {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: white;
  border-bottom: 1px solid #e4e7ed;
}

.header-content h2 {
  margin: 0 0 4px 0;
  font-size: 20px;
  font-weight: 600;
  color: #303133;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.main-content {
  flex: 1;
  display: flex;
  min-height: 0;
}

.sidebar {
  width: 300px;
  background: white;
  border-right: 1px solid #e4e7ed;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #e4e7ed;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
  display: flex;
  align-items: center;
  gap: 8px;
}

.sidebar-actions {
  display: flex;
  gap: 4px;
}

.search-box {
  padding: 16px 20px;
  border-bottom: 1px solid #e4e7ed;
}

.organization-tree {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
}

.content-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #f5f7fa;
  overflow: hidden;
}

.selected-info {
  padding: 20px 24px;
  background: white;
  border-bottom: 1px solid #e4e7ed;
}

.info-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  padding: 24px;
  color: white;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
}

.stats-row {
  display: flex;
  gap: 32px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-label {
  font-size: 14px;
  opacity: 0.9;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: white;
  border-bottom: 1px solid #e4e7ed;
}

.toolbar-left {
  display: flex;
  align-items: center;
}

.toolbar-right {
  display: flex;
  gap: 8px;
}

.data-table {
  flex: 1;
  padding: 24px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.pagination-wrapper {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}

.grade-config {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}
</style>
