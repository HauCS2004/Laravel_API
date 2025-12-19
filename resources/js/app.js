import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';

import 'bootstrap/dist/css/bootstrap.min.css'; // Import CSS
import 'bootstrap';

const app = createApp(App);
app.use(router);
app.mount('#app');