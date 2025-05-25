<template>
  <div class="div_contenedor">
    <div v-if="cargando" class="flex justify-center items-center min-h-[400px]">
      <Loader :visible="true" />
    </div>

    <div v-else-if="error" class="text-center py-12 text-red-500 text-xl">
      {{ error }}
    </div>

    <div v-else>
      <div class="div_encabezado">
        <h1>{{ servicio.nombre }}</h1>
      </div>

      <p class="descripcion mb-6">{{ servicio.descripcion }}</p>

      <div class="card_servicio mb-8">
        <div class="div_infoServicio">
          <h2>Reservaciones</h2>
            <ul class="mt-4 pl-6 space-y-4 text-gray-700">
              <li
                v-for="reserva in reservas"
                :key="reserva.id_reserva"
                class="tarjeta-reserva bg-gray-100 rounded-xl p-4 shadow-sm"
              >
                <p class="reserva bold">Reserva #{{ reserva.id_reserva }}</p>
                <p><strong>Usuario:</strong> {{ reserva.id_usuario }}</p>
                <p><strong>Servicio:</strong> {{ reserva.id_servicio }}</p>
                <p><strong>Estado:</strong> 
                  <span
                    :class="{
                      'text-green-600 font-semibold': reserva.estado === 'confirmada',
                      'text-yellow-600 font-semibold': reserva.estado === 'apartada' || 'apartado',
                      'text-red-600 font-semibold': reserva.estado === 'cancelada'
                    }"
                  >
                    {{ reserva.estado }}
                  </span>
                </p>
                <p><strong>Fecha:</strong> {{ reserva.fecha }}</p>
                <p><strong>Tipo:</strong> {{ reserva.tipo_servicio }}</p>

                <div class="div_btnValidar">
                  <button
                    class="btn_validar mt-3"
                    @click="validarQR(reserva.id_reserva)"
                  >
                  Validar QR
                  </button>
                </div>
                
              </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import Loader from '../Loader.vue';

  export default {
    name: 'ServicioDetalle',
    components: {
      Loader
    },
    data() {
      return {
        reservas: [],
        servicio:{
          nombre: "",
          descripcion: ""
        },
        cargando: true,
        error: null
      };
    },
    mounted() {
      const id_servicio = this.$route.params.id;
      console.log("ID del servicio desde la ruta:", id_servicio);
      this.obtenerServicio(id_servicio);
      this.obtenerReservas(id_servicio);
    },
    methods: {
      obtenerReservas(id_servicio) {
        axios.get('http://127.0.0.1:8000/api/reservas/servicios/' + id_servicio)
          .then(response => {
            this.cargando = true;
            this.reservas = response.data.reservas;
            console.log("Mostrando las reservas del servicio: " + this.reservas.length);
          })
          .catch(error => {
            console.error('Error al obtener reservas:', error);
          })
          .finally(() => {
            this.cargando = false; // Ocultar el loader al finalizar la carga
          });
      },
      obtenerServicio(id_servicio) {
        axios.get('http://127.0.0.1:8000/api/servicios/' + id_servicio)
          .then(response => {
            this.cargando = true;
            this.servicio = {
              nombre: response.data.nombre,
              descripcion: response.data.descripcion
            };            
          })
          .catch(error => {
            console.error('Error al obtener datos del servicio:', error);
          })
          .finally(() => {
            this.cargando = false; // Ocultar el loader al finalizar la carga
          });
      },
      validarQR() {
        alert('Funcionalidad de validación por QR en desarrollo.');
      }
    }
  };
</script>

<style scoped>
  .div_contenedor {
    max-width: 1200px;
    margin: 10px auto;
    padding: 40px;
    font-family: Arial, sans-serif;
    background: #fdfdfd;
    border-radius: 10px;
  }

  .div_encabezado h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: .50rem;
  }

  .descripcion {
    font-size: 1.125rem;
    color: #4b5563;
    background-color: #ffffff;
    padding: .50rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    margin-bottom: 1.5rem;
  }

  .card_servicio {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
  }

  .div_infoServicio h2 {
    font-size: 30px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
  }

  ul {
    padding-left: 1.5rem;
    color: #374151;
  }

  ul li {
    margin-bottom: 0.75rem;
  }

  .tarjeta-reserva {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }

  .tarjeta-reserva:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  .reserva{
    font-size: 20px;
  }

  .div_btnValidar{
    display: flex;
    justify-content: flex-end;
  }

  .btn_validar {
    margin-top: 2rem;
    display: inline-block;
    background: linear-gradient(to right, #3b82f6, #6366f1);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
    cursor: pointer;
  }

  .btn_validar:hover {
    background: linear-gradient(to right, #2563eb, #4f46e5);
    transform: scale(1.03);
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
  }

  .bold{
    font-weight: bold;
  }

  /* Loader y error */
  .min-h-\[400px\] {
    min-height: 400px;
  }

  .text-red-500 {
    color: #ef4444;
  }

  .text-xl {
    font-size: 1.25rem;
  }
</style>