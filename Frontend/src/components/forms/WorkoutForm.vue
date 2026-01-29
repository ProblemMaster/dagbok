<template>
  <form @submit.prevent="submitForm" class="workout-form">

    <div>
      <label for="type">Träning</label>
      <br>
      <select v-model.number="form.activity_id" required>
        <option disabled value="">Välj aktivitet</option>
        <option
          v-for="activity in activities"
          :key="activity.id"
          :value="activity.id"
          >
          {{ activity.name }}
        </option>
      </select>
      <br>
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
        @change="formatDate"
        required />
    </div>

    <div>
      <label for="time">Tid</label>
      <br>
      <input
        type ="number"
        v-model.number="form.duration_value"
        @keypress="onlyNumbers"
        />

        <select v-model = "form.duration_unit">
          <option value="">Välj enhet</option>
          <option value="min">Minuter</option>
          <option value="h">Timmar</option>
        </select>
    </div>

    <div>
      <label for="length">Längd</label>
      <br>
      <input
        type ="number"
        v-model.number="form.distance_value"
        @keypress="onlyNumbers"
        />

        <select v-model = "form.distance_unit">
          <option value="">Välj enhet</option>
          <option value="km">Kilometer</option>
          <option value="m">Meter</option>
        </select>
    </div>

    <div>
      <label for="difficulty">Svårighetsgrad</label>
      <br>
      <select v-model.number="form.effort_level" required>
        <option disabled value="">Välj hur jobbigt det var</option>
        <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
      </select>
    </div>

    <button type="submit">Spara träning</button>
    <button type="button" @click="avbryt">Avbryt</button>

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Workout skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import api from "@/api/api"

const router = useRouter()

const activities = ref([])

// Funktion för att hämta aktiviteter från backend via api.js
const fetchActivities = async () => {
  try {
    activities.value = await api.getActivities()
  } catch (error) {
    console.error(error)
    activities.value = []
  }
}

onMounted(() => {
  fetchActivities()
})

function onlyNumbers(event) {
  const keyCode = event.keyCode || event.which;
  if (keyCode < 48 || keyCode > 57) {
    event.preventDefault();
  }
}

// Form-data
const form = reactive({
  activity_id: null,
  date: "",
  description: "",
  effort_level: null,
  distance_value: null,
  distance_unit: "",
  duration_value: null,
  duration_unit: ""
})

const formatDate = (e) => {
  const [y, m, d] = e.target.value.split("-")
  form.date = `${d}-${m}-${y}`
}

// Feedback
const success = ref(false)
const error = ref(false)

// Submit-funktion som använder api.createWorkout
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    const data = await api.createWorkout(form)
    console.log("Svar från backend:", data)

    success.value = true

    // Nollställ formuläret
    form.activity_id = null
    form.date = ""
    form.description = ""
    form.effort_level = null
    form.distance_value = null
    form.distance_unit = ""
    form.duration_value = null
    form.duration_unit = ""

    // Byt view efter att ha sparat
    router.push('/charts')

  } catch (err) {
    console.error(err)
    error.value = true
  }
}

const avbryt = () => {
  router.push('/');
};
</script>

<style scoped>
.workout-form {
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

.workout-form > div {
  display: flex;
  flex-direction: column;
  gap: 0.5 rem;
}

label {
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

input[type="text"],
input[type="date"],
input[type=number],
select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: Arial, sant-serif;
  transition: all 0.2s ease;
  background: white;
}

input[type="text"]:focus,
input[type="date"]:focus,
input[type="number"]:focus,
select:focus {
  outline: none;
  border-color: #008FFB;
  box-shadow: 0 0 0 3px rgba (o, 143, 251, 0.1);
}

input[type="number"] {
  width: 120px;
  margin-right: 0.5rem;
}

select {
  cursor: pointer;
  min-width: 150px;
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

a {
  color: #008FFB;
  text-decoration: none;
  font-size: 0.9rem;
  margin-top: 0.25rem;
  transition: color 0.2s ease;
}

a:hover {
  color: #0077d4;
  text-decoration: underline;
}


.success-msg {
  color: green;
}

.error-msg {
  color: red;
}
</style>
