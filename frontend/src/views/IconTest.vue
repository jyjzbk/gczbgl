<template>
  <div class="icon-test">
    <h2>图标测试页面</h2>
    <div class="icon-grid">
      <div v-for="icon in icons" :key="icon.name" class="icon-item">
        <el-icon :size="24">
          <component :is="icon.component" />
        </el-icon>
        <span>{{ icon.name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  Operation,
  School,
  Box,
  TrendCharts,
  Plus,
  Search,
  Edit,
  Setting,
  User,
  Odometer,
  DataAnalysis,
  Tools,
  Check,
  Lock,
  Message,
  Phone,
  UserFilled,
  Key,
  Camera,
  FullScreen,
  Bell,
  ArrowDown,
  SwitchButton,
  Fold
} from '@element-plus/icons-vue'

const icons = [
  { name: 'Operation', component: Operation },
  { name: 'School', component: School },
  { name: 'Box', component: Box },
  { name: 'TrendCharts', component: TrendCharts },
  { name: 'Plus', component: Plus },
  { name: 'Search', component: Search },
  { name: 'Edit', component: Edit },
  { name: 'Setting', component: Setting },
  { name: 'User', component: User },
  { name: 'Odometer', component: Odometer },
  { name: 'DataAnalysis', component: DataAnalysis },
  { name: 'Tools', component: Tools },
  { name: 'Check', component: Check },
  { name: 'Lock', component: Lock },
  { name: 'Message', component: Message },
  { name: 'Phone', component: Phone },
  { name: 'UserFilled', component: UserFilled },
  { name: 'Key', component: Key },
  { name: 'Camera', component: Camera },
  { name: 'FullScreen', component: FullScreen },
  { name: 'Bell', component: Bell },
  { name: 'ArrowDown', component: ArrowDown },
  { name: 'SwitchButton', component: SwitchButton },
  { name: 'Fold', component: Fold }
]
</script>

<style scoped>
.icon-test {
  padding: 20px;
}

.icon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
  margin-top: 20px;
}

.icon-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  text-align: center;
}

.icon-item span {
  font-size: 12px;
  color: #606266;
}
</style>
