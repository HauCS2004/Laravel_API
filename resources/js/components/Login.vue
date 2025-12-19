<script setup>
import { ref } from 'vue'

const email = ref('')
const password = ref('')
const emit = defineEmits(['login-success', 'switch-to-register'])

const handleLogin = async () => {
  // Gửi sự kiện ra App cha để xử lý API
  emit('login-success', { email: email.value, password: password.value })
}
</script>

<template>
  <div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow">
        <div class="card-body p-4">
          <h3 class="text-center text-primary mb-4">Đăng Nhập</h3>
          
          <form @submit.prevent="handleLogin">
            <div class="mb-3">
              <label>Email</label>
              <input v-model="email" type="email" class="form-control" required placeholder="admin@gmail.com">
            </div>
            
            <div class="mb-3">
              <label>Mật khẩu</label>
              <input v-model="password" type="password" class="form-control" required placeholder="123456">
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Đăng nhập</button>
            
            <div class="text-center">
              <span>Chưa có tài khoản? </span>
              <a href="#" @click.prevent="$emit('switch-to-register')">Đăng ký ngay</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>