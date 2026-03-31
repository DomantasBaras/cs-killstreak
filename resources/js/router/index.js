import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    { path: '/',          component: () => import('@/pages/Home.vue'),      name: 'home' },
    { path: '/vip',       component: () => import('@/pages/Vip.vue'),       name: 'vip' },
    { path: '/rules',     component: () => import('@/pages/Rules.vue'),     name: 'rules' },
    { path: '/stats',     component: () => import('@/pages/Stats.vue'),     name: 'stats' },
    { path: '/appeals',   component: () => import('@/pages/Appeals.vue'),   name: 'appeals' },
    { path: '/discord',   component: () => import('@/pages/Discord.vue'),   name: 'discord' },
]

export default createRouter({
    history: createWebHistory(),
    routes
})