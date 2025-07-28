<template>
  <div class="laboratory-management-page">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>实验室管理</h2>
        <p>按组织架构管理学校实验室信息和配置</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="handleCreate">
          新增实验室
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

      <!-- 右侧实验室列表 -->
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
                <span class="stat-label">实验室总数</span>
                <span class="stat-value">{{ organizationStats.total_laboratories }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">学校数量</span>
                <span class="stat-value">{{ organizationStats.total_schools }}</span>
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
            <el-form-item label="实验室名称">
              <el-input
                v-model="searchForm.search"
                placeholder="请输入实验室名称"
                clearable
                style="width: 200px"
              />
            </el-form-item>
            <el-form-item label="实验室类型">
              <el-select
                v-model="searchForm.type"
                placeholder="请选择实验室类型"
                clearable
                style="width: 150px"
              >
                <el-option
                  v-for="type in laboratoryTypes"
                  :key="type.id"
                  :label="type.name"
                  :value="type.id"
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
                <el-option label="正常" :value="1" />
                <el-option label="维护中" :value="0" />
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="handleSearch">搜索</el-button>
              <el-button @click="handleReset">重置</el-button>
            </el-form-item>
          </el-form>
        </div>

        <!-- 实验室列表 -->
        <div class="table-section" v-if="selectedOrganization">
          <el-table
            v-loading="loading"
            :data="laboratoryList"
            stripe
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID" width="80" />
            <el-table-column prop="name" label="实验室名称" />
            <el-table-column prop="code" label="实验室编号" />
            <el-table-column label="实验室类型" width="120">
              <template #default="{ row }">
                <div class="type-display">
                  <el-icon
                    v-if="row.laboratory_type?.icon"
                    :style="{ color: row.laboratory_type?.color || '#409EFF' }"
                    class="type-icon"
                  >
                    <component :is="row.laboratory_type.icon" />
                  </el-icon>
                  <span>{{ row.laboratory_type?.name || row.type_name || '未知类型' }}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="所属学校">
              <template #default="{ row }">
                {{ row.school?.name || '未知学校' }}
              </template>
            </el-table-column>
            <el-table-column prop="location" label="位置" show-overflow-tooltip />
            <el-table-column prop="capacity" label="容量" width="80" />
            <el-table-column prop="manager_name" label="管理员" />
            <el-table-column label="状态" width="100">
              <template #default="{ row }">
                <el-tag :type="row.status === 1 ? 'success' : 'danger'">
                  {{ row.status === 1 ? '正常' : '维护中' }}
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
          <el-empty description="请选择左侧组织架构查看实验室列表" />
        </div>
      </div>
    </div>

    <!-- 新增/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑实验室' : '新增实验室'"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="formRef"
        :model="laboratoryForm"
        :rules="formRules"
        label-width="100px"
      >
        <el-form-item v-if="canSelectSchool" label="所属学校" prop="school_id">
          <el-select v-model="laboratoryForm.school_id" placeholder="请选择学校" style="width: 100%">
            <el-option
              v-for="school in schoolOptions"
              :key="school.id"
              :label="school.name"
              :value="school.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="实验室名称" prop="name">
          <el-input v-model="laboratoryForm.name" placeholder="请输入实验室名称" />
        </el-form-item>
        <el-form-item label="实验室编号" prop="code">
          <el-input v-model="laboratoryForm.code" placeholder="请输入实验室编号" />
        </el-form-item>
        <el-form-item label="实验室类型" prop="type_id">
          <el-select v-model="laboratoryForm.type_id" placeholder="请选择实验室类型" style="width: 100%">
            <el-option
              v-for="type in laboratoryTypes"
              :key="type.id"
              :label="type.name"
              :value="type.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="位置" prop="location">
          <el-input v-model="laboratoryForm.location" placeholder="请输入实验室位置" />
        </el-form-item>
        <el-form-item label="面积(㎡)" prop="area">
          <el-input-number v-model="laboratoryForm.area" :min="0" :precision="2" style="width: 100%" />
        </el-form-item>
        <el-form-item label="容量(人)" prop="capacity">
          <el-input-number v-model="laboratoryForm.capacity" :min="1" :max="200" style="width: 100%" />
        </el-form-item>
        <el-form-item label="设备清单" prop="equipment_list">
          <el-input
            v-model="laboratoryForm.equipment_list"
            type="textarea"
            :rows="3"
            placeholder="请输入设备清单"
          />
        </el-form-item>
        <el-form-item label="安全规则" prop="safety_rules">
          <el-input
            v-model="laboratoryForm.safety_rules"
            type="textarea"
            :rows="3"
            placeholder="请输入安全规则"
          />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="laboratoryForm.status">
            <el-radio :label="1">正常</el-radio>
            <el-radio :label="0">维护中</el-radio>
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
import { ref, reactive, computed, onMounted } from 'vue'
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
  getLaboratoryListApi,
  getOrganizationLaboratoriesApi,
  createLaboratoryApi,
  updateLaboratoryApi,
  deleteLaboratoryApi,
  type Laboratory,
  type CreateLaboratoryParams,
  type UpdateLaboratoryParams
} from '@/api/laboratory'
import {
  getLaboratoryTypesApi,
  type LaboratoryType
} from '@/api/laboratoryType'
import { getManageableSchoolsApi } from '@/api/organization'
import {
  getOrganizationStatsApi,
  type OrganizationNode,
  type OrganizationStats
} from '@/api/organization'
import { useAuthStore } from '@/stores/auth'
import OrganizationTree from '@/components/OrganizationTree.vue'

// 权限检查
const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()
const organizationTreeRef = ref()

// 实验室列表
const laboratoryList = ref<Laboratory[]>([])

// 组织相关数据
const selectedOrganization = ref<OrganizationNode | null>(null)
const selectedOrganizationId = ref<number | undefined>(undefined)
const organizationStats = ref<OrganizationStats | null>(null)

// 学校选项
const schoolOptions = ref<any[]>([])

// 实验室类型选项
const laboratoryTypes = ref<LaboratoryType[]>([])

// 是否可以选择学校
const canSelectSchool = computed(() => {
  return authStore.userInfo?.organization_level && authStore.userInfo.organization_level < 5
})

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

// 实验室表单
const laboratoryForm = reactive({
  id: 0,
  school_id: authStore.userInfo?.school_id || 0,
  name: '',
  code: '',
  type_id: undefined as number | undefined,
  location: '',
  area: 0,
  capacity: 30,
  manager_id: undefined as number | undefined,
  equipment_list: '',
  safety_rules: '',
  status: 1
})

// 表单验证规则
const formRules: FormRules = {
  school_id: [
    { required: true, message: '请选择学校', trigger: 'change' }
  ],
  name: [
    { required: true, message: '请输入实验室名称', trigger: 'blur' },
    { min: 2, max: 100, message: '实验室名称长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入实验室编号', trigger: 'blur' },
    { min: 2, max: 50, message: '实验室编号长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  type_id: [
    { required: true, message: '请选择实验室类型', trigger: 'change' }
  ],
  capacity: [
    { required: true, message: '请输入容量', trigger: 'blur' },
    { type: 'number', min: 1, max: 200, message: '容量必须在 1 到 200 之间', trigger: 'blur' }
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

// 加载实验室类型
const loadLaboratoryTypes = async () => {
  try {
    const response = await getLaboratoryTypesApi({ status: 1 }) // 只获取启用的类型
    laboratoryTypes.value = response.data.data || response.data
  } catch (error) {
    console.error('加载实验室类型失败:', error)
    ElMessage.error('加载实验室类型失败')
  }
}

// 获取实验室列表
const fetchLaboratoryList = async () => {
  if (!selectedOrganization.value) {
    laboratoryList.value = []
    pagination.total = 0
    return
  }

  try {
    loading.value = true

    // 判断是否是学校节点（通过 type 字段或 level === 5 来判断）
    // 如果是学校节点，直接按学校ID过滤；否则按组织层级过滤
    const isSchoolNode = (selectedOrganization.value as any).type === 'school' ||
                        selectedOrganization.value.level === 5

    let params: any

    if (isSchoolNode) {
      // 学校节点：直接按学校ID过滤
      params = {
        school_id: selectedOrganization.value.id,
        page: pagination.current_page,
        per_page: pagination.per_page,
        ...searchForm
      }

      // 使用普通的实验室列表API
      const response = await getLaboratoryListApi(params)

      // 处理分页数据
      if (response.data.data) {
        // Laravel分页格式
        laboratoryList.value = response.data.data
        pagination.current_page = response.data.current_page
        pagination.per_page = response.data.per_page
        pagination.total = response.data.total
        pagination.last_page = response.data.last_page
      } else {
        // 简单数组格式
        laboratoryList.value = response.data
        pagination.total = response.data.length
      }
    } else {
      // 区域节点：按组织层级过滤
      params = {
        organization_id: selectedOrganization.value.id,
        organization_level: selectedOrganization.value.level,
        page: pagination.current_page,
        per_page: pagination.per_page,
        ...searchForm
      }

      const response = await getOrganizationLaboratoriesApi(params)

      // 处理分页数据
      if (response.data.data) {
        // Laravel分页格式
        laboratoryList.value = response.data.data
        pagination.current_page = response.data.current_page
        pagination.per_page = response.data.per_page
        pagination.total = response.data.total
        pagination.last_page = response.data.last_page
      } else if (response.data.items) {
        // 自定义分页格式
        laboratoryList.value = response.data.items
        if (response.data.pagination) {
          Object.assign(pagination, response.data.pagination)
        }
      } else {
        // 简单数组格式
        laboratoryList.value = response.data
        pagination.total = response.data.length
      }
    }
  } catch (error) {
    console.error('获取实验室列表失败:', error)
    ElMessage.error('获取实验室列表失败')
    laboratoryList.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  fetchLaboratoryList()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    search: '',
    type: undefined,
    status: undefined
  })
  pagination.current_page = 1
  fetchLaboratoryList()
}

// 分页大小改变
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchLaboratoryList()
}

// 当前页改变
const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchLaboratoryList()
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
  await fetchOrganizationStats(organization.id, organization.type)

  // 获取实验室列表
  await fetchLaboratoryList()
}

// 获取组织统计信息
const fetchOrganizationStats = async (organizationId: number, organizationType?: string) => {
  try {
    const response = await getOrganizationStatsApi(organizationId, organizationType)
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
    fetchOrganizationStats(selectedOrganization.value.id, selectedOrganization.value.type)
    fetchLaboratoryList()
  }
  loadSchoolOptions()
}

// 新增实验室
const handleCreate = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 编辑实验室
const handleEdit = (laboratory: Laboratory) => {
  isEdit.value = true
  Object.assign(laboratoryForm, {
    id: laboratory.id,
    school_id: laboratory.school_id,
    name: laboratory.name,
    code: laboratory.code,
    type_id: laboratory.type_id || laboratory.type, // 优先使用type_id，兼容旧数据
    location: laboratory.location,
    area: laboratory.area,
    capacity: laboratory.capacity,
    manager_id: laboratory.manager_id,
    equipment_list: laboratory.equipment_list,
    safety_rules: laboratory.safety_rules,
    status: laboratory.status
  })
  dialogVisible.value = true
}

// 删除实验室
const handleDelete = async (laboratory: Laboratory) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除实验室 "${laboratory.name}" 吗？此操作不可恢复！`,
      '删除实验室',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await deleteLaboratoryApi(laboratory.id)
    ElMessage.success('实验室删除成功')
    fetchLaboratoryList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除实验室失败:', error)
      ElMessage.error('删除实验室失败')
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
      school_id: authStore.userInfo?.school_id || laboratoryForm.school_id,
      name: laboratoryForm.name,
      code: laboratoryForm.code,
      type_id: laboratoryForm.type_id,
      location: laboratoryForm.location,
      area: laboratoryForm.area,
      capacity: laboratoryForm.capacity,
      manager_id: laboratoryForm.manager_id || null,
      equipment_list: laboratoryForm.equipment_list,
      safety_rules: laboratoryForm.safety_rules,
      status: laboratoryForm.status
    }

    if (isEdit.value) {
      await updateLaboratoryApi(laboratoryForm.id, formData)
      ElMessage.success('实验室更新成功')
    } else {
      await createLaboratoryApi(formData as CreateLaboratoryParams)
      ElMessage.success('实验室创建成功')
    }

    dialogVisible.value = false
    fetchLaboratoryList()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '实验室更新失败' : '实验室创建失败')
  } finally {
    submitting.value = false
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(laboratoryForm, {
    id: 0,
    name: '',
    code: '',
    type_id: undefined,
    location: '',
    area: 0,
    capacity: 30,
    manager_id: undefined,
    equipment_list: '',
    safety_rules: '',
    status: 1
  })
  formRef.value?.clearValidate()
}

// 加载学校选项
const loadSchoolOptions = async () => {
  if (canSelectSchool.value) {
    try {
      const response = await getManageableSchoolsApi()
      schoolOptions.value = response.data || []
    } catch (error) {
      console.error('加载学校选项失败:', error)
    }
  }
}

// 初始化
onMounted(() => {
  loadSchoolOptions()
  loadLaboratoryTypes()
  // 实验室列表将在选择组织后加载
})
</script>

<style scoped>
.laboratory-management-page {
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

.pagination-section {
  display: flex;
  justify-content: center;
  margin-top: 20px;
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
  .laboratory-management-page {
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

/* 实验室类型显示样式 */
.type-display {
  display: flex;
  align-items: center;
  gap: 6px;
}

.type-icon {
  font-size: 14px;
}
</style>
