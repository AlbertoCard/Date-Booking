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
            <span class="font-medium">Detalles del servicio</span>
          </div>
        </nav>

        <!-- Contenedor del contenido principal -->
        <div class="bg-white rounded-xl shadow-sm p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Columna izquierda: Galería e información -->
            <div class="space-y-8">
              <!-- Galería de imágenes -->
              <div class="space-y-4">
                <div class="grid grid-cols-3 gap-3">
                  <div v-for="i in 3" :key="i" 
                       class="aspect-square bg-gray-100 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-200 cursor-pointer overflow-hidden group">
                    <div class="w-full h-full flex items-center justify-center relative">
                      <i class="material-icons text-gray-400 text-3xl group-hover:scale-110 transition-transform duration-200">image</i>
                      <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                    </div>
                  </div>
                </div>
                <div class="flex justify-center space-x-2">
                  <span v-for="(_, index) in 3" :key="index"
                        :class="[
                          'w-2 h-2 rounded-full transition-all duration-300 cursor-pointer',
                          currentSlide === index ? 'w-6 bg-black' : 'bg-gray-300 hover:bg-gray-400'
                        ]"
                        @click="currentSlide = index">
                  </span>
                </div>
              </div>

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
                    <p class="font-medium text-gray-900">{{ servicio?.categoria || 'No especificada' }}</p>
                  </div>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-sm text-gray-500">ID Establecimiento</span>
                    <p class="font-medium text-gray-900">#{{ servicio?.id_establecimiento || '000' }}</p>
                  </div>
                </div>

                <!-- Ubicación -->
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
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
                <span class="text-sm text-gray-500 block mb-1">Precio del servicio</span>
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
                    <span>Fecha de reserva</span>
                  </label>
                  <input 
                    type="date" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors" 
                    v-model="fechaSeleccionada"
                    :min="fechaMinima" 
                  />
                </div>

                <!-- Horario -->
                <div class="space-y-2">
                  <label class="flex items-center space-x-2 text-gray-700 font-medium">
                    <span class="text-xl">⏰</span>
                    <span>Horario disponible</span>
                  </label>
                  <select 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition-colors appearance-none bg-white"
                    v-model="horaSeleccionada"
                    :disabled="!fechaSeleccionada"
                  >
                    <option disabled value="">Selecciona un horario</option>
                    <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                      {{ hora }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Botón de reserva -->
              <button
                @click="realizarReserva"
                :disabled="!puedeReservar"
                class="w-full bg-black text-white py-4 rounded-lg font-medium 
                       hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black 
                       disabled:bg-gray-300 disabled:cursor-not-allowed
                       transition-all duration-200 transform hover:scale-[1.02]"
              >
                {{ puedeReservar ? 'Realizar Reservación!' : 'Selecciona fecha y hora' }}
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
import Loader from './Loader.vue';

const router = useRouter();
const route = useRoute();
const servicio = ref(null);
const cargando = ref(true);
const error = ref(null);
const fechaSeleccionada = ref('');
const horaSeleccionada = ref('');
const horasDisponibles = ref([]);
const currentSlide = ref(0);

// Computed properties
const fechaMinima = computed(() => {
  const hoy = new Date();
  return hoy.toISOString().split('T')[0];
});

const puedeReservar = computed(() => {
  return fechaSeleccionada.value && horaSeleccionada.value;
});

// Methods
const volver = () => {
  router.back();
};

const cargarServicio = async () => {
  try {
    console.log('ID del servicio:', route.params.id);
    const url = `/api/servicios/${route.params.id}`;
    console.log('URL de la petición:', url);
    
    const response = await axios.get(url);
    console.log('Respuesta del servidor:', response.data);
    
    servicio.value = response.data;
  } catch (err) {
    console.error('Error completo:', err);
    error.value = `Error al cargar el servicio: ${err.response?.data?.message || err.message}`;
  } finally {
    cargando.value = false;
  }
};

const cargarDisponibilidad = () => {
  horasDisponibles.value = [
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00'
  ];
};

const realizarReserva = async () => {
  if (!puedeReservar.value) {
    alert('Por favor, selecciona una fecha y horario para continuar.');
    return;
  }

  try {
    const datosReserva = {
      id_servicio: servicio.value.id_servicio,
      fecha: fechaSeleccionada.value,
      hora: horaSeleccionada.value
    };

    const response = await axios.post('/api/reservas', datosReserva);
    
    if (response.data.id_reserva) {
      alert(`¡Reservación exitosa! Te esperamos el ${fechaSeleccionada.value} a las ${horaSeleccionada.value}`);
      router.push(`/pago/${response.data.id_reserva}`);
    }
  } catch (err) {
    alert('Lo sentimos, hubo un error al procesar tu reserva. Por favor, intenta nuevamente.');
    console.error(err);
  }
};

// Lifecycle
onMounted(() => {
  cargarServicio();
  cargarDisponibilidad();
});
</script>
