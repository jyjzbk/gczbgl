<template>
  <el-dialog
    v-model="visible"
    title="查看模板"
    width="600px"
    :before-close="handleClose"
  >
    <div v-if="template" class="template-detail">
      <!-- 基本信息 -->
      <div class="info-section">
        <h4>基本信息</h4>
        <div class="info-grid">
          <div class="info-item">
            <span class="label">模板名称：</span>
            <span class="value">{{ template.name }}</span>
          </div>
          <div class="info-item">
            <span class="label">创建级别：</span>
            <span class="value">{{ template.creator_level_name }}</span>
          </div>
          <div class="info-item">
            <span class="label">创建人：</span>
            <span class="value">{{ template.creator_user?.name }}</span>
          </div>
          <div class="info-item">
            <span class="label">使用次数：</span>
            <span class="value">{{ template.usage_count }}</span>
          </div>
          <div class="info-item">
            <span class="label">状态：</span>
            <el-tag :type="template.status === 1 ? 'success' : 'danger'">
              {{ template.status_name }}
            </el-tag>
          </div>
          <div class="info-item">
            <span class="label">默认模板：</span>
            <el-tag v-if="template.is_default === 1" type="warning" size="small">
              是
            </el-tag>
            <span v-else class="value">否</span>
          </div>
          <div class="info-item full-width">
            <span class="label">描述：</span>
            <span class="value">{{ template.description || '无' }}</span>
          </div>
        </div>
      </div>

      <!-- 适用年级 -->
      <div class="info-section">
        <h4>适用年级</h4>
        <div class="grade-tags">
          <el-tag
            v-for="grade in template.applicable_grades"
            :key="grade"
            style="margin-right: 8px; margin-bottom: 8px"
          >
            {{ grade }}年级
          </el-tag>
        </div>
      </div>

      <!-- 学科配置 -->
      <div class="info-section">
        <h4>学科配置</h4>
        <div class="config-list">
          <div
            v-for="(versionId, subjectId) in template.assignment_config"
            :key="subjectId"
            class="config-item"
          >
            <div class="config-row">
              <div class="subject-info">
                <el-icon class="subject-icon"><Reading /></el-icon>
                <span class="subject-name">{{ getSubjectName(Number(subjectId)) }}</span>
              </div>
              <div class="arrow">
                <el-icon><Right /></el-icon>
              </div>
              <div class="version-info">
                <el-icon class="version-icon"><Document /></el-icon>
                <span class="version-name">{{ getVersionName(versionId) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 时间信息 -->
      <div class="info-section">
        <h4>时间信息</h4>
        <div class="info-grid">
          <div class="info-item">
            <span class="label">创建时间：</span>
            <span class="value">{{ formatDate(template.created_at) }}</span>
          </div>
          <div class="info-item">
            <span class="label">更新时间：</span>
            <span class="value">{{ formatDate(template.updated_at) }}</span>
          </div>
          <div class="info-item">
            <span class="label">最后使用：</span>
            <span class="value">
              {{ template.last_used_at ? formatDate(template.last_used_at) : '未使用' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Reading, Document, Right } from '@element-plus/icons-vue'
import type { TextbookAssignmentTemplate } from '@/api/textbookAssignment'
import type { Subject, TextbookVersion } from '@/api/experiment'
import { formatDate } from '@/utils/date'

interface Props {
  modelValue: boolean
  template: TextbookAssignmentTemplate | null
  subjects: Subject[]
  textbookVersions: TextbookVersion[]
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const visible = ref(false)

// 监听props变化
watch(
  () => props.modelValue,
  (newVal) => {
    visible.value = newVal
  },
  { immediate: true }
)

// 监听visible变化
watch(visible, (newVal) => {
  emit('update:modelValue', newVal)
})

// 方法
const getSubjectName = (subjectId: number) => {
  const subject = props.subjects.find(s => s.id === subjectId)
  return subject?.name || '未知学科'
}

const getVersionName = (versionId: number) => {
  const version = props.textbookVersions.find(v => v.id === versionId)
  return version ? `${version.name}${version.publisher ? ` (${version.publisher})` : ''}` : '未知版本'
}

const handleClose = () => {
  visible.value = false
}
</script>

<style scoped>
.template-detail {
  padding: 10px 0;
}

.info-section {
  margin-bottom: 24px;
}

.info-section:last-child {
  margin-bottom: 0;
}

.info-section h4 {
  margin: 0 0 12px 0;
  color: #303133;
  font-size: 16px;
  font-weight: 600;
  border-bottom: 1px solid #e4e7ed;
  padding-bottom: 8px;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.info-item {
  display: flex;
  align-items: center;
  line-height: 1.5;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.label {
  font-weight: 500;
  color: #606266;
  min-width: 80px;
  flex-shrink: 0;
}

.value {
  color: #303133;
  flex: 1;
}

.grade-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.config-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.config-item {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 16px;
}

.config-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.subject-info,
.version-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.subject-icon {
  color: #409eff;
  font-size: 16px;
}

.version-icon {
  color: #67c23a;
  font-size: 16px;
}

.subject-name,
.version-name {
  font-weight: 500;
  color: #303133;
}

.arrow {
  margin: 0 16px;
  color: #909399;
}

.dialog-footer {
  text-align: right;
}
</style>
