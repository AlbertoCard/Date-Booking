import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import Login from "./Components/Login.vue";
import RestablecerContraseña from "./Components/RestablecerContraseña.vue";
import DashboardCliente from "./Components/DashboardCliente.vue";
import DashboardEstablecimiento from "./Components/DashboardEstablecimiento.vue";
import Registro from "./Components/Registro.vue";

// Importar middlewares
import auth from './middleware/auth';
import role from './middleware/role';
import NuevoServicio from "./components/NuevoServicio.vue";
import VerServicio from "./Components/VerServicios.vue";
import Dashboard from "./components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import NuevaDisponibilidad from "./Components/NuevaDisponibilidad.vue";
import DetalleServicio from "./Components/DetalleServicio.vue";
import Busqueda from "./Components/Busqueda.vue";

const routes = [
  { 
    path: "/", 
    component: Inicio,
    meta: { requiresAuth: false }
  },
  { 
    path: "/inicio", 
    component: Inicio,
    meta: { requiresAuth: false }
  },
  { 
    path: "/nosotros", 
    component: Nosotros,
    meta: { requiresAuth: false }
  },
  { 
    path: "/login", 
    component: Login,
    meta: { requiresAuth: false }
  },
  { 
    path: "/dashboard-cliente", 
    component: DashboardCliente,
    meta: { 
      requiresAuth: true,
      role: 'cliente'
    }
  },
  { 
    path: "/dashboard-establecimiento", 
    component: DashboardEstablecimiento,
    meta: { 
      requiresAuth: true,
      role: 'establecimiento'
    }
  },
  { 
    path: "/reset-password", 
    component: RestablecerContraseña,
    meta: { requiresAuth: false }
  },
  { 
    path: "/registro", 
    component: Registro,
    meta: { requiresAuth: false }
  },
  { 
    path: "/:pathMatch(.*)*", 
    component: NotFound,
    meta: { requiresAuth: false }
  },
  { path: "/", component: Inicio },
  { path: "/nosotros", component: Nosotros },
  { path: "/detalle-servicio/:id", component: DetalleServicio },
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
  { path: "/:pathMatch(.*)*", component: NotFound },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Aplicar middlewares
router.beforeEach(auth);  // Primero verificamos autenticación
router.beforeEach(role);  // Luego verificamos roles

export default router;