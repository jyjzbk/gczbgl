export default {
  path: '/school-catalog-config',
  component: () => import('@/layouts/DefaultLayout.vue'),
  meta: { 
    title: '学校目录配置', 
    icon: 'Setting',
    requiresAuth: true 
  },
  children: [
    {
      path: 'my-config',
      name: 'MyCatalogConfig',
      component: () => import('@/views/school-catalog/MyCatalogConfig.vue'),
      meta: {
        title: '我的目录配置',
        keepAlive: true,
        permission: ['school_experiment_catalog.view', 'school_experiment_catalog.config']
      }
    },
    {
      path: 'subordinate-assignment',
      name: 'SubordinateAssignment', 
      component: () => import('@/views/school-catalog/SubordinateAssignment.vue'),
      meta: {
        title: '下级目录指定',
        keepAlive: true,
        permission: 'school_experiment_catalog.assign'
      }
    },
    {
      path: 'completion-statistics',
      name: 'CompletionStatistics',
      component: () => import('@/views/school-catalog/CompletionStatistics.vue'),
      meta: {
        title: '完成率统计',
        keepAlive: true,
        permission: 'school_experiment_catalog.completion_stats'
      }
    }
  ]
}
