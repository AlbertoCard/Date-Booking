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
                        <span class="font-medium">Reserva de Evento</span>
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
                                <span class="text-sm text-gray-500 block mb-1">Precio por lugar</span>
                                <div class="text-4xl font-bold text-gray-900">
                                    ${{ servicio?.costo || '0' }}
                                </div>
                            </div>

                            <!-- Formulario -->
                            <div class="space-y-6">
                                <!-- Fecha del evento -->
                                <div v-if="disponibilidad" class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">📅</span>
                                        <span>Fecha del evento</span>
                                    </label>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <p class="text-gray-900 font-medium">{{ formatearFecha(disponibilidad.fecha) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Hora del evento -->
                                <div v-if="disponibilidad" class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">⏰</span>
                                        <span>Hora del evento</span>
                                    </label>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <p class="text-gray-900 font-medium">{{ disponibilidad.hora_inicio }}</p>
                                    </div>
                                </div>

                                <!-- Selección de lugares -->
                                <div class="mt-8">
                                    <h3 class="text-lg font-semibold mb-4">Selecciona tus lugares</h3>

                                    <!-- Escenario -->
                                    <div
                                        class="bg-gradient-to-r from-blue-600 to-blue-800 text-white text-center py-4 rounded-t-lg shadow-lg mb-4">
                                        <h4 class="text-xl font-bold">ESCENARIO</h4>
                                    </div>

                                    <!-- Lugares disponibles -->
                                    <div class="bg-white rounded-lg shadow p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <div v-for="lugar in lugares" :key="lugar.id" class="relative">
                                                <button @click="toggleLugar(lugar)"
                                                    class="w-full p-3 rounded-lg text-center font-medium transition-all duration-200"
                                                    :class="lugaresSeleccionados.includes(`${lugar.fila}${lugar.numero}`)
                                                        ? 'bg-blue-600 text-white'
                                                        : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">
                                                    {{ lugar.fila }}{{ lugar.numero }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Resumen de selección -->
                                        <div class="mt-6 border-t pt-4">
                                            <h4 class="font-semibold mb-3">Lugares seleccionados</h4>
                                            <div v-if="lugaresSeleccionados.length > 0" class="space-y-2">
                                                <div class="flex flex-wrap gap-2">
                                                    <div v-for="lugar in lugaresSeleccionados" :key="lugar"
                                                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                                                        {{ lugar }}
                                                        <button @click="quitarLugar(lugar)"
                                                            class="text-blue-500 hover:text-blue-700">
                                                            ×
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mt-4 text-right">
                                                    <span class="font-semibold">Total seleccionados: {{
                                                        lugaresSeleccionados.length }}</span>
                                                </div>
                                            </div>
                                            <p v-else class="text-gray-500 text-sm text-center py-2">
                                                No hay lugares seleccionados
                                            </p>
                                        </div>
                                    </div>
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
const disponibilidad = ref(null);
const lugares = ref([]);
const lugaresSeleccionados = ref([]);

// Computed properties
const puedeReservar = computed(() => {
    const tieneDisponibilidad = !!disponibilidad.value;
    const tieneLugaresSeleccionados = lugaresSeleccionados.value.length > 0;
    const tieneServicio = !!servicio.value;

    console.log('Estado de validación:', {
        tieneDisponibilidad,
        tieneLugaresSeleccionados,
        tieneServicio
    });

    return tieneDisponibilidad && tieneLugaresSeleccionados && tieneServicio;
});

// Métodos
const volver = () => {
    router.back();
};

const cargarLugares = async () => {
    try {
        const response = await axios.get(`/api/lugares/${route.params.id}`);
        console.log('Lugares cargados:', response.data);

        // Verificar que los lugares tengan la estructura correcta
        if (!Array.isArray(response.data)) {
            throw new Error('Los datos de lugares no tienen el formato esperado');
        }

        // Asegurarse de que cada lugar tenga los campos necesarios
        lugares.value = response.data.filter(lugar => {
            const tieneCamposNecesarios = lugar.fila && lugar.numero;
            if (!tieneCamposNecesarios) {
                console.warn('Lugar con formato incorrecto:', lugar);
            }
            return tieneCamposNecesarios;
        });

        if (lugares.value.length === 0) {
            throw new Error('No se encontraron lugares disponibles');
        }
    } catch (err) {
        console.error('Error al cargar los lugares:', err);
        error.value = `Error al cargar los lugares: ${err.response?.data?.message || err.message}`;
    }
};

const toggleLugar = (lugar) => {
    if (!lugar || !lugar.fila || !lugar.numero) {
        console.error('Lugar inválido:', lugar);
        return;
    }

    // Asegurarse de que fila sea una letra y número sea un número
    const fila = lugar.fila.toString().toLowerCase().trim();
    const numero = lugar.numero.toString().trim();

    if (!/^[a-z]$/.test(fila)) {
        console.error('Fila inválida:', fila);
        return;
    }

    if (!/^\d+$/.test(numero)) {
        console.error('Número inválido:', numero);
        return;
    }

    const lugarId = `${fila}${numero}`;
    const index = lugaresSeleccionados.value.indexOf(lugarId);

    if (index === -1) {
        lugaresSeleccionados.value.push(lugarId);
    } else {
        lugaresSeleccionados.value.splice(index, 1);
    }

    console.log('Lugares seleccionados actualizados:', lugaresSeleccionados.value);
};

const quitarLugar = (lugarId) => {
    const index = lugaresSeleccionados.value.indexOf(lugarId);
    if (index !== -1) {
        lugaresSeleccionados.value.splice(index, 1);
    }
};

const cargarDisponibilidad = async () => {
    try {
        const response = await axios.get(`/api/disponibilidad/${route.params.id}`);
        disponibilidad.value = response.data;
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

const realizarReserva = async () => {
    if (!puedeReservar.value) {
        console.log('No se puede reservar:', {
            disponibilidad: disponibilidad.value,
            lugaresSeleccionados: lugaresSeleccionados.value
        });
        return;
    }

    try {
        const userData = JSON.parse(localStorage.getItem('userData'));
        if (!userData || !userData.uid) {
            alert('Error: No se encontró la información del usuario. Por favor, inicia sesión nuevamente.');
            return;
        }

        // Formatear la fecha correctamente
        const fecha = disponibilidad.value.fecha;
        const hora = disponibilidad.value.hora_inicio;

        // Asegurarse de que la hora tenga el formato correcto (HH:mm)
        const [horas, minutos] = hora.split(':');
        const horaFormateada = `${horas.padStart(2, '0')}:${minutos.padStart(2, '0')}`;

        // Construir la fecha y hora en el formato correcto
        const fechaHora = `${fecha} ${horaFormateada}:00`;

        console.log('Fecha formateada:', fechaHora);

        // Asegurarse de que los lugares estén en el formato correcto (array de strings)
        const lugaresFormateados = lugaresSeleccionados.value.map(lugar => {
            return lugar.toLowerCase().trim();
        });

        const reservaData = {
            id_usuario: userData.uid,
            id_servicio: parseInt(route.params.id),
            estado: 'apartada',
            fecha: fechaHora,
            tipo_servicio: 'evento',
            lugares: lugaresFormateados
        };

        // Validación adicional antes de enviar
        if (!reservaData.id_usuario || !reservaData.id_servicio || !reservaData.fecha || !reservaData.lugares.length) {
            throw new Error('Faltan datos requeridos para la reserva');
        }

        // Validar formato de fecha
        const fechaRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
        if (!fechaRegex.test(reservaData.fecha)) {
            console.error('Fecha inválida:', reservaData.fecha);
            throw new Error(`Formato de fecha inválido. Se esperaba YYYY-MM-DD HH:mm:ss, se recibió: ${reservaData.fecha}`);
        }

        // Validar formato de lugares
        const lugarRegex = /^[a-z]\d+$/;
        const lugaresInvalidos = lugaresFormateados.filter(lugar => !lugarRegex.test(lugar));
        if (lugaresInvalidos.length > 0) {
            throw new Error(`Formato inválido para los lugares: ${lugaresInvalidos.join(', ')}`);
        }

        console.log('Intentando realizar reserva con datos:', reservaData);

        const response = await axios.post('/api/reservas/evento', reservaData);
        console.log('Respuesta del servidor:', response.data);

        if (response.data.id_reserva) {
            // Iniciamos el proceso de pago con Stripe
            try {
                const stripeResponse = await axios.post('/api/stripe/checkout', {
                    userId: userData.uid,
                    reservaId: response.data.id_reserva,
                    monto: servicio.value.costo * lugaresSeleccionados.value.length // Multiplicamos por el número de lugares seleccionados
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
        } else {
            throw new Error('No se recibió ID de reserva en la respuesta');
        }
    } catch (err) {
        console.error('Error detallado:', err);
        console.error('Respuesta del servidor:', err.response?.data);

        let mensajeError = 'Error al realizar la reserva. ';
        if (err.response?.data?.message) {
            mensajeError += err.response.data.message;
        } else if (err.response?.data?.errors) {
            const errores = Object.values(err.response.data.errors).flat();
            mensajeError += errores.join(', ');
        } else if (err.message) {
            mensajeError += err.message;
        } else {
            mensajeError += 'Por favor, intenta nuevamente.';
        }

        alert(mensajeError);
    }
};

const formatearFecha = (fecha) => {
    if (!fecha) return '';

    const date = new Date(fecha);
    if (isNaN(date.getTime())) {
        console.error('Fecha inválida:', fecha);
        return '';
    }

    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const año = date.getFullYear();
    return `${dia}/${mes}/${año}`;
};

// Lifecycle
onMounted(() => {
    cargarServicio();
    cargarLugares();
    cargarDisponibilidad();
});
</script>
