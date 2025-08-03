<template>
  <div class="smart-reservation">
    <div class="page-card">
      <div class="page-header">
        <h2>智能实验预约</h2>
        <p>选择实验和时间，系统将自动配置器材清单并检测冲突</p>
      </div>

      <!-- 预约表单 -->
      <el-form
        ref="reservationFormRef"
        :model="reservationForm"
        :rules="reservationRules"
        label-width="120px"
        class="reservation-form"
      >
        <!-- 实验目录筛选 -->
        <div class="experiment-filters">
          <h4>实验目录筛选</h4>
          <el-row :gutter="16">
            <el-col :span="6">
              <el-form-item label="学科">
                <el-select
                  v-model="catalogFilters.subject_id"
                  placeholder="请选择学科"
                  clearable
                  style="width: 100%"
                  @change="handleSubjectChange"
                >
                  <el-option
                    v-for="subject in (subjects || [])"
                    :key="subject.id"
                    :label="subject.name"
                    :value="subject.id"
                  />
                </el-select>
              </el-form-item>
            </el-col>

            <el-col :span="6">
              <el-form-item label="教材版本">
                <el-select
                  v-model="catalogFilters.textbook_version_id"
                  placeholder="请选择版本"
                  clearable
                  style="width: 100%"
                  @change="handleVersionChange"
                >
                  <el-option
                    v-for="version in (textbookVersions || [])"
                    :key="version.id"
                    :label="version.name"
                    :value="version.id"
                  />
                </el-select>
              </el-form-item>
            </el-col>

            <el-col :span="6">
              <el-form-item label="年级">
                <el-select
                  v-model="catalogFilters.grade_level"
                  placeholder="请选择年级"
                  clearable
                  style="width: 100%"
                  @change="handleGradeChange"
                >
                  <el-option
                    v-for="grade in (gradeOptions || [])"
                    :key="grade.value"
                    :label="grade.label"
                    :value="grade.value"
                  />
                </el-select>
              </el-form-item>
            </el-col>

            <el-col :span="6">
              <el-form-item label="册次">
                <el-select
                  v-model="catalogFilters.volume"
                  placeholder="请选择册次"
                  clearable
                  style="width: 100%"
                  @change="handleVolumeChange"
                >
                  <el-option label="上册" value="上册" />
                  <el-option label="下册" value="下册" />
                  <el-option label="全册" value="全册" />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row :gutter="16">
            <el-col :span="12">
              <el-form-item label="章节">
                <ChapterSelector
                  v-model="catalogFilters.chapter_id"
                  :subject-id="catalogFilters.subject_id"
                  :textbook-version-id="catalogFilters.textbook_version_id"
                  :grade-level="catalogFilters.grade_level"
                  :volume="catalogFilters.volume"
                  display-mode="select"
                  :show-filters="false"
                  @change="handleChapterChange"
                />
              </el-form-item>
            </el-col>

            <el-col :span="12">
              <el-form-item label="实验类型">
                <el-select
                  v-model="catalogFilters.experiment_type"
                  placeholder="请选择类型"
                  clearable
                  style="width: 100%"
                  @change="loadExperimentCatalogs"
                >
                  <el-option label="必做" value="必做" />
                  <el-option label="选做" value="选做" />
                  <el-option label="演示" value="演示" />
                  <el-option label="分组" value="分组" />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="实验名称" prop="catalog_id">
              <el-select
                v-model="reservationForm.catalog_id"
                placeholder="请选择实验"
                filterable
                clearable
                style="width: 100%"
                @change="handleExperimentChange"
              >
                <el-option
                  v-for="catalog in experimentCatalogs"
                  :key="catalog.id"
                  :label="catalog.name"
                  :value="catalog.id"
                />
              </el-select>
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item label="实验室" prop="laboratory_id">
              <el-select
                v-model="reservationForm.laboratory_id"
                placeholder="请选择实验室"
                clearable
                style="width: 100%"
                @change="handleLaboratoryChange"
              >
                <el-option
                  v-for="lab in (laboratories || [])"
                  :key="lab.id"
                  :label="lab.name"
                  :value="lab.id"
                >
                  <div class="laboratory-option">
                    <span>{{ lab.name }}</span>
                    <span class="capacity">容量: {{ lab.capacity }}人</span>
                  </div>
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="预约日期" prop="reservation_date">
              <el-date-picker
                v-model="reservationForm.reservation_date"
                type="date"
                placeholder="选择日期"
                style="width: 100%"
                value-format="YYYY-MM-DD"
                :disabled-date="disabledDate"
                @change="checkConflicts"
              />
            </el-form-item>
          </el-col>

          <el-col :span="8">
            <el-form-item label="开始时间" prop="start_time">
              <el-time-picker
                v-model="reservationForm.start_time"
                placeholder="选择开始时间"
                style="width: 100%"
                format="HH:mm"
                value-format="HH:mm"
                @change="checkConflicts"
              />
            </el-form-item>
          </el-col>

          <el-col :span="8">
            <el-form-item label="结束时间" prop="end_time">
              <el-time-picker
                v-model="reservationForm.end_time"
                placeholder="选择结束时间"
                style="width: 100%"
                format="HH:mm"
                value-format="HH:mm"
                @change="checkConflicts"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="班级名称" prop="class_name">
              <el-input
                v-model="reservationForm.class_name"
                placeholder="请输入班级名称"
              />
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item label="学生人数" prop="student_count">
              <el-input-number
                v-model="reservationForm.student_count"
                :min="1"
                :max="100"
                style="width: 100%"
                @change="handleStudentCountChange"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="优先级" prop="priority">
              <el-select
                v-model="reservationForm.priority"
                placeholder="请选择优先级"
                style="width: 100%"
              >
                <el-option label="低" value="low" />
                <el-option label="普通" value="normal" />
                <el-option label="高" value="high" />
                <el-option label="紧急" value="urgent" />
              </el-select>
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item label="自动借用器材">
              <el-switch
                v-model="reservationForm.auto_borrow_equipment"
                active-text="是"
                inactive-text="否"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="实验准备说明">
          <el-input
            v-model="reservationForm.preparation_notes"
            type="textarea"
            :rows="3"
            placeholder="请输入实验准备说明"
          />
        </el-form-item>
      </el-form>

      <!-- 实验信息展示 -->
      <div v-if="selectedExperiment" class="experiment-info">
        <h3>实验信息</h3>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="实验名称">
            {{ selectedExperiment.name }}
          </el-descriptions-item>
          <el-descriptions-item label="实验类型">
            <el-tag :type="getExperimentTypeColor(selectedExperiment.type)">
              {{ getExperimentTypeName(selectedExperiment.type) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="建议时长">
            {{ selectedExperiment.duration }}分钟
          </el-descriptions-item>
          <el-descriptions-item label="建议人数">
            {{ selectedExperiment.student_count }}人
          </el-descriptions-item>
          <el-descriptions-item label="实验目的" :span="2">
            {{ selectedExperiment.objective }}
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <!-- 器材需求配置 -->
      <div class="equipment-requirements">
        <div class="equipment-header">
          <h3>器材需求配置</h3>
          <div class="equipment-actions">
            <el-button
              v-if="selectedExperiment && selectedExperiment.parent_catalog_id"
              type="primary"
              size="small"
              @click="handleInheritEquipment"
            >
              <el-icon><Download /></el-icon>
              继承标准配置
            </el-button>
            <el-button type="success" size="small" @click="handleAutoCalculate">
              <el-icon><Operation /></el-icon>
              智能计算
            </el-button>
          </div>
        </div>

        <!-- 器材需求表格 -->
        <el-table :data="equipmentRequirements || []" border size="small">
          <el-table-column prop="equipment_name" label="器材名称" min-width="150">
            <template #default="{ row }">
              <div class="equipment-info">
                <span class="equipment-name">{{ row.equipment_name }}</span>
                <el-tag v-if="row.is_inherited" type="info" size="small" class="inherited-tag">
                  继承
                </el-tag>
                <el-tag v-if="row.is_auto_calculated" type="success" size="small" class="auto-tag">
                  智能
                </el-tag>
              </div>
            </template>
          </el-table-column>

          <el-table-column prop="equipment_code" label="器材编号" width="120" />

          <el-table-column label="标准数量" align="center" width="100">
            <template #default="{ row }">
              <span class="standard-quantity">{{ row.standard_quantity || '-' }}</span>
            </template>
          </el-table-column>

          <el-table-column label="需要数量" align="center" width="120">
            <template #default="{ row, $index }">
              <el-input-number
                v-model="row.required_quantity"
                :min="0"
                :max="999"
                size="small"
                @change="handleQuantityChange($index)"
              />
            </template>
          </el-table-column>

          <el-table-column prop="available_quantity" label="可用数量" align="center" width="100" />

          <el-table-column label="状态" align="center" width="100">
            <template #default="{ row }">
              <el-tag v-if="row.shortage === 0" type="success" size="small">充足</el-tag>
              <el-tag v-else type="danger" size="small">缺少{{ row.shortage }}</el-tag>
            </template>
          </el-table-column>

          <el-table-column label="使用类型" align="center" width="120">
            <template #default="{ row, $index }">
              <el-select
                v-model="row.usage_type"
                size="small"
                style="width: 100px"
                @change="handleUsageTypeChange($index)"
              >
                <el-option label="必需" value="required" />
                <el-option label="可选" value="optional" />
                <el-option label="替代" value="alternative" />
              </el-select>
            </template>
          </el-table-column>

          <el-table-column label="调整理由" min-width="150">
            <template #default="{ row, $index }">
              <el-input
                v-model="row.adjustment_reason"
                size="small"
                placeholder="请输入调整理由"
                @change="handleReasonChange($index)"
              />
            </template>
          </el-table-column>

          <el-table-column label="操作" align="center" width="120" fixed="right">
            <template #default="{ $index }">
              <el-button
                type="primary"
                size="small"
                link
                @click="openAddEquipmentDialog"
              >
                添加
              </el-button>
              <el-button
                type="danger"
                size="small"
                link
                @click="removeEquipment($index)"
              >
                删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>

        <div v-if="equipmentRequirements.length === 0" class="empty-equipment">
          <el-empty description="暂无器材需求">
            <el-button type="primary" @click="openAddEquipmentDialog">
              添加器材需求
            </el-button>
          </el-empty>
        </div>

        <!-- 器材需求统计 -->
        <div v-if="equipmentRequirements.length > 0" class="equipment-summary">
          <el-row :gutter="16">
            <el-col :span="6">
              <el-statistic title="总器材数" :value="equipmentRequirements.length" />
            </el-col>
            <el-col :span="6">
              <el-statistic title="必需器材" :value="requiredEquipmentCount" />
            </el-col>
            <el-col :span="6">
              <el-statistic title="缺少器材" :value="shortageEquipmentCount" />
            </el-col>
            <el-col :span="6">
              <el-statistic title="可预约" :value="canReserve ? '是' : '否'" />
            </el-col>
          </el-row>
        </div>
      </div>

      <!-- 冲突提醒 -->
      <div v-if="conflicts.length > 0" class="conflict-warnings">
        <h3>冲突提醒</h3>
        <el-alert
          v-for="(conflict, index) in (conflicts || [])"
          :key="index"
          :title="conflict.message"
          :type="getConflictType(conflict.type)"
          :description="getConflictDescription(conflict)"
          show-icon
          :closable="false"
          class="conflict-alert"
        />
      </div>

      <!-- 操作按钮 -->
      <div class="form-actions">
        <el-button @click="resetForm">重置</el-button>
        <el-button type="primary" @click="checkConflicts" :loading="checkingConflicts">
          检测冲突
        </el-button>
        <el-button
          type="success"
          @click="submitReservation"
          :loading="submitting"
          :disabled="hasBlockingConflicts"
        >
          提交预约
        </el-button>
      </div>
    </div>

    <!-- 添加器材对话框 -->
    <el-dialog
      v-model="showAddEquipmentDialog"
      title="添加器材"
      width="800px"
      :close-on-click-modal="false"
    >
      <div class="add-equipment-dialog">
        <!-- 搜索器材 -->
        <div class="equipment-search">
          <el-form :model="equipmentSearchForm" inline>
            <el-form-item label="器材名称">
              <el-input
                v-model="equipmentSearchForm.search"
                placeholder="请输入器材名称或编号"
                clearable
                @input="searchEquipments"
              />
            </el-form-item>
            <el-form-item label="器材分类">
              <el-select
                v-model="equipmentSearchForm.category_id"
                placeholder="请选择分类"
                clearable
                @change="searchEquipments"
              >
                <el-option
                  v-for="category in (equipmentCategories || [])"
                  :key="category.id"
                  :label="category.name"
                  :value="category.id"
                />
              </el-select>
            </el-form-item>
          </el-form>
        </div>

        <!-- 器材列表 -->
        <el-table
          :data="availableEquipments || []"
          border
          max-height="400"
          @selection-change="handleEquipmentSelection"
        >
          <el-table-column type="selection" width="55" />
          <el-table-column prop="name" label="器材名称" />
          <el-table-column prop="code" label="器材编号" />
          <el-table-column prop="model" label="型号规格" />
          <el-table-column prop="quantity" label="库存数量" align="center" />
          <el-table-column label="需要数量" align="center" width="120">
            <template #default="{ row }">
              <el-input-number
                v-model="row.required_quantity"
                :min="1"
                :max="row.quantity"
                size="small"
              />
            </template>
          </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div class="pagination-wrapper">
          <el-pagination
            v-model:current-page="equipmentPagination.current_page"
            v-model:page-size="equipmentPagination.per_page"
            :total="equipmentPagination.total"
            :page-sizes="[10, 20, 50]"
            layout="total, sizes, prev, pager, next, jumper"
            @size-change="searchEquipments"
            @current-change="searchEquipments"
          />
        </div>
      </div>

      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAddEquipmentDialog = false">取消</el-button>
          <el-button
            type="primary"
            @click="addSelectedEquipments"
            :disabled="selectedEquipments.length === 0"
          >
            添加选中器材 ({{ selectedEquipments.length }})
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Download, Operation } from '@element-plus/icons-vue'
import type { FormInstance, FormRules } from 'element-plus'
import { smartReservationApi } from '@/api/smartReservation'
import {
  experimentCatalogApi,
  getSubjectsApi,
  getTextbookVersionsApi,
  getTextbookVersionOptionsApi,
  getTextbookChaptersApi,
  type Subject,
  type TextbookVersion,
  type TextbookChapter
} from '@/api/experiment'
import { laboratoryApi } from '@/api/laboratory'
import { getEquipmentsApi, getEquipmentCategoriesApi } from '@/api/equipment'
import { equipmentRequirementApi } from '@/api/equipmentRequirement'
import ChapterSelector from '@/components/ChapterSelector.vue'

// 响应式数据
const reservationFormRef = ref<FormInstance>()
const experimentCatalogs = ref<any[]>([])
const laboratories = ref<any[]>([])
const selectedExperiment = ref<any>(null)
const equipmentRequirements = ref<any[]>([])
const conflicts = ref<any[]>([])
const checkingConflicts = ref(false)
const submitting = ref(false)

// 基础数据
const subjects = ref<Subject[]>([])
const textbookVersions = ref<TextbookVersion[]>([])
const chapters = ref<TextbookChapter[]>([])

// 实验目录筛选
const catalogFilters = reactive({
  subject_id: undefined as number | undefined,
  textbook_version_id: undefined as number | undefined,
  grade_level: undefined as string | undefined,
  volume: undefined as string | undefined,
  chapter_id: undefined as number | undefined,
  experiment_type: undefined as string | undefined
})

// 添加器材相关数据
const showAddEquipmentDialog = ref(false)
const availableEquipments = ref<any[]>([])
const equipmentCategories = ref<any[]>([])
const selectedEquipments = ref<any[]>([])

// 器材搜索表单
const equipmentSearchForm = reactive({
  search: '',
  category_id: null
})

// 器材分页数据
const equipmentPagination = reactive({
  current_page: 1,
  per_page: 10,
  total: 0
})

// 表单数据
const reservationForm = reactive({
  catalog_id: null,
  laboratory_id: null,
  reservation_date: '',
  start_time: '',
  end_time: '',
  class_name: '',
  student_count: 30,
  priority: 'normal',
  auto_borrow_equipment: true,
  preparation_notes: ''
})

// 表单验证规则
const reservationRules: FormRules = {
  catalog_id: [{ required: true, message: '请选择实验', trigger: 'change' }],
  laboratory_id: [{ required: true, message: '请选择实验室', trigger: 'change' }],
  reservation_date: [{ required: true, message: '请选择预约日期', trigger: 'change' }],
  start_time: [{ required: true, message: '请选择开始时间', trigger: 'change' }],
  end_time: [{ required: true, message: '请选择结束时间', trigger: 'change' }],
  class_name: [{ required: true, message: '请输入班级名称', trigger: 'blur' }],
  student_count: [{ required: true, message: '请输入学生人数', trigger: 'blur' }]
}

// 年级选项
const gradeOptions = [
  { label: '一年级', value: '1' },
  { label: '二年级', value: '2' },
  { label: '三年级', value: '3' },
  { label: '四年级', value: '4' },
  { label: '五年级', value: '5' },
  { label: '六年级', value: '6' },
  { label: '七年级', value: '7' },
  { label: '八年级', value: '8' },
  { label: '九年级', value: '9' },
  { label: '高一', value: '10' },
  { label: '高二', value: '11' },
  { label: '高三', value: '12' }
]

// 计算属性
const hasBlockingConflicts = computed(() => {
  return conflicts.value.some(conflict =>
    ['laboratory_time', 'teacher_time', 'capacity'].includes(conflict.type)
  )
})

const requiredEquipmentCount = computed(() => {
  return equipmentRequirements.value.filter(req => req.usage_type === 'required').length
})

const shortageEquipmentCount = computed(() => {
  return equipmentRequirements.value.filter(req => req.shortage > 0).length
})

const canReserve = computed(() => {
  const hasRequiredShortage = equipmentRequirements.value.some(req =>
    req.usage_type === 'required' && req.shortage > 0
  )
  return !hasBlockingConflicts.value && !hasRequiredShortage
})

// 方法
// 加载基础数据
const loadBaseData = async () => {
  try {
    const [subjectsRes, versionsRes] = await Promise.all([
      getSubjectsApi(),
      getTextbookVersionOptionsApi()
    ])

    // 处理学科数据
    if (subjectsRes.data) {
      if (subjectsRes.data.items && Array.isArray(subjectsRes.data.items)) {
        subjects.value = subjectsRes.data.items
      } else if (subjectsRes.data.data && Array.isArray(subjectsRes.data.data)) {
        subjects.value = subjectsRes.data.data
      } else if (Array.isArray(subjectsRes.data)) {
        subjects.value = subjectsRes.data
      } else {
        subjects.value = []
      }
    }

    // 处理教材版本数据
    if (versionsRes.data) {
      if (versionsRes.data.data && Array.isArray(versionsRes.data.data)) {
        textbookVersions.value = versionsRes.data.data
      } else if (Array.isArray(versionsRes.data)) {
        textbookVersions.value = versionsRes.data
      } else {
        textbookVersions.value = []
      }
    }


  } catch (error) {
    console.error('加载基础数据失败:', error)
    ElMessage.error('加载基础数据失败')
  }
}

// 筛选条件变化处理
const handleSubjectChange = () => {
  catalogFilters.textbook_version_id = undefined
  catalogFilters.chapter_id = undefined
  loadExperimentCatalogs()
}

const handleVersionChange = () => {
  catalogFilters.chapter_id = undefined
  loadExperimentCatalogs()
}

const handleGradeChange = () => {
  loadExperimentCatalogs()
}

const handleVolumeChange = () => {
  loadExperimentCatalogs()
}

const handleChapterChange = () => {
  loadExperimentCatalogs()
}

const loadExperimentCatalogs = async () => {
  try {
    // 构建参数，过滤掉undefined值
    const params: any = {
      per_page: 100
    }

    // 只添加有值的筛选条件
    Object.keys(catalogFilters).forEach(key => {
      const value = catalogFilters[key as keyof typeof catalogFilters]
      if (value !== undefined && value !== null && value !== '') {
        params[key] = value
      }
    })

    const response = await experimentCatalogApi.getList(params)

    // 处理分页数据结构
    let catalogs = []
    if (response.data && response.data.data) {
      if (Array.isArray(response.data.data.data)) {
        // 新格式：{code, message, data: {current_page, data: [...]}}
        catalogs = response.data.data.data
      } else if (Array.isArray(response.data.data)) {
        // 旧格式：{success, data: [...]}
        catalogs = response.data.data
      }
    } else if (Array.isArray(response.data)) {
      catalogs = response.data
    }

    experimentCatalogs.value = catalogs
  } catch (error) {
    console.error('加载实验目录失败:', error)
    ElMessage.error('加载实验目录失败')
  }
}

const loadLaboratories = async () => {
  try {
    const response = await laboratoryApi.getList({ per_page: 100 })
    // 处理分页数据结构
    laboratories.value = Array.isArray(response.data.data) ? response.data.data :
                         Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('加载实验室列表失败:', error)
    ElMessage.error('加载实验室列表失败')
  }
}

const handleExperimentChange = async (catalogId: number) => {
  if (!catalogId) {
    selectedExperiment.value = null
    equipmentRequirements.value = []
    return
  }

  // 获取实验详情
  const experiment = experimentCatalogs.value.find(item => item.id === catalogId)
  selectedExperiment.value = experiment

  // 根据实验和学生人数生成器材需求
  await generateEquipmentRequirements()
}

const handleLaboratoryChange = () => {
  checkConflicts()
}

const handleStudentCountChange = () => {
  generateEquipmentRequirements()
  checkConflicts()
}

const generateEquipmentRequirements = async () => {
  if (!reservationForm.catalog_id || !reservationForm.student_count) {
    equipmentRequirements.value = []
    return
  }

  try {
    // 调用API获取实验目录的器材需求配置
    const response = await equipmentRequirementApi.getRequirements(reservationForm.catalog_id, { active_only: true })
    const requirements = response.data.data || response.data || []

    if (requirements.length === 0) {
      ElMessage.warning('该实验目录暂未配置器材需求')
      equipmentRequirements.value = []
      return
    }

    // 根据学生人数和配置计算实际需求量
    equipmentRequirements.value = requirements.map(req => {
      let calculatedQuantity = req.required_quantity

      // 根据计算类型计算需求量
      if (req.calculation_type === 'per_group' && req.group_size) {
        // 按小组计算：学生人数 / 小组人数 * 每组需要数量
        const groupCount = Math.ceil(reservationForm.student_count / req.group_size)
        calculatedQuantity = groupCount * req.required_quantity
      } else if (req.calculation_type === 'per_student') {
        // 按学生计算：学生人数 * 每人需要数量
        calculatedQuantity = reservationForm.student_count * req.required_quantity
      }
      // 'fixed' 类型保持原数量不变

      // 确保不少于最小数量
      calculatedQuantity = Math.max(calculatedQuantity, req.min_quantity || 1)

      return {
        equipment_id: req.equipment_id,
        equipment_name: req.equipment?.name || '未知器材',
        equipment_code: req.equipment?.code || '',
        required_quantity: calculatedQuantity,
        available_quantity: req.equipment?.quantity || 0,
        shortage: Math.max(0, calculatedQuantity - (req.equipment?.quantity || 0)),
        is_required: req.is_required,
        usage_note: req.usage_note,
        safety_note: req.safety_note,
        calculation_type: req.calculation_type,
        group_size: req.group_size
      }
    })
  } catch (error) {
    console.error('获取器材需求配置失败:', error)
    ElMessage.error('获取器材需求配置失败')
    equipmentRequirements.value = []
  }
}

const checkConflicts = async () => {
  if (!reservationForm.laboratory_id || !reservationForm.reservation_date ||
      !reservationForm.start_time || !reservationForm.end_time) {
    return
  }

  checkingConflicts.value = true
  try {
    // 格式化日期和时间
    const formatDate = (date: any) => {
      if (!date) return ''
      if (typeof date === 'string') return date.split('T')[0] // 如果是ISO字符串，取日期部分
      if (date instanceof Date) return date.toISOString().split('T')[0]
      return date
    }

    const response = await smartReservationApi.checkConflicts({
      laboratory_id: reservationForm.laboratory_id,
      reservation_date: formatDate(reservationForm.reservation_date),
      start_time: reservationForm.start_time,
      end_time: reservationForm.end_time,
      student_count: reservationForm.student_count
    })

    conflicts.value = response.data.conflicts
  } catch (error) {
    ElMessage.error('冲突检测失败')
  } finally {
    checkingConflicts.value = false
  }
}

const submitReservation = async () => {
  if (!reservationFormRef.value) return

  const valid = await reservationFormRef.value.validate()
  if (!valid) return

  if (hasBlockingConflicts.value) {
    ElMessage.warning('存在阻塞性冲突，无法提交预约')
    return
  }

  submitting.value = true
  try {
    // 格式化日期和时间
    const formatDate = (date: any) => {
      if (!date) return ''
      if (typeof date === 'string') return date.split('T')[0] // 如果是ISO字符串，取日期部分
      if (date instanceof Date) return date.toISOString().split('T')[0]
      return date
    }

    const formData = {
      ...reservationForm,
      reservation_date: formatDate(reservationForm.reservation_date)
    }

    const response = await smartReservationApi.create(formData)

    ElMessage.success('预约提交成功')

    if (response.data.has_conflicts) {
      ElMessageBox.alert(
        '预约已提交，但检测到一些冲突，请注意处理',
        '提醒',
        { type: 'warning' }
      )
    }

    resetForm()
  } catch (error) {
    ElMessage.error('预约提交失败')
  } finally {
    submitting.value = false
  }
}

const resetForm = () => {
  reservationFormRef.value?.resetFields()
  selectedExperiment.value = null
  equipmentRequirements.value = []
  conflicts.value = []
}

// 器材管理相关方法
const handleQuantityChange = (index: number) => {
  const requirement = equipmentRequirements.value[index]
  if (requirement) {
    // 重新计算缺少数量
    requirement.shortage = Math.max(0, requirement.required_quantity - requirement.available_quantity)
    // 标记为手动调整
    requirement.is_auto_calculated = false
    // 如果数量与标准数量不同，标记为已调整
    if (requirement.standard_quantity && requirement.required_quantity !== requirement.standard_quantity) {
      requirement.is_adjusted = true
    }
  }
}

const handleUsageTypeChange = (index: number) => {
  // 使用类型变化时的处理逻辑
  const requirement = equipmentRequirements.value[index]
  if (requirement) {
    // 标记为手动调整
    requirement.is_auto_calculated = false
  }
}

const handleReasonChange = (index: number) => {
  // 调整理由变化时的处理逻辑
  const requirement = equipmentRequirements.value[index]
  if (requirement) {
    // 标记为手动调整
    requirement.is_auto_calculated = false
  }
}

const removeEquipment = (index: number) => {
  equipmentRequirements.value.splice(index, 1)
}

// 智能计算器材需求
const handleAutoCalculate = async () => {
  if (!selectedExperiment.value || !reservationForm.student_count) {
    ElMessage.warning('请先选择实验和输入学生人数')
    return
  }

  try {
    // 根据学生人数和实验类型智能计算器材需求
    equipmentRequirements.value.forEach(requirement => {
      if (requirement.standard_quantity) {
        // 根据学生人数计算实际需求
        const studentCount = reservationForm.student_count
        let calculatedQuantity = requirement.standard_quantity

        // 根据实验类型调整计算方式
        if (selectedExperiment.value.experiment_type === '分组') {
          // 分组实验：按组计算（假设每组4人）
          const groupCount = Math.ceil(studentCount / 4)
          calculatedQuantity = requirement.standard_quantity * groupCount
        } else if (selectedExperiment.value.experiment_type === '演示') {
          // 演示实验：固定数量
          calculatedQuantity = requirement.standard_quantity
        } else {
          // 其他类型：按人数比例计算
          calculatedQuantity = Math.ceil(requirement.standard_quantity * studentCount / 30)
        }

        requirement.required_quantity = calculatedQuantity
        requirement.shortage = Math.max(0, calculatedQuantity - requirement.available_quantity)
        requirement.is_auto_calculated = true
      }
    })

    ElMessage.success('智能计算完成')
  } catch (error) {
    console.error('智能计算失败:', error)
    ElMessage.error('智能计算失败')
  }
}

// 继承上级器材配置
const handleInheritEquipment = async () => {
  if (!selectedExperiment.value || !selectedExperiment.value.parent_catalog_id) {
    ElMessage.warning('当前实验目录没有上级配置可继承')
    return
  }

  try {
    await ElMessageBox.confirm(
      '确定要继承上级实验目录的器材配置吗？这将覆盖当前配置。',
      '确认继承',
      { type: 'warning' }
    )

    // 这里应该调用API获取上级配置
    ElMessage.success('继承配置成功')
    await loadEquipmentRequirements()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('继承配置失败:', error)
      ElMessage.error('继承配置失败')
    }
  }
}

const openAddEquipmentDialog = () => {
  showAddEquipmentDialog.value = true
  // 重置搜索条件
  equipmentSearchForm.search = ''
  equipmentSearchForm.category_id = null
  equipmentPagination.current_page = 1
  // 自动搜索器材
  searchEquipments()
}

const loadEquipmentCategories = async () => {
  try {
    const response = await getEquipmentCategoriesApi()
    equipmentCategories.value = response.data.data || response.data || []
  } catch (error) {
    console.error('加载器材分类失败:', error)
  }
}

const searchEquipments = async () => {
  try {
    const params = {
      page: equipmentPagination.current_page,
      per_page: equipmentPagination.per_page,
      search: equipmentSearchForm.search,
      category_id: equipmentSearchForm.category_id,
      status: 1 // 只显示正常状态的器材
    }

    const response = await getEquipmentsApi(params)
    console.log('设备API响应:', response.data) // 调试日志

    // 处理不同的响应数据结构
    let equipmentList = []
    let totalCount = 0

    if (response.data.data && response.data.data.items) {
      // 标准分页响应格式: { data: { items: [], pagination: { total: 0 } } }
      equipmentList = response.data.data.items
      totalCount = response.data.data.pagination?.total || 0
    } else if (response.data.data && Array.isArray(response.data.data)) {
      // 简单数组格式: { data: [] }
      equipmentList = response.data.data
      totalCount = response.data.total || equipmentList.length
    } else if (Array.isArray(response.data)) {
      // 直接数组格式: []
      equipmentList = response.data
      totalCount = equipmentList.length
    } else {
      console.warn('未知的设备API响应格式:', response.data)
      equipmentList = []
      totalCount = 0
    }

    // 为每个器材添加默认需要数量
    availableEquipments.value = equipmentList.map(equipment => ({
      ...equipment,
      required_quantity: 1
    }))

    // 更新分页信息
    equipmentPagination.total = totalCount

  } catch (error) {
    console.error('搜索器材失败:', error)
    ElMessage.error('搜索器材失败')
    availableEquipments.value = []
    equipmentPagination.total = 0
  }
}

const handleEquipmentSelection = (selection) => {
  selectedEquipments.value = selection
}

const addSelectedEquipments = () => {
  const addedCount = selectedEquipments.value.length

  selectedEquipments.value.forEach(equipment => {
    // 检查是否已经存在
    const exists = equipmentRequirements.value.find(req => req.equipment_id === equipment.id)
    if (!exists) {
      equipmentRequirements.value.push({
        equipment_id: equipment.id,
        equipment_name: equipment.name,
        equipment_code: equipment.code,
        required_quantity: equipment.required_quantity,
        available_quantity: equipment.quantity || 0,
        shortage: Math.max(0, equipment.required_quantity - (equipment.quantity || 0)),
        is_required: true
      })
    }
  })

  showAddEquipmentDialog.value = false
  selectedEquipments.value = []
  ElMessage.success(`已添加 ${addedCount} 个器材`)
}

const disabledDate = (time: Date) => {
  return time.getTime() < Date.now() - 8.64e7 // 不能选择今天之前的日期
}

const getExperimentTypeName = (type: number | string) => {
  if (typeof type === 'string') {
    return type
  }
  const types = { 1: '必做', 2: '选做', 3: '演示', 4: '分组' }
  return types[type] || '未知'
}

const getExperimentTypeColor = (type: number | string) => {
  if (typeof type === 'string') {
    const colors = { '必做': 'danger', '选做': 'warning', '演示': 'info', '分组': 'success' }
    return colors[type] || 'default'
  }
  const colors = { 1: 'danger', 2: 'warning', 3: 'info', 4: 'success' }
  return colors[type] || 'default'
}

const getConflictType = (type: string) => {
  const types = {
    'laboratory_time': 'error',
    'teacher_time': 'error',
    'capacity': 'error',
    'equipment_borrowed': 'warning'
  }
  return types[type] || 'warning'
}

const getConflictDescription = (conflict: any) => {
  // 根据冲突类型返回详细描述
  return JSON.stringify(conflict.details || conflict)
}

// 监控数据变化
watch(experimentCatalogs, (newVal, oldVal) => {
  // 数据变化监控（已移除调试信息）
}, { deep: true })

// 生命周期
onMounted(async () => {
  try {
    await loadBaseData()
    await loadExperimentCatalogs()
    await loadLaboratories()
    await loadEquipmentCategories()
  } catch (error) {
    console.error('初始化数据加载失败:', error)
  }
})
</script>

<style scoped>
.smart-reservation {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 24px;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 16px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  color: #303133;
}

.page-header p {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.reservation-form {
  margin-bottom: 24px;
}

.experiment-option,
.laboratory-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.capacity {
  font-size: 12px;
  color: #909399;
}

.experiment-info,
.equipment-requirements,
.conflict-warnings {
  margin: 24px 0;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 6px;
}

.experiment-info h3,
.equipment-requirements h3,
.conflict-warnings h3 {
  margin: 0 0 16px 0;
  color: #303133;
  font-size: 16px;
}

.conflict-alert {
  margin-bottom: 8px;
}

.form-actions {
  text-align: right;
  padding-top: 16px;
  border-top: 1px solid #ebeef5;
}

.form-actions .el-button {
  margin-left: 12px;
}

.equipment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.equipment-header h3 {
  margin: 0;
}

.empty-equipment {
  padding: 40px 0;
}

.add-equipment-dialog .equipment-search {
  margin-bottom: 16px;
  padding: 16px;
  background-color: #f5f7fa;
  border-radius: 4px;
}

.pagination-wrapper {
  margin-top: 16px;
  text-align: center;
}

/* 新增样式 */
.experiment-filters {
  margin-bottom: 24px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e4e7ed;
}

.experiment-filters h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.experiment-option {
  width: 100%;
}

.experiment-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.experiment-name {
  font-weight: 500;
  color: #303133;
}

.experiment-meta {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.equipment-actions {
  display: flex;
  gap: 8px;
}

.equipment-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.equipment-name {
  font-weight: 500;
}

.inherited-tag,
.auto-tag {
  font-size: 10px;
}

.standard-quantity {
  color: #909399;
  font-size: 12px;
}

.equipment-summary {
  margin-top: 16px;
  padding: 16px;
  background: white;
  border-radius: 6px;
  border: 1px solid #e4e7ed;
}

:deep(.el-table--small .el-table__cell) {
  padding: 6px 0;
}

:deep(.el-input-number--small) {
  width: 100px;
}

:deep(.el-select--small) {
  width: 100px;
}

.experiment-filters h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.experiment-option {
  width: 100%;
}

.experiment-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.experiment-name {
  font-weight: 500;
  color: #303133;
}

.experiment-meta {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.equipment-actions {
  display: flex;
  gap: 8px;
}

.equipment-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.equipment-name {
  font-weight: 500;
}

.inherited-tag,
.auto-tag {
  font-size: 10px;
}

.standard-quantity {
  color: #909399;
  font-size: 12px;
}

.equipment-summary {
  margin-top: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

:deep(.el-table--small .el-table__cell) {
  padding: 6px 0;
}

:deep(.el-input-number--small) {
  width: 100px;
}

:deep(.el-select--small) {
  width: 100px;
}
</style>
