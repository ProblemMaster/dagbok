// router/index.js
import { createRouter, createWebHistory } from "vue-router"
import ActivitiesView from "@/views/ActivitiesView.vue"
import WorkoutView from "@/views/WorkoutView.vue"
import NotFoundView from "@/views/NotFoundView.vue"

const routes = [
  { path: "/", redirect: "/workouts" },
  { path: "/activities", component: ActivitiesView },
  { path: "/workouts", component: WorkoutView },
  { path: "/:pathMatch(.*)*", component: NotFoundView }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
