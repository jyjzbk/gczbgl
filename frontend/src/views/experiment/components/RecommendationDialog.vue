<template>
  <el-dialog
    v-model="visible"
    title="智能推荐器材"
    width="1000px"
    :before-close="handleClose"
  >
    <div class="recommendation-dialog">
      <!-- 推荐说明 -->
      <el-alert
        title="智能推荐说明"
        type="info"
        :closable="false"
        show-icon
      >
        <template #default>
          系统基于实验学科、类型和器材使用频率为您推荐合适的器材配置，您可以选择需要的器材添加到配置中。
        </template>
      </el-alert>

      <!-- 加载状态 -->
      <div v-if="loading" class="loading-container">
        <el-skeleton :rows="5" animated />
      </div>

      <!-- 推荐列表 -->
      <div v-else class="recommendations-list">
        <div class="list-header">
          <span>为您推荐 {{ recommendations.length }} 种器材</span>
          <div class="batch-actions">
            <el-button @click="selectAll" size="small">全选</el-button>
            <el-button @click="selectNone" size="small">取消全选</el-button>
            <el-button @click="selectHighConfidence" size="small" type="primary">
              选择高置信度 (≥80%)
            </el-button>
          </div>
        </div>

        <div class="recommendation-items">
          <div
            v-for="item in recommendations"
            :key="item.equipment_id"
            class="recommendation-item"
            :class="{ selected: selectedItems.includes(item.equipment_id) }"
            @click="toggleSelection(item.equipment_id)"
          >
            <!-- 选择框 -->
            <div class="item-checkbox">
              <el-checkbox
                :model-value="selectedItems.includes(item.equipment_id)"
                @change="toggleSelection(item.equipment_id)"
              />
            </div>

            <!-- 器材信息 -->
            <div class="item-info">
              <div class="item-header">
                <span class="equipment-name">{{ item.equipment_name }}</span>
                <el-tag size="small">{{ item.equipment_code }}</el-tag>
                <el-tag size="small" type="info">{{ item.category_name }}</el-tag>
              </div>
              
              <div class="item-meta">
                <span>推荐数量：{{ item.recommended_quantity }}</span>
                <span>计算方式：{{ getCalculationTypeText(item.calculation_type) }}</span>
              </div>
            </div>

            <!-- 置信度 -->
            <div class="item-confidence">
              <div class="confidence-label">置信度</div>
              <el-progress
                :percentage="Math.round(item.confidence * 100)"
                :color="getConfidenceColor(item.confidence)"
                :stroke-width="8"
              />
            </div>

            <!-- 配置调整 -->
            <div class="item-config" v-if="selectedItems.includes(item.equipment_id)">
              <el-form :model="getItemConfig(item.equipment_id)" size="small" inline>
                <el-form-item label="数量">
                  <el-input-number
                    v-model="getItemConfig(item.equipment_id).required_quantity"
                    :min="1"
                    :max="999"
                    style="width: 100px"
                  />
                </el-form-item>
                
                <el-form-item label="计算方式">
                  <el-select
                    v-model="getItemConfig(item.equipment_id).calculation_type"
                    style="width: 120px"
                  >
                    <el-option label="固定数量" value="fixed" />
                    <el-option label="按组计算" value="per_group" />
                    <el-option label="按学生计算" value="per_student" />
                  </el-select>
                </el-form-item>
                
                <el-form-item
                  v-if="getItemConfig(item.equipment_id).calculation_type === 'per_group'"
                  label="组大小"
                >
                  <el-input-number
                    v-model="getItemConfig(item.equipment_id).group_size"
                    :min="1"
                    :max="20"
                    style="width: 80px"
                  />
                </el-form-item>
                
                <el-form-item label="必需">
                  <el-switch v-model="getItemConfig(item.equipment_id).is_required" />
                </el-form-item>
              </el-form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <span class="selected-count">已选择 {{ selectedItems.length }} 种器材</span>
        <div>
          <el-button @click="handleClose">取消</el-button>
          <el-button
            type="primary"
            @click="handleConfirm"
            :disabled="selectedItems.length === 0"
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
import { ref, computed, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { equipmentRequirementApi, type EquipmentRecommendation } from '@/api/equipmentRequirement'

interface Props {
  modelValue: boolean
  catalogId: number
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm', items: any[]): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const visible = ref(false)
const loading = ref(false)
const confirming = ref(false)
const recommendations = ref<EquipmentRecommendation[]>([])
const selectedItems = ref<number[]>([])
const itemConfigs = ref<Record<number, any>>({})

// 监听显示状态
watch(() => props.modelValue, (val) => {
  visible.value = val
  if (val) {
    loadRecommendations()
  }
})

watch(visible, (val) => {
  emit('update:modelValue', val)
})

// 计算属性
const getCalculationTypeText = (type: string) => {
  const types = {
    'fixed': '固定数量',
    'per_group': '按组计算',
    'per_student': '按学生计算'
  }
  return types[type] || '固定数量'
}

const getConfidenceColor = (confidence: number) => {
  if (confidence >= 0.8) return '#67c23a'
  if (confidence >= 0.6) return '#e6a23c'
  return '#f56c6c'
}

// 方法
const loadRecommendations = async () => {
  loading.value = true
  try {
    const response = await equipmentRequirementApi.getRecommendations(props.catalogId)
    recommendations.value = response.data
    
    // 初始化配置
    recommendations.value.forEach(item => {
      itemConfigs.value[item.equipment_id] = {
        required_quantity: item.recommended_quantity,
        min_quantity: Math.max(1, Math.floor(item.recommended_quantity * 0.8)),
        calculation_type: item.calculation_type,
        group_size: 4,
        is_required: true,
        is_active: true,
        sort_order: 0
      }
    })
  } catch (error) {
    ElMessage.error('加载推荐器材失败')
  } finally {
    loading.value = false
  }
}

const getItemConfig = (equipmentId: number) => {
  return itemConfigs.value[equipmentId] || {}
}

const toggleSelection = (equipmentId: number) => {
  const index = selectedItems.value.indexOf(equipmentId)
  if (index > -1) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(equipmentId)
  }
}

const selectAll = () => {
  selectedItems.value = recommendations.value.map(item => item.equipment_id)
}

const selectNone = () => {
  selectedItems.value = []
}

const selectHighConfidence = () => {
  selectedItems.value = recommendations.value
    .filter(item => item.confidence >= 0.8)
    .map(item => item.equipment_id)
}

const handleConfirm = async () => {
  confirming.value = true
  try {
    const selectedRequirements = selectedItems.value.map(equipmentId => {
      const recommendation = recommendations.value.find(r => r.equipment_id === equipmentId)
      const config = itemConfigs.value[equipmentId]
      
      return {
        equipment_id: equipmentId,
        ...config
      }
    })
    
    emit('confirm', selectedRequirements)
    handleClose()
  } catch (error) {
    ElMessage.error('添加器材失败')
  } finally {
    confirming.value = false
  }
}

const handleClose = () => {
  visible.value = false
  selectedItems.value = []
  itemConfigs.value = {}
}
</script>

<style scoped>
.recommendation-dialog {
  max-height: 600px;
}

.loading-container {
  padding: 20px;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 20px 0;
  padding-bottom: 10px;
  border-bottom: 1px solid #e4e7ed;
}

.recommendations-list {
  margin-top: 20px;
}

.recommendation-item {
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 12px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.recommendation-item:hover {
  border-color: #409eff;
  box-shadow: 0 2px 8px rgba(64, 158, 255, 0.1);
}

.recommendation-item.selected {
  border-color: #409eff;
  background-color: #f0f9ff;
}

.item-info {
  flex: 1;
}

.item-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.equipment-name {
  font-weight: 500;
  color: #303133;
}

.item-meta {
  display: flex;
  gap: 20px;
  color: #606266;
  font-size: 14px;
}

.item-confidence {
  width: 120px;
  text-align: center;
}

.confidence-label {
  font-size: 12px;
  color: #909399;
  margin-bottom: 8px;
}

.item-config {
  width: 100%;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
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
