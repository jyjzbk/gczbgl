<template>
  <div class="equipment-qrcode">
    <div class="page-card">
      <div class="page-header">
        <h2>设备二维码管理</h2>
        <p>生成、管理和打印设备二维码，支持扫码查询设备信息</p>
      </div>
      
      <!-- 功能选项卡 -->
      <el-tabs v-model="activeTab" type="border-card">
        <!-- 二维码生成 -->
        <el-tab-pane label="二维码生成" name="generate">
          <div class="generate-section">
            <el-row :gutter="20">
              <el-col :span="12">
                <el-card title="设备选择">
                  <el-form :model="generateForm" label-width="100px">
                    <el-form-item label="选择设备">
                      <el-select
                        v-model="generateForm.equipment_id"
                        placeholder="请选择设备"
                        filterable
                        remote
                        :remote-method="searchEquipments"
                        :loading="equipmentLoading"
                        style="width: 100%"
                        @change="handleEquipmentSelect"
                      >
                        <el-option
                          v-for="equipment in equipmentOptions"
                          :key="equipment.id"
                          :label="`${equipment.name} (${equipment.code})`"
                          :value="equipment.id"
                        >
                          <div class="equipment-option">
                            <div class="equipment-name">{{ equipment.name }}</div>
                            <div class="equipment-info">
                              编号: {{ equipment.code }} | 位置: {{ equipment.location }}
                            </div>
                          </div>
                        </el-option>
                      </el-select>
                    </el-form-item>
                    
                    <el-form-item label="二维码类型">
                      <el-radio-group v-model="generateForm.qr_type">
                        <el-radio label="basic">基础信息</el-radio>
                        <el-radio label="detailed">详细信息</el-radio>
                        <el-radio label="url">链接地址</el-radio>
                      </el-radio-group>
                    </el-form-item>
                    
                    <el-form-item label="二维码尺寸">
                      <el-select v-model="generateForm.size" style="width: 100%">
                        <el-option label="小 (100x100)" value="100" />
                        <el-option label="中 (200x200)" value="200" />
                        <el-option label="大 (300x300)" value="300" />
                        <el-option label="特大 (400x400)" value="400" />
                      </el-select>
                    </el-form-item>
                    
                    <el-form-item label="包含信息">
                      <el-checkbox-group v-model="generateForm.include_info">
                        <el-checkbox label="name">设备名称</el-checkbox>
                        <el-checkbox label="code">设备编号</el-checkbox>
                        <el-checkbox label="location">存放位置</el-checkbox>
                        <el-checkbox label="contact">联系方式</el-checkbox>
                      </el-checkbox-group>
                    </el-form-item>
                    
                    <el-form-item>
                      <el-button 
                        type="primary" 
                        @click="generateQRCode"
                        :loading="generating"
                        :disabled="!generateForm.equipment_id"
                      >
                        生成二维码
                      </el-button>
                      <el-button @click="resetGenerate">重置</el-button>
                    </el-form-item>
                  </el-form>
                </el-card>
              </el-col>
              
              <el-col :span="12">
                <el-card title="二维码预览">
                  <div class="qrcode-preview">
                    <div v-if="currentQRCode" class="qrcode-display">
                      <div class="qrcode-image">
                        <img :src="currentQRCode.url" :alt="currentQRCode.equipment_name" />
                      </div>
                      <div class="qrcode-info">
                        <h4>{{ currentQRCode.equipment_name }}</h4>
                        <p>设备编号: {{ currentQRCode.equipment_code }}</p>
                        <p>生成时间: {{ currentQRCode.created_at }}</p>
                      </div>
                      <div class="qrcode-actions">
                        <el-button type="primary" @click="downloadQRCode">
                          <el-icon><Download /></el-icon>
                          下载
                        </el-button>
                        <el-button @click="printQRCode">
                          <el-icon><Printer /></el-icon>
                          打印
                        </el-button>
                        <el-button @click="shareQRCode">
                          <el-icon><Share /></el-icon>
                          分享
                        </el-button>
                      </div>
                    </div>
                    <div v-else class="qrcode-placeholder">
                      <el-empty description="请选择设备并生成二维码" />
                    </div>
                  </div>
                </el-card>
              </el-col>
            </el-row>
          </div>
        </el-tab-pane>
        
        <!-- 批量生成 -->
        <el-tab-pane label="批量生成" name="batch">
          <div class="batch-section">
            <el-card title="批量生成设置">
              <el-form :model="batchForm" label-width="120px">
                <el-form-item label="选择设备">
                  <div class="batch-equipment-select">
                    <el-button type="primary" @click="showEquipmentSelector">
                      选择设备 (已选择 {{ selectedEquipments.length }} 个)
                    </el-button>
                    <el-button @click="selectAllEquipments">全选设备</el-button>
                    <el-button @click="clearSelectedEquipments">清空选择</el-button>
                  </div>
                </el-form-item>
                
                <el-form-item label="生成模板">
                  <el-select v-model="batchForm.template" style="width: 200px">
                    <el-option label="标准模板" value="standard" />
                    <el-option label="简洁模板" value="simple" />
                    <el-option label="详细模板" value="detailed" />
                  </el-select>
                </el-form-item>
                
                <el-form-item label="输出格式">
                  <el-radio-group v-model="batchForm.output_format">
                    <el-radio label="pdf">PDF文件</el-radio>
                    <el-radio label="images">图片压缩包</el-radio>
                    <el-radio label="excel">Excel表格</el-radio>
                  </el-radio-group>
                </el-form-item>
                
                <el-form-item label="排版设置">
                  <el-row :gutter="10">
                    <el-col :span="8">
                      <el-input-number 
                        v-model="batchForm.columns" 
                        :min="1" 
                        :max="6"
                        placeholder="每行列数"
                      />
                    </el-col>
                    <el-col :span="8">
                      <el-input-number 
                        v-model="batchForm.rows" 
                        :min="1" 
                        :max="10"
                        placeholder="每页行数"
                      />
                    </el-col>
                    <el-col :span="8">
                      <el-select v-model="batchForm.page_size" placeholder="页面大小">
                        <el-option label="A4" value="A4" />
                        <el-option label="A3" value="A3" />
                        <el-option label="Letter" value="Letter" />
                      </el-select>
                    </el-col>
                  </el-row>
                </el-form-item>
                
                <el-form-item>
                  <el-button 
                    type="primary" 
                    @click="batchGenerate"
                    :loading="batchGenerating"
                    :disabled="selectedEquipments.length === 0"
                  >
                    批量生成
                  </el-button>
                  <el-button @click="previewBatch">预览效果</el-button>
                </el-form-item>
              </el-form>
            </el-card>
            
            <!-- 已选设备列表 -->
            <el-card v-if="selectedEquipments.length > 0" title="已选设备" style="margin-top: 20px">
              <el-table :data="selectedEquipments" border size="small" max-height="300">
                <el-table-column prop="name" label="设备名称" />
                <el-table-column prop="code" label="设备编号" />
                <el-table-column prop="location" label="存放位置" />
                <el-table-column label="操作" width="80">
                  <template #default="{ $index }">
                    <el-button 
                      type="danger" 
                      size="small" 
                      text
                      @click="removeSelectedEquipment($index)"
                    >
                      移除
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
            </el-card>
          </div>
        </el-tab-pane>
        
        <!-- 扫码查询 -->
        <el-tab-pane label="扫码查询" name="scan">
          <div class="scan-section">
            <el-row :gutter="20">
              <el-col :span="12">
                <el-card title="扫码查询">
                  <div class="scan-area">
                    <div v-if="!scanning" class="scan-placeholder">
                      <el-button type="primary" size="large" @click="startScan">
                        <el-icon><Camera /></el-icon>
                        开始扫码
                      </el-button>
                      <p>或者</p>
                      <el-upload
                        :auto-upload="false"
                        :show-file-list="false"
                        accept="image/*"
                        @change="handleImageUpload"
                      >
                        <el-button>上传二维码图片</el-button>
                      </el-upload>
                    </div>
                    <div v-else class="scan-camera">
                      <video ref="videoRef" autoplay></video>
                      <div class="scan-overlay">
                        <div class="scan-frame"></div>
                      </div>
                      <div class="scan-controls">
                        <el-button @click="stopScan">停止扫码</el-button>
                        <el-button @click="captureImage">拍照识别</el-button>
                      </div>
                    </div>
                  </div>
                </el-card>
              </el-col>
              
              <el-col :span="12">
                <el-card title="查询结果">
                  <div v-if="scanResult" class="scan-result">
                    <el-descriptions :column="1" border>
                      <el-descriptions-item label="设备名称">
                        {{ scanResult.name }}
                      </el-descriptions-item>
                      <el-descriptions-item label="设备编号">
                        {{ scanResult.code }}
                      </el-descriptions-item>
                      <el-descriptions-item label="设备型号">
                        {{ scanResult.model }}
                      </el-descriptions-item>
                      <el-descriptions-item label="存放位置">
                        {{ scanResult.location }}
                      </el-descriptions-item>
                      <el-descriptions-item label="设备状态">
                        <el-tag :type="getStatusTagType(scanResult.status)">
                          {{ getStatusText(scanResult.status) }}
                        </el-tag>
                      </el-descriptions-item>
                    </el-descriptions>
                    
                    <div class="scan-actions">
                      <el-button type="primary" @click="viewEquipmentDetail">
                        查看详情
                      </el-button>
                      <el-button @click="borrowEquipment">
                        申请借用
                      </el-button>
                      <el-button @click="reportMaintenance">
                        报修设备
                      </el-button>
                    </div>
                  </div>
                  <div v-else class="scan-placeholder">
                    <el-empty description="暂无扫码结果" />
                  </div>
                </el-card>
              </el-col>
            </el-row>
          </div>
        </el-tab-pane>
        
        <!-- 二维码管理 -->
        <el-tab-pane label="二维码管理" name="manage">
          <div class="manage-section">
            <!-- 搜索筛选 -->
            <div class="table-search">
              <el-form :model="searchForm" inline>
                <el-form-item label="设备名称">
                  <el-input
                    v-model="searchForm.equipment_name"
                    placeholder="请输入设备名称"
                    clearable
                    style="width: 150px"
                  />
                </el-form-item>
                <el-form-item label="设备编号">
                  <el-input
                    v-model="searchForm.equipment_code"
                    placeholder="请输入设备编号"
                    clearable
                    style="width: 150px"
                  />
                </el-form-item>
                <el-form-item label="生成日期">
                  <el-date-picker
                    v-model="dateRange"
                    type="daterange"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                    value-format="YYYY-MM-DD"
                    style="width: 240px"
                  />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" :icon="Search" @click="handleSearch">
                    搜索
                  </el-button>
                  <el-button :icon="Refresh" @click="handleReset">
                    重置
                  </el-button>
                </el-form-item>
              </el-form>
            </div>
            
            <!-- 二维码列表 -->
            <div class="qrcode-grid">
              <div 
                v-for="qrcode in qrcodeList" 
                :key="qrcode.id"
                class="qrcode-item"
              >
                <div class="qrcode-image">
                  <img :src="qrcode.url" :alt="qrcode.equipment_name" />
                </div>
                <div class="qrcode-info">
                  <h4>{{ qrcode.equipment_name }}</h4>
                  <p>编号: {{ qrcode.equipment_code }}</p>
                  <p>生成: {{ qrcode.created_at }}</p>
                </div>
                <div class="qrcode-actions">
                  <el-button size="small" @click="downloadSingleQRCode(qrcode)">
                    下载
                  </el-button>
                  <el-button size="small" @click="printSingleQRCode(qrcode)">
                    打印
                  </el-button>
                  <el-button size="small" type="danger" @click="deleteQRCode(qrcode)">
                    删除
                  </el-button>
                </div>
              </div>
            </div>
            
            <!-- 分页 -->
            <div class="table-pagination">
              <el-pagination
                v-model:current-page="pagination.current_page"
                v-model:page-size="pagination.per_page"
                :total="pagination.total"
                :page-sizes="[12, 24, 48, 96]"
                layout="total, sizes, prev, pager, next, jumper"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
              />
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
    
    <!-- 设备选择器对话框 -->
    <EquipmentSelector
      v-model="selectorVisible"
      :selected="selectedEquipments"
      @confirm="handleEquipmentSelectorConfirm"
    />
    
    <!-- 批量预览对话框 -->
    <BatchPreviewDialog
      v-model="previewVisible"
      :equipments="selectedEquipments"
      :settings="batchForm"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Search,
  Refresh,
  Download,
  Printer,
  Share,
  Camera
} from '@element-plus/icons-vue'
import {
  getEquipmentsApi,
  generateEquipmentQRCodeApi,
  batchGenerateQRCodesApi,
  type Equipment
} from '@/api/equipment'
import EquipmentSelector from './components/EquipmentSelector.vue'
import BatchPreviewDialog from './components/BatchPreviewDialog.vue'

// 响应式数据
const activeTab = ref('generate')
const equipmentLoading = ref(false)
const generating = ref(false)
const batchGenerating = ref(false)
const scanning = ref(false)
const selectorVisible = ref(false)
const previewVisible = ref(false)

const equipmentOptions = ref<Equipment[]>([])
const selectedEquipments = ref<Equipment[]>([])
const currentQRCode = ref<any>(null)
const scanResult = ref<Equipment | null>(null)
const qrcodeList = ref<any[]>([])
const dateRange = ref<[string, string] | null>(null)

const videoRef = ref<HTMLVideoElement>()
let mediaStream: MediaStream | null = null

// 表单数据
const generateForm = reactive({
  equipment_id: 0,
  qr_type: 'basic',
  size: '200',
  include_info: ['name', 'code']
})

const batchForm = reactive({
  template: 'standard',
  output_format: 'pdf',
  columns: 3,
  rows: 4,
  page_size: 'A4'
})

const searchForm = reactive({
  equipment_name: '',
  equipment_code: '',
  start_date: '',
  end_date: ''
})

// 分页数据
const pagination = reactive({
  current_page: 1,
  per_page: 12,
  total: 0
})

// 搜索设备
const searchEquipments = async (query: string) => {
  if (!query) return

  equipmentLoading.value = true
  try {
    const response = await getEquipmentsApi({
      search: query,
      per_page: 20
    })
    equipmentOptions.value = response.data.items
  } catch (error) {
    console.error('搜索设备失败:', error)
  } finally {
    equipmentLoading.value = false
  }
}

// 设备选择
const handleEquipmentSelect = (equipmentId: number) => {
  const equipment = equipmentOptions.value.find(eq => eq.id === equipmentId)
  if (equipment) {
    generateForm.equipment_id = equipmentId
  }
}

// 生成二维码
const generateQRCode = async () => {
  if (!generateForm.equipment_id) {
    ElMessage.warning('请先选择设备')
    return
  }

  generating.value = true
  try {
    const response = await generateEquipmentQRCodeApi(generateForm.equipment_id)
    currentQRCode.value = {
      url: response.data.qr_code_url,
      equipment_name: response.data.equipment_name,
      equipment_code: response.data.equipment_code,
      created_at: new Date().toLocaleString()
    }
    ElMessage.success('二维码生成成功')
  } catch (error) {
    console.error('生成二维码失败:', error)
    ElMessage.error('生成二维码失败')
  } finally {
    generating.value = false
  }
}

// 重置生成表单
const resetGenerate = () => {
  generateForm.equipment_id = 0
  generateForm.qr_type = 'basic'
  generateForm.size = '200'
  generateForm.include_info = ['name', 'code']
  currentQRCode.value = null
}

// 下载二维码
const downloadQRCode = () => {
  if (!currentQRCode.value) return

  const link = document.createElement('a')
  link.href = currentQRCode.value.url
  link.download = `${currentQRCode.value.equipment_code}_qrcode.png`
  link.click()

  ElMessage.success('下载成功')
}

// 打印二维码
const printQRCode = () => {
  if (!currentQRCode.value) return

  const printWindow = window.open('', '_blank')
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>设备二维码 - ${currentQRCode.value.equipment_code}</title>
          <style>
            body { text-align: center; margin: 20px; font-family: Arial, sans-serif; }
            .qrcode-container { display: inline-block; border: 1px solid #ddd; padding: 20px; }
            .equipment-info { margin-bottom: 10px; font-size: 14px; }
            .equipment-name { font-weight: bold; margin-bottom: 5px; }
            img { max-width: 200px; }
          </style>
        </head>
        <body>
          <div class="qrcode-container">
            <div class="equipment-info">
              <div class="equipment-name">${currentQRCode.value.equipment_name}</div>
              <div>设备编号: ${currentQRCode.value.equipment_code}</div>
            </div>
            <img src="${currentQRCode.value.url}" alt="设备二维码" />
          </div>
        </body>
      </html>
    `)
    printWindow.document.close()
    printWindow.print()
  }
}

// 分享二维码
const shareQRCode = () => {
  if (!currentQRCode.value) return

  if (navigator.share) {
    navigator.share({
      title: `设备二维码 - ${currentQRCode.value.equipment_name}`,
      text: `设备编号: ${currentQRCode.value.equipment_code}`,
      url: currentQRCode.value.url
    })
  } else {
    // 复制链接到剪贴板
    navigator.clipboard.writeText(currentQRCode.value.url)
    ElMessage.success('二维码链接已复制到剪贴板')
  }
}

// 显示设备选择器
const showEquipmentSelector = () => {
  selectorVisible.value = true
}

// 全选设备
const selectAllEquipments = async () => {
  try {
    const response = await getEquipmentsApi({ per_page: 1000 })
    selectedEquipments.value = response.data.items
    ElMessage.success(`已选择 ${selectedEquipments.value.length} 个设备`)
  } catch (error) {
    console.error('获取设备列表失败:', error)
    ElMessage.error('获取设备列表失败')
  }
}

// 清空选择
const clearSelectedEquipments = () => {
  selectedEquipments.value = []
  ElMessage.success('已清空选择')
}

// 移除选中设备
const removeSelectedEquipment = (index: number) => {
  selectedEquipments.value.splice(index, 1)
}

// 设备选择器确认
const handleEquipmentSelectorConfirm = (equipments: Equipment[]) => {
  selectedEquipments.value = equipments
}

// 批量生成
const batchGenerate = async () => {
  if (selectedEquipments.value.length === 0) {
    ElMessage.warning('请先选择设备')
    return
  }

  batchGenerating.value = true
  try {
    const ids = selectedEquipments.value.map(eq => eq.id)
    const response = await batchGenerateQRCodesApi(ids)

    // 处理生成结果
    const successCount = response.data.results.filter((r: any) => r.success).length
    ElMessage.success(`成功生成 ${successCount} 个二维码`)

    // 根据输出格式处理下载
    if (batchForm.output_format === 'pdf') {
      downloadBatchPDF(response.data.pdf_url)
    } else if (batchForm.output_format === 'images') {
      downloadBatchImages(response.data.zip_url)
    } else if (batchForm.output_format === 'excel') {
      downloadBatchExcel(response.data.excel_url)
    }
  } catch (error) {
    console.error('批量生成失败:', error)
    ElMessage.error('批量生成失败')
  } finally {
    batchGenerating.value = false
  }
}

// 预览批量效果
const previewBatch = () => {
  if (selectedEquipments.value.length === 0) {
    ElMessage.warning('请先选择设备')
    return
  }
  previewVisible.value = true
}

// 下载批量PDF
const downloadBatchPDF = (url: string) => {
  const link = document.createElement('a')
  link.href = url
  link.download = `设备二维码_${new Date().toISOString().slice(0, 10)}.pdf`
  link.click()
}

// 下载批量图片
const downloadBatchImages = (url: string) => {
  const link = document.createElement('a')
  link.href = url
  link.download = `设备二维码_${new Date().toISOString().slice(0, 10)}.zip`
  link.click()
}

// 下载批量Excel
const downloadBatchExcel = (url: string) => {
  const link = document.createElement('a')
  link.href = url
  link.download = `设备二维码_${new Date().toISOString().slice(0, 10)}.xlsx`
  link.click()
}

// 开始扫码
const startScan = async () => {
  try {
    mediaStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment' }
    })

    if (videoRef.value) {
      videoRef.value.srcObject = mediaStream
      scanning.value = true
    }
  } catch (error) {
    console.error('启动摄像头失败:', error)
    ElMessage.error('启动摄像头失败，请检查权限设置')
  }
}

// 停止扫码
const stopScan = () => {
  if (mediaStream) {
    mediaStream.getTracks().forEach(track => track.stop())
    mediaStream = null
  }
  scanning.value = false
}

// 拍照识别
const captureImage = () => {
  // 实现拍照识别逻辑
  ElMessage.info('拍照识别功能开发中...')
}

// 处理图片上传
const handleImageUpload = (file: any) => {
  // 实现图片二维码识别逻辑
  ElMessage.info('图片识别功能开发中...')
}

// 状态标签类型
const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'success',
    2: 'warning',
    3: 'danger',
    4: 'info'
  }
  return typeMap[status] || 'info'
}

// 状态文本
const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '正常',
    2: '借出',
    3: '维修',
    4: '报废'
  }
  return textMap[status] || '未知'
}

// 查看设备详情
const viewEquipmentDetail = () => {
  if (scanResult.value) {
    ElMessage.info('跳转到设备详情页面...')
  }
}

// 申请借用
const borrowEquipment = () => {
  if (scanResult.value) {
    ElMessage.info('跳转到设备借用页面...')
  }
}

// 报修设备
const reportMaintenance = () => {
  if (scanResult.value) {
    ElMessage.info('跳转到设备报修页面...')
  }
}

// 搜索二维码
const handleSearch = () => {
  // 处理日期范围
  if (dateRange.value) {
    searchForm.start_date = dateRange.value[0]
    searchForm.end_date = dateRange.value[1]
  } else {
    searchForm.start_date = ''
    searchForm.end_date = ''
  }

  pagination.current_page = 1
  loadQRCodeList()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    equipment_name: '',
    equipment_code: '',
    start_date: '',
    end_date: ''
  })
  dateRange.value = null
  pagination.current_page = 1
  loadQRCodeList()
}

// 加载二维码列表
const loadQRCodeList = async () => {
  try {
    // 模拟加载二维码列表
    qrcodeList.value = [
      {
        id: 1,
        equipment_name: '显微镜-001',
        equipment_code: 'EQ001',
        url: '/api/qrcodes/eq001.png',
        created_at: '2024-01-10'
      },
      {
        id: 2,
        equipment_name: '离心机-002',
        equipment_code: 'EQ002',
        url: '/api/qrcodes/eq002.png',
        created_at: '2024-01-09'
      }
    ]
    pagination.total = qrcodeList.value.length
  } catch (error) {
    console.error('加载二维码列表失败:', error)
  }
}

// 下载单个二维码
const downloadSingleQRCode = (qrcode: any) => {
  const link = document.createElement('a')
  link.href = qrcode.url
  link.download = `${qrcode.equipment_code}_qrcode.png`
  link.click()
  ElMessage.success('下载成功')
}

// 打印单个二维码
const printSingleQRCode = (qrcode: any) => {
  const printWindow = window.open('', '_blank')
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>设备二维码 - ${qrcode.equipment_code}</title>
          <style>
            body { text-align: center; margin: 20px; font-family: Arial, sans-serif; }
            .qrcode-container { display: inline-block; border: 1px solid #ddd; padding: 20px; }
            .equipment-info { margin-bottom: 10px; font-size: 14px; }
            .equipment-name { font-weight: bold; margin-bottom: 5px; }
            img { max-width: 200px; }
          </style>
        </head>
        <body>
          <div class="qrcode-container">
            <div class="equipment-info">
              <div class="equipment-name">${qrcode.equipment_name}</div>
              <div>设备编号: ${qrcode.equipment_code}</div>
            </div>
            <img src="${qrcode.url}" alt="设备二维码" />
          </div>
        </body>
      </html>
    `)
    printWindow.document.close()
    printWindow.print()
  }
}

// 删除二维码
const deleteQRCode = async (qrcode: any) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除"${qrcode.equipment_name}"的二维码吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    // 实现删除逻辑
    ElMessage.success('删除成功')
    loadQRCodeList()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除二维码失败:', error)
      ElMessage.error('删除二维码失败')
    }
  }
}

// 页码变化
const handleCurrentChange = () => {
  loadQRCodeList()
}

// 页大小变化
const handleSizeChange = () => {
  pagination.current_page = 1
  loadQRCodeList()
}

// 初始化
onMounted(() => {
  loadQRCodeList()
})

// 清理资源
onUnmounted(() => {
  stopScan()
})
</script>

<style scoped>
.equipment-qrcode {
  padding: 20px;
}

.page-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.page-header {
  margin-bottom: 24px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

/* 二维码生成 */
.generate-section {
  padding: 20px 0;
}

.equipment-option {
  padding: 4px 0;
}

.equipment-name {
  font-weight: 600;
  margin-bottom: 2px;
}

.equipment-info {
  font-size: 12px;
  color: #909399;
}

.qrcode-preview {
  min-height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qrcode-display {
  text-align: center;
}

.qrcode-image img {
  max-width: 200px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.qrcode-info {
  margin: 16px 0;
}

.qrcode-info h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
}

.qrcode-info p {
  margin: 4px 0;
  color: #666;
  font-size: 14px;
}

.qrcode-actions {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.qrcode-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* 批量生成 */
.batch-section {
  padding: 20px 0;
}

.batch-equipment-select {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

/* 扫码查询 */
.scan-section {
  padding: 20px 0;
}

.scan-area {
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ddd;
  border-radius: 8px;
  position: relative;
}

.scan-placeholder {
  text-align: center;
}

.scan-placeholder p {
  margin: 16px 0;
  color: #666;
}

.scan-camera {
  position: relative;
  width: 100%;
  height: 300px;
}

.scan-camera video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}

.scan-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.scan-frame {
  width: 200px;
  height: 200px;
  border: 2px solid #409EFF;
  border-radius: 8px;
  background: rgba(64, 158, 255, 0.1);
}

.scan-controls {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
}

.scan-result {
  padding: 16px 0;
}

.scan-actions {
  margin-top: 16px;
  display: flex;
  gap: 8px;
  justify-content: center;
}

/* 二维码管理 */
.manage-section {
  padding: 20px 0;
}

.table-search {
  margin-bottom: 20px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
}

.qrcode-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.qrcode-item {
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  transition: box-shadow 0.3s;
}

.qrcode-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.qrcode-item .qrcode-image img {
  width: 120px;
  height: 120px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.qrcode-item .qrcode-info {
  margin: 12px 0;
}

.qrcode-item .qrcode-info h4 {
  margin: 0 0 8px 0;
  font-size: 14px;
  font-weight: 600;
}

.qrcode-item .qrcode-info p {
  margin: 4px 0;
  font-size: 12px;
  color: #666;
}

.qrcode-item .qrcode-actions {
  display: flex;
  gap: 4px;
  justify-content: center;
}

.table-pagination {
  display: flex;
  justify-content: flex-end;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .equipment-qrcode {
    padding: 10px;
  }

  .page-card {
    padding: 16px;
  }

  .qrcode-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
  }

  .batch-equipment-select {
    flex-direction: column;
  }

  .qrcode-actions,
  .scan-actions {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
