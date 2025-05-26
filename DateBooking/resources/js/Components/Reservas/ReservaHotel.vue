<template>
    <!-- Contenedor principal con fondo gris claro -->
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-6xl mx-auto p-6">
            <!-- Estado de carga -->
            <div v-if="cargando" class="flex justify-center items-center min-h-[400px]">
                <Loader :visible="true" />
            </div>

            <!-- Error state -->
            <div v-else-if="error" class="text-center py-12">
                <div class="text-red-500 text-xl">
                    {{ error }}
                </div>
            </div>

            <div v-else class="space-y-8">
                <!-- Encabezado con navegación -->
                <nav class="flex items-center space-x-4 py-4 border-b">
                    <button @click="volver" class="text-gray-600 hover:text-black transition-colors">
                        <span class="text-2xl">←</span>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-600">Servicios</span>
                        <span class="text-gray-400">/</span>
                        <span class="font-medium">Reserva de Hotel</span>
                    </div>
                </nav>

                <!-- Contenedor del contenido principal -->
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Columna izquierda: Información del servicio -->
                        <div class="space-y-8">
                            <!-- Información del servicio -->
                            <div class="space-y-6">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                        {{ servicio?.nombre || 'Nombre del Hotel' }}
                                    </h1>
                                    <p class="text-gray-600 leading-relaxed">
                                        {{ servicio?.descripcion || 'Descripción del hotel' }}
                                    </p>
                                </div>

                                <!-- Detalles adicionales -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-sm text-gray-500">Categoría</span>
                                        <p class="font-medium text-gray-900">{{ servicio?.categoria || 'No especificada'
                                        }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-sm text-gray-500">Establecimiento</span>
                                        <p class="font-medium text-gray-900">{{ servicio?.establecimiento?.nombre ||
                                            'No especificado' }}</p>
                                    </div>
                                </div>

                                <!-- Ubicación -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                                            <span class="text-xl">📍</span>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500 block">Ubicación</span>
                                            <p class="font-medium text-gray-900">{{ servicio?.ciudad?.nombre ||
                                                'Ciudad no especificada' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Formulario de reserva -->
                        <div class="bg-gray-50 rounded-xl p-6 space-y-8">
                            <!-- Formulario de fechas -->
                            <div class="space-y-6">
                                <!-- Fecha de llegada -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">📅</span>
                                        <span>Fecha de llegada</span>
                                    </label>
                                    <input type="date" v-model="fechaInicio" :min="fechaMinima"
                                        class="w-full p-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Fecha de salida -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">📅</span>
                                        <span>Fecha de salida</span>
                                    </label>
                                    <input type="date" v-model="fechaFin" :min="fechaInicio || fechaMinima"
                                        class="w-full p-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Selección de habitación -->
                            <div v-if="habitaciones.length > 0" class="space-y-4">
                                <h3 class="font-medium text-gray-900">Habitaciones disponibles</h3>
                                <div class="grid gap-4">
                                    <div v-for="habitacion in habitaciones" :key="habitacion.id_habitacion"
                                        class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-500 cursor-pointer transition-colors"
                                        :class="{ 'border-blue-500 bg-blue-50': habitacionSeleccionada === habitacion.id_habitacion }"
                                        @click="seleccionarHabitacion(habitacion)">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ habitacion.tipo }}</h4>
                                                <p class="text-sm text-gray-600">Capacidad: {{ habitacion.capacidad }}
                                                    personas</p>
                                                <p class="text-sm text-gray-600">Número: {{ habitacion.numero }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">${{ servicio?.costo || 0 }}</p>
                                                <p class="text-sm text-gray-500">por noche</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de la estadía -->
                            <div v-if="habitacionSeleccionada && fechaInicio && fechaFin"
                                class="bg-white p-4 rounded-lg border border-gray-200">
                                <h3 class="font-medium text-gray-900 mb-2">Resumen de tu estadía</h3>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p>• Llegada: {{ formatearFecha(fechaInicio) }}</p>
                                    <p>• Salida: {{ formatearFecha(fechaFin) }}</p>
                                    <p>• Noches: {{ calcularNoches }}</p>
                                    <p>• Total: ${{ calcularTotal }}</p>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h3 class="font-medium text-gray-900 mb-2">Información Importante</h3>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p>• La reserva se mantendrá por 15 minutos</p>
                                    <p>• Se requiere confirmación de pago</p>
                                    <p>• Cancelaciones con 24 horas de anticipación</p>
                                    <p>• Check-in: 15:00 hrs</p>
                                    <p>• Check-out: 12:00 hrs</p>
                                </div>
                            </div>

                            <!-- Botón de reserva -->
                            <button @click="realizarReserva" :disabled="!puedeReservar" class="w-full bg-black text-white py-4 rounded-lg font-medium 
                                hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black 
                                disabled:bg-gray-300 disabled:cursor-not-allowed
                                transition-all duration-200 transform hover:scale-[1.02]">
                                {{ puedeReservar ? 'Realizar Reservación!' : 'Selecciona una habitación' }}
                            </button>

                            <!-- Nota informativa -->
                            <p class="text-center text-sm text-gray-500">
                                Al realizar la reserva, aceptas nuestros términos y condiciones de servicio
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Loader from '../Loader.vue';
import { loadStripe } from '@stripe/stripe-js';

const router = useRouter();
const route = useRoute();
const servicio = ref(null);
const cargando = ref(true);
const error = ref(null);
const fechaInicio = ref('');
const fechaFin = ref('');
const habitaciones = ref([]);
const habitacionSeleccionada = ref(null);

// Computed properties
const fechaMinima = computed(() => {
    const hoy = new Date();
    return hoy.toISOString().split('T')[0];
});

const calcularNoches = computed(() => {
    if (!fechaInicio.value || !fechaFin.value) return 0;
    const inicio = new Date(fechaInicio.value);
    const fin = new Date(fechaFin.value);
    const diffTime = Math.abs(fin - inicio);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const calcularTotal = computed(() => {
    if (!servicio.value?.costo || !calcularNoches.value) return 0;
    return servicio.value.costo * calcularNoches.value;
});

const puedeReservar = computed(() => {
    return habitacionSeleccionada.value && fechaInicio.value && fechaFin.value;
});

// Métodos
const volver = () => {
    router.back();
};

const formatearFecha = (fecha) => {
    if (!fecha) return '';
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const año = date.getFullYear();
    return `${dia}/${mes}/${año}`;
};

const cargarServicio = async () => {
    try {
        const servicioResponse = await axios.get(`/api/servicios/${route.params.id}`);
        servicio.value = servicioResponse.data;
        await cargarHabitaciones();
    } catch (err) {
        console.error('Error al cargar los datos:', err);
        error.value = `Error al cargar los datos: ${err.response?.data?.message || err.message}`;
    } finally {
        cargando.value = false;
    }
};

const cargarHabitaciones = async () => {
    try {
        const response = await axios.get(`/api/habitaciones/${route.params.id}`);
        habitaciones.value = response.data;
    } catch (err) {
        console.error('Error al cargar las habitaciones:', err);
        error.value = `Error al cargar las habitaciones: ${err.response?.data?.message || err.message}`;
    }
};

const seleccionarHabitacion = (habitacion) => {
    habitacionSeleccionada.value = habitacion.id_habitacion;
};

const realizarReserva = async () => {
    if (!puedeReservar.value) return;

    try {
        const userData = JSON.parse(localStorage.getItem('userData'));
        if (!userData || !userData.uid) {
            alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
            return;
        }

        const reservaData = {
            id_usuario: userData.uid,
            id_servicio: parseInt(route.params.id),
            estado: "apartada",
            tipo_servicio: "hotel",
            fecha_inicio: fechaInicio.value,
            fecha_fin: fechaFin.value,
            id_habitacion: habitacionSeleccionada.value
        };

        const response = await axios.post('/api/reservas/hotel', reservaData);

        if (response.data.id_reserva) {
            // Iniciamos el proceso de pago con Stripe
            try {
                const stripeResponse = await axios.post('/api/stripe/checkout', {
                    userId: userData.uid,
                    reservaId: response.data.id_reserva,
                    monto: calcularTotal.value // Usamos el total calculado que incluye el número de noches
                });

                // Cargamos Stripe y redirigimos al checkout
                const stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);
                const result = await stripe.redirectToCheckout({
                    sessionId: stripeResponse.data.id
                });

                if (result.error) {
                    throw new Error(result.error.message);
                }
            } catch (stripeError) {
                console.error('Error al procesar el pago:', stripeError);
                alert('Error al procesar el pago. Por favor, intenta nuevamente.');
            }
        }
    } catch (err) {
        console.error('Error al realizar la reserva:', err);
        alert(err.response?.data?.message || 'Error al realizar la reserva. Por favor, intenta nuevamente.');
    }
};

// Lifecycle
onMounted(() => {
    cargarServicio();
});
</script>
