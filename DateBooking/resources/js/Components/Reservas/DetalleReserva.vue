<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-6xl mx-auto p-6">
            <!-- Estado de carga -->
            <div v-if="cargando" class="flex justify-center items-center min-h-[200px]">
                <Loader :visible="true" />
            </div>

            <!-- Error -->
            <div v-else-if="error" class="error-container">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ error }}</span>
            </div>

            <!-- Contenido principal -->
            <div v-else-if="reserva" class="space-y-8">
                <!-- Encabezado con navegación -->
                <nav class="flex items-center space-x-4 py-4 border-b">
                    <button @click="$router.back()" class="text-gray-600 hover:text-black transition-colors">
                        <span class="text-2xl">←</span>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-600">Reservas</span>
                        <span class="text-gray-400">/</span>
                        <span class="font-medium">Detalles de la reserva</span>
                    </div>
                </nav>

                <!-- Contenedor del contenido principal -->
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Columna izquierda: Imagen e información -->
                        <div class="space-y-8">
                            <!-- Imagen del servicio -->
                            <div
                                class="w-48 h-48 mx-auto bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                <img :src="reserva.servicio.imagen || '/images/default-service.jpg'"
                                    :alt="reserva.servicio.nombre" class="w-full h-full object-cover" />
                            </div>

                            <!-- Información del servicio -->
                            <div class="space-y-6">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                        {{ reserva.servicio.nombre }}
                                    </h1>
                                    <p class="text-gray-600 leading-relaxed">
                                        {{ reserva.servicio.descripcion }}
                                    </p>
                                </div>

                                <!-- Estado de la reserva -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <span class="text-sm text-gray-500 block mb-1">Estado de la reserva</span>
                                    <div class="estado" :class="`estado-${reserva.estado.toLowerCase()}`">
                                        {{ reserva.estado }}
                                    </div>
                                </div>

                                <!-- Tipo de Servicio -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <span class="text-sm text-gray-500 block mb-1">Tipo de Servicio</span>
                                    <p class="font-medium text-gray-900">{{ reserva.tipo_servicio }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Detalles y QR -->
                        <div class="space-y-8">
                            <!-- Información del Establecimiento y QR -->
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Información del Establecimiento -->
                                    <div>
                                        <h3 class="text-xl font-medium mb-4 text-gray-800">Establecimiento</h3>
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-store text-gray-500 text-lg"></i>
                                                <span class="text-gray-700">{{ reserva.servicio.establecimiento.nombre
                                                    }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-map-marker-alt text-gray-500 text-lg"></i>
                                                <span class="text-gray-700">{{
                                                    reserva.servicio.establecimiento.direccion }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-phone text-gray-500 text-lg"></i>
                                                <span class="text-gray-700">{{ reserva.servicio.establecimiento.telefono
                                                    }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Código QR o Botón de Reseña -->
                                    <div class="flex flex-col items-center">
                                        <template v-if="reserva.estado.toUpperCase() === 'PENDIENTE'">
                                            <div class="flex flex-col items-center">
                                                <QrPersonalizado ref="qrPersonalizado" :value="qrValue" />
                                                <div class="mt-4 text-center">
                                                    <p class="text-sm text-gray-600 mb-3">Muestra este código al
                                                        establecimiento</p>
                                                    <button @click="descargarQR"
                                                        class="px-5 py-2 bg-white text-gray-800 rounded-full hover:bg-gray-50 transition-all duration-300 text-sm font-medium flex items-center justify-center gap-2 shadow-sm hover:shadow-md border border-gray-200">
                                                        <i class="fas fa-download"></i>
                                                        Descargar QR
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Plantilla oculta para descarga personalizada -->
                                            <div ref="plantillaDescarga" style="display:none;">
                                                <div
                                                    style="width:350px; background:#fff; border-radius:18px; box-shadow:0 2px 10px #0001; padding:24px; text-align:center;">
                                                    <h2 style="color:#4f46e5; margin-bottom:8px;">Reserva #{{
                                                        reserva.id_reserva }}</h2>
                                                    <div style="font-size:16px; color:#222; margin-bottom:8px;">{{
                                                        reserva.servicio.nombre }}</div>
                                                    <div style="font-size:14px; color:#555; margin-bottom:8px;">{{
                                                        reserva.fecha }}</div>
                                                    <div style="font-size:14px; color:#555; margin-bottom:8px;">{{
                                                        reserva.servicio.establecimiento.nombre }}</div>
                                                    <div style="font-size:14px; color:#555; margin-bottom:16px;">Estado:
                                                        {{ reserva.estado }}</div>
                                                    <div style="display:flex; justify-content:center; margin-top:16px;">
                                                        <QrPersonalizado :value="qrValue" :size="200" />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else-if="reserva.estado.toUpperCase() === 'COMPLETADA'">
                                            <div class="text-center">
                                                <i class="fas fa-star text-4xl text-yellow-400 mb-4"></i>
                                                <button @click="agregarResena"
                                                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 text-lg font-medium flex items-center gap-2">
                                                    <i class="fas fa-pen"></i>
                                                    Añadir Reseña
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha y Precio -->
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="mb-6">
                                    <span class="text-sm text-gray-500 block mb-1">Fecha y Hora</span>
                                    <p class="text-xl font-semibold text-gray-800">{{ reserva.fecha.split(' ')[0] }}</p>
                                    <p class="text-lg text-gray-600">{{ reserva.fecha.split(' ')[1] }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 block mb-1">Precio Total</span>
                                    <p class="text-3xl font-bold text-purple-600">${{ reserva.servicio.costo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loader from '../Loader.vue';
import { getAuth } from 'firebase/auth';
import QrcodeVue from 'qrcode.vue';
import QrPersonalizado from './QrPersonalizado.vue';
import html2canvas from 'html2canvas';

export default {
    components: {
        Loader,
        QrcodeVue,
        QrPersonalizado
    },
    data() {
        return {
            reserva: null,
            cargando: true,
            error: null,
            qrValue: ''
        };
    },
    methods: {
        async obtenerDetalleReserva() {
            try {
                this.cargando = true;
                this.error = null;
                const reservaId = this.$route.params.id;
                const auth = getAuth();
                const user = auth.currentUser;

                if (!user) {
                    this.error = 'Debes iniciar sesión para ver los detalles de la reserva';
                    return;
                }

                const response = await axios.get(`/api/reservas/detalle/${reservaId}`, {
                    headers: {
                        'Authorization': `Bearer ${await user.getIdToken()}`
                    }
                });

                this.reserva = response.data.reserva;
                this.qrValue = JSON.stringify({
                    id: this.reserva.id_reserva,
                    servicio: this.reserva.servicio.nombre,
                    fecha: this.reserva.fecha,
                    estado: this.reserva.estado
                });
            } catch (error) {
                console.error('Error al obtener el detalle de la reserva:', error);
                if (error.response?.status === 404) {
                    this.error = 'La reserva no existe o no está disponible';
                } else {
                    this.error = 'Error al cargar los detalles de la reserva';
                }
            } finally {
                this.cargando = false;
            }
        },
        async descargarQR() {
            try {
                const plantilla = this.$refs.plantillaDescarga;
                plantilla.style.display = 'block'; // Mostrar temporalmente
                await this.$nextTick();

                const canvas = await html2canvas(plantilla, { backgroundColor: null });
                const link = document.createElement('a');
                link.download = `reserva-${this.reserva.id_reserva}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

                plantilla.style.display = 'none'; // Ocultar de nuevo
            } catch (error) {
                console.error('Error al descargar la plantilla:', error);
            }
        },
        agregarResena() {
            // Aquí puedes implementar la lógica para redirigir a la página de reseñas
            // o abrir un modal para añadir la reseña
            this.$router.push(`/agregar-resena/${this.reserva.id_reserva}`);
        }
    },
    mounted() {
        this.obtenerDetalleReserva();
    }
};
</script>

<style scoped>
.min-h-screen {
    min-height: 100vh;
}

.bg-gray-50 {
    background-color: #f9fafb;
}

.max-w-6xl {
    max-width: 72rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.bg-white {
    background-color: white;
}

.rounded-2xl {
    border-radius: 1.5rem;
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.p-8 {
    padding: 2rem;
}

.flex {
    display: flex;
}

.flex-col {
    flex-direction: column;
}

.gap-8 {
    gap: 2rem;
}

.flex-1 {
    flex: 1 1 0%;
}

.items-center {
    align-items: center;
}

.mb-6 {
    margin-bottom: 1.5rem;
}

.mr-3 {
    margin-right: 0.75rem;
}

.text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
}

.text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

.font-bold {
    font-weight: 700;
}

.font-semibold {
    font-weight: 600;
}

.text-gray-800 {
    color: #1f2937;
}

.text-gray-700 {
    color: #374151;
}

.text-gray-600 {
    color: #4b5563;
}

.text-gray-500 {
    color: #6b7280;
}

.w-full {
    width: 100%;
}

.md\:w-96 {
    width: 24rem;
}

.p-6 {
    padding: 1.5rem;
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.border {
    border-width: 1px;
}

.border-2 {
    border-width: 2px;
}

.border-gray-100 {
    border-color: #f3f4f6;
}

.border-gray-300 {
    border-color: #d1d5db;
}

.rounded-full {
    border-radius: 9999px;
}

.text-base {
    font-size: 1rem;
    line-height: 1.5rem;
}

.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.mt-4 {
    margin-top: 1rem;
}

.mt-6 {
    margin-top: 1.5rem;
}

.px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.py-3 {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

.bg-red-500 {
    background-color: #ef4444;
}

.text-white {
    color: white;
}

.hover\:bg-red-600:hover {
    background-color: #dc2626;
}

.text-purple-600 {
    color: #9333ea;
}

.leading-relaxed {
    line-height: 1.625;
}

.estado {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.estado-pendiente {
    background-color: #fff3cd;
    color: #856404;
}

.estado-confirmada {
    background-color: #d4edda;
    color: #155724;
}

.estado-cancelada {
    background-color: #f8d7da;
    color: #721c24;
}

.error-container {
    text-align: center;
    padding: 1.5rem;
    background-color: #fff5f5;
    border-radius: 1rem;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: #dc2626;
}

@media (min-width: 768px) {
    .md\:flex-row {
        flex-direction: row;
    }
}

@media (max-width: 768px) {
    .p-6 {
        padding: 1rem;
    }

    .text-3xl {
        font-size: 1.5rem;
    }

    .text-2xl {
        font-size: 1.25rem;
    }
}

/* Nuevos estilos */
.overflow-hidden {
    overflow: hidden;
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.object-cover {
    object-fit: cover;
}

.aspect-square {
    aspect-ratio: 1 / 1;
}

.w-48 {
    width: 12rem;
}

.h-48 {
    height: 12rem;
}

/* Estilos para el QR */
.qr-container {
    position: relative;
    padding: 15px;
    background: white;
    border-radius: 30px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.qr-wrapper {
    position: relative;
    width: 140px;
    height: 140px;
    border-radius: 25px;
    overflow: hidden;
    background: white;
    transition: all 0.3s ease;
}

.qr-container:hover .qr-wrapper {
    transform: scale(1.05);
}

.qr-code {
    border-radius: 20px;
    padding: 8px;
    background: white;
}

.qr-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    padding: 8px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transition: all 0.3s ease;
}

.qr-container:hover .qr-overlay {
    opacity: 1;
}

/* Ajustes responsivos para el QR */
@media (max-width: 768px) {
    .qr-container {
        padding: 12px;
    }

    .qr-wrapper {
        width: 120px;
        height: 120px;
    }
}
</style>