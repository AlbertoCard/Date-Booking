import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import DashboardCliente from "./Components/DashboardCliente.vue";
import DashboardEstablecimiento from "./Components/DashboardEstablecimiento.vue";

import NuevoServicio from "./Components/Servicios/NuevoServicio.vue";
import VerServicio from "./Components/Servicios/VerServicios.vue";
import Login from "./Components/Auth/Login.vue";
import RestablecerContraseña from "./components/Auth/RestablecerContraseña.vue";
import Dashboard from "./components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./Components/Auth/Registro.vue";
import NuevaDisponibilidad from "./Components/Servicios/NuevaDisponibilidad.vue";
import DetalleServicio from "./Components/Servicios/DetalleServicio.vue";
import Busqueda from "./Components/Servicios/Busqueda.vue";
import Establecimiento from "./Components/Establecimientos/Establecimiento.vue";

// Importar middlewares
import auth from './middleware/auth';
import role from './middleware/role';


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
    path: "/detalle-servicio/:id", 
    component: DetalleServicio,
    meta: { requiresAuth: false }
  },
  { 
    path: "/nuevo-servicio", 
    component: NuevoServicio,
    meta: { requiresAuth: true }
  },
  { 
    path: "/servicio-agregados", 
    component: VerServicio,
    meta: { requiresAuth: true }
  },
  { 
    path: "/nueva-disponibilidad/:id", 
    component: NuevaDisponibilidad,
    meta: { requiresAuth: true }
  },
  { 
    path: "/dashboard", 
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  { 
    path: "/busqueda/:search", 
    component: Busqueda,
    meta: { requiresAuth: false }
  },
  { 
    path: "/servicios", 
    component: Servicios,
    meta: { requiresAuth: true }
  },
  { 
    path: "/validaciones", 
    component: Validaciones,
    meta: { requiresAuth: true }
  },
  { 
    path: "/servicio/:id",
    name: 'detalle-servicio',
    component: DetalleServicio,
    meta: { requiresAuth: false }
  },
  { 
    path: "/:pathMatch(.*)*", 
    component: NotFound,
    meta: { requiresAuth: false }
  },
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