import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./components/Inicio.vue";
import Nosotros from "./components/Nosotros.vue";
import NotFound from "./components/NotFound.vue";
import NuevoServicio from "./components/NuevoServicio.vue";
import VerServicio from "./Components/VerServicios.vue";
import Login from "./components/Login.vue";
import RestablecerContraseña from "./components/RestablecerContraseña.vue";
import Dashboard from "./components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./components/Registro.vue";
import NuevaDisponibilidad from "./Components/NuevaDisponibilidad.vue";
import DetalleServicio from "./Components/DetalleServicio.vue";
import Busqueda from "./Components/Busqueda.vue";
// import Registro from "./components/Registro.vue";

const routes = [
  { path: "/", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/nuevo-servicio", component: NuevoServicio },
  { path: "/servicio-agregados", component: VerServicio },
  { path: "/nueva-disponibilidad/:id", component: NuevaDisponibilidad },
  { path: "/detalle-servicio/:id", component: DetalleServicio },
  { path: "/:pathMatch(.*)*", component: NotFound },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  { path: "/reset-password", component: RestablecerContraseña },
  { path: "/registro", component: Registro },
  { path: "/busqueda/:search", component: Busqueda },
  { path: "/servicios", component: Servicios },
  {path: "/validaciones", component: Validaciones},
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;