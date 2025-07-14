<template>
  <el-dialog
    v-model="visible"
    :title="dialogTitle"
    width="600px"
    :before-close="handleClose"
  >
    <div class="qrcode-content">
      <!-- 单个设备二维码 -->
      <div v-if="!batchMode && equipment" class="single-qrcode">
        <div class="equipment-info">
          <h4>{{ equipment.name }}</h4>
          <p>设备编号: {{ equipment.code }}</p>
          <p>设备型号: {{ equipment.model }}</p>
          <p>存放位置: {{ equipment.location }}</p>
        </div>
        
        <div class="qrcode-display">
          <div v-if="qrCodeUrl" class="qrcode-image">
            <el-image
              :src="qrCodeUrl"
              fit="contain"
              style="width: 200px; height: 200px;"
            />
          </div>
          <div v-else class="qrcode-placeholder">
            <el-button 
              type="primary" 
              :loading="generating"
              @click="generateSingleQRCode"
            >
              {{ generating ? '生成中...' : '生成二维码' }}
            </el-button>
          </div>
        </div>
      </div>
      
      <!-- 批量设备二维码 -->
      <div v-else-if="batchMode && selectedEquipments.length > 0" class="batch-qrcode">
        <div class="batch-info">
          <p>已选择 {{ selectedEquipments.length }} 个设备</p>
          <el-button 
            type="primary" 
            :loading="generating"
            @click="generateBatchQRCodes"
          >
            {{ generating ? '生成中...' : '批量生成二维码' }}
          </el-button>
        </div>
        
        <div v-if="batchResults.length > 0" class="batch-results">
          <h4>生成结果</h4>
          <div class="results-grid">
            <div 
              v-for="result in batchResults" 
              :key="result.equipment.id"
              class="result-item"
            >
              <div class="equipment-name">{{ result.equipment.name }}</div>
              <div class="equipment-code">{{ result.equipment.code }}</div>
              <div v-if="result.success" class="qrcode-mini">
                <el-image
                  :src="result.qrCodeUrl"
                  fit="contain"
                  style="width: 80px; height: 80px;"
                />
              </div>
              <div v-else class="error-message">
                生成失败: {{ result.error }}
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 操作按钮 -->
      <div v-if="qrCodeUrl || batchResults.length > 0" class="action-buttons">
        <el-button @click="downloadQRCodes">
          <el-icon><Download /></el-icon>
          下载二维码
        </el-button>
        <el-button @click="printQRCodes">
          <el-icon><Printer /></el-icon>
          打印二维码
        </el-button>
        <el-button @click="previewPrint">
          <el-icon><View /></el-icon>
          打印预览
        </el-button>
      </div>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
        <el-button 
          v-if="!qrCodeUrl && !batchResults.length" 
          type="primary" 
          :loading="generating"
          @click="batchMode ? generateBatchQRCodes() : generateSingleQRCode()"
        >
          {{ generating ? '生成中...' : '生成二维码' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { Download, Printer, View } from '@element-plus/icons-vue'
import {
  generateEquipmentQRCodeApi,
  batchGenerateQRCodesApi,
  type Equipment
} from '@/api/equipment'

interface Props {
  modelValue: boolean
  equipment?: Equipment | null
  batchMode?: boolean
  selectedEquipments?: Equipment[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

interface BatchResult {
  equipment: Equipment
  success: boolean
  qrCodeUrl?: string
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  equipment: null,
  batchMode: false,
  selectedEquipments: () => []
})

const emit = defineEmits<Emits>()

// 响应式数据
const generating = ref(false)
const qrCodeUrl = ref('')
const batchResults = ref<BatchResult[]>([])

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const dialogTitle = computed(() => {
  return props.batchMode ? '批量生成二维码' : '设备二维码'
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    resetData()
    // 如果设备已有二维码，直接显示
    if (!props.batchMode && props.equipment?.qr_code) {
      qrCodeUrl.value = props.equipment.qr_code
    }
  }
})

// 重置数据
const resetData = () => {
  qrCodeUrl.value = ''
  batchResults.value = []
  generating.value = false
}

// 生成单个二维码
const generateSingleQRCode = async () => {
  if (!props.equipment) return
  
  generating.value = true
  try {
    const response = await generateEquipmentQRCodeApi(props.equipment.id)
    qrCodeUrl.value = response.data.qr_code_url
    ElMessage.success('二维码生成成功')
  } catch (error) {
    console.error('生成二维码失败:', error)
    ElMessage.error('生成二维码失败')
  } finally {
    generating.value = false
  }
}

// 批量生成二维码
const generateBatchQRCodes = async () => {
  if (props.selectedEquipments.length === 0) return
  
  generating.value = true
  try {
    const ids = props.selectedEquipments.map(eq => eq.id)
    const response = await batchGenerateQRCodesApi(ids)
    
    // 处理批量结果
    batchResults.value = props.selectedEquipments.map(equipment => {
      const result = response.data.results.find((r: any) => r.equipment_id === equipment.id)
      return {
        equipment,
        success: result?.success || false,
        qrCodeUrl: result?.qr_code_url,
        error: result?.error
      }
    })
    
    const successCount = batchResults.value.filter(r => r.success).length
    ElMessage.success(`成功生成 ${successCount} 个二维码`)
  } catch (error) {
    console.error('批量生成二维码失败:', error)
    ElMessage.error('批量生成二维码失败')
  } finally {
    generating.value = false
  }
}

// 下载二维码
const downloadQRCodes = () => {
  if (props.batchMode) {
    // 批量下载
    batchResults.value.forEach(result => {
      if (result.success && result.qrCodeUrl) {
        const link = document.createElement('a')
        link.href = result.qrCodeUrl
        link.download = `${result.equipment.code}_qrcode.png`
        link.click()
      }
    })
    ElMessage.success('批量下载完成')
  } else if (qrCodeUrl.value && props.equipment) {
    // 单个下载
    const link = document.createElement('a')
    link.href = qrCodeUrl.value
    link.download = `${props.equipment.code}_qrcode.png`
    link.click()
    ElMessage.success('下载成功')
  }
}

// 打印二维码
const printQRCodes = () => {
  const printWindow = window.open('', '_blank')
  if (!printWindow) return
  
  let content = `
    <html>
      <head>
        <title>设备二维码打印</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .qrcode-item { 
            display: inline-block; 
            margin: 10px; 
            text-align: center; 
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 10px;
            width: 200px;
          }
          .equipment-info { margin-bottom: 8px; font-size: 12px; }
          .equipment-name { font-weight: bold; margin-bottom: 4px; }
          .equipment-code { color: #666; }
          img { max-width: 150px; height: 150px; }
          @media print {
            .qrcode-item { margin: 5px; }
          }
        </style>
      </head>
      <body>
  `
  
  if (props.batchMode) {
    batchResults.value.forEach(result => {
      if (result.success && result.qrCodeUrl) {
        content += `
          <div class="qrcode-item">
            <div class="equipment-info">
              <div class="equipment-name">${result.equipment.name}</div>
              <div class="equipment-code">编号: ${result.equipment.code}</div>
              <div class="equipment-code">型号: ${result.equipment.model}</div>
            </div>
            <img src="${result.qrCodeUrl}" alt="设备二维码" />
          </div>
        `
      }
    })
  } else if (qrCodeUrl.value && props.equipment) {
    content += `
      <div class="qrcode-item">
        <div class="equipment-info">
          <div class="equipment-name">${props.equipment.name}</div>
          <div class="equipment-code">编号: ${props.equipment.code}</div>
          <div class="equipment-code">型号: ${props.equipment.model}</div>
        </div>
        <img src="${qrCodeUrl.value}" alt="设备二维码" />
      </div>
    `
  }
  
  content += `
      </body>
    </html>
  `
  
  printWindow.document.write(content)
  printWindow.document.close()
  printWindow.print()
}

// 打印预览
const previewPrint = () => {
  printQRCodes()
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.qrcode-content {
  min-height: 200px;
}

.single-qrcode {
  display: flex;
  gap: 24px;
  align-items: flex-start;
}

.equipment-info {
  flex: 1;
}

.equipment-info h4 {
  margin: 0 0 12px 0;
  font-size: 18px;
  font-weight: 600;
}

.equipment-info p {
  margin: 8px 0;
  color: #666;
}

.qrcode-display {
  flex-shrink: 0;
  text-align: center;
}

.qrcode-placeholder {
  width: 200px;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ddd;
  border-radius: 8px;
}

.batch-info {
  text-align: center;
  margin-bottom: 24px;
}

.batch-info p {
  margin-bottom: 16px;
  font-size: 16px;
  color: #666;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
  max-height: 400px;
  overflow-y: auto;
}

.result-item {
  text-align: center;
  padding: 12px;
  border: 1px solid #eee;
  border-radius: 6px;
}

.equipment-name {
  font-weight: 600;
  margin-bottom: 4px;
  font-size: 12px;
}

.equipment-code {
  color: #666;
  font-size: 11px;
  margin-bottom: 8px;
}

.qrcode-mini {
  margin-bottom: 8px;
}

.error-message {
  color: #f56c6c;
  font-size: 11px;
}

.action-buttons {
  margin-top: 24px;
  text-align: center;
  display: flex;
  justify-content: center;
  gap: 12px;
}

.dialog-footer {
  text-align: right;
}
</style>
