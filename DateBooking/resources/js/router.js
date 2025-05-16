import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./components/Inicio.vue";
import Nosotros from "./components/Nosotros.vue";
import NotFound from "./components/NotFound.vue";
<<<<<<< HEAD
import NuevoServicio from "./components/NuevoServicio.vue";
import VerServicio from "./Components/VerServicios.vue";
import Login from "./components/Login.vue";
import RestablecerContraseña from "./components/RestablecerContraseña.vue";
import Dashboard from "./components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./components/Registro.vue";
import NuevaDisponibilidad from "./Components/NuevaDisponibilidad.vue";
=======

>>>>>>> 01eea8bacf528dd3c4b6622d59aa798f1b6498ac
import DetalleServicio from "./Components/DetalleServicio.vue";

const routes = [
  { path: "/inicio", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/detalle-servicio/:id", component: DetalleServicio },
  { path: "/:pathMatch(.*)*", component: NotFound },
<<<<<<< HEAD
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  { path: "/reset-password", component: RestablecerContraseña },
  { path: "/registro", component: Registro },
  { path: "/busqueda/:search", component: Busqueda },
  { path: "/servicios", component: Servicios },
  {path: "/validaciones", component: Validaciones},
=======
>>>>>>> 01eea8bacf528dd3c4b6622d59aa798f1b6498ac
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;