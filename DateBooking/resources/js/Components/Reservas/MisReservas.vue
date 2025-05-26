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
        <div v-else>
            <!-- Reservas en curso -->
            <div class="seccion">
                <div class="seccion-header" @click="mostrarReservas = !mostrarReservas">
                    <h2><i class="fas fa-clock"></i> En curso</h2>
                    <i :class="['fas', mostrarReservas ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                </div>
                <div v-if="mostrarReservas">
                    <div v-if="reservas.length === 0" class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>No tienes reservas en curso</p>
                    </div>
                    <div v-else class="grid-reservas">
                        <div v-for="reserva in reservas" :key="reserva.id_reserva" class="tarjeta-reserva"
                            @click="verDetalleReserva(reserva.id_reserva)">
                            <div class="info-principal">
                                <div class="imagen">
                                    <span>{{ reserva.servicio.nombre[0] }}</span>
                                </div>
                                <div class="info-reserva">
                                    <h3>{{ reserva.servicio.nombre }}</h3>
                                    <p class="descripcion">{{ reserva.tipo_servicio }}</p>
                                    <div class="estado" :class="`estado-${reserva.estado.toLowerCase()}`">
                                        {{ reserva.estado }}
                                    </div>
                                </div>
                            </div>
                            <div class="fecha-hora">
                                <div class="fecha">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ reserva.fecha.split(' ')[0] }}</span>
                                </div>
                                <div class="hora">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ reserva.fecha.split(' ')[1] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservas completadas -->
            <div class="seccion">
                <div class="seccion-header" @click="mostrarPasadas = !mostrarPasadas">
                    <h2><i class="fas fa-check-circle"></i> Completadas</h2>
                    <i :class="['fas', mostrarPasadas ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                </div>
                <div v-if="mostrarPasadas">
                    <div v-if="reservasPasadas.length === 0" class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>No tienes reservas completadas</p>
                    </div>
                    <div v-else class="grid-reservas">
                        <div v-for="reserva in reservasPasadas" :key="reserva.id_reserva" class="tarjeta-reserva"
                            @click="verDetalleReserva(reserva.id_reserva)">
                            <div class="info-principal">
                                <div class="imagen">
                                    <span>{{ reserva.servicio.nombre[0] }}</span>
                                </div>
                                <div class="info-reserva">
                                    <h3>{{ reserva.servicio.nombre }}</h3>
                                    <p class="descripcion">{{ reserva.tipo_servicio }}</p>
                                    <div class="estado" :class="`estado-${reserva.estado.toLowerCase()}`">
                                        {{ reserva.estado }}
                                    </div>
                                </div>
                            </div>
                            <div class="fecha-hora">
                                <div class="fecha">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ reserva.fecha.split(' ')[0] }}</span>
                                </div>
                                <div class="hora">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ reserva.fecha.split(' ')[1] }}</span>
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
import { useRouter } from 'vue-router';
import Loader from '../Loader.vue';

export default {
    components: {
        Loader
    },
    data() {
        return {
            reservas: [],
            reservasPasadas: [],
            mostrarReservas: true,
            mostrarPasadas: false,
            cargando: true,
            error: null
        };
    },
    methods: {
        verDetalleReserva(id) {
            this.$router.push(`/detalle-reserva/${id}`);
        },
        async obtenerReservas() {
            try {
                const userId = this.$route.params.id;
                const response = await axios.get(`/api/reservas/${userId}`);

                if (response.data.reservas) {
                    // Filtrar reservas pendientes y confirmadas
                    this.reservas = response.data.reservas.filter(reserva =>
                        reserva.estado.toUpperCase() === 'PENDIENTE' ||
                        reserva.estado.toUpperCase() === 'CONFIRMADA'
                    );

                    // Filtrar reservas completadas y canceladas
                    this.reservasPasadas = response.data.reservas.filter(reserva =>
                        reserva.estado.toUpperCase() === 'COMPLETADA' ||
                        reserva.estado.toUpperCase() === 'CANCELADA'
                    );

                }
            } catch (error) {
                console.error('Error al obtener las reservas:', error);
                this.error = 'Error al cargar las reservas. Por favor, intenta nuevamente.';
            } finally {
                this.cargando = false;
            }
        }
    },
    mounted() {
        this.obtenerReservas();
    }
};
</script>

<style scoped>
.contenedor {
    max-width: 800px;
    margin: 15px auto;
    padding: 15px;
}

.seccion {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.seccion:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.seccion-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    transition: all 0.3s ease;
}

.seccion-header:hover {
    background: linear-gradient(to right, #f1f5f9, #e2e8f0);
}

.seccion-header h2 {
    font-size: 18px;
    color: #1e40af;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.seccion-header i {
    color: #4b5563;
    font-size: 16px;
    transition: transform 0.3s ease;
}

.seccion-header:hover i {
    transform: scale(1.1);
}

.grid-reservas {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 100%));
    gap: 20px;
    padding: 20px;
}

.tarjeta-reserva {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e5e7eb;
}

.tarjeta-reserva:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: #d1d5db;
}

.info-principal {
    display: flex;
    align-items: center;
    gap: 16px;
}

.imagen {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 24px;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.info-reserva {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.info-reserva h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.descripcion {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.fecha-hora {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.fecha,
.hora {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #4b5563;
}

.fecha i,
.hora i {
    color: #3b82f6;
    font-size: 16px;
}

.estado {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.estado-confirmada {
    background: #dcfce7;
    color: #166534;
}

.estado-pendiente {
    background: #fef9c3;
    color: #854d0e;
}

.estado-cancelada {
    background: #fee2e2;
    color: #991b1b;
}

.estado-completada {
    background: #e0f2fe;
    color: #0369a1;
}

.empty-state {
    padding: 40px;
    text-align: center;
    color: #6b7280;
    background: #f9fafb;
    border-radius: 12px;
    margin: 20px;
}

.empty-state i {
    font-size: 32px;
    margin-bottom: 12px;
    color: #9ca3af;
}

.empty-state p {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

.error-container {
    text-align: center;
    padding: 24px;
    background: #fef2f2;
    border-radius: 12px;
    margin: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.error-container i {
    font-size: 24px;
}

@media (max-width: 768px) {
    .contenedor {
        padding: 10px;
    }

    .grid-reservas {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 15px;
    }

    .tarjeta-reserva {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
    }

    .info-principal {
        width: 100%;
    }

    .fecha-hora {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
    }

    .seccion-header {
        padding: 15px;
    }

    .seccion-header h2 {
        font-size: 16px;
    }
}
</style>