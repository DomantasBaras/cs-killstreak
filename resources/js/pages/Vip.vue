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
        <h2 class="text-orange-400 font-bold text-xl mb-6">VIP Privalumai</h2>
        <ul class="space-y-3">
          <li v-for="perk in perks" :key="perk" class="flex items-center gap-3 text-gray-300">
            <span class="text-green-400 font-bold">✓</span>
            {{ t('vip.perks.' + perk) }}
          </li>
        </ul>
        <div class="mt-8 pt-6 border-t border-gray-800">
          <div class="text-3xl font-bold text-white">5 EUR / mėn</div>
          <p class="text-gray-500 text-sm mt-1">Mokestis kartojasi kas mėnesį</p>
        </div>
      </div>

      <!-- Checkout card -->
      <div class="bg-gray-900 border border-orange-500/30 rounded-2xl p-8">
        <h2 class="text-white font-bold text-xl mb-6">Įsigyti VIP</h2>

        <!-- Auth method selector -->
        <div class="flex gap-2 mb-6">
          <button
            v-for="method in authMethods"
            :key="method.value"
            @click="form.authMethod = method.value"
            class="flex-1 py-2 px-3 rounded-lg text-sm font-semibold transition-colors"
            :class="form.authMethod === method.value
              ? 'bg-orange-500 text-white'
              : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
          >
            {{ method.label }}
          </button>
        </div>

        <div class="space-y-4">

          <!-- IP method -->
          <div v-if="form.authMethod === 'ip'" class="bg-gray-800/50 rounded-xl p-4 text-sm text-gray-400">
            <p class="text-yellow-400 font-semibold mb-2">⚠️ Svarbu apie IP</p>
            <p>VIP bus priskirtas prie jūsų <strong class="text-white">dabartinio IP adreso</strong>. Jei turite dinaminį IP (keičiasi), VIP gali nustoti veikti. Rekomenduojame naudoti Steam ID arba Nickname metodą.</p>
            <p class="mt-2">Jūsų IP bus nustatytas automatiškai užsakymo metu.</p>
          </div>

          <!-- Steam ID method -->
          <div v-if="form.authMethod === 'steamid'">
            <label class="text-gray-400 text-sm block mb-1">Steam ID</label>
            <input v-model="form.steamId"
              type="text"
              placeholder="STEAM_0:1:12345678"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3
                     text-white placeholder-gray-600 focus:outline-none
                     focus:border-orange-500 transition-colors" />
            <p class="text-gray-600 text-xs mt-1">
              Steam ID rasite: <a href="https://steamid.io" target="_blank" class="text-orange-400 hover:underline">steamid.io</a>
              arba žaidimo konsolėje įvedę <code class="text-orange-300">status</code>
            </p>
          </div>

          <!-- Nickname method -->
          <div v-if="form.authMethod === 'nick'">
            <label class="text-gray-400 text-sm block mb-1">Žaidėjo vardas (Nickname)</label>
            <input v-model="form.nickname"
              type="text"
              placeholder="Jūsų nick žaidime"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3
                     text-white placeholder-gray-600 focus:outline-none
                     focus:border-orange-500 transition-colors" />

            <label class="text-gray-400 text-sm block mb-1 mt-3">Slaptažodis</label>
            <input v-model="form.password"
              type="password"
              placeholder="Sugalvokite slaptažodį"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3
                     text-white placeholder-gray-600 focus:outline-none
                     focus:border-orange-500 transition-colors" />

            <div class="bg-blue-900/30 border border-blue-500/30 rounded-xl p-3 mt-3 text-xs text-gray-400">
              <p class="text-blue-300 font-semibold mb-1">📋 Kaip naudoti slaptažodį žaidime:</p>
              <p>Prisijungę prie serverio, atidarykite konsolę (<kbd class="bg-gray-700 px-1 rounded">~</kbd>) ir įveskite:</p>
              <code class="block bg-gray-800 rounded p-2 mt-1 text-orange-300">setinfo _pw jūsų_slaptažodis</code>
              <p class="mt-1">⚠️ Po <code class="text-orange-300">setinfo</code> būtinai padėkite tarpą prieš <code class="text-orange-300">_pw</code></p>
            </div>
          </div>

          <!-- Email (always shown) -->
          <div>
            <label class="text-gray-400 text-sm block mb-1">El. paštas</label>
            <input v-model="form.email"
              type="email"
              placeholder="jusu@pastas.lt"
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
            {{ loading ? 'Apdorojama...' : 'Pirkti VIP — 5 EUR/mėn' }}
          </button>
        </div>

        <p class="text-gray-600 text-xs text-center mt-4">
          Saugus mokėjimas per Stripe. Galite atšaukti bet kada.
        </p>
      </div>
    </div>

    <!-- Success message -->
    <div v-if="route.query.session_id"
      class="mt-8 bg-green-900/30 border border-green-500/30 rounded-xl p-6 text-center">
      <div class="text-green-400 font-bold text-xl mb-2">✅ VIP aktyvuotas!</div>
      <p class="text-gray-400">VIP bus aktyvuotas kitą žemėlapį. Ačiū už palaikymą!</p>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const authMethods = [
  { value: 'ip',      label: 'IP' },
  { value: 'steamid', label: 'Steam ID' },
  { value: 'nick',    label: 'Nickname' },
]

const perks = ['hp', 'hs', 'money', 'awp', 'slot', 'tag', 'grenades', 'armor']

const form = ref({
  authMethod: 'steamid',
  steamId:    '',
  nickname:   '',
  password:   '',
  email:      '',
})

const loading = ref(false)
const error   = ref('')

async function checkout() {
  error.value = ''

  if (!form.value.email) {
    error.value = 'Įveskite el. paštą'
    return
  }

  if (form.value.authMethod === 'steamid' && !form.value.steamId) {
    error.value = 'Įveskite Steam ID'
    return
  }

  if (form.value.authMethod === 'nick' && (!form.value.nickname || !form.value.password)) {
    error.value = 'Įveskite nickname ir slaptažodį'
    return
  }

  loading.value = true

  try {
    const res = await axios.post('/api/vip/checkout', {
      auth_method: form.value.authMethod,
      steam_id:    form.value.steamId,
      nickname:    form.value.nickname,
      password:    form.value.password,
      email:       form.value.email,
    })
    window.location.href = res.data.url
  } catch (e) {
    error.value = 'Klaida. Bandykite dar kartą.'
  } finally {
    loading.value = false
  }
}
</script>