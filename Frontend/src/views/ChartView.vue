<script setup>
import { ref, onMounted, nextTick, watch } from "vue"
import { useRouter } from "vue-router"
import ApexCharts from "apexcharts"
import api from "@/api/api"

// State
const loading = ref(true)
const error = ref(null)
const charts = ref([]) // Ändrat från chart till charts (array)

// Filter state
const filterType = ref('all')
const selectedActivity = ref(null)
const dateFrom = ref('')
const dateTo = ref('')

const activities = ref([])
const activitiesWithData = ref([])

const normalizeDate = (date) => {
  if (!date) return null
  const regex = /^\d{2}-\d{2}-\d{4}$/
  return regex.test(date) ? date : null
}

const fetchActivities = async () => {
  try {
    activities.value = await api.getActivities()
  } catch (err) {
    console.error(err)
    activities.value = []
  }
}

// Funktion för att hämta och rendera data
const fetchAndRenderChart = async () => {
  const from = normalizeDate(dateFrom.value)
  const to = normalizeDate(dateTo.value)

  loading.value = true
  error.value = null
  activitiesWithData.value = []

  // Förstör alla gamla diagram först
  charts.value.forEach(chart => {
    if (chart) {
      chart.destroy()
    }
  })
  charts.value = []

  try {
    // Hämta data baserat på valt filter
    if (filterType.value === 'all') {
      // Hämta data för alla aktiviteter
      if (activities.value.length === 0) {
        error.value = 'Inga aktiviteter hittades. Skapa aktiviteter först!'
        loading.value = false
        return
      }

      // Sätt loading till false så elementen blir synliga
      loading.value = false
      await nextTick()

      // Skapa ett diagram för varje aktivitet
      for (const activity of activities.value) {
        try {
          const data = await api.getActivityTimeline(activity.id, from, to)

          // Hantera array-respons
          let activityData = Array.isArray(data) ? data[0] : data

          // Kolla om det finns data för denna aktivitet
          if (!activityData || !activityData.distance || !activityData.duration) {
            console.log(`Ingen data för ${activity.name}`)
            continue
          }

          // Om det inte finns några datapunkter, hoppa över
          if (activityData.distance.data.length === 0) {
            continue
          }

          activitiesWithData.value.push(activity)

          await nextTick()

          await renderChart(activityData, `chart-${activity.id}`)
        } catch (err) {
          console.error(`Fel för aktivitet ${activity.name}:`, err)
        }
      }

      if (charts.value.length === 0) {
        error.value = 'Ingen data hittades för några aktiviteter i det valda datumintervallet.'
      }

    } else {
      // Visa diagram för en specifik aktivitet
      if (!selectedActivity.value) {
        error.value = 'Välj en aktivitet'
        loading.value = false
        return
      }

      const data = await api.getActivityTimeline(selectedActivity.value, from, to)

      // Hantera array-respons
      let activityData = Array.isArray(data) ? data[0] : data

      if (!activityData) {
        error.value = 'Ingen data returnerades från backend'
        loading.value = false
        return
      }

      if (!activityData.distance || !activityData.duration) {
        error.value = 'Backend returnerade ogiltig data-struktur.'
        loading.value = false
        return
      }

      if (activityData.distance.data.length === 0) {
        error.value = 'Ingen data hittades för vald aktivitet i det valda datumintervallet.'
        loading.value = false
        return
      }

      loading.value = false
      await nextTick()

      await renderChart(activityData, 'chart-single')
    }

  } catch (err) {
    console.error('Error:', err)
    error.value = err.message
    loading.value = false
  }
}

// Hjälpfunktion för att rendera ett enskilt diagram
const renderChart = async (data, elementId) => {
  const options = {
    series: [
      {
        name: `Distans (${data.distance.unit})`,
        data: data.distance.data.map(Number),
        type: 'column'
      },
      {
        name: `Duration (${data.duration.unit})`,
        data: data.duration.data,
        type: 'line'
      }
    ],
    chart: {
      type: "line",
      height: 400,
      toolbar: {
        show: true
      }
    },
    title: {
      text: data.activity_name,
      align: 'left',
      style: {
        fontSize: '18px',
        fontWeight: 'bold'
      }
    },
    xaxis: {
      categories: data.labels,
      title: {
        text: 'Datum'
      }
    },
    yaxis: [
      {
        title: {
          text: `Distans (${data.distance.unit})`
        },
        labels: {
          formatter: (val) => val.toFixed(2)
        }
      },
      {
        opposite: true,
        title: {
          text: `Duration (${data.duration.unit})`
        }
      }
    ],
    stroke: {
      width: [0, 4]
    },
    dataLabels: {
      enabled: true,
      enabledOnSeries: [1]
    },
    colors: ['#008FFB', '#00E396']
  }

  const chartElement = document.querySelector(`#${elementId}`)
  if (!chartElement) {
    console.error(`Chart element #${elementId} hittades inte`)
    return
  }

  const chart = new ApexCharts(chartElement, options)
  await chart.render()
  charts.value.push(chart)
}

// Återställ filter
const resetFilters = () => {
  filterType.value = 'all'
  selectedActivity.value = null
  dateFrom.value = ''
  dateTo.value = ''
  fetchAndRenderChart()
}

// Router-funktion för att navigera till /workouts
const router = useRouter()
const goToWorkouts = () => {
  router.push('/workouts')
}

watch(filterType, (newValie) => {
  if (newValie === 'all') {
    selectedActivity.value = null
    fetchAndRenderChart()
  }
})

// Hämta data vid första laddning
onMounted(() => {
  fetchActivities().then(() => {
    fetchAndRenderChart()
  })
})

</script>

<template>
  <div class="chart-container">
    <!-- Navigation -->
    <div class="navigation">
      <button @click="goToWorkouts" class="btn-nav">
        📋 Gå till Workouts
      </button>
    </div>

    <!-- Filter Section -->
    <div class="filters">
      <h3>Filter</h3>

      <!-- Aktivitetstyp -->
      <div class="filter-group">
        <label>Visa:</label>
        <div class="radio-group">
          <label>
            <input type="radio" v-model="filterType" value="all" />
            Alla aktiviteter
          </label>
          <label>
            <input type="radio" v-model="filterType" value="activity" />
            Specifik aktivitet
          </label>
        </div>
      </div>

      <!-- Välj aktivitet (visas bara om filterType är 'activity') -->
      <div class="filter-group" v-if="filterType === 'activity'">
        <label for="activity-select">Välj aktivitet:</label>
        <select v-model="selectedActivity">
          <option :value="null">-- Välj aktivitet --</option>
          <option v-for="act in activities" :key="act.id" :value="act.id">
            {{ act.name }}
          </option>
        </select>
      </div>

      <!-- Datumintervall -->
      <div class="filter-group">
        <label>Datumintervall (valfritt):</label>
        <div class="date-inputs">
          <input
            type="text"
            v-model="dateFrom"
            placeholder="dd-mm-yyyy"
            @input="(e) => e.target.value = e.target.value.replace(/[^0-9-]/g, '')"
          />
          <span>till</span>
          <input
            type="text"
            v-model="dateTo"
            placeholder="dd-mm-yyyy"
            @input="(e) => e.target.value = e.target.value.replace(/[^0-9-]/g, '')"
          />
        </div>
        <small>Format: dd-mm-yyyy (t.ex. 01-02-2026)</small>
      </div>

      <!-- Knappar -->
      <div class="filter-actions">
        <button @click="fetchAndRenderChart" class="btn-primary">Uppdatera diagram</button>
        <button @click="resetFilters" class="btn-secondary">Återställ</button>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-wrapper">
      <div v-if="loading" class="loading">Laddar data...</div>
      <div v-else-if="error" class="error">
        <strong>Fel:</strong> {{ error }}
        <div v-if="error.includes('Ingen data hittades')" class="help-text">
          <p>För att se diagram behöver du:</p>
          <ol>
            <li>Skapa en aktivitetstyp (t.ex. "Löpning")</li>
            <li>Lägg till workouts med datum, distans och duration</li>
            <li>Uppdatera diagrammet</li>
          </ol>
        </div>
      </div>

      <!-- Visa flera diagram när filterType är 'all' -->
      <div v-if="!loading && !error && filterType === 'all'">
        <div
          v-for="activity in activitiesWithData"
          :key="activity.id"
          class="chart-item"
        >
          <div :id="`chart-${activity.id}`"></div>
        </div>
      </div>

      <!-- Visa ett diagram när en specifik aktivitet är vald -->
      <div v-if="!loading && !error && filterType === 'activity'" id="chart-single"></div>
    </div>
  </div>
</template>

<style scoped>
.chart-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.navigation {
  margin-bottom: 20px;
}

.btn-nav {
  padding: 12px 24px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-nav:hover {
  background-color: #45a049;
}

.filters {
  background: #f5f5f5;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.filters h3 {
  margin-top: 0;
  margin-bottom: 15px;
}

.filter-group {
  margin-bottom: 15px;
}

.filter-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 5px;
}

.radio-group {
  display: flex;
  gap: 15px;
}

.radio-group label {
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: normal;
}

select, input[type="text"] {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

select {
  width: 100%;
  max-width: 300px;
}

.date-inputs {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.date-inputs input {
  flex: 1;
  min-width: 140px;
}

small {
  display: block;
  color: #666;
  margin-top: 5px;
}

.filter-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.btn-primary, .btn-secondary {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: #008FFB;
  color: white;
}

.btn-primary:hover {
  background-color: #0077d4;
}

.btn-secondary {
  background-color: #e0e0e0;
  color: #333;
}

.btn-secondary:hover {
  background-color: #d0d0d0;
}

.chart-wrapper {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.error {
  background-color: #fee;
  color: #c33;
  padding: 15px;
  border-radius: 4px;
  border-left: 4px solid #c33;
}

.help-text {
  margin-top: 15px;
  color: #666;
  background: #f9f9f9;
  padding: 10px;
  border-radius: 4px;
}

.help-text ol {
  margin: 10px 0 0 20px;
}

.chart-item {
  margin-bottom: 40px;
  min-height: 400px;
}

.chart-item:last-child {
  margin-bottom: 0;
}
</style>
