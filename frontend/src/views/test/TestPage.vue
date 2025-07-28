<template>
  <div class="test-page">
    <h1>测试页面</h1>
    <p>如果你能看到这个页面，说明路由和模块加载正常工作。</p>
    <el-button type="primary" @click="testApi">测试API</el-button>
  </div>
</template>

<script setup lang="ts">
import { ElMessage, ElButton } from 'element-plus'

const testApi = () => {
  ElMessage.success('测试成功！')
}
</script>

<style scoped>
.test-page {
  padding: 20px;
}
</style>
