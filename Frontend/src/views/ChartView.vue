<template>
  <h1>Översikt</h1>

  <RouterLink to="/workouts" class="nav-button">
    Gå till Workouts
  </RouterLink>

<div id="chart"></div>


</template>

<script setup>
import { onMounted } from "vue"
import ApexCharts from "apexcharts"

onMounted(() => {
  const options = {
    series: [
      { name: "Session Duration", data: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10] },
      { name: "Page Views", data: [35, 41, 62, 42, 13, 18, 29, 37, 36, 51, 32, 35] }
    ],
    chart: { type: "line", height: 350 },
    xaxis: {
      categories: ["01 Jan","02 Jan","03 Jan","04 Jan","05 Jan","06 Jan","07 Jan","08 Jan","09 Jan","10 Jan","11 Jan","12 Jan"]
    }
  }

  const chart = new ApexCharts(
    document.querySelector("#chart"),
    options
  )

  chart.render()
})
</script>
