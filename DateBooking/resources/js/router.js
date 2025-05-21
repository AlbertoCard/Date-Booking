import { createRouter, createWebHistory } from "vue-router";

<<<<<<< HEAD
import Inicio from "./Components/Inicio.vue";
import Nosotros from "./Components/Nosotros.vue";
import NotFound from "./Components/NotFound.vue";
import Login from "./Components/Login.vue";
import RestablecerContraseña from "./Components/RestablecerContraseña.vue";
import Dashboard from "./Components/Dashboard.vue";
import Registro from "./Components/Registro.vue";
import ReservaUsuario from "./Components/ReservaUsuario.vue";
=======
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
<<<<<<< HEAD
import Registro from "./components/Registro.vue";
import NuevaDisponibilidad from "./Components/NuevaDisponibilidad.vue";
import DetalleServicio from "./Components/DetalleServicio.vue";
<<<<<<< HEAD
import Busqueda from "./Components/Busqueda.vue";
>>>>>>> incremento-1
// import Registro from "./components/Registro.vue";
=======
>>>>>>> 4a80199 (Vistas de servicios, creacion de nuevo servicio y disponibilidad)

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
<<<<<<< HEAD
  { path: "/reservausuario", component: ReservaUsuario }
=======
  { path: "/busqueda/:search", component: Busqueda },
  { path: "/servicios", component: Servicios },
  {path: "/validaciones", component: Validaciones},
=======
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
    { path: "/reservas/:id", component: MisReservas },

    { path: "/", component: Inicio, meta: { requiresAuth: false } },
    { path: "/inicio", component: Inicio, meta: { requiresAuth: false } },
    { path: "/nosotros", component: Nosotros, meta: { requiresAuth: false } },
    { path: "/login", component: Login, meta: { requiresAuth: false } },
    {
        path: "/dashboard-cliente",
        component: DashboardCliente,
        meta: { requiresAuth: true, role: "cliente" },
    },
    {
        path: "/dashboard-establecimiento",
        component: DashboardEstablecimiento,
        meta: { requiresAuth: true, role: "establecimiento" },
    },
    {
        path: "/reset-password",
        component: RestablecerContraseña,
        meta: { requiresAuth: false },
    },
    { path: "/registro", component: Registro, meta: { requiresAuth: false } },
    {
        path: "/detalle-servicio/:id",
        component: DetalleServicio,
        meta: { requiresAuth: false },
    },
    {
        path: "/nuevo-servicio",
        component: NuevoServicio,
        meta: { requiresAuth: true },
    },
    {
        path: "/servicio-agregados",
        component: VerServicio,
        meta: { requiresAuth: true },
    },
    {
        path: "/editar-servicio/:id",
        component: EditarServicio,
        meta: { requiresAuth: true },
    },
    { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true } },
    {
        path: "/busqueda/:search",
        component: Busqueda,
        meta: { requiresAuth: false },
    },
    { path: "/servicios", component: Servicios, meta: { requiresAuth: true } },
    {
        path: "/validaciones",
        component: Validaciones,
        meta: { requiresAuth: true },
    },
    {
        path: "/servicio/:id",
        name: "detalle-servicio",
        component: DetalleServicio,
        meta: { requiresAuth: false },
    },
    {
        path: "/establecimientos/:nombre",
        component: Establecimiento,
        meta: { requiresAuth: false },
    },
    {
        path: "/:pathMatch(.*)*",
        component: NotFound,
        meta: { requiresAuth: false },
    },
>>>>>>> 46894ccfec96818f67ff8f9f85f49ec6e7efa3b8
>>>>>>> incremento-1
];

const history = createWebHistory();

const router = createRouter({
  history,
  routes,
});

<<<<<<< HEAD
=======
// Aplicar middlewares
router.beforeEach(auth); // Primero verificamos autenticación
router.beforeEach(role); // Luego verificamos roles

>>>>>>> 46894ccfec96818f67ff8f9f85f49ec6e7efa3b8
export default router;