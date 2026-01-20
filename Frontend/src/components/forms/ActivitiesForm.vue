<template>
  <form @submit.prevent="submitForm" class="activities-form">
    <div>
      <label for="activity-name">Activity Name</label>
      <input
        type="text"
        id="activity-name"
        v-model="form.name"
        placeholder="Ex: Yoga"
        required
      />
    </div>

    <div>
      <label for="category">Category</label>
      <select id="category" v-model="form.category" required>
        <option disabled value="">Välj kategori</option>
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

    <button type="submit">Skicka activity</button>

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Activity skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref } from "vue"

// Form-data
const form = reactive({
  name: "",
  category: "",
  duration: null,
  date: ""
})

// Feedback
const success = ref(false)
const error = ref(false)

// Submit med fetch
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    const response = await fetch("http://localhost:3000/activities", {
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
    form.category = ""
    form.duration = null
    form.date = ""
  } catch (err) {
    console.error(err)
    error.value = true
  }
}
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
