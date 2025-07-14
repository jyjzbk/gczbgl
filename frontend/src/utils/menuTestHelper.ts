// 菜单权限测试辅助工具
export interface MenuTestResult {
  role: string
  permissions: string[]
  expectedMenus: {
    [menuName: string]: {
      visible: boolean
      subMenus?: {
        [subMenuName: string]: boolean
      }
    }
  }
}

// 角色权限配置
export const rolePermissions: { [role: string]: string[] } = {
  province_admin: [
    'user', 'user.list', 'user.create', 'user.update', 'user.delete',
    'role', 'role.list', 'role.create', 'role.update', 'role.delete',
    'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
    'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance',
    'system', 'system.read', 'log', 'log.read'
  ],
  school_admin: [
    'user', 'user.list', 'user.create', 'user.update',
    'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
    'equipment', 'equipment.list', 'equipment.create', 'equipment.update', 'equipment.delete', 'equipment.borrow', 'equipment.maintenance'
  ],
  school_teacher: [
    'experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record',
    'equipment', 'equipment.list', 'equipment.borrow'
  ],
  school_student: [
    'experiment', 'experiment.catalog', 'experiment.booking',
    'equipment', 'equipment.list'
  ]
}

// 菜单权限检查函数
export const checkMenuPermissions = (permissions: string[]) => {
  const hasPermission = (permission: string) => permissions.includes(permission)
  const hasAnyPermission = (permissionList: string[]) => permissionList.some(p => hasPermission(p))

  return {
    // 一级菜单
    hasUserPermission: hasAnyPermission(['user', 'user.list', 'role', 'role.list']),
    hasBasicDataPermission: hasAnyPermission(['user', 'user.list', 'user.create']),
    hasExperimentPermission: hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.booking', 'experiment.record']),
    hasEquipmentPermission: hasAnyPermission(['equipment', 'equipment.list']),
    hasStatisticsPermission: hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']) ||
                            hasAnyPermission(['equipment', 'equipment.list']) ||
                            hasAnyPermission(['user', 'user.list']),
    hasSystemPermission: hasAnyPermission(['system', 'system.read', 'log', 'log.read']),

    // 二级菜单 - 用户管理
    userList: hasAnyPermission(['user', 'user.list']),
    roleManagement: hasAnyPermission(['role', 'role.list']),
    permissionManagement: hasAnyPermission(['role', 'role.list']),

    // 二级菜单 - 基础数据
    schoolManagement: hasAnyPermission(['user', 'user.list', 'user.create']),
    laboratoryManagement: hasAnyPermission(['user', 'user.list', 'user.create']),
    subjectManagement: hasAnyPermission(['user', 'user.list', 'user.create']),

    // 二级菜单 - 实验管理
    experimentCatalog: hasAnyPermission(['experiment', 'experiment.catalog']),
    experimentBooking: hasPermission('experiment.booking'),
    experimentRecord: hasPermission('experiment.record'),
    experimentStatistics: hasAnyPermission(['experiment', 'experiment.catalog', 'experiment.record']),

    // 二级菜单 - 设备管理
    equipmentManagement: hasAnyPermission(['equipment', 'equipment.list']),
    equipmentBorrow: hasPermission('equipment.borrow'),
    equipmentMaintenance: hasPermission('equipment.maintenance'),
    equipmentQrcode: hasAnyPermission(['equipment', 'equipment.list']),

    // 二级菜单 - 统计报表
    statisticsExperiment: hasAnyPermission(['experiment', 'experiment.catalog']),
    statisticsEquipment: hasAnyPermission(['equipment', 'equipment.list']),
    statisticsRegion: hasAnyPermission(['user', 'user.list'])
  }
}

// 生成测试结果
export const generateMenuTestResults = (): MenuTestResult[] => {
  return Object.entries(rolePermissions).map(([role, permissions]) => {
    const menuChecks = checkMenuPermissions(permissions)
    
    return {
      role,
      permissions,
      expectedMenus: {
        dashboard: { visible: true }, // 仪表盘对所有用户可见
        userManagement: {
          visible: menuChecks.hasUserPermission,
          subMenus: {
            userList: menuChecks.userList,
            roleManagement: menuChecks.roleManagement,
            permissionManagement: menuChecks.permissionManagement
          }
        },
        basicData: {
          visible: menuChecks.hasBasicDataPermission,
          subMenus: {
            schoolManagement: menuChecks.schoolManagement,
            laboratoryManagement: menuChecks.laboratoryManagement,
            subjectManagement: menuChecks.subjectManagement
          }
        },
        experimentManagement: {
          visible: menuChecks.hasExperimentPermission,
          subMenus: {
            experimentCatalog: menuChecks.experimentCatalog,
            experimentBooking: menuChecks.experimentBooking,
            experimentRecord: menuChecks.experimentRecord,
            experimentStatistics: menuChecks.experimentStatistics
          }
        },
        equipmentManagement: {
          visible: menuChecks.hasEquipmentPermission,
          subMenus: {
            equipmentManagement: menuChecks.equipmentManagement,
            equipmentBorrow: menuChecks.equipmentBorrow,
            equipmentMaintenance: menuChecks.equipmentMaintenance,
            equipmentQrcode: menuChecks.equipmentQrcode
          }
        },
        statistics: {
          visible: menuChecks.hasStatisticsPermission,
          subMenus: {
            statisticsExperiment: menuChecks.statisticsExperiment,
            statisticsEquipment: menuChecks.statisticsEquipment,
            statisticsRegion: menuChecks.statisticsRegion
          }
        },
        systemManagement: {
          visible: menuChecks.hasSystemPermission
        }
      }
    }
  })
}

// 打印测试结果
export const printMenuTestResults = () => {
  const results = generateMenuTestResults()
  
  console.log('=== 角色菜单权限测试结果 ===')
  
  results.forEach(result => {
    console.log(`\n角色: ${result.role}`)
    console.log(`权限: ${result.permissions.join(', ')}`)
    console.log('菜单显示:')
    
    Object.entries(result.expectedMenus).forEach(([menuName, menuInfo]) => {
      const status = menuInfo.visible ? '✅' : '❌'
      console.log(`  ${status} ${menuName}`)
      
      if (menuInfo.subMenus) {
        Object.entries(menuInfo.subMenus).forEach(([subMenuName, visible]) => {
          const subStatus = visible ? '✅' : '❌'
          console.log(`    ${subStatus} ${subMenuName}`)
        })
      }
    })
  })
}
