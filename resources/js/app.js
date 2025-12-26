import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';
import  Toast  from "vue-toastification";
import "vue-toastification/dist/index.css";

import 'bootstrap/dist/css/bootstrap.min.css'; // Import CSS

import 'bootstrap';
// resources/js/app.js
import 'bootstrap-icons/font/bootstrap-icons.css'; // 👈 Thêm dòng này


const options = {
    position: "bottom-right",
    time:3000,
    closeOnClick: true,
    pauseOnHover: true,
    
};
const app = createApp(App);
app.use(router);
app.use(Toast, options);
console.log(router.getRoutes());
app.mount('#app');