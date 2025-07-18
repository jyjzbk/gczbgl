<template>
  <div class="laboratory-type-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>实验室类型管理</h2>
        <p>管理系统中的实验室类型，支持动态添加和修改</p>
      </div>
      <div class="header-actions">
        <PermissionTooltip permission="laboratory_type.create">
          <el-button type="primary" :icon="Plus" @click="handleCreate">
            新增类型
          </el-button>
        </PermissionTooltip>
        <el-button :icon="Refresh" @click="loadData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 搜索区域 -->
    <div class="search-section">
      <el-form :model="searchForm" inline>
        <el-form-item label="类型名称">
          <el-input
            v-model="searchForm.search"
            placeholder="请输入类型名称或代码"
            clearable
            style="width: 200px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item label="状态">
          <el-select
            v-model="searchForm.status"
            placeholder="请选择状态"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="option in statusOptions"
              :key="option.value"
              :label="option.label"
              :value="option.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">
            搜索
          </el-button>
          <el-button :icon="RefreshLeft" @click="handleReset">
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 数据表格 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        style="width: 100%"
        @sort-change="handleSortChange"
      >
        <el-table-column type="index" label="序号" width="60" align="center" />
        
        <el-table-column prop="name" label="类型名称" min-width="120">
          <template #default="{ row }">
            <div class="type-info">
              <el-icon 
                v-if="row.icon" 
                :style="{ color: row.color || '#409EFF' }"
                class="type-icon"
              >
                <component :is="row.icon" />
              </el-icon>
              <span>{{ row.name }}</span>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column prop="code" label="类型代码" width="120" />
        
        <el-table-column prop="description" label="描述" min-width="200" show-overflow-tooltip />
        
        <el-table-column prop="sort_order" label="排序" width="80" align="center" sortable="custom" />
        
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status_text }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column prop="created_at" label="创建时间" width="160" />
        
        <el-table-column label="操作" width="200" align="center" fixed="right">
          <template #default="{ row }">
            <PermissionTooltip permission="laboratory_type.update">
              <el-button
                type="primary"
                size="small"
                :icon="Edit"
                @click="handleEdit(row)"
              >
                编辑
              </el-button>
            </PermissionTooltip>
            <PermissionTooltip permission="laboratory_type.delete">
              <el-button
                type="danger"
                size="small"
                :icon="Delete"
                @click="handleDelete(row)"
              >
                删除
              </el-button>
            </PermissionTooltip>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-section">
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

    <!-- 编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogMode === 'create' ? '新增实验室类型' : '编辑实验室类型'"
      width="600px"
      destroy-on-close
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="100px"
      >
        <el-form-item label="类型名称" prop="name">
          <el-input v-model="formData.name" placeholder="请输入类型名称" />
        </el-form-item>
        
        <el-form-item label="类型代码" prop="code">
          <el-input v-model="formData.code" placeholder="请输入类型代码（英文大写）" />
        </el-form-item>
        
        <el-form-item label="描述" prop="description">
          <el-input
            v-model="formData.description"
            type="textarea"
            :rows="3"
            placeholder="请输入类型描述"
          />
        </el-form-item>
        
        <el-form-item label="图标" prop="icon">
          <el-input v-model="formData.icon" placeholder="请输入图标名称" />
        </el-form-item>
        
        <el-form-item label="颜色" prop="color">
          <el-color-picker v-model="formData.color" />
        </el-form-item>
        
        <el-form-item label="排序" prop="sort_order">
          <el-input-number
            v-model="formData.sort_order"
            :min="0"
            :max="999"
            controls-position="right"
          />
        </el-form-item>
        
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="formData.status">
            <el-radio :label="1">启用</el-radio>
            <el-radio :label="0">禁用</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, RefreshLeft, Refresh, Edit, Delete } from '@element-plus/icons-vue'
import {
  getLaboratoryTypesApi,
  createLaboratoryTypeApi,
  updateLaboratoryTypeApi,
  deleteLaboratoryTypeApi,
  type LaboratoryType,
  type LaboratoryTypeForm,
  LABORATORY_TYPE_STATUS_OPTIONS
} from '@/api/laboratoryType'
import PermissionTooltip from '@/components/PermissionTooltip.vue'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const tableData = ref<LaboratoryType[]>([])
const dialogVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const currentType = ref<LaboratoryType | null>(null)

// 搜索表单
const searchForm = reactive({
  search: '',
  status: undefined as number | undefined
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 排序数据
const sortData = reactive({
  sort_field: '',
  sort_order: ''
})

// 表单数据
const formData = reactive<LaboratoryTypeForm>({
  name: '',
  code: '',
  description: '',
  icon: '',
  color: '#409EFF',
  sort_order: 0,
  status: 1
})

// 表单引用
const formRef = ref()

// 状态选项
const statusOptions = LABORATORY_TYPE_STATUS_OPTIONS

// 表单验证规则
const formRules = {
  name: [
    { required: true, message: '请输入类型名称', trigger: 'blur' },
    { min: 2, max: 100, message: '长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入类型代码', trigger: 'blur' },
    { pattern: /^[A-Z_]+$/, message: '代码只能包含大写字母和下划线', trigger: 'blur' }
  ],
  sort_order: [
    { required: true, message: '请输入排序值', trigger: 'blur' }
  ]
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
    
    const response = await getLaboratoryTypesApi(params)
    
    if (response.data.data) {
      tableData.value = response.data.data
      Object.assign(pagination, response.data)
    } else {
      tableData.value = response.data
    }
  } catch (error) {
    console.error('加载实验室类型失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
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
    search: '',
    status: undefined
  })
  pagination.current_page = 1
  loadData()
}

// 排序变化
const handleSortChange = ({ prop, order }: any) => {
  if (order) {
    sortData.sort_field = prop
    sortData.sort_order = order === 'ascending' ? 'asc' : 'desc'
  } else {
    sortData.sort_field = ''
    sortData.sort_order = ''
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

// 新增
const handleCreate = () => {
  dialogMode.value = 'create'
  currentType.value = null
  resetForm()
  dialogVisible.value = true
}

// 编辑
const handleEdit = (row: LaboratoryType) => {
  dialogMode.value = 'edit'
  currentType.value = row
  Object.assign(formData, {
    name: row.name,
    code: row.code,
    description: row.description || '',
    icon: row.icon || '',
    color: row.color || '#409EFF',
    sort_order: row.sort_order,
    status: row.status
  })
  dialogVisible.value = true
}

// 删除
const handleDelete = async (row: LaboratoryType) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除实验室类型"${row.name}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteLaboratoryTypeApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('删除失败:', error)
      ElMessage.error(error.response?.data?.message || '删除失败')
    }
  }
}

// 提交表单
const handleSubmit = async () => {
  try {
    await formRef.value?.validate()
    
    submitting.value = true
    
    if (dialogMode.value === 'create') {
      await createLaboratoryTypeApi(formData)
      ElMessage.success('创建成功')
    } else {
      await updateLaboratoryTypeApi(currentType.value!.id, formData)
      ElMessage.success('更新成功')
    }
    
    dialogVisible.value = false
    loadData()
  } catch (error: any) {
    console.error('提交失败:', error)
    ElMessage.error(error.response?.data?.message || '操作失败')
  } finally {
    submitting.value = false
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(formData, {
    name: '',
    code: '',
    description: '',
    icon: '',
    color: '#409EFF',
    sort_order: 0,
    status: 1
  })
  formRef.value?.clearValidate()
}

// 初始化
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.laboratory-type-management {
  padding: 20px;
  background: #f5f7fa;
  min-height: 100vh;
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

.search-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-section {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.type-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.type-icon {
  font-size: 16px;
}

.pagination-section {
  padding: 20px;
  display: flex;
  justify-content: center;
}
</style>
