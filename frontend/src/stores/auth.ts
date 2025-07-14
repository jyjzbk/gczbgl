import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { loginApi, logoutApi, getUserInfoApi } from '@/api/auth'

// 用户信息接口
export interface UserInfo {
  id: number
  username: string
  real_name: string
  email: string
  phone?: string
  avatar?: string
  role: string
  school_id?: number
  school_name?: string
  permissions: string[]
  created_at: string
}

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  
  // 状态
  const token = ref<string | null>(localStorage.getItem('token'))
  const userInfo = ref<UserInfo | null>(null)
  const permissions = ref<string[]>([])
  const isLoggedIn = ref(false)
  
  // 计算属性
  const isAuthenticated = computed(() => {
    return !!token.value && !!userInfo.value
  })
  
  const userRole = computed(() => {
    return userInfo.value?.role || ''
  })
  
  const userName = computed(() => {
    return userInfo.value?.real_name || userInfo.value?.username || ''
  })
  
  // 设置token
  const setToken = (newToken: string) => {
    token.value = newToken
    localStorage.setItem('token', newToken)
  }
  
  // 清除token
  const clearToken = () => {
    token.value = null
    localStorage.removeItem('token')
  }
  
  // 设置用户信息
  const setUserInfo = (info: UserInfo) => {
    userInfo.value = info
    permissions.value = info.permissions || []
    isLoggedIn.value = true
  }
  
  // 清除用户信息
  const clearUserInfo = () => {
    userInfo.value = null
    permissions.value = []
    isLoggedIn.value = false
  }
  
  // 登录
  const login = async (credentials: { username: string; password: string }) => {
    try {
      console.log('正在调用登录API:', credentials.username)

      // 调用登录API
      const response = await loginApi(credentials)

      console.log('登录API响应:', response)

      // 检查响应格式
      if (response.data && response.data.token) {
        // 后端返回的格式: { success: true, data: { token: "...", user: {...} } }
        setToken(response.data.token)
        setUserInfo({
          ...response.data.user,
          role: response.data.user.role || 'admin', // 设置默认角色
          permissions: response.data.user.permissions || ['user:read', 'user:write']
        } as UserInfo)
      } else if (response.access_token) {
        // Laravel JWT 格式
        setToken(response.access_token)
        setUserInfo({
          ...response.user,
          permissions: response.user.permissions || ['user:read', 'user:write']
        } as UserInfo)
      } else if (response.token) {
        // 标准格式
        setToken(response.token)
        setUserInfo(response.user as UserInfo)
      } else {
        throw new Error('登录响应格式错误: ' + JSON.stringify(response))
      }

      ElMessage.success('登录成功')
      return true
    } catch (error) {
      console.error('登录失败:', error)
      ElMessage.error('登录失败，请检查用户名和密码')
      return false
    }
  }
  
  // 退出登录
  const logout = async () => {
    try {
      // 这里应该调用退出登录API
      // await logoutApi()
      
      clearToken()
      clearUserInfo()
      
      ElMessage.success('已退出登录')
      router.push('/login')
    } catch (error) {
      console.error('退出登录失败:', error)
      // 即使API调用失败，也要清除本地状态
      clearToken()
      clearUserInfo()
      router.push('/login')
    }
  }
  
  // 获取用户信息
  const fetchUserInfo = async () => {
    if (!token.value) {
      return false
    }

    try {
      console.log('正在获取用户信息...')

      // 调用获取用户信息API
      const response = await getUserInfoApi()

      console.log('用户信息API响应:', response)

      setUserInfo({
        ...response.data,
        role: response.data.role || 'admin', // 设置默认角色
        permissions: response.data.permissions || ['user:read', 'user:write']
      } as UserInfo)
      return true
    } catch (error) {
      console.error('获取用户信息失败:', error)
      clearToken()
      clearUserInfo()
      return false
    }
  }
  
  // 检查权限
  const hasPermission = (permission: string) => {
    return permissions.value.includes(permission)
  }
  
  // 检查角色
  const hasRole = (role: string) => {
    return userRole.value === role
  }
  
  // 检查是否有任一权限
  const hasAnyPermission = (permissionList: string[]) => {
    return permissionList.some(permission => hasPermission(permission))
  }
  
  // 初始化认证状态
  const initAuth = async () => {
    if (token.value) {
      await fetchUserInfo()
    }
  }
  
  return {
    // 状态
    token,
    userInfo,
    permissions,
    isLoggedIn,
    
    // 计算属性
    isAuthenticated,
    userRole,
    userName,
    
    // 方法
    setToken,
    clearToken,
    setUserInfo,
    clearUserInfo,
    login,
    logout,
    fetchUserInfo,
    hasPermission,
    hasRole,
    hasAnyPermission,
    initAuth
  }
})
