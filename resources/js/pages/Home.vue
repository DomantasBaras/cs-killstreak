<template>
  <div>
    <!-- Hero -->
    <div class="text-center py-16">
      <h1 class="text-5xl font-bold text-white mb-3">CS KillStreak</h1>
      <p class="text-gray-400 text-lg mb-8">{{ t('home.subtitle') }}</p>

      <button @click="copyIp"
        class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-lg text-lg transition-colors">
        {{ t('home.connect') }} — {{ serverIp }}
      </button>
      <p v-if="copied" class="text-green-400 text-sm mt-2">IP copied!</p>
    </div>

    <!-- Server status cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 text-center">
        <div class="text-3xl font-bold text-orange-400 mb-1">
          <span v-if="loading">—</span>
          <span v-else-if="status">{{ status.players }}/{{ status.maxPlayers }}</span>
          <span v-else>—</span>
        </div>
        <div class="text-gray-500 text-sm">{{ t('home.players_online') }}</div>
      </div>

      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 text-center">
        <div class="text-xl font-bold text-white mb-1 truncate">
          <span v-if="loading">—</span>
          <span v-else-if="status">{{ status.map }}</span>
          <span v-else>—</span>
        </div>
        <div class="text-gray-500 text-sm">{{ t('home.current_map') }}</div>
      </div>

      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 text-center">
        <div class="text-xl font-bold mb-1"
          :class="status ? 'text-green-400' : 'text-red-400'">
          <span v-if="loading">...</span>
          <span v-else-if="status">Online</span>
          <span v-else>{{ t('home.server_offline') }}</span>
        </div>
        <div class="text-gray-500 text-sm">Status</div>
      </div>
    </div>

    <!-- Servers list -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-orange-400 font-bold mb-1">5v5 Classic</h3>
        <p class="text-gray-400 text-sm mb-3">Competitive matchmaking style</p>
        <code class="text-gray-300 text-sm bg-gray-800 px-3 py-1 rounded">{{ serverIp }}</code>
      </div>
      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h3 class="text-orange-400 font-bold mb-1">GunGame</h3>
        <p class="text-gray-400 text-sm mb-3">Casual fun, no buy menu</p>
        <code class="text-gray-300 text-sm bg-gray-800 px-3 py-1 rounded">{{ serverIp }}</code>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

const { t } = useI18n()
const status = ref(null)
const loading = ref(true)
const copied = ref(false)
const serverIp = '38.210.227.192:27015' // update with real IP later

async function fetchStatus() {
  try {
    const res = await axios.get('/api/server/status')
    status.value = res.data
  } catch {
    status.value = null
  } finally {
    loading.value = false
  }
}

function copyIp() {
  navigator.clipboard.writeText(serverIp)
  copied.value = true
  setTimeout(() => copied.value = false, 2000)
}

onMounted(fetchStatus)
</script>