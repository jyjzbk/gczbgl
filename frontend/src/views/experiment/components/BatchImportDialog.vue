<template>
  <el-dialog
    v-model="visible"
    title="批量导入实验目录"
    width="600px"
    :before-close="handleClose"
  >
    <div class="import-content">
      <!-- 步骤指示器 -->
      <el-steps :active="currentStep" align-center class="steps">
        <el-step title="下载模板" />
        <el-step title="上传文件" />
        <el-step title="导入结果" />
      </el-steps>
      
      <!-- 第一步：下载模板 -->
      <div v-if="currentStep === 0" class="step-content">
        <div class="template-info">
          <el-alert
            title="导入说明"
            type="info"
            :closable="false"
            show-icon
          >
            <template #default>
              <p>1. 请先下载导入模板，按照模板格式填写数据</p>
              <p>2. 支持Excel格式文件（.xlsx, .xls）</p>
              <p>3. 实验编号不能重复</p>
              <p>4. 必填字段：学科ID、实验名称、实验编号、类型、年级、学期</p>
            </template>
          </el-alert>
        </div>
        
        <div class="template-download">
          <el-button type="primary" @click="downloadTemplate">
            <el-icon><Download /></el-icon>
            下载导入模板
          </el-button>
        </div>
      </div>
      
      <!-- 第二步：上传文件 -->
      <div v-if="currentStep === 1" class="step-content">
        <el-upload
          ref="uploadRef"
          :auto-upload="false"
          :show-file-list="true"
          :on-change="handleFileChange"
          :before-remove="handleFileRemove"
          accept=".xlsx,.xls"
          drag
        >
          <div class="upload-area">
            <el-icon class="upload-icon"><UploadFilled /></el-icon>
            <div class="upload-text">
              <p>点击或拖拽Excel文件到此处</p>
              <p class="upload-tip">支持 .xlsx、.xls 格式</p>
            </div>
          </div>
        </el-upload>
        
        <!-- 文件预览 -->
        <div v-if="previewData.length > 0" class="file-preview">
          <h4>数据预览（前5条）</h4>
          <el-table :data="previewData.slice(0, 5)" border size="small">
            <el-table-column prop="subject_name" label="学科" width="80" />
            <el-table-column prop="name" label="实验名称" min-width="150" />
            <el-table-column prop="code" label="编号" width="100" />
            <el-table-column prop="type_name" label="类型" width="80" />
            <el-table-column prop="grade" label="年级" width="60" />
            <el-table-column prop="semester_name" label="学期" width="80" />
          </el-table>
          <p class="preview-info">
            共解析到 {{ previewData.length }} 条数据
          </p>
        </div>
      </div>
      
      <!-- 第三步：导入结果 -->
      <div v-if="currentStep === 2" class="step-content">
        <div class="import-result">
          <el-result
            :icon="importResult.success ? 'success' : 'warning'"
            :title="importResult.title"
            :sub-title="importResult.subtitle"
          >
            <template #extra>
              <div class="result-stats">
                <el-descriptions :column="2" border>
                  <el-descriptions-item label="总数据量">
                    {{ importResult.total }}
                  </el-descriptions-item>
                  <el-descriptions-item label="成功导入">
                    <span style="color: #67c23a">{{ importResult.imported }}</span>
                  </el-descriptions-item>
                  <el-descriptions-item label="失败数量">
                    <span style="color: #f56c6c">{{ importResult.errors.length }}</span>
                  </el-descriptions-item>
                  <el-descriptions-item label="导入时间">
                    {{ importResult.time }}
                  </el-descriptions-item>
                </el-descriptions>
              </div>
              
              <!-- 错误信息 -->
              <div v-if="importResult.errors.length > 0" class="error-list">
                <h4>错误详情</h4>
                <el-scrollbar height="200px">
                  <div
                    v-for="(error, index) in importResult.errors"
                    :key="index"
                    class="error-item"
                  >
                    <el-tag type="danger" size="small">{{ error }}</el-tag>
                  </div>
                </el-scrollbar>
              </div>
            </template>
          </el-result>
        </div>
      </div>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">
          {{ currentStep === 2 ? '关闭' : '取消' }}
        </el-button>
        
        <el-button
          v-if="currentStep === 0"
          type="primary"
          @click="nextStep"
        >
          下一步
        </el-button>
        
        <el-button
          v-if="currentStep === 1"
          @click="prevStep"
        >
          上一步
        </el-button>
        
        <el-button
          v-if="currentStep === 1"
          type="primary"
          :loading="importing"
          :disabled="previewData.length === 0"
          @click="handleImport"
        >
          {{ importing ? '导入中...' : '开始导入' }}
        </el-button>
        
        <el-button
          v-if="currentStep === 2"
          type="primary"
          @click="handleClose"
        >
          完成
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { ElMessage, type UploadFile } from 'element-plus'
import { Download, UploadFilled } from '@element-plus/icons-vue'
import { batchImportCatalogsApi } from '@/api/experiment'
import * as XLSX from 'xlsx'

interface Props {
  modelValue: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 上传组件引用
const uploadRef = ref()

// 对话框显示状态
const visible = ref(false)

// 当前步骤
const currentStep = ref(0)

// 导入状态
const importing = ref(false)

// 预览数据
const previewData = ref<any[]>([])

// 导入结果
const importResult = reactive({
  success: false,
  title: '',
  subtitle: '',
  total: 0,
  imported: 0,
  errors: [] as string[],
  time: ''
})

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
    if (newVal) {
      resetDialog()
    }
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 重置对话框
const resetDialog = () => {
  currentStep.value = 0
  previewData.value = []
  importing.value = false
  Object.assign(importResult, {
    success: false,
    title: '',
    subtitle: '',
    total: 0,
    imported: 0,
    errors: [],
    time: ''
  })
}

// 下载模板
const downloadTemplate = () => {
  // 创建模板数据
  const templateData = [
    {
      '学科ID': 1,
      '学科名称': '物理',
      '实验名称': '测量物体的长度',
      '实验编号': 'WL_001',
      '实验类型': 2,
      '类型说明': '1-演示实验 2-分组实验 3-探究实验 4-综合实验',
      '年级': 8,
      '学期': 1,
      '学期说明': '1-上学期 2-下学期',
      '章节': '第一章 机械运动',
      '时长(分钟)': 45,
      '学生人数': 2,
      '难度等级': 1,
      '难度说明': '1-5星，数字越大越难',
      '标准实验': 1,
      '标准说明': '1-是 0-否',
      '状态': 1,
      '状态说明': '1-启用 0-禁用',
      '实验目标': '学会使用刻度尺测量物体长度',
      '实验器材': '刻度尺、铅笔、硬币等',
      '实验步骤': '1.观察刻度尺\n2.学习测量方法\n3.测量物体长度',
      '安全注意': '使用刻度尺时要轻拿轻放'
    }
  ]
  
  // 创建工作簿
  const ws = XLSX.utils.json_to_sheet(templateData)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, '实验目录模板')
  
  // 下载文件
  XLSX.writeFile(wb, '实验目录导入模板.xlsx')
  ElMessage.success('模板下载成功')
}

// 处理文件变化
const handleFileChange = (file: UploadFile) => {
  if (!file.raw) return
  
  const reader = new FileReader()
  reader.onload = (e) => {
    try {
      const data = new Uint8Array(e.target?.result as ArrayBuffer)
      const workbook = XLSX.read(data, { type: 'array' })
      const sheetName = workbook.SheetNames[0]
      const worksheet = workbook.Sheets[sheetName]
      const jsonData = XLSX.utils.sheet_to_json(worksheet)
      
      // 处理数据
      previewData.value = jsonData.map((row: any) => ({
        subject_id: row['学科ID'],
        subject_name: row['学科名称'],
        name: row['实验名称'],
        code: row['实验编号'],
        type: row['实验类型'],
        type_name: getTypeName(row['实验类型']),
        grade: row['年级'],
        semester: row['学期'],
        semester_name: row['学期'] === 1 ? '上学期' : '下学期',
        chapter: row['章节'],
        duration: row['时长(分钟)'],
        student_count: row['学生人数'],
        difficulty_level: row['难度等级'],
        is_standard: row['标准实验'],
        status: row['状态'],
        objective: row['实验目标'],
        materials: row['实验器材'],
        procedure: row['实验步骤'],
        safety_notes: row['安全注意']
      }))
      
      ElMessage.success(`成功解析 ${previewData.value.length} 条数据`)
    } catch (error) {
      ElMessage.error('文件解析失败，请检查文件格式')
      console.error('文件解析错误:', error)
    }
  }
  reader.readAsArrayBuffer(file.raw)
}

// 处理文件移除
const handleFileRemove = () => {
  previewData.value = []
  return true
}

// 获取类型名称
const getTypeName = (type: number) => {
  const typeMap: Record<number, string> = {
    1: '演示实验',
    2: '分组实验',
    3: '探究实验',
    4: '综合实验'
  }
  return typeMap[type] || '未知'
}

// 下一步
const nextStep = () => {
  currentStep.value++
}

// 上一步
const prevStep = () => {
  currentStep.value--
}

// 处理导入
const handleImport = async () => {
  if (previewData.value.length === 0) {
    ElMessage.warning('请先上传文件')
    return
  }
  
  importing.value = true
  const startTime = Date.now()
  
  try {
    const response = await batchImportCatalogsApi({
      catalogs: previewData.value
    })
    
    const endTime = Date.now()
    const duration = Math.round((endTime - startTime) / 1000)
    
    // 设置导入结果
    Object.assign(importResult, {
      success: response.data.imported > 0,
      title: response.data.imported > 0 ? '导入完成' : '导入失败',
      subtitle: response.message,
      total: previewData.value.length,
      imported: response.data.imported,
      errors: response.data.errors || [],
      time: `${duration}秒`
    })
    
    currentStep.value = 2
    
    if (response.data.imported > 0) {
      emit('success')
    }
  } catch (error) {
    console.error('批量导入失败:', error)
    ElMessage.error('导入失败')
  } finally {
    importing.value = false
  }
}

// 处理关闭
const handleClose = () => {
  visible.value = false
  if (currentStep.value === 2 && importResult.imported > 0) {
    emit('success')
  }
}
</script>

<style scoped>
.import-content {
  padding: 20px 0;
}

.steps {
  margin-bottom: 30px;
}

.step-content {
  min-height: 300px;
  padding: 20px 0;
}

.template-info {
  margin-bottom: 20px;
}

.template-download {
  text-align: center;
  padding: 40px 0;
}

.upload-area {
  padding: 40px 20px;
  text-align: center;
}

.upload-icon {
  font-size: 40px;
  color: #c0c4cc;
  margin-bottom: 16px;
}

.upload-text p {
  margin: 0;
  color: #606266;
}

.upload-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 8px;
}

.file-preview {
  margin-top: 20px;
}

.file-preview h4 {
  margin: 0 0 12px;
  font-size: 14px;
  color: #303133;
}

.preview-info {
  margin: 12px 0 0;
  font-size: 12px;
  color: #909399;
}

.import-result {
  text-align: center;
}

.result-stats {
  margin: 20px 0;
}

.error-list {
  margin-top: 20px;
  text-align: left;
}

.error-list h4 {
  margin: 0 0 12px;
  font-size: 14px;
  color: #303133;
}

.error-item {
  margin-bottom: 8px;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

:deep(.el-alert__content) {
  line-height: 1.6;
}

:deep(.el-upload-dragger) {
  border: 2px dashed #d9d9d9;
  border-radius: 6px;
  width: 100%;
  height: auto;
  text-align: center;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.3s;
}

:deep(.el-upload-dragger:hover) {
  border-color: #409eff;
}
</style>
