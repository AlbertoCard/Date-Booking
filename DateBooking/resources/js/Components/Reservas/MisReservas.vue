<template>
    <div class="contenedor">
        <div class="encabezado" @click="mostrarReservas = !mostrarReservas" style="cursor:pointer;">
            <h1>En curso</h1>
        </div>
        <div v-if="mostrarReservas">
            <div class="tarjeta-reserva" v-for="reserva in reservas" :key="reserva.id_reserva">
                <div class="info-reserva">
                    <h2 class="reserva-servicio">{{ reserva.servicio.nombre }}</h2>
                    <div class="reserva-detalles">
                        <div class="reserva-info">
                            <span class="reserva-estado" :class="`estado-${reserva.estado.toLowerCase()}`">{{
                                reserva.estado }}</span>
                            <span class="reserva-tipo">{{ reserva.tipo_servicio }}</span>
                        </div>
                        <div class="reserva-horario">
                            <span class="reserva-fecha">{{ reserva.fecha.split(' ')[0] }}</span>
                            <span class="reserva-hora">{{ reserva.fecha.split(' ')[1] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="encabezado" @click="mostrarPasadas = !mostrarPasadas" style="cursor:pointer;">
            <h1>Completadas</h1>
        </div>
        <div v-if="mostrarPasadas">
            <!-- Aquí puedes filtrar y mostrar solo las reservas pasadas -->
            <ul>
                <li v-for="reserva in reservasPasadas" :key="reserva.id">
                    <!-- {{ reserva.nombre }} - {{ reserva.fecha }} -->
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            // Variables reactivas aquí
            reservas: [],
        };
    },
    methods: {
        // Métodos para manejar la lógica de la aplicación
        mostrarReservas() {
            this.mostrarReservas = !this.mostrarReservas;
        },
        mostrarPasadas() {
            this.mostrarPasadas = !this.mostrarPasadas;
        }
    },
    mounted() {
        const userId = this.$route.params.id;
        axios.get(`/api/reservas/${userId}`)
            .then(response => {
                this.reservas = response.data.reservas;
                console.log('Reservas obtenidas:', this.reservas);
            })
            .catch(error => {
                console.error('Error al obtener las reservas:', error);
            });
    }
};
</script>

<style scoped>
.contenedor {
    max-width: 1200px;
    width: 100%;
    margin: 10px auto;
    margin-top: 0px;
    padding: 60px;
    font-family: Arial, sans-serif;
    background: #fcfcfc;
}

.encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.encabezado h1 {
    font-size: 24px;
}

.encabezado h1:hover {
    color: #007BFF;
}

.tarjeta-reserva {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.info-reserva {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.reserva-servicio {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 4px;
}

.reserva-detalles {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    font-size: 16px;
    color: #555;
}

.reserva-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.reserva-horario {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.reserva-estado {
    font-weight: 600;
}

.estado-confirmada {
    background: #e6f9ec;
    color: #1a7f37;
    border: 1px solid #b6e7c9;
}

.estado-pendiente {
    background: #fffbe6;
    color: #b8860b;
    border: 1px solid #ffe58f;
}

.estado-cancelada {
    background: #ffeaea;
    color: #d93025;
    border: 1px solid #ffb3b3;
}
</style>