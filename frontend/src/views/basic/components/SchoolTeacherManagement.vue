<template>
  <div class="school-teacher-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>教师管理</h2>
        <p>按组织架构管理学校教师信息和教学配置</p>
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
                <span class="stat-label">教师总数</span>
                <span class="stat-value">{{ pagination.total }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">学科数量</span>
                <span class="stat-value">{{ uniqueSubjects }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">平均教龄</span>
                <span class="stat-value">{{ averageExperience }}年</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 操作栏 -->
        <div class="toolbar">
          <div class="toolbar-left">
            <el-form :model="searchParams" inline>
              <el-form-item label="学科">
                <el-select v-model="searchParams.subject" placeholder="请选择学科" clearable style="width: 150px">
                  <el-option
                    v-for="subject in subjectOptions"
                    :key="subject"
                    :label="subject"
                    :value="subject"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="状态">
                <el-select v-model="searchParams.status" placeholder="请选择状态" clearable style="width: 120px">
                  <el-option label="在职" :value="1" />
                  <el-option label="离职" :value="0" />
                </el-select>
              </el-form-item>
              <el-form-item>
                <el-input
                  v-model="searchParams.search"
                  placeholder="搜索教师姓名"
                  style="width: 200px"
                  clearable
                  @keyup.enter="fetchTeacherList"
                >
                  <template #prefix>
                    <el-icon><Search /></el-icon>
                  </template>
                </el-input>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="fetchTeacherList">搜索</el-button>
                <el-button @click="resetSearch">重置</el-button>
              </el-form-item>
            </el-form>
          </div>

          <div class="toolbar-right">
            <el-button type="primary" @click="handleCreate">
              <el-icon><Plus /></el-icon>
              新增教师
            </el-button>
            <el-button type="success" @click="handleBatchImport">
              <el-icon><Upload /></el-icon>
              批量导入
            </el-button>
          </div>
        </div>

        <!-- 教师列表 -->
        <div class="data-table">
          <el-table
            v-loading="loading"
            :data="teacherList"
            stripe
            style="width: 100%"
          >
            <el-table-column label="教师姓名" width="120">
              <template #default="{ row }">
                {{ row.user?.real_name }}
              </template>
            </el-table-column>
            <el-table-column prop="employee_number" label="工号" width="120" />
            <el-table-column label="学校" width="200">
              <template #default="{ row }">
                {{ row.school?.name }}
              </template>
            </el-table-column>
            <el-table-column prop="subject" label="任教学科" width="100" />
            <el-table-column label="任教年级" width="150">
              <template #default="{ row }">
                <el-tag
                  v-for="grade in row.teaching_grade_names"
                  :key="grade"
                  size="small"
                  style="margin-right: 4px"
                >
                  {{ grade }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="title" label="职称" width="100" />
            <el-table-column prop="education" label="学历" width="80" />
            <el-table-column label="联系方式" width="120">
              <template #default="{ row }">
                {{ row.user?.phone || '-' }}
              </template>
            </el-table-column>
            <el-table-column prop="join_date" label="入职日期" width="120" />
            <el-table-column prop="status" label="状态" width="80">
              <template #default="{ row }">
                <el-tag :type="getStatusType(row.status)">
                  {{ row.status_name }}
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

    <!-- 新增/编辑教师对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="isEdit ? '编辑教师' : '新增教师'"
      width="600px"
    >
      <el-form
        ref="formRef"
        :model="teacherForm"
        :rules="formRules"
        label-width="100px"
      >
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="学校" prop="school_id">
              <el-select
                v-model="teacherForm.school_id"
                placeholder="请选择学校"
                style="width: 100%"
                :disabled="isEdit"
                @change="handleFormSchoolChange"
              >
                <el-option
                  v-for="school in schools"
                  :key="school.id"
                  :label="school.name"
                  :value="school.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="选择用户" prop="user_id" v-if="!isEdit">
              <el-select
                v-model="teacherForm.user_id"
                placeholder="请选择用户"
                style="width: 100%"
                filterable
                remote
                :remote-method="searchUsers"
                :loading="userSearchLoading"
              >
                <el-option
                  v-for="user in availableUsers"
                  :key="user.id"
                  :label="`${user.real_name} (${user.username})`"
                  :value="user.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="工号" prop="employee_number">
              <el-input
                v-model="teacherForm.employee_number"
                placeholder="请输入工号"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="任教学科" prop="subject">
              <el-select
                v-model="teacherForm.subject"
                placeholder="请选择学科"
                style="width: 100%"
              >
                <el-option
                  v-for="subject in subjectOptions"
                  :key="subject"
                  :label="subject"
                  :value="subject"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="任教年级" prop="teaching_grades">
          <el-checkbox-group v-model="teacherForm.teaching_grades">
            <el-checkbox
              v-for="grade in gradeOptions"
              :key="grade.value"
              :label="grade.value"
            >
              {{ grade.label }}
            </el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="职称" prop="title">
              <el-select
                v-model="teacherForm.title"
                placeholder="请选择职称"
                style="width: 100%"
              >
                <el-option
                  v-for="title in titleOptions"
                  :key="title"
                  :label="title"
                  :value="title"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="学历" prop="education">
              <el-select
                v-model="teacherForm.education"
                placeholder="请选择学历"
                style="width: 100%"
              >
                <el-option
                  v-for="education in educationOptions"
                  :key="education"
                  :label="education"
                  :value="education"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="入职日期" prop="join_date">
          <el-date-picker
            v-model="teacherForm.join_date"
            type="date"
            placeholder="请选择入职日期"
            style="width: 100%"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Refresh, Search, Upload, OfficeBuilding, Minus } from '@element-plus/icons-vue'
import {
  getSchoolTeacherListApi,
  createSchoolTeacherApi,
  updateSchoolTeacherApi,
  deleteSchoolTeacherApi,
  getAvailableUsersApi,
  type SchoolTeacher,
  type CreateSchoolTeacherParams,
  type UpdateSchoolTeacherParams,
  type AvailableUser
} from '@/api/schoolTeacher'
import { getSchoolOptionsApi, type School } from '@/api/school'
import OrganizationTree from '@/components/OrganizationTree.vue'
import type { OrganizationNode } from '@/types/organization'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const userSearchLoading = ref(false)
const editDialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()

// 列表数据
const teacherList = ref<SchoolTeacher[]>([])
const schools = ref<School[]>([])
const availableUsers = ref<AvailableUser[]>([])

// 组织架构相关
const selectedOrganization = ref<OrganizationNode | null>(null)
const organizationSearchText = ref('')

// 搜索参数
const searchParams = reactive({
  school_id: undefined as number | undefined,
  subject: undefined as string | undefined,
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
const teacherForm = reactive<CreateSchoolTeacherParams>({
  school_id: 0,
  user_id: 0,
  employee_number: '',
  subject: '',
  teaching_grades: [],
  title: '',
  education: '',
  join_date: ''
})

// 选项数据
const subjectOptions = [
  '语文', '数学', '英语', '物理', '化学', '生物',
  '历史', '地理', '政治', '音乐', '美术', '体育',
  '信息技术', '科学', '道德与法治'
]

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

const titleOptions = [
  '助教', '讲师', '副教授', '教授', '特级教师', 
  '高级教师', '中级教师', '初级教师'
]

const educationOptions = ['专科', '本科', '硕士', '博士']

// 表单验证规则
const formRules: FormRules = {
  school_id: [{ required: true, message: '请选择学校', trigger: 'change' }],
  user_id: [{ required: true, message: '请选择用户', trigger: 'change' }]
}

// 计算属性
const uniqueSubjects = computed(() => {
  const subjects = new Set(teacherList.value.map(teacher => teacher.subject).filter(Boolean))
  return subjects.size
})

const averageExperience = computed(() => {
  if (teacherList.value.length === 0) return 0
  const currentYear = new Date().getFullYear()
  const totalExperience = teacherList.value.reduce((total, teacher) => {
    if (teacher.join_date) {
      const joinYear = new Date(teacher.join_date).getFullYear()
      return total + (currentYear - joinYear)
    }
    return total
  }, 0)
  return Math.round(totalExperience / teacherList.value.length)
})

// 获取组织标签类型
const getOrganizationTagType = (level: number) => {
  const types = ['', 'danger', 'warning', 'info', 'success', 'primary']
  return types[level] || 'info'
}

// 方法
const getStatusType = (status: number) => {
  switch (status) {
    case 1: return 'success'
    case 2: return 'warning'
    default: return 'danger'
  }
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

const fetchTeacherList = async () => {
  loading.value = true
  try {
    const response = await getSchoolTeacherListApi({
      ...searchParams,
      page: pagination.current_page,
      per_page: pagination.per_page
    })
    
    if (response.data) {
      if (Array.isArray(response.data)) {
        teacherList.value = response.data
      } else {
        teacherList.value = response.data.data
        pagination.current_page = response.data.current_page
        pagination.per_page = response.data.per_page
        pagination.total = response.data.total
        pagination.last_page = response.data.last_page
      }
    }
  } catch (error) {
    console.error('获取教师列表失败:', error)
    ElMessage.error('获取教师列表失败')
  } finally {
    loading.value = false
  }
}

const searchUsers = async (query: string) => {
  if (!teacherForm.school_id) {
    ElMessage.warning('请先选择学校')
    return
  }
  
  userSearchLoading.value = true
  try {
    const response = await getAvailableUsersApi({
      school_id: teacherForm.school_id,
      search: query
    })
    
    if (response.data) {
      availableUsers.value = response.data
    }
  } catch (error) {
    console.error('搜索用户失败:', error)
  } finally {
    userSearchLoading.value = false
  }
}

const handleSchoolChange = () => {
  pagination.current_page = 1
  fetchTeacherList()
}

const handleFormSchoolChange = () => {
  teacherForm.user_id = 0
  availableUsers.value = []
}

const handleSearch = () => {
  pagination.current_page = 1
  fetchTeacherList()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchTeacherList()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchTeacherList()
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
  fetchTeacherList()
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
    subject: undefined,
    search: '',
    status: undefined
  })
  pagination.current_page = 1
  fetchTeacherList()
}

const handleCreate = () => {
  isEdit.value = false
  resetForm()
  editDialogVisible.value = true
}

const handleEdit = (row: SchoolTeacher) => {
  isEdit.value = true
  Object.assign(teacherForm, {
    school_id: row.school_id,
    user_id: row.user_id,
    employee_number: row.employee_number,
    subject: row.subject,
    teaching_grades: row.teaching_grades || [],
    title: row.title,
    education: row.education,
    join_date: row.join_date
  })
  editDialogVisible.value = true
}

const handleDelete = async (row: SchoolTeacher) => {
  try {
    await ElMessageBox.confirm('确定要删除这个教师吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    await deleteSchoolTeacherApi(row.id)
    ElMessage.success('删除成功')
    fetchTeacherList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除教师失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

const handleBatchImport = () => {
  ElMessage.info('批量导入功能待开发')
}

const resetForm = () => {
  Object.assign(teacherForm, {
    school_id: 0,
    user_id: 0,
    employee_number: '',
    subject: '',
    teaching_grades: [],
    title: '',
    education: '',
    join_date: ''
  })
  availableUsers.value = []
  formRef.value?.clearValidate()
}

const submitForm = async () => {
  if (!formRef.value) return
  
  try {
    await formRef.value.validate()
    submitting.value = true
    
    if (isEdit.value) {
      ElMessage.warning('编辑功能待完善')
    } else {
      await createSchoolTeacherApi(teacherForm)
      ElMessage.success('创建成功')
      editDialogVisible.value = false
      fetchTeacherList()
    }
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error('提交失败')
  } finally {
    submitting.value = false
  }
}

const refreshData = () => {
  fetchTeacherList()
}

// 生命周期
onMounted(() => {
  fetchSchools()
  fetchTeacherList()
})
</script>

<style scoped>
.school-teacher-management {
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
</style>
