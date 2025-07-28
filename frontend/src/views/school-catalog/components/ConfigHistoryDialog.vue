<template>
  <el-dialog
    v-model="visible"
    title="配置历史"
    width="800px"
  >
    <el-table :data="historyData" v-loading="loading" stripe>
      <el-table-column prop="config_type_name" label="配置类型" width="120">
        <template #default="{ row }">
          <el-tag :type="row.config_type === 'selection' ? 'success' : 'info'">
            {{ row.config_type_name }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="source_org_name" label="目录来源" min-width="200">
        <template #default="{ row }">
          {{ row.source_org_name }} ({{ row.source_level_name }})
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" width="100">
        <template #default="{ row }">
          <el-tag :type="row.status === 1 ? 'success' : 'info'">
            {{ row.status === 1 ? '启用' : '禁用' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="configured_at" label="配置时间" width="180">
        <template #default="{ row }">
          {{ formatDateTime(row.configured_at) }}
        </template>
      </el-table-column>
      <el-table-column prop="configuredBy.name" label="配置人员" width="120" />
      <el-table-column label="操作" width="100">
        <template #default="{ row }">
          <el-button size="small" @click="viewDetail(row)">
            详情
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="showDetailDialog"
      title="配置详情"
      width="600px"
      append-to-body
    >
      <el-descriptions v-if="currentDetail" :column="2" border>
        <el-descriptions-item label="配置类型">
          <el-tag :type="currentDetail.config_type === 'selection' ? 'success' : 'info'">
            {{ currentDetail.config_type_name }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag :type="currentDetail.status === 1 ? 'success' : 'info'">
            {{ currentDetail.status === 1 ? '启用' : '禁用' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="目录来源">
          {{ currentDetail.source_org_name }}
        </el-descriptions-item>
        <el-descriptions-item label="目录级别">
          {{ currentDetail.source_level_name }}
        </el-descriptions-item>
        <el-descriptions-item label="修改权限">
          <el-tag :type="currentDetail.can_modify_selection ? 'success' : 'danger'">
            {{ currentDetail.can_modify_selection ? '允许' : '不允许' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="删除权限">
          <el-tag :type="currentDetail.can_delete_experiments ? 'success' : 'danger'">
            {{ currentDetail.can_delete_experiments ? '允许' : '不允许' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="配置时间">
          {{ formatDateTime(currentDetail.configured_at) }}
        </el-descriptions-item>
        <el-descriptions-item label="配置人员">
          {{ currentDetail.configuredBy?.name || '未知' }}
        </el-descriptions-item>
        <el-descriptions-item label="生效日期">
          {{ currentDetail.effective_date || '立即生效' }}
        </el-descriptions-item>
        <el-descriptions-item label="失效日期">
          {{ currentDetail.expiry_date || '永久有效' }}
        </el-descriptions-item>
        <el-descriptions-item label="配置理由" :span="2">
          {{ currentDetail.config_reason || '无' }}
        </el-descriptions-item>
      </el-descriptions>
    </el-dialog>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="visible = false">关闭</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { schoolCatalogConfigApi, type SchoolExperimentCatalogConfig } from '@/api/schoolCatalogConfig'

interface Props {
  modelValue: boolean
  schoolId?: number
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const loading = ref(false)
const historyData = ref<SchoolExperimentCatalogConfig[]>([])
const showDetailDialog = ref(false)
const currentDetail = ref<SchoolExperimentCatalogConfig>()

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 方法
const loadHistory = async () => {
  if (!props.schoolId) return
  
  try {
    loading.value = true
    const response = await schoolCatalogConfigApi.getConfigHistory(props.schoolId)
    
    if (response.data.success) {
      historyData.value = response.data.data
    } else {
      ElMessage.error(response.data.message || '获取配置历史失败')
    }
  } catch (error) {
    console.error('获取配置历史失败:', error)
    ElMessage.error('获取配置历史失败')
  } finally {
    loading.value = false
  }
}

const viewDetail = (config: SchoolExperimentCatalogConfig) => {
  currentDetail.value = config
  showDetailDialog.value = true
}

const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}

// 监听器
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    loadHistory()
  }
})
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
