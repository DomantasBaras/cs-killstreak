<template>
  <div>
    <div class="text-center py-16">
      <h1 class="text-5xl font-bold text-white mb-3">Player Stats</h1>
      <p class="text-gray-400 text-lg mb-8">Top players on CS KillStreak</p>
    </div>
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
      <div class="grid grid-cols-4 px-6 py-3 text-gray-500 text-sm border-b border-gray-800">
        <span>#</span><span>Player</span><span>Kills</span><span>K/D</span>
      </div>
      <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>
      <div v-else-if="players.length === 0" class="text-center py-12 text-gray-500">No stats yet.</div>
      <div v-else>
        <div v-for="(p, i) in players" :key="p.name"
          class="grid grid-cols-4 px-6 py-4 border-b border-gray-800 hover:bg-gray-800 transition-colors">
          <span class="text-orange-400 font-bold">{{ i + 1 }}</span>
          <span class="text-white">{{ p.name }}</span>
          <span class="text-gray-300">{{ p.kills }}</span>
          <span class="text-gray-300">{{ p.kd }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
const players = ref([])
const loading = ref(true)
async function fetchStats() {
  try {
    const res = await axios.get('/api/stats')
    players.value = res.data
  } catch {
    players.value = []
  } finally {
    loading.value = false
  }
}
onMounted(fetchStats)
</script>