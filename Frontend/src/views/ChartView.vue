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

// Hämta aktiviteter för dropdown
const activities = ref([])

onMounted(async () => {
  try {
    activities.value = await api.getActivities()
  } catch (error) {
    console.error('Fel:', error)
  }
})

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

    // Skapa nytt diagram
    chart.value = new ApexCharts(
      document.querySelector("#chart"),
      options
    )
    chart.value.render()
    loading.value = false

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

// Hämta data vid första laddning
onMounted(() => {
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
  <h1>Översikt</h1>

  <RouterLink to="/workouts" class="nav-button">
    Gå till Workouts
  </RouterLink>
  <div class="chart-container">
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
      </div>
      <div v-else id="chart"></div>
    </div>
  </div>
</template>
