<template>
  <div class="dashboard">
    <div class="page-card">
      <h2>欢迎使用实验教学管理平台</h2>
      <p>这是一个基于Vue 3 + Element Plus开发的现代化管理系统</p>
      
      <!-- 统计卡片 -->
      <el-row :gutter="20" class="stats-cards mt-20">
        <el-col :span="6" v-for="stat in statsData" :key="stat.key">
          <el-card class="stat-card">
            <div class="stat-content">
              <div class="stat-icon" :style="{ color: stat.color }">
                <el-icon :size="40">
                  <component :is="stat.icon" />
                </el-icon>
              </div>
              <div class="stat-info">
                <div class="stat-value">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
      
      <!-- 快捷操作 -->
      <div class="quick-actions mt-20">
        <h3>快捷操作</h3>
        <el-row :gutter="16" class="mt-16">
          <el-col :span="6" v-for="action in quickActions" :key="action.key">
            <el-card class="action-card" @click="handleQuickAction(action.path)">
              <div class="action-content">
                <el-icon :size="24" :color="action.color">
                  <component :is="action.icon" />
                </el-icon>
                <span class="action-label">{{ action.label }}</span>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>
      
      <!-- 系统信息 -->
      <div class="system-info mt-20">
        <h3>系统信息</h3>
        <el-descriptions :column="2" border class="mt-16">
          <el-descriptions-item label="系统版本">v1.0.0</el-descriptions-item>
          <el-descriptions-item label="Vue版本">{{ vueVersion }}</el-descriptions-item>
          <el-descriptions-item label="Element Plus版本">{{ elementVersion }}</el-descriptions-item>
          <el-descriptions-item label="当前用户">{{ authStore.userName }}</el-descriptions-item>
          <el-descriptions-item label="用户角色">{{ authStore.userRole }}</el-descriptions-item>
          <el-descriptions-item label="登录时间">{{ loginTime }}</el-descriptions-item>
        </el-descriptions>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { version as vueVersion } from 'vue'
import {
  School,
  Operation,
  Box,
  TrendCharts,
  Plus,
  Search,
  Edit,
  Setting
} from '@element-plus/icons-vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// 统计数据
const statsData = ref([
  { 
    key: 'schools', 
    label: '学校总数', 
    value: '5', 
    icon: School, 
    color: '#409EFF' 
  },
  {
    key: 'experiments',
    label: '实验总数',
    value: '128',
    icon: Operation,
    color: '#67C23A'
  },
  { 
    key: 'equipments', 
    label: '设备总数', 
    value: '151', 
    icon: Box, 
    color: '#E6A23C' 
  },
  { 
    key: 'completion_rate', 
    label: '开出率', 
    value: '85%', 
    icon: TrendCharts, 
    color: '#F56C6C' 
  }
])

// 快捷操作
const quickActions = ref([
  {
    key: 'add-experiment',
    label: '新增实验',
    icon: Plus,
    color: '#67C23A',
    path: '/experiment-catalogs'
  },
  {
    key: 'search-equipment',
    label: '查找设备',
    icon: Search,
    color: '#409EFF',
    path: '/equipments'
  },
  {
    key: 'booking-experiment',
    label: '实验预约',
    icon: Edit,
    color: '#E6A23C',
    path: '/experiment-bookings'
  },
  {
    key: 'system-settings',
    label: '系统设置',
    icon: Setting,
    color: '#909399',
    path: '/settings'
  }
])

// 版本信息
const elementVersion = computed(() => {
  // 这里可以从package.json获取实际版本
  return '2.10.4'
})

const loginTime = computed(() => {
  return new Date().toLocaleString()
})

// 处理快捷操作
const handleQuickAction = (path: string) => {
  router.push(path)
}

onMounted(() => {
  // 这里可以加载实际的统计数据
  console.log('仪表盘已加载')
})
</script>

<style scoped>
.dashboard {
  padding: 0;
}

.stats-cards {
  margin-top: 20px;
}

.stat-card {
  cursor: pointer;
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  flex-shrink: 0;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  line-height: 1;
}

.stat-label {
  font-size: 14px;
  color: #909399;
  margin-top: 4px;
}

.quick-actions h3,
.system-info h3 {
  color: #303133;
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 0;
}

.action-card {
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px 0;
}

.action-label {
  font-size: 14px;
  color: #606266;
  font-weight: 500;
}

@media (max-width: 768px) {
  .stats-cards .el-col,
  .quick-actions .el-col {
    margin-bottom: 16px;
  }
  
  .stat-content {
    gap: 12px;
  }
  
  .stat-value {
    font-size: 20px;
  }
  
  .action-content {
    padding: 12px 0;
  }
}
</style>
