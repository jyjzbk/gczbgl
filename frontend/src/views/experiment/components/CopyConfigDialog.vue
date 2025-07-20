<template>
  <el-dialog
    v-model="visible"
    title="复制器材配置"
    width="800px"
    :before-close="handleClose"
  >
    <div class="copy-config-dialog">
      <!-- 搜索筛选 -->
      <div class="search-section">
        <el-form :model="searchForm" inline>
          <el-form-item label="学科">
            <el-select v-model="searchForm.subject_id" placeholder="选择学科" clearable>
              <el-option
                v-for="subject in subjects"
                :key="subject.id"
                :label="subject.name"
                :value="subject.id"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="年级">
            <el-select v-model="searchForm.grade" placeholder="选择年级" clearable>
              <el-option
                v-for="grade in grades"
                :key="grade"
                :label="`${grade}年级`"
                :value="grade"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="实验类型">
            <el-select v-model="searchForm.experiment_type" placeholder="选择类型" clearable>
              <el-option label="演示实验" value="演示实验" />
              <el-option label="分组实验" value="分组实验" />
              <el-option label="探究实验" value="探究实验" />
              <el-option label="综合实验" value="综合实验" />
            </el-select>
          </el-form-item>
          
          <el-form-item>
            <el-button @click="searchCatalogs" type="primary" icon="Search">
              搜索
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 实验目录列表 -->
      <div class="catalogs-section">
        <div class="section-title">选择要复制的实验目录</div>
        
        <div v-if="loading" class="loading-container">
          <el-skeleton :rows="3" animated />
        </div>
        
        <div v-else class="catalogs-list">
          <div
            v-for="catalog in catalogs"
            :key="catalog.id"
            class="catalog-item"
            :class="{ selected: selectedCatalog?.id === catalog.id }"
            @click="selectCatalog(catalog)"
          >
            <div class="catalog-info">
              <div class="catalog-header">
                <span class="catalog-name">{{ catalog.name }}</span>
                <el-tag size="small">{{ catalog.code }}</el-tag>
                <el-tag size="small" type="info">{{ catalog.subject_name }}</el-tag>
              </div>
              
              <div class="catalog-meta">
                <span>{{ catalog.grade }}年级</span>
                <span>{{ catalog.experiment_type }}</span>
                <span>{{ catalog.equipment_count || 0 }} 种器材</span>
              </div>
            </div>
            
            <div class="catalog-actions">
              <el-button
                @click.stop="previewEquipment(catalog)"
                size="small"
                type="primary"
                link
              >
                预览器材
              </el-button>
            </div>
          </div>
        </div>
        
        <el-empty v-if="!loading && catalogs.length === 0" description="暂无符合条件的实验目录" />
      </div>

      <!-- 器材预览 -->
      <div v-if="previewVisible" class="equipment-preview">
        <div class="preview-header">
          <span>{{ previewCatalog?.name }} - 器材清单</span>
          <el-button @click="closePreview" size="small" icon="Close" />
        </div>
        
        <div class="equipment-list">
          <div
            v-for="equipment in previewEquipments"
            :key="equipment.id"
            class="equipment-item"
          >
            <span class="equipment-name">{{ equipment.equipment_name }}</span>
            <span class="equipment-quantity">
              {{ equipment.required_quantity }}{{ equipment.unit }}
              ({{ getCalculationTypeText(equipment.calculation_type) }})
            </span>
            <el-tag v-if="equipment.is_required" size="small" type="success">必需</el-tag>
            <el-tag v-else size="small" type="info">可选</el-tag>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">取消</el-button>
        <el-button
          type="primary"
          @click="handleConfirm"
          :disabled="!selectedCatalog"
          :loading="confirming"
        >
          确认复制
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { equipmentRequirementApi } from '@/api/equipmentRequirement'
import { experimentCatalogApi } from '@/api/experiment'
import { subjectApi } from '@/api/subject'

interface Props {
  modelValue: boolean
  catalogId: number
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm', sourceCatalogId: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const visible = ref(false)
const loading = ref(false)
const confirming = ref(false)
const previewVisible = ref(false)

const searchForm = reactive({
  subject_id: null,
  grade: null,
  experiment_type: null
})

const subjects = ref([])
const grades = ref([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])
const catalogs = ref([])
const selectedCatalog = ref(null)
const previewCatalog = ref(null)
const previewEquipments = ref([])

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
  loadSubjects()
})

// 方法
const loadSubjects = async () => {
  try {
    const response = await subjectApi.getSubjects()
    subjects.value = response.data
  } catch (error) {
    console.error('加载学科失败:', error)
  }
}

const loadInitialData = () => {
  searchCatalogs()
}

const searchCatalogs = async () => {
  loading.value = true
  try {
    const params = {
      ...searchForm,
      has_equipment_config: true, // 只显示已配置器材的实验
      exclude_id: props.catalogId // 排除当前实验
    }
    
    const response = await experimentCatalogApi.getCatalogs(params)
    catalogs.value = response.data.data || response.data
  } catch (error) {
    ElMessage.error('加载实验目录失败')
  } finally {
    loading.value = false
  }
}

const selectCatalog = (catalog) => {
  selectedCatalog.value = catalog
}

const previewEquipment = async (catalog) => {
  try {
    previewCatalog.value = catalog
    const response = await equipmentRequirementApi.getRequirements(catalog.id, { active_only: true })
    previewEquipments.value = response.data
    previewVisible.value = true
  } catch (error) {
    ElMessage.error('加载器材清单失败')
  }
}

const closePreview = () => {
  previewVisible.value = false
  previewCatalog.value = null
  previewEquipments.value = []
}

const getCalculationTypeText = (type: string) => {
  const types = {
    'fixed': '固定数量',
    'per_group': '按组计算',
    'per_student': '按学生计算'
  }
  return types[type] || '固定数量'
}

const handleConfirm = async () => {
  if (!selectedCatalog.value) return
  
  confirming.value = true
  try {
    emit('confirm', selectedCatalog.value.id)
    handleClose()
  } catch (error) {
    ElMessage.error('复制配置失败')
  } finally {
    confirming.value = false
  }
}

const handleClose = () => {
  visible.value = false
  selectedCatalog.value = null
  closePreview()
  searchForm.subject_id = null
  searchForm.grade = null
  searchForm.experiment_type = null
}
</script>

<style scoped>
.copy-config-dialog {
  max-height: 600px;
  overflow-y: auto;
}

.search-section {
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.section-title {
  font-weight: 500;
  margin-bottom: 16px;
  color: #303133;
}

.catalogs-list {
  max-height: 300px;
  overflow-y: auto;
}

.catalog-item {
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 12px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.catalog-item:hover {
  border-color: #409eff;
  box-shadow: 0 2px 8px rgba(64, 158, 255, 0.1);
}

.catalog-item.selected {
  border-color: #409eff;
  background-color: #f0f9ff;
}

.catalog-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.catalog-name {
  font-weight: 500;
  color: #303133;
}

.catalog-meta {
  display: flex;
  gap: 16px;
  color: #606266;
  font-size: 14px;
}

.equipment-preview {
  margin-top: 20px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  overflow: hidden;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #f5f7fa;
  border-bottom: 1px solid #e4e7ed;
  font-weight: 500;
}

.equipment-list {
  max-height: 200px;
  overflow-y: auto;
  padding: 16px;
}

.equipment-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.equipment-item:last-child {
  border-bottom: none;
}

.equipment-name {
  flex: 1;
  font-weight: 500;
}

.equipment-quantity {
  color: #606266;
  font-size: 14px;
}
</style>