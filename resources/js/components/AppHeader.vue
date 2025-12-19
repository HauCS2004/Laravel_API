<script setup>
defineProps(['cartCount', 'isShowCart'])
defineEmits(['toggleCart']) // Gửi sự kiện bấm nút về App cha
</script>

<template>
  <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded">
    <h1 class="h3 m-0 text-primary">🛍️ Shop Của Hậu</h1>
    
    <button class="btn btn-primary position-relative" @click="$emit('toggleCart')">
      <span v-if="!isShowCart">🛒 Xem Giỏ Hàng</span>
      <span v-else>⬅️ Tiếp tục mua sắm</span>
      
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ cartCount }}
      </span>
    </button>
  </div>
</template>