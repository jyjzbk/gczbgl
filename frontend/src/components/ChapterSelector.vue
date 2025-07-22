<template>
  <div class="chapter-selector">
    <!-- 筛选条件 -->
    <div class="selector-filters" v-if="showFilters">
      <el-form :model="filters" inline size="small">
        <el-form-item label="学科" v-if="!hideSubject">
          <el-select
            v-model="filters.subject_id"
            placeholder="请选择学科"
            clearable
            style="width: 150px"
            @change="handleSubjectChange"
          >
            <el-option
              v-for="subject in subjects"
              :key="subject.id"
              :label="subject.name"
              :value="subject.id"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="教材版本" v-if="!hideVersion">
          <el-select
            v-model="filters.textbook_version_id"
            placeholder="请选择版本"
            clearable
            style="width: 150px"
            @change="handleVersionChange"
          >
            <el-option
              v-for="version in textbookVersions"
              :key="version.id"
              :label="version.name"
              :value="version.id"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="年级" v-if="!hideGrade">
          <el-select
            v-model="filters.grade_level"
            placeholder="请选择年级"
            clearable
            style="width: 120px"
            @change="handleGradeChange"
          >
            <el-option
              v-for="grade in gradeOptions"
              :key="grade.value"
              :label="grade.label"
              :value="grade.value"
            />
          </el-select>
        </el-form-item>
        
        <el-form-item label="册次" v-if="!hideVolume">
          <el-select
            v-model="filters.volume"
            placeholder="请选择册次"
            clearable
            style="width: 120px"
            @change="handleVolumeChange"
          >
            <el-option label="上册" value="上册" />
            <el-option label="下册" value="下册" />
            <el-option label="全册" value="全册" />
          </el-select>
        </el-form-item>
      </el-form>
    </div>
    
    <!-- 章节选择 -->
    <div class="selector-content">
      <!-- 树形选择器 -->
      <div v-if="displayMode === 'tree'" class="tree-selector">
        <el-tree
          ref="treeRef"
          v-loading="loading"
          :data="treeData"
          :props="treeProps"
          node-key="id"
          :default-expand-all="false"
          :expand-on-click-node="false"
          :check-strictly="checkStrictly"
          :show-checkbox="multiple"
          :highlight-current="!multiple"
          @node-click="handleNodeClick"
          @check="handleNodeCheck"
          class="chapter-tree"
        >
          <template #default="{ node, data }">
            <div class="tree-node-content">
              <span class="node-code">{{ data.code }}</span>
              <span class="node-name">{{ data.name }}</span>
              <el-tag size="small" type="info" class="node-level">
                {{ data.level }}级
              </el-tag>
            </div>
          </template>
        </el-tree>
        
        <div v-if="!loading && treeData.length === 0" class="empty-state">
          <el-empty description="暂无章节数据" :image-size="60">
            <template #image>
              <el-icon size="60" color="#ddd"><Document /></el-icon>
            </template>
          </el-empty>
        </div>
      </div>
      
      <!-- 下拉选择器 -->
      <div v-else class="select-selector">
        <el-select
          :model-value="selectedValue"
          placeholder="请选择章节"
          clearable
          filterable
          :multiple="multiple"
          :collapse-tags="multiple"
          :collapse-tags-tooltip="multiple"
          style="width: 100%"
          @update:model-value="handleSelectChange"
        >
          <el-option
            v-for="chapter in flatChapters"
            :key="chapter.id"
            :label="getChapterLabel(chapter)"
            :value="chapter.id"
          >
            <div class="option-content">
              <span class="option-code">{{ chapter.code }}</span>
              <span class="option-name">{{ chapter.name }}</span>
              <el-tag size="small" type="info" class="option-level">
                {{ chapter.level }}级
              </el-tag>
            </div>
          </el-option>
        </el-select>
      </div>
    </div>
    
    <!-- 已选择的章节 -->
    <div v-if="multiple && selectedChapters.length > 0" class="selected-chapters">
      <div class="selected-title">已选择章节：</div>
      <div class="selected-list">
        <el-tag
          v-for="chapter in selectedChapters"
          :key="chapter.id"
          closable
          @close="handleRemoveChapter(chapter.id)"
          class="selected-tag"
        >
          {{ chapter.code }} {{ chapter.name }}
        </el-tag>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Document } from '@element-plus/icons-vue'
import { debounce, useCache } from '@/utils/performance'
import type { 
  TextbookChapter, 
  TextbookVersion,
  Subject 
} from '@/api/experiment'
import {
  getTextbookChaptersApi,
  getTextbookChapterTreeApi,
  getTextbookVersionsApi,
  getTextbookVersionOptionsApi,
  getSubjectsApi
} from '@/api/experiment'

// Props
interface Props {
  modelValue?: number | number[] | null
  multiple?: boolean
  displayMode?: 'tree' | 'select'
  showFilters?: boolean
  hideSubject?: boolean
  hideVersion?: boolean
  hideGrade?: boolean
  hideVolume?: boolean
  checkStrictly?: boolean
  subjectId?: number
  textbookVersionId?: number
  gradeLevel?: string
  volume?: string
}

const props = withDefaults(defineProps<Props>(), {
  multiple: false,
  displayMode: 'tree',
  showFilters: true,
  hideSubject: false,
  hideVersion: false,
  hideGrade: false,
  hideVolume: false,
  checkStrictly: false
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: number | number[] | null]
  'change': [chapters: TextbookChapter | TextbookChapter[] | null]
}>()

// 响应式数据
const loading = ref(false)
const treeRef = ref()
const subjects = ref<Subject[]>([])
const textbookVersions = ref<TextbookVersion[]>([])
const treeData = ref<TextbookChapter[]>([])
const flatChapters = ref<TextbookChapter[]>([])

// 筛选条件
const filters = reactive({
  subject_id: props.subjectId || undefined,
  textbook_version_id: props.textbookVersionId || undefined,
  grade_level: props.gradeLevel || '',
  volume: props.volume || ''
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

// 计算属性
const selectedValue = computed(() => props.modelValue)

const selectedChapters = computed(() => {
  if (!props.multiple || !props.modelValue) return []
  
  const ids = Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue]
  return flatChapters.value.filter(chapter => ids.includes(chapter.id))
})

// 加载基础数据
const loadBaseData = async () => {
  try {
    const [subjectsRes, versionsRes] = await Promise.all([
      getSubjectsApi(),
      getTextbookVersionOptionsApi()
    ])

    subjects.value = subjectsRes.data.data || subjectsRes.data
    textbookVersions.value = versionsRes.data.data || versionsRes.data
  } catch (error) {
    console.error('加载基础数据失败:', error)
    ElMessage.error('加载基础数据失败')
  }
}

// 加载章节数据
const loadChapters = async () => {
  if (!filters.subject_id || !filters.textbook_version_id) {
    treeData.value = []
    flatChapters.value = []
    return
  }
  
  loading.value = true
  try {
    if (props.displayMode === 'tree') {
      const response = await getTextbookChapterTreeApi(filters)
      treeData.value = response.data.data || response.data
    } else {
      const response = await getTextbookChaptersApi(filters)
      flatChapters.value = response.data.data || response.data
    }
  } catch (error) {
    console.error('加载章节数据失败:', error)
    ElMessage.error('加载章节数据失败')
  } finally {
    loading.value = false
  }
}

// 监听筛选条件变化（添加防抖优化）
const debouncedLoadChapters = debounce(loadChapters, 300)

watch(
  () => [filters.subject_id, filters.textbook_version_id, filters.grade_level, filters.volume],
  () => {
    debouncedLoadChapters()
  },
  { deep: true }
)

// 监听外部传入的筛选条件
watch(
  () => [props.subjectId, props.textbookVersionId, props.gradeLevel, props.volume],
  ([subjectId, versionId, gradeLevel, volume]) => {
    if (subjectId !== undefined) filters.subject_id = subjectId
    if (versionId !== undefined) filters.textbook_version_id = versionId
    if (gradeLevel !== undefined) filters.grade_level = gradeLevel
    if (volume !== undefined) filters.volume = volume
  },
  { immediate: true }
)

// 筛选条件变化处理
const handleSubjectChange = () => {
  filters.textbook_version_id = undefined
  textbookVersions.value = []
  loadChapters()
}

const handleVersionChange = () => {
  loadChapters()
}

const handleGradeChange = () => {
  loadChapters()
}

const handleVolumeChange = () => {
  loadChapters()
}

// 树节点点击
const handleNodeClick = (data: TextbookChapter) => {
  if (!props.multiple) {
    emit('update:modelValue', data.id)
    emit('change', data)
  }
}

// 树节点勾选
const handleNodeCheck = (data: TextbookChapter, checked: any) => {
  if (props.multiple) {
    const checkedKeys = checked.checkedKeys as number[]
    emit('update:modelValue', checkedKeys)
    
    const checkedChapters = flattenTree(treeData.value).filter(chapter => 
      checkedKeys.includes(chapter.id)
    )
    emit('change', checkedChapters)
  }
}

// 下拉选择变化
const handleSelectChange = (value: number | number[]) => {
  emit('update:modelValue', value)
  
  if (props.multiple) {
    const ids = Array.isArray(value) ? value : [value]
    const chapters = flatChapters.value.filter(chapter => ids.includes(chapter.id))
    emit('change', chapters)
  } else {
    const chapter = flatChapters.value.find(chapter => chapter.id === value)
    emit('change', chapter || null)
  }
}

// 移除已选择的章节
const handleRemoveChapter = (id: number) => {
  if (props.multiple && Array.isArray(props.modelValue)) {
    const newValue = props.modelValue.filter(item => item !== id)
    emit('update:modelValue', newValue)
    
    const chapters = flatChapters.value.filter(chapter => newValue.includes(chapter.id))
    emit('change', chapters)
  }
}

// 获取章节标签
const getChapterLabel = (chapter: TextbookChapter) => {
  return `${chapter.code} ${chapter.name}`
}

// 扁平化树结构
const flattenTree = (tree: TextbookChapter[]): TextbookChapter[] => {
  const result: TextbookChapter[] = []
  
  const flatten = (nodes: TextbookChapter[]) => {
    nodes.forEach(node => {
      result.push(node)
      if (node.children && node.children.length > 0) {
        flatten(node.children)
      }
    })
  }
  
  flatten(tree)
  return result
}

// 初始化
onMounted(() => {
  loadBaseData()
  loadChapters()
})
</script>

<style scoped>
.chapter-selector {
  width: 100%;
}

.selector-filters {
  margin-bottom: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.selector-content {
  min-height: 200px;
}

.tree-selector {
  border: 1px solid #dcdfe6;
  border-radius: 6px;
  padding: 8px;
  max-height: 400px;
  overflow-y: auto;
}

.empty-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}

.tree-node-content {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.node-code {
  background: #f0f9ff;
  color: #0369a1;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  font-family: monospace;
}

.node-name {
  flex: 1;
  font-weight: 500;
}

.node-level {
  margin-left: auto;
}

.option-content {
  display: flex;
  align-items: center;
  gap: 8px;
}

.option-code {
  background: #f0f9ff;
  color: #0369a1;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  font-family: monospace;
}

.option-name {
  flex: 1;
}

.option-level {
  margin-left: auto;
}

.selected-chapters {
  margin-top: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.selected-title {
  font-size: 14px;
  font-weight: 500;
  color: #303133;
  margin-bottom: 8px;
}

.selected-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.selected-tag {
  margin: 0;
}

:deep(.chapter-tree .el-tree-node__content) {
  height: auto;
  padding: 4px 0;
}

:deep(.el-tree-node__expand-icon) {
  color: #409eff;
}
</style>
