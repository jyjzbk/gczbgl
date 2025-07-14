import request from './request'

// 登录接口
export interface LoginParams {
  username: string
  password: string
  remember?: boolean
}

export interface LoginResponse {
  token: string
  user: {
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
}

export const loginApi = (data: LoginParams) => {
  return request.post<LoginResponse>('/auth/login', data)
}

// 注册接口
export interface RegisterParams {
  username: string
  password: string
  password_confirmation: string
  real_name: string
  email: string
  phone?: string
  school_id?: number
}

export interface RegisterResponse {
  message: string
  user: {
    id: number
    username: string
    real_name: string
    email: string
    phone?: string
    role: string
    school_id?: number
    created_at: string
  }
}

export const registerApi = (data: RegisterParams) => {
  return request.post<RegisterResponse>('/auth/register', data)
}

// 退出登录接口
export const logoutApi = () => {
  return request.post('/auth/logout')
}

// 获取用户信息接口
export const getUserInfoApi = () => {
  return request.get('/auth/me')
}

// 刷新token接口
export const refreshTokenApi = () => {
  return request.post('/auth/refresh')
}

// 发送验证码接口
export interface SendCodeParams {
  email: string
  type: 'register' | 'reset_password'
}

export const sendVerificationCodeApi = (data: SendCodeParams) => {
  return request.post('/auth/send-code', data)
}

// 重置密码接口
export interface ResetPasswordParams {
  email: string
  code: string
  password: string
  password_confirmation: string
}

export const resetPasswordApi = (data: ResetPasswordParams) => {
  return request.post('/auth/reset-password', data)
}

// 修改密码接口
export interface ChangePasswordParams {
  current_password: string
  password: string
  password_confirmation: string
}

export const changePasswordApi = (data: ChangePasswordParams) => {
  return request.post('/auth/change-password', data)
}
