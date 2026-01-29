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
  gap: 1.5rem;
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.activities-form > div {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

input[type="text"] {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: Arial, sans-serif;
  transition: all 0.2s ease;
  background: white;
}

input[type="text"]:focus {
  outline: none;
  border-color: #008FFB;
  box-shadow: 0 0 0 3px rgba(0, 142, 251, 0.1);
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

button[type="submit"] {
  background-color: #008FFF;
  color: white;
}

button[type="submit"]:hover {
  background-color: #0077d4;
}

button[type="button"] {
  background-color: #e0e0e0;
  color: #333;
}

button[type="button"]:hover {
  background-color: #d0d0d0;
}

.success-msg {
  color: green;
}

.error-msg {
  color: red;
}
</style>
