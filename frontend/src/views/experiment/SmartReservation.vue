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
                >
                  <div class="experiment-option">
                    <span>{{ catalog.name }}</span>
                    <el-tag size="small" :type="getExperimentTypeColor(catalog.type)">
                      {{ getExperimentTypeName(catalog.type) }}
                    </el-tag>
                  </div>
                </el-option>
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
                  v-for="lab in laboratories"
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

      <!-- 器材清单 -->
      <div v-if="equipmentRequirements.length > 0" class="equipment-requirements">
        <h3>所需器材清单</h3>
        <el-table :data="equipmentRequirements" border>
          <el-table-column prop="equipment_name" label="器材名称" />
          <el-table-column prop="equipment_code" label="器材编号" />
          <el-table-column prop="required_quantity" label="需要数量" align="center" />
          <el-table-column prop="available_quantity" label="可用数量" align="center" />
          <el-table-column label="状态" align="center">
            <template #default="{ row }">
              <el-tag v-if="row.shortage === 0" type="success">充足</el-tag>
              <el-tag v-else type="danger">缺少{{ row.shortage }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="必需" align="center">
            <template #default="{ row }">
              <el-tag v-if="row.is_required" type="warning" size="small">必需</el-tag>
              <el-tag v-else type="info" size="small">可选</el-tag>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!-- 冲突提醒 -->
      <div v-if="conflicts.length > 0" class="conflict-warnings">
        <h3>冲突提醒</h3>
        <el-alert
          v-for="(conflict, index) in conflicts"
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import type { FormInstance, FormRules } from 'element-plus'
import { smartReservationApi } from '@/api/smartReservation'
import { experimentCatalogApi } from '@/api/experiment'
import { laboratoryApi } from '@/api/laboratory'

// 响应式数据
const reservationFormRef = ref<FormInstance>()
const experimentCatalogs = ref([])
const laboratories = ref([])
const selectedExperiment = ref(null)
const equipmentRequirements = ref([])
const conflicts = ref([])
const checkingConflicts = ref(false)
const submitting = ref(false)

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

// 计算属性
const hasBlockingConflicts = computed(() => {
  return conflicts.value.some(conflict => 
    ['laboratory_time', 'teacher_time', 'capacity'].includes(conflict.type)
  )
})

// 方法
const loadExperimentCatalogs = async () => {
  try {
    const response = await experimentCatalogApi.getList({ per_page: 100 })
    experimentCatalogs.value = response.data
  } catch (error) {
    ElMessage.error('加载实验目录失败')
  }
}

const loadLaboratories = async () => {
  try {
    const response = await laboratoryApi.getList({ per_page: 100 })
    laboratories.value = response.data
  } catch (error) {
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
    return
  }

  try {
    // 这里应该调用API获取器材需求，暂时使用模拟数据
    equipmentRequirements.value = [
      {
        equipment_id: 1,
        equipment_name: '刻度尺',
        equipment_code: 'EQ001',
        required_quantity: Math.ceil(reservationForm.student_count / 2),
        available_quantity: 20,
        shortage: Math.max(0, Math.ceil(reservationForm.student_count / 2) - 20),
        is_required: true
      }
    ]
  } catch (error) {
    ElMessage.error('生成器材清单失败')
  }
}

const checkConflicts = async () => {
  if (!reservationForm.laboratory_id || !reservationForm.reservation_date || 
      !reservationForm.start_time || !reservationForm.end_time) {
    return
  }

  checkingConflicts.value = true
  try {
    const response = await smartReservationApi.checkConflicts({
      laboratory_id: reservationForm.laboratory_id,
      reservation_date: reservationForm.reservation_date,
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
    const response = await smartReservationApi.create(reservationForm)
    
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

const disabledDate = (time: Date) => {
  return time.getTime() < Date.now() - 8.64e7 // 不能选择今天之前的日期
}

const getExperimentTypeName = (type: number) => {
  const types = { 1: '必做', 2: '选做', 3: '演示', 4: '分组' }
  return types[type] || '未知'
}

const getExperimentTypeColor = (type: number) => {
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

// 生命周期
onMounted(() => {
  loadExperimentCatalogs()
  loadLaboratories()
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
</style>
