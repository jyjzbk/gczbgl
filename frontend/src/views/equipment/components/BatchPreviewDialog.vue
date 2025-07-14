<template>
  <el-dialog
    v-model="visible"
    title="批量生成预览"
    width="900px"
    :before-close="handleClose"
  >
    <div class="batch-preview">
      <!-- 设置信息 -->
      <el-card title="生成设置" class="settings-card">
        <el-descriptions :column="3" border>
          <el-descriptions-item label="生成模板">
            {{ getTemplateText(settings.template) }}
          </el-descriptions-item>
          <el-descriptions-item label="输出格式">
            {{ getFormatText(settings.output_format) }}
          </el-descriptions-item>
          <el-descriptions-item label="页面大小">
            {{ settings.page_size }}
          </el-descriptions-item>
          <el-descriptions-item label="每行列数">
            {{ settings.columns }}
          </el-descriptions-item>
          <el-descriptions-item label="每页行数">
            {{ settings.rows }}
          </el-descriptions-item>
          <el-descriptions-item label="设备数量">
            {{ equipments.length }}个
          </el-descriptions-item>
        </el-descriptions>
      </el-card>
      
      <!-- 预览区域 -->
      <el-card title="预览效果" class="preview-card">
        <div class="preview-container">
          <div class="preview-page" :style="pageStyle">
            <div class="preview-grid" :style="gridStyle">
              <div 
                v-for="(equipment, index) in previewEquipments" 
                :key="equipment.id"
                class="preview-item"
                :style="itemStyle"
              >
                <div class="qrcode-placeholder">
                  <div class="qr-icon">📱</div>
                </div>
                <div class="equipment-info">
                  <div class="equipment-name">{{ equipment.name }}</div>
                  <div class="equipment-code">{{ equipment.code }}</div>
                  <div v-if="settings.template !== 'simple'" class="equipment-location">
                    {{ equipment.location }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- 分页指示器 -->
        <div v-if="totalPages > 1" class="page-indicator">
          <span>预览第 1 页，共 {{ totalPages }} 页</span>
          <el-button-group>
            <el-button size="small" @click="prevPage" :disabled="currentPage === 1">
              上一页
            </el-button>
            <el-button size="small" @click="nextPage" :disabled="currentPage === totalPages">
              下一页
            </el-button>
          </el-button-group>
        </div>
      </el-card>
      
      <!-- 设备列表 -->
      <el-card title="设备列表" class="equipment-list-card">
        <el-table :data="equipments" border size="small" max-height="200">
          <el-table-column type="index" label="#" width="50" />
          <el-table-column prop="name" label="设备名称" />
          <el-table-column prop="code" label="设备编号" />
          <el-table-column prop="location" label="存放位置" />
          <el-table-column label="页码" width="80">
            <template #default="{ $index }">
              {{ Math.floor($index / itemsPerPage) + 1 }}
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
        <el-button type="primary" @click="handleGenerate">
          确认生成
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Equipment } from '@/api/equipment'

interface Props {
  modelValue: boolean
  equipments: Equipment[]
  settings: {
    template: string
    output_format: string
    columns: number
    rows: number
    page_size: string
  }
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'generate'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const currentPage = ref(1)

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const itemsPerPage = computed(() => {
  return props.settings.columns * props.settings.rows
})

const totalPages = computed(() => {
  return Math.ceil(props.equipments.length / itemsPerPage.value)
})

const previewEquipments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return props.equipments.slice(start, end)
})

const pageStyle = computed(() => {
  const sizeMap: Record<string, { width: string; height: string }> = {
    A4: { width: '210mm', height: '297mm' },
    A3: { width: '297mm', height: '420mm' },
    Letter: { width: '216mm', height: '279mm' }
  }
  
  const size = sizeMap[props.settings.page_size] || sizeMap.A4
  
  return {
    width: size.width,
    height: size.height,
    transform: 'scale(0.3)',
    transformOrigin: 'top left'
  }
})

const gridStyle = computed(() => {
  return {
    display: 'grid',
    gridTemplateColumns: `repeat(${props.settings.columns}, 1fr)`,
    gridTemplateRows: `repeat(${props.settings.rows}, 1fr)`,
    gap: '10mm',
    padding: '10mm',
    height: '100%'
  }
})

const itemStyle = computed(() => {
  return {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    border: '1px solid #ddd',
    borderRadius: '4px',
    padding: '5mm',
    backgroundColor: '#fff'
  }
})

// 模板文本
const getTemplateText = (template: string) => {
  const templateMap: Record<string, string> = {
    standard: '标准模板',
    simple: '简洁模板',
    detailed: '详细模板'
  }
  return templateMap[template] || '未知'
}

// 格式文本
const getFormatText = (format: string) => {
  const formatMap: Record<string, string> = {
    pdf: 'PDF文件',
    images: '图片压缩包',
    excel: 'Excel表格'
  }
  return formatMap[format] || '未知'
}

// 上一页
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

// 下一页
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

// 确认生成
const handleGenerate = () => {
  emit('generate')
  handleClose()
}

// 关闭对话框
const handleClose = () => {
  currentPage.value = 1
  emit('update:modelValue', false)
}
</script>

<style scoped>
.batch-preview {
  max-height: 70vh;
  overflow-y: auto;
}

.settings-card,
.preview-card,
.equipment-list-card {
  margin-bottom: 20px;
}

.preview-container {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 300px;
  background: #f5f5f5;
  border-radius: 6px;
  padding: 20px;
  overflow: auto;
}

.preview-page {
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}

.preview-item {
  text-align: center;
}

.qrcode-placeholder {
  width: 60px;
  height: 60px;
  background: #f0f0f0;
  border: 1px dashed #ccc;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}

.qr-icon {
  font-size: 24px;
  opacity: 0.5;
}

.equipment-info {
  font-size: 12px;
  line-height: 1.2;
}

.equipment-name {
  font-weight: 600;
  margin-bottom: 2px;
  color: #333;
}

.equipment-code {
  color: #666;
  margin-bottom: 2px;
}

.equipment-location {
  color: #999;
  font-size: 10px;
}

.page-indicator {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.dialog-footer {
  text-align: right;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .preview-container {
    padding: 10px;
  }
  
  .preview-page {
    transform: scale(0.2);
  }
  
  .page-indicator {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
