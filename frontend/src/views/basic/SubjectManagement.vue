<template>
  <div class="subject-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>学科管理</h2>
        <p>管理系统中的学科分类信息</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus" @click="handleCreate">
          新增学科
        </el-button>
      </div>
    </div>

    <!-- 搜索区域 -->
    <div class="search-section">
      <el-form :model="searchForm" inline>
        <el-form-item label="学科名称">
          <el-input
            v-model="searchForm.search"
            placeholder="请输入学科名称"
            clearable
            style="width: 200px"
          />
        </el-form-item>
        <el-form-item label="学科类型">
          <el-select
            v-model="searchForm.type"
            placeholder="请选择学科类型"
            clearable
            style="width: 120px"
          >
            <el-option label="理科" :value="1" />
            <el-option label="文科" :value="2" />
            <el-option label="综合" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="学段">
          <el-select
            v-model="searchForm.stage"
            placeholder="请选择学段"
            clearable
            style="width: 120px"
          >
            <el-option label="小学" :value="1" />
            <el-option label="初中" :value="2" />
            <el-option label="高中" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="状态">
          <el-select
            v-model="searchForm.status"
            placeholder="请选择状态"
            clearable
            style="width: 120px"
          >
            <el-option label="启用" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">搜索</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 学科列表 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="subjectList"
        stripe
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="学科名称" />
        <el-table-column prop="code" label="学科代码" />
        <el-table-column prop="type_name" label="学科类型" />
        <el-table-column prop="stage_name" label="学段" />
        <el-table-column prop="sort_order" label="排序" width="80" />
        <el-table-column label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '启用' : '禁用' }}
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

    <!-- 新增/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑学科' : '新增学科'"
      width="500px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="formRef"
        :model="subjectForm"
        :rules="formRules"
        label-width="100px"
      >
        <el-form-item label="学科名称" prop="name">
          <el-input v-model="subjectForm.name" placeholder="请输入学科名称" />
        </el-form-item>
        <el-form-item label="学科代码" prop="code">
          <el-input v-model="subjectForm.code" placeholder="请输入学科代码" />
        </el-form-item>
        <el-form-item label="学科类型" prop="type">
          <el-select v-model="subjectForm.type" placeholder="请选择学科类型" style="width: 100%">
            <el-option label="理科" :value="1" />
            <el-option label="文科" :value="2" />
            <el-option label="综合" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="学段" prop="stage">
          <el-select v-model="subjectForm.stage" placeholder="请选择学段" style="width: 100%">
            <el-option label="小学" :value="1" />
            <el-option label="初中" :value="2" />
            <el-option label="高中" :value="3" />
          </el-select>
        </el-form-item>
        <el-form-item label="排序" prop="sort_order">
          <el-input-number v-model="subjectForm.sort_order" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-radio-group v-model="subjectForm.status">
            <el-radio :label="1">启用</el-radio>
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
import { Plus } from '@element-plus/icons-vue'
import {
  getSubjectListApi,
  createSubjectApi,
  updateSubjectApi,
  deleteSubjectApi,
  type Subject,
  type CreateSubjectParams,
  type UpdateSubjectParams,
  SUBJECT_TYPES,
  SUBJECT_STAGES
} from '@/api/subject'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const formRef = ref<FormInstance>()

// 学科列表
const subjectList = ref<Subject[]>([])

// 搜索表单
const searchForm = reactive({
  search: '',
  type: undefined as number | undefined,
  stage: undefined as number | undefined,
  status: undefined as number | undefined
})

// 分页信息
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 学科表单
const subjectForm = reactive({
  id: 0,
  name: '',
  code: '',
  type: 1,
  stage: 1,
  sort_order: 0,
  status: 1
})

// 表单验证规则
const formRules: FormRules = {
  name: [
    { required: true, message: '请输入学科名称', trigger: 'blur' },
    { min: 2, max: 50, message: '学科名称长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入学科代码', trigger: 'blur' },
    { min: 2, max: 20, message: '学科代码长度在 2 到 20 个字符', trigger: 'blur' }
  ],
  type: [
    { required: true, message: '请选择学科类型', trigger: 'change' }
  ],
  stage: [
    { required: true, message: '请选择学段', trigger: 'change' }
  ]
}

// 获取学科列表
const fetchSubjectList = async () => {
  try {
    loading.value = true
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm
    }

    const response = await getSubjectListApi(params)

    // 处理响应数据，添加类型和学段名称
    const subjects = Array.isArray(response.data.data) ? response.data.data : response.data
    subjectList.value = subjects.map((subject: Subject) => ({
      ...subject,
      type_name: SUBJECT_TYPES[subject.type as keyof typeof SUBJECT_TYPES] || '未知',
      stage_name: SUBJECT_STAGES[subject.stage as keyof typeof SUBJECT_STAGES] || '未知'
    }))

    // 更新分页信息
    if (response.data.pagination) {
      Object.assign(pagination, response.data.pagination)
    } else {
      pagination.total = subjectList.value.length
    }
  } catch (error) {
    console.error('获取学科列表失败:', error)
    ElMessage.error('获取学科列表失败')
    subjectList.value = []
    pagination.total = 0
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  fetchSubjectList()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    search: '',
    type: undefined,
    stage: undefined,
    status: undefined
  })
  pagination.current_page = 1
  fetchSubjectList()
}

// 分页大小改变
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  fetchSubjectList()
}

// 当前页改变
const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  fetchSubjectList()
}

// 新增学科
const handleCreate = () => {
  isEdit.value = false
  resetForm()
  dialogVisible.value = true
}

// 编辑学科
const handleEdit = (subject: Subject) => {
  isEdit.value = true
  Object.assign(subjectForm, {
    id: subject.id,
    name: subject.name,
    code: subject.code,
    type: subject.type,
    stage: subject.stage,
    sort_order: subject.sort_order,
    status: subject.status
  })
  dialogVisible.value = true
}

// 删除学科
const handleDelete = async (subject: Subject) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除学科 "${subject.name}" 吗？此操作不可恢复！`,
      '删除学科',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await deleteSubjectApi(subject.id)
    ElMessage.success('学科删除成功')
    fetchSubjectList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除学科失败:', error)
      ElMessage.error('删除学科失败')
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
      name: subjectForm.name,
      code: subjectForm.code,
      type: subjectForm.type,
      stage: subjectForm.stage,
      sort_order: subjectForm.sort_order,
      status: subjectForm.status
    }

    if (isEdit.value) {
      await updateSubjectApi(subjectForm.id, formData)
      ElMessage.success('学科更新成功')
    } else {
      await createSubjectApi(formData as CreateSubjectParams)
      ElMessage.success('学科创建成功')
    }

    dialogVisible.value = false
    fetchSubjectList()
  } catch (error) {
    console.error('提交失败:', error)
    ElMessage.error(isEdit.value ? '学科更新失败' : '学科创建失败')
  } finally {
    submitting.value = false
  }
}

// 重置表单
const resetForm = () => {
  Object.assign(subjectForm, {
    id: 0,
    name: '',
    code: '',
    type: 1,
    stage: 1,
    sort_order: 0,
    status: 1
  })
  formRef.value?.clearValidate()
}

// 初始化
onMounted(() => {
  fetchSubjectList()
})
</script>

<style scoped>
.subject-management-page {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
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
  background: #fff;
  padding: 20px;
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
</style>
