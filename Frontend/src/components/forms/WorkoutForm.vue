<template>
  <form @submit.prevent="submitForm" class="workout-form">
    <div>
      <label for="name">Workout Name</label>
      <input
        type="text"
        id="name"
        v-model="form.name"
        placeholder="Ex: Morning Run"
        required
      />
    </div>

    <div>
      <label for="type">Workout Type</label>
      <select id="type" v-model="form.type" required>
        <option disabled value="">Välj typ</option>
        <option>Cardio</option>
        <option>Strength</option>
        <option>Flexibility</option>
        <option>Balance</option>
      </select>
    </div>

    <div>
      <label for="duration">Duration (minutes)</label>
      <input
        type="number"
        id="duration"
        v-model.number="form.duration"
        min="1"
        required
      />
    </div>

    <div>
      <label for="date">Date</label>
      <input type="date" id="date" v-model="form.date" required />
    </div>

    <button type="submit">Skicka workout</button>

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Workout skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref } from "vue"

// Form-data
const form = reactive({
  name: "",
  type: "",
  duration: null,
  date: ""
})

// Feedback
const success = ref(false)
const error = ref(false)

// Submit-funktion med fetch
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    const response = await fetch("http://localhost:3000/workout", {
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
    // Reset form
    form.name = ""
    form.type = ""
    form.duration = null
    form.date = ""
  } catch (err) {
    console.error(err)
    error.value = true
  }
}
</script>

<style scoped>
.workout-form {
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
