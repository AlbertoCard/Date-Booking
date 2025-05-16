<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <!-- Modal de confirmación -->
        <Transition name="fade">
            <div v-if="showModal" class="modal-backdrop" @click="showModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-icon" :class="{ 'active': servicioAToggle?.disponibilidad?.[0]?.activo === 0 }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="servicioAToggle?.disponibilidad?.[0]?.activo === 0
                                ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                                : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'" />
                        </svg>
                    </div>
                    <h3 class="modal-title">
                        {{ servicioAToggle?.disponibilidad?.[0]?.activo === 0 ? '¿Activar servicio?' :
                            '¿Desactivar servicio ? ' }}
                    </h3>
                    <p class="modal-message">
                        {{ mensajeModal }}
                    </p>
                    <div class="modal-actions">
                        <button class="modal-button cancel" @click="showModal = false">
                            Cancelar
                        </button>
                        <button class="modal-button confirm" @click="confirmarToggle">
                            {{ servicioAToggle?.disponibilidad?.[0]?.activo === 0 ? 'Activar' : 'Desactivar' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-6xl mx-auto p-8">
            <!-- Encabezado con título y botón nuevo servicio -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Mis Servicios</h1>
                <button @click="$router.push('/nuevo-servicio')"
                    class="nuevo-servicio bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-300">
                    + Nuevo Servicio
                </button>
            </div>

            <!-- Tabs de navegación -->
            <div class="tab-container">
                <button class="tab" :class="{ active: activeTab === 'activos' }" @click="activeTab = 'activos'">
                    Activos
                </button>
                <button class="tab" :class="{ active: activeTab === 'inactivos' }" @click="activeTab = 'inactivos'">
                    Inactivos
                </button>
                <button class="tab" :class="{ active: activeTab === 'sin-disponibilidad' }"
                    @click="activeTab = 'sin-disponibilidad'">
                    Sin Disponibilidad
                </button>
            </div>

            <!-- Contenido de los tabs -->
            <div class="mt-6">
                <Transition name="slide-fade" mode="out-in">
                    <div :key="activeTab">
                        <!-- Lista de servicios -->
                        <div v-for="(servicio, index) in serviciosFiltrados" :key="servicio.id_servicio"
                            class="tarjeta-servicio group">
                            <!-- Imagen o ícono -->
                            <div class="imagen transform group-hover:scale-105 transition-all duration-300"></div>

                            <!-- Contenido -->
                            <div class="info-servicio">
                                <h2 class="text-xl font-bold text-gray-800">{{ servicio.nombre }}</h2>
                                <p class="descripcion text-gray-600">{{ servicio.descripcion }}</p>

                                <!-- Estado de disponibilidad -->
                                <div class="mt-3">
                                    <div v-if="servicio.disponibilidad && servicio.disponibilidad.length > 0" :class="[
                                        'estado-disponibilidad',
                                        servicio.disponibilidad[0].activo === 1 ? 'activo' : 'inactivo'
                                    ]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path v-if="servicio.disponibilidad[0].activo === 1" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ servicio.disponibilidad[0].activo === 1
                                            ? 'Servicio con disponibilidad'
                                            : 'Servicio temporalmente no disponible' }}
                                    </div>
                                    <div v-else class="estado-disponibilidad sin-disponibilidad">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <p class="mb-2">Servicio no disponible</p>
                                        <button @click="agregarDisponibilidad(servicio.id_servicio)"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300 text-sm">
                                            + Añadir disponibilidad
                                        </button>
                                    </div>
                                </div>
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
                                <button @click="toggleDisponibilidad(servicio.id_servicio)" :class="[
                                    'toggle-button',
                                    {
                                        'active': servicio.disponibilidad &&
                                            servicio.disponibilidad.length > 0 &&
                                            servicio.disponibilidad[0].activo === 1
                                    }
                                ]" :disabled="!servicio.disponibilidad || servicio.disponibilidad.length === 0">
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-text">
                                        {{ servicio.disponibilidad && servicio.disponibilidad.length > 0 &&
                                            servicio.disponibilidad[0].activo === 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Mensaje cuando no hay servicios -->
                        <div v-if="serviciosFiltrados.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                            <p class="text-gray-500">
                                {{ mensajeNoServicios }}
                            </p>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import API_ROUTES from '../utils/index.js';

export default {
    name: 'VerServicios',
    data() {
        return {
            servicios: [],
            activeTab: 'activos',
            showModal: false,
            servicioAToggle: null,
            loading: false,
            // Constantes para estados
            ESTADOS: {
                ACTIVO: 1,
                INACTIVO: 0
            },
            TABS: {
                ACTIVOS: 'activos',
                INACTIVOS: 'inactivos',
                SIN_DISPONIBILIDAD: 'sin-disponibilidad'
            }
        };
    },
    computed: {
        serviciosFiltrados() {
            return this.servicios.filter(servicio => {
                const tieneDisponibilidad = servicio.disponibilidad && servicio.disponibilidad.length > 0;

                switch (this.activeTab) {
                    case this.TABS.ACTIVOS:
                        return tieneDisponibilidad && servicio.disponibilidad[0].activo === this.ESTADOS.ACTIVO;
                    case this.TABS.INACTIVOS:
                        return tieneDisponibilidad && servicio.disponibilidad[0].activo === this.ESTADOS.INACTIVO;
                    case this.TABS.SIN_DISPONIBILIDAD:
                        return !tieneDisponibilidad;
                    default:
                        return false;
                }
            });
        },
        mensajeNoServicios() {
            const mensajes = {
                [this.TABS.ACTIVOS]: 'No hay servicios activos',
                [this.TABS.INACTIVOS]: 'No hay servicios inactivos',
                [this.TABS.SIN_DISPONIBILIDAD]: 'No hay servicios sin disponibilidad'
            };
            return mensajes[this.activeTab] || 'No hay servicios disponibles';
        },
        // Nuevo computed property para el estado del servicio
        estadoServicio() {
            if (!this.servicioAToggle?.disponibilidad?.length) return null;
            return this.servicioAToggle.disponibilidad[0].activo;
        },
        // Nuevo computed property para el mensaje del modal
        mensajeModal() {
            if (!this.servicioAToggle) return '';
            return this.estadoServicio === this.ESTADOS.INACTIVO
                ? `¿Estás seguro de que deseas activar el servicio "${this.servicioAToggle.nombre}"?`
                : `¿Estás seguro de que deseas desactivar el servicio "${this.servicioAToggle.nombre}"?`;
        }
    },
    mounted() {
        this.obtenerServicios();
    },
    methods: {
        async obtenerServicios() {
            try {
                const response = await axios.get(API_ROUTES.servicios);
                this.servicios = response.data.map(servicio => ({
                    ...servicio,
                    estrellas: Math.floor(Math.random() * 5) + 1
                }));
            } catch (error) {
                console.error('Error al obtener los servicios:', error);
            }
        },
        agregarDisponibilidad(idServicio) {
            this.$router.push(`/nueva-disponibilidad/${idServicio}`);
        },
        async toggleDisponibilidad(idServicio) {
            // Encontrar el servicio que se va a toggle
            const servicio = this.servicios.find(s => s.id_servicio === idServicio);
            if (!servicio) return;

            this.servicioAToggle = servicio;
            this.showModal = true;
        },

        async confirmarToggle() {
            if (this.loading) return;
            this.loading = true;

            try {
                const response = await axios.put(API_ROUTES.disponibilidad.toggle(this.servicioAToggle.id_servicio));

                // Actualizar el estado localmente sin recargar todos los servicios
                const index = this.servicios.findIndex(s => s.id_servicio === this.servicioAToggle.id_servicio);
                if (index !== -1 && this.servicios[index].disponibilidad?.length > 0) {
                    this.servicios[index].disponibilidad[0].activo = response.data.activo;
                }

                this.showModal = false;
                this.servicioAToggle = null;
            } catch (error) {
                console.error('Error al cambiar el estado:', error);
            } finally {
                this.loading = false;
            }
        }
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

.tab-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 20px;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-radius: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.tab {
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    color: #64748b;
    background-color: transparent;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.tab.active {
    background-color: #2563eb;
    color: white;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.tab:hover:not(.active) {
    background-color: rgba(37, 99, 235, 0.1);
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(20px);
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

.acciones button {
    padding: 0.60rem 1.5rem;
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
    margin-right: 1rem;
}

.cancelar {
    background: linear-gradient(to right, #fee2e2, #fecaca);
    color: #dc2626;
    margin-right: 1rem;
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

.toggle-button {
    position: relative;
    width: 110px;
    height: 36px;
    border-radius: 18px;
    background-color: #e5e7eb;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    padding: 4px;
}

.toggle-button.active {
    background-color: #2563eb;
}

.toggle-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.toggle-slider {
    position: absolute;
    left: 4px;
    width: 28px;
    height: 28px;
    background-color: white;
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-button.active .toggle-slider {
    left: calc(100% - 32px);
}

.toggle-text {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 32px;
    transition: all 0.3s ease;
}

.toggle-button.active .toggle-text {
    color: white;
    margin-left: 8px;
}

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

    .tab-container {
        padding: 10px;
        gap: 8px;
    }

    .tab {
        padding: 10px 16px;
        font-size: 0.75rem;
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

/* Estilos del Modal */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background-color: white;
    border-radius: 1rem;
    padding: 2rem;
    max-width: 90%;
    width: 400px;
    text-align: center;
    position: relative;
    transform: translateY(0);
    transition: transform 0.3s ease;
    z-index: 10000;
}

.modal-icon {
    margin: 0 auto 1.5rem;
    width: 3.5rem;
    height: 3.5rem;
    background-color: #fee2e2;
    color: #dc2626;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.modal-icon.active {
    background-color: #dcfce7;
    color: #16a34a;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
}

.modal-message {
    color: #64748b;
    margin-bottom: 2rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.modal-button {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.modal-button.cancel {
    background-color: #e5e7eb;
    color: #374151;
}

.modal-button.cancel:hover {
    background-color: #d1d5db;
}

.modal-button.confirm {
    background-color: #2563eb;
    color: white;
}

.modal-button.confirm:hover {
    background-color: #1d4ed8;
}

/* Animaciones del modal */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-active .modal-content {
    animation: modal-in 0.3s ease-out;
}

.fade-leave-active .modal-content {
    animation: modal-out 0.3s ease-in;
}

@keyframes modal-in {
    0% {
        transform: translateY(20px);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes modal-out {
    0% {
        transform: translateY(0);
        opacity: 1;
    }

    100% {
        transform: translateY(20px);
        opacity: 0;
    }
}

/* Ajustes responsive para el modal */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
        padding: 1.5rem;
    }

    .modal-title {
        font-size: 1.25rem;
    }

    .modal-message {
        font-size: 0.875rem;
    }

    .modal-button {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}

.estado-disponibilidad {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.estado-disponibilidad.activo {
    background-color: #dcfce7;
    color: #16a34a;
}

.estado-disponibilidad.inactivo {
    background-color: #fef9c3;
    color: #854d0e;
    border: 1px dashed #ca8a04;
}

.estado-disponibilidad.sin-disponibilidad {
    background-color: #fee2e2;
    color: #dc2626;
    flex-direction: column;
    align-items: flex-start;
}

.estado-disponibilidad svg {
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .estado-disponibilidad {
        justify-content: center;
        text-align: center;
    }

    .estado-disponibilidad.sin-disponibilidad {
        align-items: center;
    }
}
</style>
