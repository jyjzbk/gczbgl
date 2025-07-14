<template>
  <div class="school-management-page">
    <div class="page-header">
      <div class="header-content">
        <h2>学校管理</h2>
        <p>管理系统中的学校信息</p>
      </div>
      <div class="header-actions">
        <el-button type="primary" :icon="Plus">
          新增学校
        </el-button>
      </div>
    </div>

    <!-- 学校列表 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="schoolList"
        stripe
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="学校名称" />
        <el-table-column prop="code" label="学校代码" />
        <el-table-column prop="type" label="学校类型" />
        <el-table-column prop="address" label="地址" />
        <el-table-column prop="contact_person" label="联系人" />
        <el-table-column prop="contact_phone" label="联系电话" />
        <el-table-column label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status === 1 ? '正常' : '禁用' }}
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
const schoolList = ref([
  {
    id: 1,
    name: '北京市第一中学',
    code: 'BJ001',
    type: '高中',
    address: '北京市朝阳区',
    contact_person: '张校长',
    contact_phone: '010-12345678',
    status: 1
  },
  {
    id: 2,
    name: '上海市实验小学',
    code: 'SH001',
    type: '小学',
    address: '上海市浦东新区',
    contact_person: '李主任',
    contact_phone: '021-87654321',
    status: 1
  }
])

onMounted(() => {
  // TODO: 获取学校列表
})
</script>

<style scoped>
.school-management-page {
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
