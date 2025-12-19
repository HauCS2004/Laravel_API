<script setup>
defineProps(['cartItems', 'totalAmount'])
defineEmits(['removeFromCart', 'updateQuantity', 'checkout']) // Thêm sự kiện checkout
</script>

<template>
  <div>
    <div v-if="cartItems.length === 0" class="text-center py-5">
      <h3>Giỏ hàng đang trống! 😢</h3>
    </div>

    <div v-else>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light text-center">
            <tr><th>Ảnh</th><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Thành tiền</th><th>Xóa</th></tr>
          </thead>
          <tbody>
            <tr v-for="item in cartItems" :key="item.id">
              <td class="text-center">
                 <img :src="item.product?.image ? '/storage/' + item.product.image : 'https://via.placeholder.com/50'" width="50">
              </td>
              <td>{{ item.product?.name }}</td>
              <td class="text-end">{{ Number(item.product?.price).toLocaleString() }} đ</td>
              
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <button @click="$emit('updateQuantity', item, -1)" class="btn btn-sm btn-outline-secondary" :disabled="item.quantity <= 1">➖</button>
                  <span class="fw-bold">{{ item.quantity }}</span>
                  <button @click="$emit('updateQuantity', item, 1)" class="btn btn-sm btn-outline-secondary">➕</button>
                </div>
              </td>
              
              <td class="text-end fw-bold">{{ Number(item.product?.price * item.quantity).toLocaleString() }} đ</td>
              <td class="text-center"><button @click="$emit('removeFromCart', item.id)" class="btn btn-sm btn-danger">❌</button></td>
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
        <button class="btn btn-warning btn-lg shadow" @click="$emit('checkout')">💳 Thanh toán ngay</button>
      </div>
    </div>
  </div>
</template>