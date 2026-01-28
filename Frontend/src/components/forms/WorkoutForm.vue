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

    <!-- Feedback -->
    <p v-if="success" class="success-msg">Workout skickad!</p>
    <p v-if="error" class="error-msg">Något gick fel. Försök igen.</p>
  </form>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

const activities = ref([])

// Funktion för att hämta aktiviteter från bakcend
const fetchActivities = async () => {
  try {
    const response = await fetch('http://localhost:8000/activities')
    if (!response.ok) throw new Error('Något gick fel vid hämtning av aktiviteter')
    activities.value = await response.json()
  } catch (error) {
    console.error(error)
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

// Submit-funktion med fetch
const submitForm = async () => {
  success.value = false
  error.value = false

  try {
    //TODO: API anrop
    const response = await fetch("http://localhost:5173/workouts", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(form)
    })

    if (!response.ok) throw new Error("Misslyckades att spara workout")

    const data = await response.json()
    console.log("Svar från backend:", data)



    success.value = true

      form.activity_id = null,
      form.date = "",
      form.description = "",
      form.effort_level = null,
      form.distance_value = null,
      form.distance_unit = "",
      form.duration_value = null,
      form.duration_unit = ""

    // Byt view efter att ha sparat
    router.push('/charts');

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
