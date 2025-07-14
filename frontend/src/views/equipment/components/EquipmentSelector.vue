<template>
  <el-dialog
    v-model="visible"
    title="选择设备"
    width="800px"
    :before-close="handleClose"
  >
    <div class="equipment-selector">
      <!-- 搜索筛选 -->
      <div class="selector-search">
        <el-form :model="searchForm" inline>
          <el-form-item label="设备分类">
            <el-select
              v-model="searchForm.category_id"
              placeholder="请选择分类"
              clearable
              style="width: 150px"
            >
              <el-option
                v-for="category in categories"
                :key="category.id"
                :label="category.name"
                :value="category.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="设备状态">
            <el-select
              v-model="searchForm.status"
              placeholder="请选择状态"
              clearable
              style="width: 120px"
            >
              <el-option label="正常" :value="1" />
              <el-option label="借出" :value="2" />
              <el-option label="维修" :value="3" />
              <el-option label="报废" :value="4" />
            </el-select>
          </el-form-item>
          
          <el-form-item label="关键词">
            <el-input
              v-model="searchForm.search"
              placeholder="设备名称/编号"
              clearable
              style="width: 200px"
            />
          </el-form-item>
          
          <el-form-item>
            <el-button type="primary" @click="handleSearch">
              搜索
            </el-button>
            <el-button @click="handleReset">
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      
      <!-- 已选设备统计 -->
      <div class="selection-summary">
        <span>已选择 {{ selectedEquipments.length }} 个设备</span>
        <el-button size="small" @click="clearSelection">清空选择</el-button>
      </div>
      
      <!-- 设备列表 -->
      <div class="equipment-list">
        <el-table
          ref="tableRef"
          v-loading="loading"
          :data="tableData"
          @selection-change="handleSelectionChange"
          border
          height="400"
        >
          <el-table-column type="selection" width="55" />
          
          <el-table-column
            prop="name"
            label="设备名称"
            min-width="150"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="code"
            label="设备编号"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="category.name"
            label="设备分类"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="model"
            label="型号"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="location"
            label="存放位置"
            width="120"
            show-overflow-tooltip
          />
          
          <el-table-column
            prop="status"
            label="状态"
            width="80"
            align="center"
          >
            <template #default="{ row }">
              <el-tag :type="getStatusTagType(row.status)">
                {{ getStatusText(row.status) }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>
      </div>
      
      <!-- 分页 -->
      <div class="selector-pagination">
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
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button type="primary" @click="handleConfirm">
          确定 ({{ selectedEquipments.length }})
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import {
  getEquipmentsApi,
  getEquipmentCategoriesApi,
  type Equipment,
  type EquipmentCategory
} from '@/api/equipment'

interface Props {
  modelValue: boolean
  selected?: Equipment[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm', equipments: Equipment[]): void
}

const props = withDefaults(defineProps<Props>(), {
  selected: () => []
})

const emit = defineEmits<Emits>()

// 响应式数据
const tableRef = ref()
const loading = ref(false)
const tableData = ref<Equipment[]>([])
const selectedEquipments = ref<Equipment[]>([])
const categories = ref<EquipmentCategory[]>([])

// 搜索表单
const searchForm = reactive({
  category_id: undefined,
  status: undefined,
  search: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    selectedEquipments.value = [...props.selected]
    loadData()
    loadCategories()
  }
})

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm
    }
    
    const response = await getEquipmentsApi(params)
    tableData.value = response.data.items
    pagination.total = response.data.pagination.total
    
    // 恢复选中状态
    setTimeout(() => {
      if (tableRef.value) {
        tableData.value.forEach(row => {
          const isSelected = selectedEquipments.value.some(item => item.id === row.id)
          if (isSelected) {
            tableRef.value.toggleRowSelection(row, true)
          }
        })
      }
    }, 100)
  } catch (error) {
    console.error('加载设备数据失败:', error)
    ElMessage.error('加载设备数据失败')
  } finally {
    loading.value = false
  }
}

// 加载设备分类
const loadCategories = async () => {
  try {
    const response = await getEquipmentCategoriesApi()
    categories.value = response.data
  } catch (error) {
    console.error('加载设备分类失败:', error)
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  loadData()
}

// 重置
const handleReset = () => {
  Object.assign(searchForm, {
    category_id: undefined,
    status: undefined,
    search: ''
  })
  pagination.current_page = 1
  loadData()
}

// 清空选择
const clearSelection = () => {
  selectedEquipments.value = []
  if (tableRef.value) {
    tableRef.value.clearSelection()
  }
}

// 表格选择变化
const handleSelectionChange = (selection: Equipment[]) => {
  // 合并当前页选择和其他页已选择的设备
  const currentPageIds = tableData.value.map(item => item.id)
  const otherPageSelected = selectedEquipments.value.filter(item => !currentPageIds.includes(item.id))
  selectedEquipments.value = [...otherPageSelected, ...selection]
}

// 页码变化
const handleCurrentChange = () => {
  loadData()
}

// 页大小变化
const handleSizeChange = () => {
  pagination.current_page = 1
  loadData()
}

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'warning',
    3: 'danger',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '正常',
    2: '借出',
    3: '维修',
    4: '报废'
  }
  return textMap[status] || '未知'
}

// 确认选择
const handleConfirm = () => {
  emit('confirm', selectedEquipments.value)
  handleClose()
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}

// 初始化
onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.equipment-selector {
  max-height: 70vh;
}

.selector-search {
  margin-bottom: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.selection-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding: 12px;
  background: #e6f7ff;
  border-radius: 6px;
  font-size: 14px;
}

.equipment-list {
  margin-bottom: 16px;
}

.selector-pagination {
  display: flex;
  justify-content: flex-end;
}

.dialog-footer {
  text-align: right;
}
</style>
