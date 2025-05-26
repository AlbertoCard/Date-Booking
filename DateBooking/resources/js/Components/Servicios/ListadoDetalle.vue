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
        
        <div class="div_btnValidar">
          <button class="btn_validar mt-3" @click="abrirLectorQR()">
            Validar QR
          </button>
        </div>
      </div>

      <p class="descripcion mb-6">{{ servicio.descripcion }}</p>

      <div class="card_servicio mb-8">
        <div class="div_infoServicio">
          <h2>Reservaciones</h2>
          <ul class="mt-4 pl-6 space-y-4 text-gray-700">
            <li v-for="reserva in reservas" :key="reserva.id_reserva"
              class="tarjeta-reserva bg-gray-100 rounded-xl p-4 shadow-sm">
              <p class="reserva bold">Reserva #{{ reserva.id_reserva }}</p>
              <p><strong>Usuario:</strong> {{ reserva.id_usuario }}</p>
              <p><strong>Servicio:</strong> {{ reserva.id_servicio }}</p>
              <p><strong>Estado:</strong>
                <span :class="{
                  'text-green-600 font-semibold': reserva.estado === 'confirmada',
                  'text-yellow-600 font-semibold': ['apartada', 'apartado'].includes(reserva.estado),
                  'text-red-600 font-semibold': reserva.estado === 'cancelada'
                }">
                  {{ reserva.estado }}
                </span>
              </p>
              <p><strong>Fecha:</strong> {{ reserva.fecha }}</p>
              <p><strong>Tipo:</strong> {{ reserva.tipo_servicio }}</p>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div v-if="mostrarLectorQR" class="modal_qr">
      <div class="modal_qr_contenido">
        <h3>Escanea el código QR</h3>
        <qrcode-stream @detect="onDetect" class="qr-cam"></qrcode-stream>
        <button class="btn_cerrar" @click="mostrarLectorQR = false">Cancelar</button>
      </div>
    </div>

    <transition name="fade-smooth">
      <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-40 backdrop-blur-sm z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full text-center border border-gray-200">
          <h2 class="text-lg font-medium mb-2 text-gray-700">Reserva confirmada</h2>
          <p class="text-gray-600"><strong>Reserva:</strong> {{ reservaValidada }}</p>
        </div>
      </div>
    </transition>

    <transition name="fade-smooth">
      <div v-if="mostrarModalError" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-40 backdrop-blur-sm z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full text-center border border-gray-200">
          <h2 class="text-lg font-medium mb-2 text-gray-700">Error al validar la reserva</h2>
          <p class="text-gray-600"><strong>Mensaje:</strong> {{ mensajeError }}</p>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import Loader from '../Loader.vue';
import { QrcodeStream } from 'vue-qrcode-reader';

export default {
  name: 'ServicioDetalle',
  components: {
    Loader,
    QrcodeStream
  },
  data() {
    return {
      reservas: [],
      servicio: {
        nombre: "",
        descripcion: ""
      },
      cargando: true,
      error: null,
      mostrarLectorQR: false,
      camaraDroidCamId: null,
      reservaValidada: null,
      mostrarModal: false,
      mostrarModalError: false,
      mensajeError: '',
    };
  },
  mounted() {
    const id_servicio = this.$route.params.id;
    console.log("ID del servicio desde la ruta:", id_servicio);
    this.obtenerServicio(id_servicio);
    this.obtenerReservas(id_servicio);
    this.getCamarasDisponibles();
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
    abrirLectorQR() {
      this.mostrarLectorQR = true;
    },
    onDetect(result) {
      console.log("QR detectado:", result, typeof result);
      this.mostrarLectorQR = false;

      try {
        let id_reserva_qr;

        if (Array.isArray(result) && result.length > 0 && result[0].rawValue) {
          const qrData = JSON.parse(result[0].rawValue);
          id_reserva_qr = qrData.id;
        } else if (typeof result === 'object' && result !== null && 'id' in result) {
          id_reserva_qr = result.id;
        } else if (typeof result === 'string' && result.trim().startsWith('{')) {
          const qrData = JSON.parse(result);
          id_reserva_qr = qrData.id;
        } else {
          id_reserva_qr = parseInt(result);
        }

        if (!id_reserva_qr || isNaN(id_reserva_qr)) {
          alert("El código QR escaneado no es válido.");
          return;
        }

        const estadoNuevo = 'confirmada';

        axios.post('http://127.0.0.1:8000/api/reservas/validar-reserva', {
          id_reserva: id_reserva_qr,
          estado: estadoNuevo
        })
        .then(response => {
          this.cargando = true;
          this.reservaValidada = id_reserva_qr;
          this.mostrarModal = true;

          setTimeout(() => {
            this.mostrarModal = false;
            this.reservaValidada = null;
          }, 4000);

          if (response.data.success) {
            alert("Reserva validada correctamente.");
            this.obtenerReservas(this.$route.params.id);
          } else if (response.data.message) {
            alert(`${response.data.message}`);
          } else if (response.data.error) {
            alert(`${response.data.error}`);
          } else {
            alert("No se pudo validar la reserva.");
          }
        })
        .catch(error => {
          console.error('Error al validar la reserva:', error);

          if (error.response && error.response.data) {
            this.mensajeError = error.response?.data?.message || 'Ocurrió un error inesperado.';
            this.mostrarModalError = true;
            const data = error.response.data;
            setTimeout(() => {
              this.mostrarModalError = false;
              this.mensajeError = '';
            }, 4000);
            alert(data.message || data.error || "Hubo un error al validar la reserva.");
          } else {
            alert("Hubo un error al validar la reserva.");
          }
        });

      } catch (error) {
        console.error('Error al procesar el código QR:', error);
        alert("El código QR escaneado no es válido. Asegúrese de escanear un QR correcto.");
      }
    },
    async getCamarasDisponibles() {
      const dispositivos = await navigator.mediaDevices.enumerateDevices();
      const camaras = dispositivos.filter(device => device.kind === 'videoinput');
      console.log("Cámaras disponibles:", camaras);
      this.camaraDroidCamId = camaras.find(c => c.label.toLowerCase().includes('droidcam'))?.deviceId;
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

.tarjeta-reserva p {
  margin-left: 20px;
}

.reserva {
  font-size: 20px;
  margin-bottom: 20px;
}

.div_btnValidar {
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

.bold {
  font-weight: bold;
}

.modal_qr {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(17, 24, 39, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal_qr_contenido {
  background-color: white;
  padding: 2rem;
  border-radius: 1rem;
  text-align: center;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal_qr_contenido h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #1f2937;
}

.qr-cam {
  width: 100%;
  max-height: 300px;
  margin-bottom: 1.5rem;
  border-radius: 0.5rem;
  overflow: hidden;
}

.btn_cerrar {
  background: linear-gradient(to right, #f87171, #ef4444);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.btn_cerrar:hover {
  background: linear-gradient(to right, #dc2626, #b91c1c);
  transform: scale(1.05);
}

.fade-smooth-enter-active, .fade-smooth-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}
.fade-smooth-enter-from, .fade-smooth-leave-to {
  opacity: 0;
  transform: scale(0.98);
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