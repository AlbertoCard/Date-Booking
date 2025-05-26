import { createRouter, createWebHistory } from "vue-router";

import Inicio from "./Components/Inicio.vue";
import NotFound from "./Components/NotFound.vue";
import NuevoServicio from "./Components/Servicios/NuevoServicio.vue";
import VerServicio from "./Components/Servicios/VerServicios.vue";
import Login from "./Components/Auth/Login.vue";
import RestablecerContraseña from "./Components/Auth/RestablecerContraseña.vue";
import Dashboard from "./Components/Dashboard.vue";
import Servicios from "./Components/Admin/Servicios.vue";
import Validaciones from "./Components/Admin/Validaciones.vue";
import Registro from "./Components/Auth/Registro.vue";
import EditarServicio from "./Components/Servicios/EditarServicio.vue";
import DetalleServicio from "./Components/Servicios/DetalleServicio.vue";
import Busqueda from "./Components/Servicios/Busqueda.vue";
import Establecimiento from "./Components/Establecimientos/Establecimiento.vue";
import MisReservas from "./Components/Reservas/MisReservas.vue";
import NodoServicio from "./Components/Servicios/NodoServicio.vue";
import AgregarResena from './Components/Reservas/AgregarResena.vue';
import StripePayment from './Components/StripePayment.vue';

import HotelForm from './Components/Servicios/HotelForm.vue';
import EventoForm from './Components/Servicios/EventoForm.vue';
import RestauranteForm from './Components/Servicios/RestauranteForm.vue';
import ConsultorioForm from './Components/Servicios/ConsultorioForm.vue';

import ReservaConsultorio from './Components/Reservas/ReservaConsultorio.vue';
import ReservaRestaurante from './Components/Reservas/ReservaRestaurante.vue';
import ReservaEvento from './Components/Reservas/ReservaEvento.vue';
import ReservaHotel from './Components/Reservas/ReservaHotel.vue';

import LectorQr from './Components/Reservas/LectorQr.vue';
import ServicioListado from './Components/Servicios/ServicioListado.vue';
import ListadoDetalle from "./Components/Servicios/ListadoDetalle.vue";
import Afiliados from "./Components/Afiliados/Afiliados.vue";




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
    {
        path: "/nodo-servicio/:id",
        component: NodoServicio,
        meta: { 
            requiresAuth: false,
            title: 'Nodo Servicio'  
        }
    },
    {
        path: "/reserva-consultorio/:id",
        component: ReservaConsultorio,
        meta: { 
            requiresAuth: true,
            title: 'Reserva de Consultorio'
        }
    },
    {
        path: "/reserva-restaurante/:id",
        component: ReservaRestaurante,
        meta: { 
            requiresAuth: true,
            title: 'Reserva de Restaurante'
        }
    },
    {
        path: "/reserva-evento/:id",
        component: ReservaEvento,
        meta: { 
            requiresAuth: true,
            title: 'Reserva de Evento'
        }
    },
    {
        path: "/reserva-hotel/:id",
        component: ReservaHotel,
        meta: { 
            requiresAuth: true,
            title: 'Reserva de Hotel'
        }
    },

    // Rutas para clientes
    {
        path: "/reservas/:id",
        component: MisReservas,
        meta: { 
            requiresAuth: true,
            role: "cliente",
            title: 'Mis Reservas'
        }
    },
    {
        path: '/detalle-reserva/:id',
        component: () => import('./Components/Reservas/DetalleReserva.vue'),
        meta: {
            requiresAuth: true,
            requiresRole: 'cliente',
            title: 'Detalle de Reserva'
        }
    },
    {
        path: '/agregar-resena/:id',
        name: 'agregar-resena',
        component: AgregarResena,
        meta: { 
            requiresAuth: true,
            role: "cliente",
            title: 'Añadir Reseña'
        }
    },
    {
        path: '/lector-qr',
        name: 'lector-qr',
        component: LectorQr,
        meta: { 
            requiresAuth: true,
            role: "cliente",
            title: 'Lector QR'
        },
    },
    // Rutas para establecimientos
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
    {
        path: "/nuevo-servicio/hotel",
        component: HotelForm,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Nuevo Servicio de Hotel'
        }
    },
    {
        path: "/nuevo-servicio/evento",
        component: EventoForm,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Nuevo Servicio de Evento'
        }
    },
    {
        path: "/nuevo-servicio/restaurante",
        component: RestauranteForm,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Nuevo Servicio de Restaurante'
        }
    },
    {
        path: "/nuevo-servicio/consultorio",
        component: ConsultorioForm,
        meta: { 
            requiresAuth: true,
            role: "establecimiento",
            title: 'Nuevo Servicio de Consultorio'
        }
    },
     {
        path: "/afiliados",
        component: Afiliados,
        meta: {
            requiresAuth: true,
            role: "establecimiento",
            title: 'Gestion de Afiliados'
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

    // Ruta para Afiliados
    {
        path: "/servicios-listado",
        component: ServicioListado,
        meta: {
            requiresAuth: true,
            role: 'afiliado',
            title: 'Listado de Servicios'
        }
    },
    {
        path: "/detalle-listado/:id",
        component: ListadoDetalle,
        meta: {
            requiresAuth: true,
            role: 'afiliado',
            title: 'Validacion de Reservas'
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
    },
    {
        path: '/prueba-pago',
        name: 'prueba-pago',
        component: StripePayment
    },
    
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