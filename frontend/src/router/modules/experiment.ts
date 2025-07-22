import { useExperimentPermission } from '@/composables/useExperimentPermission'

export default {
  path: '/experiment',
  component: () => import('@/layouts/DefaultLayout.vue'),
  meta: { title: '实验管理', icon: 'Experiment' },
  children: [
    {
      path: 'catalogs',
      name: 'ExperimentCatalogs',
      component: () => import('@/views/experiment/ExperimentCatalogs.vue'),
      meta: {
        title: '实验目录',
        keepAlive: true,
        permission: 'experiment-catalogs'
      }
    },
    {
      path: 'catalogs/:catalogId/equipment-config',
      name: 'EquipmentRequirementConfig',
      component: () => import('@/views/experiment/EquipmentRequirementConfig.vue'),
      meta: {
        title: '器材配置',
        activeMenu: '/experiment/catalogs',
        permission: 'experiment-catalogs'
      }
    },
    {
      path: 'smart-reservation',
      name: 'SmartReservation',
      component: () => import('@/views/experiment/SmartReservation.vue'),
      meta: {
        title: '智能预约',
        keepAlive: true,
        permission: 'smart-reservation'
      }
    },
    {
      path: 'reservations',
      name: 'ExperimentReservations',
      component: () => import('@/views/experiment/ReservationList.vue'),
      meta: {
        title: '实验预约记录',
        keepAlive: true,
        permission: 'smart-reservation'
      }
    }
  ]
}