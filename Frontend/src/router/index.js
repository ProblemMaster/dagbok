// router/index.js
import { createRouter, createWebHistory } from "vue-router"
import ActivitiesView from "@/views/ActivitiesView.vue"
import WorkoutView from "@/views/WorkoutView.vue"

const routes = [
  { path: "/", redirect: "/activities" },
  { path: "/activities", component: ActivitiesView },
  { path: "/workout", component: WorkoutView }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
