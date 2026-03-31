<template>
  <div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-white mb-3">{{ t('vip.title') }}</h1>
      <p class="text-gray-400">{{ t('vip.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Perks card -->
      <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8">
        <h2 class="text-orange-400 font-bold text-xl mb-6">
          {{ t('vip.perks_title') }}
        </h2>
        <ul class="space-y-3">
          <li v-for="perk in perks" :key="perk"
            class="flex items-center gap-3 text-gray-300">
            <span class="text-green-400 font-bold">✓</span>
            {{ t('vip.perks.' + perk) }}
          </li>
        </ul>

        <div class="mt-8 pt-6 border-t border-gray-800">
          <div class="text-3xl font-bold text-white">
            {{ t('vip.price') }}
          </div>
          <p class="text-gray-500 text-sm mt-1">{{ t('vip.recurring') }}</p>
        </div>
      </div>

      <!-- Checkout card -->
      <div class="bg-gray-900 border border-orange-500/30 rounded-2xl p-8">
        <h2 class="text-white font-bold text-xl mb-6">
          {{ t('vip.buy') }}
        </h2>

        <div class="space-y-4">
          <div>
            <label class="text-gray-400 text-sm block mb-1">
              {{ t('vip.steamid_label') }}
            </label>
            <input v-model="form.steamId"
              type="text"
              placeholder="STEAM_0:1:12345678"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3
                     text-white placeholder-gray-600 focus:outline-none
                     focus:border-orange-500 transition-colors" />
            <p class="text-gray-600 text-xs mt-1">{{ t('vip.steamid_help') }}</p>
          </div>

          <div>
            <label class="text-gray-400 text-sm block mb-1">
              {{ t('vip.email_label') }}
            </label>
            <input v-model="form.email"
              type="email"
              placeholder="you@email.com"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3
                     text-white placeholder-gray-600 focus:outline-none
                     focus:border-orange-500 transition-colors" />
          </div>

          <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

          <button @click="checkout"
            :disabled="loading"
            class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-700
                   disabled:text-gray-500 text-white font-bold py-4 rounded-xl
                   transition-colors text-lg mt-2">
            {{ loading ? t('vip.processing') : t('vip.buy_button') }}
          </button>
        </div>

        <p class="text-gray-600 text-xs text-center mt-4">
          {{ t('vip.stripe_note') }}
        </p>
      </div>
    </div>

    <!-- Success message -->
    <div v-if="route.query.session_id"
      class="mt-8 bg-green-900/30 border border-green-500/30 rounded-xl p-6 text-center">
      <div class="text-green-400 font-bold text-xl mb-2">{{ t('vip.success_title') }}</div>
      <p class="text-gray-400">{{ t('vip.success_msg') }}</p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import axios from 'axios'

const { t } = useI18n()
const route = useRoute()

const perks = ['hp', 'hs', 'money', 'awp', 'slot', 'tag', 'grenades', 'armor']

const form = ref({ steamId: '', email: '' })
const loading = ref(false)
const error = ref('')

async function checkout() {
  error.value = ''

  if (!form.value.steamId || !form.value.email) {
    error.value = t('vip.error_fields')
    return
  }

  loading.value = true

  try {
    const res = await axios.post('/api/vip/checkout', {
      steam_id: form.value.steamId,
      email:    form.value.email,
    })
    window.location.href = res.data.url
  } catch (e) {
    error.value = t('vip.error_generic')
  } finally {
    loading.value = false
  }
}
</script>