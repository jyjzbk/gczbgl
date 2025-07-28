<template>
  <el-dialog
    v-model="visible"
    title="配置详情"
    width="600px"
  >
    <el-descriptions v-if="config" :column="2" border>
      <el-descriptions-item label="配置类型">
        <el-tag :type="config.config_type === 'selection' ? 'success' : 'info'">
          {{ config.config_type_name }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="状态">
        <el-tag :type="config.status === 1 ? 'success' : 'info'">
          {{ config.status === 1 ? '启用' : '禁用' }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="目录来源">
        {{ config.source_org_name }}
      </el-descriptions-item>
      <el-descriptions-item label="目录级别">
        {{ config.source_level_name }}
      </el-descriptions-item>
      <el-descriptions-item label="修改权限">
        <el-tag :type="config.can_modify_selection ? 'success' : 'danger'">
          {{ config.can_modify_selection ? '允许' : '不允许' }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="删除权限">
        <el-tag :type="config.can_delete_experiments ? 'success' : 'danger'">
          {{ config.can_delete_experiments ? '允许' : '不允许' }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="配置时间">
        {{ formatDateTime(config.configured_at) }}
      </el-descriptions-item>
      <el-descriptions-item label="配置人员">
        {{ config.configuredBy?.name || '未知' }}
      </el-descriptions-item>
      <el-descriptions-item label="生效日期">
        {{ config.effective_date || '立即生效' }}
      </el-descriptions-item>
      <el-descriptions-item label="失效日期">
        {{ config.expiry_date || '永久有效' }}
      </el-descriptions-item>
      <el-descriptions-item label="配置理由" :span="2">
        {{ config.config_reason || '无' }}
      </el-descriptions-item>
    </el-descriptions>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="visible = false">关闭</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { SchoolExperimentCatalogConfig } from '@/api/schoolCatalogConfig'

interface Props {
  modelValue: boolean
  config?: SchoolExperimentCatalogConfig
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 方法
const formatDateTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleString('zh-CN')
}
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
</style>
