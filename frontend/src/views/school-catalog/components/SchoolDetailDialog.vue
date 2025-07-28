<template>
  <el-dialog
    v-model="visible"
    :title="`${schoolName} - 完成率详情`"
    width="900px"
  >
    <div v-loading="loading">
      <!-- 总体统计 -->
      <el-card class="overview-card" shadow="never" v-if="statisticsData">
        <template #header>
          <span>📊 总体完成率</span>
        </template>
        <el-row :gutter="20">
          <el-col :span="8">
            <div class="stat-item">
              <div class="stat-value">{{ statisticsData.overall.total_experiments }}</div>
              <div class="stat-label">总实验数</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-item">
              <div class="stat-value">{{ statisticsData.overall.completed_experiments }}</div>
              <div class="stat-label">已完成数</div>
            </div>
          </el-col>
          <el-col :span="8">
            <div class="stat-item">
              <div class="stat-value">{{ statisticsData.overall.completion_rate }}%</div>
              <div class="stat-label">完成率</div>
            </div>
          </el-col>
        </el-row>
      </el-card>

      <!-- 分维度统计 -->
      <el-row :gutter="20" v-if="statisticsData">
        <el-col :span="8">
          <el-card class="dimension-card" shadow="never">
            <template #header>
              <span>按学科统计</span>
            </template>
            <div v-for="(data, key) in statisticsData.by_subject" :key="key" class="dimension-item">
              <div class="dimension-name">{{ data.dimension_name }}</div>
              <div class="dimension-stats">
                <span>{{ data.completed }}/{{ data.total }}</span>
                <el-tag :type="getCompletionRateType(data.rate)" size="small">
                  {{ data.rate }}%
                </el-tag>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="dimension-card" shadow="never">
            <template #header>
              <span>按年级统计</span>
            </template>
            <div v-for="(data, key) in statisticsData.by_grade" :key="key" class="dimension-item">
              <div class="dimension-name">{{ data.dimension_name }}</div>
              <div class="dimension-stats">
                <span>{{ data.completed }}/{{ data.total }}</span>
                <el-tag :type="getCompletionRateType(data.rate)" size="small">
                  {{ data.rate }}%
                </el-tag>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card class="dimension-card" shadow="never">
            <template #header>
              <span>按学期统计</span>
            </template>
            <div v-for="(data, key) in statisticsData.by_semester" :key="key" class="dimension-item">
              <div class="dimension-name">{{ data.dimension_name }}</div>
              <div class="dimension-stats">
                <span>{{ data.completed }}/{{ data.total }}</span>
                <el-tag :type="getCompletionRateType(data.rate)" size="small">
                  {{ data.rate }}%
                </el-tag>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- 实验详情列表 -->
      <el-card class="detail-card" shadow="never" v-if="statisticsData">
        <template #header>
          <div class="card-header">
            <span>📋 实验详情列表</span>
            <div class="header-actions">
              <el-input
                v-model="searchText"
                placeholder="搜索实验名称"
                size="small"
                style="width: 200px;"
                clearable
              />
            </div>
          </div>
        </template>
        
        <el-table :data="filteredExperiments" stripe max-height="400">
          <el-table-column prop="name" label="实验名称" min-width="200" />
          <el-table-column prop="code" label="实验编号" width="120" />
          <el-table-column prop="grade" label="年级" width="80">
            <template #default="{ row }">
              {{ row.grade }}年级
            </template>
          </el-table-column>
          <el-table-column prop="semester" label="学期" width="80">
            <template #default="{ row }">
              {{ row.semester === 1 ? '上学期' : '下学期' }}
            </template>
          </el-table-column>
          <el-table-column prop="experiment_type" label="类型" width="100" />
          <el-table-column prop="is_completed" label="完成状态" width="120">
            <template #default="{ row }">
              <el-tag :type="row.is_completed ? 'success' : 'danger'">
                {{ row.is_completed ? '✅ 已完成' : '❌ 未完成' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="difficulty_level" label="难度" width="80">
            <template #default="{ row }">
              <el-rate 
                v-model="row.difficulty_level" 
                disabled 
                show-score 
                text-color="#ff9900"
                score-template="{value}"
                size="small"
              />
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="visible = false">关闭</el-button>
        <el-button type="primary" @click="exportDetail">导出详情</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { completionStatisticsApi, type CompletionData, type ExperimentDetail } from '@/api/completionStatistics'

interface Props {
  modelValue: boolean
  schoolId?: number
  schoolName?: string
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// 响应式数据
const loading = ref(false)
const statisticsData = ref<CompletionData>()
const searchText = ref('')

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const filteredExperiments = computed(() => {
  if (!statisticsData.value || !searchText.value) {
    return statisticsData.value?.detailed_list || []
  }
  
  return statisticsData.value.detailed_list.filter(experiment =>
    experiment.name.toLowerCase().includes(searchText.value.toLowerCase()) ||
    experiment.code.toLowerCase().includes(searchText.value.toLowerCase())
  )
})

// 方法
const loadStatistics = async () => {
  if (!props.schoolId) return
  
  try {
    loading.value = true
    const response = await completionStatisticsApi.getSchoolStatistics(props.schoolId)
    
    if (response.data.success) {
      statisticsData.value = response.data.data.completion_data
    } else {
      ElMessage.error(response.data.message || '获取学校统计失败')
    }
  } catch (error) {
    console.error('获取学校统计失败:', error)
    ElMessage.error('获取学校统计失败')
  } finally {
    loading.value = false
  }
}

const getCompletionRateType = (rate: number) => {
  if (rate >= 90) return 'success'
  if (rate >= 80) return 'primary'
  if (rate >= 70) return 'warning'
  return 'danger'
}

const exportDetail = () => {
  // 导出功能实现
  ElMessage.info('导出功能开发中...')
}

// 监听器
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.schoolId) {
    loadStatistics()
  }
})
</script>

<style scoped>
.overview-card,
.dimension-card,
.detail-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.stat-item {
  text-align: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
  color: #409eff;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.dimension-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.dimension-item:last-child {
  border-bottom: none;
}

.dimension-name {
  font-weight: 500;
}

.dimension-stats {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dialog-footer {
  text-align: right;
}
</style>
