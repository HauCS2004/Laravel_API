<script setup>
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// Hàm kiểm tra link nào đang active để tô màu
const isActive = (path) => route.path === path

const handleLogout = async () => {
    if(!confirm('Đăng xuất khỏi Admin?')) return;
    try { await axios.post('/api/logout'); } catch(e) {}
    localStorage.removeItem('auth_token');
    window.location.href = '/login'; // Reload lại trang cho sạch
}
</script>

<template>
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-dark text-white text-center py-3">
            <h5 class="mb-0">👑 QUẢN TRỊ</h5>
            <small>Admin Panel</small>
        </div>
        <div class="list-group list-group-flush">
            <router-link to="/admin/dashboard" class="list-group-item list-group-item-action py-3" :class="{ 'active': isActive('/admin/dashboard') }">
                📂 Dashboard
            </router-link>
            <router-link to="/admin/products" class="list-group-item list-group-item-action py-3" :class="{ 'active': isActive('/admin/products') }">
                📦 Sản Phẩm
            </router-link>

            <router-link to="/admin/orders" class="list-group-item list-group-item-action py-3" :class="{ 'active': isActive('/admin/orders') }">
                📄 Đơn Hàng
            </router-link>
            <router-link to="/admin/categories" class="list-group-item list-group-item-action py-3" :class="{ 'active': isActive('/admin/categories') }">
                📂 Danh Mục
            </router-link>
            
            <router-link to="/" class="list-group-item list-group-item-action py-3 text-success">
                🏠 Về Shop Bán Hàng
            </router-link>

            
        </div>
    </div>
</template>

<style scoped>
/* Tô màu cho nút đang chọn */
.active {
    background-color: #0d6efd;
    color: white;
    font-weight: bold;
    border-color: #0d6efd;
}
</style>