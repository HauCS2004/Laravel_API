<script setup>
import { ref } from 'vue'

const props = defineProps(['products', 'currentPage', 'lastPage'])
const emit = defineEmits(['changePage', 'addToCart', 'search']) 

// Khai báo biến (Tên biến đặt sao cũng được, quan trọng là lúc emit)
const keyword = ref('') 
const minPrice = ref('')
const maxPrice = ref('')

// Xử lý khi bấm nút Lọc
const handleSearch = () => {
    console.log("1. Đã bấm nút Lọc, dữ liệu là:", { 
        keyword: keyword.value, 
        min_price: minPrice.value, 
        max_price: maxPrice.value 
    });

    emit('search', {
        keyword: keyword.value,
        min_price: minPrice.value,
        max_price: maxPrice.value
    })
}

// Reset form
const resetSearch = () => {
    keyword.value = ''
    minPrice.value = ''
    maxPrice.value = ''
    handleSearch() 
}
</script>

<template>
  <div>
    <div class="card mb-4 shadow-sm bg-light border-0">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input v-model="keyword" type="text" class="form-control" placeholder="🔍 Tìm tên sản phẩm..." @keyup.enter="handleSearch">
                </div>
                <div class="col-md-2">
                    <input v-model="minPrice" type="number" class="form-control" placeholder="Giá từ...">
                </div>
                <div class="col-md-2">
                    <input v-model="maxPrice" type="number" class="form-control" placeholder="Giá đến...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100 fw-bold" @click="handleSearch">Lọc</button>
                    <button class="btn btn-outline-danger" @click="resetSearch">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="products.length > 0" class="row">
      <div class="col-md-3 mb-4" v-for="product in products" :key="product.id">
        <div class="card h-100 shadow-sm hover-shadow">
            <img :src="product.image_url || 'https://placehold.co/300'" 
                 class="card-img-top p-3" 
                 style="height: 200px; object-fit: contain" 
                 alt="...">
            
            <div class="card-body d-flex flex-column">
                <h6 class="card-title text-truncate">{{ product.name }}</h6>
                <p class="card-text text-danger fw-bold fs-5">{{ Number(product.price).toLocaleString() }} đ</p>
                
                <button class="btn btn-outline-primary w-100 mt-auto" @click="$emit('addToCart', product)">
                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                </button>
            </div>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-5">
        <h4 class="text-muted">😢 Không tìm thấy sản phẩm nào!</h4>
        <button class="btn btn-link" @click="resetSearch">Xem tất cả</button>
    </div>

    <div class="d-flex justify-content-center mt-4" v-if="lastPage > 1">
        <button class="btn btn-outline-secondary me-2" :disabled="currentPage === 1" @click="$emit('changePage', currentPage - 1)">« Trước</button>
        <span class="align-self-center fw-bold">Trang {{ currentPage }} / {{ lastPage }}</span>
        <button class="btn btn-outline-secondary ms-2" :disabled="currentPage === lastPage" @click="$emit('changePage', currentPage + 1)">Sau »</button>
    </div>
  </div>
</template>