<script setup>
defineProps(['products', 'currentPage', 'lastPage', 'error'])
defineEmits(['changePage', 'addToCart'])
</script>

<template>
  <div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    
    <div v-else>
      <div class="row g-4">
        <div v-for="product in products" :key="product.id" class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm product-card">
            <img :src="product.image ? '/storage/' + product.image : 'https://via.placeholder.com/150'" 
                 class="card-img-top p-3" style="height: 200px; object-fit: contain;">
            <div class="card-body d-flex flex-column text-center">
              <h5 class="card-title text-truncate" :title="product.name">{{ product.name }}</h5>
              <p class="card-text text-danger fw-bold">{{ Number(product.price).toLocaleString() }} đ</p>
              <button @click="$emit('addToCart', product)" class="btn btn-success mt-auto">Thêm vào giỏ</button>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-center gap-3 mt-4" v-if="products.length > 0">
         <button @click="$emit('changePage', currentPage - 1)" :disabled="currentPage === 1" class="btn btn-secondary">Prev</button>
         <span class="align-self-center fw-bold">Trang {{ currentPage }} / {{ lastPage }}</span>
         <button @click="$emit('changePage', currentPage + 1)" :disabled="currentPage === lastPage" class="btn btn-secondary">Next</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-card:hover { transform: translateY(-5px); transition: 0.2s; }
</style>