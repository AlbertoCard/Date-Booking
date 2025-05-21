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
    { 
        path: "/", 
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
        path: "/registro", 
        component: Registro, 
        meta: { requiresAuth: false } 
    },
    {
        path: "/dashboard-cliente",
        component: DashboardCliente,
        meta: { requiresAuth: true, role: "cliente" }
    },
    {
        path: "/dashboard-establecimiento",
        component: DashboardEstablecimiento,
        meta: { requiresAuth: true, role: "establecimiento" }
    },
    {
        path: "/reset-password",
        component: RestablecerContraseña,
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
        path: "/editar-servicio/:id",
        component: EditarServicio,
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
        path: "/establecimientos/:nombre",
        component: Establecimiento,
        meta: { requiresAuth: false }
    },
    {
        path: "/reservas/:id",
        component: MisReservas,
        meta: { requiresAuth: true }
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
router.beforeEach(auth); // Primero verificamos autenticación
router.beforeEach(role); // Luego verificamos roles

export default router;