import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';
import  Toast  from "vue-toastification";
import "vue-toastification/dist/index.css";

import 'bootstrap/dist/css/bootstrap.min.css'; // Import CSS
import 'bootstrap';


const options = {
    position: "bottom-right",
    time:3000,
    closeOnClick: true,
    pauseOnHover: true,
    
};
const app = createApp(App);
app.use(router);
app.use(Toast, options);
app.mount('#app');