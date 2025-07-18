<template>
  <div class="school-management-page">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>学校管理</h2>
        <p>按组织架构管理系统中的学校信息</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="handleCreate">
          新增学校
        </el-button>
        <el-button :icon="Refresh" @click="refreshData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 工具栏区域 -->
    <div class="toolbar-section">
      <div class="search-area">
        <el-input
          v-model="searchQuery"
          placeholder="搜索学校名称或代码"
          :prefix-icon="Search"
          clearable
          style="width: 300px"
          @input="handleSearch"
        />
      </div>
      <div class="toolbar-buttons">
        <el-button :icon="Plus" @click="expandAll">展开全部</el-button>
        <el-button :icon="Minus" @click="collapseAll">折叠全部</el-button>
        <el-button :icon="Refresh" @click="refreshData">刷新</el-button>
      </div>
    </div>

    <!-- 学校树形结构 -->
    <div class="school-tree">
      <el-tree
        ref="treeRef"
        v-loading="loading"
        :data="treeData"
        :props="treeProps"
        :filter-node-method="filterNode"
        :expand-on-click-node="false"
        :default-expand-all="false"
        node-key="id"
        class="school-tree-component"
      >
        <template #default="{ node, data }">
          <div class="tree-node">
            <div class="node-content">
              <!-- 组织/学校图标 -->
              <el-icon class="node-icon" :class="getNodeIconClass(data)">
                <component :is="getNodeIcon(data)" />
              </el-icon>

              <!-- 节点信息 -->
              <div class="node-info">
                <div class="node-name">{{ data.name }}</div>
                <el-tag
                  :type="getNodeTagType(data)"
                  size="small"
                  class="node-tag"
                >
                  {{ getNodeTypeLabel(data) }}
                </el-tag>
              </div>
            </div>

            <!-- 统计信息 -->
            <div class="node-stats" v-if="data.stats || data.type === 'school'">
              <div class="stat-item" v-if="data.stats && data.stats.schools !== undefined">
                <el-icon><OfficeBuilding /></el-icon>
                <span>{{ data.stats.schools }}</span>
              </div>
              <div class="stat-item" v-if="data.stats && data.stats.users !== undefined">
                <el-icon><User /></el-icon>
                <span>{{ data.stats.users }}</span>
              </div>
              <div class="stat-item" v-if="data.student_count">
                <el-icon><User /></el-icon>
                <span>{{ data.student_count }}</span>
              </div>
              <div class="stat-item" v-if="data.class_count">
                <el-icon><OfficeBuilding /></el-icon>
                <span>{{ data.class_count }}</span>
              </div>
              <div class="stat-item" v-if="data.teacher_count">
                <el-icon><Avatar /></el-icon>
                <span>{{ data.teacher_count }}</span>
              </div>
            </div>

            <!-- 操作按钮 -->
            <div class="node-actions" v-if="data.type === 'school'">
              <el-button
                type="primary"
                :icon="View"
                size="small"
                @click.stop="viewSchool(data)"
              >
                查看
              </el-button>
              <el-button
                type="warning"
                :icon="Edit"
                size="small"
                @click.stop="editSchool(data)"
              >
                编辑
              </el-button>
            </div>
          </div>
        </template>
      </el-tree>
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
import { ref, reactive, onMounted, nextTick } from 'vue'
import { ElMessage, type FormInstance, type FormRules } from 'element-plus'
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
  Location
} from '@element-plus/icons-vue'
import {
  getOrganizationTreeApi,
  type OrganizationNode
} from '@/api/organization'
import {
  createSchoolApi,
  updateSchoolApi,
  type School,
  type CreateSchoolParams,
  type UpdateSchoolParams
} from '@/api/school'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const editDialogVisible = ref(false)
const detailDialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()
const treeRef = ref()

// 树形数据
const treeData = ref<any[]>([])
const searchQuery = ref('')

// 当前操作的学校
const currentSchool = ref<School | null>(null)
const currentDetailSchool = ref<School | null>(null)

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

// 树形相关方法
const expandAll = async () => {
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

const collapseAll = async () => {
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

const handleSearch = () => {
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
    type: school.type,
    address: school.address || '',
    contact_person: school.contact_person || '',
    contact_phone: school.contact_phone || '',
    student_count: school.student_count || 0,
    class_count: school.class_count || 0,
    teacher_count: school.teacher_count || 0,
    status: school.status
  })

  editDialogVisible.value = true
}

const handleCreate = () => {
  currentSchool.value = null
  isEdit.value = false
  resetForm()
  editDialogVisible.value = true
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
      // 更新学校
      const params: UpdateSchoolParams = {
        name: schoolForm.name,
        code: schoolForm.code,
        type: schoolForm.type,
        level: 5, // 学校级别
        region_id: 1, // 默认区域ID，实际应该从当前用户的组织获取
        address: schoolForm.address,
        contact_person: schoolForm.contact_person,
        contact_phone: schoolForm.contact_phone,
        student_count: schoolForm.student_count,
        class_count: schoolForm.class_count,
        teacher_count: schoolForm.teacher_count,
        status: schoolForm.status
      }

      await updateSchoolApi(currentSchool.value.id, params)
      ElMessage.success('学校更新成功')
    } else {
      // 创建学校
      const params: CreateSchoolParams = {
        name: schoolForm.name,
        code: schoolForm.code,
        type: schoolForm.type,
        level: 5, // 学校级别
        region_id: 1, // 默认区域ID，实际应该从当前用户的组织获取
        address: schoolForm.address,
        contact_person: schoolForm.contact_person,
        contact_phone: schoolForm.contact_phone,
        student_count: schoolForm.student_count,
        class_count: schoolForm.class_count,
        teacher_count: schoolForm.teacher_count,
        status: schoolForm.status
      }

      await createSchoolApi(params)
      ElMessage.success('学校创建成功')
    }

    editDialogVisible.value = false
    refreshData()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '学校更新失败' : '学校创建失败')
  } finally {
    submitting.value = false
  }
}

// 刷新数据
const refreshData = () => {
  fetchOrganizationTree()
}

// 生命周期
onMounted(() => {
  fetchOrganizationTree()
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
