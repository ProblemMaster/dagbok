<template>
  <form @submit.prevent="submitForm" class="activities-form">
    <div>
      <label for="activity-name">Ny aktivitet:</label>
      <br>
      <input
        type="text"
        id="activity-name"
        v-model="form.name"
        placeholder="Ex: Yoga"
        required
      />
    </div>

    <button type="submit" >Spara aktivitet</button>
    <button type="button" @click="avbryt">Avbryt</button>

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Activity skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref } from "vue"
import { useRouter } from 'vue-router'
import api from "@/api/api"


const router = useRouter()

// Form-data
const form = reactive({
  name: "",
})

// Feedback
const success = ref(false)
const error = ref(false)

// Submit med api.createActivity
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    await api.createActivity(form)
    success.value = true

    // Byt view efter att ha sparat
    router.push('/workouts')

    // Reset form
    form.name = ""
  } catch {
    error.value = true
  }
}
const avbryt = () => {
  router.push('/workouts');
};

</script>

<style scoped>
.activities-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 400px;
}

.success-msg {
  color: green;
}

.error-msg {
  color: red;
}
</style>
