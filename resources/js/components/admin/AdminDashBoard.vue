<script setup>
import AdminSidebar from './AdminSidebar.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
    revenue: 0,
    pending_orders: 0,
    total_products: 0,
    total_users: 0
})

const fetchStats = async () => {
    try {
        const res = await axios.get('/api/dashboard')
         stats.value = res.data.data
        console.log("api tra ve",res.data);
    } catch (e) {
        console.error('Lỗi tải thống kê')
    }
}

onMounted(() => fetchStats())
</script>

<template>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0">
            <AdminSidebar />
        </div>

        <div class="col-md-9 col-lg-10">
            <h3 class="mb-4 fw-bold text-secondary">📊 Tổng Quan Kinh Doanh</h3>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-primary text-white h-100 shadow border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2 opacity-75">Doanh Thu</h6>
                            <h3 class="fw-bold mb-0">
                                {{ Number(stats.revenue).toLocaleString() }} đ
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-warning text-dark h-100 shadow border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2 opacity-75">Đơn Chờ Xử Lý</h6>
                            <h3 class="fw-bold mb-0">
                                {{ stats.pending_orders }} đơn
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-success text-white h-100 shadow border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2 opacity-75">Sản Phẩm</h6>
                            <h3 class="fw-bold mb-0">
                                {{ stats.total_products }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-info text-dark h-100 shadow border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-2 opacity-75">Khách Hàng</h6>
                            <h3 class="fw-bold mb-0">
                                {{ stats.total_users }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 text-center text-muted">
                <i class="bi bi-bar-chart-line display-1"></i>
                <p>Khu vực dành cho biểu đồ tăng trưởng (Coming soon)</p>
            </div>

        </div>
    </div>
</div>
</template>