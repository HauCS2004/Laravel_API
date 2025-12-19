import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';

import 'bootstrap/dist/css/bootstrap.min.css'; // Import CSS
import 'bootstrap';

const app = createApp(App);
app.mount('#app');