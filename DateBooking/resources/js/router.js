import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./components/Inicio.vue";
import Nosotros from "./components/Nosotros.vue";
import NotFound from "./components/NotFound.vue";
import NuevoServicio from "./Components/Servicios/NuevoServicio.vue";
import VerServicio from "./Components/Servicios/VerServicios.vue";
import Login from "./components/Auth/Login.vue";
import RestablecerContraseña from "./components/Auth/RestablecerContraseña.vue";
import Dashboard from "./components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./Components/Auth/Registro.vue";
import NuevaDisponibilidad from "./Components/Servicios/NuevaDisponibilidad.vue";
import DetalleServicio from "./Components/Servicios/DetalleServicio.vue";
import Busqueda from "./Components/Servicios/Busqueda.vue";
import Establecimiento from "./Components/Establecimientos/Establecimiento.vue";
import MisReservas from "./Components/Reservas/MisReservas.vue";

const routes = [
  { path: "/", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/detalle-servicio/:name", component: DetalleServicio },
  { path: "/nuevo-servicio", component: NuevoServicio },
  { path: "/servicio-agregados", component: VerServicio },
  { path: "/nueva-disponibilidad/:id", component: NuevaDisponibilidad },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  { path: "/reset-password", component: RestablecerContraseña },
  { path: "/registro", component: Registro },
  { path: "/busqueda/:search", component: Busqueda },
  { path: "/servicios", component: Servicios },
  { path: "/validaciones", component: Validaciones },
  {path: "/establecimientos/:nombre", component: Establecimiento},
  { path: "/reservas/:id", component: MisReservas },
  { path: "/:pathMatch(.*)*", component: NotFound },
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;