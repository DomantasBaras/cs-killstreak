<template>
  <div>
    <div class="text-center py-16">
      <h1 class="text-5xl font-bold text-white mb-3">Server Rules</h1>
      <p class="text-gray-400 text-lg">Play fair. Keep it fun.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="rule in rules" :key="rule.id"
        class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
          <span class="text-orange-400 font-bold text-xl">{{ rule.id }}</span>
          <div>
            <h3 class="text-white font-bold mb-1">{{ rule.title }}</h3>
            <p class="text-gray-400 text-sm">{{ rule.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const rules = [
  { id: '01', title: 'No cheating', desc: 'Hacks, aimbots, wallhacks or any third-party software that gives an unfair advantage is strictly forbidden.' },
  { id: '02', title: 'No racism or hate speech', desc: 'Racist, sexist or otherwise hateful language in chat or voice will result in a permanent ban.' },
  { id: '03', title: 'No mic spam', desc: 'Do not spam voice chat with music, soundboards or excessive noise.' },
  { id: '04', title: 'No griefing teammates', desc: 'Intentional teamkilling, blocking or sabotaging your own team is not allowed.' },
  { id: '05', title: 'Respect admins', desc: 'Admin decisions are final. Do not argue in chat — use the Appeals page instead.' },
  { id: '06', title: 'No advertising', desc: 'Do not advertise other servers, websites or Discord servers in chat.' },
]
</script>