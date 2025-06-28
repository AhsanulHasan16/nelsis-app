import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './pages/Dashboard.vue';
import Login from './pages/auth/Login.vue';

const routes = [
    { path: '/dashboard', component: Dashboard },
    { path: '/login', component: Login },
];
  
const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
export default router;