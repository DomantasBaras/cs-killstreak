<template>
  <div class="vote-section">
    <p class="vote-label">Balsuok už mūsų serverį!</p>
    <div class="vote-buttons">
      <a
        href="http://www.cs-servers.lt/vote.php?sid=99318"
        target="_blank"
        title="Balsuok už CS KillStreak!"
        class="vote-link"
      >
        <img
          src="/vote-counter2.png"
          alt="cs-servers.lt"
          title="Balsuok!"
          border="0"
          width="88"
          height="31"
        />
      </a>
      <a
        href="http://www.cs-servers.lt/vote.php?sid=99318"
        target="_blank"
        title="Balsuok už CS KillStreak!"
        class="vote-link"
      >
        <img
          src="/vote-button.png"
          alt="cs-servers.lt"
          title="Balsuok!"
          border="0"
          width="88"
          height="56"
        />
      </a>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.vote-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.vote-label {
  font-size: 12px;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0;
}

.vote-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.vote-link {
  display: block;
  transition: opacity 0.15s;
}

.vote-link:hover {
  opacity: 0.8;
}
</style>
