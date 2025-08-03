<template>
  <el-dialog
    v-model="visible"
    title="模板管理"
    width="900px"
    :before-close="handleClose"
  >
    <div class="template-management">
      <!-- 操作栏 -->
      <div class="action-bar">
        <el-button type="primary" :icon="Plus" @click="showCreateDialog">
          新建模板
        </el-button>
        <div class="search-bar">
          <el-input
            v-model="searchForm.search"
            placeholder="搜索模板名称"
            style="width: 200px"
            clearable
            @input="handleSearch"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
        </div>
      </div>

      <!-- 模板列表 -->
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        style="width: 100%; margin-top: 16px"
      >
        <el-table-column prop="name" label="模板名称" width="150" />
        <el-table-column prop="description" label="描述" min-width="200" />
        <el-table-column prop="creator_level_name" label="创建级别" width="100" />
        <el-table-column prop="creator_user.name" label="创建人" width="100" />
        <el-table-column prop="applicable_grades" label="适用年级" width="120">
          <template #default="{ row }">
            <el-tag
              v-for="grade in row.applicable_grades"
              :key="grade"
              size="small"
              style="margin-right: 4px"
            >
              {{ grade }}年级
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="usage_count" label="使用次数" width="100" />
        <el-table-column prop="status" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status_name }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="is_default" label="默认" width="80">
          <template #default="{ row }">
            <el-tag v-if="row.is_default === 1" type="warning" size="small">
              默认
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="handleEdit(row)"
            >
              编辑
            </el-button>
            <el-button
              type="info"
              size="small"
              @click="handleView(row)"
            >
              查看
            </el-button>
            <el-button
              type="danger"
              size="small"
              @click="handleDelete(row)"
            >
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
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
      </div>
    </template>

    <!-- 创建/编辑模板对话框 -->
    <CreateTemplateDialog
      v-model="createDialogVisible"
      :mode="dialogMode"
      :template="currentTemplate"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
      @success="handleCreateSuccess"
    />

    <!-- 查看模板对话框 -->
    <ViewTemplateDialog
      v-model="viewDialogVisible"
      :template="currentTemplate"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
    />
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search } from '@element-plus/icons-vue'
import { textbookAssignmentTemplateApi } from '@/api/textbookAssignment'
import type {
  TextbookAssignmentTemplate,
  TemplateListParams
} from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'
import CreateTemplateDialog from './CreateTemplateDialog.vue'
import ViewTemplateDialog from './ViewTemplateDialog.vue'

interface Props {
  modelValue: boolean
  subjects: Subject[]
  textbookVersions: TextbookVersion[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const loading = ref(false)
const visible = ref(false)
const tableData = ref<TextbookAssignmentTemplate[]>([])
const currentTemplate = ref<TextbookAssignmentTemplate | null>(null)

// 对话框状态
const createDialogVisible = ref(false)
const viewDialogVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')

// 搜索表单
const searchForm = reactive<TemplateListParams>({
  search: '',
  status: undefined,
  creator_level: undefined
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 10,
  total: 0
})

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      loadTemplates()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 方法
const loadTemplates = async () => {
  loading.value = true
  try {
    const params = {
      ...searchForm,
      page: pagination.current_page,
      per_page: pagination.per_page
    }
    
    const response = await textbookAssignmentTemplateApi.getTemplates(params)
    const data = response.data.data || response.data
    
    tableData.value = data.items || data
    pagination.total = data.pagination?.total || 0
  } catch (error) {
    console.error('加载模板列表失败:', error)
    ElMessage.error('加载模板列表失败')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.current_page = 1
  loadTemplates()
}

const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadTemplates()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadTemplates()
}

const showCreateDialog = () => {
  dialogMode.value = 'create'
  currentTemplate.value = null
  createDialogVisible.value = true
}

const handleEdit = (template: TextbookAssignmentTemplate) => {
  dialogMode.value = 'edit'
  currentTemplate.value = template
  createDialogVisible.value = true
}

const handleView = (template: TextbookAssignmentTemplate) => {
  currentTemplate.value = template
  viewDialogVisible.value = true
}

const handleDelete = async (template: TextbookAssignmentTemplate) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除模板"${template.name}"吗？此操作不可恢复。`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await textbookAssignmentTemplateApi.deleteTemplate(template.id)
    ElMessage.success('删除成功')
    loadTemplates()
    emit('success')
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除模板失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

const handleCreateSuccess = () => {
  loadTemplates()
  emit('success')
}

const handleClose = () => {
  visible.value = false
}
</script>

<style scoped>
.template-management {
  padding: 10px 0;
}

.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.search-bar {
  display: flex;
  gap: 12px;
  align-items: center;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.dialog-footer {
  text-align: right;
}
</style>
