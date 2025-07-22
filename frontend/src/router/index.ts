import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    // 认证相关路由
    {
      path: '/login',
      component: AuthLayout,
      children: [
        {
          path: '',
          name: 'Login',
          component: () => import('@/views/auth/Login.vue'),
          meta: { title: '用户登录', requiresGuest: true }
        }
      ]
    },
    {
      path: '/register',
      component: AuthLayout,
      children: [
        {
          path: '',
          name: 'Register',
          component: () => import('@/views/auth/Register.vue'),
          meta: { title: '用户注册', requiresGuest: true }
        }
      ]
    },
    {
      path: '/forgot-password',
      component: AuthLayout,
      children: [
        {
          path: '',
          name: 'ForgotPassword',
          component: () => import('@/views/auth/ForgotPassword.vue'),
          meta: { title: '忘记密码', requiresGuest: true }
        }
      ]
    },
    // 主应用路由
    {
      path: '/dashboard',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: () => import('@/views/Dashboard.vue'),
          meta: { title: '仪表盘' }
        }
      ]
    },


    {
      path: '/profile',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'Profile',
          component: () => import('@/views/user/Profile.vue'),
          meta: { title: '个人资料' }
        }
      ]
    },
    // 用户管理路由
    {
      path: '/users',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'UserList',
          component: () => import('@/views/user/UserList.vue'),
          meta: { title: '用户列表', requiredPermissions: ['user', 'user.list'] }
        }
      ]
    },
    {
      path: '/roles',
      component: DefaultLayout,
      meta: { requiresAuth: true, requiredPermissions: ['role', 'role.list'] },
      children: [
        {
          path: '',
          name: 'RoleManagement',
          component: () => import('@/views/user/RoleManagement.vue'),
          meta: { title: '角色管理', requiredPermissions: ['role', 'role.list'] }
        }
      ]
    },
    {
      path: '/permissions',
      component: DefaultLayout,
      meta: { requiresAuth: true, requiredPermissions: ['role', 'role.list'] },
      children: [
        {
          path: '',
          name: 'PermissionManagement',
          component: () => import('@/views/user/PermissionManagement.vue'),
          meta: { title: '权限管理', requiredPermissions: ['role', 'role.list'] }
        }
      ]
    },
    {
      path: '/organization-management',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'OrganizationManagement',
          component: () => import('@/views/user/OrganizationManagement.vue'),
          meta: { title: '组织信息管理' }
        }
      ]
    },
    // 基础数据管理路由
    {
      path: '/schools',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'SchoolManagement',
          component: () => import('@/views/basic/SchoolManagement.vue'),
          meta: { title: '学校管理' }
        }
      ]
    },
    {
      path: '/laboratories',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'LaboratoryManagement',
          component: () => import('@/views/basic/LaboratoryManagement.vue'),
          meta: { title: '实验室管理' }
        }
      ]
    },
    {
      path: '/subjects',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'SubjectManagement',
          component: () => import('@/views/basic/SubjectManagement.vue'),
          meta: { title: '学科管理' }
        }
      ]
    },
    {
      path: '/laboratory-types',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'LaboratoryTypeManagement',
          component: () => import('@/views/basic/LaboratoryTypeManagement.vue'),
          meta: { title: '实验室类型管理' }
        }
      ]
    },
    {
      path: '/equipment-standards',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'EquipmentStandardManagement',
          component: () => import('@/views/basic/EquipmentStandardManagement.vue'),
          meta: { title: '教学仪器配备标准' }
        }
      ]
    },
    // 教材版本管理路由
    {
      path: '/textbook-versions',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'TextbookVersions',
          component: () => import('@/views/basic/TextbookVersions.vue'),
          meta: { title: '教材版本管理' }
        }
      ]
    },
    // 章节结构管理路由
    {
      path: '/textbook-chapters',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'TextbookChapters',
          component: () => import('@/views/basic/TextbookChapters.vue'),
          meta: { title: '章节结构管理' }
        }
      ]
    },
    // 实验管理路由
    {
      path: '/experiment-catalogs',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'ExperimentCatalogs',
          component: () => import('@/views/experiment/ExperimentCatalogs.vue'),
          meta: { title: '实验目录' }
        }
      ]
    },
    {
      path: '/experiment-bookings',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'ExperimentBookings',
          component: () => import('@/views/experiment/ExperimentBookings.vue'),
          meta: { title: '实验预约' }
        }
      ]
    },
    {
      path: '/experiment-records',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'ExperimentRecords',
          component: () => import('@/views/experiment/ExperimentRecords.vue'),
          meta: { title: '实验记录' }
        }
      ]
    },
    {
      path: '/experiment-statistics',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'ExperimentStatistics',
          component: () => import('@/views/experiment/ExperimentStatistics.vue'),
          meta: { title: '实验统计' }
        }
      ]
    },
    // 智能实验预约路由
    {
      path: '/smart-reservation',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'SmartReservation',
          component: () => import('@/views/experiment/SmartReservation.vue'),
          meta: { title: '智能预约' }
        }
      ]
    },
    {
      path: '/laboratory-schedule',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'LaboratorySchedule',
          component: () => import('@/views/experiment/LaboratorySchedule.vue'),
          meta: { title: '实验室课表' }
        }
      ]
    },
    {
      path: '/personal-archive',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'PersonalArchive',
          component: () => import('@/views/experiment/PersonalArchive.vue'),
          meta: { title: '个人实验档案' }
        }
      ]
    },
    // 设备管理路由
    {
      path: '/equipment-management',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'EquipmentManagement',
          component: () => import('@/views/equipment/EquipmentManagement.vue'),
          meta: { title: '设备档案管理' }
        }
      ]
    },
    {
      path: '/equipment-borrow',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'EquipmentBorrow',
          component: () => import('@/views/equipment/EquipmentBorrow.vue'),
          meta: { title: '设备借用管理' }
        }
      ]
    },
    {
      path: '/equipment-maintenance',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'EquipmentMaintenance',
          component: () => import('@/views/equipment/EquipmentMaintenance.vue'),
          meta: { title: '设备维修管理' }
        }
      ]
    },
    {
      path: '/equipment-qrcode',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'EquipmentQRCode',
          component: () => import('@/views/equipment/EquipmentQRCode.vue'),
          meta: { title: '设备二维码管理' }
        }
      ]
    },
    // 统计报表路由
    {
      path: '/statistics',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          redirect: '/statistics/dashboard'
        },
        {
          path: 'dashboard',
          name: 'StatisticsDashboard',
          component: () => import('@/views/statistics/Dashboard.vue'),
          meta: {
            title: '统计仪表盘',
            requiredPermissions: ['statistics.view']
          }
        },
        {
          path: 'reports',
          name: 'StatisticsReports',
          component: () => import('@/views/statistics/Reports.vue'),
          meta: {
            title: '详细报表',
            requiredPermissions: ['statistics.view']
          }
        },
        {
          path: 'test',
          name: 'StatisticsTest',
          component: () => import('@/views/statistics/Test.vue'),
          meta: {
            title: 'API测试',
            requiredPermissions: ['statistics.view']
          }
        }
      ]
    },
    // TODO: 其他实验管理页面待开发
    // 调试页面
    {
      path: '/debug/auth',
      name: 'AuthDebug',
      component: () => import('@/views/debug/AuthDebug.vue'),
      meta: { title: '认证调试' }
    },

    // 这里后续会添加更多路由
  ],
})

// 路由守卫
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // 如果有token但没有用户信息，尝试获取用户信息
  if (authStore.token && !authStore.userInfo) {
    await authStore.fetchUserInfo()
  }

  // 检查是否需要认证
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // 未登录，跳转到登录页面
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
      return
    }

    // 检查权限
    const requiredPermissions = to.meta.requiredPermissions as string[]
    if (requiredPermissions && requiredPermissions.length > 0) {
      const hasPermission = requiredPermissions.some(permission =>
        authStore.hasPermission(permission)
      )

      if (!hasPermission) {
        // 权限不足，跳转到403页面或首页
        console.warn('权限不足，无法访问:', to.path, '需要权限:', requiredPermissions, '用户权限:', authStore.permissions)
        next('/dashboard')
        return
      }
    }
  }

  // 检查是否需要游客状态（已登录用户不能访问登录、注册页面）
  if (to.meta.requiresGuest) {
    if (authStore.isAuthenticated) {
      // 已登录，跳转到首页
      next('/dashboard')
      return
    }
  }

  next()
})

export default router
