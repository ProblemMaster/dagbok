let activities = []

export async function sendForm(data) {
  return new Promise(resolve => {
    setTimeout(() => {
      activities.push({ id: activities.length + 1, ...data })
      resolve({ success: true })
    }, 500)
  })
}

export async function getActivities() {
  return new Promise(resolve => {
    setTimeout(() => resolve([...activities]), 500)
  })
}
