<template>
    <div class="contenedor">
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
        <div v-else-if="reserva" class="detalle-reserva">
            <!-- Encabezado -->
            <div class="header">
                <h1>{{ reserva.servicio.nombre }}</h1>
                <div class="estado" :class="`estado-${reserva.estado.toLowerCase()}`">
                    {{ reserva.estado }}
                </div>
            </div>

            <!-- Información principal -->
            <div class="info-principal">
                <!-- Detalles de la Reserva -->
                <div class="seccion">
                    <h2><i class="fas fa-calendar-alt"></i> Detalles de la Reserva</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <span class="etiqueta">Fecha</span>
                                <span class="valor">{{ reserva.fecha.split(' ')[0] }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <span class="etiqueta">Hora</span>
                                <span class="valor">{{ reserva.fecha.split(' ')[1] }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <div>
                                <span class="etiqueta">Tipo de Servicio</span>
                                <span class="valor">{{ reserva.tipo_servicio }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Establecimiento -->
                <div class="seccion">
                    <h2><i class="fas fa-building"></i> Información del Establecimiento</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-store"></i>
                            <div>
                                <span class="etiqueta">Nombre</span>
                                <span class="valor">{{ reserva.servicio.establecimiento.nombre }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="etiqueta">Dirección</span>
                                <span class="valor">{{ reserva.servicio.establecimiento.direccion }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="etiqueta">Teléfono</span>
                                <span class="valor">{{ reserva.servicio.establecimiento.telefono }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Servicio -->
                <div class="seccion">
                    <h2><i class="fas fa-info-circle"></i> Información del Servicio</h2>
                    <div class="info-grid">
                        <div class="info-item full-width">
                            <i class="fas fa-align-left"></i>
                            <div>
                                <span class="etiqueta">Descripción</span>
                                <span class="valor">{{ reserva.servicio.descripcion }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-dollar-sign"></i>
                            <div>
                                <span class="etiqueta">Costo</span>
                                <span class="valor precio">${{ reserva.servicio.costo }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="acciones">
                <button class="btn-secundario" @click="$router.back()">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </button>
                <button v-if="reserva.estado === 'PENDIENTE'" class="btn-cancelar" @click="cancelarReserva">
                    <i class="fas fa-times"></i>
                    Cancelar Reserva
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loader from '../Loader.vue';
import { getAuth } from 'firebase/auth';

export default {
    components: {
        Loader
    },
    data() {
        return {
            reserva: null,
            cargando: true,
            error: null
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
        async cancelarReserva() {
            if (!confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
                return;
            }

            try {
                this.cargando = true;
                this.error = null;
                const auth = getAuth();
                const user = auth.currentUser;

                if (!user) {
                    this.error = 'Debes iniciar sesión para cancelar la reserva';
                    return;
                }

                await axios.post(`/api/reservas/${this.reserva.id_reserva}/cancelar`, {}, {
                    headers: {
                        'Authorization': `Bearer ${await user.getIdToken()}`
                    }
                });

                this.reserva.estado = 'cancelada';
                alert('Reserva cancelada exitosamente');
            } catch (error) {
                console.error('Error al cancelar la reserva:', error);
                this.error = 'Error al cancelar la reserva';
            } finally {
                this.cargando = false;
            }
        }
    },
    mounted() {
        this.obtenerDetalleReserva();
    }
};
</script>

<style scoped>
.contenedor {
    max-width: 1200px;
    margin: 15px auto;
    padding: 15px;
}

.detalle-reserva {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.info-principal {
    padding: 20px;
}

.seccion {
    margin-bottom: 20px;
}

.seccion h2 {
    font-size: 16px;
    color: #4a90e2;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.seccion h2 i {
    font-size: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.info-item i {
    color: #4a90e2;
    font-size: 16px;
    margin-top: 2px;
}

.etiqueta {
    display: block;
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 2px;
}

.valor {
    display: block;
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
}

.precio {
    color: #10b981;
    font-weight: 600;
}

.full-width {
    grid-column: 1 / -1;
}

.estado {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.estado-pendiente {
    background: #fff3cd;
    color: #856404;
}

.estado-confirmada {
    background: #d4edda;
    color: #155724;
}

.estado-cancelada {
    background: #f8d7da;
    color: #721c24;
}

.acciones {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.btn-secundario, .btn-cancelar {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-secundario {
    background: #e9ecef;
    color: #495057;
    border: none;
}

.btn-secundario:hover {
    background: #dee2e6;
}

.btn-cancelar {
    background: #dc3545;
    color: white;
    border: none;
}

.btn-cancelar:hover {
    background: #c82333;
}

.error-container {
    text-align: center;
    padding: 20px;
    background: #fff5f5;
    border-radius: 8px;
    margin: 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #dc3545;
}

.error-container i {
    font-size: 20px;
}

@media (max-width: 768px) {
    .contenedor {
        padding: 10px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .acciones {
        flex-direction: column;
    }

    .btn-secundario, .btn-cancelar {
        width: 100%;
        justify-content: center;
    }
}
</style> 