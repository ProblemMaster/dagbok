<template>
  <div class="not-found">
    <h1>404</h1>
    <p>Sidan du söker finns inte.</p>

    <RouterLink to="/activities">
      Gå till Activities
    </RouterLink>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router"
</script>

<style scoped>
.not-found {
  text-align: center;
  margin-top: 4rem;
}

h1 {
  font-size: 4rem;
}
</style>
