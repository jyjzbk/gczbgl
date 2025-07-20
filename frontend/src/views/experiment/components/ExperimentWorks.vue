<template>
  <div class="experiment-works">
    <!-- 上传区域 -->
    <div v-if="!readonly" class="upload-section">
      <el-upload
        ref="uploadRef"
        :action="uploadUrl"
        :headers="uploadHeaders"
        :data="uploadData"
        :before-upload="beforeUpload"
        :on-success="handleUploadSuccess"
        :on-error="handleUploadError"
        :show-file-list="false"
        drag
        multiple
        accept="image/*,video/*,.pdf,.doc,.docx"
      >
        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
        <div class="el-upload__text">
          将文件拖到此处，或<em>点击上传</em>
        </div>
        <template #tip>
          <div class="el-upload__tip">
            支持图片、视频、PDF、Word文档，单个文件不超过50MB
          </div>
        </template>
      </el-upload>

      <!-- 上传表单 -->
      <el-dialog v-model="showUploadForm" title="上传实验作品" width="500px">
        <el-form :model="uploadForm" :rules="uploadRules" label-width="80px">
          <el-form-item label="作品标题" prop="title">
            <el-input v-model="uploadForm.title" placeholder="请输入作品标题" />
          </el-form-item>
          <el-form-item label="作品描述">
            <el-input
              v-model="uploadForm.description"
              type="textarea"
              :rows="3"
              placeholder="请输入作品描述"
            />
          </el-form-item>
          <el-form-item label="学生">
            <el-select
              v-model="uploadForm.student_id"
              placeholder="请选择学生（可选）"
              clearable
              filterable
            >
              <el-option
                v-for="student in students"
                :key="student.id"
                :label="student.name"
                :value="student.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="公开展示">
            <el-switch v-model="uploadForm.is_public" />
          </el-form-item>
        </el-form>
        <template #footer>
          <el-button @click="showUploadForm = false">取消</el-button>
          <el-button type="primary" @click="confirmUpload" :loading="uploading">
            确定上传
          </el-button>
        </template>
      </el-dialog>
    </div>

    <!-- 作品列表 -->
    <div class="works-list">
      <div class="list-header">
        <h3>实验作品 ({{ works.length }})</h3>
        <div class="view-controls">
          <el-radio-group v-model="viewMode" size="small">
            <el-radio-button label="grid">网格</el-radio-button>
            <el-radio-button label="list">列表</el-radio-button>
          </el-radio-group>
        </div>
      </div>

      <!-- 网格视图 -->
      <div v-if="viewMode === 'grid'" class="grid-view">
        <div
          v-for="work in works"
          :key="work.id"
          class="work-card"
          @click="viewWork(work)"
        >
          <div class="work-preview">
            <img
              v-if="work.type === 'photo'"
              :src="work.thumbnail_url || work.file_url"
              :alt="work.title"
              class="work-image"
            />
            <div v-else-if="work.type === 'video'" class="work-video">
              <el-icon size="48"><VideoPlay /></el-icon>
              <span>视频</span>
            </div>
            <div v-else class="work-document">
              <el-icon size="48"><Document /></el-icon>
              <span>{{ getFileTypeName(work.type) }}</span>
            </div>
          </div>
          
          <div class="work-info">
            <h4 class="work-title">{{ work.title }}</h4>
            <p class="work-meta">
              <span>{{ work.student?.name || '教师' }}</span>
              <span>{{ formatDate(work.created_at) }}</span>
            </p>
            <div class="work-actions">
              <el-button size="small" type="primary" @click.stop="viewWork(work)">
                查看
              </el-button>
              <el-button
                v-if="!readonly"
                size="small"
                type="danger"
                @click.stop="deleteWork(work)"
              >
                删除
              </el-button>
            </div>
          </div>
        </div>
      </div>

      <!-- 列表视图 -->
      <div v-else class="list-view">
        <el-table :data="works" border>
          <el-table-column label="类型" width="80" align="center">
            <template #default="{ row }">
              <el-icon v-if="row.type === 'photo'" color="#67c23a"><Picture /></el-icon>
              <el-icon v-else-if="row.type === 'video'" color="#409eff"><VideoPlay /></el-icon>
              <el-icon v-else color="#909399"><Document /></el-icon>
            </template>
          </el-table-column>
          
          <el-table-column prop="title" label="标题" min-width="150" />
          
          <el-table-column label="上传者" width="100">
            <template #default="{ row }">
              {{ row.student?.name || row.uploader?.name }}
            </template>
          </el-table-column>
          
          <el-table-column prop="file_size" label="文件大小" width="100">
            <template #default="{ row }">
              {{ formatFileSize(row.file_size) }}
            </template>
          </el-table-column>
          
          <el-table-column label="质量评分" width="120" align="center">
            <template #default="{ row }">
              <el-rate
                v-if="row.quality_score"
                :model-value="row.quality_score"
                disabled
                size="small"
              />
              <span v-else class="no-score">未评分</span>
            </template>
          </el-table-column>
          
          <el-table-column prop="created_at" label="上传时间" width="150">
            <template #default="{ row }">
              {{ formatDateTime(row.created_at) }}
            </template>
          </el-table-column>
          
          <el-table-column label="操作" width="150" fixed="right">
            <template #default="{ row }">
              <el-button size="small" type="primary" @click="viewWork(row)">
                查看
              </el-button>
              <el-button
                v-if="!readonly"
                size="small"
                type="danger"
                @click="deleteWork(row)"
              >
                删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!-- 空状态 -->
      <el-empty v-if="works.length === 0" description="暂无实验作品" />
    </div>

    <!-- 作品详情对话框 -->
    <el-dialog
      v-model="showWorkDetail"
      :title="selectedWork?.title"
      width="800px"
      :close-on-click-modal="false"
    >
      <WorkDetail
        v-if="selectedWork"
        :work="selectedWork"
        :readonly="readonly"
        @updated="loadWorks"
      />
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { UploadFilled, VideoPlay, Document, Picture } from '@element-plus/icons-vue'
import { experimentWorkApi } from '@/api/experimentWork'
import { userApi } from '@/api/user'
import { useAuthStore } from '@/stores/auth'
import WorkDetail from './WorkDetail.vue'
import dayjs from 'dayjs'

// Props
const props = defineProps<{
  recordId: number
  readonly?: boolean
}>()

// 响应式数据
const works = ref([])
const students = ref([])
const selectedWork = ref(null)
const showUploadForm = ref(false)
const showWorkDetail = ref(false)
const viewMode = ref('grid')
const uploading = ref(false)
const pendingFile = ref(null)

const authStore = useAuthStore()

// 上传表单
const uploadForm = reactive({
  title: '',
  description: '',
  student_id: null,
  is_public: false
})

const uploadRules = {
  title: [{ required: true, message: '请输入作品标题', trigger: 'blur' }]
}

// 计算属性
const uploadUrl = computed(() => {
  return `${import.meta.env.VITE_API_BASE_URL}/experiment-works`
})

const uploadHeaders = computed(() => {
  return {
    'Authorization': `Bearer ${authStore.token}`
  }
})

const uploadData = computed(() => {
  return {
    record_id: props.recordId,
    ...uploadForm
  }
})

// 方法
const loadWorks = async () => {
  try {
    const response = await experimentWorkApi.getList({
      record_id: props.recordId,
      per_page: 100
    })
    works.value = response.data
  } catch (error) {
    ElMessage.error('加载作品列表失败')
  }
}

const loadStudents = async () => {
  try {
    const response = await userApi.getList({
      role: 'student',
      per_page: 100
    })
    students.value = response.data
  } catch (error) {
    console.error('加载学生列表失败')
  }
}

const beforeUpload = (file: File) => {
  const isValidSize = file.size / 1024 / 1024 < 50 // 50MB
  if (!isValidSize) {
    ElMessage.error('文件大小不能超过50MB')
    return false
  }

  // 保存文件信息，显示上传表单
  pendingFile.value = file
  uploadForm.title = file.name.split('.')[0] // 使用文件名作为默认标题
  showUploadForm.value = true
  
  return false // 阻止自动上传
}

const confirmUpload = async () => {
  if (!uploadForm.title) {
    ElMessage.warning('请输入作品标题')
    return
  }

  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('file', pendingFile.value)
    formData.append('record_id', props.recordId.toString())
    formData.append('title', uploadForm.title)
    formData.append('description', uploadForm.description)
    if (uploadForm.student_id) {
      formData.append('student_id', uploadForm.student_id.toString())
    }
    formData.append('is_public', uploadForm.is_public.toString())

    await experimentWorkApi.upload(formData)
    
    ElMessage.success('作品上传成功')
    showUploadForm.value = false
    resetUploadForm()
    loadWorks()
  } catch (error) {
    ElMessage.error('作品上传失败')
  } finally {
    uploading.value = false
  }
}

const handleUploadSuccess = () => {
  ElMessage.success('上传成功')
  loadWorks()
}

const handleUploadError = () => {
  ElMessage.error('上传失败')
}

const viewWork = (work: any) => {
  selectedWork.value = work
  showWorkDetail.value = true
}

const deleteWork = async (work: any) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除作品"${work.title}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await experimentWorkApi.delete(work.id)
    ElMessage.success('删除成功')
    loadWorks()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

const resetUploadForm = () => {
  uploadForm.title = ''
  uploadForm.description = ''
  uploadForm.student_id = null
  uploadForm.is_public = false
  pendingFile.value = null
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

const formatFileSize = (size: number) => {
  if (size < 1024) return size + ' B'
  if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB'
  return (size / 1024 / 1024).toFixed(1) + ' MB'
}

const formatDate = (date: string) => {
  return dayjs(date).format('MM-DD')
}

const formatDateTime = (datetime: string) => {
  return dayjs(datetime).format('YYYY-MM-DD HH:mm')
}

// 生命周期
onMounted(() => {
  loadWorks()
  if (!props.readonly) {
    loadStudents()
  }
})
</script>

<style scoped>
.experiment-works {
  padding: 20px 0;
}

.upload-section {
  margin-bottom: 24px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 6px;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.list-header h3 {
  margin: 0;
  color: #303133;
}

.grid-view {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 16px;
}

.work-card {
  border: 1px solid #ebeef5;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.work-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.work-preview {
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
}

.work-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.work-video,
.work-document {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #909399;
}

.work-video span,
.work-document span {
  margin-top: 8px;
  font-size: 14px;
}

.work-info {
  padding: 12px;
}

.work-title {
  margin: 0 0 8px 0;
  font-size: 14px;
  font-weight: bold;
  color: #303133;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.work-meta {
  margin: 0 0 12px 0;
  font-size: 12px;
  color: #909399;
  display: flex;
  justify-content: space-between;
}

.work-actions {
  display: flex;
  gap: 8px;
}

.no-score {
  color: #c0c4cc;
  font-size: 12px;
}
</style>
