<<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-6xl mx-auto p-8">
            <!-- Encabezado -->
            <div class="encabezado">
                <h1 class="text-3xl font-bold text-gray-900">Mis Servicios</h1>
                <button @click="$router.push('/nuevo-servicio')"
                    class="nuevo-servicio bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-300">
                    + Nuevo Servicio
                </button>
            </div>

            <!-- Lista de servicios -->
            <div v-for="(servicio, index) in servicios" :key="servicio.id_servicio" class="tarjeta-servicio group">
                <!-- Imagen o ícono -->
                <div class="imagen transform group-hover:scale-105 transition-all duration-300"></div>

                <!-- Contenido -->
                <div class="info-servicio">
                    <h2 class="text-xl font-bold text-gray-800">{{ servicio.nombre }}</h2>
                    <p class="descripcion text-gray-600">{{ servicio.descripcion }}</p>
                </div>

                <!-- Botones -->
                <div class="acciones">
                    <button class="editar transform hover:scale-105 transition-all duration-300">
                        Editar
                    </button>
                    <button class="cancelar transform hover:scale-105 transition-all duration-300">
                        Cancelar
                    </button>
                </div>

                <!-- Precio y estrellas -->
                <div class="precio-estrellas">
                    <p class="precio text-blue-600">${{ servicio.costo }}</p>
                    <p class="estrellas">
                        <span v-for="i in 5" :key="i" class="transition-colors duration-300">
                            {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                    </p>
                </div>

                <!-- Switch ON/OFF -->
                <div class="estado">
                    <BotonOffOn></BotonOffOn>
                </div>
            </div>
        </div>
    </div>
</template>

    <script>
    import axios from 'axios';
    import BotonOffOn from './Componente_reutilizable/boton_off_on.vue';

    export default {
        name: 'VerServicios',
        components: {
            BotonOffOn,
        },
        data() {
            return {
                servicios: [],
            };
        },
        mounted() {
            this.obtenerServicios();
        },
        methods: {
            async obtenerServicios() {
                try {
                    const response = await axios.get('http://localhost:8000/api/servicios');
                    // Mapeamos la respuesta para agregar campos visuales simulados
                    this.servicios = response.data.map(servicio => ({
                        ...servicio,
                        estrellas: Math.floor(Math.random() * 5) + 1, // ⭐ simulado
                        activo: true, // ON/OFF simulado
                    }));
                } catch (error) {
                    console.error('Error al obtener los servicios:', error);
                }
            },
            toggleServicio(index) {
                this.servicios[index].activo = !this.servicios[index].activo;
            },
        },
    };
</script>

    <style scoped>
    .container {
        backdrop-filter: blur(10px);
        transform-style: preserve-3d;
        perspective: 1000px;
        background: rgba(255, 255, 255, 0.9);
    }

    .encabezado {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1rem 0;
    }

    .nuevo-servicio {
        padding: 0.75rem 1.5rem;
        color: white;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .tarjeta-servicio {
        display: flex;
        align-items: center;
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 1rem;
        gap: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        transform-style: preserve-3d;
        border: 1px solid rgba(37, 99, 235, 0.1);
    }

    .tarjeta-servicio:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .imagen {
        width: 100px;
        height: 100px;
        background: linear-gradient(to br, #2563eb, #3b82f6);
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .info-servicio {
        flex: 1;
        transform: translateZ(0);
        transition: transform 0.3s ease;
    }

    .info-servicio:hover {
        transform: translateZ(10px);
    }

    .acciones button {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .editar {
        background: linear-gradient(to right, #e5e7eb, #d1d5db);
        color: #374151;
    }

    .cancelar {
        background: linear-gradient(to right, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .precio {
        font-size: 1.5rem;
        font-weight: bold;
        background: linear-gradient(to right, #2563eb, #3b82f6);
        -webkit-background-clip: text;
        color: transparent;
    }

    .estrellas {
        color: #f59e0b;
        font-size: 1.25rem;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .container {
            margin: 1rem;
            padding: 1rem;
        }

        .tarjeta-servicio {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .acciones {
            flex-direction: row;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .precio-estrellas,
        .estado {
            align-self: center;
            margin-top: 1rem;
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .imagen {
        animation: float 6s ease-in-out infinite;
    }
</style>
