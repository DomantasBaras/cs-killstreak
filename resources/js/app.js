import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import router from './router'
import App from './App.vue'
import en from './i18n/en.json'
import lt from './i18n/lt.json'

const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('locale') || 'lt',
    fallbackLocale: 'en',
    messages: { en, lt }
})

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(i18n)
app.mount('#app')