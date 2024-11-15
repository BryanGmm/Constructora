import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './assets/tailwind.css'
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
window.L = L;


createApp(App).use(store).use(router).mount('#app')
