<template>
  <div class="debug-permissions" v-if="showDebug">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>权限调试信息</span>
          <el-button type="text" @click="showDebug = false">关闭</el-button>
        </div>
      </template>
      
      <div class="debug-content">
        <h4>用户信息:</h4>
        <p>用户名: {{ authStore.userInfo?.username }}</p>
        <p>角色: {{ authStore.userInfo?.role }}</p>
        
        <h4>权限列表:</h4>
        <el-tag v-for="permission in authStore.permissions" :key="permission" size="small" style="margin: 2px;">
          {{ permission }}
        </el-tag>
        
        <h4>权限检查:</h4>
        <div class="permission-checks">
          <div v-for="perm in testPermissions" :key="perm">
            <el-tag :type="authStore.hasPermission(perm) ? 'success' : 'danger'" size="small">
              {{ perm }}: {{ authStore.hasPermission(perm) ? '✅' : '❌' }}
            </el-tag>
          </div>
        </div>
        
        <h4>菜单权限:</h4>
        <div class="menu-permissions">
          <p>用户管理: {{ hasUserPermission ? '✅' : '❌' }} (需要: user, user.list, role, role.list)</p>
          <p>实验管理: {{ hasExperimentPermission ? '✅' : '❌' }} (需要: experiment, experiment.catalog, experiment.booking, experiment.record)</p>
          <p>设备管理: {{ hasEquipmentPermission ? '✅' : '❌' }} (需要: equipment, equipment.list)</p>
          <p>基础数据: {{ hasBasicDataPermission ? '✅' : '❌' }} (需要: user, user.list, user.create)</p>
          <p>统计报表: {{ hasStatisticsPermission ? '✅' : '❌' }} (需要: experiment, experiment.catalog, equipment, equipment.list, user, user.list)</p>
          <p>系统管理: {{ hasSystemPermission ? '✅' : '❌' }} (需要: system, system.read, log, log.read)</p>
        </div>

        <h4>详细权限检查:</h4>
        <div class="detailed-checks">
          <p>authStore.hasAnyPermission(['user', 'user.list', 'role', 'role.list']): {{ authStore.hasAnyPermission(['user', 'user.list', 'role', 'role.list']) ? '✅' : '❌' }}</p>
          <p>authStore.hasAnyPermission(['experiment', 'experiment.catalog']): {{ authStore.hasAnyPermission(['experiment', 'experiment.catalog']) ? '✅' : '❌' }}</p>
          <p>authStore.hasAnyPermission(['equipment', 'equipment.list']): {{ authStore.hasAnyPermission(['equipment', 'equipment.list']) ? '✅' : '❌' }}</p>
        </div>

        <h4>子菜单权限检查:</h4>
        <div class="submenu-checks">
          <h5>实验管理子菜单:</h5>
          <p>实验目录: {{ authStore.hasAnyPermission(['experiment', 'experiment.catalog']) ? '✅' : '❌' }}</p>
          <p>实验预约: {{ authStore.hasPermission('experiment.booking') ? '✅' : '❌' }}</p>
          <p>实验记录: {{ authStore.hasPermission('experiment.record') ? '✅' : '❌' }}</p>
          <p>实验统计: {{ authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']) ? '✅' : '❌' }}</p>

          <h5>设备管理子菜单:</h5>
          <p>设备档案: {{ authStore.hasAnyPermission(['equipment', 'equipment.list']) ? '✅' : '❌' }}</p>
          <p>设备借用: {{ authStore.hasPermission('equipment.borrow') ? '✅' : '❌' }}</p>
          <p>设备维修: {{ authStore.hasPermission('equipment.maintenance') ? '✅' : '❌' }}</p>
          <p>二维码管理: {{ authStore.hasAnyPermission(['equipment', 'equipment.list']) ? '✅' : '❌' }}</p>

          <h5>统计报表子菜单:</h5>
          <p>实验统计: {{ authStore.hasAnyPermission(['experiment', 'experiment.catalog']) ? '✅' : '❌' }}</p>
          <p>设备统计: {{ authStore.hasAnyPermission(['equipment', 'equipment.list']) ? '✅' : '❌' }}</p>
          <p>区域分析: {{ authStore.hasAnyPermission(['user', 'user.list']) ? '✅' : '❌' }}</p>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const showDebug = ref(true)

const testPermissions = [
  'user', 'user.list', 'role', 'role.list',
  'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
  'equipment', 'equipment.list', 'equipment.create', 'equipment.borrow'
]

// 复制侧边栏的权限检查逻辑
const hasUserPermission = computed(() => {
  return authStore.hasAnyPermission(['user', 'user.list', 'role', 'role.list'])
})

const hasExperimentPermission = computed(() => {
  return authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'])
})

const hasEquipmentPermission = computed(() => {
  return authStore.hasAnyPermission(['equipment', 'equipment.list'])
})

const hasBasicDataPermission = computed(() => {
  return authStore.hasAnyPermission(['user', 'user.list', 'user.create'])
})

const hasStatisticsPermission = computed(() => {
  return authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']) ||
         authStore.hasAnyPermission(['equipment', 'equipment.list']) ||
         authStore.hasAnyPermission(['user', 'user.list'])
})

const hasSystemPermission = computed(() => {
  return authStore.hasAnyPermission(['system', 'system.read', 'log', 'log.read'])
})
</script>

<style scoped>
.debug-permissions {
  position: fixed;
  top: 10px;
  right: 10px;
  width: 400px;
  z-index: 9999;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.debug-content h4 {
  margin: 10px 0 5px 0;
  color: #333;
}

.permission-checks div {
  margin: 5px 0;
}

.menu-permissions p {
  margin: 5px 0;
}
</style>
