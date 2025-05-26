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
                        <span class="font-medium">Reserva de Restaurante</span>
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
                                        {{ servicio?.nombre || 'Nombre del Servicio' }}
                                    </h1>
                                    <p class="text-gray-600 leading-relaxed">
                                        {{ servicio?.descripcion || 'Descripción del servicio' }}
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
                                        <span class="text-sm text-gray-500">ID Establecimiento</span>
                                        <p class="font-medium text-gray-900">#{{ servicio?.id_establecimiento || '000'
                                            }}</p>
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
                            <!-- Precio -->
                            <div class="text-center">
                                <span class="text-sm text-gray-500 block mb-1">Precio por persona</span>
                                <div class="text-4xl font-bold text-gray-900">
                                    ${{ servicio?.costo || '0' }}
                                </div>
                            </div>

                            <!-- Formulario -->
                            <div class="space-y-6">
                                <!-- Fecha -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">📅</span>
                                        <span>Fecha de la reserva</span>
                                    </label>
                                    <input type="date" v-model="fechaSeleccionada" :min="fechaMinima"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
                                        @change="cargarDisponibilidad" />
                                </div>

                                <!-- Horario -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">⏰</span>
                                        <span>Horario disponible</span>
                                    </label>
                                    <select
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors appearance-none bg-white"
                                        v-model="horaSeleccionada" :disabled="!disponibilidad">
                                        <option disabled value="">Selecciona un horario</option>
                                        <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                                            {{ hora }}
                                        </option>
                                    </select>
                                    <p v-if="disponibilidad && horasDisponibles.length === 0"
                                        class="text-sm text-red-500 mt-1">
                                        No hay horarios disponibles para esta fecha
                                    </p>
                                </div>

                                <!-- Número de Personas -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">👥</span>
                                        <span>Número de Personas</span>
                                    </label>
                                    <select
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors appearance-none bg-white"
                                        v-model="numeroPersonas">
                                        <option disabled value="">Selecciona el número de personas</option>
                                        <option v-for="n in [2, 4, 6]" :key="n" :value="n">{{ n }} personas</option>
                                    </select>
                                </div>

                                <!-- Información adicional -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-medium text-gray-900 mb-2">Información Importante</h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p>• La reserva se mantendrá por 15 minutos</p>
                                        <p>• Se requiere confirmación de pago</p>
                                        <p>• Cancelaciones con 24 horas de anticipación</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de reserva -->
                            <button @click="realizarReserva" :disabled="!puedeReservar" class="w-full bg-black text-white py-4 rounded-lg font-medium 
                                hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black 
                                disabled:bg-gray-300 disabled:cursor-not-allowed
                                transition-all duration-200 transform hover:scale-[1.02]">
                                {{ puedeReservar ? 'Realizar Reservación!' : 'Completa todos los campos' }}
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
const horaSeleccionada = ref('');
const numeroPersonas = ref('');
const horasDisponibles = ref([]);
const disponibilidad = ref(null);
const fechaSeleccionada = ref('');

// Computed properties
const fechaMinima = computed(() => {
    const hoy = new Date();
    return hoy.toISOString().split('T')[0];
});

const puedeReservar = computed(() => {
    return fechaSeleccionada.value && horaSeleccionada.value && numeroPersonas.value;
});

// Métodos
const volver = () => {
    router.back();
};

const cargarDisponibilidad = async () => {
    if (!fechaSeleccionada.value) return;

    try {
        const response = await axios.get(`/api/disponibilidad/${route.params.id}`, {
            params: {
                fecha: fechaSeleccionada.value
            }
        });

        disponibilidad.value = response.data;

        if (disponibilidad.value) {
            calcularHorasDisponibles();
        } else {
            horasDisponibles.value = [];
        }
    } catch (err) {
        console.error('Error al cargar la disponibilidad:', err);
        error.value = `Error al cargar la disponibilidad: ${err.response?.data?.message || err.message}`;
    }
};

const cargarServicio = async () => {
    try {
        const servicioResponse = await axios.get(`/api/servicios/${route.params.id}`);
        servicio.value = servicioResponse.data;
    } catch (err) {
        console.error('Error al cargar los datos:', err);
        error.value = `Error al cargar los datos: ${err.response?.data?.message || err.message}`;
    } finally {
        cargando.value = false;
    }
};

const calcularHorasDisponibles = () => {
    if (!disponibilidad.value) return;

    try {
        // Convertir las horas a objetos Date para facilitar el cálculo
        const horaInicio = new Date(`2000-01-01T${disponibilidad.value.hora_inicio}`);
        const horaFin = new Date(`2000-01-01T${disponibilidad.value.hora_fin}`);

        // Convertir el intervalo a minutos
        const [horasIntervalo, minutosIntervalo] = disponibilidad.value.intervalo.split(':');
        const intervaloMinutos = parseInt(horasIntervalo) * 60 + parseInt(minutosIntervalo);

        const horasArray = [];
        let horaActual = horaInicio;

        while (horaActual <= horaFin) {
            horasArray.push(horaActual.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }));
            horaActual = new Date(horaActual.getTime() + intervaloMinutos * 60000);
        }

        horasDisponibles.value = horasArray;
    } catch (err) {
        console.error('Error al calcular horas disponibles:', err);
        horasDisponibles.value = [];
    }
};

const realizarReserva = async () => {
    if (!puedeReservar.value) return;

    try {
        const userData = JSON.parse(localStorage.getItem('userData'));
        if (!userData || !userData.uid) {
            alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
            return;
        }

        const fechaHora = `${fechaSeleccionada.value} ${horaSeleccionada.value}:00`;

        const reservaData = {
            id_usuario: userData.uid,
            id_servicio: parseInt(route.params.id),
            estado: 'apartado',
            fecha: fechaHora,
            tipo_servicio: 'restaurante',
            personas: numeroPersonas.value
        };

        // Primero creamos la reserva
        const response = await axios.post('/api/reservas/restaurante', reservaData);

        if (response.data.id_reserva) {
            // Iniciamos el proceso de pago con Stripe
            try {
                const stripeResponse = await axios.post('/api/stripe/checkout', {
                    userId: userData.uid,
                    reservaId: response.data.id_reserva,
                    monto: servicio.value.costo
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
