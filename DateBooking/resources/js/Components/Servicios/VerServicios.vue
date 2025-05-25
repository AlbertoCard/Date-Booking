<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <Loader :visible="loading" />
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
                <div class="relative">
                    <button @click="toggleDropdown"
                        class="nuevo-servicio bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-300 flex items-center gap-4">
                        <span>+ Nuevo Servicio</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            :class="{ 'transform rotate-180': showDropdown }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <div v-if="showDropdown"
                            class="absolute right-0 mt-2 w-52 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                            <div class="py-2" role="menu" aria-orientation="vertical">
                                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Selecciona una categoría
                                </div>
                                <div class="border-t border-gray-100"></div>
                                <button v-for="categoria in categorias" :key="categoria.id"
                                    @click="seleccionarCategoria(categoria)"
                                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center gap-3 transition-all duration-200 group"
                                    role="menuitem">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ categoria.nombre }}</span>
                                        <span class="text-xs text-gray-500 group-hover:text-blue-500">
                                            {{ getCategoriaDescripcion(categoria.id) }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>

            <!-- Tabs de navegación -->
            <div class="tab-container">
                <button class="tab" :class="{ active: activeTab === 'activos' }" @click="activeTab = 'activos'">
                    Activos
                </button>
                <button class="tab" :class="{ active: activeTab === 'inactivos' }" @click="activeTab = 'inactivos'">
                    Inactivos
                </button>
            </div>

            <!-- Contenido de los tabs -->
            <div class="mt-6">
                <Transition name="slide-fade" mode="out-in">
                    <div :key="activeTab">
                        <!-- Lista de servicios -->
                        <div v-for="servicio in serviciosFiltrados" :key="servicio.id_servicio"
                            class="tarjeta-servicio group">
                            <!-- Contenido clickeable -->
                            <div class="contenido-clickeable" @click="verDetalleServicio(servicio.id_servicio)">
                                <!-- Imagen o ícono -->
                                <div class="imagen transform group-hover:scale-105 transition-all duration-300">
                                    <template v-if="servicio.imagen && servicio.imagen.url">
                                        <img :src="servicio.imagen.url" :alt="servicio.nombre"
                                            class="w-full h-full object-cover rounded-lg" @error="handleImageError"
                                            @load="handleImageLoad">
                                    </template>
                                    <div v-else
                                        class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <div class="info-servicio">
                                    <h2 class="text-xl font-bold text-gray-800">{{ servicio.nombre }}</h2>
                                    <p class="descripcion text-gray-600">{{ servicio.descripcion }}</p>

                                    <!-- Estado de disponibilidad -->
                                    <div class="mt-3">
                                        <div v-if="servicio.disponibilidad && servicio.disponibilidad.length > 0"
                                            :class="[
                                                'estado-disponibilidad',
                                                servicio.disponibilidad[0].activo === 1 ? 'activo' : 'inactivo'
                                            ]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path v-if="servicio.disponibilidad[0].activo === 1"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <path v-else stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ servicio.disponibilidad[0].activo === 1
                                                ? 'Servicio con disponibilidad'
                                                : 'Servicio temporalmente no disponible' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio y estrellas -->
                                <div class="precio-estrellas">
                                    <p class="precio text-blue-600">${{ servicio.costo }}</p>
                                    <p class="estrellas">
                                        <span v-for="i in 5" :key="i" class="transition-colors duration-300">
                                            {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Botones y controles (no clickeables) -->
                            <div class="controles">
                                <!-- Botones -->
                                <div class="acciones">
                                    <button @click.stop="editarDisponibilidad(servicio.id_servicio)"
                                        class="editar transform hover:scale-105 transition-all duration-300">
                                        Editar
                                    </button>
                                    <button @click.stop="cancelarServicio(servicio.id_servicio)"
                                        class="cancelar transform hover:scale-105 transition-all duration-300">
                                        Cancelar
                                    </button>
                                </div>

                                <!-- Switch ON/OFF -->
                                <div class="estado">
                                    <button @click.stop="toggleDisponibilidad(servicio.id_servicio)" :class="[
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
import Loader from '../Loader.vue';

export default {
    components: {
        Loader,
    },
    name: 'VerServicios',
    data() {
        return {
            servicios: [],
            activeTab: 'activos',
            showModal: false,
            showDropdown: false,
            servicioAToggle: null,
            loading: false,
            // Constantes para estados
            ESTADOS: {
                ACTIVO: 1,
                INACTIVO: 0
            },
            TABS: {
                ACTIVOS: 'activos',
                INACTIVOS: 'inactivos'
            },
            categorias: [
                { id: 'hotel', nombre: 'Hotel', icono: 'fas fa-hotel' },
                { id: 'restaurante', nombre: 'Restaurante', icono: 'fas fa-utensils' },
                { id: 'consultorio', nombre: 'Consultorio', icono: 'fas fa-stethoscope' },
                { id: 'evento', nombre: 'Evento', icono: 'fas fa-calendar-alt' },
            ]
        };
    },
    computed: {
        serviciosFiltrados() {
            return this.servicios.filter(servicio => {
                const tieneDisponibilidad = servicio.disponibilidad && servicio.disponibilidad.length > 0;

                if (!tieneDisponibilidad) {
                    return false;
                }

                switch (this.activeTab) {
                    case this.TABS.ACTIVOS:
                        return servicio.disponibilidad[0].activo === this.ESTADOS.ACTIVO;
                    case this.TABS.INACTIVOS:
                        return servicio.disponibilidad[0].activo === this.ESTADOS.INACTIVO;
                    default:
                        return false;
                }
            });
        },
        mensajeNoServicios() {
            const mensajes = {
                [this.TABS.ACTIVOS]: 'No hay servicios activos',
                [this.TABS.INACTIVOS]: 'No hay servicios inactivos'
            };
            return mensajes[this.activeTab] || 'No hay servicios disponibles';
        },
        estadoServicio() {
            if (!this.servicioAToggle?.disponibilidad?.length) return null;
            return this.servicioAToggle.disponibilidad[0].activo;
        },
        mensajeModal() {
            if (!this.servicioAToggle) return '';
            return this.estadoServicio === this.ESTADOS.INACTIVO
                ? `¿Estás seguro de que deseas activar el servicio "${this.servicioAToggle.nombre}"?`
                : `¿Estás seguro de que deseas desactivar el servicio "${this.servicioAToggle.nombre}"?`;
        }
    },
    mounted() {
        this.loading = true;
        this.obtenerServicios();
        // Agregar event listener para cerrar el dropdown al hacer clic fuera
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        // Remover event listener al desmontar el componente
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        async obtenerServicios() {
            try {
                console.log('Obteniendo servicios...');
                // Obtener datos del usuario del localStorage
                const userData = JSON.parse(localStorage.getItem('userData'));

                if (!userData || userData.rol !== 'establecimiento') {
                    console.error('Usuario no es un establecimiento');
                    return;
                }

                // Obtener el establecimiento del usuario
                const estabResponse = await axios.get(`/api/establecimientos/usuario/${userData.uid}`);
                console.log('Respuesta del establecimiento:', estabResponse.data);

                if (!estabResponse.data.establecimientos || estabResponse.data.establecimientos.length === 0) {
                    console.error('No se encontró el establecimiento');
                    return;
                }

                const idEstablecimiento = estabResponse.data.establecimientos[0].id_establecimiento;
                console.log('ID del establecimiento:', idEstablecimiento);

                // Obtener los servicios del establecimiento
                const response = await axios.get(`/api/servicios?id_establecimiento=${idEstablecimiento}`);
                console.log('Respuesta del servidor:', response.data);

                this.servicios = response.data.map(servicio => {
                    console.log('Procesando servicio:', {
                        id: servicio.id_servicio,
                        nombre: servicio.nombre,
                        tieneImagen: !!servicio.imagen,
                        urlImagen: servicio.imagen?.url
                    });

                    return {
                        ...servicio,
                        estrellas: Math.floor(Math.random() * 5) + 1
                    };
                });
            } catch (error) {
                console.error('Error al obtener servicios:', error);
                this.error = 'Error al cargar los servicios';
            }
            finally {
                this.loading = false;
            }
        },
        handleImageError(e) {
            console.error('Error al cargar la imagen:', {
                src: e.target.src,
                servicio: this.servicios.find(s => s.imagen?.url === e.target.src)
            });
            e.target.style.display = 'none';
            e.target.parentElement.classList.add('bg-gradient-to-br', 'from-blue-500', 'to-blue-600');
        },
        handleImageLoad(e) {
            console.log('Imagen cargada exitosamente:', {
                src: e.target.src,
                servicio: this.servicios.find(s => s.imagen?.url === e.target.src)
            });
        },
        agregarDisponibilidad(idServicio) {
            this.$router.push(`/editar-servicio/${idServicio}`);
        },
        editarDisponibilidad(idServicio) {
            this.$router.push(`/editar-servicio/${idServicio}`);
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
                const response = await axios.put(`/api/disponibilidad/${this.servicioAToggle.id_servicio}/toggle`);

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
        },
        verDetalleServicio(idServicio) {
            this.$router.push(`/nodo-servicio/${idServicio}`);
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },
        handleClickOutside(event) {
            const dropdown = this.$el.querySelector('.relative');
            if (dropdown && !dropdown.contains(event.target)) {
                this.showDropdown = false;
            }
        },
        seleccionarCategoria(categoria) {
            this.showDropdown = false;
            switch (categoria.id) {
                case 'hotel':
                    this.$router.push('/nuevo-servicio/hotel');
                    break;
                case 'evento':
                    this.$router.push('/nuevo-servicio/evento');
                    break;
                case 'restaurante':
                    this.$router.push('/nuevo-servicio/restaurante');
                    break;
                case 'consultorio':
                    this.$router.push('/nuevo-servicio/consultorio');
                    break;
            }
        },
        getCategoriaDescripcion(id) {
            const descripciones = {
                hotel: 'Servicios de alojamiento',
                restaurante: 'Servicios gastronómicos',
                consultorio: 'Servicios de salud',
                evento: 'Servicios de eventos',
                otro: 'Otros servicios'
            };
            return descripciones[id] || '';
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
    padding: 10px 50px;
    border: none;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1.1rem;
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

.contenido-clickeable {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    flex: 1;
}

.controles {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-left: 1rem;
    border-left: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .tarjeta-servicio {
        flex-direction: column;
    }

    .contenido-clickeable {
        flex-direction: column;
        width: 100%;
    }

    .controles {
        width: 100%;
        padding-left: 0;
        border-left: none;
        border-top: 1px solid #e5e7eb;
        padding-top: 1rem;
    }
}

.categorias-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.categoria-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.categoria-button:hover {
    transform: translateY(-2px);
    border-color: #3b82f6;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
}

.categoria-icon {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 0.5rem;
}

.categoria-button span {
    font-weight: 600;
    color: #1f2937;
}

/* Ajustes para el modal de categorías */
.modal-content {
    max-width: 600px;
    width: 90%;
}

@media (max-width: 640px) {
    .categorias-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Estilos mejorados para el dropdown */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

/* Animación para los botones del menú */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.dropdown-menu button {
    animation: slideIn 0.2s ease-out forwards;
    animation-delay: calc(var(--index) * 0.05s);
}

@media (max-width: 768px) {
    .nuevo-servicio {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>