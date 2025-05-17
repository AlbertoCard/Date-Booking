<template>
  <div class="contenedor-detalle">
    <!-- Loading state -->
    <div v-if="cargando" class="loading">
      <Loader :visible="true" />
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="error-container">
      <p>{{ error }}</p>
    </div>

    <!-- Content -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Información del servicio -->
      <div class="info-servicio">
        <h1 class="text-3xl font-bold mb-4">{{ servicio.nombre }}</h1>
        <p class="text-gray-600 mb-4">{{ servicio.descripcion }}</p>
        <div class="precio text-2xl font-bold text-primary mb-4">
          ${{ servicio.costo }}
        </div>
        <div class="categoria mb-4">
          <span class="bg-gray-100 px-3 py-1 rounded-full text-sm">
            {{ servicio.categoria }}
          </span>
        </div>
      </div>

      <!-- Formulario de reserva -->
      <div class="formulario-reserva bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Realizar Reserva</h2>
        
        <!-- Selector de fecha -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Fecha</label>
          <input 
            type="date" 
            v-model="fechaSeleccionada"
            class="w-full p-2 border rounded"
            :min="fechaMinima"
          >
        </div>

        <!-- Selector de hora -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Hora</label>
          <select 
            v-model="horaSeleccionada"
            class="w-full p-2 border rounded"
            :disabled="!fechaSeleccionada"
          >
            <option value="">Selecciona una hora</option>
            <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
              {{ hora }}
            </option>
          </select>
        </div>

        <!-- Campos adicionales según el tipo de servicio -->
        <div v-if="camposAdicionales.length > 0" class="mb-4">
          <div v-for="campo in camposAdicionales" :key="campo.nombre" class="mb-3">
            <label class="block text-sm font-medium mb-2">{{ campo.label }}</label>
            <input 
              :type="campo.tipo"
              v-model="campo.valor"
              class="w-full p-2 border rounded"
              :placeholder="campo.placeholder"
            >
          </div>
        </div>

        <!-- Resumen de la reserva -->
        <div v-if="mostrarResumen" class="resumen-reserva bg-gray-50 p-4 rounded mb-4">
          <h3 class="font-medium mb-2">Resumen de la reserva</h3>
          <p>Fecha: {{ fechaSeleccionada }}</p>
          <p>Hora: {{ horaSeleccionada }}</p>
          <p class="font-bold mt-2">Total a pagar: ${{ servicio.costo }}</p>
        </div>

        <!-- Botón de reserva -->
        <button 
          @click="realizarReserva"
          :disabled="!puedeReservar"
          class="w-full bg-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-dark disabled:bg-gray-300 disabled:cursor-not-allowed"
        >
          Reservar ahora
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Loader from './Loader.vue';

// Estado
const route = useRoute();
const servicio = ref(null);
const cargando = ref(true);
const error = ref(null);
const fechaSeleccionada = ref('');
const horaSeleccionada = ref('');
const horasDisponibles = ref([]);
const camposAdicionales = ref([]);

// Computed properties
const fechaMinima = computed(() => {
  const hoy = new Date();
  return hoy.toISOString().split('T')[0];
});

const mostrarResumen = computed(() => {
  return fechaSeleccionada.value && horaSeleccionada.value;
});

const puedeReservar = computed(() => {
  return fechaSeleccionada.value && 
         horaSeleccionada.value && 
         !camposAdicionales.value.some(campo => !campo.valor);
});

// Methods
const cargarServicio = async () => {
  try {
    const response = await axios.get(`/api/servicios/${route.params.id}`);
    servicio.value = response.data;
    configurarCamposAdicionales();
  } catch (err) {
    error.value = 'Error al cargar el servicio';
    console.error(err);
  } finally {
    cargando.value = false;
  }
};

const cargarDisponibilidad = async () => {
  if (!fechaSeleccionada.value) return;
  
  try {
    const response = await axios.get(`/api/disponibilidad/${servicio.value.id_servicio}`, {
      params: {
        fecha: fechaSeleccionada.value
      }
    });
    horasDisponibles.value = response.data;
  } catch (err) {
    console.error('Error al cargar disponibilidad:', err);
  }
};

const configurarCamposAdicionales = () => {
  if (!servicio.value) return;
  
  // Configurar campos según la categoría del servicio
  switch (servicio.value.categoria) {
    case 'restaurante':
      camposAdicionales.value = [
        {
          nombre: 'personas',
          label: 'Número de personas',
          tipo: 'number',
          valor: '',
          placeholder: 'Ingrese el número de personas'
        }
      ];
      break;
    case 'hotel':
      camposAdicionales.value = [
        {
          nombre: 'huespedes',
          label: 'Número de huéspedes',
          tipo: 'number',
          valor: '',
          placeholder: 'Ingrese el número de huéspedes'
        }
      ];
      break;
    // Agregar más casos según las categorías
  }
};

const realizarReserva = async () => {
  try {
    const datosReserva = {
      id_servicio: servicio.value.id_servicio,
      fecha: fechaSeleccionada.value,
      hora: horaSeleccionada.value,
      detalles: Object.fromEntries(
        camposAdicionales.value.map(campo => [campo.nombre, campo.valor])
      )
    };

    const response = await axios.post('/api/reservas', datosReserva);
    
    // Redirigir a la página de pago
    if (response.data.id_reserva) {
      window.location.href = `/pago/${response.data.id_reserva}`;
    }
  } catch (err) {
    error.value = 'Error al realizar la reserva';
    console.error(err);
  }
};

// Watchers
watch(fechaSeleccionada, () => {
  horaSeleccionada.value = '';
  cargarDisponibilidad();
});

// Lifecycle
onMounted(() => {
  cargarServicio();
});
</script>

<style scoped>
.contenedor-detalle {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.error-container {
  text-align: center;
  padding: 2rem;
  color: #ef4444;
}

.bg-primary {
  background-color: #3b82f6;
}

.hover\:bg-primary-dark:hover {
  background-color: #2563eb;
}

.text-primary {
  color: #3b82f6;
}
</style>
