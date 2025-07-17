<template>
  <div class="organization-management">
    <div class="page-header">
      <h2>组织信息管理</h2>
      <p class="page-description">管理您权限范围内的组织和学校信息</p>
    </div>

    <!-- 搜索和筛选 -->
    <div class="search-section">
      <el-row :gutter="20">
        <el-col :span="8">
          <el-input
            v-model="searchQuery"
            placeholder="搜索组织名称"
            clearable
            @input="handleSearch"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
        </el-col>
        <el-col :span="6">
          <el-select
            v-model="typeFilter"
            placeholder="组织类型"
            clearable
            @change="handleFilter"
          >
            <el-option label="全部" value="" />
            <el-option label="行政区域" value="region" />
            <el-option label="学校" value="school" />
          </el-select>
        </el-col>
        <el-col :span="6">
          <el-button type="primary" @click="refreshData">
            <el-icon><Refresh /></el-icon>
            刷新
          </el-button>
        </el-col>
      </el-row>
    </div>

    <!-- 组织列表 -->
    <div class="organization-list">
      <el-table
        v-loading="loading"
        :data="filteredOrganizations"
        stripe
        style="width: 100%"
      >
        <el-table-column prop="name" label="组织名称" min-width="200">
          <template #default="{ row }">
            <div class="org-name">
              <el-tag
                :type="row.type === 'region' ? 'primary' : 'success'"
                size="small"
                style="margin-right: 8px"
              >
                {{ row.type === 'region' ? '区域' : '学校' }}
              </el-tag>
              {{ row.name }}
            </div>
          </template>
        </el-table-column>
        
        <el-table-column prop="code" label="组织代码" width="120" />
        
        <el-table-column label="级别" width="80">
          <template #default="{ row }">
            <el-tag size="small">
              {{ getLevelText(row.level, row.type) }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column prop="contact_person" label="联系人" width="100" />
        <el-table-column prop="contact_phone" label="联系电话" width="130" />
        <el-table-column prop="address" label="地址" min-width="200" show-overflow-tooltip />
        
        <el-table-column label="操作" width="120" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              size="small"
              @click="editOrganization(row)"
            >
              编辑
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <!-- 编辑对话框 -->
    <el-dialog
      v-model="editDialogVisible"
      :title="`编辑${currentOrg?.type === 'region' ? '区域' : '学校'}信息`"
      width="600px"
      @close="resetEditForm"
    >
      <el-form
        ref="editFormRef"
        :model="editForm"
        :rules="editRules"
        label-width="100px"
      >
        <el-form-item label="组织名称" prop="name">
          <el-input v-model="editForm.name" placeholder="请输入组织名称" />
        </el-form-item>
        
        <el-form-item 
          v-if="canEditField('code')"
          label="组织代码" 
          prop="code"
        >
          <el-input v-model="editForm.code" placeholder="请输入组织代码" />
        </el-form-item>
        
        <el-form-item label="联系人" prop="contact_person">
          <el-input v-model="editForm.contact_person" placeholder="请输入联系人" />
        </el-form-item>
        
        <el-form-item label="联系电话" prop="contact_phone">
          <el-input v-model="editForm.contact_phone" placeholder="请输入联系电话" />
        </el-form-item>
        
        <el-form-item label="地址" prop="address">
          <el-input
            v-model="editForm.address"
            type="textarea"
            :rows="3"
            placeholder="请输入详细地址"
          />
        </el-form-item>
        
        <!-- 学校特有字段 -->
        <template v-if="currentOrg?.type === 'school'">
          <el-form-item label="学生人数" prop="student_count">
            <el-input-number
              v-model="editForm.student_count"
              :min="0"
              placeholder="学生人数"
              style="width: 100%"
            />
          </el-form-item>
          
          <el-form-item label="班级数量" prop="class_count">
            <el-input-number
              v-model="editForm.class_count"
              :min="0"
              placeholder="班级数量"
              style="width: 100%"
            />
          </el-form-item>
          
          <el-form-item label="教师人数" prop="teacher_count">
            <el-input-number
              v-model="editForm.teacher_count"
              :min="0"
              placeholder="教师人数"
              style="width: 100%"
            />
          </el-form-item>
        </template>
      </el-form>
      
      <template #footer>
        <el-button @click="editDialogVisible = false">取消</el-button>
        <el-button
          type="primary"
          :loading="submitting"
          @click="submitEdit"
        >
          保存
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Search, Refresh } from '@element-plus/icons-vue'
import { getEditableOrganizationsApi, updateOrganizationApi } from '@/api/organization'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const organizations = ref<any[]>([])
const searchQuery = ref('')
const typeFilter = ref('')
const editDialogVisible = ref(false)
const currentOrg = ref<any>(null)
const editFormRef = ref<FormInstance>()

// 编辑表单
const editForm = reactive({
  name: '',
  code: '',
  contact_person: '',
  contact_phone: '',
  address: '',
  student_count: 0,
  class_count: 0,
  teacher_count: 0
})

// 表单验证规则
const editRules: FormRules = {
  name: [
    { required: true, message: '请输入组织名称', trigger: 'blur' },
    { min: 2, max: 255, message: '长度在 2 到 255 个字符', trigger: 'blur' }
  ],
  code: [
    { max: 50, message: '长度不能超过 50 个字符', trigger: 'blur' }
  ],
  contact_person: [
    { max: 100, message: '长度不能超过 100 个字符', trigger: 'blur' }
  ],
  contact_phone: [
    { max: 20, message: '长度不能超过 20 个字符', trigger: 'blur' }
  ],
  address: [
    { max: 500, message: '长度不能超过 500 个字符', trigger: 'blur' }
  ]
}

// 计算属性
const filteredOrganizations = computed(() => {
  let result = organizations.value

  // 按类型筛选
  if (typeFilter.value) {
    result = result.filter(org => org.type === typeFilter.value)
  }

  // 按名称搜索
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(org => 
      org.name.toLowerCase().includes(query) ||
      org.code.toLowerCase().includes(query)
    )
  }

  return result
})

// 方法
const fetchOrganizations = async () => {
  loading.value = true
  try {
    const response = await getEditableOrganizationsApi()
    if (response.data) {
      organizations.value = response.data
    }
  } catch (error) {
    console.error('获取组织列表失败:', error)
    ElMessage.error('获取组织列表失败')
  } finally {
    loading.value = false
  }
}

const getLevelText = (level: number, type: string) => {
  if (type === 'region') {
    const levelMap = {
      1: '省级',
      2: '市级', 
      3: '区县级',
      4: '学区级'
    }
    return levelMap[level as keyof typeof levelMap] || `${level}级`
  } else {
    return '学校'
  }
}

const canEditField = (field: string) => {
  return currentOrg.value?.editable_fields?.includes(field) || false
}

const editOrganization = (org: any) => {
  currentOrg.value = org
  
  // 填充表单
  editForm.name = org.name || ''
  editForm.code = org.code || ''
  editForm.contact_person = org.contact_person || ''
  editForm.contact_phone = org.contact_phone || ''
  editForm.address = org.address || ''
  editForm.student_count = org.student_count || 0
  editForm.class_count = org.class_count || 0
  editForm.teacher_count = org.teacher_count || 0
  
  editDialogVisible.value = true
}

const resetEditForm = () => {
  currentOrg.value = null
  Object.assign(editForm, {
    name: '',
    code: '',
    contact_person: '',
    contact_phone: '',
    address: '',
    student_count: 0,
    class_count: 0,
    teacher_count: 0
  })
  editFormRef.value?.clearValidate()
}

const submitEdit = async () => {
  if (!editFormRef.value || !currentOrg.value) return
  
  try {
    await editFormRef.value.validate()
    
    submitting.value = true
    
    // 只提交可编辑的字段
    const updateData: any = {}
    const editableFields = currentOrg.value.editable_fields || []
    
    editableFields.forEach((field: string) => {
      if (editForm[field as keyof typeof editForm] !== undefined) {
        updateData[field] = editForm[field as keyof typeof editForm]
      }
    })
    
    await updateOrganizationApi(currentOrg.value.type, currentOrg.value.id, updateData)
    
    ElMessage.success('组织信息更新成功')
    editDialogVisible.value = false
    await fetchOrganizations()
    
  } catch (error) {
    console.error('更新组织信息失败:', error)
    ElMessage.error('更新组织信息失败')
  } finally {
    submitting.value = false
  }
}

const handleSearch = () => {
  // 搜索逻辑已在计算属性中处理
}

const handleFilter = () => {
  // 筛选逻辑已在计算属性中处理
}

const refreshData = () => {
  fetchOrganizations()
}

// 生命周期
onMounted(() => {
  fetchOrganizations()
})
</script>

<style scoped>
.organization-management {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-description {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.search-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.organization-list {
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.org-name {
  display: flex;
  align-items: center;
}
</style>
