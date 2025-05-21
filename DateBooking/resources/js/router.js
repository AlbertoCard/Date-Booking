import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import Login from "./Components/Login.vue";
import RestablecerContraseña from "./Components/RestablecerContraseña.vue";
import Dashboard from "./Components/Dashboard.vue";
import Registro from "./Components/Registro.vue";
import ReservaUsuario from "./Components/ReservaUsuario.vue";
// import Registro from "./components/Registro.vue";
const routes = [
  { path: "/", component: Inicio },
  { path: "/inicio", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/:pathMatch(.*)*", component: NotFound },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  { path: "/reset-password", component: RestablecerContraseña },
  { path: "/registro", component: Registro },
  { path: "/reservausuario", component: ReservaUsuario }
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

export default router;