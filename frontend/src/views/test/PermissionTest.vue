<template>
  <div class="permission-test">
    <h2>权限测试页面</h2>
    
    <div class="user-info">
      <h3>当前用户信息</h3>
      <p>用户名: {{ currentUser?.username }}</p>
      <p>组织类型: {{ currentUser?.organization_type }}</p>
      <p>组织级别: {{ currentUser?.organization_level }}</p>
      <p>组织ID: {{ currentUser?.organization_id }}</p>
      <p>当前用户级别: {{ currentUserLevel }}</p>
    </div>

    <div class="management-levels">
      <h3>可见的管理级别选项</h3>
      <ul>
        <li v-for="level in visibleManagementLevels" :key="level.value">
          {{ level.label }} ({{ level.value }})
        </li>
      </ul>
    </div>

    <div class="test-catalogs">
      <h3>测试实验目录权限</h3>
      <div v-for="catalog in testCatalogs" :key="catalog.id" class="catalog-item">
        <h4>{{ catalog.name }} (级别: {{ catalog.management_level }})</h4>
        <p>
          查看: {{ canViewCatalog(catalog) ? '✓' : '✗' }} |
          编辑: {{ canEditCatalog(catalog) ? '✓' : '✗' }} |
          删除: {{ canDeleteCatalog(catalog) ? '✓' : '✗' }} |
          复制: {{ canCopyCatalog(catalog) ? '✓' : '✗' }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useExperimentPermission } from '@/composables/useExperimentPermission'

const {
  currentUser,
  currentUserLevel,
  visibleManagementLevels,
  canViewCatalog,
  canEditCatalog,
  canDeleteCatalog,
  canCopyCatalog
} = useExperimentPermission()

// 测试用的实验目录数据
const testCatalogs = ref([
  {
    id: 1,
    name: '省级实验目录',
    management_level: 1,
    created_by_level: 1,
    created_by_org_id: 1,
    created_by_org_type: 'province'
  },
  {
    id: 2,
    name: '市级实验目录',
    management_level: 2,
    created_by_level: 2,
    created_by_org_id: 9,
    created_by_org_type: 'region'
  },
  {
    id: 3,
    name: '区县级实验目录',
    management_level: 3,
    created_by_level: 3,
    created_by_org_id: 15,
    created_by_org_type: 'county'
  },
  {
    id: 4,
    name: '学区级实验目录',
    management_level: 4,
    created_by_level: 4,
    created_by_org_id: 25,
    created_by_org_type: 'district'
  },
  {
    id: 5,
    name: '学校级实验目录',
    management_level: 5,
    created_by_level: 5,
    created_by_org_id: 1,
    created_by_org_type: 'school'
  }
])

onMounted(() => {
  console.log('当前用户:', currentUser.value)
  console.log('用户级别:', currentUserLevel.value)
  console.log('可见级别:', visibleManagementLevels.value)
})
</script>

<style scoped>
.permission-test {
  padding: 20px;
}

.user-info, .management-levels, .test-catalogs {
  margin-bottom: 30px;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.catalog-item {
  margin-bottom: 15px;
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 3px;
}

.catalog-item h4 {
  margin: 0 0 5px 0;
}

.catalog-item p {
  margin: 0;
  font-family: monospace;
}
</style>
