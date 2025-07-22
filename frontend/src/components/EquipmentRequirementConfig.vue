<template>
  <div class="equipment-requirement-config">
    <div class="config-header">
      <h4>器材需求配置</h4>
      <div class="header-actions">
        <el-button 
          v-if="canInherit" 
          type="primary" 
          size="small" 
          @click="handleInherit"
        >
          <el-icon><Download /></el-icon>
          继承上级配置
        </el-button>
        <el-button 
          type="success" 
          size="small" 
          @click="handleAddEquipment"
        >
          <el-icon><Plus /></el-icon>
          添加器材
        </el-button>
      </div>
    </div>
    
    <!-- 器材需求列表 -->
    <div class="equipment-list">
      <el-table
        :data="equipmentRequirements"
        stripe
        size="small"
        empty-text="暂无器材需求"
      >
        <el-table-column prop="equipment.name" label="器材名称" min-width="150">
          <template #default="{ row }">
            <div class="equipment-info">
              <span class="equipment-name">{{ row.equipment?.name || '未知器材' }}</span>
              <el-tag v-if="row.is_inherited" type="info" size="small" class="inherited-tag">
                继承
              </el-tag>
            </div>
          </template>
        </el-table-column>
        
        <el-table-column prop="equipment.category.name" label="分类" width="120">
          <template #default="{ row }">
            {{ row.equipment?.category?.name || '-' }}
          </template>
        </el-table-column>
        
        <el-table-column prop="equipment.model" label="规格型号" width="120">
          <template #default="{ row }">
            {{ row.equipment?.model || '-' }}
          </template>
        </el-table-column>
        
        <el-table-column prop="equipment.unit" label="单位" width="80">
          <template #default="{ row }">
            {{ row.equipment?.unit || '个' }}
          </template>
        </el-table-column>
        
        <el-table-column prop="quantity" label="需求数量" width="120">
          <template #default="{ row, $index }">
            <el-input-number
              v-model="row.quantity"
              :min="0"
              :max="9999"
              size="small"
              style="width: 100px"
              @change="handleQuantityChange($index, row)"
            />
          </template>
        </el-table-column>
        
        <el-table-column prop="usage_type" label="使用类型" width="120">
          <template #default="{ row, $index }">
            <el-select
              v-model="row.usage_type"
              size="small"
              style="width: 100px"
              @change="handleUsageTypeChange($index, row)"
            >
              <el-option label="必需" value="required" />
              <el-option label="可选" value="optional" />
              <el-option label="替代" value="alternative" />
            </el-select>
          </template>
        </el-table-column>
        
        <el-table-column prop="remark" label="备注" min-width="150">
          <template #default="{ row, $index }">
            <el-input
              v-model="row.remark"
              size="small"
              placeholder="请输入备注"
              @change="handleRemarkChange($index, row)"
            />
          </template>
        </el-table-column>
        
        <el-table-column label="操作" width="100" fixed="right">
          <template #default="{ row, $index }">
            <el-button
              type="danger"
              link
              size="small"
              @click="handleRemoveEquipment($index)"
            >
              <el-icon><Delete /></el-icon>
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    
    <!-- 添加器材对话框 -->
    <el-dialog
      v-model="addDialogVisible"
      title="添加器材需求"
      width="800px"
    >
      <div class="add-equipment-content">
        <!-- 器材搜索 -->
        <div class="equipment-search">
          <el-form inline>
            <el-form-item label="器材分类">
              <el-select
                v-model="searchForm.category_id"
                placeholder="请选择分类"
                clearable
                style="width: 150px"
                @change="loadEquipments"
              >
                <el-option
                  v-for="category in equipmentCategories"
                  :key="category.id"
                  :label="category.name"
                  :value="category.id"
                />
              </el-select>
            </el-form-item>
            
            <el-form-item label="关键词">
              <el-input
                v-model="searchForm.search"
                placeholder="器材名称/型号"
                clearable
                style="width: 200px"
                @keyup.enter="loadEquipments"
              />
            </el-form-item>
            
            <el-form-item>
              <el-button type="primary" @click="loadEquipments">
                <el-icon><Search /></el-icon>
                搜索
              </el-button>
            </el-form-item>
          </el-form>
        </div>
        
        <!-- 器材列表 -->
        <div class="equipment-table">
          <el-table
            v-loading="equipmentLoading"
            :data="availableEquipments"
            stripe
            size="small"
            max-height="300"
            @selection-change="handleSelectionChange"
          >
            <el-table-column type="selection" width="55" />
            <el-table-column prop="name" label="器材名称" min-width="150" />
            <el-table-column prop="category.name" label="分类" width="120" />
            <el-table-column prop="model" label="规格型号" width="120" />
            <el-table-column prop="unit" label="单位" width="80" />
            <el-table-column prop="stock_quantity" label="库存" width="80" />
          </el-table>
        </div>
      </div>
      
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="addDialogVisible = false">取消</el-button>
          <el-button 
            type="primary" 
            @click="handleConfirmAdd"
            :disabled="selectedEquipments.length === 0"
          >
            确定添加 ({{ selectedEquipments.length }})
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Download, Plus, Delete, Search } from '@element-plus/icons-vue'

// 接口类型定义
interface Equipment {
  id: number
  name: string
  model?: string
  unit: string
  stock_quantity: number
  category?: {
    id: number
    name: string
  }
}

interface EquipmentRequirement {
  id?: number
  equipment_id: number
  equipment?: Equipment
  quantity: number
  usage_type: 'required' | 'optional' | 'alternative'
  remark?: string
  is_inherited?: boolean
}

interface EquipmentCategory {
  id: number
  name: string
}

// Props
interface Props {
  modelValue: EquipmentRequirement[]
  catalogId?: number
  parentCatalogId?: number
  canInherit?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  canInherit: false
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: EquipmentRequirement[]]
  'change': [value: EquipmentRequirement[]]
}>()

// 响应式数据
const equipmentRequirements = ref<EquipmentRequirement[]>([])
const addDialogVisible = ref(false)
const equipmentLoading = ref(false)
const availableEquipments = ref<Equipment[]>([])
const selectedEquipments = ref<Equipment[]>([])
const equipmentCategories = ref<EquipmentCategory[]>([])

// 搜索表单
const searchForm = reactive({
  category_id: undefined as number | undefined,
  search: ''
})

// 监听外部数据变化
const updateEquipmentRequirements = () => {
  equipmentRequirements.value = [...props.modelValue]
}

// 计算属性
const hasChanges = computed(() => {
  return JSON.stringify(equipmentRequirements.value) !== JSON.stringify(props.modelValue)
})

// 继承上级配置
const handleInherit = async () => {
  try {
    await ElMessageBox.confirm(
      '确定要继承上级实验目录的器材配置吗？这将覆盖当前配置。',
      '确认继承',
      {
        type: 'warning'
      }
    )
    
    // 这里应该调用API获取上级配置
    ElMessage.success('继承配置成功')
    emitChange()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('继承配置失败:', error)
      ElMessage.error('继承配置失败')
    }
  }
}

// 添加器材
const handleAddEquipment = () => {
  addDialogVisible.value = true
  loadEquipmentCategories()
  loadEquipments()
}

// 移除器材
const handleRemoveEquipment = (index: number) => {
  equipmentRequirements.value.splice(index, 1)
  emitChange()
}

// 数量变化
const handleQuantityChange = (index: number, row: EquipmentRequirement) => {
  emitChange()
}

// 使用类型变化
const handleUsageTypeChange = (index: number, row: EquipmentRequirement) => {
  emitChange()
}

// 备注变化
const handleRemarkChange = (index: number, row: EquipmentRequirement) => {
  emitChange()
}

// 器材选择变化
const handleSelectionChange = (selection: Equipment[]) => {
  selectedEquipments.value = selection
}

// 确认添加器材
const handleConfirmAdd = () => {
  selectedEquipments.value.forEach(equipment => {
    // 检查是否已存在
    const exists = equipmentRequirements.value.some(req => req.equipment_id === equipment.id)
    if (!exists) {
      equipmentRequirements.value.push({
        equipment_id: equipment.id,
        equipment: equipment,
        quantity: 1,
        usage_type: 'required',
        remark: '',
        is_inherited: false
      })
    }
  })
  
  addDialogVisible.value = false
  selectedEquipments.value = []
  emitChange()
}

// 加载器材分类
const loadEquipmentCategories = async () => {
  try {
    // 这里应该调用API获取器材分类
    equipmentCategories.value = [
      { id: 1, name: '实验器具' },
      { id: 2, name: '化学试剂' },
      { id: 3, name: '物理器材' },
      { id: 4, name: '生物标本' }
    ]
  } catch (error) {
    console.error('加载器材分类失败:', error)
  }
}

// 加载器材列表
const loadEquipments = async () => {
  equipmentLoading.value = true
  try {
    // 这里应该调用API获取器材列表
    // 模拟数据
    availableEquipments.value = [
      {
        id: 1,
        name: '烧杯',
        model: '100ml',
        unit: '个',
        stock_quantity: 50,
        category: { id: 1, name: '实验器具' }
      },
      {
        id: 2,
        name: '试管',
        model: '15ml',
        unit: '支',
        stock_quantity: 100,
        category: { id: 1, name: '实验器具' }
      }
    ]
  } catch (error) {
    console.error('加载器材列表失败:', error)
  } finally {
    equipmentLoading.value = false
  }
}

// 发出变化事件
const emitChange = () => {
  emit('update:modelValue', equipmentRequirements.value)
  emit('change', equipmentRequirements.value)
}

// 初始化
onMounted(() => {
  updateEquipmentRequirements()
})
</script>

<style scoped>
.equipment-requirement-config {
  border: 1px solid #e4e7ed;
  border-radius: 6px;
  overflow: hidden;
}

.config-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #f5f7fa;
  border-bottom: 1px solid #e4e7ed;
}

.config-header h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #303133;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.equipment-list {
  padding: 16px;
}

.equipment-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.equipment-name {
  font-weight: 500;
}

.inherited-tag {
  font-size: 10px;
}

.add-equipment-content {
  max-height: 500px;
}

.equipment-search {
  margin-bottom: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.equipment-table {
  border: 1px solid #e4e7ed;
  border-radius: 6px;
}

.dialog-footer {
  text-align: right;
}

:deep(.el-table--small .el-table__cell) {
  padding: 6px 0;
}

:deep(.el-input-number--small) {
  width: 100px;
}
</style>
