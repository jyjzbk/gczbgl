<template>
  <div class="textbook-chapters">
    <div class="page-card">
      <div class="page-header">
        <h2>章节结构管理</h2>
        <p>管理教材章节结构，为实验目录提供层级化的章节分类</p>
      </div>
      
      <!-- 筛选条件 -->
      <div class="table-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="学科">
            <el-select
              v-model="searchForm.subject_id"
              placeholder="请选择学科"
              clearable
              style="width: 150px"
              @change="loadData"
            >
              <el-option
                v-for="subject in subjects"
                :key="subject.id"
                :label="subject.name"
                :value="subject.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="教材版本">
            <el-select
              v-model="searchForm.textbook_version_id"
              placeholder="请选择版本"
              clearable
              style="width: 150px"
              @change="loadData"
            >
              <el-option
                v-for="version in textbookVersions"
                :key="version.id"
                :label="version.name"
                :value="version.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="年级">
            <el-select
              v-model="searchForm.grade_level"
              placeholder="请选择年级"
              clearable
              style="width: 120px"
              @change="loadData"
            >
              <el-option
                v-for="grade in gradeOptions"
                :key="grade.value"
                :label="grade.label"
                :value="grade.value"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="册次">
            <el-select
              v-model="searchForm.volume"
              placeholder="请选择册次"
              clearable
              style="width: 120px"
              @change="loadData"
            >
              <el-option label="上册" value="上册" />
              <el-option label="下册" value="下册" />
              <el-option label="全册" value="全册" />
            </el-select>
          </el-form-item>
          
          <el-form-item>
            <el-button type="primary" @click="loadData">
              <el-icon><Search /></el-icon>
              查询
            </el-button>
            <el-button @click="handleReset">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      
      <!-- 工具栏 -->
      <div class="table-toolbar">
        <div class="toolbar-left">
          <el-button 
            type="primary" 
            @click="handleCreate"
            :disabled="!searchForm.subject_id || !searchForm.textbook_version_id"
          >
            <el-icon><Plus /></el-icon>
            新增章节
          </el-button>
          <el-button @click="handleExpandAll">
            <el-icon><Expand /></el-icon>
            展开全部
          </el-button>
          <el-button @click="handleCollapseAll">
            <el-icon><Fold /></el-icon>
            收起全部
          </el-button>
        </div>
        
        <div class="toolbar-right">
          <el-tooltip content="刷新数据">
            <el-button circle @click="loadData">
              <el-icon><Refresh /></el-icon>
            </el-button>
          </el-tooltip>
        </div>
      </div>
      
      <!-- 章节树 -->
      <div class="chapter-tree-container">
        <el-tree
          ref="treeRef"
          v-loading="loading"
          :data="treeData"
          :props="treeProps"
          node-key="id"
          :default-expand-all="false"
          :expand-on-click-node="false"
          :render-content="renderTreeNode"
          class="chapter-tree"
        />
        
        <div v-if="!loading && treeData.length === 0" class="empty-state">
          <el-empty description="暂无章节数据">
            <template #image>
              <el-icon size="60" color="#ddd"><Document /></el-icon>
            </template>
            <el-button 
              type="primary" 
              @click="handleCreate"
              :disabled="!searchForm.subject_id || !searchForm.textbook_version_id"
            >
              创建第一个章节
            </el-button>
          </el-empty>
        </div>
      </div>
    </div>
    
    <!-- 新增/编辑对话框 -->
    <TextbookChapterDialog
      v-model="dialogVisible"
      :form-data="formData"
      :is-edit="isEdit"
      :subjects="subjects"
      :textbook-versions="textbookVersions"
      :grade-options="gradeOptions"
      @success="handleDialogSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, h } from 'vue'
import { ElMessage, ElMessageBox, ElButton, ElIcon, ElTag } from 'element-plus'
import { 
  Search, Refresh, Plus, Expand, Fold, Document, 
  Edit, Delete, CirclePlus 
} from '@element-plus/icons-vue'
import type { 
  TextbookChapter, 
  TextbookChapterListParams,
  CreateTextbookChapterParams,
  TextbookVersion,
  Subject 
} from '@/api/experiment'
import {
  getTextbookChaptersApi,
  getTextbookChapterTreeApi,
  deleteTextbookChapterApi,
  getTextbookVersionsApi,
  getTextbookVersionOptionsApi,
  getSubjectsApi
} from '@/api/experiment'
import TextbookChapterDialog from './components/TextbookChapterDialog.vue'

// 响应式数据
const loading = ref(false)
const treeData = ref<TextbookChapter[]>([])
const treeRef = ref()
const dialogVisible = ref(false)
const isEdit = ref(false)
const formData = ref<Partial<CreateTextbookChapterParams>>({})
const subjects = ref<Subject[]>([])
const textbookVersions = ref<TextbookVersion[]>([])

// 搜索表单
const searchForm = reactive<TextbookChapterListParams>({
  subject_id: undefined,
  textbook_version_id: undefined,
  grade_level: '',
  volume: ''
})

// 年级选项
const gradeOptions = [
  { label: '一年级', value: '1' },
  { label: '二年级', value: '2' },
  { label: '三年级', value: '3' },
  { label: '四年级', value: '4' },
  { label: '五年级', value: '5' },
  { label: '六年级', value: '6' },
  { label: '七年级', value: '7' },
  { label: '八年级', value: '8' },
  { label: '九年级', value: '9' },
  { label: '高一', value: '10' },
  { label: '高二', value: '11' },
  { label: '高三', value: '12' }
]

// 树形组件配置
const treeProps = {
  children: 'children',
  label: 'name'
}

// 自定义树节点渲染
const renderTreeNode = (h: any, { node, data }: { node: any; data: TextbookChapter }) => {
  return h('div', { class: 'tree-node' }, [
    h('div', { class: 'node-content' }, [
      h('span', { class: 'node-label' }, [
        h('span', { class: 'node-code' }, data.code),
        h('span', { class: 'node-name' }, data.name)
      ]),
      h('div', { class: 'node-tags' }, [
        h(ElTag, { size: 'small', type: 'info' }, () => `${data.level}级`),
        data.status === 1 
          ? h(ElTag, { size: 'small', type: 'success' }, () => '启用')
          : h(ElTag, { size: 'small', type: 'danger' }, () => '禁用')
      ])
    ]),
    h('div', { class: 'node-actions' }, [
      h(ElButton, {
        size: 'small',
        type: 'primary',
        link: true,
        onClick: () => handleAddChild(data)
      }, () => [h(ElIcon, () => h(CirclePlus)), '添加子章节']),
      h(ElButton, {
        size: 'small',
        type: 'primary',
        link: true,
        onClick: () => handleEdit(data)
      }, () => [h(ElIcon, () => h(Edit)), '编辑']),
      h(ElButton, {
        size: 'small',
        type: 'danger',
        link: true,
        onClick: () => handleDelete(data)
      }, () => [h(ElIcon, () => h(Delete)), '删除'])
    ])
  ])
}

// 加载基础数据
const loadBaseData = async () => {
  try {
    const [subjectsRes, versionsRes] = await Promise.all([
      getSubjectsApi(),
      getTextbookVersionOptionsApi()
    ])

    // 处理学科数据
    if (subjectsRes.data) {
      if (subjectsRes.data.items && Array.isArray(subjectsRes.data.items)) {
        subjects.value = subjectsRes.data.items
      } else if (subjectsRes.data.data && Array.isArray(subjectsRes.data.data)) {
        subjects.value = subjectsRes.data.data
      } else if (Array.isArray(subjectsRes.data)) {
        subjects.value = subjectsRes.data
      } else {
        subjects.value = []
      }
    }

    // 处理教材版本数据
    if (versionsRes.data) {
      if (versionsRes.data.items && Array.isArray(versionsRes.data.items)) {
        textbookVersions.value = versionsRes.data.items
      } else if (versionsRes.data.data && Array.isArray(versionsRes.data.data)) {
        textbookVersions.value = versionsRes.data.data
      } else if (Array.isArray(versionsRes.data)) {
        textbookVersions.value = versionsRes.data
      } else {
        textbookVersions.value = []
      }
    }
  } catch (error) {
    console.error('加载基础数据失败:', error)
    ElMessage.error('加载基础数据失败')
  }
}

// 加载章节数据
const loadData = async () => {
  if (!searchForm.subject_id || !searchForm.textbook_version_id) {
    treeData.value = []
    return
  }

  loading.value = true
  try {
    const response = await getTextbookChapterTreeApi(searchForm)

    if (response.data) {
      if (response.data.data && Array.isArray(response.data.data)) {
        treeData.value = response.data.data
      } else if (Array.isArray(response.data)) {
        treeData.value = response.data
      } else {
        treeData.value = []
      }
    } else {
      treeData.value = []
    }
  } catch (error) {
    console.error('加载章节数据失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

// 重置筛选
const handleReset = () => {
  Object.assign(searchForm, {
    subject_id: undefined,
    textbook_version_id: undefined,
    grade_level: '',
    volume: ''
  })
  treeData.value = []
}

// 展开全部
const handleExpandAll = () => {
  if (treeRef.value) {
    const expandAll = (nodes: TextbookChapter[]) => {
      nodes.forEach(node => {
        treeRef.value.setExpanded(node.id, true)
        if (node.children && node.children.length > 0) {
          expandAll(node.children)
        }
      })
    }
    expandAll(treeData.value)
  }
}

// 收起全部
const handleCollapseAll = () => {
  if (treeRef.value) {
    const collapseAll = (nodes: TextbookChapter[]) => {
      nodes.forEach(node => {
        treeRef.value.setExpanded(node.id, false)
        if (node.children && node.children.length > 0) {
          collapseAll(node.children)
        }
      })
    }
    collapseAll(treeData.value)
  }
}

// 新增章节
const handleCreate = () => {
  formData.value = {
    subject_id: searchForm.subject_id,
    textbook_version_id: searchForm.textbook_version_id,
    grade_level: searchForm.grade_level || '',
    volume: searchForm.volume || '',
    parent_id: undefined,
    level: 1,
    code: '',
    name: '',
    sort_order: 0,
    status: true
  }
  isEdit.value = false
  dialogVisible.value = true
}

// 添加子章节
const handleAddChild = (parent: TextbookChapter) => {
  formData.value = {
    subject_id: parent.subject_id,
    textbook_version_id: parent.textbook_version_id,
    grade_level: parent.grade_level,
    volume: parent.volume,
    parent_id: parent.id,
    level: parent.level + 1,
    code: '',
    name: '',
    sort_order: 0,
    status: true
  }
  isEdit.value = false
  dialogVisible.value = true
}

// 编辑章节
const handleEdit = (row: TextbookChapter) => {
  formData.value = {
    id: row.id, // 添加ID字段
    subject_id: row.subject_id,
    textbook_version_id: row.textbook_version_id,
    grade_level: row.grade_level,
    volume: row.volume,
    parent_id: row.parent_id,
    level: row.level,
    code: row.code,
    name: row.name,
    sort_order: row.sort_order,
    status: row.status === 1
  }
  isEdit.value = true
  dialogVisible.value = true
}

// 删除章节
const handleDelete = async (row: TextbookChapter) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除章节"${row.name}"吗？删除后其子章节也将被删除。`,
      '确认删除',
      {
        type: 'warning'
      }
    )
    
    await deleteTextbookChapterApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除章节失败:', error)
      ElMessage.error('删除失败')
    }
  }
}

// 对话框成功回调
const handleDialogSuccess = () => {
  loadData()
}

// 初始化
onMounted(() => {
  loadBaseData()
})
</script>

<style scoped>
.textbook-chapters {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 24px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.table-search {
  margin-bottom: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.table-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.chapter-tree-container {
  min-height: 400px;
}

.empty-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}

:deep(.tree-node) {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 8px 0;
}

:deep(.node-content) {
  display: flex;
  align-items: center;
  flex: 1;
}

:deep(.node-label) {
  display: flex;
  align-items: center;
  margin-right: 12px;
}

:deep(.node-code) {
  background: #f0f9ff;
  color: #0369a1;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 8px;
  font-family: monospace;
}

:deep(.node-name) {
  font-weight: 500;
}

:deep(.node-tags) {
  display: flex;
  gap: 4px;
  margin-left: 12px;
}

:deep(.node-actions) {
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s;
}

:deep(.tree-node:hover .node-actions) {
  opacity: 1;
}

:deep(.chapter-tree .el-tree-node__content) {
  height: auto;
  padding: 4px 0;
}
</style>
