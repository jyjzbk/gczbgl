<template>
  <div class="equipment-standard-management">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h2>教学仪器配备标准</h2>
        <p>管理教育部和教育厅制定的各学段学科仪器配备标准</p>
      </div>
      <div class="header-actions">
        <PermissionTooltip permission="basic.equipment_standard.create">
          <el-button type="primary" :icon="Plus" @click="handleCreate">
            新增标准
          </el-button>
        </PermissionTooltip>
        <el-button :icon="Refresh" @click="loadData">
          刷新
        </el-button>
      </div>
    </div>

    <!-- 搜索区域 -->
    <div class="search-section">
      <el-form :model="searchForm" inline>
        <el-form-item label="制定机构">
          <el-select
            v-model="searchForm.authority_type"
            placeholder="请选择制定机构"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="option in authorityOptions"
              :key="option.value"
              :label="option.label"
              :value="option.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学段">
          <el-select
            v-model="searchForm.stage"
            placeholder="请选择学段"
            clearable
            style="width: 100px"
          >
            <el-option
              v-for="option in stageOptions"
              :key="option.value"
              :label="option.label"
              :value="option.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="学科">
          <el-select
            v-model="searchForm.subject_code"
            placeholder="请选择学科"
            clearable
            style="width: 120px"
          >
            <el-option
              v-for="subject in subjects"
              :key="subject.code"
              :label="subject.name"
              :value="subject.code"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="标准名称">
          <el-input
            v-model="searchForm.search"
            placeholder="请输入标准名称"
            clearable
            style="width: 200px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">
            搜索
          </el-button>
          <el-button :icon="RefreshLeft" @click="handleReset">
            重置
          </el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 数据表格 -->
    <div class="table-section">
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        style="width: 100%"
      >
        <el-table-column type="index" label="序号" width="60" align="center" />
        
        <el-table-column prop="name" label="标准名称" min-width="200" show-overflow-tooltip />
        
        <el-table-column prop="authority_type_text" label="制定机构" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.authority_type === 1 ? 'danger' : 'warning'">
              {{ row.authority_type_text }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column prop="stage_text" label="学段" width="80" align="center" />
        
        <el-table-column prop="subject_name" label="学科" width="100" align="center" />
        
        <el-table-column prop="version" label="版本" width="80" align="center" />
        
        <el-table-column prop="effective_date" label="生效日期" width="120" align="center" />
        
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'danger'">
              {{ row.status_text }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column label="操作" width="250" align="center" fixed="right">
          <template #default="{ row }">
            <el-button
              type="info"
              size="small"
              :icon="View"
              @click="handleView(row)"
            >
              查看
            </el-button>
            <PermissionTooltip permission="basic.equipment_standard.edit">
              <el-button
                type="primary"
                size="small"
                :icon="Edit"
                @click="handleEdit(row)"
              >
                编辑
              </el-button>
            </PermissionTooltip>
            <PermissionTooltip permission="basic.equipment_standard.delete">
              <el-button
                type="danger"
                size="small"
                :icon="Delete"
                @click="handleDelete(row)"
              >
                删除
              </el-button>
            </PermissionTooltip>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination-section">
        <el-pagination
          v-model:current-page="pagination.current_page"
          v-model:page-size="pagination.per_page"
          :total="pagination.total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>

    <!-- 新增/编辑对话框 -->
    <el-dialog
      v-model="formVisible"
      :title="isEdit ? '编辑配备标准' : '新增配备标准'"
      width="70%"
      destroy-on-close
      @close="handleFormClose"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-width="120px"
        label-position="left"
      >
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="标准名称" prop="name">
              <el-input
                v-model="formData.name"
                placeholder="请输入标准名称"
                maxlength="200"
                show-word-limit
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="标准代码" prop="code">
              <el-input
                v-model="formData.code"
                placeholder="请输入标准代码"
                maxlength="100"
                show-word-limit
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="制定机构" prop="authority_type">
              <el-select
                v-model="formData.authority_type"
                placeholder="请选择制定机构"
                style="width: 100%"
              >
                <el-option
                  v-for="option in authorityOptions"
                  :key="option.value"
                  :label="option.label"
                  :value="option.value"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="学段" prop="stage">
              <el-select
                v-model="formData.stage"
                placeholder="请选择学段"
                style="width: 100%"
              >
                <el-option
                  v-for="option in stageOptions"
                  :key="option.value"
                  :label="option.label"
                  :value="option.value"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="学科" prop="subject_code">
              <el-select
                v-model="formData.subject_code"
                placeholder="请选择学科"
                style="width: 100%"
                @change="handleSubjectChange"
              >
                <el-option
                  v-for="subject in subjects"
                  :key="subject.code"
                  :label="subject.name"
                  :value="subject.code"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="版本" prop="version">
              <el-input
                v-model="formData.version"
                placeholder="请输入版本号"
                maxlength="20"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="生效日期" prop="effective_date">
              <el-date-picker
                v-model="formData.effective_date"
                type="date"
                placeholder="请选择生效日期"
                style="width: 100%"
                format="YYYY-MM-DD"
                value-format="YYYY-MM-DD"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="失效日期">
              <el-date-picker
                v-model="formData.expiry_date"
                type="date"
                placeholder="请选择失效日期（可选）"
                style="width: 100%"
                format="YYYY-MM-DD"
                value-format="YYYY-MM-DD"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="标准描述">
          <el-input
            v-model="formData.description"
            type="textarea"
            :rows="3"
            placeholder="请输入标准描述"
            maxlength="500"
            show-word-limit
          />
        </el-form-item>

        <el-form-item label="设备清单" prop="equipment_list">
          <div class="equipment-list-form">
            <div v-for="(category, categoryIndex) in formData.equipment_list" :key="categoryIndex" class="category-form">
              <div class="category-header">
                <el-input
                  v-model="category.category"
                  placeholder="请输入分类名称"
                  style="width: 200px"
                />
                <el-button
                  type="danger"
                  size="small"
                  :icon="Delete"
                  @click="removeCategory(categoryIndex)"
                  :disabled="formData.equipment_list.length <= 1"
                >
                  删除分类
                </el-button>
              </div>

              <el-table :data="category.items" border style="margin: 10px 0">
                <el-table-column label="设备名称" width="200">
                  <template #default="{ row, $index }">
                    <el-input
                      v-model="row.name"
                      placeholder="设备名称"
                      size="small"
                    />
                  </template>
                </el-table-column>
                <el-table-column label="规格型号" width="200">
                  <template #default="{ row, $index }">
                    <el-input
                      v-model="row.specification"
                      placeholder="规格型号"
                      size="small"
                    />
                  </template>
                </el-table-column>
                <el-table-column label="数量" width="100">
                  <template #default="{ row, $index }">
                    <el-input-number
                      v-model="row.quantity"
                      :min="1"
                      size="small"
                      style="width: 100%"
                    />
                  </template>
                </el-table-column>
                <el-table-column label="单位" width="80">
                  <template #default="{ row, $index }">
                    <el-input
                      v-model="row.unit"
                      placeholder="单位"
                      size="small"
                    />
                  </template>
                </el-table-column>
                <el-table-column label="操作" width="100">
                  <template #default="{ row, $index }">
                    <el-button
                      type="danger"
                      size="small"
                      :icon="Delete"
                      @click="removeItem(categoryIndex, $index)"
                      :disabled="category.items.length <= 1"
                    >
                      删除
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>

              <div class="category-actions">
                <el-button
                  type="primary"
                  size="small"
                  :icon="Plus"
                  @click="addItem(categoryIndex)"
                >
                  添加设备
                </el-button>
              </div>
            </div>

            <div class="equipment-actions">
              <el-button
                type="success"
                :icon="Plus"
                @click="addCategory"
              >
                添加分类
              </el-button>
            </div>
          </div>
        </el-form-item>
      </el-form>

      <template #footer>
        <div class="dialog-footer">
          <el-button @click="formVisible = false">取消</el-button>
          <el-button type="primary" @click="handleSubmit" :loading="submitLoading">
            {{ isEdit ? '更新' : '创建' }}
          </el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="detailVisible"
      title="配备标准详情"
      width="80%"
      destroy-on-close
    >
      <div v-if="currentStandard" class="standard-detail">
        <el-descriptions :column="2" border>
          <el-descriptions-item label="标准名称">{{ currentStandard.name }}</el-descriptions-item>
          <el-descriptions-item label="标准代码">{{ currentStandard.code }}</el-descriptions-item>
          <el-descriptions-item label="制定机构">{{ currentStandard.authority_type_text }}</el-descriptions-item>
          <el-descriptions-item label="学段">{{ currentStandard.stage_text }}</el-descriptions-item>
          <el-descriptions-item label="学科">{{ currentStandard.subject_name }}</el-descriptions-item>
          <el-descriptions-item label="版本">{{ currentStandard.version }}</el-descriptions-item>
          <el-descriptions-item label="生效日期">{{ currentStandard.effective_date }}</el-descriptions-item>
          <el-descriptions-item label="失效日期">{{ currentStandard.expiry_date || '无' }}</el-descriptions-item>
          <el-descriptions-item label="描述" :span="2">{{ currentStandard.description || '无' }}</el-descriptions-item>
        </el-descriptions>
        
        <div class="equipment-list-section" v-loading="detailLoading">
          <h3>设备清单</h3>
          <div v-for="(category, index) in detailedEquipmentList" :key="index" class="category-section">
            <h4>{{ category.category }}</h4>
            <el-table :data="category.items" border>
              <el-table-column prop="item_code" label="编号" width="120" align="center">
                <template #default="{ row }">
                  <span v-if="row.item_code">{{ row.item_code }}</span>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
              <el-table-column prop="name" label="设备名称" min-width="150" />
              <el-table-column prop="specification" label="规格型号" min-width="150" show-overflow-tooltip />
              <el-table-column prop="unit" label="单位" width="80" align="center" />
              <el-table-column prop="quantity" label="配备数量" width="100" align="center" />
              <el-table-column prop="unit_price" label="单价（元）" width="100" align="center">
                <template #default="{ row }">
                  <span v-if="row.unit_price">{{ row.unit_price }}</span>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
              <el-table-column prop="total_amount" label="总价（元）" width="100" align="center">
                <template #default="{ row }">
                  <span v-if="row.total_amount">{{ row.total_amount }}</span>
                  <span v-else-if="row.unit_price && row.quantity">{{ (row.unit_price * row.quantity).toFixed(2) }}</span>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
              <el-table-column label="是否必须配备" width="120" align="center">
                <template #default="{ row }">
                  <el-tag v-if="row.is_basic === true" type="danger" size="small">必须配备</el-tag>
                  <el-tag v-else-if="row.is_optional === true" type="warning" size="small">选配</el-tag>
                  <el-tag v-else-if="row.is_basic === false" type="info" size="small">非必须</el-tag>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
              <el-table-column prop="activity_suggestion" label="活动建议" min-width="120" show-overflow-tooltip>
                <template #default="{ row }">
                  <span v-if="row.activity_suggestion">{{ row.activity_suggestion }}</span>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
              <el-table-column prop="remarks" label="备注" min-width="120" show-overflow-tooltip>
                <template #default="{ row }">
                  <span v-if="row.remarks">{{ row.remarks }}</span>
                  <span v-else class="text-muted">-</span>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox, type FormInstance, type FormRules } from 'element-plus'
import { Plus, Search, RefreshLeft, Refresh, View, Edit, Delete } from '@element-plus/icons-vue'
import {
  getEquipmentStandardsApi,
  createEquipmentStandardApi,
  updateEquipmentStandardApi,
  deleteEquipmentStandardApi,
  getDetailedEquipmentApi,
  getSubjectsApi,
  type EquipmentStandard,
  type EquipmentStandardForm,
  type Subject,
  AUTHORITY_TYPE_OPTIONS,
  STAGE_OPTIONS
} from '@/api/equipmentStandard'
import PermissionTooltip from '@/components/PermissionTooltip.vue'

// 响应式数据
const loading = ref(false)
const tableData = ref<EquipmentStandard[]>([])
const detailVisible = ref(false)
const detailLoading = ref(false)
const currentStandard = ref<EquipmentStandard | null>(null)
const detailedEquipmentList = ref<any[]>([])
const subjects = ref<Subject[]>([])

// 表单相关
const formVisible = ref(false)
const isEdit = ref(false)
const submitLoading = ref(false)
const formRef = ref<FormInstance>()

// 表单数据
const formData = reactive<EquipmentStandardForm>({
  name: '',
  code: '',
  authority_type: undefined as number | undefined,
  stage: undefined as number | undefined,
  subject_code: '',
  subject_name: '',
  description: '',
  equipment_list: [
    {
      category: '',
      items: [
        {
          name: '',
          specification: '',
          quantity: 1,
          unit: ''
        }
      ]
    }
  ],
  version: '1.0',
  effective_date: '',
  expiry_date: '',
  status: true
})

// 表单验证规则
const formRules: FormRules = {
  name: [
    { required: true, message: '请输入标准名称', trigger: 'blur' },
    { min: 2, max: 200, message: '标准名称长度在 2 到 200 个字符', trigger: 'blur' }
  ],
  code: [
    { required: true, message: '请输入标准代码', trigger: 'blur' },
    { min: 2, max: 100, message: '标准代码长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  authority_type: [
    { required: true, message: '请选择制定机构', trigger: 'change' }
  ],
  stage: [
    { required: true, message: '请选择学段', trigger: 'change' }
  ],
  subject_code: [
    { required: true, message: '请选择学科', trigger: 'change' }
  ],
  version: [
    { required: true, message: '请输入版本号', trigger: 'blur' }
  ],
  effective_date: [
    { required: true, message: '请选择生效日期', trigger: 'change' }
  ],
  equipment_list: [
    { required: true, message: '请添加设备清单', trigger: 'change' }
  ]
}

// 搜索表单
const searchForm = reactive({
  authority_type: undefined as number | undefined,
  stage: undefined as number | undefined,
  subject_code: '',
  search: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 15,
  total: 0
})

// 选项数据
const authorityOptions = AUTHORITY_TYPE_OPTIONS
const stageOptions = STAGE_OPTIONS

// 加载学科数据
const loadSubjects = async () => {
  try {
    const response = await getSubjectsApi()
    subjects.value = response.data
  } catch (error) {
    console.error('加载学科失败:', error)
  }
}

// 加载数据
const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...searchForm
    }
    
    const response = await getEquipmentStandardsApi(params)
    tableData.value = response.data.data
    Object.assign(pagination, response.data)
  } catch (error) {
    console.error('加载配备标准失败:', error)
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.current_page = 1
  loadData()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    authority_type: undefined,
    stage: undefined,
    subject_code: '',
    search: ''
  })
  pagination.current_page = 1
  loadData()
}

// 分页变化
const handleSizeChange = (size: number) => {
  pagination.per_page = size
  pagination.current_page = 1
  loadData()
}

const handleCurrentChange = (page: number) => {
  pagination.current_page = page
  loadData()
}

// 查看详情
const handleView = async (row: EquipmentStandard) => {
  currentStandard.value = row
  detailVisible.value = true

  // 获取详细设备信息
  try {
    detailLoading.value = true
    const response = await getDetailedEquipmentApi(row.id)
    detailedEquipmentList.value = response.data.equipment_list || []
  } catch (error: any) {
    console.error('获取详细信息失败:', error)
    ElMessage.error('获取详细信息失败')
    // 使用原始数据作为备选
    detailedEquipmentList.value = row.equipment_list || []
  } finally {
    detailLoading.value = false
  }
}

// 重置表单数据
const resetFormData = () => {
  Object.assign(formData, {
    name: '',
    code: '',
    authority_type: undefined,
    stage: undefined,
    subject_code: '',
    subject_name: '',
    description: '',
    equipment_list: [
      {
        category: '',
        items: [
          {
            name: '',
            specification: '',
            quantity: 1,
            unit: ''
          }
        ]
      }
    ],
    version: '1.0',
    effective_date: '',
    expiry_date: '',
    status: true
  })
}

// 学科变化处理
const handleSubjectChange = (subjectCode: string) => {
  const subject = subjects.value.find(s => s.code === subjectCode)
  if (subject) {
    formData.subject_name = subject.name
  }
}

// 添加分类
const addCategory = () => {
  formData.equipment_list.push({
    category: '',
    items: [
      {
        name: '',
        specification: '',
        quantity: 1,
        unit: ''
      }
    ]
  })
}

// 删除分类
const removeCategory = (categoryIndex: number) => {
  if (formData.equipment_list.length > 1) {
    formData.equipment_list.splice(categoryIndex, 1)
  }
}

// 添加设备
const addItem = (categoryIndex: number) => {
  formData.equipment_list[categoryIndex].items.push({
    name: '',
    specification: '',
    quantity: 1,
    unit: ''
  })
}

// 删除设备
const removeItem = (categoryIndex: number, itemIndex: number) => {
  const category = formData.equipment_list[categoryIndex]
  if (category.items.length > 1) {
    category.items.splice(itemIndex, 1)
  }
}

// 表单关闭处理
const handleFormClose = () => {
  formRef.value?.resetFields()
  resetFormData()
  isEdit.value = false
}

// 新增
const handleCreate = () => {
  resetFormData()
  isEdit.value = false
  formVisible.value = true
}

// 编辑
const handleEdit = (row: EquipmentStandard) => {
  // 设置当前标准
  currentStandard.value = row

  // 复制数据到表单
  Object.assign(formData, {
    ...row,
    equipment_list: JSON.parse(JSON.stringify(row.equipment_list || []))
  })
  isEdit.value = true
  formVisible.value = true
}

// 表单提交
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()

    // 验证设备清单
    for (const category of formData.equipment_list) {
      if (!category.category.trim()) {
        ElMessage.error('请填写分类名称')
        return
      }
      for (const item of category.items) {
        if (!item.name.trim()) {
          ElMessage.error('请填写设备名称')
          return
        }
        if (!item.unit.trim()) {
          ElMessage.error('请填写设备单位')
          return
        }
      }
    }

    submitLoading.value = true

    if (isEdit.value) {
      await updateEquipmentStandardApi(currentStandard.value!.id, formData)
      ElMessage.success('更新成功')
    } else {
      await createEquipmentStandardApi(formData)
      ElMessage.success('创建成功')
    }

    formVisible.value = false
    loadData()
  } catch (error: any) {
    console.error('提交失败:', error)
    ElMessage.error(error.response?.data?.message || '操作失败')
  } finally {
    submitLoading.value = false
  }
}

// 删除
const handleDelete = async (row: EquipmentStandard) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除配备标准"${row.name}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteEquipmentStandardApi(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error: any) {
    if (error !== 'cancel') {
      console.error('删除失败:', error)
      ElMessage.error(error.response?.data?.message || '删除失败')
    }
  }
}

// 初始化
onMounted(() => {
  loadSubjects()
  loadData()
})
</script>

<style scoped>
.equipment-standard-management {
  padding: 20px;
  background: #f5f7fa;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content h2 {
  margin: 0 0 8px 0;
  color: #303133;
  font-size: 24px;
  font-weight: 600;
}

.header-content p {
  margin: 0;
  color: #606266;
  font-size: 14px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.search-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-section {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.pagination-section {
  padding: 20px;
  display: flex;
  justify-content: center;
}

.standard-detail {
  padding: 20px 0;
}

.equipment-list-section {
  margin-top: 30px;
}

.equipment-list-section h3 {
  margin: 0 0 20px 0;
  color: #303133;
  font-size: 18px;
  font-weight: 600;
  border-bottom: 2px solid #409EFF;
  padding-bottom: 8px;
}

.category-section {
  margin-bottom: 30px;
}

.category-section h4 {
  margin: 0 0 15px 0;
  color: #606266;
  font-size: 16px;
  font-weight: 500;
}

/* 表单样式 */
.equipment-list-form {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  padding: 15px;
  background: #fafafa;
}

.category-form {
  margin-bottom: 20px;
  padding: 15px;
  background: #fff;
  border-radius: 4px;
  border: 1px solid #e4e7ed;
}

.category-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
}

.category-actions {
  margin-top: 10px;
  text-align: center;
}

.equipment-actions {
  text-align: center;
  padding-top: 15px;
  border-top: 1px solid #e4e7ed;
}

.dialog-footer {
  text-align: right;
}

/* 详情页面样式 */
.text-muted {
  color: #909399;
  font-style: italic;
}
</style>
