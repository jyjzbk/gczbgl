<template>
  <el-dialog
    v-model="visible"
    title="批量添加器材"
    width="1000px"
    :before-close="handleClose"
  >
    <div class="batch-add-dialog">
      <!-- 搜索筛选 -->
      <div class="search-section">
        <el-form :model="searchForm" inline>
          <el-form-item label="器材名称">
            <el-input
              v-model="searchForm.keyword"
              placeholder="输入器材名称或编号"
              clearable
              style="width: 200px"
            />
          </el-form-item>
          
          <el-form-item label="器材分类">
            <el-select v-model="searchForm.category_id" placeholder="选择分类" clearable>
              <el-option
                v-for="category in categories"
                :key="category.id"
                :label="category.name"
                :value="category.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="状态">
            <el-select v-model="searchForm.status" placeholder="选择状态" clearable>
              <el-option label="正常" value="normal" />
              <el-option label="维修中" value="maintenance" />
              <el-option label="报废" value="scrapped" />
            </el-select>
          </el-form-item>
          
          <el-form-item>
            <el-button @click="searchEquipment" type="primary" icon="Search">
              搜索
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 器材列表 -->
      <div class="equipment-section">
        <div class="section-header">
          <span>选择器材 ({{ selectedEquipment.length }}/{{ equipmentList.length }})</span>
          <div class="batch-actions">
            <el-button @click="selectAll" size="small">全选</el-button>
            <el-button @click="selectNone" size="small">取消全选</el-button>
          </div>
        </div>
        
        <div v-if="loading" class="loading-container">
          <el-skeleton :rows="5" animated />
        </div>
        
        <div v-else class="equipment-list">
          <el-table
            :data="equipmentList"
            @selection-change="handleSelectionChange"
            height="400"
          >
            <el-table-column type="selection" width="55" />
            
            <el-table-column prop="name" label="器材名称" min-width="150">
              <template #default="{ row }">
                <div class="equipment-info">
                  <span class="equipment-name">{{ row.name }}</span>
                  <el-tag size="small">{{ row.code }}</el-tag>
                </div>
              </template>
            </el-table-column>
            
            <el-table-column prop="category_name" label="分类" width="120" />
            
            <el-table-column prop="model" label="型号" width="120" />
            
            <el-table-column prop="quantity" label="库存" width="80">
              <template #default="{ row }">
                <span :class="{ 'low-stock': row.quantity < 5 }">
                  {{ row.quantity }} {{ row.unit }}
                </span>
              </template>
            </el-table-column>
            
            <el-table-column prop="status" label="状态" width="80">
              <template #default="{ row }">
                <el-tag :type="getStatusType(row.status)" size="small">
                  {{ getStatusText(row.status) }}
                </el-tag>
              </template>
            </el-table-column>
            
            <el-table-column label="配置" width="300">
              <template #default="{ row }">
                <div v-if="isSelected(row.id)" class="equipment-config">
                  <el-form :model="getEquipmentConfig(row.id)" size="small" inline>
                    <el-form-item label="数量">
                      <el-input-number
                        v-model="getEquipmentConfig(row.id).required_quantity"
                        :min="1"
                        :max="999"
                        style="width: 80px"
                      />
                    </el-form-item>
                    
                    <el-form-item label="计算方式">
                      <el-select
                        v-model="getEquipmentConfig(row.id).calculation_type"
                        style="width: 100px"
                      >
                        <el-option label="固定" value="fixed" />
                        <el-option label="按组" value="per_group" />
                        <el-option label="按人" value="per_student" />
                      </el-select>
                    </el-form-item>
                  </el-form>
                </div>
              </template>
            </el-table-column>
          </el-table>
        </div>
        
        <!-- 分页 -->
        <div class="pagination-container">
          <el-pagination
            v-model:current-page="pagination.current"
            v-model:page-size="pagination.size"
            :total="pagination.total"
            :page-sizes="[10, 20, 50]"
            layout="total, sizes, prev, pager, next, jumper"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <span class="selected-count">已选择 {{ selectedEquipment.length }} 种器材</span>
        <div>
          <el-button @click="handleClose">取消</el-button>
          <el-button
            type="primary"
            @click="handleConfirm"
            :disabled="selectedEquipment.length === 0"
            :loading="confirming"
          >
            确认添加
          </el-button>
        </div>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { equipmentApi } from '@/api/equipment'
import { equipmentCategoryApi } from '@/api/equipmentCategory'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm', equipment: any[]): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const visible = ref(false)
const loading = ref(false)
const confirming = ref(false)

const searchForm = reactive({
  keyword: '',
  category_id: null,
  status: null
})

const pagination = reactive({
  current: 1,
  size: 20,
  total: 0
})

const categories = ref([])
const equipmentList = ref([])
const selectedEquipment = ref([])
const equipmentConfigs = ref<Record<number, any>>({})

// 监听显示状态
watch(() => props.modelValue, (val) => {
  visible.value = val
  if (val) {
    loadInitialData()
  }
})

watch(visible, (val) => {
  emit('update:modelValue', val)
})

// 生命周期
onMounted(() => {
  loadCategories()
})

// 方法
const loadCategories = async () => {
  try {
    const response = await equipmentCategoryApi.getCategories()
    categories.value = response.data
  } catch (error) {
    console.error('加载器材分类失败:', error)
  }
}

const loadInitialData = () => {
  searchEquipment()
}

const searchEquipment = async () => {
  loading.value = true
  try {
    const params = {
      ...searchForm,
      page: pagination.current,
      per_page: pagination.size,
      with_category: true
    }
    
    const response = await equipmentApi.getEquipment(params)
    equipmentList.value = response.data.data
    pagination.total = response.data.total
  } catch (error) {
    ElMessage.error('加载器材列表失败')
  } finally {
    loading.value = false
  }
}

const handleSelectionChange = (selection) => {
  selectedEquipment.value = selection
  
  // 为新选择的器材初始化配置
  selection.forEach(equipment => {
    if (!equipmentConfigs.value[equipment.id]) {
      equipmentConfigs.value[equipment.id] = {
        required_quantity: 1,
        min_quantity: 1,
        calculation_type: 'fixed',
        group_size: 4,
        is_required: true,
        is_active: true,
        sort_order: 0
      }
    }
  })
}

const isSelected = (equipmentId: number) => {
  return selectedEquipment.value.some(item => item.id === equipmentId)
}

const getEquipmentConfig = (equipmentId: number) => {
  if (!equipmentConfigs.value[equipmentId]) {
    equipmentConfigs.value[equipmentId] = {
      required_quantity: 1,
      min_quantity: 1,
      calculation_type: 'fixed',
      group_size: 4,
      is_required: true,
      is_active: true,
      sort_order: 0
    }
  }
  return equipmentConfigs.value[equipmentId]
}

const selectAll = () => {
  // 这里需要通过表格的ref来实现全选
  // 由于Element Plus的限制，这里简化处理
  selectedEquipment.value = [...equipmentList.value]
  handleSelectionChange(selectedEquipment.value)
}

const selectNone = () => {
  selectedEquipment.value = []
}

const getStatusType = (status: string) => {
  const types = {
    'normal': 'success',
    'maintenance': 'warning',
    'scrapped': 'danger'
  }
  return types[status] || 'info'
}

const getStatusText = (status: string) => {
  const texts = {
    'normal': '正常',
    'maintenance': '维修中',
    'scrapped': '报废'
  }
  return texts[status] || '未知'
}

const handleSizeChange = (size: number) => {
  pagination.size = size
  pagination.current = 1
  searchEquipment()
}

const handleCurrentChange = (current: number) => {
  pagination.current = current
  searchEquipment()
}

const handleConfirm = async () => {
  confirming.value = true
  try {
    const equipmentWithConfigs = selectedEquipment.value.map(equipment => ({
      equipment_id: equipment.id,
      ...equipmentConfigs.value[equipment.id]
    }))
    
    emit('confirm', equipmentWithConfigs)
    handleClose()
  } catch (error) {
    ElMessage.error('添加器材失败')
  } finally {
    confirming.value = false
  }
}

const handleClose = () => {
  visible.value = false
  selectedEquipment.value = []
  equipmentConfigs.value = {}
  searchForm.keyword = ''
  searchForm.category_id = null
  searchForm.status = null
  pagination.current = 1
}
</script>

<style scoped>
.batch-add-dialog {
  max-height: 700px;
}

.search-section {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-weight: 500;
}

.equipment-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.equipment-name {
  font-weight: 500;
}

.low-stock {
  color: #f56c6c;
  font-weight: 500;
}

.equipment-config {
  padding: 8px;
  background: #f8f9fa;
  border-radius: 4px;
}

.pagination-container {
  margin-top: 16px;
  text-align: center;
}

.dialog-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.selected-count {
  color: #606266;
  font-size: 14px;
}
</style>