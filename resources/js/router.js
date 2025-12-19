import { createRouter, createWebHistory } from 'vue-router'

// Import các component
import ProductList from './components/ProductList.vue'
import ShoppingCart from './components/ShoppingCart.vue'
import Login from './components/Login.vue'
import Checkout from './components/Checkout.vue'
import OrderHistory from './components/OrderHistory.vue'

const routes = [
    { path: '/', component: ProductList, name: 'products' },
    { path: '/cart', component: ShoppingCart, name: 'cart' },
    { path: '/login', component: Login, name: 'login' },
    { path: '/checkout', component: Checkout, name: 'checkout' },
    { path: '/orders', component: OrderHistory, name: 'orders' }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router