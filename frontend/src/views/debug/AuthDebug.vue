<template>
  <div class="auth-debug">
    <el-card header="认证状态调试">
      <div class="debug-section">
        <h3>基本信息</h3>
        <el-descriptions :column="1" border>
          <el-descriptions-item label="是否已登录">
            <el-tag :type="authStore.isAuthenticated ? 'success' : 'danger'">
              {{ authStore.isAuthenticated ? '是' : '否' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="Token">
            <el-text v-if="authStore.token" type="success">
              {{ authStore.token.substring(0, 50) }}...
            </el-text>
            <el-text v-else type="danger">无</el-text>
          </el-descriptions-item>
          <el-descriptions-item label="用户信息">
            <el-text v-if="authStore.userInfo" type="success">已加载</el-text>
            <el-text v-else type="danger">未加载</el-text>
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="debug-section" v-if="authStore.userInfo">
        <h3>用户信息</h3>
        <el-descriptions :column="1" border>
          <el-descriptions-item label="用户名">{{ authStore.userInfo.username }}</el-descriptions-item>
          <el-descriptions-item label="真实姓名">{{ authStore.userInfo.real_name }}</el-descriptions-item>
          <el-descriptions-item label="角色">{{ authStore.userInfo.role }}</el-descriptions-item>
          <el-descriptions-item label="学校">{{ authStore.userInfo.school_name || '无' }}</el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="debug-section">
        <h3>权限信息</h3>
        <p>权限数量: {{ authStore.permissions.length }}</p>
        <el-tag v-for="permission in authStore.permissions" :key="permission" style="margin: 2px;">
          {{ permission }}
        </el-tag>
        <p v-if="authStore.permissions.length === 0" class="no-permissions">
          <el-text type="warning">未加载任何权限</el-text>
        </p>
      </div>

      <div class="debug-section">
        <h3>关键权限检查</h3>
        <el-descriptions :column="1" border>
          <el-descriptions-item label="user权限">
            <el-tag :type="authStore.hasPermission('user') ? 'success' : 'danger'">
              {{ authStore.hasPermission('user') ? '有' : '无' }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="user.list权限">
            <el-tag :type="authStore.hasPermission('user.list') ? 'success' : 'danger'">
              {{ authStore.hasPermission('user.list') ? '有' : '无' }}
            </el-tag>
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="debug-section">
        <h3>操作</h3>
        <el-space>
          <el-button @click="refreshUserInfo" :loading="loading">刷新用户信息</el-button>
          <el-button @click="testUserListAPI" :loading="apiLoading">测试用户列表API</el-button>
          <el-button @click="clearAuth" type="danger">清除认证信息</el-button>
        </el-space>
      </div>

      <div class="debug-section" v-if="apiResult">
        <h3>API测试结果</h3>
        <pre>{{ apiResult }}</pre>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { getUserListApi } from '@/api/user'

const authStore = useAuthStore()
const loading = ref(false)
const apiLoading = ref(false)
const apiResult = ref('')

const refreshUserInfo = async () => {
  loading.value = true
  try {
    await authStore.fetchUserInfo()
  } catch (error) {
    console.error('刷新用户信息失败:', error)
  } finally {
    loading.value = false
  }
}

const testUserListAPI = async () => {
  apiLoading.value = true
  try {
    const response = await getUserListApi({ page: 1, per_page: 5 })
    apiResult.value = JSON.stringify(response, null, 2)
  } catch (error) {
    apiResult.value = `错误: ${error}`
  } finally {
    apiLoading.value = false
  }
}

const clearAuth = () => {
  authStore.logout()
  apiResult.value = ''
}
</script>

<style scoped>
.auth-debug {
  padding: 20px;
}

.debug-section {
  margin: 20px 0;
}

.debug-section h3 {
  margin-bottom: 10px;
  color: #409eff;
}

.no-permissions {
  margin: 10px 0;
}

pre {
  background: #f5f5f5;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  max-height: 300px;
}
</style>
