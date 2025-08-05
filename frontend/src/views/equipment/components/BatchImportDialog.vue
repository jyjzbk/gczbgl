<template>
  <el-dialog
    v-model="visible"
    title="批量导入设备"
    width="700px"
    :before-close="handleClose"
  >
    <div class="import-content">
      <!-- 步骤指示器 -->
      <el-steps :active="currentStep" finish-status="success" align-center>
        <el-step title="下载模板" />
        <el-step title="上传文件" />
        <el-step title="数据预览" />
        <el-step title="导入完成" />
      </el-steps>
      
      <!-- 步骤1: 下载模板 -->
      <div v-if="currentStep === 0" class="step-content">
        <div class="template-download">
          <el-alert
            title="导入说明"
            type="info"
            :closable="false"
            show-icon
          >
            <template #default>
              <p>1. 请先下载导入模板，按照模板格式填写设备信息</p>
              <p>2. 支持 Excel (.xlsx) 格式文件</p>
              <p>3. 单次最多导入 1000 条设备记录</p>
              <p>4. 设备编号不能重复，必填字段不能为空</p>
            </template>
          </el-alert>
          
          <div class="download-section">
            <el-button 
              type="primary" 
              size="large"
              :loading="downloadingTemplate"
              @click="downloadTemplate"
            >
              <el-icon><Download /></el-icon>
              下载导入模板
            </el-button>
          </div>
        </div>
      </div>
      
      <!-- 步骤2: 上传文件 -->
      <div v-if="currentStep === 1" class="step-content">
        <div class="file-upload">
          <el-upload
            ref="uploadRef"
            :auto-upload="false"
            :show-file-list="true"
            :limit="1"
            accept=".xlsx,.xls"
            :on-change="handleFileChange"
            :on-remove="handleFileRemove"
            drag
          >
            <el-icon class="el-icon--upload"><UploadFilled /></el-icon>
            <div class="el-upload__text">
              将文件拖到此处，或<em>点击上传</em>
            </div>
            <template #tip>
              <div class="el-upload__tip">
                只能上传 xlsx/xls 文件，且不超过 10MB
              </div>
            </template>
          </el-upload>
        </div>
      </div>
      
      <!-- 步骤3: 数据预览 -->
      <div v-if="currentStep === 2" class="step-content">
        <div class="data-preview">
          <div class="preview-header">
            <span>共 {{ previewData.length }} 条记录</span>
            <span v-if="errorCount > 0" class="error-count">
              其中 {{ errorCount }} 条有错误
            </span>
          </div>
          
          <el-table
            :data="previewData.slice(0, 50)"
            border
            size="small"
            max-height="400"
          >
            <el-table-column type="index" label="#" width="50" />
            <el-table-column prop="name" label="设备名称" width="150" />
            <el-table-column prop="code" label="设备编号" width="120" />
            <el-table-column prop="model" label="型号" width="120" />
            <el-table-column prop="brand" label="品牌" width="100" />
            <el-table-column prop="location" label="位置" width="120" />
            <el-table-column label="状态" width="80">
              <template #default="{ row }">
                <el-tag v-if="!row.errors" type="success" size="small">
                  正常
                </el-tag>
                <el-tag v-else type="danger" size="small">
                  错误
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column label="错误信息" min-width="200">
              <template #default="{ row }">
                <div v-if="row.errors" class="error-messages">
                  <div v-for="error in row.errors" :key="error" class="error-item">
                    {{ error }}
                  </div>
                </div>
              </template>
            </el-table-column>
          </el-table>
          
          <div v-if="previewData.length > 50" class="preview-tip">
            只显示前 50 条记录，实际将导入 {{ previewData.length }} 条记录
          </div>
        </div>
      </div>
      
      <!-- 步骤4: 导入完成 -->
      <div v-if="currentStep === 3" class="step-content">
        <div class="import-result">
          <el-result
            :icon="importResult.success ? 'success' : 'error'"
            :title="importResult.success ? '导入成功' : '导入失败'"
            :sub-title="importResult.message"
          >
            <template #extra>
              <div v-if="importResult.success" class="result-stats">
                <p>成功导入: {{ importResult.successCount }} 条</p>
                <p v-if="importResult.failureCount > 0">
                  失败: {{ importResult.failureCount }} 条
                </p>
              </div>
              <div v-if="importResult.errors && importResult.errors.length > 0" class="error-list">
                <h4>错误详情:</h4>
                <ul>
                  <li v-for="error in importResult.errors" :key="error">
                    {{ error }}
                  </li>
                </ul>
              </div>
            </template>
          </el-result>
        </div>
      </div>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">
          {{ currentStep === 3 ? '关闭' : '取消' }}
        </el-button>
        <el-button 
          v-if="currentStep > 0 && currentStep < 3"
          @click="prevStep"
        >
          上一步
        </el-button>
        <el-button 
          v-if="currentStep < 2"
          type="primary"
          :disabled="!canNextStep"
          @click="nextStep"
        >
          下一步
        </el-button>
        <el-button 
          v-if="currentStep === 2"
          type="primary"
          :loading="importing"
          :disabled="errorCount > 0"
          @click="startImport"
        >
          {{ importing ? '导入中...' : '开始导入' }}
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Download, UploadFilled } from '@element-plus/icons-vue'
import { batchImportEquipmentsApi, getEquipmentCategoriesApi } from '@/api/equipment'
import { useAuthStore } from '@/stores/auth'
import * as XLSX from 'xlsx'

interface Props {
  modelValue: boolean
  importType: string
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'success'): void
}

interface ImportData {
  name: string
  code: string
  model: string
  brand: string
  supplier: string
  supplier_phone: string
  category_name: string
  storage_location: string
  purchase_date: string
  purchase_price: number
  quantity: number
  unit: string
  warranty_period: number
  service_life: number
  funding_source: string
  status: number
  remark?: string
  errors?: string[]
}

interface ImportResult {
  success: boolean
  message: string
  successCount: number
  failureCount: number
  errors?: string[]
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Store
const authStore = useAuthStore()

// 响应式数据
const currentStep = ref(0)
const uploadRef = ref()
const downloadingTemplate = ref(false)
const importing = ref(false)
const selectedFile = ref<File | null>(null)
const previewData = ref<ImportData[]>([])
const categories = ref<any[]>([])
const importResult = ref<ImportResult>({
  success: false,
  message: '',
  successCount: 0,
  failureCount: 0
})

// 计算属性
const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const canNextStep = computed(() => {
  if (currentStep.value === 0) return true
  if (currentStep.value === 1) return selectedFile.value !== null
  return false
})

const errorCount = computed(() => {
  return previewData.value.filter(item => item.errors && item.errors.length > 0).length
})

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    resetDialog()
  }
})

// 重置对话框
const resetDialog = () => {
  currentStep.value = 0
  selectedFile.value = null
  previewData.value = []
  importResult.value = {
    success: false,
    message: '',
    successCount: 0,
    failureCount: 0
  }
  uploadRef.value?.clearFiles()
}

// 下载模板
const downloadTemplate = async () => {
  downloadingTemplate.value = true
  try {
    // 创建模板数据 - 匹配您现有的Excel格式，添加数量和单位列
    const templateData = [
      {
        '设备编号': 'Model-001',
        '设备名称': '示例设备1',
        '设备型号': 'Model-001',
        '设备品牌': '示例品牌1',
        '序列号': 'SN001',
        '设备分类': '实验仪器',
        '存储位置': '实验室1',
        '采购日期': '2024-01-01',
        '采购价格': 10000,
        '数量': 1,
        '单位': '台',
        '供应商': '示例供应商1',
        '保修期(月)': 12,
        '设备状态': 1,
        '设备状况': 1,
        '设备描述': '设备描述信息1',
        '技术规格': '技术规格参数1'
      },
      {
        '设备编号': 'Model-002',
        '设备名称': '示例设备2',
        '设备型号': 'Model-002',
        '设备品牌': '示例品牌2',
        '序列号': 'SN002',
        '设备分类': '实验仪器',
        '存储位置': '实验室2',
        '采购日期': '2024-01-02',
        '采购价格': 15000,
        '数量': 2,
        '单位': '套',
        '供应商': '示例供应商2',
        '保修期(月)': 12,
        '设备状态': 1,
        '设备状况': 1,
        '设备描述': '设备描述信息2',
        '技术规格': '技术规格参数2'
      }
    ]
    
    // 创建工作簿
    const wb = XLSX.utils.book_new()
    const ws = XLSX.utils.json_to_sheet(templateData)
    
    // 设置列宽（增加了设备型号、设备品牌、数量和单位列）
    ws['!cols'] = [
      { wch: 15 }, { wch: 12 }, { wch: 12 }, { wch: 12 },
      { wch: 15 }, { wch: 12 }, { wch: 15 }, { wch: 12 },
      { wch: 15 }, { wch: 8 }, { wch: 8 }, { wch: 12 },
      { wch: 10 }, { wch: 8 }, { wch: 8 }, { wch: 20 }, { wch: 20 }
    ]
    
    XLSX.utils.book_append_sheet(wb, ws, '设备导入模板')
    
    // 下载文件
    XLSX.writeFile(wb, '设备导入模板.xlsx')
    
    ElMessage.success('模板下载成功')
  } catch (error) {
    console.error('下载模板失败:', error)
    ElMessage.error('下载模板失败')
  } finally {
    downloadingTemplate.value = false
  }
}

// 文件选择变化
const handleFileChange = (file: any) => {
  selectedFile.value = file.raw
}

// 移除文件
const handleFileRemove = () => {
  selectedFile.value = null
}

// 下一步
const nextStep = () => {
  if (currentStep.value === 1 && selectedFile.value) {
    parseExcelFile()
  } else {
    currentStep.value++
  }
}

// 上一步
const prevStep = () => {
  currentStep.value--
}

// 解析Excel文件
const parseExcelFile = async () => {
  if (!selectedFile.value) return

  try {
    const data = await readExcelFile(selectedFile.value)
    console.log('Excel原始数据:', data)
    console.log('第一行数据的键名:', data.length > 0 ? Object.keys(data[0]) : [])
    previewData.value = validateData(data)
    currentStep.value = 2
  } catch (error) {
    console.error('解析文件失败:', error)
    ElMessage.error('解析文件失败，请检查文件格式')
  }
}

// 读取Excel文件
const readExcelFile = (file: File): Promise<any[]> => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      try {
        const data = new Uint8Array(e.target?.result as ArrayBuffer)
        const workbook = XLSX.read(data, { type: 'array' })
        const sheetName = workbook.SheetNames[0]
        const worksheet = workbook.Sheets[sheetName]
        const jsonData = XLSX.utils.sheet_to_json(worksheet)
        resolve(jsonData)
      } catch (error) {
        reject(error)
      }
    }
    reader.onerror = reject
    reader.readAsArrayBuffer(file)
  })
}

// 验证数据
const validateData = (data: any[]): ImportData[] => {
  return data.map((row, index) => {
    const errors: string[] = []

    // 调试信息：打印每行的键名
    if (index === 0) {
      console.log('Excel行数据键名:', Object.keys(row))
      console.log('Excel第一行数据:', row)

    // 调试：检查每个字段的值
    console.log('字段值检查:', {
      deviceName: row['设备名称'],
      deviceCode: row['设备编号'],
      deviceCategory: row['设备分类'],
      serialNumber: row['序列号'],
      storageLocation: row['存储位置']
    })
    }

    // 使用实际的Excel列名（支持带星号和不带星号）
    const deviceName = row['设备名称'] || row['设备名称*'] || ''
    const deviceCategory = row['设备分类'] || row['设备分类*'] || ''
    const deviceCode = row['设备编号'] || row['设备编号*'] || ''
    const deviceModel = row['设备型号'] || row['设备型号*'] || ''
    const deviceBrand = row['设备品牌'] || row['设备品牌*'] || ''
    const serialNumber = row['序列号'] || row['序列号*'] || ''
    const supplier = row['供应商'] || row['供应商*'] || ''
    const storageLocation = row['存储位置'] || row['存放位置'] || row['存放位置*'] || ''
    const purchaseDate = row['采购日期'] || row['采购日期*'] || ''
    const purchasePrice = row['采购价格'] || row['采购价格*'] || 0
    const quantity = row['数量'] || row['数量*'] || 1  // 支持带星号和不带星号的列名
    const unit = row['单位'] || row['单位*'] || '台'   // 支持带星号和不带星号的列名
    const warrantyPeriod = row['保修期(月)'] || row['保修期(月)*'] || 0
    const deviceStatus = row['设备状态'] || row['设备状态*'] || 1
    const deviceCondition = row['设备状况'] || row['设备状况*'] || 1
    const deviceDescription = row['设备描述'] || ''
    const technicalSpecs = row['技术规格'] || ''

    console.log('提取的字段值:', {
      deviceName, deviceCode, deviceCategory, serialNumber,
      storageLocation, purchaseDate, purchasePrice, quantity, unit
    })

    // 必填字段验证
    if (!deviceName) errors.push('设备名称不能为空')
    if (!deviceCategory) errors.push('设备分类不能为空')

    // 数据格式验证
    if (purchasePrice && isNaN(Number(purchasePrice))) {
      errors.push('采购价格必须是数字')
    }
    if (quantity && isNaN(Number(quantity))) {
      errors.push('数量必须是数字')
    }
    if (quantity && Number(quantity) <= 0) {
      errors.push('数量必须大于0')
    }
    if (warrantyPeriod && isNaN(Number(warrantyPeriod))) {
      errors.push('保修期必须是数字')
    }
    if (deviceStatus && ![1, 2, 3, 4].includes(Number(deviceStatus))) {
      errors.push('设备状态必须是1-4之间的数字')
    }

    const result = {
      name: deviceName,
      code: deviceCode,
      model: deviceModel, // 使用设备型号
      brand: deviceBrand,
      supplier: supplier,
      supplier_phone: '', // Excel中没有供应商电话字段
      category_name: deviceCategory,
      storage_location: storageLocation,
      purchase_date: purchaseDate,
      purchase_price: Number(purchasePrice) || 0,
      quantity: Number(quantity) || 1, // 从Excel读取数量，默认为1
      unit: unit || '台', // 从Excel读取单位，默认为台
      warranty_period: Number(warrantyPeriod) || 0,
      service_life: 0, // Excel中没有使用年限字段
      funding_source: '', // Excel中没有资金来源字段
      status: Number(deviceStatus) || 1,
      remark: deviceDescription,
      errors: errors.length > 0 ? errors : undefined
    }

    // 调试信息：打印转换结果
    if (index === 0) {
      console.log('转换后的数据:', result)
    }

    return result
  })
}

// 开始导入
const startImport = async () => {
  if (errorCount.value > 0) {
    ElMessage.error('请先修复数据错误')
    return
  }

  importing.value = true
  try {
    const validData = previewData.value.filter(item => !item.errors)

    // 转换数据格式，添加必需字段
    const equipmentsData = validData.map(item => {
      // 根据分类名称查找分类ID
      const category = categories.value.find(cat => cat.name === item.category_name)
      const categoryId = category ? category.id : 1 // 默认使用第一个分类

      return {
        school_id: authStore.userInfo?.school_id || 1,
        category_id: categoryId,
        name: item.name,
        code: item.code || undefined,
        model: item.model || undefined,
        brand: item.brand || undefined,
        supplier: item.supplier || undefined,
        supplier_phone: item.supplier_phone || undefined,
        purchase_date: item.purchase_date || undefined,
        purchase_price: item.purchase_price || undefined,
        quantity: item.quantity,
        unit: item.unit,
        warranty_period: item.warranty_period || undefined,
        service_life: item.service_life || undefined,
        funding_source: item.funding_source || undefined,
        storage_location: item.storage_location || undefined,
        status: item.status,
        remark: item.remark || undefined
      }
    })

    const response = await batchImportEquipmentsApi({ equipments: equipmentsData })
    
    importResult.value = {
      success: true,
      message: '设备导入完成',
      successCount: response.data.success_count,
      failureCount: response.data.failure_count,
      errors: response.data.errors
    }
    
    currentStep.value = 3
    emit('success')
  } catch (error: any) {
    console.error('导入失败:', error)
    importResult.value = {
      success: false,
      message: error.message || '导入失败',
      successCount: 0,
      failureCount: previewData.value.length,
      errors: [error.message || '导入失败']
    }
    currentStep.value = 3
  } finally {
    importing.value = false
  }
}

// 加载设备分类
const loadCategories = async () => {
  try {
    const response = await getEquipmentCategoriesApi({ all: true, status: 1 })
    // 处理不同的响应格式
    if (Array.isArray(response.data)) {
      categories.value = response.data
    } else if (response.data.data) {
      categories.value = Array.isArray(response.data.data) ? response.data.data : []
    } else {
      categories.value = []
    }
  } catch (error) {
    console.error('加载设备分类失败:', error)
    // 临时解决方案：提供默认的设备分类
    categories.value = [
      { id: 1, name: '实验仪器', code: 'SYQX' },
      { id: 2, name: '教学设备', code: 'JXSB' },
      { id: 3, name: '办公设备', code: 'BGSB' },
      { id: 4, name: '其他设备', code: 'QTSB' }
    ]
    ElMessage.warning('设备分类加载失败，使用默认分类')
  }
}

// 关闭对话框
const handleClose = () => {
  emit('update:modelValue', false)
}

// 监听对话框显示状态
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    loadCategories()
  }
})
</script>

<style scoped>
.import-content {
  min-height: 400px;
}

.step-content {
  margin-top: 24px;
  min-height: 300px;
}

.template-download {
  text-align: center;
}

.download-section {
  margin-top: 24px;
}

.file-upload {
  padding: 20px 0;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding: 12px;
  background: #f5f7fa;
  border-radius: 6px;
}

.error-count {
  color: #f56c6c;
  font-weight: 600;
}

.preview-tip {
  margin-top: 12px;
  text-align: center;
  color: #909399;
  font-size: 12px;
}

.error-messages {
  font-size: 12px;
}

.error-item {
  color: #f56c6c;
  margin-bottom: 2px;
}

.import-result {
  text-align: center;
}

.result-stats {
  margin: 16px 0;
}

.result-stats p {
  margin: 8px 0;
  font-size: 16px;
}

.error-list {
  margin-top: 16px;
  text-align: left;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.error-list h4 {
  margin-bottom: 8px;
  color: #f56c6c;
}

.error-list ul {
  margin: 0;
  padding-left: 20px;
}

.error-list li {
  margin-bottom: 4px;
  color: #f56c6c;
}

.dialog-footer {
  text-align: right;
}
</style>
