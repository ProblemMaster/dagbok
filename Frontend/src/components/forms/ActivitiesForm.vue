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
    <p v-if="validationError" class="validation-msg">{{ validationError }}</p>
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
const validationError = ref('')

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
  } catch (err) {
    // If backend returned validation errors (422), show specific message
    if (err && err.status === 422 && err.body && err.body.errors && err.body.errors.name) {
      validationError.value = Array.isArray(err.body.errors.name)
        ? err.body.errors.name.join(', ')
        : String(err.body.errors.name)
      return
    }

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

.validation-msg {
  color: #b35e00;
}
</style>
