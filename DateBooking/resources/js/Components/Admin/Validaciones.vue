<template>
    <div class="establecimientos-container">
        <Loader :visible="cargando" />
        <div class="encabezado">
            <h1>Establecimientos</h1>
        </div>

        <div class="filtros">
            <fieldset>
                <div class="button-group">
                    <input type="radio" id="todos" name="opciones" checked="" value="todos"
                        v-model="opcionSeleccionada" />
                    <label for="todos">Todos</label>
                </div>
                <div class="button-group">
                    <input type="radio" id="activos" name="opciones" value="activo"
                        v-model="opcionSeleccionada" />
                    <label for="activos">Activos</label>
                </div>
                <div class="button-group">
                    <input type="radio" id="inactivos" name="opciones" value="inactivo"
                        v-model="opcionSeleccionada" />
                    <label for="inactivos">Inactivos</label>
                </div>
            </fieldset>
        </div>


        <div v-if="!cargando && establecimientos.length === 0" class="sin-resultados">
            No se encontraron establecimientos.
        </div>
        <ul v-if="establecimientos.length" class="establecimientos-list">
            <li v-for="establecimiento in establecimientos" :key="establecimiento.id" class="establecimiento-card">
            <router-link
                :to="`establecimientos/${establecimiento.nombre}`"
                class="establecimiento-info"
                style="text-decoration: none; color: inherit;"
            >
                <div>
                    <strong>{{ establecimiento.nombre }}</strong>
                    <p v-if="establecimiento.direccion">{{ establecimiento.direccion }}</p>
                </div>
                <span
                    :class="['estado-indicador', establecimiento.estado === 'activo' ? 'activo' : 'inactivo']">
                    {{ establecimiento.estado === 'activo' ? 'Activo' : 'Inactivo' }}
                </span>
            </router-link>
            </li>
        </ul>
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
            establecimientos: [],
            opcionSeleccionada: 'todos',
            cargando: true,
        };
    },
    watch: {
        opcionSeleccionada(newValue) {
            this.filtrarEstablecimientos(newValue);
        },
    },
    mounted() {
        axios.get('/api/establecimientos')
            .then(response => {
                this.establecimientos = response.data;
            })
            .catch(error => {
                console.error('Error fetching establecimientos:', error);
            })
            .finally(() => {
                this.cargando = false;
            });
    },
    methods: {
        filtrarEstablecimientos(opcion) {
            this.cargando = true;
            if (opcion === 'todos') {
                axios.get('/api/establecimientos')
                    .then(response => {
                        this.establecimientos = response.data;
                    })
                    .catch(error => {
                        console.error('Error fetching establecimientos:', error);
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            } else {
                axios.get(`/api/establecimientos/estado/${opcion}`)
                    .then(response => {
                        this.establecimientos = response.data;
                    })
                    .catch(error => {
                        console.error('Error fetching establecimientos:', error);
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            }
        },

    },
};
</script>

<style scoped>
.encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.encabezado h1 {
    font-size: 24px;
}

.establecimientos-container {
    max-width: 900px;
    margin: 1rem auto;
    padding: 1.5rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
}

.establecimientos-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.establecimiento-card {
    background: #f5f7fa;
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: box-shadow 0.2s;
}

.establecimiento-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.09);
}

.button-group {
    flex-grow: 1;
    margin: auto;
}

.button-group input[type="radio"] {
    display: none;
}

.button-group label {
    display: inline-block;
    padding: 10px 20px;
    cursor: pointer;
    border: 1px solid #2b426d;
    background-color: #385c7e;
    color: white;
    border-radius: 15px;
    transition: all ease 0.2s;
    text-align: center;
    flex-grow: 1;
    flex-basis: 0;
    width: 90px;
    font-size: 13px;
    margin: 5px;
}

.button-group input[type="radio"]:checked+label {
    background-color: white;
    color: #02375a;
    border: 1px solid #2b426d;
}

fieldset {
    border: 0;
    display: flex;
}

.filtros {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    margin-top: -20px;
}

.sin-resultados {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 10px;
    padding: 30px 20px;
    margin: 30px 0;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.08);
    letter-spacing: 0.5px;
}


.estado-indicador {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    min-width: 80px;
    text-align: center;
    margin-left: 10px;
}

.estado-indicador.activo {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.estado-indicador.inactivo {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.establecimiento-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>