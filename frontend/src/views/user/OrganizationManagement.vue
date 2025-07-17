<template>
  <div class="organization-management">
    <div class="page-header">
      <h2>组织信息管理</h2>
      <p class="page-description">管理您权限范围内的组织和学校信息</p>
    </div>

    <!-- 工具栏 -->
    <div class="toolbar-section">
      <el-row :gutter="20">
        <el-col :span="8">
          <el-input
            v-model="searchQuery"
            placeholder="搜索组织名称或代码"
            clearable
            @input="handleSearch"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
        </el-col>
        <el-col :span="16" class="toolbar-buttons">
          <el-button @click="expandAll">
            <el-icon><FolderOpened /></el-icon>
            展开全部
          </el-button>
          <el-button @click="collapseAll">
            <el-icon><Folder /></el-icon>
            折叠全部
          </el-button>
          <el-button type="primary" @click="refreshData">
            <el-icon><Refresh /></el-icon>
            刷新
          </el-button>
        </el-col>
      </el-row>
    </div>

    <!-- 组织树 -->
    <div class="organization-tree">
      <el-tree
        ref="treeRef"
        v-loading="loading"
        :data="treeData"
        :props="treeProps"
        :filter-node-method="filterNode"
        :expand-on-click-node="false"
        :default-expand-all="false"
        node-key="id"
        class="org-tree"
      >
        <template #default="{ node, data }">
          <div class="tree-node">
            <div class="node-content">
              <!-- 组织图标 -->
              <el-icon class="node-icon" :class="getNodeIconClass(data)">
                <component :is="getNodeIcon(data)" />
              </el-icon>

              <!-- 组织信息 -->
              <div class="node-info">
                <div class="node-title">
                  <span class="node-name">{{ data.name }}</span>
                  <el-tag
                    :type="getNodeTagType(data)"
                    size="small"
                    class="node-tag"
                  >
                    {{ getLevelText(data.level, data.type) }}
                  </el-tag>
                </div>

                <div class="node-details">
                  <span v-if="data.code" class="node-code">{{ data.code }}</span>
                  <span v-if="data.contact_person" class="node-contact">
                    👤 {{ data.contact_person }}
                  </span>
                  <span v-if="data.contact_phone" class="node-phone">
                    📞 {{ data.contact_phone }}
                  </span>
                </div>

                <!-- 统计信息 -->
                <div v-if="data.stats" class="node-stats">
                  <span v-if="data.stats.sub_regions > 0" class="stat-item">
                    🏛️ {{ data.stats.sub_regions }}个下级区域
                  </span>
                  <span v-if="data.stats.schools > 0" class="stat-item">
                    🏫 {{ data.stats.schools }}所学校
                  </span>
                  <span v-if="data.stats.users > 0" class="stat-item">
                    👥 {{ data.stats.users }}个用户
                  </span>
                  <span v-if="data.type === 'school'" class="stat-item">
                    📊 {{ data.student_count || 0 }}/{{ data.class_count || 0 }}/{{ data.teacher_count || 0 }}
                    <el-tooltip content="学生数/班级数/教师数" placement="top">
                      <el-icon><QuestionFilled /></el-icon>
                    </el-tooltip>
                  </span>
                </div>
              </div>
            </div>

            <!-- 操作按钮 -->
            <div class="node-actions">
              <el-button
                v-if="!data.readonly && data.editable_fields && data.editable_fields.length > 0"
                type="primary"
                size="small"
                @click.stop="editOrganization(data)"
              >
                编辑
              </el-button>
              <el-button
                v-if="data.readonly"
                size="small"
                disabled
                @click.stop="viewDetails(data)"
              >
                只读
              </el-button>
              <el-button
                v-if="!data.readonly"
                size="small"
                @click.stop="viewDetails(data)"
              >
                详情
              </el-button>
            </div>
          </div>
        </template>
      </el-tree>
    </div>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="detailDialogVisible"
      :title="`${currentDetailOrg?.name} - 详细信息`"
      width="800px"
      @close="resetDetailDialog"
    >
      <div v-if="currentDetailOrg" class="detail-content">
        <!-- 基本信息 -->
        <div class="detail-section">
          <h3 class="section-title">
            <el-icon><InfoFilled /></el-icon>
            基本信息
          </h3>
          <el-descriptions :column="2" border>
            <el-descriptions-item label="组织名称">
              {{ currentDetailOrg.name }}
            </el-descriptions-item>
            <el-descriptions-item label="组织代码">
              {{ currentDetailOrg.code || '未设置' }}
            </el-descriptions-item>
            <el-descriptions-item label="组织类型">
              <el-tag :type="getNodeTagType(currentDetailOrg)">
                {{ getLevelText(currentDetailOrg.level, currentDetailOrg.type) }}
              </el-tag>
            </el-descriptions-item>
            <el-descriptions-item label="组织级别">
              {{ currentDetailOrg.level }}级
            </el-descriptions-item>
            <el-descriptions-item label="联系人">
              {{ currentDetailOrg.contact_person || '未设置' }}
            </el-descriptions-item>
            <el-descriptions-item label="联系电话">
              {{ currentDetailOrg.contact_phone || '未设置' }}
            </el-descriptions-item>
            <el-descriptions-item label="地址" :span="2">
              {{ currentDetailOrg.address || '未设置' }}
            </el-descriptions-item>
          </el-descriptions>
        </div>

        <!-- 学校特有信息 -->
        <div v-if="currentDetailOrg.type === 'school'" class="detail-section">
          <h3 class="section-title">
            <el-icon><School /></el-icon>
            学校信息
          </h3>
          <el-descriptions :column="3" border>
            <el-descriptions-item label="学生人数">
              {{ currentDetailOrg.student_count || 0 }}人
            </el-descriptions-item>
            <el-descriptions-item label="班级数量">
              {{ currentDetailOrg.class_count || 0 }}个
            </el-descriptions-item>
            <el-descriptions-item label="教师人数">
              {{ currentDetailOrg.teacher_count || 0 }}人
            </el-descriptions-item>
          </el-descriptions>
        </div>

        <!-- 统计信息 -->
        <div v-if="currentDetailOrg.stats" class="detail-section">
          <h3 class="section-title">
            <el-icon><DataAnalysis /></el-icon>
            统计信息
          </h3>
          <el-row :gutter="20">
            <el-col v-if="currentDetailOrg.stats.sub_regions > 0" :span="8">
              <el-card class="stat-card">
                <div class="stat-item">
                  <div class="stat-icon region-icon">🏛️</div>
                  <div class="stat-content">
                    <div class="stat-number">{{ currentDetailOrg.stats.sub_regions }}</div>
                    <div class="stat-label">下级区域</div>
                  </div>
                </div>
              </el-card>
            </el-col>
            <el-col v-if="currentDetailOrg.stats.schools > 0" :span="8">
              <el-card class="stat-card">
                <div class="stat-item">
                  <div class="stat-icon school-icon">🏫</div>
                  <div class="stat-content">
                    <div class="stat-number">{{ currentDetailOrg.stats.schools }}</div>
                    <div class="stat-label">所属学校</div>
                  </div>
                </div>
              </el-card>
            </el-col>
            <el-col :span="8">
              <el-card class="stat-card">
                <div class="stat-item">
                  <div class="stat-icon user-icon">👥</div>
                  <div class="stat-content">
                    <div class="stat-number">{{ currentDetailOrg.stats.users || 0 }}</div>
                    <div class="stat-label">用户数量</div>
                  </div>
                </div>
              </el-card>
            </el-col>
          </el-row>
        </div>

        <!-- 权限信息 -->
        <div class="detail-section">
          <h3 class="section-title">
            <el-icon><Lock /></el-icon>
            权限信息
          </h3>
          <el-descriptions :column="1" border>
            <el-descriptions-item label="编辑权限">
              <el-tag v-if="currentDetailOrg.readonly" type="info">
                只读（无编辑权限）
              </el-tag>
              <el-tag v-else type="success">
                可编辑
              </el-tag>
            </el-descriptions-item>
            <el-descriptions-item v-if="!currentDetailOrg.readonly" label="可编辑字段">
              <div class="editable-fields">
                <el-tag
                  v-for="field in currentDetailOrg.editable_fields"
                  :key="field"
                  size="small"
                  class="field-tag"
                >
                  {{ getFieldLabel(field) }}
                </el-tag>
              </div>
            </el-descriptions-item>
          </el-descriptions>
        </div>
      </div>

      <template #footer>
        <el-button @click="detailDialogVisible = false">关闭</el-button>
        <el-button
          v-if="!currentDetailOrg?.readonly && currentDetailOrg?.editable_fields?.length > 0"
          type="primary"
          @click="editFromDetail"
        >
          编辑信息
        </el-button>
      </template>
    </el-dialog>

    <!-- 编辑对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="`编辑${currentOrg?.type === 'region' ? '区域' : '学校'}信息`"
      width="600px"
      @close="resetEditForm"
    >
      <el-form
        ref="editFormRef"
        :model="editForm"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="组织名称" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入组织名称" />
        </el-form-item>
        
        <el-form-item
          v-if="canEditField('code')"
          label="组织代码"
          prop="code"
        >
          <el-input v-model="editForm.code" placeholder="请输入组织代码" />
        </el-form-item>

        <!-- 联系信息字段（区域和学校都有） -->
        <el-form-item label="联系人" prop="contact_person">
          <el-input v-model="editForm.contact_person" placeholder="请输入联系人" />
        </el-form-item>

        <el-form-item label="联系电话" prop="contact_phone">
          <el-input v-model="editForm.contact_phone" placeholder="请输入联系电话" />
        </el-form-item>

        <el-form-item label="地址" prop="address">
          <el-input
            v-model="editForm.address"
            type="textarea"
            :rows="3"
            placeholder="请输入详细地址"
          />
        </el-form-item>

        <!-- 区域特有字段 -->
        <template v-if="currentOrg?.type === 'region'">
          <el-form-item label="邮箱地址" prop="email">
            <el-input v-model="editForm.email" placeholder="请输入邮箱地址" />
          </el-form-item>

          <el-form-item label="机构描述" prop="description">
            <el-input
              v-model="editForm.description"
              type="textarea"
              :rows="3"
              placeholder="请输入机构描述"
            />
          </el-form-item>
        </template>
        
        <!-- 学校特有字段 -->
        <template v-if="currentOrg?.type === 'school'">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="editForm.student_count"
              :min="0"
              placeholder="学生人数"
              style="width: 100%"
            />
          </el-form-item>
          
          <el-form-item label="班级数量" prop="class_count">
            <el-input-number
              v-model="editForm.class_count"
              :min="0"
              placeholder="班级数量"
              style="width: 100%"
            />
          </el-form-item>
          
          <el-form-item label="教师人数" prop="teacher_count">
            <el-input-number
              v-model="editForm.teacher_count"
              :min="0"
              placeholder="教师人数"
              style="width: 100%"
            />
          </el-form-item>
        </template>
      </el-form>
      
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button
          type="primary"
          :loading="submitting"
          @click="submitEdit"
        >
          保存
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules, type ElTree } from 'element-plus'
import {
  Search,
  Refresh,
  FolderOpened,
  Folder,
  OfficeBuilding,
  School,
  Location,
  QuestionFilled,
  InfoFilled,
  DataAnalysis,
  Lock
} from '@element-plus/icons-vue'
import { getEditableOrganizationsApi, updateOrganizationApi } from '@/api/organization'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const treeData = ref<any[]>([])
const searchQuery = ref('')
const editDialogVisible = ref(false)
const detailDialogVisible = ref(false)
const currentOrg = ref<any>(null)
const currentDetailOrg = ref<any>(null)
const editFormRef = ref<FormInstance>()
const treeRef = ref<InstanceType<typeof ElTree>>()

// 树形组件配置
const treeProps = {
  children: 'children',
  label: 'name'
}

// 编辑表单
const editForm = reactive({
  name: '',
  code: '',
  contact_person: '',
  contact_phone: '',
  address: '',
  email: '',
  description: '',
  student_count: 0,
  class_count: 0,
  teacher_count: 0
})

// 表单验证规则
const editRules: FormRules = {
  name: [
    { required: true, message: '请输入组织名称', trigger: 'blur' },
    { min: 2, max: 255, message: '长度在 2 到 255 个字符', trigger: 'blur' }
  ],
  code: [
    { max: 50, message: '长度不能超过 50 个字符', trigger: 'blur' }
  ],
  contact_person: [
    { max: 100, message: '长度不能超过 100 个字符', trigger: 'blur' }
  ],
  contact_phone: [
    { max: 20, message: '长度不能超过 20 个字符', trigger: 'blur' }
  ],
  address: [
    { max: 500, message: '长度不能超过 500 个字符', trigger: 'blur' }
  ],
  email: [
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' },
    { max: 100, message: '长度不能超过 100 个字符', trigger: 'blur' }
  ],
  description: [
    { max: 1000, message: '长度不能超过 1000 个字符', trigger: 'blur' }
  ]
}

// 方法
const fetchOrganizations = async () => {
  loading.value = true
  try {
    const response = await getEditableOrganizationsApi()
    if (response.data) {
      treeData.value = response.data
    }
  } catch (error) {
    console.error('获取组织树失败:', error)
    ElMessage.error('获取组织树失败')
  } finally {
    loading.value = false
  }
}

// 树形相关方法
const expandAll = () => {
  const tree = treeRef.value
  if (tree) {
    // 获取所有节点的key
    const allKeys: string[] = []
    const collectKeys = (nodes: any[]) => {
      nodes.forEach(node => {
        allKeys.push(node.id.toString())
        if (node.children && node.children.length > 0) {
          collectKeys(node.children)
        }
      })
    }
    collectKeys(treeData.value)

    // 展开所有节点
    allKeys.forEach(key => {
      tree.setExpanded(key, true)
    })
  }
}

const collapseAll = () => {
  const tree = treeRef.value
  if (tree) {
    // 获取所有节点的key
    const allKeys: string[] = []
    const collectKeys = (nodes: any[]) => {
      nodes.forEach(node => {
        allKeys.push(node.id.toString())
        if (node.children && node.children.length > 0) {
          collectKeys(node.children)
        }
      })
    }
    collectKeys(treeData.value)

    // 折叠所有节点
    allKeys.forEach(key => {
      tree.setExpanded(key, false)
    })
  }
}

const filterNode = (value: string, data: any) => {
  if (!value) return true
  const searchValue = value.toLowerCase()
  return data.name.toLowerCase().includes(searchValue) ||
         (data.code && data.code.toLowerCase().includes(searchValue))
}

const handleSearch = () => {
  const tree = treeRef.value
  if (tree) {
    tree.filter(searchQuery.value)
  }
}

// 节点样式和图标相关方法
const getNodeIcon = (data: any) => {
  if (data.type === 'region') {
    switch (data.level) {
      case 1: return OfficeBuilding // 省级
      case 2: return OfficeBuilding // 市级
      case 3: return Location       // 区县级
      case 4: return Location       // 学区级
      default: return OfficeBuilding
    }
  } else {
    return School // 学校
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
      case 1: return 'danger'   // 省级 - 红色
      case 2: return 'warning'  // 市级 - 橙色
      case 3: return 'primary'  // 区县级 - 蓝色
      case 4: return 'info'     // 学区级 - 灰色
      default: return 'primary'
    }
  } else {
    return 'success' // 学校 - 绿色
  }
}

const getLevelText = (level: number, type: string) => {
  if (type === 'region') {
    const levelMap = {
      1: '省级',
      2: '市级',
      3: '区县级',
      4: '学区级'
    }
    return levelMap[level as keyof typeof levelMap] || `${level}级`
  } else {
    return '学校'
  }
}

const viewDetails = (data: any) => {
  currentDetailOrg.value = data
  detailDialogVisible.value = true
}

const resetDetailDialog = () => {
  currentDetailOrg.value = null
}

const editFromDetail = () => {
  if (currentDetailOrg.value) {
    detailDialogVisible.value = false
    editOrganization(currentDetailOrg.value)
  }
}

const getFieldLabel = (field: string): string => {
  const fieldLabels: Record<string, string> = {
    name: '组织名称',
    code: '组织代码',
    address: '地址',
    contact_person: '联系人',
    contact_phone: '联系电话',
    email: '邮箱地址',
    description: '机构描述',
    level: '组织级别',
    parent_id: '上级组织',
    region_id: '所属区域',
    student_count: '学生人数',
    class_count: '班级数量',
    teacher_count: '教师人数'
  }
  return fieldLabels[field] || field
}

const canEditField = (field: string) => {
  return currentOrg.value?.editable_fields?.includes(field) || false
}

const editOrganization = (org: any) => {
  currentOrg.value = org
  
  // 填充表单
  editForm.name = org.name || ''
  editForm.code = org.code || ''
  editForm.contact_person = org.contact_person || ''
  editForm.contact_phone = org.contact_phone || ''
  editForm.address = org.address || ''
  editForm.email = org.email || ''
  editForm.description = org.description || ''
  editForm.student_count = org.student_count || 0
  editForm.class_count = org.class_count || 0
  editForm.teacher_count = org.teacher_count || 0
  
  editDialogVisible.value = true
}

const resetEditForm = () => {
  currentOrg.value = null
  Object.assign(editForm, {
    name: '',
    code: '',
    contact_person: '',
    contact_phone: '',
    address: '',
    email: '',
    description: '',
    student_count: 0,
    class_count: 0,
    teacher_count: 0
  })
  editFormRef.value?.clearValidate()
}

const submitEdit = async () => {
  if (!editFormRef.value || !currentOrg.value) return
  
  try {
    await editFormRef.value.validate()
    
    submitting.value = true
    
    // 只提交可编辑的字段，并且根据组织类型过滤
    const updateData: any = {}
    const editableFields = currentOrg.value.editable_fields || []

    // 根据组织类型定义允许的字段
    const allowedFields = currentOrg.value.type === 'region'
      ? ['name', 'code', 'address', 'contact_person', 'contact_phone', 'email', 'description']
      : ['name', 'code', 'address', 'contact_person', 'contact_phone', 'student_count', 'class_count', 'teacher_count']

    editableFields.forEach((field: string) => {
      if (allowedFields.includes(field)) {
        const value = editForm[field as keyof typeof editForm]

        // 只提交有值的字段，并确保字符串字段不为空
        if (value !== undefined && value !== null) {
          if (typeof value === 'string') {
            // 字符串字段：只有非空字符串才提交
            if (value.trim() !== '') {
              updateData[field] = value.trim()
            }
          } else {
            // 数字字段：直接提交
            updateData[field] = value
          }
        }
      }
    })

    console.log('提交的数据:', updateData)
    console.log('组织类型:', currentOrg.value.type)
    console.log('组织ID:', currentOrg.value.id)

    await updateOrganizationApi(currentOrg.value.type, currentOrg.value.id, updateData)
    
    ElMessage.success('组织信息更新成功')
    editDialogVisible.value = false
    await fetchOrganizations()
    
  } catch (error) {
    console.error('更新组织信息失败:', error)
    ElMessage.error('更新组织信息失败')
  } finally {
    submitting.value = false
  }
}

const refreshData = () => {
  fetchOrganizations()
}

// 生命周期
onMounted(() => {
  fetchOrganizations()
})
</script>

<style scoped>
.organization-management {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.toolbar-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.toolbar-buttons {
  text-align: right;
}

.organization-tree {
  background: white;
  border-radius: 8px;
  padding: 20px;
  min-height: 400px;
}

.org-tree {
  font-size: 14px;
}

/* 树节点样式 */
.tree-node {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.tree-node:hover {
  background-color: #f5f7fa;
  border-radius: 4px;
}

.node-content {
  display: flex;
  align-items: flex-start;
  flex: 1;
  gap: 12px;
}

.node-icon {
  margin-top: 2px;
  font-size: 18px;
}

.icon-province { color: #f56c6c; }
.icon-city { color: #e6a23c; }
.icon-county { color: #409eff; }
.icon-district { color: #909399; }
.icon-school { color: #67c23a; }

.node-info {
  flex: 1;
}

.node-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.node-name {
  font-weight: 500;
  color: #303133;
  font-size: 15px;
}

.node-tag {
  font-size: 12px;
}

.node-details {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 4px;
  font-size: 12px;
  color: #606266;
}

.node-code {
  background: #f0f2f5;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: monospace;
}

.node-contact,
.node-phone {
  display: flex;
  align-items: center;
  gap: 4px;
}

.node-stats {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 12px;
  color: #909399;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.node-actions {
  display: flex;
  gap: 8px;
  opacity: 0;
  transition: opacity 0.2s;
}

.tree-node:hover .node-actions {
  opacity: 1;
}

/* 深度选择器，修改 el-tree 的样式 */
:deep(.el-tree-node__content) {
  height: auto !important;
  padding: 0 !important;
}

:deep(.el-tree-node__expand-icon) {
  color: #409eff;
}

:deep(.el-tree-node__expand-icon.expanded) {
  transform: rotate(90deg);
}

/* 搜索高亮 */
:deep(.el-tree-node.is-current > .el-tree-node__content) {
  background-color: #e6f7ff;
}

/* 详情对话框样式 */
.detail-content {
  max-height: 600px;
  overflow-y: auto;
}

.detail-section {
  margin-bottom: 24px;
}

.detail-section:last-child {
  margin-bottom: 0;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
  border-bottom: 2px solid #e4e7ed;
  padding-bottom: 8px;
}

.stat-card {
  margin-bottom: 12px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat-icon {
  font-size: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.region-icon {
  background: #e6f7ff;
}

.school-icon {
  background: #f6ffed;
}

.user-icon {
  background: #fff7e6;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 20px;
  font-weight: 600;
  color: #303133;
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.editable-fields {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.field-tag {
  margin: 0;
}
</style>
