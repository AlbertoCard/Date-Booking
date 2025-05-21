<!-- resources/js/Components/ReservaUsuario.vue -->
<template>
    <div class="contenedor">
        <!-- Encabezado -->
        <div class="encabezado">
            <h1>Mis Reservas</h1>
        </div>

        <!-- Lista de reservas -->
        <div v-for="(reserva, index) in reservas" :key="reserva.id_reserva" class="tarjeta-reserva">
            <!-- Imagen o ícono -->
            <div class="imagen"></div>

            <!-- Contenido -->
            <div class="info-reserva">
                <h2>{{ reserva.estado }}</h2>
                <p class="descripcion">{{ reserva.detalle_1 }}</p>
            </div>

            <!-- Precio y estrellas -->
            <div class="precio-estrellas">
                <p class="precio">${{ servicio.costo }}</p>
                <p class="estrellas">
                    <span v-for="i in 5" :key="i">
                        {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                </p>
            </div>

            <!-- Switch ON/OFF -->
            <div class="estado">
                <button :class="servicio.activo ? 'on' : 'off'" @click="toggleReserva(index)">
                    {{ servicio.activo ? 'ON' : 'OFF' }}
                </button>
            </div>
        </div>
    </div>
</template>

    <script>
    import axios from 'axios';

    export default {
        name: 'VerReservas',
        data() {
            return {
                reservas: [],
            };
        },
        mounted() {
            this.obtenerServicios();
        },
        methods: {
            async obtenerReservas() {
                try {
                    const response = await axios.get('http://localhost:8000/api/reservas');
                    // Mapeamos la respuesta para agregar campos visuales simulados
                    this.reservas = response.data.map(reserva => ({
                        ...reserva,
                        estrellas: Math.floor(Math.random() * 5) + 1, // ⭐ simulado
                        activo: true, // ON/OFF simulado
                    }));
                } catch (error) {
                    console.error('Error al obtener las reservas:', error);
                }
            },
            toggleServicio(index) {
                this.servicios[index].activo = !this.servicios[index].activo;
            },
        },
    };
</script>

    <style scoped>
    .contenedor {
        max-width: 1200px;
        margin: 20px auto;
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

    .nuevo-servicio {
        padding: 8px 16px;
        background: black;
        color: white;
        border: none;
        border-radius: 24px;
        cursor: pointer;
    }

    .tarjeta-reserva {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 40px;
        margin-bottom: 16px;
        gap: 16px;
    }

    .imagen {
        width: 100px;
        height: 100px;
        background: #ccc;
        border-radius: 8px;
    }

    .info-reserva {
        flex: 1;
    }

    .info-reserva h2 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .descripcion {
        font-size: 14px;
        color: #555;
    }

    .acciones {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .acciones button {
        padding: 9px 22px;
        font-size: 14px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
    }

    .editar {
        background-color: #e5e5e5;
    }

    .cancelar {
        background-color: #ffe5e5;
        color: #b00;
    }

    .precio-estrellas {
        text-align: right;
    }

    .precio {
        font-size: 26px;
        font-weight: bold;
    }

    .estrellas {
        color: #f5a623;
        font-size: 18px;
    }

    .estado button {
        width: 60px;
        height: 36px;
        border-radius: 18px;
        font-weight: bold;
        border: none;
        cursor: pointer;
    }

    .estado .on {
        background-color: #4caf50;
        color: white;
    }

    .estado .off {
        background-color: #ccc;
        color: black;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .tarjeta-servicio {
            flex-direction: column;
            align-items: center;
        }

        .acciones {
            flex-direction: row;
            gap: 12px;
            margin-top: 8px;
        }

        .precio-estrellas,
        .estado {
            align-self: center;
            margin-top: 8px;
        }
    }
</style>