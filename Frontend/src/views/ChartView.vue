<script setup>
import { ref, onMounted, watch } from "vue"
import ApexCharts from "apexcharts"
import api from "@/api/api"

// State
const loading = ref(true)
const error = ref(null)
const chart = ref(null)

// Filter state
const filterType = ref('all') // 'all' eller 'activity'
const selectedActivity = ref(null)
const dateFrom = ref('')
const dateTo = ref('')

// Lista över aktiviteter (hämtas från backend)
const activities = ref([])

// Funktion för att hämta och rendera data
const fetchAndRenderChart = async () => {
  loading.value = true
  error.value = null

  try {
    let data

    // Hämta data baserat på valt filter
    if (filterType.value === 'all') {
      data = await api.getAllTimeline(dateFrom.value, dateTo.value)
    } else {
      if (!selectedActivity.value) {
        error.value = 'Välj en aktivitet'
        loading.value = false
        return
      }
      data = await api.getActivityTimeline(selectedActivity.value, dateFrom.value, dateTo.value)
    }

    // Debug: Visa vad backend returnerar
    console.log('Backend data (raw):', data)

    // Om backend returnerar en array, ta första elementet
    if (Array.isArray(data)) {
      if (data.length === 0) {
        error.value = 'Ingen data hittades. Lägg till workouts i databasen först!'
        loading.value = false
        return
      }
      console.log('Data är en array med', data.length, 'element')
      data = data[0]
    }

    console.log('Data efter array-check:', data)

    // Kontrollera om data är null eller undefined
    if (!data) {
      error.value = 'Ingen data returnerades från backend'
      loading.value = false
      return
    }

    // Kontrollera om data har rätt struktur
    if (!data.distance || !data.duration) {
      console.error('Data saknar distance eller duration:', data)
      error.value = 'Backend returnerade ogiltig data-struktur. Saknar distance eller duration.'
      loading.value = false
      return
    }

    console.log('Distance data:', data.distance)
    console.log('Duration data:', data.duration)
    console.log('Labels:', data.labels)

    // Förbered data för ApexCharts
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

    // Förstör gammalt diagram om det finns
    if (chart.value) {
      chart.value.destroy()
    }

    // Vänta lite för att säkerställa att DOM är uppdaterad
    await new Promise(resolve => setTimeout(resolve, 0))

    // Kontrollera att chart-elementet finns innan vi renderar
    const chartElement = document.querySelector("#chart")
    console.log('Chart element:', chartElement)

    if (!chartElement) {
      error.value = 'Chart element hittades inte i DOM'
      loading.value = false
      return
    }

    console.log('Skapar ApexChart med options:', options)

    // Sätt loading till false INNAN vi renderar så elementet blir synligt
    loading.value = false

    // Vänta ett tick för att Vue ska uppdatera DOM
    await new Promise(resolve => setTimeout(resolve, 10))

    // Skapa nytt diagram
    chart.value = new ApexCharts(chartElement, options)
    await chart.value.render()

    console.log('Chart renderat!')

  } catch (err) {
    console.error('Fel vid hämtning av data:', err)
    error.value = err.message
    loading.value = false
  }
}

// Återställ filter
const resetFilters = () => {
  filterType.value = 'all'
  selectedActivity.value = null
  dateFrom.value = ''
  dateTo.value = ''
  fetchAndRenderChart()
}

// Navigera till workouts-sidan
const goToWorkouts = () => {
  // Ändra '/workouts' till din faktiska route
  window.location.href = '/workouts'
  // Eller om du använder Vue Router:
  // import { useRouter } from 'vue-router'
  // const router = useRouter()
  // router.push('/workouts')
}

// Hämta data vid första laddning
onMounted(async () => {
  // Hämta tillgängliga aktiviteter från backend
  try {
    activities.value = await api.getActivities()
    console.log('Aktiviteter hämtade:', activities.value)
  } catch (err) {
    console.error('Kunde inte hämta aktiviteter:', err)
  }

  // Rendera diagram med alla aktiviteter som default
  fetchAndRenderChart()
})

// Bevaka filtertyp-ändringar
watch(filterType, () => {
  if (filterType.value === 'all') {
    selectedActivity.value = null
  }
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
        <select id="activity-select" v-model="selectedActivity">
          <option :value="null">-- Välj aktivitet --</option>
          <option v-for="activity in activities" :key="activity.id" :value="activity.id">
            {{ activity.name }}
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
      <div id="chart" v-show="!loading && !error"></div>
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

#chart {
  min-height: 400px;
}
</style>
