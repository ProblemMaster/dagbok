// @/api/api.js
const API_BASE_URL = 'http://localhost:8000'

export default {
  // ============ ACTIVITIES ============
  // Hämta alla aktivitetstyper (Löpning, Yoga, etc.)
  async getActivities() {
    const response = await fetch(`${API_BASE_URL}/activities`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Skapa ny aktivitetstyp
  async createActivity(activityData) {
    const response = await fetch(`${API_BASE_URL}/activities`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(activityData)
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Uppdatera aktivitetstyp
  async updateActivity(id, activityData) {
    const response = await fetch(`${API_BASE_URL}/activities/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(activityData)
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Ta bort aktivitetstyp
  async deleteActivity(id) {
    const response = await fetch(`${API_BASE_URL}/activities/${id}`, {
      method: 'DELETE'
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // ============ WORKOUTS ============
  // Hämta alla workouts
  async getWorkouts() {
    const response = await fetch(`${API_BASE_URL}/workouts`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Skapa ny workout
  async createWorkout(workoutData) {
    const response = await fetch(`${API_BASE_URL}/workouts`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(workoutData)
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Uppdatera workout
  async updateWorkout(id, workoutData) {
    const response = await fetch(`${API_BASE_URL}/workouts/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(workoutData)
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Ta bort workout
  async deleteWorkout(id) {
    const response = await fetch(`${API_BASE_URL}/workouts/${id}`, {
      method: 'DELETE'
    })
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // ============ STATISTICS ============
  // Hämta statistik för specifik aktivitet
  async getActivityStatistics(activityId) {
    const response = await fetch(`${API_BASE_URL}/statistics/activity/${activityId}`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Hämta statistik för alla aktiviteter
  async getAllStatistics() {
    const response = await fetch(`${API_BASE_URL}/statistics/all`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // ============ TIMELINE (för diagram) ============
  // Hämta timeline för alla aktiviteter
  async getAllTimeline(from = null, to = null) {
    let url = `${API_BASE_URL}/statistics/timeline/all`

    if (from && to) {
      url += `?from=${from}&to=${to}`
    }

    const response = await fetch(url)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // Hämta timeline för specifik aktivitet
  async getActivityTimeline(activityId, from = null, to = null) {
    let url = `${API_BASE_URL}/statistics/timeline/${activityId}`

    if (from && to) {
      url += `?from=${from}&to=${to}`
    }

    const response = await fetch(url)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  },

  // ============ PDF EXPORT ============
  // Exportera workouts som PDF
  async exportWorkoutsPdf() {
    const response = await fetch(`${API_BASE_URL}/workouts/pdf`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

    // Returnera blob för PDF-nedladdning
    return await response.blob()
  },

  // Hjälpfunktion för att ladda ner PDF
  downloadPdf(blob, filename = 'workouts.pdf') {
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(url)
    document.body.removeChild(a)
  }
}
