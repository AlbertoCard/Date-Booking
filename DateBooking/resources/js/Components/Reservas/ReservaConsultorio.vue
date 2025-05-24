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
                        <span class="font-medium">Reserva de Consultorio</span>
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
                                            <p class="font-medium text-gray-900">Ciudad {{ servicio?.id_ciudad }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Formulario de reserva -->
                        <div class="bg-gray-50 rounded-xl p-6 space-y-8">
                            <!-- Precio -->
                            <div class="text-center">
                                <span class="text-sm text-gray-500 block mb-1">Precio de la consulta</span>
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
                                        <span>Fecha de la consulta</span>
                                    </label>
                                    <input type="date"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors"
                                        v-model="fechaSeleccionada" :min="fechaMinima" />
                                </div>

                                <!-- Horario -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">⏰</span>
                                        <span>Horario disponible</span>
                                    </label>
                                    <select
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors appearance-none bg-white"
                                        v-model="horaSeleccionada" :disabled="!fechaSeleccionada">
                                        <option disabled value="">Selecciona un horario</option>
                                        <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                                            {{ hora }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Selector de Médico -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 text-gray-700 font-medium">
                                        <span class="text-xl">👨‍⚕️</span>
                                        <span>Seleccionar Médico</span>
                                    </label>
                                    <select
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors appearance-none bg-white"
                                        v-model="medicoSeleccionado">
                                        <option disabled value="">Selecciona un médico</option>
                                        <option value="1">Dr. Juan Pérez - Medicina General</option>
                                        <option value="2">Dra. María García - Pediatría</option>
                                        <option value="3">Dr. Carlos López - Cardiología</option>
                                    </select>
                                </div>

                                <!-- Información del médico seleccionado -->
                                <div v-if="medicoSeleccionado" class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-medium text-gray-900 mb-2">Información del Médico</h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p>Especialidad: {{ obtenerEspecialidadMedico(medicoSeleccionado) }}</p>
                                        <p>Experiencia: 10+ años</p>
                                        <p>Horario de atención: Lunes a Viernes</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de reserva -->
                            <button @click="realizarReserva" :disabled="!puedeReservar" class="w-full bg-black text-white py-4 rounded-lg font-medium 
                       hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black 
                       disabled:bg-gray-300 disabled:cursor-not-allowed
                       transition-all duration-200 transform hover:scale-[1.02]">
                                {{ puedeReservar ? 'Realizar Reservación!' : 'Selecciona fecha, hora y médico' }}
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

const router = useRouter();
const route = useRoute();
const servicio = ref(null);
const cargando = ref(true);
const error = ref(null);
const fechaSeleccionada = ref('');
const horaSeleccionada = ref('');
const medicoSeleccionado = ref('');

// Horarios disponibles
const horasDisponibles = [
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00'
];

// Computed properties
const fechaMinima = computed(() => {
    const hoy = new Date();
    return hoy.toISOString().split('T')[0];
});

const puedeReservar = computed(() => {
    return fechaSeleccionada.value && horaSeleccionada.value && medicoSeleccionado.value;
});

// Métodos
const volver = () => {
    router.back();
};

const obtenerEspecialidadMedico = (idMedico) => {
    const especialidades = {
        '1': 'Medicina General',
        '2': 'Pediatría',
        '3': 'Cardiología'
    };
    return especialidades[idMedico] || 'No especificada';
};

const cargarServicio = async () => {
    try {
        const response = await axios.get(`/api/servicios/${route.params.id}`);
        servicio.value = response.data;
    } catch (err) {
        error.value = `Error al cargar el servicio: ${err.response?.data?.message || err.message}`;
    } finally {
        cargando.value = false;
    }
};

const realizarReserva = () => {
    // Aquí irá la lógica de reserva cuando se implemente
    console.log('Reserva realizada:', {
        fecha: fechaSeleccionada.value,
        hora: horaSeleccionada.value,
        medico: medicoSeleccionado.value
    });
};

// Lifecycle
onMounted(() => {
    cargarServicio();
});
</script>