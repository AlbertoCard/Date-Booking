import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import Login from "./Components/Login.vue";
import RestablecerContraseña from "./Components/RestablecerContraseña.vue";
import DashboardCliente from "./Components/DashboardCliente.vue";
import DashboardEstablecimiento from "./Components/DashboardEstablecimiento.vue";
import Registro from "./Components/Registro.vue";
// import Registro from "./components/Registro.vue";
const routes = [
  { path: "/", component: Inicio },
  { path: "/inicio", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/:pathMatch(.*)*", component: NotFound },
  { path: "/login", component: Login },
  { path: "/dashboard-cliente", component: DashboardCliente },
  { path: "/dashboard-establecimiento", component: DashboardEstablecimiento },
  { path: "/reset-password", component: RestablecerContraseña },
  { path: "/registro", component: Registro },
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

// Middleware de autenticación
router.beforeEach(async (to, from, next) => {
  const publicPages = ['/', '/inicio', '/nosotros', '/login', '/registro', '/reset-password'];
  const authRequired = !publicPages.includes(to.path);
  const auth = await import('./firebase').then(module => module.auth);
  
  if (authRequired && !auth.currentUser) {
    next('/login');
  } else {
    next();
  }
});

export default router;