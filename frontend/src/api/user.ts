import request from './request'

// 用户信息接口
export interface UserProfile {
  id: number
  username: string
  real_name: string
  email: string
  phone?: string
  avatar?: string
  role: string
  school_id?: number
  school_name?: string
  department?: string
  position?: string
  bio?: string
  created_at: string
  updated_at: string
}

// 更新用户资料接口
export interface UpdateProfileParams {
  real_name?: string
  email?: string
  phone?: string
  department?: string
  position?: string
  bio?: string
}

export const updateProfileApi = (data: UpdateProfileParams) => {
  return request.put('/user/profile', data)
}

// 上传头像接口
export const uploadAvatarApi = (file: File) => {
  const formData = new FormData()
  formData.append('avatar', file)
  
  return request.post('/user/avatar', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 获取用户列表接口（管理员功能）
export interface UserListParams {
  page?: number
  per_page?: number
  search?: string
  role?: string
  school_id?: number
  status?: string
}

export interface UserListResponse {
  success: boolean
  data: {
    items: UserProfile[]
    pagination: {
      current_page: number
      per_page: number
      total: number
      last_page: number
    }
  }
}

export const getUserListApi = (params: UserListParams) => {
  return request.get<UserListResponse>('/users', { params })
}

// 获取组织下的用户列表接口
export interface OrganizationUserListParams {
  organization_id?: number
  organization_level?: number
  page?: number
  per_page?: number
  search?: string
  role?: string
  status?: string
}

export const getOrganizationUsersApi = (params: OrganizationUserListParams) => {
  return request.get<UserListResponse>('/organizations/users', { params })
}

// 创建用户接口（管理员功能）
export interface CreateUserParams {
  username: string
  password: string
  real_name: string
  email: string
  phone?: string
  role: string
  school_id?: number
  department?: string
  position?: string
}

export const createUserApi = (data: CreateUserParams) => {
  return request.post('/users', data)
}

// 获取单个用户详情接口
export const getUserApi = (id: number) => {
  return request.get(`/users/${id}`)
}

// 更新用户接口（管理员功能）
export interface UpdateUserParams {
  real_name?: string
  email?: string
  phone?: string
  role?: string
  school_id?: number
  department?: string
  position?: string
  status?: string
}

export const updateUserApi = (id: number, data: UpdateUserParams) => {
  return request.put(`/users/${id}`, data)
}

// 删除用户接口（管理员功能）
export const deleteUserApi = (id: number) => {
  return request.delete(`/users/${id}`)
}

// 重置用户密码接口（管理员功能）
export interface ResetUserPasswordParams {
  password: string
  password_confirmation: string
}

export const resetUserPasswordApi = (id: number, data: ResetUserPasswordParams) => {
  return request.post(`/users/${id}/reset-password`, data)
}

// 获取学校列表接口（用于注册时选择）
export interface School {
  id: number
  name: string
  code: string
  region_name: string
}

export const getSchoolsApi = (params?: { search?: string }) => {
  return request.get<{ data: School[] }>('/schools', { params })
}

// 用户API对象（为了兼容组件中的使用方式）
export const userApi = {
  getProfile: () => request.get('/user/profile'),
  updateProfile: updateProfileApi,
  uploadAvatar: uploadAvatarApi,
  getList: getUserListApi,
  create: createUserApi,
  get: getUserApi,
  update: updateUserApi,
  delete: deleteUserApi,
  resetPassword: resetUserPasswordApi
}
