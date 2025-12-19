<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const orders = ref([])
const loading = ref(true)

const fetchOrders = async () => {
  try {
    // Gọi API lấy lịch sử đơn hàng
    const res = await axios.get('/api/orders')
    // API của bạn trả về data phân trang, nên lấy res.data.data.data
    orders.value = res.data.data.data 
  } catch (err) {
    alert('Lỗi tải đơn hàng')
  } finally {
    loading.value = false
  }
}

// Hàm format ngày tháng cho đẹp
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('vi-VN')
}

// Hàm hủy đơn (chỉ hủy khi pending)
const cancelOrder = async (id) => {
    if(!confirm('Chắc chắn hủy đơn này?')) return;
    try {
        await axios.put(`/api/orders/${id}/cancel`);
        alert('Đã hủy đơn hàng!');
        fetchOrders(); // Tải lại danh sách
    } catch (err) {
        alert(err.response?.data?.message || 'Lỗi hủy đơn');
    }
}

onMounted(() => {
  fetchOrders()
})
</script>

<template>
  <div class="container">
    <h3 class="mb-4 text-primary">📦 Lịch sử đơn hàng của tôi</h3>

    <div v-if="loading" class="text-center">Đang tải...</div>
    
    <div v-else-if="orders.length === 0" class="text-center py-5">
        <p>Bạn chưa mua đơn nào cả! 😢</p>
    </div>

    <div v-else>
      <div v-for="order in orders" :key="order.id" class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <div>
                <strong>Mã đơn: #{{ order.id }}</strong>
                <span class="text-muted ms-2">({{ formatDate(order.created_at) }})</span>
            </div>
            
            <div>
                <span v-if="order.status === 'pending'" class="badge bg-warning text-dark">Chờ xử lý</span>
                <span v-else-if="order.status === 'paid'" class="badge bg-primary">✅ Đã thanh toán (VNPAY)</span>
                <span v-else-if="order.status === 'shipping'" class="badge bg-info">Đang giao</span>
                <span v-else-if="order.status === 'completed'" class="badge bg-success">Hoàn thành</span>
                <span v-else class="badge bg-secondary">Đã hủy</span>
            </div>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li v-for="item in order.order_items" :key="item.id" class="list-group-item d-flex justify-content-between">
                    <div>
                        {{ item.product ? item.product.name : 'Sản phẩm đã xóa' }}
                        <small class="text-muted">x {{ item.quantity }}</small>
                    </div>
                    <span>{{ Number(item.price * item.quantity).toLocaleString() }} đ</span>
                </li>
            </ul>

            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <h5 class="text-danger mb-0">Tổng tiền: {{ Number(order.total_price).toLocaleString() }} đ</h5>
                
                <button v-if="order.status === 'pending'" @click="cancelOrder(order.id)" class="btn btn-outline-danger btn-sm">
                    Hủy đơn hàng
                </button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>