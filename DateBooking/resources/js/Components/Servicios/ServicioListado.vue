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
        <h1>Servicios disponibles</h1>
      </div>

      <div v-if="servicios.length > 0">
        <div v-for="servicio in servicios" :key="servicio.id_servicio" class="card_servicio"
             @click="verDetalle(servicio.id_servicio)">
          <div class="div_imagen">
            <template v-if="servicio.imagen?.url">
              <img :src="servicio.imagen.url" :alt="servicio.nombre" class="w-full h-full object-cover rounded-lg">
            </template>
            <div v-else class="w-full h-full bg-blue-500 rounded-lg flex items-center justify-center">
              <svg class="h-12 w-12 text-white" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor">
                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <div class="div_infoServicio">
            <h2>{{ servicio.nombre }}</h2>
            <p class="descripcion">{{ servicio.descripcion }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12 text-gray-500 text-xl">
        No hay servicios disponibles
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import Loader from '../Loader.vue';

  export default {
    components: {
        Loader,
    },
    data() {
        return {
            mostrarFormulario: false,
            servicios: [],
            cargando: true,
        };
    },
    mounted() {
        this.cargando = true;
        this.obtenerServicios();
    },
    methods: {
      async obtenerServicios() {
        try {
            console.log('Obteniendo servicios...');
            const userData = JSON.parse(localStorage.getItem('userData'));

            if (!userData || userData.rol == 'cliente') {
                console.error('Usuario no es un establecimiento/afiliado');
                this.error = 'No tienes permisos para ver los servicios.';
                this.cargando = false;
                return;
            }

            const estabResponse = await axios.get(`/api/establecimientos/usuario/${userData.uid}`);
            const establecimientos = estabResponse.data.establecimientos;

            if (!establecimientos || establecimientos.length === 0) {
                console.error('No se encontró el establecimiento');
                this.error = 'No se encontró el establecimiento.';
                this.cargando = false;
                return;
            }

            const idEstablecimiento = establecimientos[0].id_establecimiento;

            const response = await axios.get(
                `/api/servicios?id_establecimiento=${idEstablecimiento}`
            );

            this.servicios = response.data;

            console.log("Servicios obtenidos:", this.servicios.length);
        } catch (error) {
            console.error('Error al obtener servicios:', error);
            this.error = 'Error al cargar los servicios';
        } finally {
            this.cargando = false;
        }
      },
      verDetalle(id_servicio) {
        this.$router.push({ path: `/detalle-listado/${id_servicio}` });
      },
    }
  };
</script>

<style scope>
  .div_contenedor {
    max-width: 1200px;
    margin: 10px auto;
    padding: 40px;
    font-family: Arial, sans-serif;
    background: #fdfdfd;
    border-radius: 10px;
  }

  .div_encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
  }

  .div_encabezado h1 {
    font-size: 32px;
    font-weight: 800;
    color: #111827;
  }

  .card_servicio {
    display: flex;
    align-items: center;
    gap: 20px;
    background: #ffffff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
  }

  .card_servicio:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
  }

  .div_imagen {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e0e7ff;
  }

  .div_imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
  }

  .div_infoServicio {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .div_infoServicio h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1f2937;
  }

  .div_infoServicio .descripcion {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .text-center {
    text-align: center;
  }

  .text-red-500 {
    color: #ef4444;
  }

  .text-gray-500 {
    color: #6b7280;
  }

  .text-xl {
    font-size: 20px;
  }

  .py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
  }

  .min-h-\[400px\] {
    min-height: 400px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .tarjeta-servicio {
      flex-direction: column;
      text-align: center;
    }

    .div_imagen {
      width: 80px;
      height: 80px;
    }

    .div_infoServicio h2 {
      font-size: 18px;
    }

    .div_infoServicio .descripcion {
      font-size: 13px;
    }
  }
</style>