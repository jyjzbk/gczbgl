<template>
  <el-dialog
    v-model="visible"
    title="实验照片"
    width="800px"
    :before-close="handleClose"
  >
    <div v-if="photos.length > 0" class="photo-viewer">
      <!-- 主要显示区域 -->
      <div class="main-photo">
        <el-image
          :src="photos[currentIndex]"
          fit="contain"
          class="main-image"
          :preview-src-list="photos"
          :initial-index="currentIndex"
        />
      </div>
      
      <!-- 照片信息 -->
      <div class="photo-info">
        <div class="photo-counter">
          {{ currentIndex + 1 }} / {{ photos.length }}
        </div>
        <div class="photo-actions">
          <el-button-group>
            <el-button 
              :disabled="currentIndex === 0"
              @click="prevPhoto"
            >
              <el-icon><ArrowLeft /></el-icon>
              上一张
            </el-button>
            <el-button 
              :disabled="currentIndex === photos.length - 1"
              @click="nextPhoto"
            >
              下一张
              <el-icon><ArrowRight /></el-icon>
            </el-button>
          </el-button-group>
          
          <el-button @click="downloadPhoto">
            <el-icon><Download /></el-icon>
            下载
          </el-button>
        </div>
      </div>
      
      <!-- 缩略图列表 -->
      <div v-if="photos.length > 1" class="thumbnail-list">
        <div class="thumbnail-container">
          <div
            v-for="(photo, index) in photos"
            :key="index"
            class="thumbnail-item"
            :class="{ active: index === currentIndex }"
            @click="setCurrentPhoto(index)"
          >
            <el-image
              :src="photo"
              fit="cover"
              class="thumbnail-image"
            />
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="no-photos">
      <el-empty description="暂无照片" />
    </div>
    
    <template #footer>
      <el-button @click="handleClose">关闭</el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { ArrowLeft, ArrowRight, Download } from '@element-plus/icons-vue'

interface Props {
  modelValue: boolean
  photos: string[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 对话框显示状态
const visible = ref(false)

// 当前照片索引
const currentIndex = ref(0)

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      currentIndex.value = 0
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 上一张照片
const prevPhoto = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

// 下一张照片
const nextPhoto = () => {
  if (currentIndex.value < props.photos.length - 1) {
    currentIndex.value++
  }
}

// 设置当前照片
const setCurrentPhoto = (index: number) => {
  currentIndex.value = index
}

// 下载照片
const downloadPhoto = () => {
  if (props.photos[currentIndex.value]) {
    const link = document.createElement('a')
    link.href = props.photos[currentIndex.value]
    link.download = `实验照片_${currentIndex.value + 1}.jpg`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    ElMessage.success('照片下载成功')
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
}

// 键盘事件处理
const handleKeydown = (event: KeyboardEvent) => {
  if (!visible.value) return
  
  switch (event.key) {
    case 'ArrowLeft':
      prevPhoto()
      break
    case 'ArrowRight':
      nextPhoto()
      break
    case 'Escape':
      handleClose()
      break
  }
}

// 添加键盘事件监听
watch(visible, (newVal) => {
  if (newVal) {
    document.addEventListener('keydown', handleKeydown)
  } else {
    document.removeEventListener('keydown', handleKeydown)
  }
})
</script>

<style scoped>
.photo-viewer {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.main-photo {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.main-image {
  max-width: 100%;
  max-height: 400px;
}

.photo-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #f8f9fa;
  border-radius: 6px;
}

.photo-counter {
  font-size: 14px;
  color: #606266;
  font-weight: 500;
}

.photo-actions {
  display: flex;
  gap: 12px;
}

.thumbnail-list {
  max-height: 120px;
  overflow-y: auto;
}

.thumbnail-container {
  display: flex;
  gap: 8px;
  padding: 8px 0;
}

.thumbnail-item {
  flex-shrink: 0;
  width: 80px;
  height: 80px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s;
}

.thumbnail-item:hover {
  border-color: #409eff;
  transform: scale(1.05);
}

.thumbnail-item.active {
  border-color: #409eff;
  box-shadow: 0 0 8px rgba(64, 158, 255, 0.3);
}

.thumbnail-image {
  width: 100%;
  height: 100%;
}

.no-photos {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}

/* 滚动条样式 */
.thumbnail-list::-webkit-scrollbar {
  height: 6px;
}

.thumbnail-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.thumbnail-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.thumbnail-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

@media (max-width: 768px) {
  .main-photo {
    min-height: 300px;
  }
  
  .main-image {
    max-height: 300px;
  }
  
  .photo-info {
    flex-direction: column;
    gap: 12px;
    text-align: center;
  }
  
  .thumbnail-item {
    width: 60px;
    height: 60px;
  }
}
</style>
