<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const emit = defineEmits(['register-submit'])

// 1. Dữ liệu Form
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

// 2. Biến lưu lỗi (Nếu có lỗi thì nó sẽ chui vào đây)
const errors = ref({
    name: '',
    email: '',
    password: '',
    passwordConfirmation: ''
})

// 3. Hàm kiểm tra hợp lệ (Validate)
const validate = () => {
    let isValid = true;
    
    // Reset lỗi cũ trước khi kiểm tra mới
    errors.value = {
        name: '',
        email: '',
        password: '',
        passwordConfirmation: ''
    }

    // --- Check Tên ---
    if (!name.value) {
        errors.value.name = 'Vui lòng nhập họ tên!';
        isValid = false;
    }

    // --- Check Email ---
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value) {
        errors.value.email = 'Vui lòng nhập email!';
        isValid = false;
    } else if (!emailRegex.test(email.value)) {
        errors.value.email = 'Email không đúng định dạng!';
        isValid = false;
    }

    // --- Check Mật khẩu ---
    if (!password.value) {
        errors.value.password = 'Vui lòng nhập mật khẩu!';
        isValid = false;
    } else if (password.value.length < 6) {
        errors.value.password = 'Mật khẩu phải có ít nhất 6 ký tự!';
        isValid = false;
    }

    // --- Check Nhập lại mật khẩu ---
    if (password.value !== passwordConfirmation.value) {
        errors.value.passwordConfirmation = 'Mật khẩu nhập lại không khớp!';
        isValid = false;
    }

    return isValid;
}

const handleRegister = () => {
    // Gọi hàm validate trước
    if (validate()) {
        // Nếu OK hết thì mới gửi dữ liệu đi
        emit('register-submit', { 
            name: name.value, 
            email: email.value, 
            password: password.value, 
            password_confirmation: passwordConfirmation.value 
        })
    }
}
</script>

<template>
  <div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow border-0">
        <div class="card-body p-4">
          <h3 class="text-center text-primary mb-4 fw-bold">Đăng Ký Tài Khoản</h3>
          
          <form @submit.prevent="handleRegister">
            <div class="mb-3">
              <label class="form-label">Họ và tên</label>
              <input 
                v-model="name" 
                type="text" 
                class="form-control" 
                :class="{ 'is-invalid': errors.name }" 
                placeholder="Nguyễn Văn A"
              >
              <div class="invalid-feedback">{{ errors.name }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input 
                v-model="email" 
                type="email" 
                class="form-control" 
                :class="{ 'is-invalid': errors.email }"
                placeholder="admin@gmail.com"
              >
              <div class="invalid-feedback">{{ errors.email }}</div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Mật khẩu</label>
              <input 
                v-model="password" 
                type="password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.password }"
                placeholder="******"
              >
              <div class="invalid-feedback">{{ errors.password }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Nhập lại mật khẩu</label>
              <input 
                v-model="passwordConfirmation" 
                type="password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.passwordConfirmation }"
                placeholder="******"
              >
              <div class="invalid-feedback">{{ errors.passwordConfirmation }}</div>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-3 fw-bold">Đăng ký ngay</button>
            
            <div class="text-center">
              <span>Đã có tài khoản? </span>
              <a href="#" class="text-decoration-none" @click.prevent="router.push('/login')">Đăng nhập</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>