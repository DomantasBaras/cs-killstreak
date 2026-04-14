<template>
  <div class="stats-page">
    <div class="stats-header">
      <h1>Žaidėjų Statistika</h1>
      <p class="subtitle">CS KillStreak serverio lyderių lentelė</p>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <span>Kraunama...</span>
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else>
      <div v-if="stats.total === 0" class="empty-state">
        <p>Kol kas statistikos nėra. Prisijunk prie serverio ir pradėk žaisti!</p>
        <code>38.210.227.192:27015</code>
      </div>

      <div v-else>
      <div class="table-wrapper">
        <table class="stats-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Žaidėjas</th>
              <th>Žudimai</th>
              <th>Mirtys</th>
              <th>K/D</th>
              <th>Headshots</th>
              <th>Laikas</th>
              <th>Paskutinį kartą</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(player, index) in stats.data" :key="player.id" :class="rowClass(index)">
              <td class="rank">
                <span v-if="globalRank(index) === 1" class="medal gold">🥇</span>
                <span v-else-if="globalRank(index) === 2" class="medal silver">🥈</span>
                <span v-else-if="globalRank(index) === 3" class="medal bronze">🥉</span>
                <span v-else>{{ globalRank(index) }}</span>
              </td>
              <td class="player-name">{{ player.name }}</td>
              <td class="kills">{{ player.kills }}</td>
              <td class="deaths">{{ player.deaths }}</td>
              <td class="kd" :class="kdClass(player.kd_ratio)">{{ player.kd_ratio }}</td>
              <td class="headshots">{{ player.headshots }}</td>
              <td class="playtime">{{ formatPlaytime(player.playtime) }}</td>
              <td class="last-seen">{{ formatDate(player.last_seen) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination" v-if="stats.last_page > 1">
        <button
          :disabled="stats.current_page === 1"
          @click="fetchStats(stats.current_page - 1)"
          class="page-btn"
        >← Atgal</button>

        <button
          v-for="page in visiblePages"
          :key="page"
          @click="fetchStats(page)"
          class="page-btn"
          :class="{ active: page === stats.current_page }"
        >{{ page }}</button>

        <button
          :disabled="stats.current_page === stats.last_page"
          @click="fetchStats(stats.current_page + 1)"
          class="page-btn"
        >Pirmyn →</button>
      </div>

      <p class="total-count">Iš viso žaidėjų: <strong>{{ stats.total }}</strong></p>
      </div><!-- end v-else (has data) -->
    </div><!-- end outer v-else -->
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const stats = ref({ data: [], current_page: 1, last_page: 1, total: 0, per_page: 25 })
const loading = ref(true)
const error = ref(null)

async function fetchStats(page = 1) {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(`/api/stats?page=${page}`)
    if (!res.ok) throw new Error('Nepavyko gauti duomenų')
    stats.value = await res.json()
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function globalRank(index) {
  return (stats.value.current_page - 1) * (stats.value.per_page || 25) + index + 1
}

function rowClass(index) {
  const rank = globalRank(index)
  if (rank === 1) return 'row-gold'
  if (rank === 2) return 'row-silver'
  if (rank === 3) return 'row-bronze'
  return ''
}

function kdClass(kd) {
  if (kd >= 2) return 'kd-excellent'
  if (kd >= 1) return 'kd-good'
  return 'kd-bad'
}

function formatPlaytime(seconds) {
  if (!seconds) return '0m'
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  if (h > 0) return `${h}h ${m}m`
  return `${m}m`
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  if (diff < 60) return 'Ką tik'
  if (diff < 3600) return `${Math.floor(diff / 60)}min. prieš`
  if (diff < 86400) return `${Math.floor(diff / 3600)}val. prieš`
  return `${Math.floor(diff / 86400)}d. prieš`
}

const visiblePages = computed(() => {
  const current = stats.value.current_page
  const last = stats.value.last_page
  const pages = []
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  return pages
})

onMounted(() => fetchStats())
</script>

<style scoped>
.stats-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.stats-header {
  text-align: center;
  margin-bottom: 2rem;
}

.stats-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #e8c84a;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #9ca3af;
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 3rem;
  color: #9ca3af;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #374151;
  border-top-color: #e8c84a;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.error {
  text-align: center;
  color: #ef4444;
  padding: 2rem;
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 0.75rem;
  border: 1px solid #1f2937;
}

.stats-table {
  width: 100%;
  border-collapse: collapse;
  background: #111827;
}

.stats-table thead tr {
  background: #1f2937;
}

.stats-table th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  color: #6b7280;
  letter-spacing: 0.05em;
}

.stats-table td {
  padding: 0.75rem 1rem;
  border-top: 1px solid #1f2937;
  font-size: 0.875rem;
  color: #d1d5db;
}

.stats-table tbody tr:hover {
  background: #1a2332;
}

.row-gold td { background: rgba(234, 197, 54, 0.05); }
.row-silver td { background: rgba(156, 163, 175, 0.05); }
.row-bronze td { background: rgba(180, 83, 9, 0.05); }

.rank { font-weight: 700; color: #6b7280; width: 60px; }
.medal { font-size: 1.2rem; }
.player-name { font-weight: 600; color: #f9fafb; }
.kills { color: #34d399; font-weight: 600; }
.deaths { color: #f87171; }
.kd { font-weight: 700; }
.kd-excellent { color: #34d399; }
.kd-good { color: #fbbf24; }
.kd-bad { color: #f87171; }
.headshots { color: #f59e0b; }
.playtime { color: #818cf8; }
.last-seen { color: #6b7280; font-size: 0.8rem; }

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: #1f2937;
  border: 1px solid #374151;
  color: #d1d5db;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.15s;
}

.page-btn:hover:not(:disabled) {
  background: #374151;
  color: #f9fafb;
}

.page-btn.active {
  background: #e8c84a;
  color: #111827;
  border-color: #e8c84a;
  font-weight: 700;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-state code {
  display: block;
  margin-top: 0.5rem;
  color: #e8c84a;
  font-size: 1.1rem;
}

.total-count {
  text-align: center;
  color: #6b7280;
  font-size: 0.875rem;
  margin-top: 1rem;
}
</style>