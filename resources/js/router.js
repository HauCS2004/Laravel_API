import { createRouter, createWebHistory } from 'vue-router'

// Import các component
import ProductList from './components/ProductList.vue'
import ShoppingCart from './components/ShoppingCart.vue'
import Login from './components/Login.vue'
import Checkout from './components/Checkout.vue'
import OrderHistory from './components/OrderHistory.vue'
import Register from './components/Register.vue'
import AdminOrders from './components/admin/AdminOrders.vue'
import AdminProducts from './components/admin/AdminProducts.vue'
import AdminCategory from './components/admin/AdminCategory.vue'
import AdminDashboard from './components/admin/AdminDashBoard.vue'

const routes = [
    { path: '/', component: ProductList, name: 'products' },
    { path: '/cart', component: ShoppingCart, name: 'cart' },
    { path: '/login', component: Login, name: 'login' },
    { path: '/checkout', component: Checkout, name: 'checkout' },   
    { path: '/orders', component: OrderHistory, name: 'orders' },
    { path: '/register', component: Register, name: 'register' },
    { path: '/admin/orders', component: AdminOrders, name: 'admin-orders' },
    {  path: '/admin/products',  component: AdminProducts},
    {  path: '/admin/categories',  component: AdminCategory},
    {  path: '/admin/dashboard',  component: AdminDashboard},
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router