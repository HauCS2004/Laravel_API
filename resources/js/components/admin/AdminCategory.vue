<script setup>
import AdminSidebar from './AdminSidebar.vue' // Nhớ import Sidebar
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'

const categories = ref([])
const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
    id: null,
    name: ''
})

// 1. Lấy danh sách
const fetchCategories = async () => {
    try {
        const res = await axios.get('/api/categories')
        categories.value = res.data.data
    } catch (e) {
        alert('Lỗi tải danh mục')
    }
}

// 2. Mở modal
const openModal = (cat = null) => {
    showModal.value = true
    if (cat) {
        isEditing.value = true
        form.id = cat.id
        form.name = cat.name
    } else {
        isEditing.value = false
        form.id = null
        form.name = ''
    }
}

// 3. Lưu (Thêm/Sửa)
const saveCategory = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/categories/${form.id}`, { name: form.name })
            alert('✅ Cập nhật thành công!')
        } else {
            await axios.post('/api/categories', { name: form.name })
            alert('✅ Thêm mới thành công!')
        }
        showModal.value = false
        fetchCategories()
    } catch (e) {
        const msg = e.response?.data?.errors?.name?.[0] || 'Lỗi xử lý'
        alert('❌ ' + msg)
    }
}

// 4. Xóa
const deleteCategory = async (id) => {
    if(!confirm('Xóa danh mục này? (Sản phẩm thuộc danh mục này có thể bị lỗi)')) return;
    try {
        await axios.delete(`/api/categories/${id}`)
        fetchCategories()
    } catch (e) {
        alert('Lỗi xóa')
    }
}

onMounted(() => fetchCategories())
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
                    <h5 class="mb-0">📂 Quản Lý Danh Mục</h5>
                    <button class="btn btn-light text-primary fw-bold" @click="openModal(null)">+ Thêm Mới</button>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cat in categories" :key="cat.id">
                                <td>#{{ cat.id }}</td>
                                <td class="fw-bold">{{ cat.name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" @click="openModal(cat)">✏️ Sửa</button>
                                    <button class="btn btn-sm btn-outline-danger me-3" @click="deleteCategory(cat.id)">🗑️ Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showModal" class="modal-backdrop fade show"></div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog modal-sm"> <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEditing ? 'Sửa Danh Mục' : 'Thêm Danh Mục' }}</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveCategory">
                        <div class="mb-3">
                            <label>Tên danh mục</label>
                            <input v-model="form.name" type="text" class="form-control" required placeholder="Ví dụ: Laptop...">
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" @click="showModal = false">Hủy</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
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