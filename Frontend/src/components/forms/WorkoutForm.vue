<template>
  <form @submit.prevent="submitForm" class="workout-form">

    <div>
      <label for="type">Träning</label>
      <br>
      <select id="type" v-model="form.type" required>
        <option disabled value="">Välj typ</option>
        <option>Cardio</option>
        <option>Strength</option>
        <option>Flexibility</option>
        <option>Balance</option>
      </select>

      <RouterLink to="/activities">Lägg till ny aktivitet</RouterLink>
    </div>

    <div>
      <label for="description">Beskrivning</label>
      <br>
      <input
        type="text"
        id="description"
        v-model="form.description"
        required
      />
    </div>

    <div>
      <label for="date">Datum</label>
      <br>
      <input type="date"
        id="date"
        v-model="form.date"
        required />
    </div>

    <div>
      <label for="time">Tid</label>
      <br>
      <input
        type ="time"
        id="time"
        v-model="form.time"
        />

        <select id="time_unit" v-model = "form.time_unit">
          <option value="">Välj enhet</option>
          <option>Minuter</option>
          <option>Timmar</option>
        </select>
    </div>

    <div>
      <label for="length">Längd</label>
      <br>
      <input
        type ="number"
        id="length"
        v-model="form.length"
        />

        <select id="length_unit" v-model = "form.length_unit">
          <option value="">Välj enhet</option>
          <option>Kilometer</option>
          <option>Meter</option>
        </select>
    </div>

    <div>
      <label for="difficulty">Svårighetsgrad</label>
      <br>
      <select id="difficulty" v-model="form.difficulty" required>
        <option disabled value="">Välj hur jobbigt det var</option>
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
        <option>6</option>
        <option>7</option>
        <option>8</option>
        <option>9</option>
        <option>10</option>
      </select>
    </div>

    <button type="submit">Spara träning</button>

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Workout skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref } from "vue"

// Form-data
const form = reactive({
  type: "",
  duration: "",
  date: "",
  time: "",
  time_unit: "",
  length: "",
  length_unit: "",
  difficulty: "",

})

// Feedback
const success = ref(false)
const error = ref(false)

// Submit-funktion med fetch
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    const response = await fetch("http://localhost:5173/workouts", {
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
      form.type = ""
      form.duration = ""
      form.date = ""
      form.time = ""
      form.time_unit = ""
      form.length = ""
      form.length_unit = ""
      form.difficulty = ""
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
