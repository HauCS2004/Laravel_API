<script setup>
import { ref, computed, onMounted } from 'vue' // Thêm computed
import axios from 'axios'

// --- 1. PHẦN SẢN PHẨM (Giữ nguyên) ---
const products = ref([]) 
const error = ref(null)
const currentPage = ref(1)
const lastPage = ref(1)

const fetchProducts = async (page = 1) => {
  try {
    const response = await axios.get('/api/products?page=' + page)
    products.value = response.data.data.data 
    currentPage.value = response.data.data.current_page
    lastPage.value = response.data.data.last_page
  } catch (err) {
    error.value = 'Lỗi SP: ' + err.message
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= lastPage.value) fetchProducts(page)
}

const addToCart = async (product) => {
  try {
    await axios.post('/api/cart', { product_id: product.id, quantity: 1 });
    alert(`✅ Đã thêm "${product.name}" vào giỏ!`);
    fetchCart(); // Thêm xong thì cập nhật lại giỏ hàng luôn
  } catch (err) {
    alert('❌ Lỗi thêm giỏ: ' + err.message);
  }
}

// --- 2. PHẦN GIỎ HÀNG (Mới thêm) ---
const cartItems = ref([])
const isShowCart = ref(false) // Biến để bật/tắt màn hình giỏ hàng

// Hàm lấy danh sách giỏ hàng
const fetchCart = async () => {
  try {
    const response = await axios.get('/api/cart');
    // 👇 Lưu ý: Kiểm tra xem API của bạn trả về data ở lớp nào nhé
    // Mình đang giả định nó trả về mảng trực tiếp hoặc trong .data
    cartItems.value = response.data.data || response.data; 
    console.log("Giỏ hàng:", cartItems.value);
  } catch (err) {
    console.error("Lỗi lấy giỏ hàng:", err);
  }
}

// Hàm xóa sản phẩm khỏi giỏ (API DELETE)
const removeFromCart = async (id) => {
  if(!confirm("Bạn chắc chắn muốn xóa món này?")) return;
  try {
    await axios.delete('/api/cart/' + id);
    fetchCart(); // Xóa xong load lại danh sách
  } catch (err) {
    alert('Lỗi xóa: ' + err.message);
  }
}
// Hàm cập nhật số lượng (Tăng hoặc Giảm)
const updateQuantity = async (item, change) => {
  const newQuantity = item.quantity + change;
  
  // Nếu giảm xuống dưới 1 thì chặn lại (hoặc bạn có thể hỏi user muốn xóa không)
  if (newQuantity < 1) return;

  try {
    // Gọi API cập nhật (PUT /api/cart/{id})
    // Body gửi lên: { quantity: ... }
    await axios.put('/api/cart/' + item.id, { 
      quantity: newQuantity 
    });
    
    // Cập nhật thành công thì tải lại giỏ hàng để tính lại tổng tiền
    fetchCart(); 
    
  } catch (err) {
    console.error(err);
    alert('❌ Lỗi cập nhật số lượng!');
  }
}

// Hàm tính Tổng tiền (Tự động tính lại khi giỏ hàng thay đổi)
const totalAmount = computed(() => {
  return cartItems.value.reduce((sum, item) => {
    // Giả sử API trả về cấu trúc item.product.price và item.quantity
    // Bạn cần kiểm tra lại console.log để sửa đoạn này cho khớp
    return sum + (item.product.price * item.quantity);
  }, 0);
});

onMounted(() => {
  fetchProducts();
  fetchCart(); // Load giỏ hàng ngay khi vào trang
})
</script>

<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">🛍️ Shop Của Hậu</h1>
      
      <button class="btn btn-primary position-relative" @click="isShowCart = !isShowCart">
        <span v-if="!isShowCart">🛒 Xem Giỏ Hàng</span>
        <span v-else>⬅️ Tiếp tục mua sắm</span>
        
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          {{ cartItems.length }}
        </span>
      </button>
    </div>
    
    <div v-if="!isShowCart">
       <div v-if="error" class="alert alert-danger">{{ error }}</div>
       <div v-else>
          <div class="row g-4">
            <div v-for="product in products" :key="product.id" class="col-6 col-md-4 col-lg-3">
              <div class="card h-100 shadow-sm">
                <img :src="product.image ? '/storage/' + product.image : 'https://via.placeholder.com/150'" 
                     class="card-img-top p-3" style="height: 200px; object-fit: contain;">
                <div class="card-body d-flex flex-column text-center">
                  <h5 class="card-title text-truncate">{{ product.name }}</h5>
                  <p class="card-text text-danger fw-bold">{{ Number(product.price).toLocaleString() }} đ</p>
                  <button @click="addToCart(product)" class="btn btn-success mt-auto">Thêm vào giỏ</button>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-center gap-3 mt-4">
             <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="btn btn-secondary">Prev</button>
             <span>Trang {{ currentPage }}</span>
             <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" class="btn btn-secondary">Next</button>
          </div>
       </div>
    </div>

    <div v-else>
      <div v-if="cartItems.length === 0" class="text-center py-5">
        <h3>Giỏ hàng đang trống trơn! 😢</h3>
        <button class="btn btn-outline-primary mt-3" @click="isShowCart = false">Quay lại mua hàng</button>
      </div>

      <div v-else>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
              <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Xóa</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cartItems" :key="item.id">
                <td class="text-center">
                   <img :src="item.product && item.product.image ? '/storage/' + item.product.image : 'https://via.placeholder.com/50'" width="50">
                </td>
                <td>{{ item.product ? item.product.name : 'Sản phẩm lỗi' }}</td>
                <td class="text-end">{{ item.product ? Number(item.product.price).toLocaleString() : 0 }} đ</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center align-items-center gap-2">
                    <button 
                      @click="updateQuantity(item, -1)" 
                      class="btn btn-sm btn-outline-secondary" 
                      :disabled="item.quantity <= 1"
                    >
                      ➖
                    </button>

                    <span class="fw-bold px-2" style="min-width: 30px;">
                      {{ item.quantity }}
                    </span>

                    <button 
                      @click="updateQuantity(item, 1)" 
                      class="btn btn-sm btn-outline-secondary"
                    >
                      ➕
                    </button>
                  </div>
                </td>
                <td class="text-end fw-bold">
                  {{ item.product ? Number(item.product.price * item.quantity).toLocaleString() : 0 }} đ
                </td>
                <td class="text-center">
                  <button @click="removeFromCart(item.id)" class="btn btn-sm btn-danger">❌</button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end fw-bold fs-5">TỔNG CỘNG:</td>
                <td colspan="2" class="text-danger fw-bold fs-4">{{ Number(totalAmount).toLocaleString() }} đ</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-warning btn-lg">💳 Thanh toán ngay</button>
        </div>
      </div>
    </div>

  </div>
</template>