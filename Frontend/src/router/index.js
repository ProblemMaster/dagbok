// router/index.js
import { createRouter, createWebHistory } from "vue-router"
import ActivitiesView from "@/views/ActivitiesView.vue"
import WorkoutView from "@/views/WorkoutView.vue"
import NotFoundView from "@/views/NotFoundView.vue"
import ChartView from "@/views/ChartView.vue"

const routes = [
  { path: "/", redirect: "/charts" },
  { path: "/activities", component: ActivitiesView },
  { path: "/workouts", component: WorkoutView },
  { path: "/charts", component: ChartView },
  { path: "/:pathMatch(.*)*", component: NotFoundView }

]

export default createRouter({
  history: createWebHistory(),
  routes
})
