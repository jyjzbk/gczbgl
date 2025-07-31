<template>
  <div class="school-management-page">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>学校管理</h2>
        <p>统一管理学校信息、班级、教师和实验室</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="handleCreate" v-if="activeTab === 'schools'">
          新增学校
        </el-button>
        <el-button type="primary" :icon="Plus" @click="handleCreateClass" v-if="activeTab === 'classes'">
          新增班级
        </el-button>
        <el-button type="primary" :icon="Plus" @click="handleCreateTeacher" v-if="activeTab === 'teachers'">
          新增教师
        </el-button>
        <el-button :icon="Refresh" @click="refreshData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 标签页导航 -->
    <div class="tabs-section">
      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <el-tab-pane label="学校信息" name="schools">
          <template #label>
            <span><el-icon><SchoolIcon /></el-icon> 学校信息</span>
          </template>
        </el-tab-pane>
        <el-tab-pane label="班级管理" name="classes">
          <template #label>
            <span><el-icon><UserFilled /></el-icon> 班级管理</span>
          </template>
        </el-tab-pane>
        <el-tab-pane label="教师管理" name="teachers">
          <template #label>
            <span><el-icon><Avatar /></el-icon> 教师管理</span>
          </template>
        </el-tab-pane>
        <el-tab-pane label="实验室管理" name="laboratories">
          <template #label>
            <span><el-icon><OfficeBuilding /></el-icon> 实验室管理</span>
          </template>
        </el-tab-pane>
      </el-tabs>
    </div>

    <!-- 标签页内容 -->
    <div class="tab-content">
      <!-- 学校信息标签页 -->
      <div v-show="activeTab === 'schools'">
        <!-- 主要内容区域 -->
        <div class="school-main-content">
          <!-- 左侧组织架构 -->
          <div class="school-sidebar" :style="{ width: sidebarWidth + 'px' }">
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
                ref="organizationTreeRef"
                :selected-node="selectedOrganization"
                :search-text="organizationSearchText"
                @node-click="handleOrganizationSelect"
              />
            </div>
          </div>

          <!-- 分割线 -->
          <div
            class="resize-handle"
            @mousedown="startResize"
          ></div>

          <!-- 右侧内容区域 -->
          <div class="school-content-area">
            <!-- 选中组织信息 -->
            <div v-if="selectedOrganization" class="selected-info">
              <div class="info-card">
                <div class="card-header">
                  <h3>{{ selectedOrganization.name }}</h3>
                  <el-tag :type="getOrganizationTagType(selectedOrganization.level)">
                    {{ getOrganizationLevelName(selectedOrganization.level) }}
                  </el-tag>
                </div>
                <div class="stats-row">
                  <div class="stat-item">
                    <span class="stat-label">学校总数</span>
                    <span class="stat-value">{{ schoolPagination.total }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-label">学生总数</span>
                    <span class="stat-value">{{ totalStudents }}</span>
                  </div>
                  <div class="stat-item">
                    <span class="stat-label">教师总数</span>
                    <span class="stat-value">{{ totalTeachers }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- 操作栏 -->
            <div class="toolbar">
              <div class="toolbar-left">
                <el-form :model="searchParams" inline>
                  <el-form-item label="学校类型">
                    <el-select v-model="searchParams.type" placeholder="请选择学校类型" clearable style="width: 150px">
                      <el-option label="小学" :value="1" />
                      <el-option label="初中" :value="2" />
                      <el-option label="高中" :value="3" />
                      <el-option label="九年制" :value="4" />
                    </el-select>
                  </el-form-item>
                  <el-form-item label="学校级别">
                    <el-select v-model="searchParams.level" placeholder="请选择学校级别" clearable style="width: 150px">
                      <el-option label="省直" :value="1" />
                      <el-option label="市直" :value="2" />
                      <el-option label="区县直" :value="3" />
                      <el-option label="学区" :value="4" />
                    </el-select>
                  </el-form-item>
                  <el-form-item>
                    <el-input
                      v-model="searchParams.name"
                      placeholder="搜索学校名称"
                      clearable
                      style="width: 200px"
                      @keyup.enter="handleSearch"
                    >
                      <template #prefix>
                        <el-icon><Search /></el-icon>
                      </template>
                    </el-input>
                  </el-form-item>
                  <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetSearch">重置</el-button>
                  </el-form-item>
                </el-form>
              </div>
              <div class="toolbar-right">
                <el-button type="primary" @click="addSchool">
                  <el-icon><Plus /></el-icon>
                  新增学校
                </el-button>
                <el-button @click="exportSchools">
                  <el-icon><Download /></el-icon>
                  导出
                </el-button>
              </div>
            </div>

            <!-- 学校列表 -->
            <div class="school-list" v-loading="loading">
              <el-table
                :data="schoolList"
                stripe
                style="width: 100%"
                @selection-change="handleSelectionChange"
              >
                <el-table-column type="selection" width="55" />
                <el-table-column prop="name" label="学校名称" width="250">
                  <template #default="{ row }">
                    <div class="school-name-cell">
                      <el-icon class="school-icon">
                        <SchoolIcon />
                      </el-icon>
                      <span>{{ row.name }}</span>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column prop="code" label="学校代码" width="140" />
                <el-table-column prop="type" label="学校类型" width="120">
                  <template #default="{ row }">
                    <el-tag :type="getSchoolTypeTag(row.type)" size="small">
                      {{ getSchoolTypeName(row.type) }}
                    </el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="level" label="学校级别" width="120">
                  <template #default="{ row }">
                    <el-tag :type="getSchoolLevelTag(row.level || row.school_level)" size="small">
                      {{ getSchoolLevelName(row.level || row.school_level) }}
                    </el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="student_count" label="学生数" width="100" />
                <el-table-column prop="class_count" label="班级数" width="100" />
                <el-table-column prop="teacher_count" label="教师数" width="100" />
                <el-table-column prop="contact_person" label="联系人" width="130" />
                <el-table-column prop="contact_phone" label="联系电话" width="150" />
                <el-table-column label="操作" width="220" fixed="right">
                  <template #default="{ row }">
                    <div class="action-buttons">
                      <el-button type="primary" size="small" @click="viewSchool(row)">
                        查看
                      </el-button>
                      <el-button type="warning" size="small" @click="editSchool(row)">
                        编辑
                      </el-button>
                      <el-button type="danger" size="small" @click="deleteSchool(row)">
                        删除
                      </el-button>
                    </div>
                  </template>
                </el-table-column>
              </el-table>

              <!-- 分页 -->
              <div class="pagination-wrapper">
                <el-pagination
                  v-model:current-page="schoolPagination.current"
                  v-model:page-size="schoolPagination.size"
                  :total="schoolPagination.total"
                  :page-sizes="[10, 20, 30, 50, 100]"
                  layout="total, sizes, prev, pager, next, jumper"
                  @size-change="handleSizeChange"
                  @current-change="handleCurrentChange"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 班级管理标签页 -->
      <div v-show="activeTab === 'classes'">
        <SchoolClassManagement />
      </div>

      <!-- 教师管理标签页 -->
      <div v-show="activeTab === 'teachers'">
        <SchoolTeacherManagement />
      </div>

      <!-- 实验室管理标签页 -->
      <div v-show="activeTab === 'laboratories'">
        <LaboratoryManagement />
      </div>
    </div>

    <!-- 学校详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      title="学校详情"
      width="600px"
      destroy-on-close
    >
      <div class="school-detail" v-if="currentDetailSchool">
        <div class="detail-header">
          <el-avatar :size="60" :icon="SchoolIcon" />
          <div class="detail-title">
            <h2>{{ currentDetailSchool.name }}</h2>
            <el-tag :type="getSchoolTypeTag(currentDetailSchool.type)">
              {{ getSchoolTypeName(currentDetailSchool.type) }}
            </el-tag>
          </div>
        </div>
        
        <el-descriptions :column="2" border>
          <el-descriptions-item label="学校代码">{{ currentDetailSchool.code }}</el-descriptions-item>
          <el-descriptions-item label="所属区域">{{ currentDetailSchool.region_name }}</el-descriptions-item>
          <el-descriptions-item label="联系人">{{ currentDetailSchool.contact_person }}</el-descriptions-item>
          <el-descriptions-item label="联系电话">{{ currentDetailSchool.contact_phone }}</el-descriptions-item>
          <el-descriptions-item label="学校地址" :span="2">{{ currentDetailSchool.address }}</el-descriptions-item>
          <el-descriptions-item label="学生数量">{{ currentDetailSchool.student_count }}</el-descriptions-item>
          <el-descriptions-item label="班级数量">{{ currentDetailSchool.class_count }}</el-descriptions-item>
          <el-descriptions-item label="教师数量">{{ currentDetailSchool.teacher_count }}</el-descriptions-item>
          <el-descriptions-item label="状态">
            <el-tag :type="currentDetailSchool.status === 1 ? 'success' : 'danger'">
              {{ currentDetailSchool.status === 1 ? '正常' : '禁用' }}
            </el-tag>
          </el-descriptions-item>
        </el-descriptions>
      </div>
    </el-dialog>

    <!-- 学校编辑对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="isEdit ? '编辑学校' : '新增学校'"
      width="600px"
      destroy-on-close
    >
      <el-form
        ref="formRef"
        :model="schoolForm"
        :rules="formRules"
        label-width="100px"
        style="max-height: 500px; overflow-y: auto"
      >
        <el-form-item label="学校名称" prop="name">
          <el-input v-model="schoolForm.name" placeholder="请输入学校名称" />
        </el-form-item>
        <el-form-item label="学校代码" prop="code">
          <el-input v-model="schoolForm.code" placeholder="请输入学校代码" />
        </el-form-item>
        <el-form-item label="学校类型" prop="type">
          <el-select v-model="schoolForm.type" placeholder="请选择学校类型" style="width: 100%">
            <el-option :value="1" label="小学" />
            <el-option :value="2" label="初中" />
            <el-option :value="3" label="高中" />
            <el-option :value="4" label="九年一贯制" />
          </el-select>
        </el-form-item>
        <el-form-item label="联系人" prop="contact_person">
          <el-input v-model="schoolForm.contact_person" placeholder="请输入联系人" />
        </el-form-item>
        <el-form-item label="联系电话" prop="contact_phone">
          <el-input v-model="schoolForm.contact_phone" placeholder="请输入联系电话" />
        </el-form-item>
        <el-form-item label="学校地址" prop="address">
          <el-input v-model="schoolForm.address" placeholder="请输入学校地址" />
        </el-form-item>
        <el-form-item label="学生数量" prop="student_count">
          <el-input-number v-model="schoolForm.student_count" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="班级数量" prop="class_count">
          <el-input-number v-model="schoolForm.class_count" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="教师数量" prop="teacher_count">
          <el-input-number v-model="schoolForm.teacher_count" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="schoolForm.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitting">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, nextTick, computed, watch } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import {
  Plus,
  Minus,
  Refresh,
  Search,
  View,
  Edit,
  User,
  Phone,
  Avatar,
  School as SchoolIcon,
  OfficeBuilding,
  Operation,
  MapLocation,
  Location,
  UserFilled,
  Download
} from '@element-plus/icons-vue'
import {
  getOrganizationTreeApi,
  type OrganizationNode
} from '@/api/organization'
import {
  createSchoolApi,
  updateSchoolApi,
  getSchoolListApi,
  getOrganizationSchoolsApi,
  deleteSchoolApi,
  type School,
  type CreateSchoolParams,
  type UpdateSchoolParams
} from '@/api/school'

// 导入子组件
import SchoolClassManagement from './components/SchoolClassManagement.vue'
import SchoolTeacherManagement from './components/SchoolTeacherManagement.vue'
import LaboratoryManagement from '../basic/LaboratoryManagement.vue'
import OrganizationTree from '@/components/OrganizationTree.vue'
import { parseSchoolId, isSchoolNode, getOrganizationType } from '@/utils/organization'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const editDialogVisible = ref(false)
const detailDialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()
const treeRef = ref()
const organizationTreeRef = ref()

// 标签页相关
const activeTab = ref('schools')

// 树形数据
const treeData = ref<any[]>([])
const searchQuery = ref('')

// 组织架构相关
const selectedOrganization = ref<OrganizationNode | null>(null)
const organizationSearchText = ref('')

// 学校列表相关
const schoolList = ref<School[]>([])
const selectedSchools = ref<School[]>([])
const schoolPagination = reactive({
  current: 1,
  size: 30, // 增加默认显示数量
  total: 0
})

// 搜索参数
const searchParams = reactive({
  name: '',
  type: undefined as number | undefined,
  level: undefined as number | undefined,
  organization_id: undefined as number | undefined
})

// 当前操作的学校
const currentSchool = ref<School | null>(null)
const currentDetailSchool = ref<School | null>(null)

// 侧边栏宽度调整
const sidebarWidth = ref(300)
const isResizing = ref(false)

// 树形组件配置
const treeProps = {
  children: 'children',
  label: 'name'
}

// 学校表单
const schoolForm = reactive({
  id: 0,
  name: '',
  code: '',
  type: 1,
  address: '',
  contact_person: '',
  contact_phone: '',
  student_count: 0,
  class_count: 0,
  teacher_count: 0,
  status: 1
})

// 表单验证规则
const formRules: FormRules = {
  name: [
    { required: true, message: '请输入学校名称', trigger: 'blur' },
    { min: 2, max: 200, message: '学校名称长度在 2 到 200 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入学校代码', trigger: 'blur' },
    { min: 2, max: 50, message: '学校代码长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  type: [
    { required: true, message: '请选择学校类型', trigger: 'change' }
  ],
  contact_person: [
    { required: true, message: '请输入联系人', trigger: 'blur' }
  ],
  contact_phone: [
    { required: true, message: '请输入联系电话', trigger: 'blur' },
    { pattern: /^[0-9\-\+\(\)\s]+$/, message: '请输入有效的电话号码', trigger: 'blur' }
  ]
}

// 计算属性
const totalStudents = computed(() => {
  if (!selectedOrganization.value) return 0

  // 如果选中的是学校，直接返回学校的学生数
  if (selectedOrganization.value.level === 5 || selectedOrganization.value.type === 'school') {
    return selectedOrganization.value.student_count || 0
  }

  // 如果选中的是组织，计算下属学校的学生总数
  return schoolList.value.reduce((sum, school) => sum + (school.student_count || 0), 0)
})

const totalTeachers = computed(() => {
  if (!selectedOrganization.value) return 0

  // 如果选中的是学校，直接返回学校的教师数
  if (selectedOrganization.value.level === 5 || selectedOrganization.value.type === 'school') {
    return selectedOrganization.value.teacher_count || 0
  }

  // 如果选中的是组织，计算下属学校的教师总数
  return schoolList.value.reduce((sum, school) => sum + (school.teacher_count || 0), 0)
})

const totalClasses = computed(() => {
  if (!selectedOrganization.value) return 0

  // 如果选中的是学校，直接返回学校的班级数
  if (selectedOrganization.value.level === 5 || selectedOrganization.value.type === 'school') {
    return selectedOrganization.value.class_count || 0
  }

  // 如果选中的是组织，计算下属学校的班级总数
  return schoolList.value.reduce((sum, school) => sum + (school.class_count || 0), 0)
})

// 组织架构相关方法
const getOrganizationTagType = (level: number) => {
  const types = {
    1: 'primary',
    2: 'success',
    3: 'warning',
    4: 'danger',
    5: 'info'
  }
  return types[level as keyof typeof types] || 'info'
}

const getOrganizationLevelName = (level: number) => {
  const names = {
    1: '省级',
    2: '市级',
    3: '区县',
    4: '学区',
    5: '学校'
  }
  return names[level as keyof typeof names] || '未知'
}

const handleOrganizationSelect = (node: OrganizationNode) => {
  console.log('选中组织:', node)
  selectedOrganization.value = node
  searchParams.organization_id = node.id
  // 重置分页
  schoolPagination.current = 1
  // 重置搜索条件
  searchParams.name = ''
  searchParams.type = undefined
  searchParams.level = undefined
  // 获取该组织下的学校列表
  fetchSchoolList()
}

const refreshOrganization = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.refreshTree()
  }
}

// 侧边栏宽度调整方法
const startResize = (e: MouseEvent) => {
  isResizing.value = true
  const startX = e.clientX
  const startWidth = sidebarWidth.value

  const handleMouseMove = (e: MouseEvent) => {
    if (!isResizing.value) return

    const deltaX = e.clientX - startX
    const newWidth = startWidth + deltaX

    // 限制最小和最大宽度
    if (newWidth >= 250 && newWidth <= 600) {
      sidebarWidth.value = newWidth
    }
  }

  const handleMouseUp = () => {
    isResizing.value = false
    document.removeEventListener('mousemove', handleMouseMove)
    document.removeEventListener('mouseup', handleMouseUp)
    document.body.style.cursor = ''
    document.body.style.userSelect = ''
  }

  document.addEventListener('mousemove', handleMouseMove)
  document.addEventListener('mouseup', handleMouseUp)
  document.body.style.cursor = 'col-resize'
  document.body.style.userSelect = 'none'
}

// 节点相关辅助函数
const getNodeIcon = (data: any) => {
  if (data.type === 'region') {
    switch (data.level) {
      case 1: return Operation      // 省级
      case 2: return MapLocation    // 市级
      case 3: return Location       // 区县级
      case 4: return OfficeBuilding // 学区级
      default: return OfficeBuilding
    }
  } else {
    return SchoolIcon // 学校
  }
}

const getNodeIconClass = (data: any) => {
  if (data.type === 'region') {
    switch (data.level) {
      case 1: return 'icon-province'
      case 2: return 'icon-city'
      case 3: return 'icon-county'
      case 4: return 'icon-district'
      default: return 'icon-region'
    }
  } else {
    return 'icon-school'
  }
}

const getNodeTagType = (data: any) => {
  if (data.type === 'region') {
    switch (data.level) {
      case 1: return 'primary'  // 省级
      case 2: return 'success'  // 市级
      case 3: return 'warning'  // 区县级
      case 4: return 'danger'   // 学区级
      default: return 'info'
    }
  } else {
    // 对于学校节点，根据学校类型返回不同的标签颜色
    return getSchoolTypeTag(data.school_type || 1)
  }
}

const getNodeTypeLabel = (data: any) => {
  if (data.type === 'region') {
    switch (data.level) {
      case 1: return '省级'
      case 2: return '市级'
      case 3: return '区县级'
      case 4: return '学区级'
      default: return '区域'
    }
  } else {
    // 对于学校节点，使用 school_type 字段
    return getSchoolTypeName(data.school_type || 1)
  }
}

// 学校类型相关辅助函数
const getSchoolTypeName = (type: number) => {
  const types = {
    1: '小学',
    2: '初中',
    3: '高中',
    4: '九年一贯制'
  }
  return types[type as keyof typeof types] || '其他'
}

const getSchoolTypeTag = (type: number) => {
  const tags = {
    1: 'success',  // 小学
    2: 'warning',  // 初中
    3: 'danger',   // 高中
    4: 'primary'   // 九年一贯制
  }
  return tags[type as keyof typeof tags] || 'info'
}

const getSchoolLevelName = (level: number) => {
  const names = {
    1: '省直',
    2: '市直',
    3: '区县直',
    4: '学区'
  }
  return names[level as keyof typeof names] || '未知'
}

const getSchoolLevelTag = (level: number) => {
  const tags = {
    1: 'primary',
    2: 'success',
    3: 'warning',
    4: 'danger'
  }
  return tags[level as keyof typeof tags] || 'info'
}

// 学校列表相关方法
const fetchSchoolList = async () => {
  if (!selectedOrganization.value) {
    schoolList.value = []
    schoolPagination.total = 0
    return
  }

  loading.value = true
  try {
    // 判断选中的是学校还是组织
    if (selectedOrganization.value.level === 5 || selectedOrganization.value.type === 'school') {
      // 如果选中的是学校，只显示该学校的信息
      const schoolData = {
        id: selectedOrganization.value.id,
        name: selectedOrganization.value.name,
        code: selectedOrganization.value.code || '',
        type: selectedOrganization.value.school_type || 1,
        level: selectedOrganization.value.school_level || 3,
        student_count: selectedOrganization.value.student_count || 0,
        class_count: selectedOrganization.value.class_count || 0,
        teacher_count: selectedOrganization.value.teacher_count || 0,
        contact_person: selectedOrganization.value.contact_person || '',
        contact_phone: selectedOrganization.value.contact_phone || '',
        region_name: selectedOrganization.value.region_name || ''
      }

      schoolList.value = [schoolData]
      schoolPagination.total = 1
      console.log('显示单个学校信息:', schoolData)
      return
    }

    // 如果选中的是组织，获取该组织下的所有学校
    const params = {
      page: schoolPagination.current,
      per_page: schoolPagination.size,
      organization_id: isSchoolNode(selectedOrganization.value) ? parseSchoolId(selectedOrganization.value) : selectedOrganization.value.id,
      organization_level: selectedOrganization.value.level,
      search: searchParams.name || undefined,
      type: searchParams.type,
      status: 1 // 只获取启用的学校
    }

    // 使用专门的组织学校API
    const response = await getOrganizationSchoolsApi(params)
    console.log('学校列表API响应:', response)

    if (response.success && response.data) {
      schoolList.value = response.data.items || []
      schoolPagination.total = response.data.pagination?.total || 0

      console.log('获取到的学校列表:', schoolList.value)
      console.log('学校总数:', schoolPagination.total)
    } else {
      // 如果组织学校API失败，尝试使用普通学校API
      const fallbackParams = {
        page: schoolPagination.current,
        per_page: schoolPagination.size,
        region_id: selectedOrganization.value.id,
        search: searchParams.name || undefined,
        type: searchParams.type,
        level: searchParams.level
      }

      const fallbackResponse = await getSchoolListApi(fallbackParams)
      if (fallbackResponse.data) {
        if (fallbackResponse.data.data) {
          schoolList.value = fallbackResponse.data.data || []
          schoolPagination.total = fallbackResponse.data.pagination?.total || 0
        } else if (Array.isArray(fallbackResponse.data)) {
          schoolList.value = fallbackResponse.data
          schoolPagination.total = fallbackResponse.data.length
        }
      } else {
        ElMessage.error('获取学校列表失败')
      }
    }
  } catch (error) {
    console.error('获取学校列表失败:', error)
    ElMessage.error('获取学校列表失败')
    schoolList.value = []
    schoolPagination.total = 0
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  schoolPagination.current = 1
  fetchSchoolList()
}

const resetSearch = () => {
  searchParams.name = ''
  searchParams.type = undefined
  searchParams.level = undefined
  schoolPagination.current = 1
  fetchSchoolList()
}

const handleSelectionChange = (selection: School[]) => {
  selectedSchools.value = selection
}

const handleSizeChange = (size: number) => {
  schoolPagination.size = size
  schoolPagination.current = 1
  fetchSchoolList()
}

const handleCurrentChange = (current: number) => {
  schoolPagination.current = current
  fetchSchoolList()
}

const exportSchools = () => {
  ElMessage.info('导出功能开发中...')
}

const deleteSchool = async (school: School) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除学校"${school.name}"吗？此操作不可恢复。`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    const response = await deleteSchoolApi(school.id)
    if (response.success) {
      ElMessage.success('删除成功')
      fetchSchoolList()
    } else {
      ElMessage.error(response.message || '删除失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除学校失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

// 树形过滤方法
const filterNode = (value: string, data: any) => {
  if (!value) return true
  return data.name.includes(value) || (data.code && data.code.includes(value))
}

// 获取组织和学校树
const fetchOrganizationTree = async () => {
  loading.value = true
  try {
    const response = await getOrganizationTreeApi()
    if (response.data) {
      treeData.value = response.data
    }
  } catch (error) {
    console.error('获取组织树失败:', error)
    ElMessage.error('获取组织树失败')
    treeData.value = []
  } finally {
    loading.value = false
  }
}

// 组织树相关方法
const expandAll = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.expandAll()
  }
}

const collapseAll = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.collapseAll()
  }
}

// 旧的树形相关方法（保留用于其他标签页）
const expandAllTree = async () => {
  if (loading.value) {
    ElMessage.warning('数据加载中，请稍后再试')
    return
  }

  if (treeData.value.length === 0) {
    await fetchOrganizationTree()
  }

  await nextTick()

  const tree = treeRef.value
  if (tree && treeData.value.length > 0) {
    try {
      if (tree.store && tree.store.nodesMap) {
        const nodesMap = tree.store.nodesMap
        Object.values(nodesMap).forEach((node: any) => {
          if (node && node.childNodes && node.childNodes.length > 0) {
            node.expanded = true
          }
        })
        ElMessage.success('已展开所有节点')
      }
    } catch (error) {
      console.error('展开所有节点失败:', error)
      ElMessage.error('展开失败，请重试')
    }
  } else {
    ElMessage.warning('树形组件未就绪或数据为空')
  }
}

const collapseAllTree = async () => {
  if (loading.value) {
    ElMessage.warning('数据加载中，请稍后再试')
    return
  }

  await nextTick()

  const tree = treeRef.value
  if (tree && treeData.value.length > 0) {
    try {
      if (tree.store && tree.store.nodesMap) {
        const nodesMap = tree.store.nodesMap
        Object.values(nodesMap).forEach((node: any) => {
          if (node && node.childNodes && node.childNodes.length > 0) {
            node.expanded = false
          }
        })
        ElMessage.success('已折叠所有节点')
      }
    } catch (error) {
      console.error('折叠所有节点失败:', error)
      ElMessage.error('折叠失败，请重试')
    }
  } else {
    ElMessage.warning('树形组件未就绪或数据为空')
  }
}

// 旧的树形搜索方法（保留用于其他标签页）
const handleTreeSearch = () => {
  const tree = treeRef.value
  if (tree) {
    tree.filter(searchQuery.value)
  }
}

// 学校操作方法
const viewSchool = (school: School) => {
  currentDetailSchool.value = school
  detailDialogVisible.value = true
}

const editSchool = (school: School) => {
  currentSchool.value = school
  isEdit.value = true

  // 填充表单
  Object.assign(schoolForm, {
    id: school.id,
    name: school.name,
    code: school.code,
    type: Number(school.type) || 1, // 确保是数字，默认为小学
    address: school.address || '',
    contact_person: school.contact_person || '',
    contact_phone: school.contact_phone || '',
    student_count: Number(school.student_count) || 0,
    class_count: Number(school.class_count) || 0,
    teacher_count: Number(school.teacher_count) || 0,
    status: Number(school.status) || 1
  })

  editDialogVisible.value = true
}

const addSchool = () => {
  if (!selectedOrganization.value) {
    ElMessage.warning('请先选择一个组织')
    return
  }
  currentSchool.value = null
  isEdit.value = false
  resetForm()
  editDialogVisible.value = true
}

const handleCreate = () => {
  addSchool()
}

const resetForm = () => {
  Object.assign(schoolForm, {
    id: 0,
    name: '',
    code: '',
    type: 1,
    address: '',
    contact_person: '',
    contact_phone: '',
    student_count: 0,
    class_count: 0,
    teacher_count: 0,
    status: 1
  })
  formRef.value?.clearValidate()
}

const submitForm = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    if (isEdit.value && currentSchool.value) {
      // 更新学校 - 确保所有数字字段都正确转换
      const params: UpdateSchoolParams = {
        name: schoolForm.name?.trim() || '',
        code: schoolForm.code?.trim() || '',
        type: parseInt(String(schoolForm.type)) || 1,
        level: parseInt(String(currentSchool.value.school_level)) || 3, // 使用school_level而不是level
        region_id: parseInt(String(currentSchool.value.region_id)) || 1,
        address: schoolForm.address?.trim() || '',
        contact_person: schoolForm.contact_person?.trim() || '',
        contact_phone: schoolForm.contact_phone?.trim() || '',
        student_count: parseInt(String(schoolForm.student_count)) || 0,
        class_count: parseInt(String(schoolForm.class_count)) || 0,
        teacher_count: parseInt(String(schoolForm.teacher_count)) || 0,
        status: parseInt(String(schoolForm.status)) || 1
      }

      // 验证必需字段
      if (!params.name || !params.code || !params.type || !params.level) {
        ElMessage.error('请填写完整的学校信息')
        return
      }



      // 验证数值范围
      if (![1, 2, 3, 4].includes(params.type)) {
        ElMessage.error('学校类型无效')
        return
      }
      if (![1, 2, 3, 4].includes(params.level)) {
        ElMessage.error('学校级别无效')
        return
      }

      await updateSchoolApi(currentSchool.value.id, params)
      ElMessage.success('学校更新成功')
    } else {
      // 创建学校 - 确保所有数字字段都正确转换
      const params: CreateSchoolParams = {
        name: schoolForm.name?.trim() || '',
        code: schoolForm.code?.trim() || '',
        type: parseInt(String(schoolForm.type)) || 1,
        level: 3, // 默认区县直学校
        region_id: selectedOrganization.value?.id || 1, // 使用选中的组织ID
        address: schoolForm.address?.trim() || '',
        contact_person: schoolForm.contact_person?.trim() || '',
        contact_phone: schoolForm.contact_phone?.trim() || '',
        student_count: parseInt(String(schoolForm.student_count)) || 0,
        class_count: parseInt(String(schoolForm.class_count)) || 0,
        teacher_count: parseInt(String(schoolForm.teacher_count)) || 0,
        status: parseInt(String(schoolForm.status)) || 1
      }

      // 验证必需字段
      if (!params.name || !params.code || !params.type) {
        ElMessage.error('请填写完整的学校信息')
        return
      }

      // 验证数值范围
      if (![1, 2, 3, 4].includes(params.type)) {
        ElMessage.error('学校类型无效')
        return
      }

      await createSchoolApi(params)
      ElMessage.success('学校创建成功')
    }

    editDialogVisible.value = false
    // 刷新学校列表
    fetchSchoolList()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '学校更新失败' : '学校创建失败')
  } finally {
    submitting.value = false
  }
}

// 标签页相关方法
const handleTabChange = (tabName: string) => {
  activeTab.value = tabName
  // 根据标签页刷新对应数据
  if (tabName === 'schools') {
    fetchOrganizationTree()
  }
}

const handleCreateClass = () => {
  // 这里可以触发班级管理组件的新增方法
  console.log('新增班级')
}

const handleCreateTeacher = () => {
  // 这里可以触发教师管理组件的新增方法
  console.log('新增教师')
}

// 刷新数据
const refreshData = () => {
  if (activeTab.value === 'schools') {
    fetchOrganizationTree()
  }
  // 其他标签页的刷新逻辑可以通过事件传递给子组件
}

// 生命周期
onMounted(() => {
  // 延迟一点时间让组织树加载完成
  setTimeout(() => {
    if (organizationTreeRef.value) {
      const treeData = organizationTreeRef.value.getTreeData()
      if (treeData && treeData.length > 0 && !selectedOrganization.value) {
        // 自动选择第一个组织
        handleOrganizationSelect(treeData[0])
      }
    }
  }, 1000)
})
</script>

<style scoped>
.school-management-page {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.toolbar-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-area {
  flex: 1;
}

.toolbar-buttons {
  display: flex;
  gap: 12px;
}

.school-tree {
  background: white;
  border-radius: 8px;
  padding: 20px;
  min-height: 400px;
  overflow: auto; /* 添加滚动条 */
}

/* 树形节点样式 */
.school-tree-component {
  font-size: 14px;
  width: 100%;
}

/* 调整 Element Plus 树形组件的样式 */
.school-tree-component .el-tree-node__content {
  height: auto !important; /* 覆盖默认高度 */
  min-height: 32px;
}

.school-tree-component .el-tree-node__label {
  white-space: normal !important; /* 允许文本换行 */
  word-break: break-word;
}

/* 学校管理页面样式 */
.school-management-page {
  padding: 20px;
  background: #f5f7fa;
  min-height: 100vh;
}

/* 页面头部 */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 学校信息标签页的左右分栏布局 */
.school-main-content {
  display: flex;
  flex: 1;
  min-height: 0;
  position: relative;
}

.school-sidebar {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  position: relative;
}

.resize-handle {
  width: 4px;
  background: transparent;
  cursor: col-resize;
  position: relative;
  flex-shrink: 0;
  margin: 0 8px;
}

.resize-handle::before {
  content: '';
  position: absolute;
  left: 50%;
  top: 0;
  bottom: 0;
  width: 1px;
  background: #e4e7ed;
  transform: translateX(-50%);
}

.resize-handle:hover::before {
  background: #409eff;
  width: 2px;
}

.resize-handle:active::before {
  background: #409eff;
  width: 2px;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #ebeef5;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 16px;
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
  margin-bottom: 16px;
}

.organization-tree {
  flex: 1;
  overflow: auto;
}

.school-content-area {
  flex: 1;
  min-width: 0; /* 允许弹性收缩 */
  display: flex;
  flex-direction: column;
  gap: 16px; /* 减少间距 */
  overflow: hidden;
}

.selected-info {
  background: white;
  border-radius: 8px;
  padding: 16px 20px; /* 减少上下内边距 */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  flex-shrink: 0; /* 防止被压缩 */
}

.info-card {
  display: flex;
  flex-direction: column;
  gap: 12px; /* 减少间距 */
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  font-size: 18px;
  color: #303133;
}

.stats-row {
  display: flex;
  gap: 30px; /* 减少间距 */
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-label {
  font-size: 12px;
  color: #909399;
}

.stat-value {
  font-size: 20px; /* 减少字体大小 */
  font-weight: bold;
  color: #409eff;
}

.toolbar {
  background: white;
  border-radius: 8px;
  padding: 16px 20px; /* 减少上下内边距 */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0; /* 防止被压缩 */
}

.toolbar-left {
  flex: 1;
}

.toolbar-right {
  display: flex;
  gap: 12px;
}

.school-list {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}



/* 操作按钮样式 */
.action-buttons {
  display: flex;
  gap: 4px;
  flex-wrap: nowrap;
}

.action-buttons .el-button {
  margin-left: 0 !important;
}

.school-name-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.school-icon {
  color: #409eff;
}

.pagination-wrapper {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #606266;
  font-size: 14px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

/* 工具栏 */
.toolbar-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 16px 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toolbar-buttons {
  display: flex;
  gap: 8px;
}

/* 学校树形结构 */
.school-tree {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.school-tree-component {
  padding: 16px;
}

/* 树节点样式 - 参考用户列表样式 */
.school-tree-component .el-tree-node {
  margin: 2px 0;
}

.school-tree-component .el-tree-node__content {
  padding: 0 !important;
  height: auto !important;
  min-height: 36px;
}

.tree-node {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.2s;
}

.tree-node:hover {
  background-color: #f5f7fa;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.node-icon {
  flex-shrink: 0;
  font-size: 16px;
}

/* 图标颜色 */
.icon-province { color: #409EFF; }
.icon-city { color: #67C23A; }
.icon-county { color: #E6A23C; }
.icon-district { color: #F56C6C; }
.icon-school { color: #FF9800; }

.node-info {
  flex: 1;
  min-width: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.node-name {
  font-size: 14px;
  color: #303133;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-tag {
  font-size: 12px;
  flex-shrink: 0;
}

/* 统计信息样式 - 参考用户列表 */
.node-stats {
  display: flex;
  gap: 16px;
  margin-right: 12px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #909399;
}

.stat-item .el-icon {
  font-size: 12px;
}

/* 操作按钮样式 */
.node-actions {
  display: flex;
  gap: 8px;
  opacity: 0;
  transition: opacity 0.2s;
  flex-shrink: 0;
}

.tree-node:hover .node-actions {
  opacity: 1;
}

/* 选中状态样式 */
:deep(.el-tree-node.is-current > .el-tree-node__content) {
  background: transparent;
}

:deep(.el-tree-node.is-current .tree-node) {
  background-color: #e6f7ff;
  border: 1px solid #91d5ff;
}

/* 展开/折叠图标 */
:deep(.el-tree-node__expand-icon) {
  color: #409EFF;
  font-size: 14px;
}

:deep(.el-tree-node__expand-icon.expanded) {
  transform: rotate(90deg);
}

/* 树形结构缩进 */
:deep(.el-tree-node__children) {
  padding-left: 20px;
}

/* 对话框样式 */
.school-detail {
  padding: 20px 0;
}

.detail-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #ebeef5;
}

.detail-title {
  flex: 1;
}

.detail-title h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 20px;
  font-weight: 600;
}

.detail-title .el-tag {
  margin-top: 4px;
}

.form-section {
  margin-bottom: 24px;
}

.form-section h3 {
  margin: 0 0 16px 0;
  color: #303133;
  font-size: 16px;
  font-weight: 600;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 8px;
}
</style>
