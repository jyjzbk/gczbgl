<template>
  <div class="subject-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>学科管理</h2>
        <p>管理系统中的学科分类信息</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus">
          新增学科
        </el-button>
      </div>
    </div>

    <!-- 学科列表 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="subjectList"
        stripe
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="学科名称" />
        <el-table-column prop="code" label="学科代码" />
        <el-table-column prop="category" label="学科分类" />
        <el-table-column prop="grade_range" label="适用年级" />
        <el-table-column prop="description" label="描述" />
        <el-table-column label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '启用' : '禁用' }}
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
const subjectList = ref([
  {
    id: 1,
    name: '物理',
    code: 'PHY',
    category: '理科',
    grade_range: '初中-高中',
    description: '物理学科实验教学',
    status: 1
  },
  {
    id: 2,
    name: '化学',
    code: 'CHE',
    category: '理科',
    grade_range: '初中-高中',
    description: '化学学科实验教学',
    status: 1
  },
  {
    id: 3,
    name: '生物',
    code: 'BIO',
    category: '理科',
    grade_range: '初中-高中',
    description: '生物学科实验教学',
    status: 1
  }
])

onMounted(() => {
  // TODO: 获取学科列表
})
</script>

<style scoped>
.subject-management-page {
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
