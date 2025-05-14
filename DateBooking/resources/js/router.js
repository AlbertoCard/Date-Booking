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
    path: "/:pathMatch(.*)*", 
    component: NotFound,
    meta: { requiresAuth: false }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Aplicar middlewares
router.beforeEach(auth);  // Primero verificamos autenticación
router.beforeEach(role);  // Luego verificamos roles

export default router;