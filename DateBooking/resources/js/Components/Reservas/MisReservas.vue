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
                        <div v-for="reserva in reservas" 
                            :key="reserva.id_reserva" 
                            class="tarjeta-reserva"
                            @click="verDetalleReserva(reserva.id_reserva)"
                        >
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
                        <div v-for="reserva in reservasPasadas" 
                            :key="reserva.id_reserva" 
                            class="tarjeta-reserva"
                            @click="verDetalleReserva(reserva.id_reserva)"
                        >
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
                    this.reservas = response.data.reservas.filter(reserva => 
                        reserva.estado !== 'COMPLETADA' && reserva.estado !== 'CANCELADA'
                    );
                    this.reservasPasadas = response.data.reservas.filter(reserva => 
                        reserva.estado === 'COMPLETADA' || reserva.estado === 'CANCELADA'
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
    max-width: 1200px;
    margin: 15px auto;
    padding: 15px;
}

.seccion {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.seccion-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.seccion-header h2 {
    font-size: 16px;
    color: #4a90e2;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.seccion-header i {
    color: #6b7280;
    font-size: 14px;
}

.grid-reservas {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 15px;
}

.tarjeta-reserva {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
    cursor: pointer;
}

.tarjeta-reserva:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.info-principal {
    display: flex;
    align-items: center;
    gap: 15px;
}

.imagen {
    width: 50px;
    height: 50px;
    background: #4a90e2;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 20px;
}

.info-reserva {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-reserva h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.descripcion {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

.fecha-hora {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.fecha, .hora {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
}

.fecha i, .hora i {
    color: #4a90e2;
    font-size: 14px;
}

.estado {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

.estado-confirmada {
    background: #d4edda;
    color: #155724;
}

.estado-pendiente {
    background: #fff3cd;
    color: #856404;
}

.estado-cancelada {
    background: #f8d7da;
    color: #721c24;
}

.estado-completada {
    background: #e6f3ff;
    color: #0066cc;
}

.empty-state {
    padding: 30px;
    text-align: center;
    color: #6b7280;
}

.empty-state i {
    font-size: 24px;
    margin-bottom: 8px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
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

    .grid-reservas {
        grid-template-columns: 1fr;
    }

    .tarjeta-reserva {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .info-principal {
        width: 100%;
    }

    .fecha-hora {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
    }
}
</style>