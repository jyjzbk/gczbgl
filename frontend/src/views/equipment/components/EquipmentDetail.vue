<template>
  <el-dialog
    v-model="visible"
    title="设备详情"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="equipment" class="equipment-detail">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="设备编号">
          {{ equipment.code }}
        </el-descriptions-item>
        <el-descriptions-item label="设备名称">
          {{ equipment.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备分类">
          {{ equipment.category?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备型号">
          {{ equipment.model }}
        </el-descriptions-item>
        <el-descriptions-item label="设备品牌">
          {{ equipment.brand }}
        </el-descriptions-item>
        <el-descriptions-item label="序列号">
          {{ equipment.serial_number }}
        </el-descriptions-item>
        <el-descriptions-item label="存放位置">
          {{ equipment.location }}
        </el-descriptions-item>
        <el-descriptions-item label="设备状态">
          <el-tag :type="getStatusTagType(equipment.status)">
            {{ getStatusText(equipment.status) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="设备状况">
          <el-tag :type="getConditionTagType(equipment.condition)">
            {{ getConditionText(equipment.condition) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="采购日期">
          {{ equipment.purchase_date }}
        </el-descriptions-item>
        <el-descriptions-item label="采购价格">
          ¥{{ equipment.purchase_price?.toLocaleString() }}
        </el-descriptions-item>
        <el-descriptions-item label="供应商">
          {{ equipment.supplier }}
        </el-descriptions-item>
        <el-descriptions-item label="保修期">
          {{ equipment.warranty_period }}个月
        </el-descriptions-item>
        <el-descriptions-item label="所属学校">
          {{ equipment.school?.name }}
        </el-descriptions-item>
      </el-descriptions>
      
      <!-- 技术规格 -->
      <div v-if="equipment.specifications" class="detail-section">
        <h4>技术规格</h4>
        <div class="content-text">
          {{ equipment.specifications }}
        </div>
      </div>
      
      <!-- 设备描述 -->
      <div v-if="equipment.description" class="detail-section">
        <h4>设备描述</h4>
        <div class="content-text">
          {{ equipment.description }}
        </div>
      </div>
      
      <!-- 设备照片 -->
      <div v-if="equipment.photos && equipment.photos.length > 0" class="detail-section">
        <h4>设备照片</h4>
        <div class="photo-gallery">
          <el-image
            v-for="(photo, index) in equipment.photos"
            :key="index"
            :src="photo"
            :preview-src-list="equipment.photos"
            :initial-index="index"
            fit="cover"
            class="photo-item"
          />
        </div>
      </div>
      
      <!-- 二维码 -->
      <div v-if="equipment.qr_code" class="detail-section">
        <h4>设备二维码</h4>
        <div class="qr-code-container">
          <el-image
            :src="equipment.qr_code"
            fit="contain"
            style="width: 150px; height: 150px;"
          />
          <div class="qr-code-actions">
            <el-button size="small" @click="downloadQRCode">
              下载二维码
            </el-button>
            <el-button size="small" @click="printQRCode">
              打印二维码
            </el-button>
          </div>
        </div>
      </div>
      
      <!-- 操作记录 -->
      <div class="detail-section">
        <h4>操作记录</h4>
        <el-timeline>
          <el-timeline-item
            timestamp="创建时间"
            :time="equipment.created_at"
            type="primary"
          >
            设备档案创建
          </el-timeline-item>
          <el-timeline-item
            v-if="equipment.updated_at !== equipment.created_at"
            timestamp="更新时间"
            :time="equipment.updated_at"
            type="success"
          >
            设备信息更新
          </el-timeline-item>
        </el-timeline>
      </div>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
        <el-button type="primary" @click="handleEdit">
          编辑设备
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ElMessage } from 'element-plus'
import type { Equipment } from '@/api/equipment'

interface Props {
  modelValue: boolean
  equipment?: Equipment | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'edit', equipment: Equipment): void
}

const props = withDefaults(defineProps<Props>(), {
  equipment: null
})

const emit = defineEmits<Emits>()

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

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

// 状况标签类型
const getConditionTagType = (condition: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'success',
    3: 'warning',
    4: 'danger'
  }
  return typeMap[condition] || 'info'
}

// 状况文本
const getConditionText = (condition: number) => {
  const textMap: Record<number, string> = {
    1: '优',
    2: '良',
    3: '中',
    4: '差'
  }
  return textMap[condition] || '未知'
}

// 下载二维码
const downloadQRCode = () => {
  if (!props.equipment?.qr_code) return
  
  const link = document.createElement('a')
  link.href = props.equipment.qr_code
  link.download = `${props.equipment.code}_qrcode.png`
  link.click()
  
  ElMessage.success('二维码下载成功')
}

// 打印二维码
const printQRCode = () => {
  if (!props.equipment?.qr_code) return
  
  const printWindow = window.open('', '_blank')
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>设备二维码 - ${props.equipment.code}</title>
          <style>
            body { text-align: center; margin: 20px; }
            img { max-width: 200px; }
            .info { margin: 10px 0; font-size: 14px; }
          </style>
        </head>
        <body>
          <div class="info">设备编号: ${props.equipment.code}</div>
          <div class="info">设备名称: ${props.equipment.name}</div>
          <img src="${props.equipment.qr_code}" alt="设备二维码" />
        </body>
      </html>
    `)
    printWindow.document.close()
    printWindow.print()
  }
}

// 编辑设备
const handleEdit = () => {
  if (props.equipment) {
    emit('edit', props.equipment)
    handleClose()
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.equipment-detail {
  max-height: 600px;
  overflow-y: auto;
}

.detail-section {
  margin-top: 24px;
}

.detail-section h4 {
  margin: 0 0 12px 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.content-text {
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
  line-height: 1.6;
  white-space: pre-wrap;
}

.photo-gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.photo-item {
  width: 100px;
  height: 100px;
  border-radius: 6px;
  cursor: pointer;
}

.qr-code-container {
  display: flex;
  align-items: center;
  gap: 16px;
}

.qr-code-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dialog-footer {
  text-align: right;
}
</style>
