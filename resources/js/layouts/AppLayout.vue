<template>
  <div class="min-h-screen bg-gray-950 text-white">
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
      <div class="max-w-6xl mx-auto flex items-center justify-between">

        <RouterLink to="/" class="text-orange-500 font-bold text-xl tracking-wide">
          CS KillStreak
        </RouterLink>

        <div class="flex items-center gap-6 text-sm">
          <RouterLink v-for="link in navLinks" :key="link.to"
            :to="link.to"
            class="text-gray-400 hover:text-orange-400 transition-colors"
            active-class="text-orange-400">
            {{ t(link.label) }}
          </RouterLink>
        </div>

        <button @click="toggleLocale"
          class="text-xs bg-gray-800 hover:bg-gray-700 px-3 py-1 rounded border border-gray-700 transition-colors">
          {{ locale === 'lt' ? 'EN' : 'LT' }}
        </button>

      </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-10">
      <slot />
    </main>

    <footer class="border-t border-gray-800 text-center text-gray-600 text-xs py-6 mt-20">
      CS KillStreak © {{ new Date().getFullYear() }}
    </footer>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const navLinks = [
  { to: '/',        label: 'nav.home' },
  { to: '/vip',     label: 'nav.vip' },
  { to: '/stats',   label: 'nav.stats' },
  { to: '/rules',   label: 'nav.rules' },
  { to: '/appeals', label: 'nav.appeals' },
  { to: '/discord', label: 'nav.discord' },
]

function toggleLocale() {
  locale.value = locale.value === 'lt' ? 'en' : 'lt'
  localStorage.setItem('locale', locale.value)
}
</script>