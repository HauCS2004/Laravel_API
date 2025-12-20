<script setup>
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'
import AdminSidebar from './AdminSidebar.vue'

const products = ref([])
const categories = ref([]) // 1. Thêm biến lưu danh sách danh mục
const isEditing = ref(false)
const showModal = ref(false)

// Form data (Thêm category_id)
const form = reactive({
    id: null,
    name: '',
    price: '',
    description: '',
    category_id: '', // 2. Bắt buộc phải có
    image: null,      
    image_url: ''    
})

// Lấy danh sách sản phẩm
const fetchProducts = async () => {
    try {
        const res = await axios.get('/api/products')
        // Tùy cấu trúc paginate hay list thường mà bạn chỉnh dòng này nhé
        products.value = res.data.data.data || res.data.data 
    } catch (e) {
        console.error('Lỗi tải SP:', e)
    }
}

// 3. Lấy danh sách danh mục (Để đổ vào Dropdown)
const fetchCategories = async () => {
    try {
        // Giả sử bạn có API này. Nếu chưa có thì xem hướng dẫn bên dưới code nhé!
        const res = await axios.get('/api/categories') 
        categories.value = res.data.data || res.data 
    } catch (e) {
        console.error('Chưa có API Categories, dùng tạm data giả')
        // Data giả để test nếu chưa có API Category
        categories.value = [
            { id: 1, name: 'Điện thoại' },
            { id: 2, name: 'Laptop' },
            { id: 3, name: 'Phụ kiện' }
        ]
    }
}

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.image = file
        form.image_url = URL.createObjectURL(file)
    }
}

const openModal = (product = null) => {
    showModal.value = true
    if (product) {
        // SỬA
        isEditing.value = true
        form.id = product.id
        form.name = product.name
        form.price = product.price
        form.description = product.description
        form.category_id = product.category_id // Load danh mục cũ
        
        // Backend lưu 'upload/abc.jpg', Frontend cần thêm '/storage/'
        form.image_url = product.image ? `/storage/${product.image}` : ''
        form.image = null
    } else {
        // THÊM
        isEditing.value = false
        form.id = null
        form.name = ''
        form.price = ''
        form.description = ''
        form.category_id = '' // Reset danh mục
        form.image_url = ''
        form.image = null
    }
}

const saveProduct = async () => {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('price', form.price)
    formData.append('description', form.description || '')
    formData.append('category_id', form.category_id) // 4. Gửi category_id lên

    if (form.image) {
        formData.append('image', form.image)
    }

    try {
        if (isEditing.value) {
            formData.append('_method', 'PUT') 
            await axios.post(`/api/products/${form.id}`, formData)
            alert('✅ Cập nhật thành công!')
        } else {
            await axios.post('/api/products', formData)
            alert('✅ Thêm mới thành công!')
        }
        showModal.value = false
        fetchProducts()
    } catch (e) {
        console.error(e)
        // Hiển thị lỗi chi tiết từ Backend trả về (nếu có)
        const errors = e.response?.data?.errors
        if (errors) {
            alert('❌ Lỗi: ' + Object.values(errors).join('\n'))
        } else {
            alert('❌ Lỗi xử lý: ' + (e.response?.data?.message || e.message))
        }
    }
}

const deleteProduct = async (id) => {
    if (!confirm('Xóa thật không?')) return
    try {
        await axios.delete(`/api/products/${id}`)
        fetchProducts()
    } catch (e) {
        alert('Lỗi xóa')
    }
}

// Hàm helper để hiển thị ảnh
const getImageUrl = (path) => {
    if (!path) return 'https://placehold.co/50'; // Ảnh mặc định nếu null
    if (path.startsWith('http')) return path; // Nếu là link online
    return `/storage/${path}`; // Nếu là path trong storage
}

// Tên danh mục hiển thị
const getCategoryName = (id) => {
    const cat = categories.value.find(c => c.id === id)
    return cat ? cat.name : '---'
}

onMounted(() => {
    fetchProducts()
    fetchCategories() // Gọi hàm lấy danh mục
})
</script>


<template>
  <div class="container-fluid mt-4">
      <div class="row">
          
          <div class="col-md-3 col-lg-2 px-0">
              <AdminSidebar />
          </div>

          <div class="col-md-9 col-lg-10">
              
              <div class="card shadow border-0">
                  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">📦 Quản Lý Sản Phẩm</h5>
                      <button class="btn btn-light text-primary fw-bold" @click="openModal(null)">+ Thêm Mới</button>
                  </div>

                  <div class="card-body p-0">
                      <div class="table-responsive">
                          <table class="table table-hover align-middle mb-0">
                              <thead class="bg-light">
                                  <tr>
                                      <th>Ảnh</th>
                                      <th>Tên sản phẩm</th>
                                      <th>Danh mục</th>
                                      <th>Giá</th>
                                      <th>Hành động</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr v-for="p in products" :key="p.id">
                                      <td>
                                          <img :src="getImageUrl(p.image)" class="rounded border" width="50" height="50" style="object-fit: cover;">
                                      </td>
                                      <td>
                                          <div class="fw-bold">{{ p.name }}</div>
                                          <small class="text-muted text-truncate d-block" style="max-width: 200px;">{{ p.description }}</small>
                                      </td>
                                      <td>
                                          <span class="badge bg-info text-dark">{{ getCategoryName(p.category_id) }}</span>
                                      </td>
                                      <td class="text-danger fw-bold">{{ Number(p.price).toLocaleString() }} đ</td>
                                      <td>
                                          <button class="btn btn-sm btn-outline-primary me-2" @click="openModal(p)">✏️</button>
                                          <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(p.id)">🗑️</button>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              </div>
      </div>

      <div v-if="showModal" class="modal-backdrop fade show"></div>
      <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">{{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}</h5>
                      <button type="button" class="btn-close" @click="showModal = false"></button>
                  </div>
                  <div class="modal-body">
                      <form @submit.prevent="saveProduct">
                          <div class="mb-3">
                              <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                              <select v-model="form.category_id" class="form-select" required>
                                  <option value="" disabled>-- Chọn danh mục --</option>
                                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                      {{ cat.name }}
                                  </option>
                              </select>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Tên sản phẩm</label>
                              <input v-model="form.name" type="text" class="form-control" required>
                          </div>
                          
                          <div class="mb-3">
                              <label class="form-label">Giá</label>
                              <input v-model="form.price" type="number" class="form-control" required>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Mô tả</label>
                              <textarea v-model="form.description" class="form-control"></textarea>
                          </div>
                          
                          <div class="mb-3">
                              <label class="form-label">Hình ảnh</label>
                              <input type="file" class="form-control" @change="handleFileUpload" accept="image/*">
                              <div v-if="form.image_url" class="mt-2 text-center">
                                  <img :src="form.image_url" class="img-thumbnail" style="max-height: 150px">
                              </div>
                          </div>

                          <div class="text-end">
                              <button type="button" class="btn btn-secondary me-2" @click="showModal = false">Hủy</button>
                              <button type="submit" class="btn btn-primary">Lưu lại</button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<style scoped>
.modal-backdrop { opacity: 0.5; background-color: #000; }
</style>