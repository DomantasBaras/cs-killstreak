<template>
  <div>
    <div class="text-center py-16">
      <h1 class="text-5xl font-bold text-white mb-3">Discord</h1>
      <p class="text-gray-400 text-lg mb-8">Join the community — chat, get VIP, report players.</p>
      <a href="https://discord.gg/YOURCODE" target="_blank"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-lg text-lg transition-colors inline-block">
        Join Discord Server
      </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="item in perks" :key="item.title"
        class="bg-gray-900 border border-gray-800 rounded-xl p-6 text-center">
        <div class="text-3xl mb-3">{{ item.icon }}</div>
        <h3 class="text-white font-bold mb-1">{{ item.title }}</h3>
        <p class="text-gray-400 text-sm">{{ item.desc }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const perks = [
  { icon: '🎮', title: 'Server Updates', desc: 'Get notified about new maps, events and server changes.' },
  { icon: '⭐', title: 'Get VIP', desc: 'Purchase or earn VIP status directly through Discord.' },
  { icon: '🚨', title: 'Report Players', desc: 'Report cheaters and rulebreakers with evidence.' },
]
</script>