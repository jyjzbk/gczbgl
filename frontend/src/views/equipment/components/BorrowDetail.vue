<template>
  <el-dialog
    v-model="visible"
    title="借用详情"
    width="700px"
    :before-close="handleClose"
  >
    <div v-if="borrow" class="borrow-detail">
      <el-descriptions :column="2" border>
        <el-descriptions-item label="设备名称">
          {{ borrow.equipment?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="设备编号">
          {{ borrow.equipment?.code }}
        </el-descriptions-item>
        <el-descriptions-item label="借用人">
          {{ borrow.borrower_name }}
        </el-descriptions-item>
        <el-descriptions-item label="联系电话">
          {{ borrow.borrower_phone }}
        </el-descriptions-item>
        <el-descriptions-item label="借用日期">
          {{ borrow.borrow_date }}
        </el-descriptions-item>
        <el-descriptions-item label="预计归还">
          {{ borrow.expected_return_date }}
        </el-descriptions-item>
        <el-descriptions-item label="实际归还">
          {{ borrow.actual_return_date || '未归还' }}
        </el-descriptions-item>
        <el-descriptions-item label="借用状态">
          <el-tag :type="getStatusTagType(borrow.status)">
            {{ getStatusText(borrow.status) }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="借用用途" :span="2">
          {{ borrow.purpose }}
        </el-descriptions-item>
        <el-descriptions-item v-if="borrow.remark" label="备注" :span="2">
          {{ borrow.remark }}
        </el-descriptions-item>
      </el-descriptions>
    </div>
    
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="handleClose">关闭</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { EquipmentBorrow } from '@/api/equipment'

interface Props {
  modelValue: boolean
  borrow?: EquipmentBorrow | null
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
}

const props = withDefaults(defineProps<Props>(), {
  borrow: null
})

const emit = defineEmits<Emits>()

const visible = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const getStatusTagType = (status: number) => {
  const typeMap: Record<number, string> = {
    1: 'warning',
    2: 'success',
    3: 'primary',
    4: 'success',
    5: 'danger',
    6: 'danger'
  }
  return typeMap[status] || 'info'
}

const getStatusText = (status: number) => {
  const textMap: Record<number, string> = {
    1: '申请中',
    2: '已批准',
    3: '已借出',
    4: '已归还',
    5: '已拒绝',
    6: '逾期'
  }
  return textMap[status] || '未知'
}

const handleClose = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.borrow-detail {
  max-height: 500px;
  overflow-y: auto;
}

.dialog-footer {
  text-align: right;
}
</style>
