<template>
  <div>
    <div class="text-center py-16">
      <h1 class="text-5xl font-bold text-white mb-3">Ban Appeals</h1>
      <p class="text-gray-400 text-lg mb-8">Think you were banned unfairly? Submit an appeal below.</p>
    </div>
    <div class="max-w-xl mx-auto bg-gray-900 border border-gray-800 rounded-xl p-8">
      <div v-if="submitted" class="text-center py-8">
        <div class="text-green-400 text-4xl mb-4">✓</div>
        <p class="text-white font-bold text-lg">Appeal submitted!</p>
        <p class="text-gray-400 text-sm mt-2">We'll review it within 48 hours.</p>
      </div>
      <div v-else>
        <div class="mb-5">
          <label class="text-gray-400 text-sm mb-1 block">Steam name / Nick</label>
          <input v-model="form.name" type="text"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-orange-500" />
        </div>
        <div class="mb-5">
          <label class="text-gray-400 text-sm mb-1 block">Steam ID or IP</label>
          <input v-model="form.steamid" type="text"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-orange-500" />
        </div>
        <div class="mb-5">
          <label class="text-gray-400 text-sm mb-1 block">Why should you be unbanned?</label>
          <textarea v-model="form.reason" rows="4"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-orange-500"></textarea>
        </div>
        <p v-if="error" class="text-red-400 text-sm mb-4">{{ error }}</p>
        <button @click="submit"
          :disabled="!form.name || !form.reason || loading"
          class="w-full bg-orange-500 hover:bg-orange-600 disabled:opacity-40 text-white font-bold py-3 rounded-lg transition-colors">
          {{ loading ? 'Submitting…' : 'Submit Appeal' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const submitted = ref(false)
const loading = ref(false)
const error = ref(null)
const form = ref({ name: '', steamid: '', reason: '' })

async function submit() {
  if (!form.value.name || !form.value.reason) return
  loading.value = true
  error.value = null
  try {
    const res = await fetch('/api/appeals', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value),
    })
    if (!res.ok) {
      const data = await res.json()
      error.value = data.message || 'Something went wrong.'
      return
    }
    submitted.value = true
  } catch {
    error.value = 'Network error. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>