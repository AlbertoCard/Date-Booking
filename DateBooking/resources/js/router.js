import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import NuevoServicio from "./Components/Servicios/NuevoServicio.vue";
import VerServicio from "./Components/Servicios/VerServicios.vue";
import Login from "./Components/Auth/Login.vue";
import RestablecerContraseña from "./Components/Auth/RestablecerContraseña.vue";
import DashboardCliente from "./Components/DashboardCliente.vue";
import DashboardEstablecimiento from "./Components/DashboardEstablecimiento.vue";
import Dashboard from "./Components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./Components/Auth/Registro.vue";
import EditarServicio from "./Components/Servicios/EditarServicio.vue";
import DetalleServicio from "./Components/Servicios/DetalleServicio.vue";
import Busqueda from "./Components/Servicios/Busqueda.vue";
import Establecimiento from "./Components/Establecimientos/Establecimiento.vue";
import MisReservas from "./Components/Reservas/MisReservas.vue";

// Importar middlewares
import auth from "./middleware/auth";
import role from "./middleware/role";

const routes = [
    // Rutas públicas
    { 
        path: "/", 
        component: Inicio, 
        meta: { 
            requiresAuth: false,
            title: 'Inicio'
        } 
    },
    { 
        path: "/nosotros", 
        component: Nosotros, 
        meta: { 
            requiresAuth: false,
            title: 'Nosotros'
        } 
    },
    { 
        path: "/login", 
        component: Login, 
        meta: { 
            requiresAuth: false,
            title: 'Iniciar Sesión'
        } 
    },
    { 
        path: "/registro", 
        component: Registro, 
        meta: { 
            requiresAuth: false,
            title: 'Registro'
        } 
    },
    {
        path: "/reset-password",
        component: RestablecerContraseña,
        meta: { 
            requiresAuth: false,
            title: 'Restablecer Contraseña'
        }
    },
    {
        path: "/servicio/:id",
        component: DetalleServicio,
        meta: { 
            requiresAuth: false,
            title: 'Detalle del Servicio'
        }
    },
    {
        path: "/busqueda/:search",
        component: Busqueda,
        meta: { 
            requiresAuth: false,
            title: 'Resultados de Búsqueda'
        }
    },
    {
        path: "/establecimientos/:nombre",
        component: Establecimiento,
        meta: { 
            requiresAuth: false,
            title: 'Establecimiento'
        }
    },

    // Rutas para clientes
    {
        path: "/dashboard-cliente",
        component: DashboardCliente,
        meta: { 
            requiresAuth: true, 
            role: "cliente",
            title: 'Dashboard Cliente'
        }
    },
    {
        path: "/reservas/:id",
        component: MisReservas,
        meta: { 
            requiresAuth: true,
            role: "cliente",
            title: 'Mis Reservas'
        }
    },

    // Rutas para establecimientos
    {
        path: "/dashboard-establecimiento",
        component: DashboardEstablecimiento,
        meta: { 
            requiresAuth: true, 
            role: "establecimiento",
            title: 'Dashboard Establecimiento'
        }
    },
    {
        path: "/nuevo-servicio",
        component: NuevoServicio,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Nuevo Servicio'
        }
    },
    {
        path: "/servicio-agregados",
        component: VerServicio,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Mis Servicios'
        }
    },
    {
        path: "/editar-servicio/:id",
        component: EditarServicio,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Editar Servicio'
        }
    },

    // Rutas para administradores
    { 
        path: "/dashboard", 
        component: Dashboard, 
        meta: { 
            requiresAuth: true,
            role: "admin",
            title: 'Dashboard Admin'
        } 
    },
    { 
        path: "/servicios", 
        component: Servicios, 
        meta: { 
            requiresAuth: true,
            role: "admin",
            title: 'Gestión de Servicios'
        } 
    },
    {
        path: "/validaciones",
        component: Validaciones,
        meta: { 
            requiresAuth: true,
            role: "admin",
            title: 'Validaciones'
        }
    },

    // Ruta 404
    {
        path: "/:pathMatch(.*)*",
        component: NotFound,
        meta: { 
            requiresAuth: false,
            title: 'Página no encontrada'
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Aplicar middlewares
router.beforeEach(auth); // Primero verificamos autenticación
router.beforeEach(role); // Luego verificamos roles

// Actualizar el título de la página
router.beforeEach((to, from, next) => {
    document.title = to.meta.title ? `${to.meta.title} - Date Booking` : 'Date Booking';
    next();
});

export default router;