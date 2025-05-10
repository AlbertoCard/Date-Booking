import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./components/Inicio.vue";
import Nosotros from "./components/Nosotros.vue";
import NotFound from "./components/NotFound.vue";
import NuevoServicio from "./components/NuevoServicio.vue";
import VerServicio from "./components/VerServicios.vue";

const routes = [
  { path: "/", component: Inicio },
  { path: "/inicio", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/nuevo-servicio", component: NuevoServicio },
  { path: "/servicio-agregados", component: VerServicio },
  { path: "/:pathMatch(.*)*", component: NotFound },
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;