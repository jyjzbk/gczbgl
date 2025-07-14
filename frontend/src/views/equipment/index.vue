<template>
  <div class="equipment-index">
    <div class="page-card">
      <div class="page-header">
        <h2>设备管理系统</h2>
        <p>欢迎使用设备管理系统，请选择相应的功能模块</p>
      </div>
      
      <el-row :gutter="20" class="module-grid">
        <el-col :span="6" v-for="module in modules" :key="module.name">
          <el-card 
            class="module-card" 
            :body-style="{ padding: '20px' }"
            @click="navigateTo(module.path)"
          >
            <div class="module-content">
              <div class="module-icon">
                <el-icon :size="48" :color="module.color">
                  <component :is="module.icon" />
                </el-icon>
              </div>
              <div class="module-info">
                <h3>{{ module.name }}</h3>
                <p>{{ module.description }}</p>
              </div>
              <div class="module-stats">
                <span class="stat-number">{{ module.count }}</span>
                <span class="stat-label">{{ module.unit }}</span>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
      
      <!-- 快速统计 -->
      <el-row :gutter="20" class="stats-section">
        <el-col :span="24">
          <el-card title="设备概览">
            <el-row :gutter="20">
              <el-col :span="6" v-for="stat in stats" :key="stat.label">
                <div class="stat-item">
                  <div class="stat-value" :style="{ color: stat.color }">
                    {{ stat.value }}
                  </div>
                  <div class="stat-label">{{ stat.label }}</div>
                </div>
              </el-col>
            </el-row>
          </el-card>
        </el-col>
      </el-row>
      
      <!-- 最近活动 -->
      <el-row :gutter="20" class="activity-section">
        <el-col :span="12">
          <el-card title="最近借用">
            <el-timeline>
              <el-timeline-item
                v-for="activity in recentBorrows"
                :key="activity.id"
                :timestamp="activity.time"
                type="primary"
              >
                {{ activity.description }}
              </el-timeline-item>
            </el-timeline>
          </el-card>
        </el-col>
        
        <el-col :span="12">
          <el-card title="最近维修">
            <el-timeline>
              <el-timeline-item
                v-for="activity in recentMaintenances"
                :key="activity.id"
                :timestamp="activity.time"
                :type="activity.type"
              >
                {{ activity.description }}
              </el-timeline-item>
            </el-timeline>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { 
  Box, 
  Tools, 
  DocumentCopy, 
  QrCode 
} from '@element-plus/icons-vue'

const router = useRouter()

// 模块数据
const modules = ref([
  {
    name: '设备档案',
    description: '管理设备基本信息和档案',
    icon: Box,
    color: '#409EFF',
    path: '/equipment-management',
    count: 156,
    unit: '台设备'
  },
  {
    name: '设备借用',
    description: '处理设备借用申请和归还',
    icon: DocumentCopy,
    color: '#67C23A',
    path: '/equipment-borrow',
    count: 23,
    unit: '借用中'
  },
  {
    name: '设备维修',
    description: '管理设备维修和保养记录',
    icon: Tools,
    color: '#E6A23C',
    path: '/equipment-maintenance',
    count: 8,
    unit: '维修中'
  },
  {
    name: '二维码管理',
    description: '生成和管理设备二维码',
    icon: QrCode,
    color: '#F56C6C',
    path: '/equipment-qrcode',
    count: 142,
    unit: '已生成'
  }
])

// 统计数据
const stats = ref([
  { label: '设备总数', value: 156, color: '#409EFF' },
  { label: '正常设备', value: 142, color: '#67C23A' },
  { label: '借出设备', value: 8, color: '#E6A23C' },
  { label: '维修设备', value: 6, color: '#F56C6C' }
])

// 最近借用活动
const recentBorrows = ref([
  {
    id: 1,
    description: '张老师借用了显微镜-001',
    time: '2024-01-15 14:30'
  },
  {
    id: 2,
    description: '李老师归还了离心机-002',
    time: '2024-01-15 10:20'
  },
  {
    id: 3,
    description: '王老师申请借用天平-003',
    time: '2024-01-14 16:45'
  }
])

// 最近维修活动
const recentMaintenances = ref([
  {
    id: 1,
    description: '显微镜-005 维修完成',
    time: '2024-01-15 15:00',
    type: 'success'
  },
  {
    id: 2,
    description: '离心机-008 开始维修',
    time: '2024-01-15 09:30',
    type: 'warning'
  },
  {
    id: 3,
    description: '天平-012 报修申请',
    time: '2024-01-14 14:20',
    type: 'danger'
  }
])

// 导航到指定页面
const navigateTo = (path: string) => {
  router.push(path)
}
</script>

<style scoped>
.equipment-index {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 32px;
  text-align: center;
}

.page-header h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 16px;
}

.module-grid {
  margin-bottom: 32px;
}

.module-card {
  cursor: pointer;
  transition: all 0.3s;
  height: 180px;
}

.module-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.module-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  height: 100%;
}

.module-icon {
  margin-bottom: 16px;
}

.module-info {
  flex: 1;
  margin-bottom: 16px;
}

.module-info h3 {
  margin: 0 0 8px 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.module-info p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.module-stats {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat-number {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
}

.stat-label {
  font-size: 12px;
  color: #9ca3af;
}

.stats-section,
.activity-section {
  margin-bottom: 24px;
}

.stat-item {
  text-align: center;
  padding: 16px;
}

.stat-value {
  font-size: 32px;
  font-weight: 600;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .module-grid .el-col {
    margin-bottom: 20px;
  }
}

@media (max-width: 768px) {
  .equipment-index {
    padding: 10px;
  }
  
  .page-card {
    padding: 16px;
  }
  
  .module-grid .el-col {
    span: 12;
  }
  
  .activity-section .el-col {
    span: 24;
    margin-bottom: 20px;
  }
}
</style>
