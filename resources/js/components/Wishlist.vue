<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from "vue-toastification";

const wishlistItems = ref([]);
const toast = useToast();
const emit = defineEmits(['addToCart']); // Để gọi hàm thêm giỏ của App.vue

// 1. Lấy danh sách
const fetchWishlist = async () => {
    try {
        const res = await axios.get('/api/wishlist');
        wishlistItems.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

// 2. Xóa khỏi Wishlist
const removeWishlist = async (id) => {
    try {
        // Gọi lại API toggle (vì logic toggle là: có rồi bấm cái nữa là xóa)
        // Hoặc Hậu viết hàm delete riêng cũng được, ở đây mình tận dụng hàm toggle
        await axios.post('/api/wishlist/toggle', { product_id: id });
        
        // Xóa trên giao diện luôn cho nhanh, khỏi gọi lại API
        wishlistItems.value = wishlistItems.value.filter(item => item.product.id !== id);
        toast.info("Đã xóa khỏi yêu thích");
    } catch (e) {
        toast.error("Lỗi xóa");
    }
};

// 3. Chuyển sang giỏ hàng
const moveToCart = (product) => {
    emit('addToCart', product); // Gọi hàm thêm giỏ
    removeWishlist(product.id); // Thêm xong thì xóa khỏi wishlist luôn cho gọn (tùy Hậu chọn)
};

onMounted(() => {
    fetchWishlist();
});
</script>

<template>
    <div class="container py-4">
        <h2>❤️ Danh sách yêu thích</h2>
        
        <div v-if="wishlistItems.length > 0" class="row mt-4">
            <div class="col-md-12">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in wishlistItems" :key="item.id">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img :src="item.product.image_url" width="60" class="rounded">
                                    <div>
                                        <h6 class="mb-0">{{ item.product.name }}</h6>
                                        <small v-if="item.product.stock > 0" class="text-success">Còn hàng</small>
                                        <small v-else class="text-danger">Hết hàng</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-danger fw-bold">{{ Number(item.product.price).toLocaleString() }} đ</td>
                            <td>
                                <button class="btn btn-primary btn-sm me-2" 
                                        @click="moveToCart(item.product)"
                                        :disabled="item.product.stock <= 0">
                                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                </button>
                                <button class="btn btn-outline-danger btn-sm" @click="removeWishlist(item.product.id)">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div v-else class="text-center py-5">
            <i class="bi bi-heart-break fs-1 text-muted"></i>
            <p class="mt-3">Chưa có sản phẩm yêu thích nào.</p>
            <router-link to="/" class="btn btn-outline-primary">Đi dạo mua sắm ngay</router-link>
        </div>
    </div>
</template>