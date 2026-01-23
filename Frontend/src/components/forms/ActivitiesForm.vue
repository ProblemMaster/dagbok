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


const router = useRouter()

// Form-data
const form = reactive({
  name: "",
  description: "",
})

// Feedback
const success = ref(false)
const error = ref(false)

// Submit med fetch
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    //TODO: API anrop
    const response = await fetch("http://localhost:5173/activities", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(form)
    })

    if (!response.ok) throw new Error("Servern svarade inte som förväntat")

    const data = await response.json()
    console.log("Svar från backend:", data)
      success.value = true

    // Byt view efter att ha sparat
    this.$router.push('/aktivitet');

    // Reset form
    form.name = ""
  } catch (err) {
    console.error(err)
    error.value = true
  }
}
const avbryt = () => {
  router.push('/workouts'); // eller t.ex. '/aktiviteter', '/hem' etc.
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
