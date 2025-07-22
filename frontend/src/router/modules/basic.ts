export default {
  path: '/basic',
  component: () => import('@/layouts/DefaultLayout.vue'),
  meta: { title: '基础数据', icon: 'Setting' },
  children: [
    {
      path: 'textbook-versions',
      name: 'TextbookVersions',
      component: () => import('@/views/basic/TextbookVersions.vue'),
      meta: { 
        title: '教材版本管理', 
        keepAlive: true,
        permission: 'textbook-versions'
      }
    },
    {
      path: 'textbook-chapters',
      name: 'TextbookChapters',
      component: () => import('@/views/basic/TextbookChapters.vue'),
      meta: { 
        title: '章节结构管理', 
        keepAlive: true,
        permission: 'textbook-chapters'
      }
    }
  ]
}
