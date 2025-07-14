<template>
  <div class="permission-test">
    <el-card>
      <template #header>
        <h2>权限测试页面</h2>
      </template>
      
      <div class="test-content">
        <h3>用户信息</h3>
        <p><strong>用户名:</strong> {{ authStore.userInfo?.username }}</p>
        <p><strong>角色:</strong> {{ authStore.userInfo?.role }}</p>
        <p><strong>真实姓名:</strong> {{ authStore.userInfo?.real_name }}</p>
        
        <h3>权限列表 ({{ authStore.permissions.length }}个)</h3>
        <div class="permissions-grid">
          <el-tag 
            v-for="permission in authStore.permissions" 
            :key="permission" 
            type="success" 
            size="small"
            style="margin: 2px;"
          >
            {{ permission }}
          </el-tag>
        </div>
        
        <h3>权限测试</h3>
        <el-table :data="permissionTests" style="width: 100%">
          <el-table-column prop="permission" label="权限" width="200" />
          <el-table-column prop="result" label="结果" width="100">
            <template #default="scope">
              <el-tag :type="scope.row.result ? 'success' : 'danger'" size="small">
                {{ scope.row.result ? '✅ 有权限' : '❌ 无权限' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="description" label="说明" />
        </el-table>
        
        <h3>菜单权限测试</h3>
        <el-table :data="menuTests" style="width: 100%">
          <el-table-column prop="menu" label="菜单" width="150" />
          <el-table-column prop="result" label="显示状态" width="120">
            <template #default="scope">
              <el-tag :type="scope.row.result ? 'success' : 'info'" size="small">
                {{ scope.row.result ? '✅ 显示' : '❌ 隐藏' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="permissions" label="需要权限" />
        </el-table>
        
        <h3>原始数据</h3>
        <el-collapse>
          <el-collapse-item title="用户信息原始数据" name="userInfo">
            <pre>{{ JSON.stringify(authStore.userInfo, null, 2) }}</pre>
          </el-collapse-item>
          <el-collapse-item title="权限数组原始数据" name="permissions">
            <pre>{{ JSON.stringify(authStore.permissions, null, 2) }}</pre>
          </el-collapse-item>
        </el-collapse>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// 权限测试数据
const permissionTests = computed(() => {
  const tests = [
    { permission: 'user', description: '用户管理基础权限' },
    { permission: 'user.list', description: '用户列表查看' },
    { permission: 'role', description: '角色管理基础权限' },
    { permission: 'role.list', description: '角色列表查看' },
    { permission: 'experiment', description: '实验管理基础权限' },
    { permission: 'experiment.catalog', description: '实验目录' },
    { permission: 'experiment.booking', description: '实验预约' },
    { permission: 'experiment.record', description: '实验记录' },
    { permission: 'equipment', description: '设备管理基础权限' },
    { permission: 'equipment.list', description: '设备列表查看' },
    { permission: 'equipment.create', description: '设备创建' },
    { permission: 'equipment.borrow', description: '设备借用' }
  ]
  
  return tests.map(test => ({
    ...test,
    result: authStore.hasPermission(test.permission)
  }))
})

// 菜单权限测试
const menuTests = computed(() => {
  const tests = [
    {
      menu: '用户管理',
      permissions: ['user', 'user.list', 'role', 'role.list'],
      result: authStore.hasAnyPermission(['user', 'user.list', 'role', 'role.list'])
    },
    {
      menu: '实验管理',
      permissions: ['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'],
      result: authStore.hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record'])
    },
    {
      menu: '设备管理',
      permissions: ['equipment', 'equipment.list'],
      result: authStore.hasAnyPermission(['equipment', 'equipment.list'])
    },
    {
      menu: '基础数据',
      permissions: ['user', 'user.list', 'user.create'],
      result: authStore.hasAnyPermission(['user', 'user.list', 'user.create'])
    }
  ]
  
  return tests.map(test => ({
    ...test,
    permissions: test.permissions.join(', ')
  }))
})
</script>

<style scoped>
.permission-test {
  padding: 20px;
}

.test-content h3 {
  margin: 20px 0 10px 0;
  color: #333;
}

.permissions-grid {
  margin: 10px 0;
  min-height: 40px;
  border: 1px dashed #ddd;
  padding: 10px;
  border-radius: 4px;
}

pre {
  background: #f5f5f5;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 12px;
}
</style>
