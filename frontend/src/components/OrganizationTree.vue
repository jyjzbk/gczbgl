<template>
  <div class="organization-tree">
    <!-- 树形结构头部 -->
    <div class="tree-header">
      <div class="header-title">
        <el-icon><OfficeBuilding /></el-icon>
        <span>组织架构</span>
      </div>
      <div class="header-actions">
        <el-tooltip content="刷新组织架构">
          <el-button 
            :icon="Refresh" 
            size="small" 
            circle 
            @click="refreshTree"
            :loading="loading"
          />
        </el-tooltip>
        <el-tooltip content="展开全部">
          <el-button 
            :icon="Plus" 
            size="small" 
            circle 
            @click="expandAll"
          />
        </el-tooltip>
        <el-tooltip content="收起全部">
          <el-button 
            :icon="Minus" 
            size="small" 
            circle 
            @click="collapseAll"
          />
        </el-tooltip>
      </div>
    </div>

    <!-- 搜索框 -->
    <div class="tree-search">
      <el-input
        v-model="searchText"
        placeholder="搜索组织..."
        :prefix-icon="Search"
        clearable
        size="small"
        @input="handleSearch"
      />
    </div>

    <!-- 树形结构 -->
    <div class="tree-content" v-loading="loading">
      <el-tree
        ref="treeRef"
        :data="filteredTreeData"
        :props="treeProps"
        :default-expand-all="false"
        :expand-on-click-node="false"
        :highlight-current="true"
        node-key="id"
        @node-click="handleNodeClick"
        @node-expand="handleNodeExpand"
        @node-collapse="handleNodeCollapse"
      >
        <template #default="{ node, data }">
          <div class="tree-node">
            <div class="node-content">
              <div class="node-icon">
                <el-icon :color="getLevelColor(data.level)">
                  <component :is="getLevelIcon(data.level)" />
                </el-icon>
              </div>
              <div class="node-info">
                <div class="node-name">{{ data.name }}</div>
                <div class="node-stats" v-if="showStats">
                  <span class="stat-item" v-if="data.user_count !== undefined">
                    <el-icon><User /></el-icon>
                    {{ data.user_count }}
                  </span>
                  <span class="stat-item" v-if="data.school_count !== undefined">
                    <el-icon><School /></el-icon>
                    {{ data.school_count }}
                  </span>
                  <span class="stat-item" v-if="data.equipment_count !== undefined">
                    <el-icon><Box /></el-icon>
                    {{ data.equipment_count }}
                  </span>
                </div>
              </div>
            </div>
            <div class="node-badge" v-if="data.level">
              <el-tag :type="getLevelTagType(data.level)" size="small">
                {{ getLevelName(data.level) }}
              </el-tag>
            </div>
          </div>
        </template>
      </el-tree>
    </div>

    <!-- 空状态 -->
    <div class="tree-empty" v-if="!loading && (!treeData || treeData.length === 0)">
      <el-empty description="暂无组织数据" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import {
  OfficeBuilding,
  Refresh,
  Plus,
  Minus,
  Search,
  User,
  School,
  Box,
  Operation,
  MapLocation,
  Location,
  House
} from '@element-plus/icons-vue'
import { 
  getOrganizationTreeApi, 
  type OrganizationNode 
} from '@/api/organization'

// Props
interface Props {
  showStats?: boolean
  defaultExpandLevel?: number
  selectedNodeId?: number
}

const props = withDefaults(defineProps<Props>(), {
  showStats: true,
  defaultExpandLevel: 2,
  selectedNodeId: undefined
})

// Emits
const emit = defineEmits<{
  nodeClick: [node: OrganizationNode]
  nodeExpand: [node: OrganizationNode]
  nodeCollapse: [node: OrganizationNode]
}>()

// 响应式数据
const loading = ref(false)
const searchText = ref('')
const treeRef = ref()
const treeData = ref<OrganizationNode[]>([])

// 树形组件配置
const treeProps = {
  children: 'children',
  label: 'name'
}

// 计算属性 - 过滤后的树数据
const filteredTreeData = computed(() => {
  if (!searchText.value) {
    return treeData.value
  }
  return filterTree(treeData.value, searchText.value)
})

// 获取组织层级图标
const getLevelIcon = (level: number) => {
  const icons = {
    1: Operation,     // 省级
    2: MapLocation,   // 市级
    3: Location,      // 区县级
    4: OfficeBuilding, // 学区级
    5: House          // 学校级
  }
  return icons[level as keyof typeof icons] || OfficeBuilding
}

// 获取组织层级颜色
const getLevelColor = (level: number) => {
  const colors = {
    1: '#409EFF', // 省级 - 蓝色
    2: '#67C23A', // 市级 - 绿色
    3: '#E6A23C', // 区县级 - 橙色
    4: '#F56C6C', // 学区级 - 红色
    5: '#909399'  // 学校级 - 灰色
  }
  return colors[level as keyof typeof colors] || '#909399'
}

// 获取组织层级标签类型
const getLevelTagType = (level: number) => {
  const types = {
    1: 'primary',
    2: 'success',
    3: 'warning',
    4: 'danger',
    5: 'info'
  }
  return types[level as keyof typeof types] || 'info'
}

// 获取组织层级名称
const getLevelName = (level: number) => {
  const names = {
    1: '省级',
    2: '市级',
    3: '区县',
    4: '学区',
    5: '学校'
  }
  return names[level as keyof typeof names] || '未知'
}

// 过滤树数据
const filterTree = (tree: OrganizationNode[], keyword: string): OrganizationNode[] => {
  const result: OrganizationNode[] = []
  
  for (const node of tree) {
    if (node.name.toLowerCase().includes(keyword.toLowerCase())) {
      result.push({ ...node })
    } else if (node.children && node.children.length > 0) {
      const filteredChildren = filterTree(node.children, keyword)
      if (filteredChildren.length > 0) {
        result.push({
          ...node,
          children: filteredChildren
        })
      }
    }
  }
  
  return result
}

// 获取组织树数据
const fetchTreeData = async () => {
  try {
    loading.value = true
    const response = await getOrganizationTreeApi()
    
    if (response.success) {
      treeData.value = response.data
      
      // 设置默认展开
      await nextTick()
      setDefaultExpand()
      
      // 设置默认选中
      if (props.selectedNodeId) {
        setCurrentNode(props.selectedNodeId)
      }
    } else {
      ElMessage.error('获取组织架构失败')
    }
  } catch (error) {
    console.error('获取组织架构失败:', error)
    ElMessage.error('获取组织架构失败')
  } finally {
    loading.value = false
  }
}

// 设置默认展开
const setDefaultExpand = () => {
  if (!treeRef.value || !treeData.value.length) return

  const expandedKeys: number[] = []

  const collectExpandedKeys = (nodes: OrganizationNode[], currentLevel = 1) => {
    for (const node of nodes) {
      if (currentLevel <= props.defaultExpandLevel) {
        expandedKeys.push(node.id)
        if (node.children && node.children.length > 0) {
          collectExpandedKeys(node.children, currentLevel + 1)
        }
      }
    }
  }

  collectExpandedKeys(treeData.value)

  // 使用 nextTick 确保 DOM 更新后再设置展开状态
  nextTick(() => {
    if (treeRef.value && expandedKeys.length > 0) {
      treeRef.value.setExpandedKeys(expandedKeys)
    }
  })
}

// 设置当前选中节点
const setCurrentNode = (nodeId: number) => {
  if (treeRef.value) {
    treeRef.value.setCurrentKey(nodeId)
  }
}

// 事件处理
const handleNodeClick = (data: OrganizationNode) => {
  emit('nodeClick', data)
}

const handleNodeExpand = (data: OrganizationNode) => {
  emit('nodeExpand', data)
}

const handleNodeCollapse = (data: OrganizationNode) => {
  emit('nodeCollapse', data)
}

const handleSearch = () => {
  // 搜索时自动展开所有匹配的节点
  if (searchText.value && treeRef.value) {
    nextTick(() => {
      const expandedKeys: number[] = []
      const collectMatchedKeys = (nodes: OrganizationNode[]) => {
        for (const node of nodes) {
          if (node.name.toLowerCase().includes(searchText.value.toLowerCase())) {
            expandedKeys.push(node.id)
          }
          if (node.children && node.children.length > 0) {
            collectMatchedKeys(node.children)
          }
        }
      }
      collectMatchedKeys(filteredTreeData.value)
      if (expandedKeys.length > 0) {
        treeRef.value.setExpandedKeys(expandedKeys)
      }
    })
  }
}

const refreshTree = () => {
  fetchTreeData()
}

const expandAll = () => {
  if (treeRef.value) {
    const allKeys: number[] = []
    const collectAllKeys = (nodes: OrganizationNode[]) => {
      for (const node of nodes) {
        allKeys.push(node.id)
        if (node.children && node.children.length > 0) {
          collectAllKeys(node.children)
        }
      }
    }
    collectAllKeys(treeData.value)
    treeRef.value.setExpandedKeys(allKeys)
  }
}

const collapseAll = () => {
  if (treeRef.value) {
    treeRef.value.setExpandedKeys([])
  }
}

// 监听选中节点变化
watch(() => props.selectedNodeId, (newId) => {
  if (newId) {
    setCurrentNode(newId)
  }
})

// 组件挂载
onMounted(() => {
  fetchTreeData()
})

// 暴露方法
defineExpose({
  refreshTree,
  expandAll,
  collapseAll,
  setCurrentNode,
  getTreeData: () => treeData.value
})
</script>

<style scoped>
.organization-tree {
  height: 100%;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e4e7ed;
}

.tree-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #e4e7ed;
  background: #fafafa;
  border-radius: 8px 8px 0 0;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #303133;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.tree-search {
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.tree-content {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
}

.tree-empty {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

/* 树节点样式 */
.tree-node {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 4px 8px;
  border-radius: 6px;
  transition: all 0.2s;
}

.tree-node:hover {
  background-color: #f5f7fa;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 0;
}

.node-icon {
  flex-shrink: 0;
}

.node-info {
  flex: 1;
  min-width: 0;
}

.node-name {
  font-size: 14px;
  color: #303133;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-stats {
  display: flex;
  gap: 12px;
  margin-top: 2px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 12px;
  color: #909399;
}

.stat-item .el-icon {
  font-size: 12px;
}

.node-badge {
  flex-shrink: 0;
  margin-left: 8px;
}

/* 自定义树组件样式 */
:deep(.el-tree) {
  background: transparent;
}

:deep(.el-tree-node__content) {
  height: auto;
  padding: 4px 0;
  background: transparent;
}

:deep(.el-tree-node__content:hover) {
  background: transparent;
}

:deep(.el-tree-node.is-current > .el-tree-node__content) {
  background: transparent;
}

:deep(.el-tree-node.is-current .tree-node) {
  background-color: #e6f7ff;
  border: 1px solid #91d5ff;
}

:deep(.el-tree-node__expand-icon) {
  color: #606266;
  font-size: 14px;
}

:deep(.el-tree-node__expand-icon.expanded) {
  transform: rotate(90deg);
}

/* 响应式设计 */
@media (max-width: 768px) {
  .tree-header {
    padding: 12px;
  }

  .header-title {
    font-size: 14px;
  }

  .tree-search {
    padding: 8px 12px;
  }

  .tree-content {
    padding: 4px;
  }

  .node-stats {
    display: none;
  }

  .node-badge .el-tag {
    font-size: 10px;
    padding: 0 4px;
    height: 18px;
    line-height: 18px;
  }
}

/* 加载状态 */
.tree-content.is-loading {
  min-height: 200px;
}

/* 滚动条样式 */
.tree-content::-webkit-scrollbar {
  width: 6px;
}

.tree-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.tree-content::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.tree-content::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
