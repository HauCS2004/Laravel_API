<script setup>
import { ref, onMounted, computed } from 'vue'; // 1. Nhớ thêm computed
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from "vue-toastification";

const route = useRoute();
const toast = useToast();

const product = ref(null);
const comments = ref([]);
const isWishlist = ref(false);
const isLoading = ref(true);
const emit = defineEmits(['addToCart']);
const newComment = ref({ content: '', rating: 5 });

// --- 2. LOGIC BIẾN 1 ẢNH THÀNH DANH SÁCH ẢNH (QUAN TRỌNG) ---
const productImages = computed(() => {
    if (!product.value) return [];
    
    
    const mainImage = product.value.image_url || 'https://placehold.co/400';
    
    // Logic mở rộng: Nếu sau này ông lén sửa DB lưu dạng JSON mảng, đoạn này sẽ tự parse
    // Còn hiện tại nó sẽ chạy dòng 'return [mainImage]'
    const rawImage = product.value.image;
    if (rawImage && typeof rawImage === 'string' && rawImage.startsWith('[')) {
        try {
            
             const parsed = JSON.parse(rawImage);
             if (Array.isArray(parsed) && parsed.length > 0) {
                 return parsed.map(path => path.startsWith('http') ? path : `/storage/${path}`);
             }
        } catch (e) {}
    }

    
    return [mainImage];
});
// -----------------------------------------------------------

const fetchProductDetail = async () => {
    try {
        const res = await axios.get(`/api/products/${route.params.id}`);
        product.value = res.data.product;
        comments.value = res.data.product.comments;
        isWishlist.value = res.data.is_wishlist;
    } catch (e) {
        toast.error("Không tìm thấy sản phẩm!");
    } finally {
        isLoading.value = false;
    }
};

const toggleWishlist = async () => {
    try {
        const res = await axios.post('/api/wishlist/toggle', { product_id: product.value.id });
        if (res.data.status === 'added') {
            isWishlist.value = true;
            toast.success("❤️ Đã thích sản phẩm!");
        } else {
            isWishlist.value = false;
            toast.info("💔 Đã bỏ thích.");
        }
    } catch (e) {
        if(e.response && e.response.status === 401) {
            toast.warning("Vui lòng đăng nhập để lưu sản phẩm!");
        } else {
            toast.error("Lỗi kết nối server");
        }
    }
};

const submitComment = async () => {
    if (!newComment.value.content) return toast.warning("Vui lòng nhập nội dung!");
    try {
        const res = await axios.post('/api/comments', {
            product_id: product.value.id,
            content: newComment.value.content,
            rating: newComment.value.rating
        });
        comments.value.unshift(res.data);
        newComment.value.content = '';
        toast.success("Cảm ơn đánh giá của bạn!");
    } catch (e) {
        toast.error("Lỗi khi gửi bình luận");
    }
};

const addToCart = async () => {
     emit('addToCart', product.value);
     toast.success("Đã thêm vào giỏ hàng!");
}

onMounted(() => {
    fetchProductDetail();
});
</script>

<template>
    <div class="container mt-4" v-if="!isLoading && product">
        <div class="row shadow-sm bg-white p-4 rounded">
            
            <div class="col-md-5">
                <div id="productCarousel" class="carousel slide border rounded overflow-hidden" data-bs-ride="carousel">
                    
                    <div class="carousel-inner">
                        <div 
                            v-for="(imgUrl, index) in productImages" 
                            :key="index" 
                            class="carousel-item" 
                            :class="{ active: index === 0 }"
                        >
                            <img :src="imgUrl" class="d-block w-100" style="height: 400px; object-fit: contain;" alt="Product Image">
                        </div>
                    </div>

                    <button v-if="productImages.length > 1" class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                    </button>
                    <button v-if="productImages.length > 1" class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                    </button>

                    <div class="d-flex mt-2 gap-2 overflow-auto px-1 pb-2" v-if="productImages.length > 1">
                        <img 
                            v-for="(imgUrl, index) in productImages" 
                            :key="index"
                            :src="imgUrl"
                            class="border rounded cursor-pointer"
                            style="width: 60px; height: 60px; object-fit: cover;"
                            :class="{ 'border-primary border-2': index === 0 }"
                            data-bs-target="#productCarousel" 
                            :data-bs-slide-to="index"
                        >
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <h2 class="fw-bold">{{ product.name }}</h2>
                <div class="text-warning mb-2">
                    <i class="bi bi-star-fill" v-for="n in 5" :key="n"></i> 
                    <span class="text-muted ms-2">({{ comments.length }} đánh giá)</span>
                </div>
                
                <h3 class="text-danger fw-bold my-3">{{ Number(product.price).toLocaleString() }} đ</h3>
                <div class="text-muted mb-3">
                    <i class="bi bi-eye"></i> {{ product.views }} lượt xem
                </div>
                <p class="text-muted">{{ product.description || 'Chưa có mô tả chi tiết cho sản phẩm này.' }}</p>
                <p>Tồn kho: <strong>{{ product.stock }}</strong></p>

                <div class="d-flex gap-3 mt-4">
                    <button class="btn btn-primary btn-lg " @click="addToCart" :disabled="product.stock <= 0">
                        <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                    </button>

                        <button class="btn btn-lg border" 
                            :class="isWishlist ? 'btn-danger text-white' : 'btn-outline-danger'"
                            @click="toggleWishlist">
                            <i class="bi" :class="isWishlist ? 'bi-heart-fill' : 'bi-heart'"></i>
                        </button>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-3">Đánh giá & Bình luận</h4>
                
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="fw-bold me-2">Đánh giá:</label>
                            <select v-model="newComment.rating" class="form-select d-inline-block w-auto">
                                <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                                <option value="3">⭐⭐⭐ (Bình thường)</option>
                                <option value="2">⭐⭐ (Tệ)</option>
                                <option value="1">⭐ (Rất tệ)</option>
                            </select>
                        </div>
                        <textarea v-model="newComment.content" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                        <button class="btn btn-success mt-2" @click="submitComment">Gửi đánh giá</button>
                    </div>
                </div>

                <div v-if="comments.length > 0">
                    <div v-for="comment in comments" :key="comment.id" class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>{{ comment.user ? comment.user.name : 'Người dùng ẩn danh' }}</strong>
                            <span class="text-warning">
                                <span v-for="n in comment.rating">★</span>
                            </span>
                        </div>
                        <p class="mb-1">{{ comment.content }}</p>
                        <small class="text-muted">{{ new Date(comment.created_at).toLocaleString() }}</small>
                    </div>
                </div>
                <div v-else class="text-center text-muted py-3">
                    Chưa có đánh giá nào. Hãy là người đầu tiên!
                </div>
            </div>
        </div>
    </div>
    
    <div v-else class="text-center mt-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p>Đang tải chi tiết...</p>
    </div>
</template>