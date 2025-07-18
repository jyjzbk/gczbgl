<template>
  <div class="statistics-test">
    <h1>统计API测试页面</h1>
    
    <el-card>
      <template #header>
        <span>API测试</span>
      </template>
      
      <el-space direction="vertical" style="width: 100%">
        <el-button @click="testDashboard" :loading="loading.dashboard">
          测试仪表盘API
        </el-button>
        
        <el-button @click="testExperiment" :loading="loading.experiment">
          测试实验统计API
        </el-button>
        
        <el-button @click="testEquipment" :loading="loading.equipment">
          测试设备统计API
        </el-button>
        
        <el-button @click="testUser" :loading="loading.user">
          测试用户统计API
        </el-button>
        
        <el-button @click="testPerformance" :loading="loading.performance">
          测试绩效统计API
        </el-button>
      </el-space>
    </el-card>
    
    <el-card v-if="results.length > 0" style="margin-top: 20px">
      <template #header>
        <span>测试结果</span>
      </template>
      
      <div v-for="(result, index) in results" :key="index" class="test-result">
        <h4>{{ result.name }}</h4>
        <p><strong>状态:</strong> 
          <el-tag :type="result.success ? 'success' : 'danger'">
            {{ result.success ? '成功' : '失败' }}
          </el-tag>
        </p>
        <p><strong>响应时间:</strong> {{ result.duration }}ms</p>
        <p><strong>HTTP状态:</strong> {{ result.status }}</p>
        <div v-if="result.data">
          <p><strong>响应数据:</strong></p>
          <pre>{{ JSON.stringify(result.data, null, 2) }}</pre>
        </div>
        <div v-if="result.error">
          <p><strong>错误信息:</strong></p>
          <pre>{{ result.error }}</pre>
        </div>
        <hr>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import { statisticsApi } from '@/api/statistics'

const loading = ref({
  dashboard: false,
  experiment: false,
  equipment: false,
  user: false,
  performance: false
})

const results = ref<any[]>([])

const addResult = (name: string, success: boolean, status: number, duration: number, data?: any, error?: any) => {
  results.value.unshift({
    name,
    success,
    status,
    duration,
    data,
    error,
    timestamp: new Date().toLocaleString()
  })
}

const testDashboard = async () => {
  loading.value.dashboard = true
  const startTime = Date.now()
  
  try {
    const response = await statisticsApi.getDashboardStats()
    const duration = Date.now() - startTime
    
    addResult('仪表盘统计', true, 200, duration, response.data)
    ElMessage.success('仪表盘API测试成功')
  } catch (error: any) {
    const duration = Date.now() - startTime
    addResult('仪表盘统计', false, error.response?.status || 0, duration, null, error.message)
    ElMessage.error('仪表盘API测试失败')
  } finally {
    loading.value.dashboard = false
  }
}

const testExperiment = async () => {
  loading.value.experiment = true
  const startTime = Date.now()
  
  try {
    const response = await statisticsApi.getExperimentStats({
      start_date: '2024-01-01',
      end_date: '2024-12-31'
    })
    const duration = Date.now() - startTime
    
    addResult('实验统计', true, 200, duration, response.data)
    ElMessage.success('实验统计API测试成功')
  } catch (error: any) {
    const duration = Date.now() - startTime
    addResult('实验统计', false, error.response?.status || 0, duration, null, error.message)
    ElMessage.error('实验统计API测试失败')
  } finally {
    loading.value.experiment = false
  }
}

const testEquipment = async () => {
  loading.value.equipment = true
  const startTime = Date.now()
  
  try {
    const response = await statisticsApi.getEquipmentStats({
      start_date: '2024-01-01',
      end_date: '2024-12-31'
    })
    const duration = Date.now() - startTime
    
    addResult('设备统计', true, 200, duration, response.data)
    ElMessage.success('设备统计API测试成功')
  } catch (error: any) {
    const duration = Date.now() - startTime
    addResult('设备统计', false, error.response?.status || 0, duration, null, error.message)
    ElMessage.error('设备统计API测试失败')
  } finally {
    loading.value.equipment = false
  }
}

const testUser = async () => {
  loading.value.user = true
  const startTime = Date.now()
  
  try {
    const response = await statisticsApi.getUserActivityStats({
      start_date: '2024-01-01',
      end_date: '2024-12-31'
    })
    const duration = Date.now() - startTime
    
    addResult('用户统计', true, 200, duration, response.data)
    ElMessage.success('用户统计API测试成功')
  } catch (error: any) {
    const duration = Date.now() - startTime
    addResult('用户统计', false, error.response?.status || 0, duration, null, error.message)
    ElMessage.error('用户统计API测试失败')
  } finally {
    loading.value.user = false
  }
}

const testPerformance = async () => {
  loading.value.performance = true
  const startTime = Date.now()
  
  try {
    const response = await statisticsApi.getOrganizationPerformance({
      start_date: '2024-01-01',
      end_date: '2024-12-31'
    })
    const duration = Date.now() - startTime
    
    addResult('绩效统计', true, 200, duration, response.data)
    ElMessage.success('绩效统计API测试成功')
  } catch (error: any) {
    const duration = Date.now() - startTime
    addResult('绩效统计', false, error.response?.status || 0, duration, null, error.message)
    ElMessage.error('绩效统计API测试失败')
  } finally {
    loading.value.performance = false
  }
}
</script>

<style scoped>
.statistics-test {
  padding: 20px;
}

.test-result {
  margin-bottom: 20px;
}

.test-result h4 {
  margin: 0 0 10px 0;
  color: #303133;
}

.test-result p {
  margin: 5px 0;
}

.test-result pre {
  background: #f5f5f5;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  max-height: 300px;
  font-size: 12px;
}

hr {
  margin: 20px 0;
  border: none;
  border-top: 1px solid #ebeef5;
}
</style>
