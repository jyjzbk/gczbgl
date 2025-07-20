export default {
  path: '/experiment',
  component: () => import('@/layouts/DefaultLayout.vue'),
  meta: { title: '实验管理', icon: 'Experiment' },
  children: [
    {
      path: 'catalogs',
      name: 'ExperimentCatalogs',
      component: () => import('@/views/experiment/CatalogList.vue'),
      meta: { title: '实验目录', keepAlive: true }
    },
    {
      path: 'catalogs/:catalogId/equipment-config',
      name: 'EquipmentRequirementConfig',
      component: () => import('@/views/experiment/EquipmentRequirementConfig.vue'),
      meta: { title: '器材配置', activeMenu: '/experiment/catalogs' }
    },
    {
      path: 'reservations',
      name: 'ExperimentReservations',
      component: () => import('@/views/experiment/ReservationList.vue'),
      meta: { title: '实验预约', keepAlive: true }
    }
  ]
}