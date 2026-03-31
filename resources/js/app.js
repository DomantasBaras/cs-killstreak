import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import en from './i18n/en.json'
import lt from './i18n/lt.json'

const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('locale') || 'lt',
    fallbackLocale: 'en',
    messages: { en, lt }
})

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/',        component: () => import('./pages/Home.vue') },
        { path: '/vip',     component: () => import('./pages/Vip.vue') },
        { path: '/rules',   component: () => import('./pages/Rules.vue') },
        { path: '/stats',   component: () => import('./pages/Stats.vue') },
        { path: '/appeals', component: () => import('./pages/Appeals.vue') },
        { path: '/discord', component: () => import('./pages/Discord.vue') },
    ]
})

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(i18n)
app.mount('#app')