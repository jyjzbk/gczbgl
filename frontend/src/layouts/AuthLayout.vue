<template>
  <div class="auth-layout">
    <!-- 背景装饰 -->
    <div class="auth-background">
      <div class="bg-pattern"></div>
    </div>
    
    <!-- 主要内容 -->
    <div class="auth-container">
      <!-- 左侧信息区域 -->
      <div class="auth-info">
        <div class="info-content">
          <div class="logo-section">
            <el-icon :size="60" color="#409EFF">
              <Operation />
            </el-icon>
            <h1 class="system-title">实验教学管理平台</h1>
            <p class="system-subtitle">Laboratory Teaching Management System</p>
          </div>
          
          <div class="feature-list">
            <div class="feature-item">
              <el-icon color="#67C23A"><Check /></el-icon>
              <span>实验教学全流程管理</span>
            </div>
            <div class="feature-item">
              <el-icon color="#67C23A"><Check /></el-icon>
              <span>设备资源智能调度</span>
            </div>
            <div class="feature-item">
              <el-icon color="#67C23A"><Check /></el-icon>
              <span>数据统计分析报表</span>
            </div>
            <div class="feature-item">
              <el-icon color="#67C23A"><Check /></el-icon>
              <span>五级联动权限管理</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 右侧表单区域 -->
      <div class="auth-form">
        <div class="form-container">
          <router-view />
        </div>
      </div>
    </div>
    
    <!-- 底部版权信息 -->
    <div class="auth-footer">
      <p>&copy; 2025 实验教学管理平台. All rights reserved.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Operation, Check } from '@element-plus/icons-vue'
</script>

<style scoped>
.auth-layout {
  min-height: 100vh;
  position: relative;
  display: flex;
  flex-direction: column;
}

.auth-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  z-index: 0;
}

.bg-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
  background-size: 100px 100px;
}

.auth-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  position: relative;
  z-index: 1;
}

.auth-info {
  flex: 1;
  max-width: 500px;
  padding-right: 60px;
  color: white;
}

.info-content {
  max-width: 400px;
}

.logo-section {
  text-align: center;
  margin-bottom: 60px;
}

.system-title {
  font-size: 32px;
  font-weight: 600;
  margin: 20px 0 10px;
  color: white;
}

.system-subtitle {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
}

.feature-list {
  space-y: 20px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 16px;
  margin-bottom: 20px;
}

.auth-form {
  flex: 0 0 450px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-container {
  width: 100%;
  max-width: 400px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 40px;
}

.auth-footer {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 20px;
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .auth-container {
    flex-direction: column;
    gap: 40px;
  }
  
  .auth-info {
    flex: none;
    padding-right: 0;
    text-align: center;
  }
  
  .auth-form {
    flex: none;
    width: 100%;
    max-width: 400px;
  }
}

@media (max-width: 768px) {
  .auth-container {
    padding: 20px;
  }
  
  .form-container {
    padding: 30px 20px;
  }
  
  .system-title {
    font-size: 24px;
  }
  
  .feature-list {
    display: none;
  }
}

@media (max-width: 480px) {
  .auth-info {
    margin-bottom: 20px;
  }
  
  .logo-section {
    margin-bottom: 30px;
  }
  
  .system-title {
    font-size: 20px;
  }
  
  .system-subtitle {
    font-size: 14px;
  }
}
</style>
