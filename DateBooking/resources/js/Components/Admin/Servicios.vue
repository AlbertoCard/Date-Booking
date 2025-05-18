<template>
    <div class="contenedor">
        <Loader :visible="cargando" />
        <!-- Encabezado -->
        <div class="encabezado">
            <h1>Todos los servicios</h1>
        </div>

        <div v-if="!cargando && servicios.length === 0" class="sin-resultados">
            No se encontraron servicios relacionados con "{{ searchText }}".
        </div>
        <!-- Lista de servicios -->
        <div v-for="servicio in servicios" :key="servicio.id_servicio" class="tarjeta-servicio">
            <!-- Imagen o ícono -->
            <div class="imagen"></div>

            <!-- Contenido -->
            <div class="info-servicio">
                <h2>{{ servicio.nombre }}</h2>
                <p class="descripcion">{{ servicio.descripcion }}</p>
            </div>
            <!-- Precio y estrellas -->
            <div class="precio-estrellas">
                <p class="precio">${{ servicio.costo }}</p>
                <p class="estrellas">
                    <span v-for="i in 5" :key="i">
                        {{ i <= servicio.estrellas ? '★' : '☆' }} </span>
                </p>
            </div>
            <!-- Disponibilidad -->
            <div class="estado">
                <span
                    :class="servicio.disponibilidad && servicio.disponibilidad.length > 0 && servicio.disponibilidad[0].activo == 1 ? 'activo' : 'inactivo'">
                    {{ servicio.disponibilidad && servicio.disponibilidad.length > 0 &&
                        servicio.disponibilidad[0].activo == 1 ? 'Activo' : 'Inactivo' }}
                </span>
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
    data() {
        return {
            servicios: [],
            cargando: true,
        };
    },
    mounted() {
        axios.get('/api/servicios')
            .then(response => {
                this.servicios = response.data;
            })
            .catch(error => {
                console.error('Error fetching servicios:', error);
            })
            .finally(() => {
                this.cargando = false;
            });
    },
    methods: {

    },

}
</script>

<style scoped>
.contenedor {
    max-width: 1200px;
    margin: 10px auto;
    margin-top: -50px;
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

.tarjeta-servicio {
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

.info-servicio {
    flex: 1;
}

.info-servicio h2 {
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


.estado {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    min-width: 90px;
    font-size: 16px;
    font-weight: bold;
}

.estado .activo {
    background-color: #d4f8e8;
    color: #219653;
    padding: 6px 18px;
    border-radius: 16px;
    border: 1px solid #219653;
}

.estado .inactivo {
    background-color: #ffeaea;
    color: #b00;
    padding: 6px 18px;
    border-radius: 16px;
    border: 1px solid #b00;
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