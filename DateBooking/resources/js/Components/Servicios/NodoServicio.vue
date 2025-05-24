<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div v-if="loading" class="flex justify-center items-center min-h-screen">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <div v-else-if="servicio"
            class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-6xl mx-auto p-8">
            <!-- Botón de regreso -->
            <button @click="$router.back()"
                class="mb-4 flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </button>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Imagen del servicio -->
                <div class="w-full md:w-1/3">
                    <div class="relative aspect-square rounded-xl overflow-hidden shadow-lg">
                        <template v-if="servicio.imagen && servicio.imagen.url">
                            <img :src="servicio.imagen.url" :alt="servicio.nombre" class="w-full h-full object-cover"
                                @error="handleImageError" @load="handleImageLoad">
                        </template>
                        <div v-else
                            class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Información del servicio -->
                <div class="w-full md:w-2/3">
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Detalles principales -->
                        <div class="space-y-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ servicio.nombre }}</h1>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Descripción</h3>
                                    <p class="text-gray-600 whitespace-pre-line">{{ servicio.descripcion }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Precio</h3>
                                    <div class="text-2xl font-bold text-blue-600">${{ servicio.costo }}</div>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Ubicación</h3>
                                    <div class="flex items-center text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="font-medium">{{ servicio.ciudad?.nombre || 'Ciudad no especificada' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Calificación</h3>
                                <div class="flex items-center">
                                    <span v-for="i in 5" :key="i" class="text-yellow-400 text-xl">
                                        {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                                </div>
                            </div>
                        </div>

                        <!-- Disponibilidad -->
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Estado del Servicio</h3>
                                <div :class="[
                                    'estado-disponibilidad',
                                    servicio.disponibilidad?.[0]?.activo === 1 ? 'activo' : 'inactivo'
                                ]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path v-if="servicio.disponibilidad?.[0]?.activo === 1" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ servicio.disponibilidad?.[0]?.activo === 1 ? 'Servicio Disponible' :
                                        'Servicio No Disponible' }}
                                </div>
                            </div>

                            <div v-if="servicio.disponibilidad && servicio.disponibilidad.length > 0"
                                class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Detalles de Disponibilidad</h3>
                                <div class="space-y-3">
                                    <div v-for="(disp, index) in servicio.disponibilidad" :key="index"
                                        class="border-b border-gray-200 pb-3 last:border-0">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Fecha</p>
                                                <p class="text-gray-900">{{ new Date(disp.fecha).toLocaleDateString() }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Tipo</p>
                                                <p class="text-gray-900">{{ disp.tipo }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Hora Inicio</p>
                                                <p class="text-gray-900">{{ disp.hora_inicio }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Hora Fin</p>
                                                <p class="text-gray-900">{{ disp.hora_fin }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Categoría</h3>
                                <p class="text-gray-600">{{ servicio.categoria }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Reseñas -->
            <div class="mt-8 border-t pt-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Reseñas de Clientes</h2>

                <!-- Estadísticas de reseñas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-3xl font-bold text-blue-600">{{ promedioCalificacion }}</div>
                        <div class="flex items-center mt-1">
                            <div class="flex">
                                <span v-for="i in 5" :key="i" class="text-yellow-400 text-lg">
                                    {{ i <= Math.round(promedioCalificacion) ? '★' : '☆' }} </span>
                            </div>
                            <span class="ml-2 text-gray-600">({{ reseñas.length }} reseñas)</span>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-lg font-semibold text-gray-700 mb-2">Distribución de calificaciones</div>
                        <div class="space-y-2">
                            <div v-for="i in 5" :key="i" class="flex items-center">
                                <span class="text-sm text-gray-600 w-8">{{ i }}★</span>
                                <div class="flex-1 h-2 bg-gray-200 rounded-full mx-2">
                                    <div class="h-2 bg-yellow-400 rounded-full transition-all duration-300"
                                        :style="{ width: `${calcularPorcentajeCalificacion(i)}%` }">
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 w-12">{{ calcularPorcentajeCalificacion(i) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de reseñas -->
                <div class="space-y-6">
                    <div v-if="reseñas.length === 0" class="text-center py-8 bg-gray-50 rounded-xl">
                        <p class="text-gray-500">No hay reseñas disponibles para este servicio</p>
                    </div>

                    <div v-for="reseña in reseñas" :key="reseña.id_reseña"
                        class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">
                                        {{ reseña.usuario?.nombre?.charAt(0) || 'U' }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">{{ reseña.usuario?.nombre || 'Usuario' }}</p>
                                    <p class="text-sm text-gray-500">{{ new Date(reseña.fecha).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span v-for="i in 5" :key="i" class="text-yellow-400">
                                    {{ i <= Math.min(Math.max(reseña.calificacion, 1), 5) ? '★' : '☆' }} </span>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-600">{{ reseña.descripcion }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12">
            <p class="text-gray-500">No se encontró la información del servicio</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'NodoServicio',
    data() {
        return {
            servicio: null,
            loading: true,
            reseñas: []
        };
    },
    computed: {
        promedioCalificacion() {
            if (this.reseñas.length === 0) return 0;
            const suma = this.reseñas.reduce((acc, reseña) => acc + Math.min(reseña.calificacion, 5), 0);
            return Math.min((suma / this.reseñas.length).toFixed(1), 5);
        }
    },
    mounted() {
        this.obtenerServicio();
        this.obtenerReseñas();
    },
    methods: {
        async obtenerServicio() {
            try {
                const idServicio = this.$route.params.id;
                const response = await axios.get(`/api/servicios/${idServicio}`);
                console.log('Respuesta completa del servidor:', response.data);
                console.log('Datos de la imagen:', response.data.imagen);
                console.log('URL de la imagen:', response.data.imagen?.url);

                this.servicio = {
                    ...response.data,
                    estrellas: Math.floor(Math.random() * 5) + 1
                };
            } catch (error) {
                console.error('Error al obtener el servicio:', error);
            } finally {
                this.loading = false;
            }
        },
        async obtenerReseñas() {
            try {
                const idServicio = this.$route.params.id;
                const response = await axios.get(`/api/servicios/${idServicio}/reseñas`);
                this.reseñas = response.data;
            } catch (error) {
                console.error('Error al obtener las reseñas:', error);
            }
        },
        handleImageError(e) {
            console.error('Error al cargar la imagen:', {
                src: e.target.src,
                servicio: this.servicio,
                imagen: this.servicio.imagen
            });
            e.target.style.display = 'none';
            e.target.parentElement.classList.add('bg-gradient-to-br', 'from-blue-500', 'to-blue-600');
        },
        handleImageLoad(e) {
            console.log('Imagen cargada exitosamente:', {
                src: e.target.src,
                servicio: this.servicio,
                imagen: this.servicio.imagen
            });
        },
        calcularPorcentajeCalificacion(calificacion) {
            if (this.reseñas.length === 0) return 0;
            // Aseguramos que la calificación esté entre 1 y 5
            const calificacionNormalizada = Math.min(Math.max(calificacion, 1), 5);
            const cantidad = this.reseñas.filter(r => Math.min(Math.max(r.calificacion, 1), 5) === calificacionNormalizada).length;
            return Math.round((cantidad / this.reseñas.length) * 100);
        }
    }
};
</script>

<style scoped>
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

.container {
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    perspective: 1000px;
    background: rgba(255, 255, 255, 0.9);
}

/* Estilos para las reseñas */
.reseña-card {
    transition: transform 0.2s ease-in-out;
}

.reseña-card:hover {
    transform: translateY(-2px);
}

.rating-bar {
    transition: width 0.3s ease-in-out;
}
</style>