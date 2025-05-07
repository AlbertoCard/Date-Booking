import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./components/Inicio.vue";
import Nosotros from "./components/Nosotros.vue";
import NotFound from "./components/NotFound.vue";
import Login from "./components/Login.vue";
import Dashboard from "./components/Dashboard.vue";

const routes = [
  { path: "/inicio", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/:pathMatch(.*)*", component: NotFound },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;