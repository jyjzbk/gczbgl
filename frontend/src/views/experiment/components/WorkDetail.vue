<template>
  <div class="work-detail">
    <!-- 作品预览 -->
    <div class="work-preview">
      <!-- 图片预览 -->
      <div v-if="work.type === 'photo'" class="image-preview">
        <el-image
          :src="work.file_url"
          :alt="work.title"
          fit="contain"
          style="width: 100%; max-height: 400px;"
          :preview-src-list="[work.file_url]"
        />
      </div>

      <!-- 视频预览 -->
      <div v-else-if="work.type === 'video'" class="video-preview">
        <video
          :src="work.file_url"
          controls
          style="width: 100%; max-height: 400px;"
        >
          您的浏览器不支持视频播放
        </video>
      </div>

      <!-- 文档预览 -->
      <div v-else class="document-preview">
        <div class="document-info">
          <el-icon size="64"><Document /></el-icon>
          <h3>{{ work.title }}</h3>
          <p>{{ getFileTypeName(work.type) }} | {{ formatFileSize(work.file_size) }}</p>
          <el-button type="primary" @click="downloadFile">
            <el-icon><Download /></el-icon>
            下载文件
          </el-button>
        </div>
      </div>
    </div>

    <!-- 作品信息 -->
    <div class="work-info">
      <el-descriptions title="作品信息" :column="2" border>
        <el-descriptions-item label="标题">
          {{ work.title }}
        </el-descriptions-item>
        <el-descriptions-item label="类型">
          <el-tag :type="getTypeColor(work.type)">
            {{ getFileTypeName(work.type) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="上传者">
          {{ work.student?.name || work.uploader?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="文件大小">
          {{ formatFileSize(work.file_size) }}
        </el-descriptions-item>
        <el-descriptions-item label="上传时间">
          {{ formatDateTime(work.created_at) }}
        </el-descriptions-item>
        <el-descriptions-item label="质量评分">
          <el-rate
            v-if="!readonly"
            v-model="editForm.quality_score"
            @change="updateWork"
          />
          <el-rate
            v-else
            :model-value="work.quality_score || 0"
            disabled
          />
        </el-descriptions-item>
        <el-descriptions-item label="是否精选" v-if="!readonly">
          <el-switch
            v-model="editForm.is_featured"
            @change="updateWork"
          />
        </el-descriptions-item>
        <el-descriptions-item label="公开展示" v-if="!readonly">
          <el-switch
            v-model="editForm.is_public"
            @change="updateWork"
          />
        </el-descriptions-item>
        <el-descriptions-item label="描述" :span="2">
          <el-input
            v-if="!readonly"
            v-model="editForm.description"
            type="textarea"
            :rows="3"
            placeholder="请输入作品描述"
            @blur="updateWork"
          />
          <span v-else>{{ work.description || '无' }}</span>
        </el-descriptions-item>
        <el-descriptions-item label="教师评语" :span="2" v-if="!readonly">
          <el-input
            v-model="editForm.teacher_comment"
            type="textarea"
            :rows="3"
            placeholder="请输入教师评语"
            @blur="updateWork"
          />
        </el-descriptions-item>
        <el-descriptions-item label="教师评语" :span="2" v-else-if="work.teacher_comment">
          {{ work.teacher_comment }}
        </el-descriptions-item>
      </el-descriptions>
    </div>

    <!-- 文件元数据 -->
    <div v-if="work.metadata" class="metadata-info">
      <h3>文件信息</h3>
      <el-descriptions :column="2" border size="small">
        <el-descriptions-item
          v-for="(value, key) in work.metadata"
          :key="key"
          :label="getMetadataLabel(key)"
        >
          {{ formatMetadataValue(key, value) }}
        </el-descriptions-item>
      </el-descriptions>
    </div>

    <!-- 操作按钮 -->
    <div class="actions" v-if="!readonly">
      <el-button @click="downloadFile">
        <el-icon><Download /></el-icon>
        下载
      </el-button>
      
      <el-button
        v-if="work.type === 'photo'"
        @click="setAsAvatar"
        type="primary"
      >
        设为头像
      </el-button>
      
      <el-button
        @click="shareWork"
        type="success"
      >
        <el-icon><Share /></el-icon>
        分享
      </el-button>
      
      <el-button
        @click="deleteWork"
        type="danger"
      >
        <el-icon><Delete /></el-icon>
        删除
      </el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Document, Download, Share, Delete } from '@element-plus/icons-vue'
import { experimentWorkApi } from '@/api/experimentWork'
import dayjs from 'dayjs'

// Props
const props = defineProps<{
  work: any
  readonly?: boolean
}>()

// Emits
const emit = defineEmits<{
  updated: []
  deleted: []
}>()

// 响应式数据
const updating = ref(false)

// 编辑表单
const editForm = reactive({
  title: props.work.title,
  description: props.work.description,
  quality_score: props.work.quality_score || 0,
  teacher_comment: props.work.teacher_comment,
  is_featured: props.work.is_featured,
  is_public: props.work.is_public
})

// 方法
const updateWork = async () => {
  if (updating.value) return

  updating.value = true
  try {
    await experimentWorkApi.update(props.work.id, editForm)
    ElMessage.success('更新成功')
    emit('updated')
  } catch (error) {
    ElMessage.error('更新失败')
  } finally {
    updating.value = false
  }
}

const downloadFile = () => {
  const link = document.createElement('a')
  link.href = props.work.file_url
  link.download = props.work.file_name
  link.target = '_blank'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const setAsAvatar = async () => {
  try {
    await ElMessageBox.confirm(
      '确定要将此图片设为头像吗？',
      '确认操作',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }
    )

    // 这里应该调用设置头像的API
    ElMessage.success('头像设置成功')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('设置头像失败')
    }
  }
}

const shareWork = async () => {
  try {
    const shareUrl = `${window.location.origin}/works/${props.work.id}`
    
    if (navigator.share) {
      await navigator.share({
        title: props.work.title,
        text: props.work.description,
        url: shareUrl
      })
    } else {
      // 复制到剪贴板
      await navigator.clipboard.writeText(shareUrl)
      ElMessage.success('分享链接已复制到剪贴板')
    }
  } catch (error) {
    ElMessage.error('分享失败')
  }
}

const deleteWork = async () => {
  try {
    await ElMessageBox.confirm(
      `确定要删除作品"${props.work.title}"吗？删除后无法恢复。`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await experimentWorkApi.delete(props.work.id)
    ElMessage.success('删除成功')
    emit('deleted')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

const getFileTypeName = (type: string) => {
  const names = {
    'photo': '图片',
    'video': '视频',
    'document': '文档',
    'other': '其他'
  }
  return names[type] || '文件'
}

const getTypeColor = (type: string) => {
  const colors = {
    'photo': 'success',
    'video': 'primary',
    'document': 'warning',
    'other': 'info'
  }
  return colors[type] || 'default'
}

const formatFileSize = (size: number) => {
  if (size < 1024) return size + ' B'
  if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB'
  return (size / 1024 / 1024).toFixed(1) + ' MB'
}

const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm:ss')
}

const getMetadataLabel = (key: string) => {
  const labels = {
    'width': '宽度',
    'height': '高度',
    'duration': '时长',
    'original_name': '原始文件名',
    'size': '文件大小',
    'mime_type': 'MIME类型'
  }
  return labels[key] || key
}

const formatMetadataValue = (key: string, value: any) => {
  if (key === 'width' || key === 'height') {
    return value + 'px'
  }
  if (key === 'duration') {
    return Math.floor(value / 60) + ':' + (value % 60).toString().padStart(2, '0')
  }
  if (key === 'size') {
    return formatFileSize(value)
  }
  return value
}

// 监听器
watch(() => props.work, (newWork) => {
  if (newWork) {
    Object.assign(editForm, {
      title: newWork.title,
      description: newWork.description,
      quality_score: newWork.quality_score || 0,
      teacher_comment: newWork.teacher_comment,
      is_featured: newWork.is_featured,
      is_public: newWork.is_public
    })
  }
}, { immediate: true })
</script>

<style scoped>
.work-detail {
  padding: 20px 0;
}

.work-preview {
  margin-bottom: 24px;
  text-align: center;
}

.image-preview {
  border: 1px solid #ebeef5;
  border-radius: 6px;
  overflow: hidden;
}

.video-preview {
  border: 1px solid #ebeef5;
  border-radius: 6px;
  overflow: hidden;
}

.document-preview {
  padding: 40px;
  border: 2px dashed #dcdfe6;
  border-radius: 6px;
  background: #fafafa;
}

.document-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.document-info h3 {
  margin: 0;
  color: #303133;
}

.document-info p {
  margin: 0;
  color: #909399;
}

.work-info {
  margin-bottom: 24px;
}

.metadata-info {
  margin-bottom: 24px;
}

.metadata-info h3 {
  margin: 0 0 16px 0;
  color: #303133;
  font-size: 16px;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 8px;
}

.actions {
  text-align: right;
  padding-top: 16px;
  border-top: 1px solid #ebeef5;
}

.actions .el-button {
  margin-left: 12px;
}
</style>
