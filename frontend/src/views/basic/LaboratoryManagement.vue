<template>
  <div class="laboratory-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>实验室管理</h2>
        <p>管理学校实验室信息和配置</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus">
          新增实验室
        </el-button>
      </div>
    </div>

    <!-- 实验室列表 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="laboratoryList"
        stripe
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="实验室名称" />
        <el-table-column prop="code" label="实验室编号" />
        <el-table-column prop="type" label="实验室类型" />
        <el-table-column prop="location" label="位置" />
        <el-table-column prop="capacity" label="容量" width="80" />
        <el-table-column prop="manager" label="管理员" />
        <el-table-column label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '正常' : '维护中' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small">编辑</el-button>
            <el-button type="danger" size="small">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Plus } from '@element-plus/icons-vue'

const loading = ref(false)
const laboratoryList = ref([
  {
    id: 1,
    name: '物理实验室A',
    code: 'LAB-PHY-A',
    type: '物理实验室',
    location: '教学楼3楼301',
    capacity: 40,
    manager: '王老师',
    status: 1
  },
  {
    id: 2,
    name: '化学实验室B',
    code: 'LAB-CHE-B',
    type: '化学实验室',
    location: '教学楼3楼302',
    capacity: 35,
    manager: '李老师',
    status: 1
  }
])

onMounted(() => {
  // TODO: 获取实验室列表
})
</script>

<style scoped>
.laboratory-management-page {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.table-section {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
