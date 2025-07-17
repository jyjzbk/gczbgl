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

    <!-- 主要内容区域 -->
    <div class="main-content">
      <!-- 左侧组织树 -->
      <div class="left-panel">
        <OrganizationTree
          ref="organizationTreeRef"
          :show-stats="true"
          :default-expand-level="2"
          :selected-node-id="selectedOrganizationId"
          @node-click="handleOrganizationSelect"
        />
      </div>

      <!-- 右侧学校列表 -->
      <div class="right-panel">
        <!-- 当前组织信息 -->
        <div class="current-organization" v-if="selectedOrganization">
          <div class="org-info">
            <div class="org-header">
              <el-icon :color="getOrganizationColor(selectedOrganization.level)">
                <component :is="getOrganizationIcon(selectedOrganization.level)" />
              </el-icon>
              <div class="org-details">
                <h3>{{ selectedOrganization.name }}</h3>
                <el-tag :type="getOrganizationTagType(selectedOrganization.level)" size="small">
                  {{ getOrganizationLevelName(selectedOrganization.level) }}
                </el-tag>
              </div>
            </div>
            <div class="org-stats" v-if="organizationStats">
              <div class="stat-item">
                <span class="stat-label">学校总数</span>
                <span class="stat-value">{{ organizationStats.total_schools }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">用户总数</span>
                <span class="stat-value">{{ organizationStats.total_users }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">设备总数</span>
                <span class="stat-value">{{ organizationStats.total_equipments }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 搜索区域 -->
        <div class="search-section" v-if="selectedOrganization">
          <el-form :model="searchForm" inline>
            <el-form-item label="学校名称">
              <el-input
                v-model="searchForm.search"
                placeholder="请输入学校名称"
                clearable
                style="width: 200px"
              />
            </el-form-item>
            <el-form-item label="学校类型">
              <el-select
                v-model="searchForm.type"
                placeholder="请选择学校类型"
                clearable
                style="width: 150px"
              >
                <el-option label="小学" :value="1" />
                <el-option label="初中" :value="2" />
                <el-option label="高中" :value="3" />
                <el-option label="九年一贯制" :value="4" />
              </el-select>
            </el-form-item>
            <el-form-item label="状态">
              <el-select
                v-model="searchForm.status"
                placeholder="请选择状态"
                clearable
                style="width: 120px"
              >
                <el-option label="正常" :value="1" />
                <el-option label="禁用" :value="0" />
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="handleSearch">搜索</el-button>
              <el-button @click="handleReset">重置</el-button>
            </el-form-item>
          </el-form>
        </div>

        <!-- 学校列表 -->
        <div class="table-section" v-if="selectedOrganization">
          <el-table
            v-loading="loading"
            :data="schoolList"
            stripe
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID" width="80" />
            <el-table-column prop="name" label="学校名称" />
            <el-table-column prop="code" label="学校代码" />
            <el-table-column prop="type_name" label="学校类型" />
            <el-table-column prop="region_name" label="所属区域" />
            <el-table-column prop="address" label="地址" show-overflow-tooltip />
            <el-table-column prop="contact_person" label="联系人" />
            <el-table-column prop="contact_phone" label="联系电话" />
            <el-table-column label="状态" width="100">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'danger'">
                  {{ row.status === 1 ? '正常' : '禁用' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column label="操作" width="180" fixed="right">
              <template #default="{ row }">
                <el-button type="primary" size="small" @click="handleEdit(row)">编辑</el-button>
                <el-button type="danger" size="small" @click="handleDelete(row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>

          <!-- 分页 -->
          <div class="pagination-section">
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
        </div>

        <!-- 空状态 -->
        <div class="empty-state" v-if="!selectedOrganization">
          <el-empty description="请选择左侧组织架构查看学校列表" />
        </div>
      </div>
    </div>

    <!-- 新增/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑学校' : '新增学校'"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="formRef"
        :model="schoolForm"
        :rules="formRules"
        label-width="100px"
      >
        <el-form-item label="学校名称" prop="name">
          <el-input v-model="schoolForm.name" placeholder="请输入学校名称" />
        </el-form-item>
        <el-form-item label="学校代码" prop="code">
          <el-input v-model="schoolForm.code" placeholder="请输入学校代码" />
        </el-form-item>
        <el-form-item label="学校类型" prop="type">
          <el-select v-model="schoolForm.type" placeholder="请选择学校类型" style="width: 100%">
            <el-option label="小学" :value="1" />
            <el-option label="初中" :value="2" />
            <el-option label="高中" :value="3" />
            <el-option label="九年一贯制" :value="4" />
          </el-select>
        </el-form-item>
        <el-form-item label="管理级别" prop="level">
          <el-select v-model="schoolForm.level" placeholder="请选择管理级别" style="width: 100%">
            <el-option label="省直" :value="1" />
            <el-option label="市直" :value="2" />
            <el-option label="区县直" :value="3" />
            <el-option label="学区" :value="4" />
          </el-select>
        </el-form-item>
        <el-form-item label="学校地址" prop="address">
          <el-input
            v-model="schoolForm.address"
            type="textarea"
            :rows="3"
            placeholder="请输入学校地址"
          />
        </el-form-item>
        <el-form-item label="联系人" prop="contact_person">
          <el-input v-model="schoolForm.contact_person" placeholder="请输入联系人" />
        </el-form-item>
        <el-form-item label="联系电话" prop="contact_phone">
          <el-input v-model="schoolForm.contact_phone" placeholder="请输入联系电话" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="schoolForm.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" :loading="submitting" @click="handleSubmit">
            {{ isEdit ? '更新' : '创建' }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import {
  Plus,
  Refresh,
  OfficeBuilding,
  Operation,
  MapLocation,
  Location,
  House
} from '@element-plus/icons-vue'
import {
  getOrganizationSchoolsApi,
  createSchoolApi,
  updateSchoolApi,
  deleteSchoolApi,
  type School,
  type CreateSchoolParams,
  type UpdateSchoolParams,
  SCHOOL_TYPES,
  SCHOOL_LEVELS
} from '@/api/school'
import {
  getOrganizationStatsApi,
  type OrganizationNode,
  type OrganizationStats
} from '@/api/organization'
import OrganizationTree from '@/components/OrganizationTree.vue'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()
const organizationTreeRef = ref()

// 学校列表
const schoolList = ref<School[]>([])

// 组织相关数据
const selectedOrganization = ref<OrganizationNode | null>(null)
const selectedOrganizationId = ref<number | undefined>(undefined)
const organizationStats = ref<OrganizationStats | null>(null)

// 搜索表单
const searchForm = reactive({
  search: '',
  type: undefined as number | undefined,
  status: undefined as number | undefined
})

// 分页信息
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 学校表单
const schoolForm = reactive({
  id: 0,
  name: '',
  code: '',
  type: 1,
  level: 1,
  address: '',
  contact_person: '',
  contact_phone: '',
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
  level: [
    { required: true, message: '请选择管理级别', trigger: 'change' }
  ],
  address: [
    { required: true, message: '请输入学校地址', trigger: 'blur' }
  ],
  contact_person: [
    { required: true, message: '请输入联系人', trigger: 'blur' }
  ],
  contact_phone: [
    { required: true, message: '请输入联系电话', trigger: 'blur' },
    { pattern: /^[0-9\-\+\(\)\s]+$/, message: '请输入有效的电话号码', trigger: 'blur' }
  ]
}

// 组织相关辅助函数
const getOrganizationIcon = (level: number) => {
  const icons = {
    1: Operation,     // 省级
    2: MapLocation,   // 市级
    3: Location,      // 区县级
    4: OfficeBuilding, // 学区级
    5: House          // 学校级
  }
  return icons[level as keyof typeof icons] || OfficeBuilding
}

const getOrganizationColor = (level: number) => {
  const colors = {
    1: '#409EFF', // 省级 - 蓝色
    2: '#67C23A', // 市级 - 绿色
    3: '#E6A23C', // 区县级 - 橙色
    4: '#F56C6C', // 学区级 - 红色
    5: '#909399'  // 学校级 - 灰色
  }
  return colors[level as keyof typeof colors] || '#909399'
}

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
    3: '区县级',
    4: '学区级',
    5: '学校级'
  }
  return names[level as keyof typeof names] || '未知'
}

// 获取学校列表
const fetchSchoolList = async () => {
  if (!selectedOrganization.value) {
    schoolList.value = []
    pagination.total = 0
    return
  }

  try {
    loading.value = true
    const params = {
      organization_id: selectedOrganization.value.id,
      organization_level: selectedOrganization.value.level,
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm
    }

    const response = await getOrganizationSchoolsApi(params)
    schoolList.value = response.data.items || response.data.data

    // 更新分页信息
    if (response.data.pagination) {
      Object.assign(pagination, response.data.pagination)
    }
  } catch (error) {
    console.error('获取学校列表失败:', error)
    ElMessage.error('获取学校列表失败')
    // 如果API不存在，使用模拟数据
    schoolList.value = [
      {
        id: 1,
        name: '郑州市中原区实验小学',
        code: 'ZY001',
        type: 1,
        type_name: '小学',
        level: 3,
        level_name: '区县直',
        region_id: 1,
        region_name: '中原区',
        address: '河南省郑州市中原区建设路123号',
        contact_person: '张校长',
        contact_phone: '0371-12345678',
        student_count: 1200,
        class_count: 24,
        teacher_count: 80,
        status: 1,
        created_at: '2024-01-01 00:00:00',
        updated_at: '2024-01-01 00:00:00'
      },
      {
        id: 2,
        name: '郑州市第一中学',
        code: 'ZY002',
        type: 3,
        type_name: '高中',
        level: 2,
        level_name: '市直',
        region_id: 1,
        region_name: '中原区',
        address: '河南省郑州市中原区中原路456号',
        contact_person: '李校长',
        contact_phone: '0371-23456789',
        student_count: 2400,
        class_count: 48,
        teacher_count: 180,
        status: 1,
        created_at: '2024-01-01 00:00:00',
        updated_at: '2024-01-01 00:00:00'
      }
    ]
    pagination.total = schoolList.value.length
  } finally {
    loading.value = false
  }
}

// 组织选择处理
const handleOrganizationSelect = async (organization: OrganizationNode) => {
  console.log('选择组织:', organization)
  selectedOrganization.value = organization
  selectedOrganizationId.value = organization.id

  // 重置分页
  pagination.current_page = 1

  // 重置搜索条件
  searchForm.search = ''
  searchForm.type = undefined
  searchForm.status = undefined

  // 获取组织统计信息
  await fetchOrganizationStats(organization.id)

  // 获取学校列表
  await fetchSchoolList()
}

// 获取组织统计信息
const fetchOrganizationStats = async (organizationId: number) => {
  try {
    const response = await getOrganizationStatsApi(organizationId)
    if (response.success) {
      organizationStats.value = response.data
    }
  } catch (error) {
    console.error('获取组织统计信息失败:', error)
    organizationStats.value = null
  }
}

// 刷新数据
const refreshData = () => {
  if (organizationTreeRef.value) {
    organizationTreeRef.value.refreshTree()
  }
  if (selectedOrganization.value) {
    fetchOrganizationStats(selectedOrganization.value.id)
    fetchSchoolList()
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  fetchSchoolList()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    search: '',
    type: undefined,
    status: undefined
  })
  pagination.current_page = 1
  fetchSchoolList()
}

// 分页大小改变
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchSchoolList()
}

// 当前页改变
const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchSchoolList()
}

// 新增学校
const handleCreate = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 编辑学校
const handleEdit = (school: School) => {
  isEdit.value = true
  Object.assign(schoolForm, {
    id: school.id,
    name: school.name,
    code: school.code,
    type: school.type,
    level: school.level,
    address: school.address,
    contact_person: school.contact_person,
    contact_phone: school.contact_phone,
    status: school.status
  })
  dialogVisible.value = true
}

// 删除学校
const handleDelete = async (school: School) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除学校 "${school.name}" 吗？此操作不可恢复！`,
      '删除学校',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await deleteSchoolApi(school.id)
    ElMessage.success('学校删除成功')
    fetchSchoolList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除学校失败:', error)
      ElMessage.error('删除学校失败')
    }
  }
}

// 提交表单
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    submitting.value = true

    const formData = {
      name: schoolForm.name,
      code: schoolForm.code,
      type: schoolForm.type,
      level: schoolForm.level,
      region_id: 1, // 默认区域ID，实际应该从表单选择
      address: schoolForm.address,
      contact_person: schoolForm.contact_person,
      contact_phone: schoolForm.contact_phone,
      status: schoolForm.status
    }

    if (isEdit.value) {
      await updateSchoolApi(schoolForm.id, formData)
      ElMessage.success('学校更新成功')
    } else {
      await createSchoolApi(formData as CreateSchoolParams)
      ElMessage.success('学校创建成功')
    }

    dialogVisible.value = false
    fetchSchoolList()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '学校更新失败' : '学校创建失败')
  } finally {
    submitting.value = false
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(schoolForm, {
    id: 0,
    name: '',
    code: '',
    type: 1,
    level: 1,
    address: '',
    contact_person: '',
    contact_phone: '',
    status: 1
  })
  formRef.value?.clearValidate()
}

// 初始化
onMounted(() => {
  // 学校列表将在选择组织后加载
})
</script>

<style scoped>
.school-management-page {
  height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: #f5f5f5;
}

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

.main-content {
  flex: 1;
  display: flex;
  gap: 20px;
  min-height: 0;
}

.left-panel {
  width: 320px;
  flex-shrink: 0;
}

.right-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* 当前组织信息样式 */
.current-organization {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.org-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.org-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.org-details h3 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 18px;
  font-weight: 600;
}

.org-stats {
  display: flex;
  gap: 24px;
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
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.search-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.table-section {
  flex: 1;
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  min-height: 0;
}

.table-section .el-table {
  flex: 1;
}

.empty-state {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pagination-section {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.dialog-footer {
  text-align: right;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .main-content {
    flex-direction: column;
  }

  .left-panel {
    width: 100%;
    height: 300px;
  }

  .right-panel {
    flex: 1;
  }
}

@media (max-width: 768px) {
  .school-management-page {
    padding: 10px;
  }

  .page-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .header-actions {
    justify-content: center;
  }

  .org-stats {
    justify-content: space-around;
  }

  .search-section .el-form {
    flex-direction: column;
  }

  .search-section .el-form-item {
    margin-right: 0;
    margin-bottom: 16px;
  }
}
</style>
